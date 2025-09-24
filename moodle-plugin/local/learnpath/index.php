<?php
/**
 * LearnPath Navigator - Main Interface
 * Team Delimiters - NatWest Hack4aCause
 *
 * @package    local_learnpath
 * @copyright  2025 Team Delimiters
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../config.php');
require_once($CFG->libdir . '/adminlib.php');

require_login();

$context = context_system::instance();
require_capability('local/learnpath:view', $context);

$PAGE->set_context($context);
$PAGE->set_url('/local/learnpath/index.php');
$PAGE->set_title(get_string('pluginname', 'local_learnpath'));
$PAGE->set_heading(get_string('learnpath_title', 'local_learnpath'));
$PAGE->set_pagelayout('standard');

echo $OUTPUT->header();

?>

<style>
.learnpath-container {
    max-width: 100%;
    margin: 0;
    padding: 0;
}

/* Khan Academy Layout */
.khan-layout {
    display: flex;
    gap: 0;
    background: #f7f8fa;
    min-height: auto;
    height: auto;
}

/* Left Sidebar */
.left-sidebar {
    width: 380px;
    background: white;
    border-right: 1px solid #e7e8ea;
    padding: 25px;
    overflow-y: auto;
    height: auto;
    max-height: calc(100vh - 100px);
}

.sidebar-header {
    margin-bottom: 25px;
}

.sidebar-header h3 {
    margin: 0 0 10px 0;
    font-size: 1.1em;
    font-weight: 600;
    color: #21242c;
}

.sidebar-dropdown {
    width: 100%;
    padding: 12px 15px;
    border: 1px solid #e7e8ea;
    border-radius: 8px;
    font-size: 1em;
    background: white;
    cursor: pointer;
    margin-bottom: 10px;
}

.course-input, .student-input {
    width: 100%;
    padding: 12px 15px;
    border: 1px solid #e7e8ea;
    border-radius: 8px;
    font-size: 1em;
    margin-bottom: 15px;
    background: white;
}

.sidebar-section h4 {
    margin: 0 0 15px 0;
    font-size: 1em;
    font-weight: 600;
    color: #21242c;
}

/* Student List */
.student-list {
    display: flex;
    flex-direction: column;
    gap: 8px;
}

.student-item {
    display: flex;
    align-items: center;
    padding: 16px;
    border-radius: 10px;
    cursor: pointer;
    transition: all 0.2s ease;
    border: 1px solid transparent;
    margin-bottom: 10px;
}

.student-item:hover {
    background: #f7f8fa;
    border-color: #e7e8ea;
}

.student-item.selected {
    background: #f0f4ff;
    border-color: #1865f2;
}

.student-avatar {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
    color: white;
    margin-right: 12px;
    font-size: 1.1em;
}

