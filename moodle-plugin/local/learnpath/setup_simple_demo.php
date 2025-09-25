<?php
/**
 * Setup Simple Demo Data for LearnPath Navigator
 * Uses direct database approach to avoid grade structure issues
 * Team Delimiters - NatWest Hack4aCause
 */

require_once(__DIR__ . '/../../config.php');
require_once($CFG->libdir . '/filelib.php');
require_once($CFG->dirroot . '/course/lib.php');
require_once($CFG->dirroot . '/user/lib.php');
require_once($CFG->dirroot . '/enrol/locallib.php');
require_once($CFG->dirroot . '/lib/enrollib.php');

require_login();
require_capability('moodle/site:config', context_system::instance());

echo "<h1>🎯 LearnPath Navigator - Simple Demo Setup</h1>";
echo "<p>Creating demo data with direct database approach...</p>";
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

// Simple grade creation function
function create_simple_grade($course, $user, $item_name, $grade_value) {
    global $DB;
    
    try {
        // Get the course category for grades
        $course_category = $DB->get_record('grade_categories', [
            'courseid' => $course->id,
            'depth' => 1
        ]);
        
        if (!$course_category) {
            echo "⚠️ No grade category found for course {$course->fullname}<br>";
            return false;
        }
        
        // Check if grade item exists
        $grade_item = $DB->get_record('grade_items', [
            'courseid' => $course->id,
            'itemname' => $item_name
        ]);
        
        if (!$grade_item) {
            // Create grade item with minimal required fields
            $grade_item_data = new stdClass();
            $grade_item_data->courseid = $course->id;
            $grade_item_data->categoryid = $course_category->id;
            $grade_item_data->itemname = $item_name;
            $grade_item_data->itemtype = 'manual';
            $grade_item_data->itemmodule = null;
            $grade_item_data->iteminstance = null;
            $grade_item_data->itemnumber = null;
            $grade_item_data->iteminfo = null;
            $grade_item_data->idnumber = null;
            $grade_item_data->calculation = null;
            $grade_item_data->gradetype = 1; // GRADE_TYPE_VALUE
            $grade_item_data->grademax = 10.00000;
            $grade_item_data->grademin = 0.00000;
            $grade_item_data->scaleid = null;
            $grade_item_data->outcomeid = null;
            $grade_item_data->gradepass = 6.00000;
            $grade_item_data->multfactor = 1.00000;
            $grade_item_data->plusfactor = 0.00000;
            $grade_item_data->aggregationcoef = 0.00000;
            $grade_item_data->aggregationcoef2 = 0.00000;
            $grade_item_data->sortorder = 1;
            $grade_item_data->display = 0;
            $grade_item_data->decimals = null;
            $grade_item_data->hidden = 0;
            $grade_item_data->locked = 0;
            $grade_item_data->locktime = 0;
            $grade_item_data->needsupdate = 0;
            $grade_item_data->weightoverride = 0;
            $grade_item_data->timecreated = time();
            $grade_item_data->timemodified = time();
            
            $grade_item_id = $DB->insert_record('grade_items', $grade_item_data);
            $grade_item = $DB->get_record('grade_items', ['id' => $grade_item_id]);
            echo "✅ Created grade item: {$item_name}<br>";
        }
        
        // Convert percentage to 10-point scale
        $actual_grade = ($grade_value / 100) * 10;
        
        // Check if grade exists
        $existing_grade = $DB->get_record('grade_grades', [
            'itemid' => $grade_item->id,
            'userid' => $user->id
        ]);
        
        if (!$existing_grade) {
            // Create grade
            $grade_data = new stdClass();
            $grade_data->itemid = $grade_item->id;
            $grade_data->userid = $user->id;
            $grade_data->rawgrade = $actual_grade;
            $grade_data->rawgrademax = 10.00000;
            $grade_data->rawgrademin = 0.00000;
            $grade_data->rawscaleid = null;
            $grade_data->usermodified = $user->id;
            $grade_data->finalgrade = $actual_grade;
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
            echo "✅ Created grade: {$item_name} = {$actual_grade}/10 ({$grade_value}%)<br>";
        } else {
            // Update existing grade
            $existing_grade->rawgrade = $actual_grade;
            $existing_grade->finalgrade = $actual_grade;
            $existing_grade->timemodified = time();
            $DB->update_record('grade_grades', $existing_grade);
            echo "✅ Updated grade: {$item_name} = {$actual_grade}/10 ({$grade_value}%)<br>";
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
        } else {
            echo "⚠️ Course {$shortname} not found<br>";
        }
    }
    
    if (empty($courses)) {
        throw new Exception("No courses found. Please check if courses exist with shortnames: " . implode(', ', array_keys($course_mappings)));
    }
    
    echo "<hr>";
    
    // ===== SCENARIO 1: THE STRUGGLING STUDENT =====
    echo "<h2>📊 Scenario 1: The Struggling Student - Sarah Martinez</h2>";
    
    $sarah = create_or_get_user('sarah.martinez', 'Sarah', 'Martinez', 'sarah.martinez@demo.com');
    
    // Enroll Sarah in Algorithmic Design
    if (isset($courses['AD101'])) {
        enroll_user_in_course($sarah, $courses['AD101']);
        
        $ad_grades = [
            ['Sorting Algorithms Quiz', 35],
            ['Array Operations Test', 28],
            ['Loop Structures Assignment', 42]
        ];
        
        foreach ($ad_grades as $grade_data) {
            create_simple_grade($courses['AD101'], $sarah, $grade_data[0], $grade_data[1]);
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
            ['Neural Networks Test', 65],
            ['AI Ethics Assignment', 85]
        ];
        
        foreach ($ai_grades as $grade_data) {
            create_simple_grade($courses['AIDEMO'], $alex, $grade_data[0], $grade_data[1]);
        }
    }
    
    // ===== SCENARIO 3: THE HIGH ACHIEVER =====
    echo "<h2>🏆 Scenario 3: The High Achiever - David Chen</h2>";
    
    $david = create_or_get_user('david.chen', 'David', 'Chen', 'david.chen@demo.com');
    
    // Enroll David in multiple courses
    if (isset($courses['AD101'])) {
        enroll_user_in_course($david, $courses['AD101']);
        
        $ad_grades = [
            ['Sorting Algorithms Quiz', 95],
            ['Array Operations Test', 96],
            ['Loop Structures Assignment', 94]
        ];
        
        foreach ($ad_grades as $grade_data) {
            create_simple_grade($courses['AD101'], $david, $grade_data[0], $grade_data[1]);
        }
    }
    
    if (isset($courses['DSA101'])) {
        enroll_user_in_course($david, $courses['DSA101']);
        
        $dsa_grades = [
            ['Data Structures Quiz', 98],
            ['Algorithm Analysis Test', 96]
        ];
        
        foreach ($dsa_grades as $grade_data) {
            create_simple_grade($courses['DSA101'], $david, $grade_data[0], $grade_data[1]);
        }
    }
    
    echo "<hr>";
    echo "<h2>🎉 Simple Demo Data Setup Complete!</h2>";
    
    echo "<div style='background: #d4edda; border: 1px solid #c3e6cb; padding: 15px; border-radius: 5px; margin: 20px 0;'>";
    echo "<h4 style='color: #155724; margin-top: 0;'>✅ Successfully Created:</h4>";
    echo "<ul style='color: #155724;'>";
    echo "<li><strong>Sarah Martinez:</strong> Struggling student in Algorithmic Design (35% avg)</li>";
    echo "<li><strong>Alex Johnson:</strong> Improving student in AI Fundamentals (76% avg)</li>";
    echo "<li><strong>David Chen:</strong> High achiever across multiple courses (95%+ avg)</li>";
    echo "</ul>";
    echo "</div>";
    
    echo "<div style='margin: 30px 0; text-align: center;'>";
    echo "<a href='index.php' style='background: #007bff; color: white; padding: 15px 30px; text-decoration: none; border-radius: 8px; font-size: 18px; margin: 10px;'>🎯 Launch LearnPath Navigator</a>";
    echo "<a href='check_existing_data.php' style='background: #6c757d; color: white; padding: 15px 30px; text-decoration: none; border-radius: 8px; font-size: 18px; margin: 10px;'>🔍 Verify Demo Data</a>";
    echo "</div>";
    
    echo "<div style='background: #fff3cd; border: 1px solid #ffeaa7; padding: 15px; border-radius: 5px;'>";
    echo "<h4 style='color: #856404; margin-top: 0;'>📋 Demo Ready!</h4>";
    echo "<p style='color: #856404;'>Your demo data is now ready. Go to LearnPath Navigator and select 'Live Moodle Data' to see the three scenarios in action.</p>";
    echo "</div>";
    
} catch (Exception $e) {
    echo "<div style='color: red; background: #ffe6e6; padding: 15px; border: 1px solid #ff0000; border-radius: 5px;'>";
    echo "<h3>❌ Error occurred:</h3>";
    echo "<p><strong>Message:</strong> " . $e->getMessage() . "</p>";
    echo "<p><strong>File:</strong> " . $e->getFile() . "</p>";
    echo "<p><strong>Line:</strong> " . $e->getLine() . "</p>";
    echo "</div>";
}
?>
