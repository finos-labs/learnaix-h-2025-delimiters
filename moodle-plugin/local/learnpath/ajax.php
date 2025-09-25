<?php
/**
 * AJAX Handler for LearnPath Navigator
 * Team Delimiters - NatWest Hack4aCause
 *
 * @package    local_learnpath
 * @copyright  2025 Team Delimiters
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define('AJAX_SCRIPT', true);

require_once(__DIR__ . '/../../config.php');
require_once($CFG->libdir . '/filelib.php');

use local_learnpath\snowflake_connector;
use local_learnpath\student_data;

require_login();
// require_sesskey(); // Temporarily disabled for testing

$context = context_system::instance();
require_capability('local/learnpath:view', $context);

header('Content-Type: application/json');

$action = required_param('action', PARAM_ALPHANUMEXT);

// Debug: Log the action received
error_log("LearnPath AJAX: Received action = " . $action);

try {
    switch ($action) {
        case 'get_student':
            $profile = required_param('profile', PARAM_ALPHA);
            $data = student_data::get_student_data($profile);
            
            echo json_encode([
                'success' => true,
                'data' => $data,
                'profile' => $profile
            ]);
            break;
            
        case 'get_student_data':
        case 'get_real_student':
            $userid = required_param('userid', PARAM_INT);
            $courseid = required_param('courseid', PARAM_INT);
            $data = student_data::get_real_student_data($userid, $courseid);
            
            if ($data) {
                echo json_encode([
                    'success' => true,
                    'data' => $data,
                    'source' => 'real_gradebook'
                ]);
            } else {
                echo json_encode([
                    'success' => false,
                    'message' => 'Student or course not found'
                ]);
            }
            break;
            
        case 'get_courses':
            // Get all courses
            $courses = $DB->get_records_sql("
                SELECT id, shortname, fullname
                FROM {course}
                WHERE id > 1
                ORDER BY shortname
            ");
            
            if ($courses) {
                echo json_encode([
                    'success' => true,
                    'courses' => array_values($courses)
                ]);
            } else {
                echo json_encode([
                    'success' => false,
                    'message' => 'No courses found'
                ]);
            }
            break;
            
        case 'get_students':
        case 'get_course_students':
            $courseid = required_param('courseid', PARAM_INT);
            
            // Get students enrolled in this course
            $students = $DB->get_records_sql("
                SELECT DISTINCT u.id, u.firstname, u.lastname, u.email
                FROM {user} u
                JOIN {role_assignments} ra ON ra.userid = u.id
                JOIN {context} ctx ON ctx.id = ra.contextid
                JOIN {role} r ON r.id = ra.roleid
                WHERE ctx.instanceid = ? 
                AND ctx.contextlevel = 50
                AND r.shortname = 'student'
                AND u.deleted = 0
                ORDER BY u.lastname, u.firstname
            ", [$courseid]);
            
            if ($students) {
                echo json_encode([
                    'success' => true,
                    'students' => array_values($students)
                ]);
            } else {
                echo json_encode([
                    'success' => false,
                    'message' => 'No students found in this course'
                ]);
            }
            break;
            
        case 'analyze_student':
            $profile = required_param('profile', PARAM_ALPHA);
            $studentData = student_data::get_student_data($profile);
            
            // Generate AI prompt
            $prompt = student_data::generate_analysis_prompt($studentData);
            
            // Execute AI analysis
            $connector = new snowflake_connector();
            $result = $connector->execute_cortex_query('mistral-7b', $prompt);
            
            echo json_encode([
                'success' => $result['success'],
                'response' => $result['response'],
                'model' => $result['model'] ?? 'mistral-7b',
                'method' => $result['method'] ?? 'real_snowflake_cortex',
                'error' => $result['error'] ?? null,
                'fallback' => $result['fallback'] ?? false
            ]);
            break;
            
        case 'analyze_real_student':
            $userid = required_param('userid', PARAM_INT);
            $courseid = required_param('courseid', PARAM_INT);
            $studentData = student_data::get_real_student_data($userid, $courseid);
            
            if ($studentData) {
                // Generate AI prompt
                $prompt = student_data::generate_analysis_prompt($studentData);
                
                // Execute AI analysis
                $connector = new snowflake_connector();
                $result = $connector->execute_cortex_query('mistral-7b', $prompt);
                
                echo json_encode([
                    'success' => $result['success'],
                    'response' => $result['response'],
                    'model' => $result['model'] ?? 'mistral-7b',
                    'method' => $result['method'] ?? 'real_snowflake_cortex',
                    'error' => $result['error'] ?? null,
                    'fallback' => $result['fallback'] ?? false
                ]);
            } else {
                echo json_encode([
                    'success' => false,
                    'message' => 'Student data not found'
                ]);
            }
            break;
            
        case 'generate_roadmap':
            $profile = required_param('profile', PARAM_ALPHA);
            $studentData = student_data::get_student_data($profile);
            
            // Generate roadmap prompt
            $prompt = student_data::generate_roadmap_prompt($studentData, 'general improvement');
            
            // Debug: Log the prompt being sent
            error_log("ROADMAP PROMPT: " . $prompt);
            
            // Execute AI roadmap generation
            $connector = new snowflake_connector();
            $result = $connector->execute_cortex_query('llama3-8b', $prompt);
            
            // Debug: Log the result
            error_log("ROADMAP RESULT: " . json_encode($result));
            
            echo json_encode([
                'success' => $result['success'],
                'roadmap' => $result['response'],
                'model' => $result['model'] ?? 'llama3-8b',
                'method' => $result['method'] ?? 'real_snowflake_cortex',
                'error' => $result['error'] ?? null,
                'fallback' => $result['fallback'] ?? false,
                'debug_prompt' => $prompt // Add for debugging
            ]);
            break;
            
        case 'generate_real_roadmap':
            $userid = required_param('userid', PARAM_INT);
            $courseid = required_param('courseid', PARAM_INT);
            $studentData = student_data::get_real_student_data($userid, $courseid);
            
            if ($studentData) {
                // Use custom prompt from JavaScript if provided, otherwise generate default
                $prompt = optional_param('prompt', '', PARAM_TEXT);
                if (empty($prompt)) {
                    $prompt = student_data::generate_roadmap_prompt($studentData, 'general improvement');
                }
                
                // Debug: Log the real data prompt
                error_log("REAL ROADMAP PROMPT: " . $prompt);
                error_log("REAL STUDENT DATA: " . json_encode($studentData));
                
                // Execute AI roadmap generation
                $connector = new snowflake_connector();
                $result = $connector->execute_cortex_query('llama3-8b', $prompt);
                
                // Debug: Log the result
                error_log("REAL ROADMAP RESULT: " . json_encode($result));
                
                echo json_encode([
                    'success' => $result['success'],
                    'roadmap' => $result['response'],
                    'model' => $result['model'] ?? 'llama3-8b',
                    'method' => $result['method'] ?? 'real_snowflake_cortex',
                    'error' => $result['error'] ?? null,
                    'fallback' => $result['fallback'] ?? false,
                    'debug_prompt' => $prompt,
                    'debug_student_data' => $studentData
                ]);
            } else {
                echo json_encode([
                    'success' => false,
                    'message' => 'Student data not found'
                ]);
            }
            break;
            
        case 'generate_real_roadmap_old':
            $userid = required_param('userid', PARAM_INT);
            $courseid = required_param('courseid', PARAM_INT);
            $studentData = student_data::get_real_student_data($userid, $courseid);
            
            if ($studentData) {
                // Generate roadmap prompt
                $prompt = student_data::generate_roadmap_prompt($studentData, 'general improvement');
                
                // Execute AI roadmap generation
                $connector = new snowflake_connector();
                $result = $connector->execute_cortex_query('mistral-7b', $prompt);
                
                echo json_encode([
                    'success' => $result['success'],
                    'response' => $result['response'],
                    'model' => $result['model'] ?? 'mistral-7b',
                    'method' => $result['method'] ?? 'real_snowflake_cortex',
                    'error' => $result['error'] ?? null,
                    'fallback' => $result['fallback'] ?? false
                ]);
            } else {
                echo json_encode([
                    'success' => false,
                    'message' => 'Student data not found'
                ]);
            }
            break;
            
        case 'get_recommendations':
            // Handle both demo and real data
            if (isset($_POST['profile'])) {
                // Demo data
                $profile = required_param('profile', PARAM_ALPHA);
                $studentData = student_data::get_student_data($profile);
            } else {
                // Real data
                $userid = required_param('userid', PARAM_INT);
                $courseid = required_param('courseid', PARAM_INT);
                $studentData = student_data::get_real_student_data($userid, $courseid);
            }
            
            if ($studentData) {
                // Use custom prompt from JavaScript if provided, otherwise generate default
                $prompt = optional_param('prompt', '', PARAM_TEXT);
                if (empty($prompt)) {
                    $prompt = student_data::generate_improvement_recommendations($studentData);
                }
                
                // Execute AI analysis with a model better suited for structured responses
                $connector = new snowflake_connector();
                $result = $connector->execute_cortex_query('llama3-8b', $prompt);
                
                echo json_encode([
                    'success' => $result['success'],
                    'recommendations' => $result['response'],
                    'model' => $result['model'] ?? 'llama3-8b',
                    'method' => $result['method'] ?? 'real_snowflake_cortex',
                    'error' => $result['error'] ?? null,
                    'fallback' => $result['fallback'] ?? false
                ]);
            } else {
                echo json_encode([
                    'success' => false,
                    'message' => 'Student data not found'
                ]);
            }
            break;
            
        case 'test_connection':
            $connector = new snowflake_connector();
            $result = $connector->test_connection();
            
            echo json_encode($result);
            break;
            
        case 'chat_message':
            // Handle both demo and real data for chatbot
            $message = required_param('message', PARAM_TEXT);
            $prompt = optional_param('prompt', '', PARAM_TEXT);
            
            if (isset($_POST['profile'])) {
                // Demo data
                $profile = required_param('profile', PARAM_ALPHA);
                $studentData = student_data::get_student_data($profile);
            } else {
                // Real data
                $userid = required_param('userid', PARAM_INT);
                $courseid = required_param('courseid', PARAM_INT);
                $studentData = student_data::get_real_student_data($userid, $courseid);
            }
            
            if ($studentData) {
                // Create comprehensive context-aware prompt with all student data
                $contextPrompt = "You are an AI Study Assistant helping a student. Here is their complete profile:\n\n";
                
                // Student Identity
                $contextPrompt .= "STUDENT PROFILE:\n";
                $contextPrompt .= "- Name: " . $studentData['name'] . "\n";
                $contextPrompt .= "- Grade Level: " . $studentData['grade'] . "\n";
                $contextPrompt .= "- Overall Performance: " . $studentData['overall'] . "%\n\n";
                
                // Performance Data
                if (!empty($studentData['scores'])) {
                    $contextPrompt .= "DETAILED GRADES:\n";
                    foreach ($studentData['scores'] as $subject => $score) {
                        $status = $score >= 80 ? "Excellent" : ($score >= 60 ? "Good" : "Needs Improvement");
                        $contextPrompt .= "- {$subject}: {$score}% ({$status})\n";
                    }
                    $contextPrompt .= "\n";
                    
                    // Identify weak and strong areas
                    $weakAreas = array_filter($studentData['scores'], function($score) { return $score < 60; });
                    $strongAreas = array_filter($studentData['scores'], function($score) { return $score >= 80; });
                    
                    if (!empty($weakAreas)) {
                        $contextPrompt .= "WEAK AREAS (Need Focus):\n";
                        foreach ($weakAreas as $subject => $score) {
                            $contextPrompt .= "- {$subject}: {$score}%\n";
                        }
                        $contextPrompt .= "\n";
                    }
                    
                    if (!empty($strongAreas)) {
                        $contextPrompt .= "STRONG AREAS (Build Upon):\n";
                        foreach ($strongAreas as $subject => $score) {
                            $contextPrompt .= "- {$subject}: {$score}%\n";
                        }
                        $contextPrompt .= "\n";
                    }
                }
                
                // Study Patterns
                if (isset($studentData['streak'])) {
                    $contextPrompt .= "STUDY PATTERNS:\n";
                    $contextPrompt .= "- Current Study Streak: " . $studentData['streak'] . " days\n";
                }
                if (isset($studentData['study_hours'])) {
                    $contextPrompt .= "- Weekly Study Hours: " . $studentData['study_hours'] . " hours\n";
                }
                if (isset($studentData['quiz_attempts'])) {
                    $contextPrompt .= "- Total Quiz Attempts: " . $studentData['quiz_attempts'] . "\n";
                }
                if (isset($studentData['last_login'])) {
                    $contextPrompt .= "- Last Login: " . $studentData['last_login'] . "\n";
                }
                $contextPrompt .= "\n";
                
                // Performance Analysis
                $contextPrompt .= "PERFORMANCE ANALYSIS:\n";
                if ($studentData['overall'] < 40) {
                    $contextPrompt .= "- Status: STRUGGLING - Needs intensive support and encouragement\n";
                    $contextPrompt .= "- Focus: Basic concepts, foundational skills, confidence building\n";
                    $contextPrompt .= "- Approach: Break down complex topics, provide step-by-step guidance\n";
                } elseif ($studentData['overall'] < 75) {
                    $contextPrompt .= "- Status: IMPROVING - Shows potential with targeted help\n";
                    $contextPrompt .= "- Focus: Strengthen weak areas while building on strengths\n";
                    $contextPrompt .= "- Approach: Balanced support with practical applications\n";
                } else {
                    $contextPrompt .= "- Status: HIGH ACHIEVER - Ready for advanced challenges\n";
                    $contextPrompt .= "- Focus: Enrichment, advanced topics, career guidance\n";
                    $contextPrompt .= "- Approach: Sophisticated mentoring and growth opportunities\n";
                }
                $contextPrompt .= "\n";
                
                // Current Message Context
                $contextPrompt .= "STUDENT QUESTION: \"" . $message . "\"\n\n";
                
                // Response Guidelines
                $contextPrompt .= "RESPONSE GUIDELINES:\n";
                $contextPrompt .= "- Be specific to their performance data and weak/strong areas\n";
                $contextPrompt .= "- Provide actionable, personalized advice\n";
                $contextPrompt .= "- Match your tone to their performance level (encouraging for struggling, analytical for improving, sophisticated for high achievers)\n";
                $contextPrompt .= "- Reference their actual grades and subjects when relevant\n";
                $contextPrompt .= "- Provide concrete next steps they can take\n\n";
                
                // Use custom prompt if provided, otherwise use the context-aware one
                $finalPrompt = !empty($prompt) ? $prompt : $contextPrompt . "Please provide a helpful, personalized response:";
                
                // Execute AI chat response using llama3-8b for better conversational responses
                $connector = new snowflake_connector();
                $result = $connector->execute_cortex_query('llama3-8b', $finalPrompt);
                
                echo json_encode([
                    'success' => $result['success'],
                    'response' => $result['response'],
                    'model' => $result['model'] ?? 'llama3-8b',
                    'method' => $result['method'] ?? 'real_snowflake_cortex',
                    'error' => $result['error'] ?? null,
                    'fallback' => $result['fallback'] ?? false
                ]);
            } else {
                echo json_encode([
                    'success' => false,
                    'message' => 'Student data not found'
                ]);
            }
            break;
            
        default:
            throw new moodle_exception('invalidaction', 'local_learnpath');
    }
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>
