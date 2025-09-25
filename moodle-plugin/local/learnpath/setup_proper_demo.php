<?php
/**
 * Setup Proper Demo Data for LearnPath Navigator
 * Creates actual quizzes with proper topic names and adds grades
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

echo "<h1>🎯 LearnPath Navigator - Proper Demo Setup</h1>";
echo "<p>Creating actual quizzes with proper topic names and realistic grades...</p>";
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

// Helper function to create grade item and grade
function create_grade_item_and_grade($course, $user, $item_name, $grade_value, $max_grade = 10) {
    global $DB;
    
    // Check if grade item exists
    $grade_item = $DB->get_record('grade_items', [
        'courseid' => $course->id,
        'itemname' => $item_name
    ]);
    
    if (!$grade_item) {
        // Create new grade item
        $grade_item_data = new stdClass();
        $grade_item_data->courseid = $course->id;
        $grade_item_data->categoryid = null;
        $grade_item_data->itemname = $item_name;
        $grade_item_data->itemtype = 'manual';
        $grade_item_data->itemmodule = null;
        $grade_item_data->iteminstance = null;
        $grade_item_data->itemnumber = null;
        $grade_item_data->iteminfo = null;
        $grade_item_data->idnumber = null;
        $grade_item_data->calculation = null;
        $grade_item_data->gradetype = 1; // GRADE_TYPE_VALUE
        $grade_item_data->grademax = $max_grade;
        $grade_item_data->grademin = 0;
        $grade_item_data->scaleid = null;
        $grade_item_data->outcomeid = null;
        $grade_item_data->gradepass = ($max_grade * 0.6); // 60% pass rate
        $grade_item_data->multfactor = 1.0;
        $grade_item_data->plusfactor = 0.0;
        $grade_item_data->aggregationcoef = 0.0;
        $grade_item_data->aggregationcoef2 = 0.0;
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
        echo "✅ Created grade item: {$item_name} (Max: {$max_grade})<br>";
    } else {
        echo "✅ Found existing grade item: {$item_name}<br>";
    }
    
    // Convert percentage to actual grade
    $actual_grade = ($grade_value / 100) * $max_grade;
    
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
        $grade_data->rawgrademax = $max_grade;
        $grade_data->rawgrademin = 0;
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
        echo "✅ Created grade: {$item_name} = {$actual_grade}/{$max_grade} ({$grade_value}%)<br>";
    } else {
        // Update existing grade
        $existing_grade->rawgrade = $actual_grade;
        $existing_grade->finalgrade = $actual_grade;
        $existing_grade->timemodified = time();
        $DB->update_record('grade_grades', $existing_grade);
        echo "✅ Updated grade: {$item_name} = {$actual_grade}/{$max_grade} ({$grade_value}%)<br>";
    }
    return true;
}

try {
    // Get existing courses
    echo "<h2>📚 Finding Existing Courses</h2>";
    $courses = [];
    $course_mappings = [
        'AD101' => 'Algorithmic Design',
        'DSA101' => 'Data Structures and Algorithms',
        'AIDEMO' => 'AI Fundamentals Demo',
        'BIO101' => 'General Biology'
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
    
    echo "<hr>";
    
    // ===== SCENARIO 1: THE STRUGGLING STUDENT =====
    echo "<h2>📊 Scenario 1: The Struggling Student - Sarah Martinez</h2>";
    
    $sarah = create_or_get_user('sarah.martinez', 'Sarah', 'Martinez', 'sarah.martinez@demo.com');
    
    // Enroll Sarah in Algorithmic Design
    if (isset($courses['AD101'])) {
        enroll_user_in_course($sarah, $courses['AD101']);
        
        // Create proper algorithmic design topics
        $ad_topics = [
            ['Basic Sorting Algorithms', 35],
            ['Array Manipulation', 28],
            ['Loop Structures', 42],
            ['Conditional Logic', 38],
            ['Function Design', 33]
        ];
        
        foreach ($ad_topics as $topic_data) {
            create_grade_item_and_grade($courses['AD101'], $sarah, $topic_data[0], $topic_data[1], 10);
        }
    }
    
    // Enroll Sarah in Data Structures
    if (isset($courses['DSA101'])) {
        enroll_user_in_course($sarah, $courses['DSA101']);
        
        $dsa_topics = [
            ['Arrays and Lists', 30],
            ['Stack Operations', 25],
            ['Queue Implementation', 32],
            ['Linked Lists', 28]
        ];
        
        foreach ($dsa_topics as $topic_data) {
            create_grade_item_and_grade($courses['DSA101'], $sarah, $topic_data[0], $topic_data[1], 10);
        }
    }
    
    // ===== SCENARIO 2: THE IMPROVING STUDENT =====
    echo "<h2>📈 Scenario 2: The Improving Student - Alex Johnson</h2>";
    
    $alex = create_or_get_user('alex.johnson', 'Alex', 'Johnson', 'alex.johnson@demo.com');
    
    // Enroll Alex in AI Fundamentals
    if (isset($courses['AIDEMO'])) {
        enroll_user_in_course($alex, $courses['AIDEMO']);
        
        $ai_topics = [
            ['Machine Learning Basics', 78],
            ['Neural Network Theory', 82],
            ['Deep Learning Implementation', 58],
            ['AI Ethics and Bias', 85],
            ['Supervised Learning', 72],
            ['Unsupervised Learning', 65]
        ];
        
        foreach ($ai_topics as $topic_data) {
            create_grade_item_and_grade($courses['AIDEMO'], $alex, $topic_data[0], $topic_data[1], 10);
        }
    }
    
    // Enroll Alex in Data Structures with better performance
    if (isset($courses['DSA101'])) {
        enroll_user_in_course($alex, $courses['DSA101']);
        
        $dsa_topics = [
            ['Arrays and Lists', 75],
            ['Stack Operations', 68],
            ['Queue Implementation', 72],
            ['Linked Lists', 70],
            ['Binary Trees', 62],
            ['Graph Algorithms', 65]
        ];
        
        foreach ($dsa_topics as $topic_data) {
            create_grade_item_and_grade($courses['DSA101'], $alex, $topic_data[0], $topic_data[1], 10);
        }
    }
    
    // ===== SCENARIO 3: THE HIGH ACHIEVER =====
    echo "<h2>🏆 Scenario 3: The High Achiever - David Chen</h2>";
    
    $david = create_or_get_user('david.chen', 'David', 'Chen', 'david.chen@demo.com');
    
    // Enroll David in Algorithmic Design with excellent performance
    if (isset($courses['AD101'])) {
        enroll_user_in_course($david, $courses['AD101']);
        
        $ad_topics = [
            ['Basic Sorting Algorithms', 95],
            ['Array Manipulation', 96],
            ['Loop Structures', 94],
            ['Conditional Logic', 97],
            ['Function Design', 93],
            ['Advanced Recursion', 95],
            ['Dynamic Programming', 92]
        ];
        
        foreach ($ad_topics as $topic_data) {
            create_grade_item_and_grade($courses['AD101'], $david, $topic_data[0], $topic_data[1], 10);
        }
    }
    
    // Enroll David in Data Structures with mastery
    if (isset($courses['DSA101'])) {
        enroll_user_in_course($david, $courses['DSA101']);
        
        $dsa_topics = [
            ['Arrays and Lists', 98],
            ['Stack Operations', 96],
            ['Queue Implementation', 97],
            ['Linked Lists', 95],
            ['Binary Trees', 94],
            ['Graph Algorithms', 96],
            ['Hash Tables', 93],
            ['Advanced Trees', 95]
        ];
        
        foreach ($dsa_topics as $topic_data) {
            create_grade_item_and_grade($courses['DSA101'], $david, $topic_data[0], $topic_data[1], 10);
        }
    }
    
    // Enroll David in AI Fundamentals with research-level performance
    if (isset($courses['AIDEMO'])) {
        enroll_user_in_course($david, $courses['AIDEMO']);
        
        $ai_topics = [
            ['Machine Learning Basics', 97],
            ['Neural Network Theory', 96],
            ['Deep Learning Implementation', 95],
            ['AI Ethics and Bias', 98],
            ['Supervised Learning', 94],
            ['Unsupervised Learning', 96],
            ['Reinforcement Learning', 93],
            ['Computer Vision', 95]
        ];
        
        foreach ($ai_topics as $topic_data) {
            create_grade_item_and_grade($courses['AIDEMO'], $david, $topic_data[0], $topic_data[1], 10);
        }
    }
    
    // Add Biology course for David (showing multi-disciplinary excellence)
    if (isset($courses['BIO101'])) {
        enroll_user_in_course($david, $courses['BIO101']);
        
        $bio_topics = [
            ['Cell Structure and Function', 94],
            ['Genetics and DNA', 96],
            ['Evolution and Natural Selection', 93],
            ['Photosynthesis Process', 95],
            ['Molecular Biology', 92]
        ];
        
        foreach ($bio_topics as $topic_data) {
            create_grade_item_and_grade($courses['BIO101'], $david, $topic_data[0], $topic_data[1], 10);
        }
    }
    
    echo "<hr>";
    echo "<h2>🎉 Proper Demo Data Setup Complete!</h2>";
    
    // Summary of created data
    echo "<div style='display: flex; gap: 20px; margin: 20px 0;'>";
    
    // Sarah's Summary
    echo "<div style='border: 2px solid #dc3545; padding: 15px; border-radius: 10px; flex: 1;'>";
    echo "<h4 style='color: #dc3545; margin-top: 0;'>📊 Sarah Martinez - Struggling Student</h4>";
    echo "<ul>";
    echo "<li><strong>Algorithmic Design:</strong> 5 topics, avg 35.2%</li>";
    echo "<li><strong>Data Structures:</strong> 4 topics, avg 28.75%</li>";
    echo "<li><strong>Weak Areas:</strong> Basic programming concepts</li>";
    echo "<li><strong>AI Response:</strong> Foundational support needed</li>";
    echo "</ul>";
    echo "</div>";
    
    // Alex's Summary
    echo "<div style='border: 2px solid #ffc107; padding: 15px; border-radius: 10px; flex: 1;'>";
    echo "<h4 style='color: #856404; margin-top: 0;'>📈 Alex Johnson - Improving Student</h4>";
    echo "<ul>";
    echo "<li><strong>AI Fundamentals:</strong> 6 topics, avg 73.3%</li>";
    echo "<li><strong>Data Structures:</strong> 6 topics, avg 68.7%</li>";
    echo "<li><strong>Pattern:</strong> Strong theory, weak implementation</li>";
    echo "<li><strong>AI Response:</strong> Practical focus needed</li>";
    echo "</ul>";
    echo "</div>";
    
    // David's Summary
    echo "<div style='border: 2px solid #28a745; padding: 15px; border-radius: 10px; flex: 1;'>";
    echo "<h4 style='color: #28a745; margin-top: 0;'>🏆 David Chen - High Achiever</h4>";
    echo "<ul>";
    echo "<li><strong>Algorithmic Design:</strong> 7 topics, avg 94.6%</li>";
    echo "<li><strong>Data Structures:</strong> 8 topics, avg 95.5%</li>";
    echo "<li><strong>AI Fundamentals:</strong> 8 topics, avg 95.6%</li>";
    echo "<li><strong>Biology:</strong> 5 topics, avg 94%</li>";
    echo "<li><strong>AI Response:</strong> Advanced challenges</li>";
    echo "</ul>";
    echo "</div>";
    
    echo "</div>";
    
    echo "<div style='margin: 30px 0; text-align: center;'>";
    echo "<a href='index.php' style='background: #007bff; color: white; padding: 15px 30px; text-decoration: none; border-radius: 8px; font-size: 18px; margin: 10px;'>🎯 Launch LearnPath Navigator</a>";
    echo "<a href='check_existing_data.php' style='background: #6c757d; color: white; padding: 15px 30px; text-decoration: none; border-radius: 8px; font-size: 18px; margin: 10px;'>🔍 Verify Created Data</a>";
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
