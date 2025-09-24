<?php
/**
 * Quick setup for sample course and grade data
 */

require_once(__DIR__ . '/../../config.php');
require_login();

global $DB, $USER;

echo "<h2>🎓 Setup Sample Course Data for LearnPath Navigator</h2>";

// Check if we're admin
if (!is_siteadmin()) {
    echo "<p style='color: red;'>You need to be a site administrator to run this setup.</p>";
    exit;
}

try {
    // Create or get course
    $course_data = [
        'fullname' => 'Data Science Fundamentals',
        'shortname' => 'DS101',
        'category' => 1, // Miscellaneous category
        'summary' => 'Learn data science with AI-powered guidance from LearnPath Navigator',
        'summaryformat' => FORMAT_HTML,
        'format' => 'topics',
        'numsections' => 6,
        'startdate' => time(),
        'visible' => 1,
        'timecreated' => time(),
        'timemodified' => time()
    ];
    
    $existing_course = $DB->get_record('course', ['shortname' => 'DS101']);
    if ($existing_course) {
        $courseid = $existing_course->id;
        echo "✅ Using existing course: {$existing_course->fullname} (ID: $courseid)<br>";
    } else {
        $courseid = $DB->insert_record('course', $course_data);
        echo "✅ Created new course: Data Science Fundamentals (ID: $courseid)<br>";
    }
    
    // Create diverse sample students
    $students = [
        ['username' => 'priya.sharma', 'firstname' => 'Priya', 'lastname' => 'Sharma', 'email' => 'priya.sharma@example.com'],
        ['username' => 'james.wilson', 'firstname' => 'James', 'lastname' => 'Wilson', 'email' => 'james.wilson@example.com'],
        ['username' => 'aisha.patel', 'firstname' => 'Aisha', 'lastname' => 'Patel', 'email' => 'aisha.patel@example.com'],
        ['username' => 'carlos.rodriguez', 'firstname' => 'Carlos', 'lastname' => 'Rodriguez', 'email' => 'carlos.rodriguez@example.com'],
        ['username' => 'emily.thompson', 'firstname' => 'Emily', 'lastname' => 'Thompson', 'email' => 'emily.thompson@example.com'],
        ['username' => 'muhammad.ali', 'firstname' => 'Muhammad', 'lastname' => 'Ali', 'email' => 'muhammad.ali@example.com'],
        ['username' => 'sophie.martin', 'firstname' => 'Sophie', 'lastname' => 'Martin', 'email' => 'sophie.martin@example.com'],
        ['username' => 'raj.kumar', 'firstname' => 'Raj', 'lastname' => 'Kumar', 'email' => 'raj.kumar@example.com'],
        ['username' => 'olivia.brown', 'firstname' => 'Olivia', 'lastname' => 'Brown', 'email' => 'olivia.brown@example.com'],
        ['username' => 'hassan.ahmed', 'firstname' => 'Hassan', 'lastname' => 'Ahmed', 'email' => 'hassan.ahmed@example.com'],
        ['username' => 'lucia.gonzalez', 'firstname' => 'Lucia', 'lastname' => 'Gonzalez', 'email' => 'lucia.gonzalez@example.com'],
        ['username' => 'kevin.lee', 'firstname' => 'Kevin', 'lastname' => 'Lee', 'email' => 'kevin.lee@example.com']
    ];
    
    $student_ids = [];
    foreach ($students as $student_data) {
        $existing_user = $DB->get_record('user', ['username' => $student_data['username']]);
        if ($existing_user) {
            $student_ids[] = $existing_user->id;
            echo "✅ Using existing student: {$student_data['firstname']} {$student_data['lastname']}<br>";
        } else {
            $user_data = array_merge($student_data, [
                'password' => hash_internal_user_password('password123'),
                'mnethostid' => 1,
                'confirmed' => 1,
                'timecreated' => time(),
                'timemodified' => time()
            ]);
            $userid = $DB->insert_record('user', $user_data);
            $student_ids[] = $userid;
            echo "✅ Created student: {$student_data['firstname']} {$student_data['lastname']} (ID: $userid)<br>";
        }
    }
    
    // Create grade categories and items with diverse performance profiles
    $grade_categories = [
        'Linear Algebra Quiz' => [95, 78, 88, 45, 92, 67, 82, 58, 91, 72, 85, 76],
        'Calculus Assignment' => [89, 82, 91, 52, 88, 71, 79, 63, 94, 68, 87, 73], 
        'Probability Theory Test' => [92, 75, 85, 28, 90, 64, 77, 55, 89, 70, 83, 69],
        'Statistics Project' => [88, 80, 87, 38, 91, 69, 81, 60, 92, 74, 86, 78],
        'Regression Analysis Lab' => [94, 77, 89, 31, 93, 66, 84, 57, 95, 71, 88, 75],
        'Data Visualization Assignment' => [96, 83, 90, 48, 89, 73, 86, 62, 93, 76, 91, 80]
    ];
    
    foreach ($grade_categories as $item_name => $scores) {
        // Create grade item
        $grade_item_data = [
            'courseid' => $courseid,
            'categoryid' => null,
            'itemname' => $item_name,
            'itemtype' => 'manual',
            'itemmodule' => null,
            'iteminstance' => null,
            'itemnumber' => null,
            'iteminfo' => null,
            'idnumber' => null,
            'calculation' => null,
            'gradetype' => 1, // GRADE_TYPE_VALUE
            'grademax' => 100.00000,
            'grademin' => 0.00000,
            'scaleid' => null,
            'outcomeid' => null,
            'gradepass' => 0.00000,
            'multfactor' => 1.00000,
            'plusfactor' => 0.00000,
            'aggregationcoef' => 0.00000,
            'aggregationcoef2' => 0.00000,
            'sortorder' => 1,
            'display' => 0,
            'decimals' => null,
            'hidden' => 0,
            'locked' => 0,
            'locktime' => 0,
            'needsupdate' => 0,
            'weightoverride' => 0,
            'timecreated' => time(),
            'timemodified' => time()
        ];
        
        $existing_item = $DB->get_record('grade_items', [
            'courseid' => $courseid,
            'itemname' => $item_name,
            'itemtype' => 'manual'
        ]);
        
        if ($existing_item) {
            $itemid = $existing_item->id;
            echo "✅ Using existing grade item: $item_name<br>";
        } else {
            $itemid = $DB->insert_record('grade_items', $grade_item_data);
            echo "✅ Created grade item: $item_name (ID: $itemid)<br>";
        }
        
        // Add grades for each student
        foreach ($student_ids as $index => $student_id) {
            $grade_data = [
                'itemid' => $itemid,
                'userid' => $student_id,
                'rawgrade' => $scores[$index],
                'rawgrademax' => 100.00000,
                'rawgrademin' => 0.00000,
                'rawscaleid' => null,
                'usermodified' => $USER->id,
                'finalgrade' => $scores[$index],
                'hidden' => 0,
                'locked' => 0,
                'locktime' => 0,
                'exported' => 0,
                'overridden' => 0,
                'excluded' => 0,
                'feedback' => null,
                'feedbackformat' => 0,
                'information' => null,
                'informationformat' => 0,
                'timecreated' => time(),
                'timemodified' => time()
            ];
            
            $existing_grade = $DB->get_record('grade_grades', [
                'itemid' => $itemid,
                'userid' => $student_id
            ]);
            
            if (!$existing_grade) {
                $DB->insert_record('grade_grades', $grade_data);
            }
        }
    }
    
    echo "<hr>";
    echo "<h3>🎉 Sample Data Setup Complete!</h3>";
    echo "<p><strong>Course ID:</strong> $courseid</p>";
    echo "<p><strong>Student IDs:</strong> " . implode(', ', $student_ids) . "</p>";
    
    echo "<h3>🧪 Test Your Plugin with Real Data:</h3>";
    echo "<p>Use these parameters in your LearnPath Navigator:</p>";
    echo "<ul>";
    foreach ($student_ids as $index => $student_id) {
        $student_name = $students[$index]['firstname'] . ' ' . $students[$index]['lastname'];
        echo "<li><strong>$student_name:</strong> User ID = $student_id, Course ID = $courseid</li>";
    }
    echo "</ul>";
    
    echo "<p><a href='/moodle/local/learnpath/' class='btn btn-primary'>🚀 Test LearnPath Navigator</a></p>";
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Error: " . $e->getMessage() . "</p>";
}

echo "<style>.btn { background: #007cba; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; }</style>";
?>
