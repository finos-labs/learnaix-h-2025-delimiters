<?php
/**
 * Student Data Configuration for Moodle Plugin
 * Team Delimiters - NatWest Hack4aCause
 *
 * @package    local_learnpath
 * @copyright  2025 Team Delimiters
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_learnpath;

defined('MOODLE_INTERNAL') || die();

class student_data {
    
    /**
     * Get real student data from Moodle gradebook
     */
    public static function get_real_student_data($userid, $courseid) {
        global $DB;
        
        // Get user info
        $user = $DB->get_record('user', ['id' => $userid]);
        if (!$user) {
            return null;
        }
        
        // Get course info
        $course = $DB->get_record('course', ['id' => $courseid]);
        if (!$course) {
            return null;
        }
        
        // Get grade items for the course (both module and manual items)
        $grade_items = $DB->get_records_select('grade_items', 
            'courseid = ? AND itemtype IN (?, ?)', 
            [$courseid, 'mod', 'manual']
        );
        
        $scores = [];
        $total_attempts = 0;
        
        foreach ($grade_items as $item) {
            // Get the grade for this user and item
            $grade = $DB->get_record('grade_grades', [
                'itemid' => $item->id,
                'userid' => $userid
            ]);
            
            if ($grade && $grade->finalgrade !== null) {
                $percentage = round(($grade->finalgrade / $item->grademax) * 100);
                $scores[$item->itemname] = $percentage;
            }
        }
        
        // Get quiz attempts (if any)
        $quiz_attempts = $DB->count_records('quiz_attempts', ['userid' => $userid]);
        
        // Calculate overall score
        $overall_score = 0;
        if (!empty($scores)) {
            $overall_score = round(array_sum($scores) / count($scores));
        }
        
        return [
            'id' => 'user_' . $userid,
            'name' => fullname($user),
            'avatar' => strtoupper(substr($user->firstname, 0, 1) . substr($user->lastname, 0, 1)),
            'grade' => 'Student',
            'overall' => $overall_score,
            'scores' => $scores,
            'streak' => rand(1, 10), // Simulated streak
            'message' => $overall_score >= 80 ? 'Excellent performance!' : 
                        ($overall_score >= 60 ? 'Good progress, keep it up!' : 'Focus on improvement areas'),
            'quiz_attempts' => $quiz_attempts,
            'study_hours' => rand(20, 50), // Simulated for demo
            'last_login' => date('Y-m-d H:i:s', $user->lastlogin)
        ];
    }
    
    /**
     * Get student data based on profile type (fallback/demo data)
     */
    public static function get_student_data($profile = 'average') {
        $profiles = [
            'struggling' => [
                'id' => 'student_001',
                'name' => 'Alex Johnson',
                'scores' => [
                    'Linear Algebra' => 45,
                    'Calculus' => 52,
                    'Probability Theory' => 28,
                    'Statistics' => 38,
                    'Regression Analysis' => 31,
                    'Data Visualization' => 48
                ],
                'quiz_attempts' => 12,
                'study_hours' => 45,
                'last_login' => '2025-01-22 14:30:00'
            ],
            'average' => [
                'id' => 'student_002',
                'name' => 'Maria Garcia',
                'scores' => [
                    'Linear Algebra' => 72,
                    'Calculus' => 68,
                    'Probability Theory' => 65,
                    'Statistics' => 70,
                    'Regression Analysis' => 63,
                    'Data Visualization' => 75
                ],
                'quiz_attempts' => 8,
                'study_hours' => 32,
                'last_login' => '2025-01-22 16:15:00'
            ],
            'advanced' => [
                'id' => 'student_003',
                'name' => 'David Chen',
                'scores' => [
                    'Linear Algebra' => 92,
                    'Calculus' => 89,
                    'Probability Theory' => 85,
                    'Statistics' => 91,
                    'Regression Analysis' => 88,
                    'Data Visualization' => 94
                ],
                'quiz_attempts' => 5,
                'study_hours' => 28,
                'last_login' => '2025-01-22 18:45:00'
            ]
        ];
        
        return $profiles[$profile] ?? $profiles['average'];
    }
    
    /**
     * Generate comprehensive analysis prompt for AI
     */
    public static function generate_analysis_prompt($studentData) {
        $scores = $studentData['scores'];
        $total_scores = array_sum($scores);
        $avg_score = count($scores) > 0 ? round($total_scores / count($scores), 1) : 0;
        
        // Categorize performance
        $strong_areas = [];
        $weak_areas = [];
        $moderate_areas = [];
        
        foreach ($scores as $subject => $score) {
            if ($score >= 85) {
                $strong_areas[] = "$subject ($score%)";
            } elseif ($score < 65) {
                $weak_areas[] = "$subject ($score%)";
            } else {
                $moderate_areas[] = "$subject ($score%)";
            }
        }
        
        $prompt = "Analyze student performance: {$studentData['name']}, Average: {$avg_score}%, Study Hours: {$studentData['study_hours']}/week. ";
        
        if (!empty($strong_areas)) {
            $prompt .= "Strong areas: " . implode(', ', $strong_areas) . ". ";
        }
        if (!empty($weak_areas)) {
            $prompt .= "Weak areas: " . implode(', ', $weak_areas) . ". ";
        }
        
        $prompt .= "Provide: 1) Strengths 2) Challenges 3) 3 improvement actions 4) Study tips. Keep response under 200 words.";
        
        return $prompt;
    }
    
    /**
     * Generate roadmap prompt compatible with JavaScript parsing
     */
    public static function generate_roadmap_prompt($studentData, $focusArea = 'general improvement') {
        $scores = $studentData['scores'];
        $avg_score = count($scores) > 0 ? round(array_sum($scores) / count($scores), 1) : 0;
        
        // Get weak areas for focus
        $weak_areas = [];
        if (!empty($scores)) {
            foreach ($scores as $subject => $score) {
                if ($score < 60) {
                    $weak_areas[] = $subject;
                }
            }
        }
        
        // Create prompt that matches JavaScript expectations
        $prompt = "Generate exactly 3 learning modules for {$studentData['name']}. Return ONLY 3 lines in this exact format:\n";
        $prompt .= "Title|Duration|Lessons|Skills\n\n";
        $prompt .= "Example:\n";
        $prompt .= "Master Quadratic Equations|2 weeks|12 lessons|Algebra,Factoring,Graphing\n";
        $prompt .= "Complete Physics Problems|3 weeks|15 lessons|Mechanics,Forces,Motion\n";
        $prompt .= "Improve Essay Writing|1 week|8 lessons|Writing,Analysis,Grammar\n\n";
        
        // Student context
        $prompt .= "Student: {$studentData['name']}\n";
        $prompt .= "Performance: {$avg_score}% overall\n";
        $prompt .= "Study hours: {$studentData['study_hours']}/week\n";
        
        if (!empty($scores)) {
            $prompt .= "Current grades: " . json_encode($scores) . "\n";
        }
        
        if (!empty($weak_areas)) {
            $prompt .= "Focus on weak areas: " . implode(', ', $weak_areas) . "\n";
        } else {
            $prompt .= "Focus on skill enhancement\n";
        }
        
        // Learning level context
        if ($avg_score < 40) {
            $prompt .= "Level: Struggling - create foundational modules\n";
        } elseif ($avg_score < 75) {
            $prompt .= "Level: Improving - create balanced modules\n";
        } else {
            $prompt .= "Level: Advanced - create challenging modules\n";
        }
        
        $prompt .= "\nCreate 3 specific learning modules. Return exactly 3 lines with pipe separators, nothing else.";
        
        return $prompt;
    }
    
    /**
     * Generate focused improvement recommendations
     */
    public static function generate_improvement_recommendations($studentData) {
        $scores = $studentData['scores'];
        $weak_subjects = [];
        
        foreach ($scores as $subject => $score) {
            if ($score < 75) {
                $weak_subjects[$subject] = $score;
            }
        }
        
        $prompt = "Student {$studentData['name']} needs help with: ";
        
        foreach ($weak_subjects as $subject => $score) {
            $prompt .= "$subject ($score%), ";
        }
        
        $prompt .= "Provide: 1) 3 immediate actions 2) Study techniques 3) Resources 4) Time management tips. Keep under 200 words.";
        
        return $prompt;
    }
}
