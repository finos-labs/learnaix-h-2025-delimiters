<?php
/**
 * Check Existing Data in Moodle
 * Analyzes current courses, users, and grades to understand the database structure
 */

require_once(__DIR__ . '/../../config.php');
require_login();

echo "<h1>🔍 Existing Moodle Data Analysis</h1>";

try {
    // Check existing courses
    echo "<h2>📚 Existing Courses</h2>";
    $courses = $DB->get_records_sql("
        SELECT id, fullname, shortname, category, visible 
        FROM {course} 
        WHERE id > 1 
        ORDER BY id DESC 
        LIMIT 10
    ");
    
    if ($courses) {
        echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
        echo "<tr><th>ID</th><th>Full Name</th><th>Short Name</th><th>Category</th><th>Visible</th></tr>";
        foreach ($courses as $course) {
            echo "<tr>";
            echo "<td>{$course->id}</td>";
            echo "<td>{$course->fullname}</td>";
            echo "<td>{$course->shortname}</td>";
            echo "<td>{$course->category}</td>";
            echo "<td>" . ($course->visible ? 'Yes' : 'No') . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<p>❌ No courses found (except site course)</p>";
    }
    
    // Check existing users
    echo "<h2>👥 Existing Users (Non-Admin)</h2>";
    $users = $DB->get_records_sql("
        SELECT id, username, firstname, lastname, email, confirmed 
        FROM {user} 
        WHERE id > 2 AND deleted = 0
        ORDER BY id DESC 
        LIMIT 10
    ");
    
    if ($users) {
        echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
        echo "<tr><th>ID</th><th>Username</th><th>First Name</th><th>Last Name</th><th>Email</th><th>Confirmed</th></tr>";
        foreach ($users as $user) {
            echo "<tr>";
            echo "<td>{$user->id}</td>";
            echo "<td>{$user->username}</td>";
            echo "<td>{$user->firstname}</td>";
            echo "<td>{$user->lastname}</td>";
            echo "<td>{$user->email}</td>";
            echo "<td>" . ($user->confirmed ? 'Yes' : 'No') . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<p>❌ No additional users found</p>";
    }
    
    // Check enrollments
    echo "<h2>🎓 Existing Enrollments</h2>";
    $enrollments = $DB->get_records_sql("
        SELECT ue.id, u.username, u.firstname, u.lastname, c.fullname as coursename, r.shortname as role
        FROM {user_enrolments} ue
        JOIN {enrol} e ON e.id = ue.enrolid
        JOIN {user} u ON u.id = ue.userid
        JOIN {course} c ON c.id = e.courseid
        JOIN {role_assignments} ra ON ra.userid = u.id
        JOIN {role} r ON r.id = ra.roleid
        JOIN {context} ctx ON ctx.id = ra.contextid AND ctx.contextlevel = 50 AND ctx.instanceid = c.id
        WHERE c.id > 1
        ORDER BY ue.id DESC
        LIMIT 10
    ");
    
    if ($enrollments) {
        echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
        echo "<tr><th>ID</th><th>Student</th><th>Course</th><th>Role</th></tr>";
        foreach ($enrollments as $enrollment) {
            echo "<tr>";
            echo "<td>{$enrollment->id}</td>";
            echo "<td>{$enrollment->firstname} {$enrollment->lastname} ({$enrollment->username})</td>";
            echo "<td>{$enrollment->coursename}</td>";
            echo "<td>{$enrollment->role}</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<p>❌ No enrollments found</p>";
    }
    
    // Check grade items
    echo "<h2>📊 Existing Grade Items</h2>";
    $grade_items = $DB->get_records_sql("
        SELECT gi.id, gi.itemname, c.fullname as coursename, gi.itemtype, gi.grademax
        FROM {grade_items} gi
        JOIN {course} c ON c.id = gi.courseid
        WHERE gi.courseid > 1 AND gi.itemtype != 'course'
        ORDER BY gi.id DESC
        LIMIT 15
    ");
    
    if ($grade_items) {
        echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
        echo "<tr><th>ID</th><th>Item Name</th><th>Course</th><th>Type</th><th>Max Grade</th></tr>";
        foreach ($grade_items as $item) {
            echo "<tr>";
            echo "<td>{$item->id}</td>";
            echo "<td>{$item->itemname}</td>";
            echo "<td>{$item->coursename}</td>";
            echo "<td>{$item->itemtype}</td>";
            echo "<td>{$item->grademax}</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<p>❌ No grade items found</p>";
    }
    
    // Check actual grades
    echo "<h2>📈 Existing Grades</h2>";
    $grades = $DB->get_records_sql("
        SELECT gg.id, u.firstname, u.lastname, gi.itemname, c.fullname as coursename, gg.finalgrade, gg.rawgrade
        FROM {grade_grades} gg
        JOIN {grade_items} gi ON gi.id = gg.itemid
        JOIN {user} u ON u.id = gg.userid
        JOIN {course} c ON c.id = gi.courseid
        WHERE gi.courseid > 1 AND gg.finalgrade IS NOT NULL
        ORDER BY gg.id DESC
        LIMIT 15
    ");
    
    if ($grades) {
        echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
        echo "<tr><th>ID</th><th>Student</th><th>Course</th><th>Item</th><th>Grade</th></tr>";
        foreach ($grades as $grade) {
            echo "<tr>";
            echo "<td>{$grade->id}</td>";
            echo "<td>{$grade->firstname} {$grade->lastname}</td>";
            echo "<td>{$grade->coursename}</td>";
            echo "<td>{$grade->itemname}</td>";
            echo "<td>{$grade->finalgrade}%</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<p>❌ No grades found</p>";
    }
    
    // Check our demo data specifically
    echo "<h2>🎯 Demo Data Status</h2>";
    
    $demo_courses = ['DEMO_BIO101', 'DEMO_AI101', 'DEMO_MATH301'];
    $demo_users = ['sarah.martinez', 'alex.johnson', 'david.chen'];
    
    echo "<h3>Demo Courses:</h3>";
    foreach ($demo_courses as $shortname) {
        $course = $DB->get_record('course', ['shortname' => $shortname]);
        if ($course) {
            echo "✅ {$shortname}: {$course->fullname} (ID: {$course->id})<br>";
        } else {
            echo "❌ {$shortname}: Not found<br>";
        }
    }
    
    echo "<h3>Demo Users:</h3>";
    foreach ($demo_users as $username) {
        $user = $DB->get_record('user', ['username' => $username]);
        if ($user) {
            echo "✅ {$username}: {$user->firstname} {$user->lastname} (ID: {$user->id})<br>";
        } else {
            echo "❌ {$username}: Not found<br>";
        }
    }
    
    echo "<hr>";
    echo "<h2>📋 Summary</h2>";
    echo "<ul>";
    echo "<li><strong>Total Courses:</strong> " . $DB->count_records_select('course', 'id > 1') . "</li>";
    echo "<li><strong>Total Users:</strong> " . $DB->count_records_select('user', 'id > 2 AND deleted = 0') . "</li>";
    echo "<li><strong>Total Enrollments:</strong> " . $DB->count_records('user_enrolments') . "</li>";
    echo "<li><strong>Total Grade Items:</strong> " . $DB->count_records_select('grade_items', 'courseid > 1 AND itemtype != \'course\'') . "</li>";
    echo "<li><strong>Total Grades:</strong> " . $DB->count_records_select('grade_grades', 'finalgrade IS NOT NULL') . "</li>";
    echo "</ul>";
    
} catch (Exception $e) {
    echo "<div style='color: red;'>❌ Error: " . $e->getMessage() . "</div>";
}
?>
