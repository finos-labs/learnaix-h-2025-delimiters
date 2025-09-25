<?php
/**
 * Setup Demo Data for LearnPath Navigator - ROBUST VERSION
 * Creates three compelling demo scenarios with realistic quiz data
 * Handles existing data properly and ensures everything is created
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

echo "<h1>🎯 LearnPath Navigator Demo Data Setup - ROBUST VERSION</h1>";
echo "<p>Creating three compelling demo scenarios with proper data validation...</p>";
echo "<p><a href='check_existing_data.php'>🔍 Check Existing Data First</a></p>";
echo "<hr>";

// Helper function to create or get course
function create_or_get_course($shortname, $fullname, $summary) {
    global $DB;
    
    $course = $DB->get_record('course', ['shortname' => $shortname]);
    if (!$course) {
        $coursedata = new stdClass();
        $coursedata->fullname = $fullname;
        $coursedata->shortname = $shortname;
        $coursedata->category = 1;
        $coursedata->summary = $summary;
        $coursedata->summaryformat = FORMAT_HTML;
        $coursedata->format = 'topics';
        $coursedata->visible = 1;
        $coursedata->startdate = time();
        $coursedata->enddate = time() + (365 * 24 * 60 * 60);
        $coursedata->numsections = 10;
        
        $course = create_course($coursedata);
        echo "✅ Created course: {$fullname} (ID: {$course->id})<br>";
    } else {
        echo "✅ Found existing course: {$fullname} (ID: {$course->id})<br>";
    }
    return $course;
}

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

// Helper function to create grade item and grade
function create_grade_item_and_grade($course, $user, $item_name, $grade_value) {
    global $DB;
    
    // Check if grade item exists
    $grade_item = $DB->get_record('grade_items', [
        'courseid' => $course->id,
        'itemname' => $item_name
    ]);
    
    if (!$grade_item) {
        // Create grade item
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
        $grade_item_data->grademax = 100;
        $grade_item_data->grademin = 0;
        $grade_item_data->scaleid = null;
        $grade_item_data->outcomeid = null;
        $grade_item_data->gradepass = 60;
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
        echo "✅ Created grade item: {$item_name}<br>";
    } else {
        echo "✅ Found existing grade item: {$item_name}<br>";
    }
    
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
        $grade_data->rawgrade = $grade_value;
        $grade_data->rawgrademax = 100;
        $grade_data->rawgrademin = 0;
        $grade_data->rawscaleid = null;
        $grade_data->usermodified = $user->id;
        $grade_data->finalgrade = $grade_value;
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
        echo "✅ Created grade: {$item_name} = {$grade_value}%<br>";
    } else {
        // Update existing grade
        $existing_grade->rawgrade = $grade_value;
        $existing_grade->finalgrade = $grade_value;
        $existing_grade->timemodified = time();
        $DB->update_record('grade_grades', $existing_grade);
        echo "✅ Updated grade: {$item_name} = {$grade_value}%<br>";
    }
}

try {
    // ===== SCENARIO 1: THE STRUGGLING STUDENT =====
    echo "<h2>📊 Scenario 1: The Struggling Student</h2>";
    
    // Create courses and users using helper functions
    $biology_course = create_or_get_course('DEMO_BIO101', 'Introduction to Biology', 'Comprehensive introduction to biological sciences');
    $sarah = create_or_get_user('sarah.martinez', 'Sarah', 'Martinez', 'sarah.martinez@demo.com');
    
    // Enroll Sarah in Biology
    enroll_user_in_course($sarah, $biology_course);
    
    // Create Biology Quiz Grades
    $bio_quizzes = [
        ['Cell Biology Quiz', 35],
        ['Genetics Fundamentals', 42],
        ['Photosynthesis Test', 28],
        ['Evolution Quiz', 55]
    ];
    
    foreach ($bio_quizzes as $quiz_data) {
        create_grade_item_and_grade($biology_course, $sarah, $quiz_data[0], $quiz_data[1]);
    }
    
    // ===== SCENARIO 2: THE IMPROVING STUDENT =====
    echo "<h2>📈 Scenario 2: The Improving Student</h2>";
    
    $ai_course = create_or_get_course('DEMO_AI101', 'Introduction to Artificial Intelligence', 'Fundamentals of AI and machine learning');
    $alex = create_or_get_user('alex.johnson', 'Alex', 'Johnson', 'alex.johnson@demo.com');
    
    // Enroll Alex in AI course
    enroll_user_in_course($alex, $ai_course);
    
    // Create AI Quiz Grades
    $ai_quizzes = [
        ['Machine Learning Basics', 78],
        ['Neural Networks Quiz', 65],
        ['AI Ethics Test', 85],
        ['Deep Learning Quiz', 58]
    ];
    
    foreach ($ai_quizzes as $quiz_data) {
        create_grade_item_and_grade($ai_course, $alex, $quiz_data[0], $quiz_data[1]);
    }
    
    // ===== SCENARIO 3: THE HIGH ACHIEVER =====
    echo "<h2>🏆 Scenario 3: The High Achiever</h2>";
    
    $math_course = create_or_get_course('DEMO_MATH301', 'Advanced Mathematics', 'Advanced mathematical concepts and applications');
    $david = create_or_get_user('david.chen', 'David', 'Chen', 'david.chen@demo.com');
    
    // Enroll David in Math course
    enroll_user_in_course($david, $math_course);
    
    // Create Math Quiz Grades
    $math_quizzes = [
        ['Calculus Advanced', 95],
        ['Linear Algebra', 92],
        ['Statistics Quiz', 88],
        ['Differential Equations', 96]
    ];
    
    foreach ($math_quizzes as $quiz_data) {
        create_grade_item_and_grade($math_course, $david, $quiz_data[0], $quiz_data[1]);
    }
    
    echo "<hr>";
    echo "<h2>🎉 Demo Data Setup Complete!</h2>";
    echo "<h3>📊 Three Scenarios Created:</h3>";
    echo "<ul>";
    echo "<li><strong>Sarah Martinez</strong> - Introduction to Biology (40% average) - Struggling Student</li>";
    echo "<li><strong>Alex Johnson</strong> - Introduction to Artificial Intelligence (71% average) - Improving Student</li>";
    echo "<li><strong>David Chen</strong> - Advanced Mathematics (93% average) - High Achiever</li>";
    echo "</ul>";
    
    echo "<h3>🚀 Ready for Demo!</h3>";
    echo "<p>You can now showcase:</p>";
    echo "<ul>";
    echo "<li>🚨 <strong>Early Warning System</strong> - Sarah's failing grades trigger alerts</li>";
    echo "<li>🎯 <strong>Personalized Roadmaps</strong> - Different study plans for each student</li>";
    echo "<li>📚 <strong>Adaptive Resources</strong> - Basic tutorials for Sarah, advanced content for David</li>";
    echo "<li>🤖 <strong>Context-Aware Chatbot</strong> - AI knows each student's specific challenges</li>";
    echo "<li>📈 <strong>Performance Analytics</strong> - Visual dashboards showing different patterns</li>";
    echo "</ul>";
    
    echo "<p><a href='index.php' class='btn btn-primary'>🎯 Go to LearnPath Navigator</a></p>";
    echo "<p><a href='check_existing_data.php' class='btn btn-secondary'>🔍 Verify Demo Data</a></p>";
    
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
        $coursedata->summary = 'Comprehensive introduction to biological sciences';
        $coursedata->format = 'topics';
        $coursedata->visible = 1;
        $coursedata->startdate = time();
        $coursedata->enddate = time() + (365 * 24 * 60 * 60); // 1 year
        
        $biology_course = create_course($coursedata);
        echo "✅ Created Biology course (ID: {$biology_course->id})<br>";
    } else {
        echo "✅ Found existing Biology course (ID: {$biology_course->id})<br>";
    }
    
    // Create/Find Sarah Martinez
    $sarah = $DB->get_record('user', ['username' => 'sarah.martinez']);
    if (!$sarah) {
        $userdata = new stdClass();
        $userdata->username = 'sarah.martinez';
        $userdata->firstname = 'Sarah';
        $userdata->lastname = 'Martinez';
        $userdata->email = 'sarah.martinez@demo.com';
        $userdata->password = hash_internal_user_password('Demo123!');
        $userdata->confirmed = 1;
        $userdata->mnethostid = $CFG->mnet_localhost_id;
        
        $sarah_id = $DB->insert_record('user', $userdata);
        $sarah = $DB->get_record('user', ['id' => $sarah_id]);
        echo "✅ Created Sarah Martinez (ID: {$sarah->id})<br>";
    } else {
        echo "✅ Found existing Sarah Martinez (ID: {$sarah->id})<br>";
    }
    
    // Enroll Sarah in Biology
    $context = context_course::instance($biology_course->id);
    $student_role = $DB->get_record('role', ['shortname' => 'student']);
    
    if (!is_enrolled($context, $sarah)) {
        enrol_try_internal_enrol($biology_course->id, $sarah->id, $student_role->id);
        echo "✅ Enrolled Sarah in Biology course<br>";
    }
    
    // Create Biology Quiz Grade Items
    $bio_quizzes = [
        ['Cell Biology Quiz', 35],
        ['Genetics Fundamentals', 42],
        ['Photosynthesis Test', 28],
        ['Evolution Quiz', 55]
    ];
    
    foreach ($bio_quizzes as $quiz_data) {
        $grade_item = $DB->get_record('grade_items', [
            'courseid' => $biology_course->id,
            'itemname' => $quiz_data[0]
        ]);
        
        if (!$grade_item) {
            $grade_item = new stdClass();
            $grade_item->courseid = $biology_course->id;
            $grade_item->categoryid = null;
            $grade_item->itemname = $quiz_data[0];
            $grade_item->itemtype = 'manual';
            $grade_item->itemmodule = null;
            $grade_item->iteminstance = null;
            $grade_item->itemnumber = null;
            $grade_item->iteminfo = null;
            $grade_item->idnumber = null;
            $grade_item->calculation = null;
            $grade_item->gradetype = 1; // GRADE_TYPE_VALUE
            $grade_item->grademax = 100;
            $grade_item->grademin = 0;
            $grade_item->scaleid = null;
            $grade_item->outcomeid = null;
            $grade_item->gradepass = 60;
            $grade_item->multfactor = 1.0;
            $grade_item->plusfactor = 0.0;
            $grade_item->aggregationcoef = 0.0;
            $grade_item->aggregationcoef2 = 0.0;
            $grade_item->sortorder = 1;
            $grade_item->display = 0;
            $grade_item->decimals = null;
            $grade_item->hidden = 0;
            $grade_item->locked = 0;
            $grade_item->locktime = 0;
            $grade_item->needsupdate = 0;
            $grade_item->weightoverride = 0;
            $grade_item->timecreated = time();
            $grade_item->timemodified = time();
            
            $grade_item->id = $DB->insert_record('grade_items', $grade_item);
            echo "✅ Created grade item: {$quiz_data[0]}<br>";
        }
        
        // Create/Update Grade
        $grade = $DB->get_record('grade_grades', [
            'itemid' => $grade_item->id,
            'userid' => $sarah->id
        ]);
        
        if (!$grade) {
            $grade = new stdClass();
            $grade->itemid = $grade_item->id;
            $grade->userid = $sarah->id;
            $grade->rawgrade = $quiz_data[1];
            $grade->rawgrademax = 100;
            $grade->rawgrademin = 0;
            $grade->rawscaleid = null;
            $grade->usermodified = $sarah->id;
            $grade->finalgrade = $quiz_data[1];
            $grade->hidden = 0;
            $grade->locked = 0;
            $grade->locktime = 0;
            $grade->exported = 0;
            $grade->overridden = 0;
            $grade->excluded = 0;
            $grade->feedback = null;
            $grade->feedbackformat = 0;
            $grade->information = null;
            $grade->informationformat = 0;
            $grade->timecreated = time();
            $grade->timemodified = time();
            
            $DB->insert_record('grade_grades', $grade);
            echo "✅ Added grade: {$quiz_data[0]} = {$quiz_data[1]}%<br>";
        } else {
            $grade->rawgrade = $quiz_data[1];
            $grade->finalgrade = $quiz_data[1];
            $grade->timemodified = time();
            $DB->update_record('grade_grades', $grade);
            echo "✅ Updated grade: {$quiz_data[0]} = {$quiz_data[1]}%<br>";
        }
    }
    
    // ===== SCENARIO 2: THE IMPROVING STUDENT =====
    echo "<h2>📈 Scenario 2: The Improving Student</h2>";
    
    // Create/Find AI Course
    $ai_course = $DB->get_record('course', ['shortname' => 'DEMO_AI101']);
    if (!$ai_course) {
        $coursedata = new stdClass();
        $coursedata->fullname = 'Introduction to Artificial Intelligence';
        $coursedata->shortname = 'DEMO_AI101';
        $coursedata->category = 1;
        $coursedata->summary = 'Fundamentals of AI and machine learning';
        $coursedata->format = 'topics';
        $coursedata->visible = 1;
        $coursedata->startdate = time();
        $coursedata->enddate = time() + (365 * 24 * 60 * 60);
        
        $ai_course = create_course($coursedata);
        echo "✅ Created AI course (ID: {$ai_course->id})<br>";
    } else {
        echo "✅ Found existing AI course (ID: {$ai_course->id})<br>";
    }
    
    // Create/Find Alex Johnson
    $alex = $DB->get_record('user', ['username' => 'alex.johnson']);
    if (!$alex) {
        $userdata = new stdClass();
        $userdata->username = 'alex.johnson';
        $userdata->firstname = 'Alex';
        $userdata->lastname = 'Johnson';
        $userdata->email = 'alex.johnson@demo.com';
        $userdata->password = hash_internal_user_password('Demo123!');
        $userdata->confirmed = 1;
        $userdata->mnethostid = $CFG->mnet_localhost_id;
        
        $alex_id = $DB->insert_record('user', $userdata);
        $alex = $DB->get_record('user', ['id' => $alex_id]);
        echo "✅ Created Alex Johnson (ID: {$alex->id})<br>";
    } else {
        echo "✅ Found existing Alex Johnson (ID: {$alex->id})<br>";
    }
    
    // Enroll Alex in AI course
    $context = context_course::instance($ai_course->id);
    if (!is_enrolled($context, $alex)) {
        enrol_try_internal_enrol($ai_course->id, $alex->id, $student_role->id);
        echo "✅ Enrolled Alex in AI course<br>";
    }
    
    // Create AI Quiz Grade Items
    $ai_quizzes = [
        ['Machine Learning Basics', 78],
        ['Neural Networks Quiz', 65],
        ['AI Ethics Test', 85],
        ['Deep Learning Quiz', 58]
    ];
    
    foreach ($ai_quizzes as $quiz_data) {
        $grade_item = $DB->get_record('grade_items', [
            'courseid' => $ai_course->id,
            'itemname' => $quiz_data[0]
        ]);
        
        if (!$grade_item) {
            $grade_item = new stdClass();
            $grade_item->courseid = $ai_course->id;
            $grade_item->categoryid = null;
            $grade_item->itemname = $quiz_data[0];
            $grade_item->itemtype = 'manual';
            $grade_item->itemmodule = null;
            $grade_item->iteminstance = null;
            $grade_item->itemnumber = null;
            $grade_item->iteminfo = null;
            $grade_item->idnumber = null;
            $grade_item->calculation = null;
            $grade_item->gradetype = 1;
            $grade_item->grademax = 100;
            $grade_item->grademin = 0;
            $grade_item->scaleid = null;
            $grade_item->outcomeid = null;
            $grade_item->gradepass = 60;
            $grade_item->multfactor = 1.0;
            $grade_item->plusfactor = 0.0;
            $grade_item->aggregationcoef = 0.0;
            $grade_item->aggregationcoef2 = 0.0;
            $grade_item->sortorder = 1;
            $grade_item->display = 0;
            $grade_item->decimals = null;
            $grade_item->hidden = 0;
            $grade_item->locked = 0;
            $grade_item->locktime = 0;
            $grade_item->needsupdate = 0;
            $grade_item->weightoverride = 0;
            $grade_item->timecreated = time();
            $grade_item->timemodified = time();
            
            $grade_item->id = $DB->insert_record('grade_items', $grade_item);
            echo "✅ Created grade item: {$quiz_data[0]}<br>";
        }
        
        // Create/Update Grade
        $grade = $DB->get_record('grade_grades', [
            'itemid' => $grade_item->id,
            'userid' => $alex->id
        ]);
        
        if (!$grade) {
            $grade = new stdClass();
            $grade->itemid = $grade_item->id;
            $grade->userid = $alex->id;
            $grade->rawgrade = $quiz_data[1];
            $grade->rawgrademax = 100;
            $grade->rawgrademin = 0;
            $grade->rawscaleid = null;
            $grade->usermodified = $alex->id;
            $grade->finalgrade = $quiz_data[1];
            $grade->hidden = 0;
            $grade->locked = 0;
            $grade->locktime = 0;
            $grade->exported = 0;
            $grade->overridden = 0;
            $grade->excluded = 0;
            $grade->feedback = null;
            $grade->feedbackformat = 0;
            $grade->information = null;
            $grade->informationformat = 0;
            $grade->timecreated = time();
            $grade->timemodified = time();
            
            $DB->insert_record('grade_grades', $grade);
            echo "✅ Added grade: {$quiz_data[0]} = {$quiz_data[1]}%<br>";
        } else {
            $grade->rawgrade = $quiz_data[1];
            $grade->finalgrade = $quiz_data[1];
            $grade->timemodified = time();
            $DB->update_record('grade_grades', $grade);
            echo "✅ Updated grade: {$quiz_data[0]} = {$quiz_data[1]}%<br>";
        }
    }
    
    // ===== SCENARIO 3: THE HIGH ACHIEVER =====
    echo "<h2>🏆 Scenario 3: The High Achiever</h2>";
    
    // Create/Find Math Course
    $math_course = $DB->get_record('course', ['shortname' => 'DEMO_MATH301']);
    if (!$math_course) {
        $coursedata = new stdClass();
        $coursedata->fullname = 'Advanced Mathematics';
        $coursedata->shortname = 'DEMO_MATH301';
        $coursedata->category = 1;
        $coursedata->summary = 'Advanced mathematical concepts and applications';
        $coursedata->format = 'topics';
        $coursedata->visible = 1;
        $coursedata->startdate = time();
        $coursedata->enddate = time() + (365 * 24 * 60 * 60);
        
        $math_course = create_course($coursedata);
        echo "✅ Created Math course (ID: {$math_course->id})<br>";
    } else {
        echo "✅ Found existing Math course (ID: {$math_course->id})<br>";
    }
    
    // Create/Find David Chen
    $david = $DB->get_record('user', ['username' => 'david.chen']);
    if (!$david) {
        $userdata = new stdClass();
        $userdata->username = 'david.chen';
        $userdata->firstname = 'David';
        $userdata->lastname = 'Chen';
        $userdata->email = 'david.chen@demo.com';
        $userdata->password = hash_internal_user_password('Demo123!');
        $userdata->confirmed = 1;
        $userdata->mnethostid = $CFG->mnet_localhost_id;
        
        $david_id = $DB->insert_record('user', $userdata);
        $david = $DB->get_record('user', ['id' => $david_id]);
        echo "✅ Created David Chen (ID: {$david->id})<br>";
    } else {
        echo "✅ Found existing David Chen (ID: {$david->id})<br>";
    }
    
    // Enroll David in Math course
    $context = context_course::instance($math_course->id);
    if (!is_enrolled($context, $david)) {
        enrol_try_internal_enrol($math_course->id, $david->id, $student_role->id);
        echo "✅ Enrolled David in Math course<br>";
    }
    
    // Create Math Quiz Grade Items
    $math_quizzes = [
        ['Calculus Advanced', 95],
        ['Linear Algebra', 92],
        ['Statistics Quiz', 88],
        ['Differential Equations', 96]
    ];
    
    foreach ($math_quizzes as $quiz_data) {
        $grade_item = $DB->get_record('grade_items', [
            'courseid' => $math_course->id,
            'itemname' => $quiz_data[0]
        ]);
        
        if (!$grade_item) {
            $grade_item = new stdClass();
            $grade_item->courseid = $math_course->id;
            $grade_item->categoryid = null;
            $grade_item->itemname = $quiz_data[0];
            $grade_item->itemtype = 'manual';
            $grade_item->itemmodule = null;
            $grade_item->iteminstance = null;
            $grade_item->itemnumber = null;
            $grade_item->iteminfo = null;
            $grade_item->idnumber = null;
            $grade_item->calculation = null;
            $grade_item->gradetype = 1;
            $grade_item->grademax = 100;
            $grade_item->grademin = 0;
            $grade_item->scaleid = null;
            $grade_item->outcomeid = null;
            $grade_item->gradepass = 60;
            $grade_item->multfactor = 1.0;
            $grade_item->plusfactor = 0.0;
            $grade_item->aggregationcoef = 0.0;
            $grade_item->aggregationcoef2 = 0.0;
            $grade_item->sortorder = 1;
            $grade_item->display = 0;
            $grade_item->decimals = null;
            $grade_item->hidden = 0;
            $grade_item->locked = 0;
            $grade_item->locktime = 0;
            $grade_item->needsupdate = 0;
            $grade_item->weightoverride = 0;
            $grade_item->timecreated = time();
            $grade_item->timemodified = time();
            
            $grade_item->id = $DB->insert_record('grade_items', $grade_item);
            echo "✅ Created grade item: {$quiz_data[0]}<br>";
        }
        
        // Create/Update Grade
        $grade = $DB->get_record('grade_grades', [
            'itemid' => $grade_item->id,
            'userid' => $david->id
        ]);
        
        if (!$grade) {
            $grade = new stdClass();
            $grade->itemid = $grade_item->id;
            $grade->userid = $david->id;
            $grade->rawgrade = $quiz_data[1];
            $grade->rawgrademax = 100;
            $grade->rawgrademin = 0;
            $grade->rawscaleid = null;
            $grade->usermodified = $david->id;
            $grade->finalgrade = $quiz_data[1];
            $grade->hidden = 0;
            $grade->locked = 0;
            $grade->locktime = 0;
            $grade->exported = 0;
            $grade->overridden = 0;
            $grade->excluded = 0;
            $grade->feedback = null;
            $grade->feedbackformat = 0;
            $grade->information = null;
            $grade->informationformat = 0;
            $grade->timecreated = time();
            $grade->timemodified = time();
            
            $DB->insert_record('grade_grades', $grade);
            echo "✅ Added grade: {$quiz_data[0]} = {$quiz_data[1]}%<br>";
        } else {
            $grade->rawgrade = $quiz_data[1];
            $grade->finalgrade = $quiz_data[1];
            $grade->timemodified = time();
            $DB->update_record('grade_grades', $grade);
            echo "✅ Updated grade: {$quiz_data[0]} = {$quiz_data[1]}%<br>";
        }
    }
    
    echo "<hr>";
    echo "<h2>🎉 Demo Data Setup Complete!</h2>";
    echo "<h3>📊 Three Scenarios Created:</h3>";
    echo "<ul>";
    echo "<li><strong>Sarah Martinez</strong> - Introduction to Biology (40% average) - Struggling Student</li>";
    echo "<li><strong>Alex Johnson</strong> - Introduction to Artificial Intelligence (71% average) - Improving Student</li>";
    echo "<li><strong>David Chen</strong> - Advanced Mathematics (93% average) - High Achiever</li>";
    echo "</ul>";
    
    echo "<h3>🚀 Ready for Demo!</h3>";
    echo "<p>You can now showcase:</p>";
    echo "<ul>";
    echo "<li>🚨 <strong>Early Warning System</strong> - Sarah's failing grades trigger alerts</li>";
    echo "<li>🎯 <strong>Personalized Roadmaps</strong> - Different study plans for each student</li>";
    echo "<li>📚 <strong>Adaptive Resources</strong> - Basic tutorials for Sarah, advanced content for David</li>";
    echo "<li>🤖 <strong>Context-Aware Chatbot</strong> - AI knows each student's specific challenges</li>";
    echo "<li>📈 <strong>Performance Analytics</strong> - Visual dashboards showing different patterns</li>";
    echo "</ul>";
    
    echo "<p><a href='index.php' class='btn btn-primary'>🎯 Go to LearnPath Navigator</a></p>";
    
} catch (Exception $e) {
    echo "<div class='alert alert-danger'>❌ Error: " . $e->getMessage() . "</div>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
}
?>
