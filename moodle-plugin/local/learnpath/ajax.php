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
            break;
            
        case 'generate_real_roadmap':
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
                // Generate improvement recommendations prompt
                $prompt = student_data::generate_improvement_recommendations($studentData);
                
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
            
        case 'test_connection':
            $connector = new snowflake_connector();
            $result = $connector->test_connection();
            
            echo json_encode($result);
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
