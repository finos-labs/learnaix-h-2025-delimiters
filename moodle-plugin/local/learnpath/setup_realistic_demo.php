<?php
/**
 * Setup Realistic Demo Data for LearnPath Navigator
 * Uses existing courses and creates realistic student scenarios
 * Based on actual Moodle data analysis
 * Team Delimiters - NatWest Hack4aCause
 */

require_once(__DIR__ . '/../../config.php');
require_once($CFG->libdir . '/filelib.php');
require_once($CFG->dirroot . '/course/lib.php');
require_once($CFG->dirroot . '/user/lib.php');
require_once($CFG->dirroot . '/enrol/locallib.php');
require_once($CFG->dirroot . '/lib/enrollib.php');
require_once($CFG->dirroot . '/lib/gradelib.php');
require_once($CFG->dirroot . '/grade/lib.php');

require_login();
require_capability('moodle/site:config', context_system::instance());

echo "<h1>🎯 LearnPath Navigator - Realistic Demo Data Setup</h1>";
echo "<p>Creating demo scenarios using your existing courses and grade structure...</p>";
echo "<p><a href='check_existing_data.php'>🔍 View Current Data Analysis</a></p>";
echo "<hr>";

// Helper function to create or get user
function create_or_get_user($username, $firstname, $lastname, $email) {
    global $DB, $CFG;
    
    $user = $DB->get_record('user', ['username' => $username]);
    if (!$user) {
        $userdata = new stdClass();
        $userdata->username = $username;
        $userdata->firstname = $firstname;
        $userdata->lastname = $lastname;
        $userdata->email = $email;
        $userdata->password = hash_internal_user_password('Demo123!');
        $userdata->confirmed = 1;
        $userdata->mnethostid = $CFG->mnet_localhost_id;
        $userdata->auth = 'manual';
        $userdata->timecreated = time();
        $userdata->timemodified = time();
        
        $user_id = $DB->insert_record('user', $userdata);
        $user = $DB->get_record('user', ['id' => $user_id]);
        echo "✅ Created user: {$firstname} {$lastname} (ID: {$user->id})<br>";
    } else {
        echo "✅ Found existing user: {$firstname} {$lastname} (ID: {$user->id})<br>";
    }
    return $user;
}

// Helper function to enroll user in course
function enroll_user_in_course($user, $course) {
    global $DB;
    
    $context = context_course::instance($course->id);
    $student_role = $DB->get_record('role', ['shortname' => 'student']);
    
    if (!is_enrolled($context, $user)) {
        // Get or create manual enrolment instance
        $enrol_instance = $DB->get_record('enrol', ['courseid' => $course->id, 'enrol' => 'manual']);
        if (!$enrol_instance) {
            // Create manual enrolment instance
            $enrol = enrol_get_plugin('manual');
            $enrol_instance_id = $enrol->add_instance($course);
            $enrol_instance = $DB->get_record('enrol', ['id' => $enrol_instance_id]);
        }
        
        // Enroll the user
        $enrol = enrol_get_plugin('manual');
        $enrol->enrol_user($enrol_instance, $user->id, $student_role->id);
        
        echo "✅ Enrolled {$user->firstname} {$user->lastname} in {$course->fullname}<br>";
    } else {
        echo "✅ {$user->firstname} {$user->lastname} already enrolled in {$course->fullname}<br>";
    }
}

