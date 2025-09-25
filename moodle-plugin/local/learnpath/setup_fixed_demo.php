<?php
/**
 * Setup Fixed Demo Data for LearnPath Navigator
 * Properly handles Moodle grade structure and categories
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

echo "<h1>🎯 LearnPath Navigator - Fixed Demo Setup</h1>";
echo "<p>Creating demo data with proper grade structure...</p>";
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
            $enrol = enrol_get_plugin('manual');
            $enrol_instance_id = $enrol->add_instance($course);
            $enrol_instance = $DB->get_record('enrol', ['id' => $enrol_instance_id]);
        }
        
        $enrol = enrol_get_plugin('manual');
        $enrol->enrol_user($enrol_instance, $user->id, $student_role->id);
        
        echo "✅ Enrolled {$user->firstname} {$user->lastname} in {$course->fullname}<br>";
    } else {
        echo "✅ {$user->firstname} {$user->lastname} already enrolled in {$course->fullname}<br>";
    }
}

// Safe grade creation function using Moodle's grade API
function create_safe_grade($course, $user, $item_name, $grade_value, $max_grade = 100) {
    global $DB, $CFG;
    
    try {
        // Use Moodle's grade API
        require_once($CFG->libdir . '/gradelib.php');
        
        // Get course grade tree
        $gtree = new grade_tree($course->id, false, false);
        
        // Check if grade item already exists
        $grade_item = grade_item::fetch(array('courseid' => $course->id, 'itemname' => $item_name));
        
        if (!$grade_item) {
            // Create new grade item using proper API
            $grade_item = new grade_item();
            $grade_item->courseid = $course->id;
            $grade_item->itemtype = 'manual';
            $grade_item->itemname = $item_name;
            $grade_item->grademax = $max_grade;
            $grade_item->grademin = 0;
            $grade_item->gradepass = $max_grade * 0.6; // 60% pass
            $grade_item->display = GRADE_DISPLAY_TYPE_REAL;
            $grade_item->decimals = 1;
            
            // Insert the grade item
            $grade_item->insert();
            echo "✅ Created grade item: {$item_name} (Max: {$max_grade})<br>";
        } else {
            echo "✅ Found existing grade item: {$item_name}<br>";
        }
        
        // Create or update the grade
        $grade_grade = grade_grade::fetch(array('itemid' => $grade_item->id, 'userid' => $user->id));
        
        if (!$grade_grade) {
            $grade_grade = new grade_grade();
            $grade_grade->itemid = $grade_item->id;
            $grade_grade->userid = $user->id;
        }
        
        $grade_grade->rawgrade = $grade_value;
        $grade_grade->finalgrade = $grade_value;
        $grade_grade->timemodified = time();
        
        if ($grade_grade->id) {
            $grade_grade->update();
            echo "✅ Updated grade: {$item_name} = {$grade_value}/{$max_grade}<br>";
        } else {
            $grade_grade->insert();
            echo "✅ Created grade: {$item_name} = {$grade_value}/{$max_grade}<br>";
        }
        
        return true;
        
    } catch (Exception $e) {
        echo "⚠️ Error creating grade for {$item_name}: " . $e->getMessage() . "<br>";
        return false;
    }
}

try {
    // Get existing courses
    echo "<h2>📚 Finding Existing Courses</h2>";
    $courses = [];
    $course_mappings = [
        'AD101' => 'Algorithmic Design',
        'DSA101' => 'Data Structures and Algorithms', 
        'AIDEMO' => 'AI Fundamentals Demo'
    ];
    
    foreach ($course_mappings as $shortname => $expected_name) {
        $course = $DB->get_record('course', ['shortname' => $shortname]);
        if ($course) {
            $courses[$shortname] = $course;
            echo "✅ Found course: {$course->fullname} (ID: {$course->id})<br>";
            
            // Ensure grade structure exists
            grade_regrade_final_grades($course->id);
            
        } else {
            echo "⚠️ Course {$shortname} not found<br>";
        }
    }
    
    if (empty($courses)) {
        throw new Exception("No courses found. Please check if courses exist.");
    }
    
    echo "<hr>";
    
    // ===== SCENARIO 1: THE STRUGGLING STUDENT =====
    echo "<h2>📊 Scenario 1: The Struggling Student - Sarah Martinez</h2>";
    
    $sarah = create_or_get_user('sarah.martinez', 'Sarah', 'Martinez', 'sarah.martinez@demo.com');
    
    // Enroll Sarah in Algorithmic Design
    if (isset($courses['AD101'])) {
        enroll_user_in_course($sarah, $courses['AD101']);
        
        // Create grades with simpler approach
        $ad_grades = [
            ['Basic Sorting Quiz', 35],
            ['Array Operations Test', 28],
            ['Loop Logic Assignment', 42],
            ['Function Design Quiz', 38]
        ];
        
        foreach ($ad_grades as $grade_data) {
            create_safe_grade($courses['AD101'], $sarah, $grade_data[0], $grade_data[1], 100);
        }
    }
    
    // ===== SCENARIO 2: THE IMPROVING STUDENT =====
    echo "<h2>📈 Scenario 2: The Improving Student - Alex Johnson</h2>";
    
    $alex = create_or_get_user('alex.johnson', 'Alex', 'Johnson', 'alex.johnson@demo.com');
    
    // Enroll Alex in AI Fundamentals
    if (isset($courses['AIDEMO'])) {
        enroll_user_in_course($alex, $courses['AIDEMO']);
        
        $ai_grades = [
            ['ML Theory Quiz', 78],
            ['Neural Networks Test', 82],
            ['Deep Learning Project', 58],
            ['AI Ethics Assignment', 85]
        ];
        
        foreach ($ai_grades as $grade_data) {
            create_safe_grade($courses['AIDEMO'], $alex, $grade_data[0], $grade_data[1], 100);
        }
    }
    
    // Enroll Alex in Data Structures
    if (isset($courses['DSA101'])) {
        enroll_user_in_course($alex, $courses['DSA101']);
        
        $dsa_grades = [
            ['Arrays Implementation', 75],
            ['Stack Operations Quiz', 68],
            ['Tree Traversal Test', 62]
        ];
        
        foreach ($dsa_grades as $grade_data) {
            create_safe_grade($courses['DSA101'], $alex, $grade_data[0], $grade_data[1], 100);
        }
    }
    
    // ===== SCENARIO 3: THE HIGH ACHIEVER =====
    echo "<h2>🏆 Scenario 3: The High Achiever - David Chen</h2>";
    
    $david = create_or_get_user('david.chen', 'David', 'Chen', 'david.chen@demo.com');
    
    // Enroll David in Algorithmic Design
    if (isset($courses['AD101'])) {
        enroll_user_in_course($david, $courses['AD101']);
        
        $ad_grades = [
            ['Basic Sorting Quiz', 95],
            ['Array Operations Test', 96],
            ['Loop Logic Assignment', 94],
            ['Function Design Quiz', 97],
            ['Advanced Algorithms Project', 93]
        ];
        
        foreach ($ad_grades as $grade_data) {
            create_safe_grade($courses['AD101'], $david, $grade_data[0], $grade_data[1], 100);
        }
    }
    
    // Enroll David in Data Structures
    if (isset($courses['DSA101'])) {
        enroll_user_in_course($david, $courses['DSA101']);
        
        $dsa_grades = [
            ['Arrays Implementation', 98],
            ['Stack Operations Quiz', 96],
            ['Tree Traversal Test', 94],
            ['Graph Algorithms Project', 96]
        ];
        
        foreach ($dsa_grades as $grade_data) {
            create_safe_grade($courses['DSA101'], $david, $grade_data[0], $grade_data[1], 100);
        }
    }
    
    // Enroll David in AI Fundamentals
    if (isset($courses['AIDEMO'])) {
        enroll_user_in_course($david, $courses['AIDEMO']);
        
        $ai_grades = [
            ['ML Theory Quiz', 97],
            ['Neural Networks Test', 96],
            ['Deep Learning Project', 95],
            ['AI Ethics Assignment', 98],
            ['Advanced AI Research', 94]
        ];
        
        foreach ($ai_grades as $grade_data) {
            create_safe_grade($courses['AIDEMO'], $david, $grade_data[0], $grade_data[1], 100);
        }
    }
    
    // Force grade recalculation for all courses
    foreach ($courses as $course) {
        grade_regrade_final_grades($course->id);
        echo "✅ Recalculated grades for {$course->fullname}<br>";
    }
    
    echo "<hr>";
    echo "<h2>🎉 Fixed Demo Data Setup Complete!</h2>";
    
    echo "<div style='background: #d4edda; border: 1px solid #c3e6cb; padding: 15px; border-radius: 5px; margin: 20px 0;'>";
    echo "<h4 style='color: #155724; margin-top: 0;'>✅ Successfully Created:</h4>";
    echo "<ul style='color: #155724;'>";
    echo "<li><strong>3 Demo Students:</strong> Sarah (struggling), Alex (improving), David (excellent)</li>";
    echo "<li><strong>Proper Enrollments:</strong> Students enrolled in appropriate courses</li>";
    echo "<li><strong>Grade Items:</strong> Created using Moodle's official grade API</li>";
    echo "<li><strong>Realistic Grades:</strong> Performance patterns from 28% to 98%</li>";
    echo "<li><strong>Grade Structure:</strong> Properly integrated with Moodle gradebook</li>";
    echo "</ul>";
    echo "</div>";
    
    echo "<div style='margin: 30px 0; text-align: center;'>";
    echo "<a href='index.php' style='background: #007bff; color: white; padding: 15px 30px; text-decoration: none; border-radius: 8px; font-size: 18px; margin: 10px;'>🎯 Launch LearnPath Navigator</a>";
    echo "<a href='check_existing_data.php' style='background: #6c757d; color: white; padding: 15px 30px; text-decoration: none; border-radius: 8px; font-size: 18px; margin: 10px;'>🔍 Verify Demo Data</a>";
    echo "</div>";
    
    echo "<div style='background: #fff3cd; border: 1px solid #ffeaa7; padding: 15px; border-radius: 5px;'>";
    echo "<h4 style='color: #856404; margin-top: 0;'>📋 Next Steps:</h4>";
    echo "<ol style='color: #856404;'>";
    echo "<li>Go to LearnPath Navigator</li>";
    echo "<li>Select 'Live Moodle Data' as data source</li>";
    echo "<li>Choose a course and student to see the demo</li>";
    echo "<li>Test the AI features with real grade data</li>";
    echo "</ol>";
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