.student-avatar.orange { background: #ff9500; }
.student-avatar.blue { background: #1865f2; }
.student-avatar.green { background: #00af54; }

.student-details {
    flex: 1;
}

.student-name {
    font-weight: 600;
    color: #21242c;
    font-size: 0.95em;
    margin-bottom: 2px;
}

.student-grade {
    font-size: 0.8em;
    color: #626569;
}

.student-score {
    font-weight: 600;
    font-size: 0.9em;
}

.student-score.orange { color: #ff9500; }
.student-score.blue { color: #1865f2; }
.student-score.green { color: #00af54; }

/* Main Area */
.main-area {
    flex: 1;
    background: #f7f8fa;
    overflow: visible;
    height: auto;
    min-height: auto;
    width: 100%;
}

/* Top Navigation Cards */
.top-nav-cards {
    display: flex;
    gap: 25px;
    padding: 30px;
    background: #f7f8fa;
}

.nav-card {
    flex: 1;
    background: white;
    border-radius: 12px;
    padding: 25px;
    cursor: pointer;
    transition: all 0.3s ease;
    border: 2px solid transparent;
    display: flex;
    align-items: center;
    gap: 20px;
    min-height: 100px;
}

.nav-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}

.nav-card.active {
    border-color: currentColor;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
}

.nav-card.blue { color: #1865f2; }
.nav-card.purple { color: #9c27b0; }
.nav-card.green { color: #00af54; }

.nav-card.blue.active { background: #f0f4ff; }
.nav-card.purple.active { background: #f3e5f5; }
.nav-card.green.active { background: #e8f5e8; }

.nav-icon {
    font-size: 2em;
    flex-shrink: 0;
}

.nav-text h3 {
    margin: 0 0 5px 0;
    font-size: 1.2em;
    font-weight: 600;
    color: #21242c;
}

.nav-text p {
    margin: 0;
    font-size: 0.95em;
    color: #626569;
}

/* Content Area */
.content-area {
    padding: 0 30px 30px 30px;
    background: #f7f8fa;
}

/* Welcome Section */
.welcome-section {
    display: flex;
    align-items: center;
    gap: 20px;
    margin-bottom: 30px;
    padding: 30px;
    background: white;
    border-radius: 12px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
}

.welcome-avatar .avatar-circle {
    width: 60px;
    height: 60px;
    background: linear-gradient(135deg, #1865f2 0%, #0d47a1 100%);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 1.4em;
    font-weight: bold;
}

.welcome-text h1 {
    margin: 0 0 5px 0;
    font-size: 1.8em;
    color: #21242c;
    font-weight: 600;
}

.welcome-text p {
    margin: 0;
    color: #626569;
    font-size: 0.9em;
}

/* Dashboard Content */
.dashboard-content {
    display: grid;
    grid-template-columns: 1fr 1fr 1fr;
    gap: 30px;
    width: 100%;
}

.performance-section, .streak-section, .achievements-section {
    background: white;
    border-radius: 12px;
    padding: 35px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
    min-height: 400px;
}

.section-title {
    font-size: 1.2em;
    font-weight: 600;
    color: #21242c;
    margin-bottom: 20px;
}

/* Performance Section */
.overall-performance {
    text-align: center;
    margin-bottom: 25px;
}

.score-display {
    margin-bottom: 15px;
}

.score-number {
    font-size: 4em;
    font-weight: bold;
    color: #1865f2;
    margin-bottom: 10px;
}

.score-label {
    color: #626569;
    font-size: 1em;
    font-weight: 500;
}

.score-bar {
    height: 8px;
    background: #f0f1f3;
    border-radius: 4px;
    overflow: hidden;
    margin: 0 auto;
    max-width: 200px;
}

.score-fill {
    height: 100%;
    background: #1865f2;
    border-radius: 4px;
    transition: width 0.3s ease;
}

.subjects-title {
    font-weight: 600;
    margin-bottom: 15px;
    color: #21242c;
}

.subjects-list {
    display: flex;
    flex-direction: column;
    gap: 18px;
}

.subject-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.subject-name {
    font-weight: 500;
    color: #21242c;
    min-width: 60px;
}

.subject-bar {
    flex: 1;
    height: 6px;
    background: #f0f1f3;
    border-radius: 3px;
    margin: 0 15px;
    overflow: hidden;
}

.subject-fill {
    height: 100%;
    border-radius: 3px;
    transition: width 0.3s ease;
}

.subject-fill.needs-work { background: #ff6b6b; }
.subject-fill.fair { background: #ffa726; }
.subject-fill.good { background: #66bb6a; }

.subject-score {
    font-weight: 600;
    min-width: 40px;
    text-align: right;
    font-size: 0.9em;
}

.subject-score.needs-work { color: #ff6b6b; }
.subject-score.fair { color: #ffa726; }
.subject-score.good { color: #66bb6a; }

/* Streak Section */
.streak-display {
    text-align: center;
    margin-bottom: 20px;
}

.streak-number {
    font-size: 4em;
    font-weight: bold;
    color: #ff6b35;
    margin-bottom: 10px;
}

.streak-text {
    color: #626569;
    font-size: 0.9em;
}

.streak-stats {
    display: flex;
    justify-content: space-between;
}

.streak-stat {
    text-align: center;
}

.stat-number {
    font-size: 1.2em;
    font-weight: 600;
    color: #21242c;
    margin-bottom: 2px;
}

.stat-label {
    font-size: 0.8em;
    color: #626569;
}

/* Achievements Section */
.achievements-section {
    /* Removed grid-column span to fit in single column */
}

/* Khan Academy Roadmap Styles */
.khan-roadmap-header {
    text-align: center;
    padding: 40px 20px;
    background: white;
    border-radius: 12px;
    margin-bottom: 30px;
}

.khan-roadmap-header h1 {
    font-size: 2.5em;
    color: #21242c;
    margin-bottom: 10px;
    font-weight: 700;
}

.khan-roadmap-header p {
    color: #626569;
    font-size: 1.2em;
    margin-bottom: 20px;
}

.generate-roadmap-btn {
    background: linear-gradient(135deg, #4285f4 0%, #1a73e8 100%);
    color: white;
    border: none;
    padding: 12px 24px;
    border-radius: 8px;
    font-size: 1em;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
}

.generate-roadmap-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(66, 133, 244, 0.3);
}

.khan-roadmap-container {
    background: white;
    border-radius: 12px;
    padding: 20px;
}

.khan-roadmap-modules {
    display: flex;
    flex-direction: column;
    gap: 15px;
    margin-bottom: 30px;
}

.khan-module {
    background: white;
    border: 2px solid #e7e8ea;
    border-radius: 12px;
    padding: 20px;
    transition: all 0.3s ease;
    cursor: pointer;
}

.khan-module:hover {
    border-color: #1865f2;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
}

.khan-module.new {
    border-color: #4285f4;
    background: #f0f7ff;
}

.khan-module.completed {
    border-color: #00af54;
    background: #f0fff4;
}

.module-header {
    display: flex;
    align-items: flex-start;
    gap: 20px;
}

.module-number {
    width: 40px;
    height: 40px;
    background: #e7e8ea;
    color: #626569;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.2em;
    font-weight: bold;
    flex-shrink: 0;
}

.khan-module.new .module-number {
    background: #4285f4;
    color: white;
}

.khan-module.completed .module-number {
    background: #00af54;
    color: white;
}

.module-content {
    flex: 1;
}

.module-week-label {
    font-size: 0.9em;
    font-weight: 500;
    color: #626569;
    margin-bottom: 5px;
    display: flex;
    align-items: center;
    gap: 10px;
}

.module-title {
    font-size: 1.2em;
    font-weight: 700;
    color: #21242c;
    margin-bottom: 8px;
    line-height: 1.3;
}

.module-badge {
    display: inline-block;
    padding: 3px 8px;
    background: #ff6b35;
    color: white;
    border-radius: 4px;
    font-size: 0.7em;
    font-weight: 600;
    text-transform: uppercase;
}

.module-description {
    color: #626569;
    line-height: 1.5;
    margin-bottom: 12px;
}

.module-meta {
    display: flex;
    gap: 20px;
    margin-bottom: 12px;
}

.module-meta-item {
    display: flex;
    align-items: center;
    gap: 5px;
    color: #626569;
    font-size: 0.9em;
}

.module-skills {
    display: flex;
    gap: 8px;
    flex-wrap: wrap;
    margin-bottom: 15px;
}

.skill-tag {
    padding: 4px 10px;
    background: #f0f1f3;
    color: #626569;
    border-radius: 15px;
    font-size: 0.85em;
}

.module-actions {
    display: flex;
    gap: 10px;
    align-items: center;
}

.module-action-btn {
    padding: 8px 16px;
    background: #1865f2;
    color: white;
    border: none;
    border-radius: 6px;
    font-size: 0.9em;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.2s ease;
}

.module-action-btn:hover {
    background: #1557d0;
}

.module-progress {
    flex: 1;
    height: 8px;
    background: #f0f1f3;
    border-radius: 4px;
    overflow: hidden;
}

.module-progress-fill {
    height: 100%;
    background: #00af54;
    border-radius: 4px;
    transition: width 0.3s ease;
}

.roadmap-footer {
    text-align: center;
    padding: 30px;
    background: linear-gradient(135deg, #f0f4ff 0%, #e8f5e8 100%);
    border-radius: 12px;
    margin-top: 30px;
}

.keep-going-message h3 {
    font-size: 1.5em;
    color: #21242c;
    margin-bottom: 10px;
}

.keep-going-message p {
    color: #626569;
    font-size: 1.1em;
}

/* Khan Academy Resources Styles */
.khan-resources-header {
    text-align: center;
    padding: 40px 20px;
    background: white;
    border-radius: 12px;
    margin-bottom: 30px;
}

.khan-resources-header h1 {
    font-size: 2.5em;
    color: #21242c;
    margin-bottom: 10px;
    font-weight: 700;
}

.khan-resources-header p {
    color: #626569;
    font-size: 1.2em;
    margin-bottom: 20px;
}

.generate-resources-btn {
    background: linear-gradient(135deg, #4285f4 0%, #1a73e8 100%);
    color: white;
    border: none;
    padding: 12px 24px;
    border-radius: 8px;
    font-size: 1em;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
}

.generate-resources-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(66, 133, 244, 0.3);
}

.khan-resources-container {
    background: white;
    border-radius: 12px;
    padding: 20px;
}

.khan-resources-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
    gap: 20px;
    margin-bottom: 30px;
}

.khan-resource-card {
    background: white;
    border: 1px solid #e7e8ea;
    border-radius: 8px;
    padding: 20px;
    transition: all 0.3s ease;
    position: relative;
    display: flex;
    flex-direction: column;
    height: 100%;
}

.khan-resource-card:hover {
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
}

.resource-header {
    display: flex;
    align-items: flex-start;
    gap: 12px;
    margin-bottom: 12px;
    position: relative;
}

.resource-icon {
    width: 50px;
    height: 50px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5em;
    flex-shrink: 0;
}

.resource-icon.red { background: #ffe5e5; color: #ff4444; }
.resource-icon.green { background: #e5ffe5; color: #00af54; }
.resource-icon.blue { background: #e5f0ff; color: #1865f2; }
.resource-icon.purple { background: #f0e5ff; color: #9c27b0; }

.resource-info {
    flex: 1;
}

.resource-title {
    font-size: 1.2em;
    font-weight: 600;
    color: #21242c;
    margin-bottom: 5px;
}

.resource-author {
    color: #626569;
    font-size: 0.9em;
    margin-bottom: 10px;
}

.resource-description {
    color: #626569;
    line-height: 1.5;
    margin-bottom: 15px;
    font-size: 0.95em;
}

.resource-meta {
    display: flex;
    gap: 20px;
    margin-bottom: 15px;
}

.resource-rating-badge {
    position: absolute;
    top: 0;
    right: 0;
    background: #fff3cd;
    color: #856404;
    padding: 4px 8px;
    border-radius: 4px;
    font-size: 0.85em;
    font-weight: 600;
}

.resource-tags {
    display: flex;
    gap: 8px;
    flex-wrap: wrap;
    margin-bottom: 15px;
}

.resource-tag {
    padding: 3px 8px;
    background: #f0f1f3;
    color: #626569;
    border-radius: 4px;
    font-size: 0.75em;
}

.resource-footer {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-top: auto;
    padding-top: 15px;
}

.start-learning-btn {
    padding: 8px 16px;
    background: #1865f2;
    color: white;
    border: none;
    border-radius: 6px;
    font-size: 0.9em;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.2s ease;
    white-space: nowrap;
}

.start-learning-btn:hover {
    background: #1557d0;
}

.start-learning-btn-orange {
    padding: 8px 16px;
    background: #ff6b35;
    color: white;
    border: none;
    border-radius: 6px;
    font-size: 0.9em;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.2s ease;
    white-space: nowrap;
}

.start-learning-btn-orange:hover {
    background: #e55a2b;
}

.resource-difficulty {
    display: flex;
    align-items: center;
    gap: 5px;
    color: #626569;
    font-size: 0.9em;
}

.resources-footer {
    margin-top: 40px;
}

.need-help-section {
    text-align: center;
    padding: 40px;
    background: linear-gradient(135deg, #f0f4ff 0%, #fff3e0 100%);
    border-radius: 12px;
}

.need-help-section h3 {
    font-size: 1.8em;
    color: #21242c;
    margin-bottom: 10px;
}

.need-help-section p {
    color: #626569;
    font-size: 1.1em;
    margin-bottom: 20px;
    max-width: 600px;
    margin-left: auto;
    margin-right: auto;
}

.help-links {
    display: flex;
    gap: 15px;
    justify-content: center;
}

.help-link {
    padding: 10px 20px;
    background: white;
    color: #1865f2;
    text-decoration: none;
    border-radius: 6px;
    font-weight: 600;
    transition: all 0.2s ease;
    border: 2px solid #1865f2;
}

.help-link:hover {
    background: #1865f2;
    color: white;
}

.roadmap-actions, .resources-actions {
    text-align: center;
    margin-top: 30px;
}

.ai-action-btn {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border: none;
    padding: 15px 30px;
    border-radius: 8px;
    font-size: 1.1em;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
    display: inline-flex;
    align-items: center;
    gap: 10px;
}

.ai-action-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(102, 126, 234, 0.4);
}

.roadmap-placeholder, .resources-placeholder {
    text-align: center;
    padding: 60px;
    color: #626569;
    font-size: 1.1em;
}

.achievements-list {
    display: flex;
    flex-direction: column;
    gap: 15px;
}

.achievement-item {
    display: flex;
    align-items: center;
    gap: 12px;
}

.achievement-icon {
    font-size: 1.5em;
    width: 40px;
    text-align: center;
}

.achievement-name {
    font-weight: 600;
    color: #21242c;
    margin-bottom: 2px;
}

.achievement-desc {
    font-size: 0.9em;
    color: #626569;
}

.selector-content {
    display: flex;
    align-items: center;
    gap: 30px;
    flex-wrap: wrap;
}

.data-source-section {
    display: flex;
    align-items: center;
    gap: 10px;
}

.profile-selection {
    flex: 1;
}

.profile-cards-horizontal {
    display: flex;
    gap: 15px;
    flex-wrap: wrap;
}

.profile-card-compact {
    display: flex;
    align-items: center;
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    border: 2px solid #dee2e6;
    border-radius: 12px;
    padding: 12px 16px;
    cursor: pointer;
    transition: all 0.3s ease;
    min-width: 180px;
}

.profile-card-compact:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
    border-color: #667eea;
}

.profile-card-compact.selected {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border-color: #667eea;
}

.profile-icon-compact {
    font-size: 1.5em;
    margin-right: 12px;
}

.profile-info-compact h4 {
    margin: 0 0 2px 0;
    font-size: 0.95em;
    font-weight: bold;
}

.profile-info-compact p {
    margin: 0;
    font-size: 0.8em;
    opacity: 0.8;
}

.real-data-selection {
    flex: 1;
}

.real-selectors {
    display: flex;
    gap: 20px;
}

.selector-item {
    flex: 1;
}

.selector-item label {
    display: block;
    margin-bottom: 5px;
    font-weight: 600;
    font-size: 0.9em;
}

/* Khan Academy Style Dashboard */
.khan-dashboard {
    background: white;
    border-radius: 15px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    overflow: hidden;
}

/* Top Navigation Cards */
.top-nav-cards {
    display: flex;
    gap: 20px;
    padding: 20px;
    background: #f7f8fa;
    border-bottom: 1px solid #e7e8ea;
}

.nav-card {
    flex: 1;
    background: white;
    border: 2px solid #e7e8ea;
    border-radius: 12px;
    padding: 20px;
    cursor: pointer;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    gap: 15px;
}

.nav-card:hover {
    border-color: #1865f2;
    box-shadow: 0 4px 12px rgba(24, 101, 242, 0.1);
}

.nav-card.active {
    border-color: #1865f2;
    background: #f0f4ff;
}

.nav-icon {
    font-size: 2em;
    flex-shrink: 0;
}

.nav-content h3 {
    margin: 0 0 5px 0;
    font-size: 1.1em;
    font-weight: 600;
    color: #21242c;
}

.nav-content p {
    margin: 0;
    font-size: 0.9em;
    color: #626569;
}

/* Main Content Area */
.main-content-area {
    padding: 30px;
}

.content-pane {
    display: none;
}

.content-pane.active {
    display: block;
}

/* Welcome Section */
.welcome-section {
    display: flex;
    align-items: center;
    gap: 20px;
    margin-bottom: 30px;
    padding: 20px;
    background: linear-gradient(135deg, #f0f4ff 0%, #e8f2ff 100%);
    border-radius: 12px;
}

.student-avatar {
    flex-shrink: 0;
}

.avatar-circle {
    width: 80px;
    height: 80px;
    background: linear-gradient(135deg, #1865f2 0%, #0d47a1 100%);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 1.8em;
    font-weight: bold;
}

.welcome-text h2 {
    margin: 0 0 5px 0;
    font-size: 1.8em;
    color: #21242c;
    font-weight: 600;
}

.welcome-text p {
    margin: 0;
    color: #626569;
    font-size: 1em;
}

/* Dashboard Grid */
.dashboard-grid {
    display: grid;
    grid-template-columns: 2fr 1fr;
    gap: 25px;
    margin-bottom: 30px;
}

.performance-card, .streak-card {
    background: white;
    border: 1px solid #e7e8ea;
    border-radius: 12px;
    padding: 25px;
}

.card-title {
    font-size: 1.1em;
    font-weight: 600;
    color: #21242c;
    margin-bottom: 20px;
}

/* Performance Card */
.overall-score {
    text-align: center;
    margin-bottom: 25px;
}

.score-circle {
    display: inline-block;
}

.score-number {
    font-size: 3em;
    font-weight: bold;
    color: #1865f2;
    margin-bottom: 5px;
}

.score-label {
    color: #626569;
    font-size: 0.9em;
}

.subject-header {
    font-weight: 600;
    margin-bottom: 15px;
    color: #21242c;
}

.subject-list {
    display: flex;
    flex-direction: column;
    gap: 12px;
}

.subject-bar {
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.subject-name {
    font-weight: 500;
    color: #21242c;
    min-width: 60px;
}

.subject-progress {
    flex: 1;
    height: 8px;
    background: #f0f1f3;
    border-radius: 4px;
    margin: 0 15px;
    overflow: hidden;
}

.progress-fill {
    height: 100%;
    border-radius: 4px;
    transition: width 0.3s ease;
}

.progress-fill.needs-work { background: #ff6b6b; }
.progress-fill.fair { background: #ffa726; }
.progress-fill.good { background: #66bb6a; }

.subject-score {
    font-weight: 600;
    min-width: 40px;
    text-align: right;
}

.subject-score.needs-work { color: #ff6b6b; }
.subject-score.fair { color: #ffa726; }
.subject-score.good { color: #66bb6a; }

/* Streak Card */
.streak-number {
    font-size: 3em;
    font-weight: bold;
    color: #ff6b35;
    text-align: center;
    margin-bottom: 5px;
}

.streak-text {
    text-align: center;
    color: #626569;
    margin-bottom: 20px;
}

.streak-details {
    display: flex;
    justify-content: space-between;
}

.streak-item {
    text-align: center;
}

.streak-count {
    display: block;
    font-size: 1.2em;
    font-weight: 600;
    color: #21242c;
}

.streak-period {
    font-size: 0.8em;
    color: #626569;
}

/* Goals and Achievements */
.goals-achievements {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 25px;
}

.goals-card, .achievements-card {
    background: white;
    border: 1px solid #e7e8ea;
    border-radius: 12px;
    padding: 25px;
}

.goals-list {
    display: flex;
    flex-direction: column;
    gap: 12px;
}

.goal-item {
    display: flex;
    align-items: flex-start;
    gap: 10px;
}

.goal-bullet {
    color: #1865f2;
    font-weight: bold;
    margin-top: 2px;
}

.goal-text {
    color: #21242c;
    line-height: 1.4;
}

.achievements-list {
    display: flex;
    flex-direction: column;
    gap: 15px;
}

.achievement-item {
    display: flex;
    align-items: center;
    gap: 12px;
}

.achievement-icon {
    font-size: 1.5em;
    width: 40px;
    text-align: center;
}

.achievement-name {
    font-weight: 600;
    color: #21242c;
    margin-bottom: 2px;
}

.achievement-desc {
    font-size: 0.9em;
    color: #626569;
}

/* Tab Navigation */
.tab-navigation {
    display: flex;
    background: #f8f9fa;
    border-bottom: 2px solid #e9ecef;
}

.tab-btn {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 15px 25px;
    border: none;
    background: transparent;
    cursor: pointer;
    transition: all 0.3s ease;
    font-family: inherit;
    font-size: 0.95em;
    font-weight: 500;
    color: #666;
    border-bottom: 3px solid transparent;
}

.tab-btn:hover {
    background: rgba(102, 126, 234, 0.1);
    color: #667eea;
}

.tab-btn.active {
    background: white;
    color: #667eea;
    border-bottom-color: #667eea;
}

.tab-icon {
    font-size: 1.2em;
}

.tab-label {
    font-weight: 600;
}

/* Tab Content */
.tab-content {
    padding: 30px;
}

.tab-pane {
    display: none;
}

.tab-pane.active {
    display: block;
}

/* Overview Grid */
.overview-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    grid-template-rows: auto auto;
    gap: 25px;
}

.overview-card {
    background: #f8f9fa;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
}

.card-header {
    background: white;
    padding: 20px;
    border-bottom: 2px solid #e9ecef;
}

.card-header h3 {
    margin: 0;
    font-size: 1.2em;
    color: #333;
}

.card-content {
    padding: 20px;
}

/* Performance Metrics */
.performance-metrics {
    display: flex;
    justify-content: space-around;
    text-align: center;
}

.metric-item {
    flex: 1;
}

.metric-value {
    font-size: 2.5em;
    font-weight: bold;
    color: #667eea;
    margin-bottom: 5px;
}

.metric-label {
    font-size: 0.9em;
    color: #666;
    font-weight: 500;
}

/* Quick Actions */
.action-buttons-grid {
    display: flex;
    flex-direction: column;
    gap: 12px;
}

.quick-action-btn {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 12px 16px;
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    border: 2px solid #dee2e6;
    border-radius: 8px;
    cursor: pointer;
    transition: all 0.3s ease;
    font-family: inherit;
    font-weight: 500;
    text-align: left;
}

.quick-action-btn:hover {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border-color: #667eea;
}

.action-icon {
    font-size: 1.3em;
}

/* Tab Headers */
.tab-header {
    text-align: center;
    margin-bottom: 30px;
    padding-bottom: 20px;
    border-bottom: 2px solid #e9ecef;
}

.tab-header h2 {
    margin: 0 0 10px 0;
    color: #333;
    font-size: 1.8em;
}

.tab-header p {
    margin: 0;
    color: #666;
    font-size: 1.1em;
}

/* Action Section */
.action-section {
    text-align: center;
    margin-bottom: 30px;
}

.primary-action-btn {
    display: inline-flex;
    align-items: center;
    gap: 12px;
    padding: 15px 30px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border: none;
    border-radius: 25px;
    font-size: 1.1em;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
    font-family: inherit;
}

.primary-action-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(102, 126, 234, 0.3);
}

.btn-icon {
    font-size: 1.3em;
}

/* Results Area */
.results-area {
    background: #f8f9fa;
    border-radius: 12px;
    padding: 20px;
    min-height: 200px;
}

.sidebar {
    width: 300px;
    background: white;
    border-radius: 15px;
    padding: 25px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    height: fit-content;
    position: sticky;
    top: 20px;
}

.sidebar-header {
    margin-bottom: 20px;
    padding-bottom: 15px;
    border-bottom: 2px solid #e9ecef;
}

.sidebar-header h3 {
    margin: 0;
    color: #333;
    font-size: 1.3em;
}

.main-content {
    flex: 1;
    min-height: 600px;
}

.welcome-screen {
    background: white;
    border-radius: 15px;
    padding: 60px 40px;
    text-align: center;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    height: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
}

.welcome-content {
    max-width: 500px;
}

.welcome-icon {
    font-size: 4em;
    margin-bottom: 20px;
}

.welcome-content h2 {
    margin: 0 0 15px 0;
    color: #333;
    font-size: 2.2em;
}

.welcome-content p {
    color: #666;
    font-size: 1.1em;
    margin-bottom: 30px;
}

.welcome-features {
    display: flex;
    justify-content: center;
    gap: 30px;
    margin: 30px 0;
}

.feature-item {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 8px;
}

.feature-icon {
    font-size: 2em;
}

.welcome-instruction {
    background: linear-gradient(135deg, #e3f2fd, #f3e5f5);
    padding: 15px 20px;
    border-radius: 25px;
    font-weight: 500;
    margin-top: 30px;
}

.loading-screen {
    background: white;
    border-radius: 15px;
    padding: 60px 40px;
    text-align: center;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    height: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
}

.loading-content {
    max-width: 400px;
}

.loading-spinner {
    width: 60px;
    height: 60px;
    border: 4px solid #f3f3f3;
    border-top: 4px solid #667eea;
    border-radius: 50%;
    animation: spin 1s linear infinite;
    margin: 0 auto 30px;
}

.loading-content h3 {
    margin: 0 0 10px 0;
    color: #333;
    font-size: 1.5em;
}

.loading-content p {
    color: #666;
    margin: 0;
}

.student-dashboard {
    display: flex;
    flex-direction: column;
    gap: 25px;
}

.dashboard-section {
    background: white;
    border-radius: 15px;
    padding: 25px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
}

.section-header {
    margin-bottom: 20px;
    padding-bottom: 15px;
    border-bottom: 2px solid #e9ecef;
}

.section-header h3 {
    margin: 0 0 5px 0;
    color: #333;
    font-size: 1.4em;
}

.section-header p {
    margin: 0;
    color: #666;
    font-size: 0.95em;
}

.ai-actions-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 20px;
}

.ai-action-card {
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    border: 2px solid #dee2e6;
    border-radius: 15px;
    padding: 25px;
    text-align: center;
    cursor: pointer;
    transition: all 0.3s ease;
    font-family: inherit;
}

.ai-action-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
    border-color: #667eea;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
}

.action-icon {
    font-size: 3em;
    margin-bottom: 15px;
}

.ai-action-card h4 {
    margin: 10px 0;
    font-size: 1.2em;
    font-weight: bold;
}

.ai-action-card p {
    margin: 0;
    opacity: 0.8;
    font-size: 0.9em;
    line-height: 1.4;
}

/* Header Section */
.header-section {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 50px;
    border-radius: 15px;
    margin: 20px;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
    display: flex;
    justify-content: space-between;
    align-items: center;
    width: calc(100% - 40px);
}

.header-text {
    flex: 1;
}

.header-text.centered {
    text-align: center;
    margin: 0 auto;
    max-width: 800px;
}

.header-text h1 {
    font-size: 2.5em;
    margin: 0 0 10px 0;
    font-weight: bold;
}

.tagline {
    font-size: 1.3em;
    margin: 5px 0;
    opacity: 0.9;
}

.subtitle {
    font-size: 1.1em;
    margin: 10px 0;
    opacity: 0.8;
}

.stat-number {
    font-size: 2.2em;
    font-weight: bold;
    margin-bottom: 5px;
}

.stat-label {
    font-size: 0.9em;
    opacity: 0.9;
}

@media (max-width: 768px) {
    .header-content {
        flex-direction: column;
        text-align: center;
    }
    
    .header-stats {
        margin-top: 20px;
        justify-content: center;
    }
}

.student-selector {
    background: white;
    padding: 30px;
    border-radius: 15px;
    margin-bottom: 30px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
}

.data-source-toggle {
    margin-bottom: 25px;
}

.profile-cards-vertical {
    display: flex;
    flex-direction: column;
    gap: 15px;
}

.profile-card-small {
    display: flex;
    align-items: center;
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    border: 2px solid #dee2e6;
    border-radius: 12px;
    padding: 15px;
    cursor: pointer;
    transition: all 0.3s ease;
}

.profile-card-small:hover {
    transform: translateX(5px);
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
    border-color: #667eea;
}

.profile-card-small.selected {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border-color: #667eea;
}

.profile-icon-small {
    font-size: 2em;
    margin-right: 15px;
}

.profile-info-small h4 {
    margin: 0 0 5px 0;
    font-size: 1.1em;
    font-weight: bold;
}

.profile-info-small p {
    margin: 0;
    font-size: 0.9em;
    opacity: 0.8;
}

.real-data-section {
    margin-top: 20px;
}

.selector-group {
    margin-bottom: 20px;
}

.selector-group label {
    display: block;
    margin-bottom: 8px;
    font-weight: 600;
    color: #333;
}

.profile-section {
    margin-top: 20px;
}

.profile-cards {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 20px;
    margin-bottom: 20px;
}

.profile-card {
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    border: 2px solid #dee2e6;
    border-radius: 15px;
    padding: 25px;
    text-align: center;
    cursor: pointer;
    transition: all 0.3s ease;
    position: relative;
}

.profile-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
    border-color: #667eea;
}

.profile-card.selected {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border-color: #667eea;
}

.profile-icon {
    font-size: 3em;
    margin-bottom: 15px;
}

.profile-card h3 {
    margin: 10px 0;
    font-size: 1.3em;
    font-weight: bold;
}

.profile-card p {
    margin: 5px 0;
    opacity: 0.8;
}

.profile-stats {
    background: rgba(0, 0, 0, 0.1);
    padding: 8px 12px;
    border-radius: 20px;
    font-size: 0.9em;
    margin-top: 15px;
    font-weight: 500;
}

.real-data-section {
    margin-top: 20px;
}

.data-selectors {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 20px;
}

.selector-group {
    display: flex;
    flex-direction: column;
}

.selector-group label {
    margin-bottom: 8px;
    font-weight: 600;
}

.dashboard-container {
    background: white;
    border-radius: 15px;
    padding: 30px;
    margin-bottom: 30px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
}

.quick-actions {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 20px;
    margin-bottom: 30px;
}

.action-btn {
    display: flex;
    align-items: center;
    padding: 20px;
    border: none;
    border-radius: 12px;
    cursor: pointer;
    transition: all 0.3s ease;
    text-align: left;
    font-family: inherit;
}

.action-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
}

.action-btn.primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
}

.action-btn.secondary {
    background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
    color: white;
}

.action-btn.success {
    background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
    color: white;
}

.btn-icon {
    font-size: 2.5em;
    margin-right: 15px;
}

.btn-text {
    flex: 1;
}

.btn-title {
    font-size: 1.2em;
    font-weight: bold;
    margin-bottom: 5px;
}

.btn-subtitle {
    font-size: 0.9em;
    opacity: 0.9;
}

.student-profile-display {
    background: #f8f9fa;
    border-radius: 12px;
    padding: 25px;
    margin-bottom: 20px;
}

.profile-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 25px;
    padding-bottom: 20px;
    border-bottom: 2px solid #dee2e6;
}

.profile-info h3 {
    margin: 0 0 10px 0;
    font-size: 1.5em;
    color: #333;
}

.profile-meta {
    display: flex;
    gap: 20px;
    flex-wrap: wrap;
}

.meta-item {
    background: white;
    padding: 8px 12px;
    border-radius: 20px;
    font-size: 0.9em;
    font-weight: 500;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.status-badge {
    padding: 12px 20px;
    border-radius: 25px;
    font-weight: bold;
    font-size: 1.1em;
}

.status-badge.excellent {
    background: linear-gradient(135deg, #28a745, #20c997);
    color: white;
}

.status-badge.good {
    background: linear-gradient(135deg, #17a2b8, #6f42c1);
    color: white;
}

.status-badge.needs-work {
    background: linear-gradient(135deg, #ffc107, #fd7e14);
    color: white;
}

.subjects-overview h4 {
    margin: 0 0 15px 0;
    color: #333;
    font-size: 1.2em;
}

.subjects-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 15px;
    margin-bottom: 20px;
}

.subject-card {
    background: white;
    border-radius: 10px;
    padding: 15px;
    text-align: center;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    transition: transform 0.2s ease;
}

.subject-card:hover {
    transform: translateY(-2px);
}

.subject-card.strong {
    border-left: 4px solid #28a745;
}

.subject-card.average {
    border-left: 4px solid #ffc107;
}

.subject-card.weak {
    border-left: 4px solid #dc3545;
}

.subject-name {
    font-weight: bold;
    margin-bottom: 8px;
    color: #333;
}

.subject-score {
    font-size: 1.5em;
    font-weight: bold;
    margin-bottom: 5px;
}

.subject-card.strong .subject-score {
    color: #28a745;
}

.subject-card.average .subject-score {
    color: #ffc107;
}

.subject-card.weak .subject-score {
    color: #dc3545;
}

.subject-status {
    font-size: 0.9em;
    opacity: 0.8;
}

.ai-prompt {
    background: linear-gradient(135deg, #e3f2fd, #f3e5f5);
    border-radius: 10px;
    padding: 20px;
    text-align: center;
    margin-top: 20px;
}

.ai-prompt p {
    margin: 0;
    font-size: 1.1em;
}

.action-buttons {
    display: flex;
    gap: 15px;
    margin-top: 15px;
    flex-wrap: wrap;
}

.result-card {
    background: #f8f9fa;
    border: 1px solid #ddd;
    border-radius: 10px;
    padding: 20px;
    margin-bottom: 20px;
}

.student-info {
    background: #e3f2fd;
    padding: 15px;
    border-radius: 8px;
    margin: 15px 0;
}

.scores-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 10px;
    margin: 15px 0;
}

.score-item {
    background: white;
    padding: 10px;
    border-radius: 5px;
    text-align: center;
}

.score-value {
    font-size: 1.5em;
    font-weight: bold;
    color: #667eea;
}

.ai-response {
    background: white;
    border-left: 4px solid #667eea;
    padding: 20px;
    margin: 15px 0;
    line-height: 1.6;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.ai-response h3 {
    color: #667eea;
    margin-top: 20px;
    margin-bottom: 10px;
    font-size: 1.2em;
    border-bottom: 2px solid #e9ecef;
    padding-bottom: 5px;
}

.ai-response h4 {
    color: #495057;
    margin-top: 15px;
    margin-bottom: 8px;
    font-size: 1.1em;
}

.ai-response ul, .ai-response ol {
    margin: 10px 0;
    padding-left: 25px;
}

.ai-response li {
    margin-bottom: 5px;
}

.roadmap-week {
    background: #f8f9fa;
    border: 1px solid #dee2e6;
    border-radius: 8px;
    padding: 15px;
    margin: 15px 0;
}

.roadmap-week h4 {
    color: #28a745;
    margin-bottom: 10px;
}

.progress-indicator {
    background: linear-gradient(90deg, #28a745 0%, #ffc107 50%, #dc3545 100%);
    height: 8px;
    border-radius: 4px;
    margin: 10px 0;
    position: relative;
}

.progress-marker {
    position: absolute;
    top: -5px;
    width: 18px;
    height: 18px;
    background: white;
    border: 3px solid #667eea;
    border-radius: 50%;
}

.recommendation-card {
    background: #e8f4fd;
    border: 1px solid #b8daff;
    border-radius: 8px;
    padding: 15px;
    margin: 10px 0;
}

.strength-highlight {
    background: #d4edda;
    border-left: 4px solid #28a745;
    padding: 10px;
    margin: 10px 0;
    border-radius: 4px;
}

.weakness-highlight {
    background: #f8d7da;
    border-left: 4px solid #dc3545;
    padding: 10px;
    margin: 10px 0;
    border-radius: 4px;
}

.action-item {
    background: #fff3cd;
    border: 1px solid #ffeaa7;
    border-radius: 6px;
    padding: 12px;
    margin: 8px 0;
    position: relative;
    padding-left: 40px;
}

.action-item::before {
    content: "✓";
    position: absolute;
    left: 15px;
    top: 12px;
    color: #28a745;
    font-weight: bold;
    font-size: 16px;
}

.loading {
    text-align: center;
    padding: 40px;
    color: #666;
}

.spinner {
    border: 4px solid #f3f3f3;
    border-top: 4px solid #667eea;
    border-radius: 50%;
    width: 40px;
    height: 40px;
    animation: spin 1s linear infinite;
    margin: 0 auto 20px;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

/* Responsive Design */
@media (max-width: 1024px) {
    .app-container {
        flex-direction: column;
        gap: 20px;
    }
    
    .sidebar {
        width: 100%;
        position: static;
    }
    
    .profile-cards-vertical {
        flex-direction: row;
        overflow-x: auto;
        gap: 10px;
    }
    
    .profile-card-small {
        min-width: 200px;
        flex-shrink: 0;
    }
    
    .data-selectors {
        grid-template-columns: 1fr;
    }
}

@media (max-width: 768px) {
    .learnpath-container {
        padding: 10px;
    }
    
    .header-content {
        flex-direction: column;
        text-align: center;
    }
    
    .header-stats {
        margin-top: 20px;
        justify-content: center;
    }
    
    .welcome-features {
        flex-direction: column;
        gap: 15px;
    }
    
    .ai-actions-grid {
        grid-template-columns: 1fr;
    }
    
    .subjects-grid {
        grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
    }
    
    .profile-meta {
        flex-direction: column;
        gap: 10px;
    }
}
</style>

<div class="learnpath-container">
    <div class="learnpath-header">
        <div class="header-content">
            <div class="header-text centered">
                <h1>🧭 LearnPath Navigator</h1>
                <p class="tagline">Your AI-powered learning companion</p>
                <p class="subtitle">Personalized study paths • Real-time analysis • Smart recommendations</p>
            </div>
        </div>
    </div>
    
    <!-- Main Container with proper spacing -->
    <div style="padding: 20px; margin-top: 20px; width: 100%;">
        <!-- Khan Academy Layout -->
        <div class="khan-layout" style="border-radius: 12px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); width: 100%;">
        <!-- Left Sidebar -->
        <div class="left-sidebar">
            <div class="sidebar-header">
                <h3>Data Source</h3>
                <select id="dataSource" class="sidebar-dropdown" onchange="toggleDataSource()">
                    <option value="demo">Demo Profiles</option>
                    <option value="real">My Data</option>
                </select>
            </div>
            
            <div class="sidebar-section">
                <h4>Student Profiles</h4>
                
                <div id="demoDataSection" class="student-list">
                    <div class="student-item" onclick="selectProfile('struggling')">
                        <div class="student-avatar orange">A</div>
                        <div class="student-details">
                            <div class="student-name">Alex Johnson</div>
                            <div class="student-grade">Grade 8</div>
                        </div>
                        <div class="student-score orange">65%</div>
                    </div>
                    
                    <div class="student-item" onclick="selectProfile('average')">
                        <div class="student-avatar blue">M</div>
                        <div class="student-details">
                            <div class="student-name">Maria Garcia</div>
                            <div class="student-grade">Grade 8</div>
                        </div>
                        <div class="student-score blue">78%</div>
                    </div>
                    
                    <div class="student-item" onclick="selectProfile('advanced')">
                        <div class="student-avatar green">D</div>
                        <div class="student-details">
                            <div class="student-name">David Chen</div>
                            <div class="student-grade">Grade 8</div>
                        </div>
                        <div class="student-score green">95%</div>
                    </div>
                </div>
                
                <div id="realDataSection" class="real-data-section" style="display: none;">
                    <div class="form-group">
                        <label>📚 Course:</label>
                        <select id="courseSelect" class="form-control" onchange="loadCourseStudents()">
                            <option value="">Select course...</option>
                            <?php
                            $courses = $DB->get_records_sql("SELECT id, shortname, fullname FROM {course} WHERE id > 1 ORDER BY shortname");
                            foreach ($courses as $course) {
                                echo "<option value='{$course->id}'>{$course->shortname} - {$course->fullname}</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>👤 Student:</label>
                        <select id="studentSelect" class="form-control" onchange="loadRealStudentData()">
                            <option value="">Select course first...</option>
                        </select>
                    </div>
                </div>
                
                <select id="studentProfile" style="display: none;">
                    <option value="struggling">Alex Johnson</option>
                    <option value="average">Maria Garcia</option>
                    <option value="advanced">David Chen</option>
                </select>
            </div>
        </div>

        <!-- Main Content Area -->
        <div class="main-area">
            <!-- Top Navigation Cards -->
            <div class="top-nav-cards">
                <div class="nav-card blue active" onclick="switchTab('dashboard')">
                    <div class="nav-icon">📊</div>
                    <div class="nav-text">
                        <h3>My Dashboard</h3>
                        <p>View your progress and achievements</p>
                    </div>
                </div>
                <div class="nav-card purple" onclick="switchTab('roadmap')">
                    <div class="nav-icon">🗺️</div>
                    <div class="nav-text">
                        <h3>Learning Roadmap</h3>
                        <p>Personalized math learning path</p>
                    </div>
                </div>
                <div class="nav-card green" onclick="switchTab('resources')">
                    <div class="nav-icon">📚</div>
                    <div class="nav-text">
                        <h3>Study Resources</h3>
                        <p>Curated materials for your level</p>
                    </div>
                </div>
            </div>

            <!-- Dashboard Content -->
            <div id="dashboard-content" class="content-area">
                <!-- Welcome Section -->
                <div class="welcome-section">
                    <div class="welcome-avatar">
                        <div class="avatar-circle" id="student-avatar"></div>
                    </div>
                    <div class="welcome-text">
                        <h1 id="welcome-title"></h1>
                        <p id="welcome-subtitle">Grade 8 • You're improving! Every step counts. 💪</p>
                    </div>
                </div>

                <!-- Dashboard Grid -->
                <div class="dashboard-content">
                    <!-- Performance Section -->
                    <div class="performance-section">
                        <div class="section-title">📈 Your Performance</div>
                        
                        <div class="overall-performance">
                            <div class="score-display">
                                <div class="score-number" id="overall-score"></div>
                                <div class="score-label">Overall Score</div>
                            </div>
                            <div class="score-bar">
                                <div class="score-fill" id="score-progress" style="width: 0%"></div>
                            </div>
                        </div>

                        <div class="subjects-performance">
                            <div class="subjects-title">Subject Performance</div>
                            <div id="subjects-list" class="subjects-list">
                                <!-- Subject bars will be populated here -->
                            </div>
                        </div>
                    </div>

                    <!-- Study Streak Section -->
                    <div class="streak-section">
                        <div class="section-title">🔥 Study Streak</div>
                        <div class="streak-display">
                            <div class="streak-number" id="streak-days"></div>
                            <div class="streak-text">Days in a row!</div>
                        </div>
                        <div class="streak-stats">
                            <div class="streak-stat">
                                <div class="stat-number" id="this-week"></div>
                                <div class="stat-label">This week</div>
                            </div>
                            <div class="streak-stat">
                                <div class="stat-number" id="best-streak"></div>
                                <div class="stat-label">Best streak</div>
                            </div>
                        </div>
                    </div>

                    <!-- Recent Achievements -->
                    <div class="achievements-section">
                        <div class="section-title">🏆 Recent Achievements</div>
                        <div class="achievements-list">
                            <div class="achievement-item">
                                <div class="achievement-icon">🧮</div>
                                <div class="achievement-info">
                                    <div class="achievement-name">Problem Solver</div>
                                    <div class="achievement-desc">Solved 20 math problems</div>
                                </div>
                            </div>
                            <div class="achievement-item">
                                <div class="achievement-icon">📖</div>
                                <div class="achievement-info">
                                    <div class="achievement-name">Bookworm</div>
                                    <div class="achievement-desc">Read 3 articles this week</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Learning Roadmap Tab -->
            <div id="roadmap-content" class="content-area" style="display: none;">
                <div class="khan-roadmap-header">
                    <h1>Your Personalized Learning Journey</h1>
                    <p>AI-generated roadmap based on your performance analysis</p>
                    <button class="generate-roadmap-btn" onclick="generateRoadmap()">
                        <span>🤖 Generate Learning Roadmap</span>
                    </button>
                </div>
                
                <div class="khan-roadmap-container">
                    <div id="roadmap-steps" class="khan-roadmap-modules">
                        <!-- AI-generated modules will appear here -->
                        <div class="roadmap-placeholder">
                            <p>Click "Generate Learning Roadmap" to create your personalized learning path</p>
                        </div>
                    </div>
                    
                    <div class="roadmap-footer">
                        <div class="keep-going-message">
                            <h3>Keep Going, Alex! 🚀</h3>
                            <p>Start your learning journey by completing the first step</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Study Resources Tab -->
            <div id="resources-content" class="content-area" style="display: none;">
                <div class="khan-resources-header">
                    <h1>Recommended Study Resources</h1>
                    <p>AI-curated materials based on your performance • Personalized for you</p>
                    <button class="generate-resources-btn" onclick="getRecommendations()">
                        <span>🤖 Get Study Resources</span>
                    </button>
                </div>
                
                <div class="khan-resources-container">
                    <div id="resources-list" class="khan-resources-grid">
                        <!-- AI-generated resources will appear here -->
                        <div class="resources-placeholder">
                            <p>Click "Get Study Resources" to receive AI-curated materials</p>
                        </div>
                    </div>
                    
                    <div class="resources-footer">
                        <div class="need-help-section">
                            <h3>Need More Help? 🎯</h3>
                            <p>These resources are just the beginning! Ask your teacher for personalized recommendations or explore our full library for comprehensive learning.</p>
                            <div class="help-links">
                                <a href="#" class="help-link">Visit Study Library</a>
                                <a href="#" class="help-link">Ask Teacher</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div> <!-- End of Main Container -->

    <!-- Khan Academy Style Dashboard -->
    <div id="main-dashboard" class="khan-dashboard" style="display: none;">
        <!-- Top Navigation Cards (like Khan Academy) -->
        <div class="top-nav-cards">
            <div class="nav-card active" onclick="switchTab('dashboard')" id="dashboard-card">
                <div class="nav-icon">📊</div>
                <div class="nav-content">
                    <h3>My Dashboard</h3>
                    <p>View your progress and achievements</p>
                </div>
            </div>
            <div class="nav-card" onclick="switchTab('roadmap')" id="roadmap-card">
                <div class="nav-icon">🗺️</div>
                <div class="nav-content">
                    <h3>Learning Roadmap</h3>
                    <p>Personalized math learning path</p>
                </div>
            </div>
            <div class="nav-card" onclick="switchTab('resources')" id="resources-card">
                <div class="nav-icon">📚</div>
                <div class="nav-content">
                    <h3>Study Resources</h3>
                    <p>Curated materials for your level</p>
                </div>
            </div>
        </div>

        <!-- Main Content Area -->
        <div class="main-content-area">
            <!-- Dashboard Tab -->
            <div id="dashboard-content" class="content-pane active">
                <!-- Welcome Section -->
                <div class="welcome-section">
                    <div class="student-avatar">
                        <div class="avatar-circle" id="student-avatar">AJ</div>
                    </div>
                    <div class="welcome-text">
                        <h2 id="welcome-message">Welcome back, Alex!</h2>
                        <p id="progress-message">Grade 8 • You're improving! Every step counts. 💪</p>
                    </div>
                </div>

                <!-- Performance Section -->
                <div class="dashboard-grid">
                    <div class="performance-card">
                        <div class="card-title">📈 Your Performance</div>
                        <div class="overall-score">
                            <div class="score-circle">
                                <div class="score-number" id="overall-score">58%</div>
                                <div class="score-label">Overall Score</div>
                            </div>
                        </div>
                        <div class="subject-bars">
                            <div class="subject-performance">
                                <div class="subject-header">Subject Performance</div>
                                <div id="subject-list" class="subject-list">
                                    <!-- Subject bars will be populated here -->
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="streak-card">
                        <div class="card-title">🔥 Study Streak</div>
                        <div class="streak-number" id="streak-days">7</div>
                        <div class="streak-text">Days in a row!</div>
                        <div class="streak-details">
                            <div class="streak-item">
                                <span class="streak-count" id="this-week">3/7</span>
                                <span class="streak-period">This week</span>
                            </div>
                            <div class="streak-item">
                                <span class="streak-count" id="best-week">14</span>
                                <span class="streak-period">Best streak</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Goals and Achievements -->
                <div class="goals-achievements">
                    <div class="goals-card">
                        <div class="card-title">🎯 This Week's Goals</div>
                        <div id="weekly-goals" class="goals-list">
                            <div class="goal-item">
                                <span class="goal-bullet">•</span>
                                <span class="goal-text">Boost math score by 10 points</span>
                            </div>
                            <div class="goal-item">
                                <span class="goal-bullet">•</span>
                                <span class="goal-text">Complete 3 practice exercises daily</span>
                            </div>
                            <div class="goal-item">
                                <span class="goal-bullet">•</span>
                                <span class="goal-text">Join 1 study group session</span>
                            </div>
                            <div class="goal-item">
                                <span class="goal-bullet">•</span>
                                <span class="goal-text">Review yesterday's notes every morning</span>
                            </div>
                        </div>
                    </div>

                    <div class="achievements-card">
                        <div class="card-title">🏆 Recent Achievements</div>
                        <div id="achievements-list" class="achievements-list">
                            <div class="achievement-item">
                                <div class="achievement-icon">🧮</div>
                                <div class="achievement-info">
                                    <div class="achievement-name">Problem Solver</div>
                                    <div class="achievement-desc">Solved 20 math problems</div>
                                </div>
                            </div>
                            <div class="achievement-item">
                                <div class="achievement-icon">📖</div>
                                <div class="achievement-info">
                                    <div class="achievement-name">Bookworm</div>
                                    <div class="achievement-desc">Read 3 articles this week</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Learning Roadmap Tab -->
            <div id="roadmap-content" class="content-pane">
                <div class="roadmap-header">
                    <h2 id="roadmap-title">Your Math Learning Journey</h2>
                    <p id="roadmap-subtitle">Personalized roadmap for Alex • 0% complete</p>
                </div>

                <div class="roadmap-container">
                    <div id="roadmap-steps" class="roadmap-steps">
                        <!-- AI-generated roadmap steps will appear here -->
                        <div class="roadmap-step">
                            <div class="step-number">1</div>
                            <div class="step-content">
                                <div class="step-title">Master Basic Algebra <span class="step-badge new">New</span></div>
                                <div class="step-description">Build strong foundation in algebraic expressions and equations</div>
                                <div class="step-meta">
                                    <span class="step-duration">⏱ 2 weeks</span>
                                    <span class="step-resources">📚 4 resources</span>
                                </div>
                                <div class="step-topics">
                                    <span class="topic-tag">Linear equations</span>
                                    <span class="topic-tag">Variable isolation</span>
                                    <span class="topic-tag">Order of operations</span>
                                </div>
                                <div class="step-actions">
                                    <button class="action-btn primary">Start Learning</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="roadmap-actions">
                        <button class="primary-action-btn" onclick="generateRoadmap()" id="roadmapBtn">
                            <span class="btn-icon">🤖</span>
                            <span>Generate AI Roadmap</span>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Study Resources Tab -->
            <div id="resources-content" class="content-pane">
                <div class="resources-header">
                    <h2>Recommended Study Resources for Math</h2>
                    <p>Handpicked resources to boost your learning • Level: Beginner</p>
                </div>

                <div class="resources-grid">
                    <div id="resources-list" class="resources-list">
                        <!-- AI-generated resources will appear here -->
                        <div class="resource-card">
                            <div class="resource-icon algebra">A</div>
                            <div class="resource-content">
                                <div class="resource-title">Algebra Fundamentals</div>
                                <div class="resource-provider">Khan Academy</div>
                                <div class="resource-description">Master the basics of algebra with step-by-step explanations and practice problems.</div>
                                <div class="resource-meta">
                                    <span class="resource-level beginner">Beginner</span>
                                    <span class="resource-duration">⏱ 4 hours</span>
                                    <span class="resource-rating">⭐ 4.8</span>
                                </div>
                                <button class="resource-btn">Start Learning</button>
                            </div>
                        </div>
                    </div>

                    <div class="resources-actions">
                        <button class="primary-action-btn" onclick="getRecommendations()" id="recommendBtn">
                            <span class="btn-icon">🤖</span>
                            <span>Get AI Recommendations</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Welcome Screen -->
    <div id="welcome-screen" class="welcome-screen" style="display: none;">
        <div class="welcome-content">
            <div class="welcome-icon">🧭</div>
            <h2>Welcome to LearnPath Navigator</h2>
            <p>Your AI-powered learning companion</p>
            <div class="welcome-features">
                <div class="feature-item">
                    <span class="feature-icon">📊</span>
                    <span>Performance Analysis</span>
                </div>
                <div class="feature-item">
                    <span class="feature-icon">🗺️</span>
                    <span>Learning Roadmaps</span>
                </div>
                <div class="feature-item">
                    <span class="feature-icon">💡</span>
                    <span>Smart Recommendations</span>
                </div>
            </div>
            <p class="welcome-instruction">👆 Select a student profile to get started</p>
        </div>
    </div>

    <!-- Loading Screen -->
    <div id="loading-screen" class="loading-screen" style="display: none;">
        <div class="loading-content">
            <div class="loading-spinner"></div>
            <h3>Loading Student Data...</h3>
            <p>Analyzing performance and preparing insights</p>
        </div>
    </div>
</div>

<script>
let currentStudentData = null;

function selectProfile(profile) {
    console.log('Selecting profile:', profile);
    
    // Update the hidden select
    document.getElementById('studentProfile').value = profile;
    
    // Update visual selection
    document.querySelectorAll('.student-item').forEach(item => {
        item.classList.remove('selected');
    });
    event.target.closest('.student-item').classList.add('selected');
    
    // Show loading screen
    showLoadingScreen();
    
    // Load student data automatically
    setTimeout(() => {
        loadStudentData();
    }, 1000);
}

function switchTab(tabName) {
    // Remove active class from all nav cards and content areas
    document.querySelectorAll('.nav-card').forEach(card => card.classList.remove('active'));
    document.querySelectorAll('.content-area').forEach(area => area.style.display = 'none');
    
    // Add active class to selected nav card and show content area
    const cards = document.querySelectorAll('.nav-card');
    if (tabName === 'dashboard') {
        cards[0].classList.add('active');
    } else if (tabName === 'roadmap') {
        cards[1].classList.add('active');
    } else if (tabName === 'resources') {
        cards[2].classList.add('active');
    }
    
    document.getElementById(tabName + '-content').style.display = 'block';
}

function loadRealStudentData() {
    const courseId = document.getElementById('courseSelect').value;
    const studentId = document.getElementById('studentSelect').value;
    
    if (!courseId || !studentId) {
        return;
    }
    
    // Show loading screen
    showLoadingScreen();
    
    // Load student data
    setTimeout(() => {
        loadStudentData();
    }, 1000);
}

function showLoadingScreen() {
    document.getElementById('welcome-screen').style.display = 'none';
    document.querySelector('.khan-layout').style.display = 'none';
    document.getElementById('loading-screen').style.display = 'flex';
}

function showMainDashboard() {
    document.getElementById('welcome-screen').style.display = 'none';
    document.getElementById('loading-screen').style.display = 'none';
    document.querySelector('.khan-layout').style.display = 'flex';
}

function showWelcomeScreen() {
    document.querySelector('.khan-layout').style.display = 'none';
    document.getElementById('loading-screen').style.display = 'none';
    document.getElementById('welcome-screen').style.display = 'flex';
}

function toggleDataSource() {
    const dataSource = document.getElementById('dataSource').value;
    const demoSection = document.getElementById('demoDataSection');
    const realSection = document.getElementById('realDataSection');
    
    if (dataSource === 'demo') {
        demoSection.style.display = 'block';
        realSection.style.display = 'none';
    } else {
        demoSection.style.display = 'none';
        realSection.style.display = 'block';
    }
    
    // Keep Khan Academy layout visible
    // showWelcomeScreen();
    
    // Reset header stats
    updateHeaderStats(null);
}

function loadCourseStudents() {
    const courseId = document.getElementById('courseSelect').value;
    const studentSelect = document.getElementById('studentSelect');
    
    if (!courseId) {
        studentSelect.innerHTML = '<option value="">Select a course first...</option>';
        return;
    }
    
    studentSelect.innerHTML = '<option value="">Loading students...</option>';
    
    // Fetch students for this course
    fetch('<?php echo $CFG->wwwroot; ?>/local/learnpath/ajax.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'action=get_course_students&courseid=' + courseId
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            let options = '<option value="">Select a student...</option>';
            data.students.forEach(student => {
                options += `<option value="${student.id}">${student.firstname} ${student.lastname}</option>`;
            });
            studentSelect.innerHTML = options;
        } else {
            studentSelect.innerHTML = '<option value="">No students found</option>';
        }
    })
    .catch(error => {
        studentSelect.innerHTML = '<option value="">Error loading students</option>';
    });
}

function loadStudentData() {
    const dataSource = document.getElementById('dataSource').value;
    
    console.log('Loading student data for dataSource:', dataSource);
    
    let ajaxData;
    if (dataSource === 'demo') {
        const profile = document.getElementById('studentProfile').value;
        ajaxData = 'action=get_student&profile=' + encodeURIComponent(profile);
    } else {
        const courseId = document.getElementById('courseSelect').value;
        const studentId = document.getElementById('studentSelect').value;
        
        if (!courseId || !studentId) {
            alert('Please select both course and student');
            return;
        }
        
        ajaxData = 'action=get_real_student&userid=' + studentId + '&courseid=' + courseId;
    }
    
    // Make AJAX call to get student data
    fetch('<?php echo $CFG->wwwroot; ?>/local/learnpath/ajax.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: ajaxData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            currentStudentData = data.data;
            displayStudentData(data.data);
            
            // Action buttons will be enabled when tabs are clicked
        } else {
            alert('Error: ' + data.message);
            showWelcomeScreen();
        }
    })
    .catch(error => {
        alert('Failed to load student data: ' + error.message);
        showWelcomeScreen();
    });
}

function updateHeaderStats(data) {
    // This function is no longer needed for Khan Academy layout
    // Stats are updated directly in displayStudentProfile
    console.log('Header stats updated for:', data ? data.name : 'no data');
}

function displayStudentData(data) {
    // Use the new profile display instead
    displayStudentProfile(data);
}

function displayStudentProfile(data) {
    const scores = Object.entries(data.scores);
    const avgScore = Math.round(Object.values(data.scores).reduce((a, b) => a + b, 0) / scores.length);

    // Update welcome section
    const initials = data.name.split(' ').map(n => n[0]).join('');
    document.getElementById('student-avatar').textContent = initials;
    document.getElementById('welcome-title').textContent = `Welcome back, ${data.name.split(' ')[0]}!`;
    document.getElementById('welcome-subtitle').textContent = `Grade 8 • You're improving! Every step counts. 💪`;

    // Update overall score
    document.getElementById('overall-score').textContent = avgScore + '%';
    document.getElementById('score-progress').style.width = avgScore + '%';

    // Update subject list with Khan Academy style bars
    const subjectsList = document.getElementById('subjects-list');
    subjectsList.innerHTML = scores.map(([subject, score]) => {
        const status = score >= 75 ? 'good' : score >= 50 ? 'fair' : 'needs-work';
        return `
            <div class="subject-row">
                <div class="subject-name">${subject}</div>
                <div class="subject-bar">
                    <div class="subject-fill ${status}" style="width: ${score}%"></div>
                </div>
                <div class="subject-score ${status}">${score}%</div>
            </div>
        `;
    }).join('');

    // Update study streak (using demo data for now)
    document.getElementById('streak-days').textContent = data.streak || 7;
    document.getElementById('this-week').textContent = data.streak_this_week || '3/7';
    document.getElementById('best-streak').textContent = data.streak_best || 14;

    // Update achievements (using demo data for now)
    const achievementsList = document.querySelector('.achievements-list');
    achievementsList.innerHTML = `
        <div class="achievement-item">
            <div class="achievement-icon">🧮</div>
            <div class="achievement-info">
                <div class="achievement-name">Problem Solver</div>
                <div class="achievement-desc">Solved ${data.problems_solved || 20} math problems</div>
            </div>
        </div>
        <div class="achievement-item">
            <div class="achievement-icon">📖</div>
            <div class="achievement-info">
                <div class="achievement-name">Bookworm</div>
                <div class="achievement-desc">Read ${data.articles_read || 3} articles this week</div>
            </div>
        </div>
    `;

    // Show the main dashboard
    showMainDashboard();

    // Update header stats
    updateHeaderStats(data);
}

function runAnalysis() {
    if (!currentStudentData) {
        alert('Please load student data first!');
        return;
    }
    
    const resultsDiv = document.getElementById('roadmap-steps');
    const dataSource = document.getElementById('dataSource').value;
    
    // Switch to roadmap tab and add loading indicator
    switchTab('roadmap');
    resultsDiv.innerHTML = '<div class="loading"><div class="spinner"></div>🤖 AI is analyzing student performance...</div>';
    
    let ajaxData;
    if (dataSource === 'demo') {
        const profile = document.getElementById('studentProfile').value;
        ajaxData = 'action=analyze_student&profile=' + encodeURIComponent(profile);
    } else {
        const courseId = document.getElementById('courseSelect').value;
        const studentId = document.getElementById('studentSelect').value;
        ajaxData = 'action=analyze_real_student&userid=' + studentId + '&courseid=' + courseId;
    }
    
    // Make AJAX call for analysis
    fetch('<?php echo $CFG->wwwroot; ?>/local/learnpath/ajax.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: ajaxData
    })
    .then(response => response.json())
    .then(data => {
        // Remove loading indicator
        const loadingDiv = document.querySelector('.loading');
        if (loadingDiv) loadingDiv.remove();
        
        console.log('Analysis response:', data); // Debug log
        
        if (data && (data.success || data.response)) {
            const formattedResponse = formatAIResponse(data.response || 'Analysis completed successfully!');
            
            // Create Khan Academy style roadmap steps
            const roadmapSteps = `
                <div class="roadmap-step">
                    <div class="step-number">1</div>
                    <div class="step-content">
                        <div class="step-title">AI-Generated Learning Plan <span class="step-badge new">New</span></div>
                        <div class="step-description">Personalized recommendations based on your performance</div>
                        <div class="step-meta">
                            <span class="step-duration">⏱ 4 weeks</span>
                            <span class="step-resources">🤖 AI-powered</span>
                        </div>
                        <div class="ai-content">
                            ${formattedResponse}
                        </div>
                        ${data.error ? '<div class="step-note">Note: ' + (data.fallback ? 'Using intelligent fallback' : 'Live AI analysis') + '</div>' : ''}
                    </div>
                </div>
            `;
            
            resultsDiv.innerHTML = roadmapSteps;
        } else {
            // Show more detailed error information
            const errorMsg = data && data.message ? data.message : 'Unknown error occurred';
            resultsDiv.innerHTML += '<div class="alert alert-danger">Failed to get AI analysis: ' + errorMsg + '<br><small>Debug: ' + JSON.stringify(data) + '</small></div>';
        }
    })
    .catch(error => {
        const loadingDiv = document.querySelector('.loading');
        if (loadingDiv) loadingDiv.remove();
        resultsDiv.innerHTML += '<div class="alert alert-danger">Failed to get AI analysis: ' + error.message + '</div>';
    });
}

function generateRoadmap() {
    if (!currentStudentData) {
        alert('Please select a student first!');
        return;
    }
    
    const resultsDiv = document.getElementById('roadmap-steps');
    const dataSource = document.getElementById('dataSource').value;
    
    // Add loading indicator
    resultsDiv.innerHTML = '<div class="loading"><div class="spinner"></div>🗺️ AI is generating personalized learning roadmap...</div>';
    
    // Create enhanced prompt for AI with specific module options
    const scores = currentStudentData.scores || {};
    const weakSubjects = Object.entries(scores).filter(([s, score]) => score < 60).map(([s]) => s);
    const strongSubjects = Object.entries(scores).filter(([s, score]) => score >= 75).map(([s]) => s);
    
    const aiPrompt = `Student scores: ${JSON.stringify(scores)}. Generate exactly 3 learning modules. Format: Title|Duration|Lessons|Skill1,Skill2,Skill3

Example for weak math student:
Master Quadratic Equations|2 weeks|12 lessons|Algebra,Factoring,Graphing
Complete 50 Physics Problems|3 weeks|18 lessons|Mechanics,Forces,Motion
Write Research Essay|1 week|8 lessons|Writing,Research,Analysis

Rules:
- Use specific actionable titles (Master X, Complete Y, Build Z)
- Focus on weakest subjects first
- No vague words like "improve" or "practice"
- Return exactly 3 lines, nothing else`;
    
    let ajaxData;
    if (dataSource === 'demo') {
        const profile = document.getElementById('studentProfile').value;
        ajaxData = 'action=generate_roadmap&profile=' + encodeURIComponent(profile) + '&prompt=' + encodeURIComponent(aiPrompt);
    } else {
        const courseId = document.getElementById('courseSelect').value;
        const studentId = document.getElementById('studentSelect').value;
        ajaxData = 'action=generate_real_roadmap&userid=' + studentId + '&courseid=' + courseId + '&prompt=' + encodeURIComponent(aiPrompt);
    }
    
    // Make AJAX call for roadmap
    fetch('<?php echo $CFG->wwwroot; ?>/local/learnpath/ajax.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: ajaxData
    })
    .then(response => response.json())
    .then(data => {
        if (data && (data.success || data.response)) {
            // Parse the AI response to create Khan Academy style modules
            const response = data.response || '';
            const modules = parseRoadmapSteps(response, currentStudentData);
            
            let roadmapHtml = '';
            modules.forEach((module, index) => {
                const statusClass = module.status === 'new' ? 'new' : module.status === 'completed' ? 'completed' : '';
                roadmapHtml += `
                    <div class="khan-module ${statusClass}">
                        <div class="module-header">
                            <div class="module-number">${module.weekNumber || (index + 1)}</div>
                            <div class="module-content">
                                <div class="module-week-label">Week ${module.weekNumber || (index + 1)}: 
                                    ${module.badge ? `<span class="module-badge">${module.badge}</span>` : ''}
                                </div>
                                <div class="module-title">${module.title}</div>
                                <div class="module-description">${module.description}</div>
                                
                                <div class="module-meta">
                                    <div class="module-meta-item">
                                        <span>⏱</span>
                                        <span>${module.duration}</span>
                                    </div>
                                    <div class="module-meta-item">
                                        <span>📚</span>
                                        <span>${module.lessons} lessons</span>
                                    </div>
                                </div>
                                
                                <div class="module-skills">
                                    ${module.skills.map(skill => `<span class="skill-tag">${skill}</span>`).join('')}
                                </div>
                                
                                <div class="module-actions">
                                    ${module.status === 'new' ? 
                                        `<button class="module-action-btn" onclick="startModule(${index})">Start Course</button>` :
                                        module.status === 'completed' ?
                                        `<div class="module-progress">
                                            <div class="module-progress-fill" style="width: 100%"></div>
                                        </div>
                                        <span style="color: #00af54;">✓ Completed</span>` :
                                        `<span style="color: #626569;">🔒 Complete previous module first</span>`
                                    }
                                </div>
                            </div>
                        </div>
                    </div>
                `;
            });
            
            resultsDiv.innerHTML = roadmapHtml || '<div class="roadmap-placeholder">No modules generated. Please try again.</div>';
        } else {
            resultsDiv.innerHTML = '<div class="alert alert-danger">Failed to generate roadmap: ' + (data && data.message ? data.message : 'Unknown error') + '</div>';
        }
    })
    .catch(error => {
        resultsDiv.innerHTML = '<div class="alert alert-danger">Failed to generate roadmap: ' + error.message + '</div>';
    });
}

// Helper function to parse roadmap steps from AI response
function parseRoadmapSteps(response, studentData) {
    // Analyze student scores to determine weak subjects
    const scores = studentData ? studentData.scores : {};
    const weakSubjects = [];
    const strongSubjects = [];
    
    // Categorize subjects by performance
    Object.entries(scores).forEach(([subject, score]) => {
        if (score < 60) {
            weakSubjects.push({subject, score});
        } else if (score >= 75) {
            strongSubjects.push({subject, score});
        }
    });
    
    // Sort weak subjects by score (lowest first)
    weakSubjects.sort((a, b) => a.score - b.score);
    
    // Generate dynamic modules based on actual student performance
    const modules = [];
    
    // Parse AI response with strict format: Title|Duration|Lessons|Skills
    if (response && response.includes('|')) {
        const lines = response.split('\n').filter(line => line.trim() && line.includes('|'));
        
        lines.forEach((line, index) => {
            if (index < 3) { // Only take first 3 modules
                const parts = line.trim().split('|');
                if (parts.length >= 4) {
                    const title = parts[0].trim();
                    const duration = parts[1].trim();
                    const lessons = parts[2].trim();
                    const skills = parts[3].trim().split(',').map(s => s.trim());
                    
                    modules.push({
                        title: title,
                        weekNumber: index + 1,
                        badge: index === 0 ? "START HERE" : index === 1 ? "RECOMMENDED" : "NEXT UP",
                        description: `Specific goals to improve your performance`,
                        duration: duration,
                        lessons: lessons,
                        skills: skills,
                        status: index === 0 ? "new" : "locked",
                        progress: 0
                    });
                }
            }
        });
    }
    
    // If no modules from AI or too few, generate based on student data
    if (modules.length < 3) {
        // Module templates matching Khan Academy style
        const moduleTemplates = [
            { icon: "📐", title: "Master Basic Algebra", type: "NEW" },
            { icon: "🧮", title: "Algebra Practice Problems", type: "POPULAR" },
            { icon: "📊", title: "Graphing & Functions", type: "POPULAR" },
            { icon: "🎯", title: "Understanding Function Notation", type: "" },
            { icon: "🔧", title: "Interactive Graphing Tool", type: "" },
            { icon: "💡", title: "Advanced Problem Solving", type: "NEW" },
            { icon: "🌍", title: "Real-World Math Challenges", type: "" },
            { icon: "📝", title: "Algebra Mastery Quiz", type: "" },
            { icon: "📚", title: "Solving Linear Equations Guide", type: "" }
        ];
        
        // Add modules for weak subjects first (max 3)
        weakSubjects.slice(0, 3).forEach((weak, index) => {
            if (modules.length < 5) {
                const template = moduleTemplates[index % moduleTemplates.length];
                modules.push({
                    title: template.title.replace('Algebra', weak.subject).replace('Math', weak.subject),
                    badge: index === 0 ? "START HERE" : template.type || "RECOMMENDED",
                    description: `Focus on improving your ${weak.subject} score from ${weak.score}%`,
                    duration: "2 weeks",
                    lessons: 10 + (index * 2),
                    skills: generateSkillsForModule(weak.subject, weak.subject),
                    status: index === 0 ? "new" : "locked",
                    progress: 0
                });
            }
        });
        
        // Add 1-2 enrichment modules if space (max total 5)
        if (modules.length < 5 && strongSubjects.length > 0) {
            const strong = strongSubjects[0];
            modules.push({
                title: `Advanced ${strong.subject} Problem Solving`,
                badge: "ENRICHMENT",
                description: `Challenge yourself with advanced ${strong.subject} concepts`,
                duration: "3 weeks",
                lessons: 15,
                skills: generateSkillsForModule(strong.subject, strong.subject, true),
                status: "locked",
                progress: 0
            });
        }
    }
    
    // IMPORTANT: Cap at exactly 3 modules
    const finalModules = modules.slice(0, 3);
    return finalModules.length > 0 ? finalModules : generateDefaultModules(studentData);
}

// Extract skills from the module title
function extractSkillsFromTitle(title, subject) {
    const skills = [];
    
    // Check for specific keywords in title
    if (title.includes('equation') || title.includes('algebra')) {
        skills.push('Equations', 'Algebra', 'Problem Solving');
    } else if (title.includes('essay') || title.includes('writing')) {
        skills.push('Essay Writing', 'Analysis', 'Critical Thinking');
    } else if (title.includes('lab') || title.includes('experiment')) {
        skills.push('Lab Work', 'Data Analysis', 'Scientific Method');
    } else if (title.includes('neural') || title.includes('ML') || title.includes('machine learning')) {
        skills.push('Neural Networks', 'TensorFlow', 'Deep Learning');
    } else if (title.includes('data') || title.includes('visualization')) {
        skills.push('Data Analysis', 'Visualization', 'Python');
    } else if (title.includes('physics') || title.includes('Newton')) {
        skills.push('Mechanics', 'Problem Solving', 'Physics Laws');
    } else {
        // Default skills based on subject
        return generateSkillsForModule(title, subject);
    }
    
    return skills.slice(0, 3); // Return max 3 skills
}

// Helper to generate skills based on subject
function generateSkillsForModule(title, subject, advanced = false) {
    const subjectSkills = {
        'Mathematics': advanced ? 
            ["Calculus", "Advanced Algebra", "Statistics"] : 
            ["Basic Algebra", "Geometry", "Problem Solving"],
        'Science': advanced ? 
            ["Lab Analysis", "Scientific Method", "Research"] : 
            ["Basic Concepts", "Experiments", "Observations"],
        'English': advanced ? 
            ["Critical Analysis", "Essay Writing", "Literature"] : 
            ["Grammar", "Vocabulary", "Reading Comprehension"],
        'History': advanced ? 
            ["Historical Analysis", "Source Evaluation", "Essay Writing"] : 
            ["Key Events", "Timeline", "Important Figures"],
        'Physics': advanced ? 
            ["Quantum Mechanics", "Relativity", "Advanced Problems"] : 
            ["Newton's Laws", "Energy", "Motion"],
        'Chemistry': advanced ? 
            ["Organic Chemistry", "Lab Techniques", "Reactions"] : 
            ["Elements", "Compounds", "Basic Reactions"],
        'Biology': advanced ? 
            ["Genetics", "Ecology", "Advanced Systems"] : 
            ["Cells", "Basic Systems", "Life Processes"]
    };
    
    // Find matching subject
    for (let key in subjectSkills) {
        if (subject && subject.toLowerCase().includes(key.toLowerCase())) {
            return subjectSkills[key];
        }
    }
    
    // Default skills if subject not found
    return advanced ? 
        ["Advanced Concepts", "Critical Thinking", "Problem Solving"] :
        ["Fundamentals", "Core Concepts", "Practice Problems"];
}

// Generate default modules if no data available
function generateDefaultModules(studentData) {
    return [
        {
            title: "Master Basic Math Concepts",
            weekNumber: 1,
            badge: "START HERE",
            description: "Complete foundational math problems",
            duration: "2 weeks",
            lessons: "12 lessons",
            skills: ["Algebra", "Equations", "Problem Solving"],
            status: "new",
            progress: 0
        },
        {
            title: "Build Study Skills Foundation",
            weekNumber: 2,
            badge: "RECOMMENDED",
            description: "Develop effective learning strategies",
            duration: "1 week",
            lessons: "8 lessons",
            skills: ["Note Taking", "Time Management", "Organization"],
            status: "locked",
            progress: 0
        },
        {
            title: "Complete Practice Tests",
            weekNumber: 3,
            badge: "NEXT UP",
            description: "Apply knowledge through testing",
            duration: "2 weeks",
            lessons: "15 lessons",
            skills: ["Test Taking", "Review", "Application"],
            status: "locked",
            progress: 0
        }
    ];
}

// Helper function to parse resources from AI response
function parseResources(response, studentData) {
    const resources = [];
    const icons = ["📐", "🧮", "🔬", "📝"];
    const colors = ["red", "green", "blue", "purple"];
    
    // Parse AI response with strict format: Title|Author|Description|Tags
    if (response && response.includes('|')) {
        const lines = response.split('\n').filter(line => line.trim() && line.includes('|'));
        
        lines.forEach((line, index) => {
            if (index < 4) { // Only take first 4 resources
                const parts = line.trim().split('|');
                if (parts.length >= 4) {
                    const title = parts[0].trim();
                    const author = parts[1].trim();
                    const description = parts[2].trim();
                    const tags = parts[3].trim().split(',').map(s => s.trim());
                    
                    resources.push({
                        icon: icons[index % icons.length],
                        iconColor: colors[index % colors.length],
                        title: title,
                        author: author,
                        description: description,
                        tags: tags,
                        rating: 4.5 + (index * 0.1),
                        buttonText: "Start Learning",
                        buttonColor: index % 2 === 0 ? "blue" : "orange"
                    });
                }
            }
        });
    }
    
    // If no resources from AI, provide defaults
    if (resources.length === 0) {
        return [
            {
                icon: "📐",
                iconColor: "red",
                title: "Khan Academy Math",
                author: "Khan Academy",
                description: "Complete math courses with practice problems",
                tags: ["Beginner", "Interactive"],
                rating: 4.8,
                buttonText: "Start Learning",
                buttonColor: "blue"
            },
            {
                icon: "🧮",
                iconColor: "green",
                title: "Desmos Calculator",
                author: "Desmos",
                description: "Graphing calculator for visual learning",
                tags: ["All levels", "Unlimited"],
                rating: 4.5,
                buttonText: "Start Learning",
                buttonColor: "blue"
            },
            {
                icon: "🔬",
                iconColor: "blue",
                title: "PhET Simulations",
                author: "PhET",
                description: "Interactive science simulations",
                tags: ["Interactive", "Lab work"],
                rating: 4.7,
                buttonText: "Start Learning",
                buttonColor: "orange"
            },
            {
                icon: "📝",
                iconColor: "purple",
                title: "Grammarly Writing",
                author: "Grammarly",
                description: "Improve writing and grammar skills",
                tags: ["Writing", "Self-paced"],
                rating: 4.6,
                buttonText: "Start Learning",
                buttonColor: "blue"
            }
        ];
    }
    
    return resources.slice(0, 4);
}

// Helper to find resources for a specific subject
function findResourcesForSubject(subject, templates) {
    // Check for exact match first
    for (let key in templates) {
        if (subject.toLowerCase().includes(key.toLowerCase())) {
            return templates[key];
        }
    }
    
    // Check for related subjects
    if (subject.toLowerCase().includes('physics') || 
        subject.toLowerCase().includes('chemistry') || 
        subject.toLowerCase().includes('biology')) {
        return templates['Science'];
    }
    
    if (subject.toLowerCase().includes('history') || 
        subject.toLowerCase().includes('geography')) {
        return templates['English']; // Use reading resources
    }
    
    // Default to math resources
    return templates['Mathematics'];
    
    // Try to parse AI response into resources
    try {
        if (response && (response.includes('Resource') || response.includes('Material'))) {
            const lines = response.split('\n').filter(line => line.trim());
            const resources = [];
            const icons = ["📐", "📊", "🧮", "📚"];
            const colors = ["red", "green", "blue", "purple"];
            
            lines.forEach((line, index) => {
                if (line.includes('Resource') || line.includes('Material')) {
                    resources.push({
                        icon: icons[index % icons.length],
                        iconColor: colors[index % colors.length],
                        title: line.replace(/^(Resource|Material)\s*\d+:?\s*/i, '').trim(),
                        author: "AI Recommended",
                        description: "Personalized learning resource based on your performance",
                        rating: 4.5 + (Math.random() * 0.4),
                        duration: `${1 + index} hours`,
                        difficulty: index === 0 ? "Beginner" : index === 1 ? "Intermediate" : "Advanced",
                        tags: ["AI Selected", "Personalized"],
                        recommended: index === 0
                    });
                }
            });
            
            return resources.length > 0 ? resources : defaultResources;
        }
    } catch (e) {
        console.error('Error parsing resources:', e);
    }
    
    return defaultResources;
}

function getRecommendations() {
    if (!currentStudentData) {
        alert('Please select a student first!');
        return;
    }
    
    const resultsDiv = document.getElementById('resources-list');
    const dataSource = document.getElementById('dataSource').value;
    
    // Add loading indicator
    resultsDiv.innerHTML = '<div class="loading"><div class="spinner"></div>💡 AI is generating personalized recommendations...</div>';
    
    // Create strict AI prompt for resources
    const scores = currentStudentData.scores || {};
    const weakSubjects = Object.entries(scores).filter(([s, score]) => score < 60).map(([s]) => s);
    
    const resourcesPrompt = `Student scores: ${JSON.stringify(scores)}. Generate exactly 4 study resources. Format: Title|Author|Description|Tags

Example for weak math student:
Algebra Problem Solver|Khan Academy|Step-by-step solutions for algebra equations|Beginner,Interactive
Graphing Calculator Tool|Desmos|Visual function graphing and exploration|All levels,Unlimited
Physics Lab Simulator|PhET|Virtual physics experiments and simulations|Interactive,Lab work
Essay Writing Guide|Grammarly|Grammar and writing improvement tools|Writing,Self-paced

Rules:
- Focus on weakest subjects: ${weakSubjects.join(', ')}
- Use real tool/platform names
- Keep descriptions under 80 characters
- Provide 2 relevant tags separated by comma
- Return exactly 4 lines, nothing else`;
    
    let ajaxData;
    if (dataSource === 'demo') {
        const profile = document.getElementById('studentProfile').value;
        ajaxData = 'action=get_recommendations&profile=' + encodeURIComponent(profile) + '&prompt=' + encodeURIComponent(resourcesPrompt);
    } else {
        const courseId = document.getElementById('courseSelect').value;
        const studentId = document.getElementById('studentSelect').value;
        ajaxData = 'action=get_recommendations&userid=' + studentId + '&courseid=' + courseId + '&prompt=' + encodeURIComponent(resourcesPrompt);
    }
    
    // Make AJAX call for recommendations
    fetch('<?php echo $CFG->wwwroot; ?>/local/learnpath/ajax.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: ajaxData
    })
    .then(response => response.json())
    .then(data => {
        // Remove loading indicator
        const loadingDiv = document.querySelector('.loading');
        if (loadingDiv) loadingDiv.remove();
        
        if (data && (data.success || data.response)) {
            // Parse the AI response to create Khan Academy style resource cards
            const response = data.response || '';
            const resources = parseResources(response, currentStudentData);
            
            let resourcesHtml = '';
            resources.forEach((resource, index) => {
                const ratingStars = resource.rating ? '⭐ ' + resource.rating : '';
                const buttonClass = resource.buttonColor === 'orange' ? 'start-learning-btn-orange' : 'start-learning-btn';
                
                resourcesHtml += `
                    <div class="khan-resource-card">
                        <div class="resource-header">
                            <div class="resource-icon ${resource.iconColor}">
                                ${resource.icon}
                            </div>
                            <div class="resource-info">
                                <h3 class="resource-title">${resource.title}</h3>
                                <p class="resource-author">${resource.author}</p>
                            </div>
                            <div class="resource-rating-badge">
                                ${ratingStars} ${resource.rating || '4.' + (5 + index)}
                            </div>
                        </div>
                        
                        <p class="resource-description">${resource.description}</p>
                        
                        <div class="resource-footer">
                            <div class="resource-tags">
                                ${resource.tags.map(tag => `<span class="resource-tag">${tag}</span>`).join('')}
                            </div>
                            <button class="${buttonClass}" onclick="startResource('${resource.title}')">
                                ${resource.buttonText || 'Start Learning'} →
                            </button>
                        </div>
                    </div>
                `;
            });
            
            resultsDiv.innerHTML = resourcesHtml || '<div class="resources-placeholder">No resources generated. Please try again.</div>';
        } else {
            resultsDiv.innerHTML += '<div class="alert alert-danger">Failed to get recommendations: ' + (data && data.message ? data.message : 'Unknown error') + '</div>';
        }
    })
    .catch(error => {
        const loadingDiv = document.querySelector('.loading');
        if (loadingDiv) loadingDiv.remove();
        resultsDiv.innerHTML += '<div class="alert alert-danger">Failed to get recommendations: ' + error.message + '</div>';
    });
}

function formatAIResponse(response) {
    // Enhanced formatting for AI responses
    let formatted = response;
    
    // Convert markdown-style headers
    formatted = formatted.replace(/^### (.*$)/gm, '<h4>$1</h4>');
    formatted = formatted.replace(/^## (.*$)/gm, '<h3>$1</h3>');
    formatted = formatted.replace(/^# (.*$)/gm, '<h3>$1</h3>');
    
    // Convert numbered lists
    formatted = formatted.replace(/^\d+\.\s(.*)$/gm, '<div class="action-item">$1</div>');
    
    // Convert bullet points
    formatted = formatted.replace(/^[-•]\s(.*)$/gm, '<li>$1</li>');
    
    // Wrap consecutive list items in ul tags
    formatted = formatted.replace(/(<li>.*<\/li>\s*)+/gs, '<ul>$&</ul>');
    
    // Highlight strengths and weaknesses
    formatted = formatted.replace(/(STRENGTH[S]?:.*?)(?=\n\n|\n[A-Z]|$)/gs, '<div class="strength-highlight">$1</div>');
    formatted = formatted.replace(/(CHALLENGE[S]?:.*?)(?=\n\n|\n[A-Z]|$)/gs, '<div class="weakness-highlight">$1</div>');
    formatted = formatted.replace(/(WEAK.*?AREA[S]?:.*?)(?=\n\n|\n[A-Z]|$)/gs, '<div class="weakness-highlight">$1</div>');
    
    // Highlight action items
    formatted = formatted.replace(/(IMMEDIATE ACTION[S]?:.*?)(?=\n\n|\n[A-Z]|$)/gs, '<div class="recommendation-card">$1</div>');
    formatted = formatted.replace(/(RECOMMENDATION[S]?:.*?)(?=\n\n|\n[A-Z]|$)/gs, '<div class="recommendation-card">$1</div>');
    
    // Convert line breaks to HTML
    formatted = formatted.replace(/\n\n/g, '</p><p>');
    formatted = formatted.replace(/\n/g, '<br>');
    formatted = '<p>' + formatted + '</p>';
    
    // Clean up empty paragraphs
    formatted = formatted.replace(/<p><\/p>/g, '');
    formatted = formatted.replace(/<p><br><\/p>/g, '');
    
    return formatted;
}
</script>

<?php
echo $OUTPUT->footer();
?>