// Helper function to create grade for existing grade item
function create_or_update_grade($course, $user, $item_name, $grade_value, $max_grade = 10) {
    global $DB;
    
    // Find existing grade item
    $grade_item = $DB->get_record('grade_items', [
        'courseid' => $course->id,
        'itemname' => $item_name
    ]);
    
    if (!$grade_item) {
        echo "⚠️ Grade item '{$item_name}' not found in {$course->fullname}<br>";
        return false;
    }
    
    // Check if grade exists
    $existing_grade = $DB->get_record('grade_grades', [
        'itemid' => $grade_item->id,
        'userid' => $user->id
    ]);
    
    // Convert grade to proper scale (your system uses 10-point scale)
    $scaled_grade = ($grade_value / 100) * $max_grade;
    
    if (!$existing_grade) {
        // Create grade
        $grade_data = new stdClass();
        $grade_data->itemid = $grade_item->id;
        $grade_data->userid = $user->id;
        $grade_data->rawgrade = $scaled_grade;
        $grade_data->rawgrademax = $max_grade;
        $grade_data->rawgrademin = 0;
        $grade_data->rawscaleid = null;
        $grade_data->usermodified = $user->id;
        $grade_data->finalgrade = $scaled_grade;
        $grade_data->hidden = 0;
        $grade_data->locked = 0;
        $grade_data->locktime = 0;
        $grade_data->exported = 0;
        $grade_data->overridden = 0;
        $grade_data->excluded = 0;
        $grade_data->feedback = null;
        $grade_data->feedbackformat = 0;
        $grade_data->information = null;
        $grade_data->informationformat = 0;
        $grade_data->timecreated = time();
        $grade_data->timemodified = time();
        
        $DB->insert_record('grade_grades', $grade_data);
        echo "✅ Created grade: {$item_name} = {$scaled_grade}/{$max_grade} ({$grade_value}%)<br>";
    } else {
        // Update existing grade
        $existing_grade->rawgrade = $scaled_grade;
        $existing_grade->finalgrade = $scaled_grade;
        $existing_grade->timemodified = time();
        $DB->update_record('grade_grades', $existing_grade);
        echo "✅ Updated grade: {$item_name} = {$scaled_grade}/{$max_grade} ({$grade_value}%)<br>";
    }
    return true;
}

