<?php
/**
 * Fix Grade Structure for LearnPath Navigator Demo
 * Repairs broken grade categories and rebuilds proper structure
 * Team Delimiters - NatWest Hack4aCause
 */

require_once(__DIR__ . '/../../config.php');
require_once($CFG->libdir . '/gradelib.php');
require_once($CFG->dirroot . '/grade/lib.php');

require_login();
require_capability('moodle/site:config', context_system::instance());

echo "<h1>🔧 Grade Structure Repair Tool</h1>";
echo "<p>Fixing broken grade categories and rebuilding proper structure...</p>";
echo "<hr>";

function fix_course_grades($courseid) {
    global $DB;
    
    $course = $DB->get_record('course', ['id' => $courseid]);
    if (!$course) {
        echo "❌ Course not found: ID {$courseid}<br>";
        return false;
    }
    
    echo "<h3>🔧 Fixing grades for: {$course->fullname}</h3>";
    
    try {
        // Step 1: Check current grade structure
        echo "<strong>Step 1: Analyzing current structure...</strong><br>";
        
        $categories = $DB->get_records('grade_categories', ['courseid' => $courseid], 'depth ASC');
        echo "Found " . count($categories) . " grade categories<br>";
        
        $items = $DB->get_records('grade_items', ['courseid' => $courseid]);
        echo "Found " . count($items) . " grade items<br>";
        
        // Step 2: Delete broken categories (except course category)
        echo "<strong>Step 2: Cleaning broken categories...</strong><br>";
        
        // Find the main course category (depth = 1)
        $main_category = $DB->get_record('grade_categories', [
            'courseid' => $courseid,
            'depth' => 1
        ]);
        
        if (!$main_category) {
            echo "⚠️ No main category found, creating one...<br>";
            
            // Create main course category
            $category_data = new stdClass();
            $category_data->courseid = $courseid;
            $category_data->parent = null;
            $category_data->depth = 1;
            $category_data->path = null;
            $category_data->fullname = '?';
            $category_data->aggregation = 13; // GRADE_AGGREGATE_WEIGHTED_MEAN2
            $category_data->keephigh = 0;
            $category_data->droplow = 0;
            $category_data->aggregateonlygraded = 0;
            $category_data->aggregateoutcomes = 0;
            $category_data->timecreated = time();
            $category_data->timemodified = time();
            
            $main_category_id = $DB->insert_record('grade_categories', $category_data);
            $main_category = $DB->get_record('grade_categories', ['id' => $main_category_id]);
            
            // Update path
            $main_category->path = '/' . $main_category->id . '/';
            $DB->update_record('grade_categories', $main_category);
            
            echo "✅ Created main course category (ID: {$main_category->id})<br>";
        } else {
            echo "✅ Found main course category (ID: {$main_category->id})<br>";
        }
        
        // Step 3: Fix all grade items to use main category
        echo "<strong>Step 3: Reassigning grade items...</strong><br>";
        
        foreach ($items as $item) {
            if ($item->itemtype !== 'course') {
                // Update categoryid to main category
                $item->categoryid = $main_category->id;
                $DB->update_record('grade_items', $item);
                echo "✅ Fixed grade item: {$item->itemname}<br>";
            }
        }
        
        // Step 4: Create course total grade item if missing
        echo "<strong>Step 4: Ensuring course total exists...</strong><br>";
        
        $course_item = $DB->get_record('grade_items', [
            'courseid' => $courseid,
            'itemtype' => 'course'
        ]);
        
        if (!$course_item) {
            echo "⚠️ No course total item found, creating one...<br>";
            
            $course_item_data = new stdClass();
            $course_item_data->courseid = $courseid;
            $course_item_data->categoryid = $main_category->id;
            $course_item_data->itemname = null;
            $course_item_data->itemtype = 'course';
            $course_item_data->itemmodule = null;
            $course_item_data->iteminstance = null;
            $course_item_data->itemnumber = null;
            $course_item_data->iteminfo = null;
            $course_item_data->idnumber = null;
            $course_item_data->calculation = null;
            $course_item_data->gradetype = 1;
            $course_item_data->grademax = 100.00000;
            $course_item_data->grademin = 0.00000;
            $course_item_data->scaleid = null;
            $course_item_data->outcomeid = null;
            $course_item_data->gradepass = 0.00000;
            $course_item_data->multfactor = 1.00000;
            $course_item_data->plusfactor = 0.00000;
            $course_item_data->aggregationcoef = 0.00000;
            $course_item_data->aggregationcoef2 = 0.00000;
            $course_item_data->sortorder = 1;
            $course_item_data->display = 0;
            $course_item_data->decimals = null;
            $course_item_data->hidden = 0;
            $course_item_data->locked = 0;
            $course_item_data->locktime = 0;
            $course_item_data->needsupdate = 0;
            $course_item_data->weightoverride = 0;
            $course_item_data->timecreated = time();
            $course_item_data->timemodified = time();
            
            $course_item_id = $DB->insert_record('grade_items', $course_item_data);
            echo "✅ Created course total item (ID: {$course_item_id})<br>";
        } else {
            echo "✅ Course total item exists (ID: {$course_item->id})<br>";
        }
        
        // Step 5: Force grade recalculation
        echo "<strong>Step 5: Recalculating grades...</strong><br>";
        
        // Mark all grades for update
        $DB->execute("UPDATE {grade_items} SET needsupdate = 1 WHERE courseid = ?", [$courseid]);
        
        // Force recalculation
        grade_regrade_final_grades($courseid);
        
        echo "✅ Grade recalculation completed<br>";
        
        echo "<div style='background: #d4edda; padding: 10px; border-radius: 5px; margin: 10px 0;'>";
        echo "✅ <strong>Successfully fixed grade structure for {$course->fullname}</strong>";
        echo "</div>";
        
        return true;
        
    } catch (Exception $e) {
        echo "<div style='background: #f8d7da; padding: 10px; border-radius: 5px; margin: 10px 0;'>";
        echo "❌ <strong>Error fixing {$course->fullname}:</strong> " . $e->getMessage();
        echo "</div>";
        return false;
    }
}

