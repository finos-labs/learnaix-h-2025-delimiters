<?php
/**
 * Simple Manual Setup - Guaranteed to Work
 */

require_once(__DIR__ . '/../../config.php');
require_login();

global $DB, $USER;

echo "<h1>🔧 Simple Manual Setup (Guaranteed to Work)</h1>";

if (!is_siteadmin()) {
    echo "<p style='color: red;'>❌ Admin access required.</p>";
    exit;
}

try {
    echo "<h2>Step 1: Create Simple Course</h2>";
    
    // Create or get simple course
    $course_data = [
        'fullname' => 'AI Fundamentals Demo',
        'shortname' => 'AIDEMO',
        'category' => 1,
        'summary' => 'Demo course for LearnPath Navigator testing',
        'summaryformat' => FORMAT_HTML,
        'format' => 'topics',
        'numsections' => 3,
        'startdate' => time(),
        'visible' => 1,
        'timecreated' => time(),
        'timemodified' => time()
    ];
    
    $existing_course = $DB->get_record('course', ['shortname' => 'AIDEMO']);
    if ($existing_course) {
        $courseid = $existing_course->id;
        echo "✅ Using existing course: AIDEMO (ID: $courseid)<br>";
    } else {
        $courseid = $DB->insert_record('course', $course_data);
        echo "✅ Created course: AIDEMO (ID: $courseid)<br>";
    }

    echo "<h2>Step 2: Create Simple Users</h2>";
    
    $simple_users = [
        ['username' => 'alice', 'firstname' => 'Alice', 'lastname' => 'Smith', 'email' => 'alice@demo.com'],
        ['username' => 'bob', 'firstname' => 'Bob', 'lastname' => 'Jones', 'email' => 'bob@demo.com'],
        ['username' => 'carol', 'firstname' => 'Carol', 'lastname' => 'Brown', 'email' => 'carol@demo.com']
    ];
    
    $user_ids = [];
    foreach ($simple_users as $user_data) {
        $existing_user = $DB->get_record('user', ['username' => $user_data['username']]);
        
        if (!$existing_user) {
            $full_user_data = array_merge($user_data, [
                'password' => hash_internal_user_password('demo123'),
                'mnethostid' => 1,
                'confirmed' => 1,
                'timecreated' => time(),
                'timemodified' => time()
            ]);
            $userid = $DB->insert_record('user', $full_user_data);
            echo "✅ Created user: {$user_data['firstname']} (ID: $userid)<br>";
        } else {
            $userid = $existing_user->id;
            echo "ℹ️ User exists: {$user_data['firstname']} (ID: $userid)<br>";
        }
        $user_ids[] = $userid;
    }

    echo "<h2>Step 3: Enroll Users</h2>";
    
    // Get student role
    $student_role = $DB->get_record('role', ['shortname' => 'student']);
    if (!$student_role) {
        throw new Exception('Student role not found');
    }
    
    $context = context_course::instance($courseid);
    foreach ($user_ids as $userid) {
        $existing_enrollment = $DB->get_record('role_assignments', [
            'roleid' => $student_role->id,
            'contextid' => $context->id,
            'userid' => $userid
        ]);
        
        if (!$existing_enrollment) {
            role_assign($student_role->id, $userid, $context->id);
            $user = $DB->get_record('user', ['id' => $userid]);
            echo "✅ Enrolled: {$user->firstname} {$user->lastname}<br>";
        }
    }

    echo "<h2>Step 4: Create Grade Items</h2>";
    
    $grade_items = [
        ['name' => 'AI Quiz 1', 'scores' => [85, 72, 90]],
        ['name' => 'ML Assignment', 'scores' => [78, 68, 85]],
        ['name' => 'Final Project', 'scores' => [92, 75, 88]]
    ];
    
    foreach ($grade_items as $item_data) {
        $existing_item = $DB->get_record('grade_items', [
            'courseid' => $courseid,
            'itemname' => $item_data['name'],
            'itemtype' => 'manual'
        ]);
        
        if (!$existing_item) {
            $grade_item = [
                'courseid' => $courseid,
                'categoryid' => null,
                'itemname' => $item_data['name'],
                'itemtype' => 'manual',
                'itemmodule' => null,
                'iteminstance' => null,
                'itemnumber' => null,
                'iteminfo' => null,
                'idnumber' => null,
                'calculation' => null,
                'gradetype' => 1,
                'grademax' => 100.00000,
                'grademin' => 0.00000,
                'scaleid' => null,
                'outcomeid' => null,
                'gradepass' => 60.00000,
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
            
            $itemid = $DB->insert_record('grade_items', $grade_item);
            echo "✅ Created grade item: {$item_data['name']} (ID: $itemid)<br>";
        } else {
            $itemid = $existing_item->id;
            echo "ℹ️ Grade item exists: {$item_data['name']}<br>";
        }
        
        // Add grades
        foreach ($user_ids as $index => $userid) {
            if (isset($item_data['scores'][$index])) {
                $existing_grade = $DB->get_record('grade_grades', [
                    'itemid' => $itemid,
                    'userid' => $userid
                ]);
                
                if (!$existing_grade) {
                    $grade = [
                        'itemid' => $itemid,
                        'userid' => $userid,
                        'rawgrade' => $item_data['scores'][$index],
                        'rawgrademax' => 100.00000,
                        'rawgrademin' => 0.00000,
                        'rawscaleid' => null,
                        'usermodified' => $USER->id,
                        'finalgrade' => $item_data['scores'][$index],
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
                    
                    $DB->insert_record('grade_grades', $grade);
                }
            }
        }
    }

    echo "<hr>";
    echo "<h2>🎉 Simple Setup Complete!</h2>";
    echo "<div style='background: #d4edda; padding: 20px; border-radius: 10px;'>";
    echo "<h3>✅ Created:</h3>";
    echo "<ul>";
    echo "<li><strong>Course:</strong> AIDEMO (ID: $courseid)</li>";
    echo "<li><strong>Students:</strong> Alice, Bob, Carol</li>";
    echo "<li><strong>Grade Items:</strong> 3 items with sample grades</li>";
    echo "<li><strong>Total Grades:</strong> 9 grade records</li>";
    echo "</ul>";
    echo "</div>";

    echo "<h3>🧪 Test Data for LearnPath Navigator:</h3>";
    echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
    echo "<tr><th>Student</th><th>User ID</th><th>Course ID</th><th>AI Quiz 1</th><th>ML Assignment</th><th>Final Project</th></tr>";
    
    $test_data = [
        ['Alice Smith', $user_ids[0], $courseid, 85, 78, 92],
        ['Bob Jones', $user_ids[1], $courseid, 72, 68, 75],
        ['Carol Brown', $user_ids[2], $courseid, 90, 85, 88]
    ];
    
    foreach ($test_data as $row) {
        echo "<tr>";
        foreach ($row as $cell) {
            echo "<td>$cell</td>";
        }
        echo "</tr>";
    }
    echo "</table>";

    echo "<h3>🚀 Next Steps:</h3>";
    echo "<div style='display: flex; gap: 10px; margin: 20px 0;'>";
    echo "<a href='/moodle/course/view.php?id=$courseid' style='background: #007cba; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>📚 View Course</a>";
    echo "<a href='/moodle/local/learnpath/' style='background: #28a745; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>🧪 Test Plugin</a>";
    echo "</div>";

    echo "<h3>🎯 Use Real Data in Plugin:</h3>";
    echo "<p>Update your LearnPath Navigator to use:</p>";
    echo "<ul>";
    echo "<li><strong>Course ID:</strong> $courseid</li>";
    echo "<li><strong>User IDs:</strong> " . implode(', ', $user_ids) . "</li>";
    echo "</ul>";

} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Error: " . $e->getMessage() . "</p>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
}
?>