try {
    // Get existing courses
    $courses = [];
    $course_mappings = [
        'AD101' => 'Algorithmic Design',
        'BIO101' => 'General Biology', 
        'DSA101' => 'Data Structures and Algorithms',
        'AIDEMO' => 'AI Fundamentals Demo'
    ];
    
    foreach ($course_mappings as $shortname => $expected_name) {
        $course = $DB->get_record('course', ['shortname' => $shortname]);
        if ($course) {
            $courses[$shortname] = $course;
            echo "✅ Found course: {$course->fullname} (ID: {$course->id})<br>";
        } else {
            echo "⚠️ Course {$shortname} not found<br>";
        }
    }
    
    if (empty($courses)) {
        throw new Exception("No target courses found. Please check course shortnames.");
    }
    
    echo "<hr>";
    
    // ===== SCENARIO 1: THE STRUGGLING STUDENT =====
    echo "<h2>📊 Scenario 1: The Struggling Student - Sarah Martinez</h2>";
    echo "<p><strong>Profile:</strong> Computer Science student struggling with programming concepts</p>";
    
    $sarah = create_or_get_user('sarah.martinez', 'Sarah', 'Martinez', 'sarah.martinez@demo.com');
    
    // Enroll Sarah in Algorithmic Design (struggling with programming)
    if (isset($courses['AD101'])) {
        enroll_user_in_course($sarah, $courses['AD101']);
        
        // Create poor grades for existing assignments
        $ad_grades = [
            ['Periodic Quiz - 1', 35],  // 3.5/10
            ['Periodic Quiz - 2', 42],  // 4.2/10
            ['Periodic Quiz - 3', 28],  // 2.8/10
            ['Periodic Quiz - 4', 45],  // 4.5/10
        ];
        
        foreach ($ad_grades as $grade_data) {
            create_or_update_grade($courses['AD101'], $sarah, $grade_data[0], $grade_data[1]);
        }
    }
    
    // Also enroll in DSA (Data Structures) - another struggling area
    if (isset($courses['DSA101'])) {
        enroll_user_in_course($sarah, $courses['DSA101']);
        
        $dsa_grades = [
            ['Arrays', 30],     // Struggling with basic concepts
            ['ML-MLM', 25],     // Very poor performance
        ];
        
        foreach ($dsa_grades as $grade_data) {
            create_or_update_grade($courses['DSA101'], $sarah, $grade_data[0], $grade_data[1]);
        }
    }
    
    // ===== SCENARIO 2: THE IMPROVING STUDENT =====
    echo "<h2>📈 Scenario 2: The Improving Student - Alex Johnson</h2>";
    echo "<p><strong>Profile:</strong> AI/ML student with mixed performance, strong in theory but weak in implementation</p>";
    
    $alex = create_or_get_user('alex.johnson', 'Alex', 'Johnson', 'alex.johnson@demo.com');
    
    // Enroll Alex in AI Fundamentals Demo
    if (isset($courses['AIDEMO'])) {
        enroll_user_in_course($alex, $courses['AIDEMO']);
        
        $ai_grades = [
            ['ML Assignment', 78],      // Good theoretical understanding
            ['Final Project', 65],      // Implementation challenges
            ['Periodic Quiz - 1', 85],  // Strong in quizzes
            ['Periodic Quiz - 2', 58],  // Weaker in practical applications
        ];
        
        foreach ($ai_grades as $grade_data) {
            create_or_update_grade($courses['AIDEMO'], $alex, $grade_data[0], $grade_data[1]);
        }
    }
    
    // Also enroll in DSA with better but inconsistent performance
    if (isset($courses['DSA101'])) {
        enroll_user_in_course($alex, $courses['DSA101']);
        
        $dsa_grades = [
            ['Arrays', 75],     // Good grasp of arrays
            ['ML-MLM', 62],     // Struggling with advanced concepts
        ];
        
        foreach ($dsa_grades as $grade_data) {
            create_or_update_grade($courses['DSA101'], $alex, $grade_data[0], $grade_data[1]);
        }
    }
    
    // ===== SCENARIO 3: THE HIGH ACHIEVER =====
    echo "<h2>🏆 Scenario 3: The High Achiever - David Chen</h2>";
    echo "<p><strong>Profile:</strong> Exceptional student excelling across multiple technical subjects</p>";
    
    $david = create_or_get_user('david.chen', 'David', 'Chen', 'david.chen@demo.com');
    
    // Enroll David in Algorithmic Design (excelling)
    if (isset($courses['AD101'])) {
        enroll_user_in_course($david, $courses['AD101']);
        
        $ad_grades = [
            ['Periodic Quiz - 1', 95],
            ['Periodic Quiz - 2', 92],
            ['Periodic Quiz - 3', 96],
            ['Periodic Quiz - 4', 94],
        ];
        
        foreach ($ad_grades as $grade_data) {
            create_or_update_grade($courses['AD101'], $david, $grade_data[0], $grade_data[1]);
        }
    }
    
    // Enroll in DSA with excellent performance
    if (isset($courses['DSA101'])) {
        enroll_user_in_course($david, $courses['DSA101']);
        
        $dsa_grades = [
            ['Arrays', 98],     // Mastery of data structures
            ['ML-MLM', 93],     // Advanced algorithm implementation
        ];
        
        foreach ($dsa_grades as $grade_data) {
            create_or_update_grade($courses['DSA101'], $david, $grade_data[0], $grade_data[1]);
        }
    }
    
    // Also enroll in AI Fundamentals with top performance
    if (isset($courses['AIDEMO'])) {
        enroll_user_in_course($david, $courses['AIDEMO']);
        
        $ai_grades = [
            ['ML Assignment', 97],      // Exceptional ML understanding
            ['Final Project', 95],      // Outstanding project work
            ['Periodic Quiz - 1', 96],  
            ['Periodic Quiz - 2', 94],  
        ];
        
        foreach ($ai_grades as $grade_data) {
            create_or_update_grade($courses['AIDEMO'], $david, $grade_data[0], $grade_data[1]);
        }
    }
    
    echo "<hr>";
    echo "<h2>🎉 Realistic Demo Data Setup Complete!</h2>";
    echo "<h3>📊 Three Compelling Scenarios Created:</h3>";
    
    echo "<div style='display: flex; gap: 20px; margin: 20px 0;'>";
    
    // Sarah's Summary
    echo "<div style='border: 2px solid #dc3545; padding: 15px; border-radius: 10px; flex: 1;'>";
    echo "<h4 style='color: #dc3545; margin-top: 0;'>📊 Sarah Martinez - Struggling Student</h4>";
    echo "<ul>";
    echo "<li><strong>Algorithmic Design:</strong> 37.5% avg (Failing)</li>";
    echo "<li><strong>Data Structures:</strong> 27.5% avg (Critical)</li>";
    echo "<li><strong>Profile:</strong> Needs intensive foundational support</li>";
    echo "<li><strong>AI Focus:</strong> Basic concepts, step-by-step learning</li>";
    echo "</ul>";
    echo "</div>";
    
    // Alex's Summary  
    echo "<div style='border: 2px solid #ffc107; padding: 15px; border-radius: 10px; flex: 1;'>";
    echo "<h4 style='color: #856404; margin-top: 0;'>📈 Alex Johnson - Improving Student</h4>";
    echo "<ul>";
    echo "<li><strong>AI Fundamentals:</strong> 71.5% avg (Good theory)</li>";
    echo "<li><strong>Data Structures:</strong> 68.5% avg (Inconsistent)</li>";
    echo "<li><strong>Profile:</strong> Strong theory, weak implementation</li>";
    echo "<li><strong>AI Focus:</strong> Practical applications, hands-on practice</li>";
    echo "</ul>";
    echo "</div>";
    
    // David's Summary
    echo "<div style='border: 2px solid #28a745; padding: 15px; border-radius: 10px; flex: 1;'>";
    echo "<h4 style='color: #28a745; margin-top: 0;'>🏆 David Chen - High Achiever</h4>";
    echo "<ul>";
    echo "<li><strong>Algorithmic Design:</strong> 94.25% avg (Excellent)</li>";
    echo "<li><strong>Data Structures:</strong> 95.5% avg (Outstanding)</li>";
    echo "<li><strong>AI Fundamentals:</strong> 95.5% avg (Exceptional)</li>";
    echo "<li><strong>AI Focus:</strong> Advanced challenges, research topics</li>";
    echo "</ul>";
    echo "</div>";
    
    echo "</div>";
    
    echo "<h3>🚀 Demo Showcase Features:</h3>";
    echo "<ul>";
    echo "<li>🚨 <strong>Early Warning System:</strong> Sarah's critical performance triggers alerts</li>";
    echo "<li>🎯 <strong>Adaptive Learning Paths:</strong> Different roadmaps for each performance level</li>";
    echo "<li>📚 <strong>Contextual Resources:</strong> Programming basics for Sarah, advanced algorithms for David</li>";
    echo "<li>🤖 <strong>Smart Chatbot:</strong> Knows each student's specific courses and grades</li>";
    echo "<li>📊 <strong>Visual Analytics:</strong> Real performance data from actual Moodle gradebook</li>";
    echo "<li>🔄 <strong>Cross-Course Analysis:</strong> AI identifies patterns across multiple subjects</li>";
    echo "</ul>";
    
    echo "<div style='margin: 30px 0; text-align: center;'>";
    echo "<a href='index.php' style='background: #007bff; color: white; padding: 15px 30px; text-decoration: none; border-radius: 8px; font-size: 18px; margin: 10px;'>🎯 Launch LearnPath Navigator</a>";
    echo "<a href='check_existing_data.php' style='background: #6c757d; color: white; padding: 15px 30px; text-decoration: none; border-radius: 8px; font-size: 18px; margin: 10px;'>🔍 Verify Demo Data</a>";
    echo "</div>";
    
    echo "<div style='background: #e7f3ff; padding: 20px; border-radius: 10px; margin: 20px 0;'>";
    echo "<h4>🎭 Demo Script Ready!</h4>";
    echo "<p><strong>Presentation Flow:</strong></p>";
    echo "<ol>";
    echo "<li><strong>Start with Sarah</strong> - Show how AI identifies struggling students and provides intensive support</li>";
    echo "<li><strong>Move to Alex</strong> - Demonstrate balanced approach for mixed performance students</li>";
    echo "<li><strong>Finish with David</strong> - Showcase advanced features for high achievers</li>";
    echo "</ol>";
    echo "<p><em>Each scenario uses real course data and actual Moodle gradebook integration!</em></p>";
    echo "</div>";
    
} catch (Exception $e) {
    echo "<div style='color: red; background: #ffe6e6; padding: 15px; border: 1px solid #ff0000; border-radius: 5px;'>";
    echo "<h3>❌ Error occurred:</h3>";
    echo "<p><strong>Message:</strong> " . $e->getMessage() . "</p>";
    echo "<p><strong>File:</strong> " . $e->getFile() . "</p>";
    echo "<p><strong>Line:</strong> " . $e->getLine() . "</p>";
    echo "<details><summary>Full Stack Trace</summary><pre>" . $e->getTraceAsString() . "</pre></details>";
    echo "</div>";
}
?>
