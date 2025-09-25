<?php
/**
 * LearnPath Navigator - Main Interface (Optimized)
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

<link rel="stylesheet" href="styles.css?v=<?php echo time(); ?>">

<div id="learnpath-app" class="learnpath-container">
    <!-- Header -->
    <div class="learnpath-header">
        <div class="header-content">
            <div class="header-text centered">
                <h1>🧭 LearnPath Navigator</h1>
                <p class="tagline">Your AI-powered learning companion</p>
                <p class="subtitle">Personalized study paths • Real-time analysis • Smart recommendations</p>
            </div>
        </div>
        <div class="header-actions">
            <label class="wide-toggle">
                <input type="checkbox" id="wideModeToggle" onchange="applyWideMode(this.checked)">
                <span>Wide mode</span>
            </label>
        </div>
    </div>
    
    <!-- Main Container -->
    <div class="main-container">
        <!-- Khan Academy Layout -->
        <div class="khan-layout">
            <!-- Left Sidebar -->
            <div class="left-sidebar">
                <div class="sidebar-header">
                    <h3>📊 Data Source</h3>
                    <select id="dataSource" class="sidebar-dropdown" onchange="switchDataSource()">
                        <option value="demo">Demo Data (Instant)</option>
                        <option value="live">Live Moodle Data</option>
                    </select>
                </div>

                <!-- Demo Data Section -->
                <div id="demo-section" class="sidebar-section">
                    <h4>👤 Select Student Profile</h4>
                    <select id="studentProfile" class="sidebar-dropdown" onchange="loadDemoStudent()">
                        <option value="">Choose a student...</option>
                        <option value="struggling_math">Alex Chen - Struggling with Math</option>
                        <option value="balanced_performer">Sarah Johnson - Balanced Performer</option>
                        <option value="advanced_science">Marcus Rodriguez - Advanced in Science</option>
                        <option value="english_focused">Emma Thompson - English Focused</option>
                        <option value="stem_enthusiast">David Kim - STEM Enthusiast</option>
                    </select>
                </div>

                <!-- Live Data Section -->
                <div id="live-section" class="sidebar-section" style="display: none;">
                    <h4>📚 Select Course</h4>
                    <select id="courseSelect" class="sidebar-dropdown" onchange="loadStudents()">
                        <option value="">Select a course...</option>
                    </select>
                    
                    <h4>👥 Select Student</h4>
                    <select id="studentSelect" class="sidebar-dropdown" onchange="loadStudentData()">
                        <option value="">First select a course</option>
                    </select>
                </div>

                <!-- Student List -->
                <div class="student-list" id="student-list">
                    <!-- Students will be populated here -->
                </div>
            </div>

            <!-- Main Content Area -->
            <div class="main-content">
                <!-- Navigation Tabs -->
                <div class="nav-tabs">
                    <div class="nav-card active" onclick="switchTab('dashboard')">
                        <div class="nav-icon">📈</div>
                        <div class="nav-text">
                            <h3>Dashboard</h3>
                            <p>Performance overview</p>
                        </div>
                    </div>
                    <div class="nav-card" onclick="switchTab('roadmap')">
                        <div class="nav-icon">🗺️</div>
                        <div class="nav-text">
                            <h3>Learning Roadmap</h3>
                            <p>AI-generated study plan</p>
                        </div>
                    </div>
                    <div class="nav-card" onclick="switchTab('resources')">
                        <div class="nav-icon">📚</div>
                        <div class="nav-text">
                            <h3>Study Resources</h3>
                            <p>Curated materials for your level</p>
                        </div>
                    </div>
                    <div class="nav-card" onclick="switchTab('chatbot')">
                        <div class="nav-icon">🤖</div>
                        <div class="nav-text">
                            <h3>AI Study Assistant</h3>
                            <p>Get instant help and explanations</p>
                        </div>
                    </div>
                </div>

                <!-- Content Areas -->
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
                            <div class="streak-number" id="streak-days">0</div>
                            <div class="streak-label">Days</div>
                        </div>
                        <div class="streak-message" id="streak-message">Start your learning journey!</div>
                    </div>
                </div>

                <!-- Roadmap Content -->
                <div id="roadmap-content" class="content-area" style="display: none;">
                    <div class="roadmap-header">
                        <h2>🗺️ Your Personalized Learning Roadmap</h2>
                        <p>AI-generated study plan based on your performance</p>
                        <button id="generate-roadmap-btn" class="generate-btn" onclick="generateRoadmap()">
                            <span class="btn-icon">🤖</span>
                            Generate AI Roadmap
                        </button>
                    </div>
                    <div id="roadmap-results" class="roadmap-results">
                        <div class="roadmap-placeholder">
                            <div class="placeholder-icon">🎯</div>
                            <h3>Ready to create your learning path?</h3>
                            <p>Click "Generate AI Roadmap" to get a personalized study plan based on your performance data.</p>
                        </div>
                    </div>
                </div>

                <!-- Resources Content -->
                <div id="resources-content" class="content-area" style="display: none;">
                    <div class="resources-header">
                        <h2>📚 Recommended Study Resources</h2>
                        <p>Curated materials matched to your learning needs</p>
                        <button class="generate-btn" onclick="getRecommendations()">
                            <span class="btn-icon">💡</span>
                            Get AI Recommendations
                        </button>
                    </div>
                    <div id="resources-list" class="resources-list">
                        <div class="resources-placeholder">
                            <div class="placeholder-icon">📖</div>
                            <h3>Discover your perfect study materials</h3>
                            <p>Get personalized resource recommendations based on your subjects and performance level.</p>
                        </div>
                    </div>
                </div>

                <!-- Chatbot Content -->
                <div id="chatbot-content" class="content-area" style="display: none;">
                    <div class="chatbot-header">
                        <h2>🤖 AI Study Assistant</h2>
                        <p>Ask me anything about your courses, get explanations, or study guidance!</p>
                    </div>
                    
                    <div class="chatbot-container">
                        <div id="chat-messages" class="chat-messages">
                            <div class="chat-message bot-message">
                                <div class="message-avatar">🤖</div>
                                <div class="message-content">
                                    <div class="message-text">
                                        Hi! I'm your AI Study Assistant. I can help you with:
                                        <ul>
                                            <li>📚 Explaining difficult concepts</li>
                                            <li>📝 Study tips and strategies</li>
                                            <li>❓ Answering course questions</li>
                                            <li>🎯 Creating study plans</li>
                                        </ul>
                                        What would you like to know?
                                    </div>
                                    <div class="message-time">Just now</div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="chat-input-container">
                            <div class="quick-questions">
                                <button class="quick-btn" onclick="sendQuickMessage('Explain my weak subjects')">
                                    📊 Explain my weak subjects
                                </button>
                                <button class="quick-btn" onclick="sendQuickMessage('How can I improve my grades?')">
                                    📈 How can I improve?
                                </button>
                                <button class="quick-btn" onclick="createStudyPlan()">
                                    📅 Create study plan
                                </button>
                            </div>
                            
                            <div class="chat-input-area">
                                <input type="text" id="chat-input" placeholder="Ask me anything about your studies..." 
                                       onkeypress="handleChatKeyPress(event)">
                                <button id="send-btn" onclick="sendChatMessage()">
                                    <span class="send-icon">📤</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="scripts.js?v=<?php echo time(); ?>"></script>

<?php
echo $OUTPUT->footer();
?>