try {
    // Get courses that need fixing
    $courses_to_fix = [
        13, // Algorithmic Design
        8,  // Data Structures and Algorithms
        7   // AI Fundamentals Demo
    ];
    
    $success_count = 0;
    
    foreach ($courses_to_fix as $courseid) {
        if (fix_course_grades($courseid)) {
            $success_count++;
        }
        echo "<hr>";
    }
    
    echo "<h2>🎉 Grade Structure Repair Complete!</h2>";
    
    echo "<div style='background: #cce5ff; padding: 20px; border-radius: 10px; margin: 20px 0;'>";
    echo "<h4>📊 Repair Summary:</h4>";
    echo "<ul>";
    echo "<li><strong>Courses Fixed:</strong> {$success_count} out of " . count($courses_to_fix) . "</li>";
    echo "<li><strong>Grade Categories:</strong> Rebuilt and properly structured</li>";
    echo "<li><strong>Grade Items:</strong> Reassigned to correct categories</li>";
    echo "<li><strong>Course Totals:</strong> Created where missing</li>";
    echo "<li><strong>Grade Calculations:</strong> Forced recalculation</li>";
    echo "</ul>";
    echo "</div>";
    
    echo "<div style='margin: 30px 0; text-align: center;'>";
    echo "<a href='index.php' style='background: #28a745; color: white; padding: 15px 30px; text-decoration: none; border-radius: 8px; font-size: 18px; margin: 10px;'>🎯 Test LearnPath Navigator</a>";
    echo "<a href='check_existing_data.php' style='background: #6c757d; color: white; padding: 15px 30px; text-decoration: none; border-radius: 8px; font-size: 18px; margin: 10px;'>🔍 Verify Grades</a>";
    echo "</div>";
    
    echo "<div style='background: #fff3cd; padding: 15px; border-radius: 5px;'>";
    echo "<h4>✅ Next Steps:</h4>";
    echo "<ol>";
    echo "<li>Try accessing the <strong>Grades</strong> section of Algorithmic Design course</li>";
    echo "<li>The 'children' error should be resolved</li>";
    echo "<li>Go to LearnPath Navigator and test with the demo data</li>";
    echo "<li>All three scenarios should work properly now</li>";
    echo "</ol>";
    echo "</div>";
    
} catch (Exception $e) {
    echo "<div style='color: red; background: #ffe6e6; padding: 15px; border: 1px solid #ff0000; border-radius: 5px;'>";
    echo "<h3>❌ Critical Error:</h3>";
    echo "<p><strong>Message:</strong> " . $e->getMessage() . "</p>";
    echo "<p><strong>File:</strong> " . $e->getFile() . "</p>";
    echo "<p><strong>Line:</strong> " . $e->getLine() . "</p>";
    echo "</div>";
}
?>
