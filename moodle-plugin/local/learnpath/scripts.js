// LearnPath Navigator - Optimized JavaScript
// Team Delimiters - NatWest Hack4aCause

let currentStudentData = null;
let currentRoadmapModules = [];

// Demo student data
const demoStudents = {
    struggling_math: {
        name: "Alex Chen",
        avatar: "AC",
        grade: "Grade 9",
        overall: 68,
        scores: {
            "Mathematics": 45,
            "Science": 78,
            "English": 72,
            "History": 65,
            "Physics": 58
        },
        streak: 3,
        message: "Keep pushing forward! Math improvement is your priority."
    },
    balanced_performer: {
        name: "Sarah Johnson", 
        avatar: "SJ",
        grade: "Grade 10",
        overall: 78,
        scores: {
            "Mathematics": 75,
            "Science": 82,
            "English": 79,
            "History": 76,
            "Chemistry": 73
        },
        streak: 7,
        message: "Great balance across subjects! Keep it up."
    },
    advanced_science: {
        name: "Marcus Rodriguez",
        avatar: "MR", 
        grade: "Grade 11",
        overall: 85,
        scores: {
            "Mathematics": 88,
            "Physics": 92,
            "Chemistry": 89,
            "Biology": 87,
            "English": 68
        },
        streak: 12,
        message: "Science star! Consider strengthening English skills."
    },
    english_focused: {
        name: "Emma Thompson",
        avatar: "ET",
        grade: "Grade 10", 
        overall: 76,
        scores: {
            "English": 91,
            "History": 85,
            "Literature": 88,
            "Mathematics": 58,
            "Science": 62
        },
        streak: 5,
        message: "Excellent in humanities! STEM subjects need attention."
    },
    stem_enthusiast: {
        name: "David Kim",
        avatar: "DK",
        grade: "Grade 12",
        overall: 89,
        scores: {
            "Mathematics": 94,
            "Physics": 91,
            "Computer Science": 96,
            "English": 72
        },
        streak: 15,
        message: "STEM excellence! Well-rounded performance overall."
    }
};

// Wide Mode toggle
function applyWideMode(enabled) {
    const root = document.getElementById('learnpath-app');
    if (!root) return;
    if (enabled) {
        root.classList.add('wide-mode');
    } else {
        root.classList.remove('wide-mode');
    }
    try { localStorage.setItem('learnpath_wide_mode', String(enabled)); } catch (e) {}
}


// Initialize page
document.addEventListener('DOMContentLoaded', function() {
    // Set to live data mode by default for testing
    const dataSourceSelect = document.getElementById('dataSource');
    if (dataSourceSelect) {
        dataSourceSelect.value = 'live';
        switchDataSource(); // This will show live section and hide demo section
        
        // Load courses after switching to live mode
        setTimeout(() => {
            loadCourses();
        }, 100);
    }

    // Restore Wide Mode from previous session (optional)
    const widePref = localStorage.getItem('learnpath_wide_mode') === 'true';
    const toggle = document.getElementById('wideModeToggle');
    if (toggle) {
        toggle.checked = widePref;
        applyWideMode(widePref);
    }
});

// Tab switching
function switchTab(tab) {
    console.log('Switching to tab:', tab);
    
    // Update nav cards
    document.querySelectorAll('.nav-card').forEach(card => card.classList.remove('active'));
    
    // Find and activate the clicked nav card
    const navCards = document.querySelectorAll('.nav-card');
    if (tab === 'dashboard') navCards[0]?.classList.add('active');
    else if (tab === 'roadmap') navCards[1]?.classList.add('active');
    else if (tab === 'resources') navCards[2]?.classList.add('active');
    else if (tab === 'chatbot') navCards[3]?.classList.add('active');
    
    // Hide all content areas
    document.querySelectorAll('.content-area').forEach(area => area.style.display = 'none');
    
    // Show selected content area
    const contentArea = document.getElementById(tab + '-content');
    if (contentArea) {
        contentArea.style.display = 'block';
    } else {
        console.error('Content area not found:', tab + '-content');
    }

    // Auto-load saved roadmap when entering roadmap tab
    if (tab === 'roadmap') {
        tryLoadSavedRoadmap();
    }
}

// Data source switching
function switchDataSource() {
    const dataSource = document.getElementById('dataSource').value;
    const demoSection = document.getElementById('demo-section');
    const liveSection = document.getElementById('live-section');
    
    if (dataSource === 'demo') {
        demoSection.style.display = 'block';
        liveSection.style.display = 'none';
        loadDemoStudent();
    } else {
        demoSection.style.display = 'none';
        liveSection.style.display = 'block';
        loadCourses();
    }
}

// Load demo student
function loadDemoStudent() {
    // Clear roadmap button label and display when switching users
    setGenerateBtnLabel(false);
    clearRoadmapDisplay();
    const profileSelect = document.getElementById('studentProfile');
    if (!profileSelect) {
        console.error('Student profile select not found');
        return;
    }
    
    const selectedProfile = profileSelect.value;
    console.log('Loading demo student:', selectedProfile);
    
    if (selectedProfile && demoStudents[selectedProfile]) {
        currentStudentData = demoStudents[selectedProfile];
        console.log('Student data loaded:', currentStudentData);
        updateDashboard();
    } else {
        console.log('No student data found for:', selectedProfile);
        // Clear the dashboard if no valid selection
        currentStudentData = null;
    }
}

// Load courses (for live data)
function loadCourses() {
    const courseSelect = document.getElementById('courseSelect');
    if (!courseSelect) {
        console.error('Course select element not found');
        return;
    }
    
    courseSelect.innerHTML = '<option value="">Loading courses...</option>';
    
    console.log('Loading courses...');
    
    fetch('ajax.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'action=get_courses'
    })
    .then(response => response.json())
    .then(data => {
        console.log('Courses response:', data);
        courseSelect.innerHTML = '<option value="">Select a course...</option>';
        if (data.success && data.courses) {
            data.courses.forEach(course => {
                courseSelect.innerHTML += `<option value="${course.id}">${course.fullname}</option>`;
            });
            console.log('Loaded', data.courses.length, 'courses');
        } else {
            console.error('Failed to load courses:', data.message);
        }
    })
    .catch(error => {
        console.error('Error loading courses:', error);
        courseSelect.innerHTML = '<option value="">Error loading courses</option>';
    });
}

// Load students for selected course
function loadStudents() {
    const courseId = document.getElementById('courseSelect').value;
    const studentSelect = document.getElementById('studentSelect');
    
    if (!courseId) {
        studentSelect.innerHTML = '<option value="">First select a course</option>';
        return;
    }
    
    studentSelect.innerHTML = '<option value="">Loading students...</option>';
    
    fetch('ajax.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: `action=get_students&courseid=${courseId}`
    })
    .then(response => response.json())
    .then(data => {
        studentSelect.innerHTML = '<option value="">Select a student...</option>';
        if (data.success && data.students) {
            data.students.forEach(student => {
                studentSelect.innerHTML += `<option value="${student.id}">${student.firstname} ${student.lastname}</option>`;
            });
        }
    })
    .catch(error => {
        studentSelect.innerHTML = '<option value="">Error loading students</option>';
    });
}

// Load student data
function loadStudentData() {
    // Clear roadmap button label and display when switching users
    setGenerateBtnLabel(false);
    clearRoadmapDisplay();
    const courseId = document.getElementById('courseSelect').value;
    const studentId = document.getElementById('studentSelect').value;
    
    console.log('Loading student data:', { courseId, studentId });
    console.log('Course ID:', courseId, 'Student ID:', studentId); // More detailed logging
    
    if (!courseId || !studentId) {
        console.log('Missing courseId or studentId');
        return;
    }
    
    fetch('ajax.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: `action=get_real_student&courseid=${courseId}&userid=${studentId}`
    })
    .then(response => response.json())
    .then(data => {
        console.log('Student data response:', data);
        if (data.success) {
            currentStudentData = data.data; // Note: it's data.data, not data.student
            console.log('Loaded student data:', currentStudentData);
            updateDashboard();
        } else {
            console.error('Failed to load student data:', data.message);
        }
    })
    .catch(error => {
        console.error('Error loading student data:', error);
    });
}

// Update dashboard with student data
function updateDashboard() {
    console.log('Updating dashboard with:', currentStudentData); // Debug log
    
    if (!currentStudentData) {
        console.log('No current student data available'); // Debug log
        return;
    }
    
    try {
        // Update welcome section
        const avatar = document.getElementById('student-avatar');
        const title = document.getElementById('welcome-title');
        const subtitle = document.getElementById('welcome-subtitle');
        
        if (avatar) avatar.textContent = currentStudentData.avatar || currentStudentData.name.substring(0, 2).toUpperCase();
        if (title) title.textContent = `Welcome back, ${currentStudentData.name}!`;
        if (subtitle) subtitle.textContent = `${currentStudentData.grade || 'Student'} • ${currentStudentData.message || 'Keep learning!'}`;
        
        // Update overall score
        const overallScore = document.getElementById('overall-score');
        const scoreProgress = document.getElementById('score-progress');
        
        if (overallScore) overallScore.textContent = currentStudentData.overall + '%';
        if (scoreProgress) scoreProgress.style.width = currentStudentData.overall + '%';
        
        // Update subjects
        updateSubjects();
        
        // Update streak
        const streakDays = document.getElementById('streak-days');
        const streakMessage = document.getElementById('streak-message');
        
        if (streakDays) streakDays.textContent = currentStudentData.streak || 0;
        if (streakMessage) streakMessage.textContent = getStreakMessage(currentStudentData.streak || 0);
        
        console.log('Dashboard updated successfully'); // Debug log
    } catch (error) {
        console.error('Error updating dashboard:', error); // Debug log
    }
}

// Update subjects display
function updateSubjects() {
    const subjectsList = document.getElementById('subjects-list');
    const subjects = currentStudentData.scores || {};
    
    console.log('Updating subjects:', subjects); // Debug log
    
    if (!subjectsList) {
        console.error('subjects-list element not found'); // Debug log
        return;
    }
    
    subjectsList.innerHTML = '';
    
    Object.entries(subjects).forEach(([subject, score]) => {
        const color = getScoreColor(score);
        const icon = getSubjectIcon(subject);
        
        const subjectHtml = `
            <div class="subject-item">
                <div class="subject-icon">${icon}</div>
                <div class="subject-info">
                    <div class="subject-name">${subject}</div>
                    <div class="subject-progress">
                        <div class="subject-progress-fill" style="width: ${score}%; background: ${color};"></div>
                    </div>
                </div>
                <div class="subject-score" style="color: ${color};">${score}%</div>
            </div>
        `;
        subjectsList.innerHTML += subjectHtml;
    });
}

// Helper functions
function getScoreColor(score) {
    if (score >= 80) return '#10b981';
    if (score >= 70) return '#f59e0b';
    if (score >= 60) return '#f97316';
    return '#ef4444';
}

function getSubjectIcon(subject) {
    const icons = {
        'Mathematics': '📐',
        'Math': '📐',
        'Science': '🔬',
        'Physics': '⚛️',
        'Chemistry': '⚗️',
        'Biology': '🧬',
        'English': '📝',
        'Literature': '📚',
        'History': '🏛️',
        'Computer Science': '💻',
        'Art': '🎨'
    };
    return icons[subject] || '📖';
}

function getStreakMessage(days) {
    if (days === 0) return 'Start your learning journey!';
    if (days < 3) return 'Great start! Keep going!';
    if (days < 7) return 'Building momentum!';
    if (days < 14) return 'Fantastic streak!';
    return 'You\'re on fire! 🔥';
}

// Generate AI roadmap
function generateRoadmap() {
    if (!currentStudentData) {
        alert('Please select a student first!');
        return;
    }
    
    const resultsDiv = document.getElementById('roadmap-results');
    // If a saved roadmap exists for this student/course and grades didn't change, show it
    const savedObj = loadRoadmapObject();
    if (savedObj && Array.isArray(savedObj.modules) && savedObj.modules.length) {
        const currentHash = getScoresHash(currentStudentData);
        const metaOk = !savedObj.meta || (
            savedObj.meta.scoresHash === currentHash &&
            (savedObj.meta.model || 'llama3-8b') === 'llama3-8b' &&
            (savedObj.meta.promptVersion || 1) === 1
        );
        if (metaOk) {
            displayRoadmapModules(savedObj.modules);
            setGenerateBtnLabel(true);
            return;
        }
    }
    resultsDiv.innerHTML = '<div class="loading"><div class="spinner"></div>🤖 AI is analyzing your performance and generating a personalized roadmap...</div>';
    
    // Create AI prompt for roadmap
    const scores = currentStudentData.scores || {};
    const weakSubjects = Object.entries(scores).filter(([s, score]) => score < 60).map(([s]) => s);
    
    console.log('Current student data:', currentStudentData); // Debug log
    console.log('Student scores:', scores); // Debug log
    console.log('Weak subjects:', weakSubjects); // Debug log
    
    // Get course name for context
    const courseSelect = document.getElementById('courseSelect');
    const courseName = courseSelect ? courseSelect.options[courseSelect.selectedIndex]?.text || 'Unknown Course' : 'Demo Course';
    
    console.log('Course name:', courseName); // Debug log
    
    const roadmapPrompt = `Generate exactly 3 learning modules for ${currentStudentData.name || 'student'} in "${courseName}". Return ONLY 3 lines in this exact format:
Title|Duration|Lessons|Skills

Example:
Master Quadratic Equations|2 weeks|12 lessons|Algebra,Factoring,Graphing
Complete Physics Problems|3 weeks|15 lessons|Mechanics,Forces,Motion
Improve Essay Writing|1 week|8 lessons|Writing,Analysis,Grammar

Student: ${currentStudentData.name || 'Student'}
Course: ${courseName}
Current performance: ${JSON.stringify(scores)}
${weakSubjects.length > 0 ? 'Weak areas to focus on: ' + weakSubjects.join(', ') : 'Focus on foundational skills'}

Create 3 specific learning modules relevant to ${courseName}. Return exactly 3 lines with pipe separators, nothing else.`;
    
    const dataSource = document.getElementById('dataSource').value;
    let ajaxData;
    
    if (dataSource === 'demo') {
        const profile = document.getElementById('studentProfile').value;
        ajaxData = 'action=generate_roadmap&profile=' + encodeURIComponent(profile) + '&prompt=' + encodeURIComponent(roadmapPrompt);
    } else {
        const courseId = document.getElementById('courseSelect').value;
        const studentId = document.getElementById('studentSelect').value;
        ajaxData = 'action=generate_real_roadmap&userid=' + studentId + '&courseid=' + courseId + '&prompt=' + encodeURIComponent(roadmapPrompt);
    }
    
    fetch('ajax.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: ajaxData
    })
    .then(response => response.json())
    .then(data => {
        const loadingDiv = document.querySelector('.loading');
        if (loadingDiv) loadingDiv.remove();
        
        if (data.success) {
            const modules = parseRoadmapModules(data.roadmap, currentStudentData);
            displayRoadmapModules(modules);
        } else {
            resultsDiv.innerHTML += '<div class="alert alert-danger">Failed to generate roadmap: ' + (data.message || 'Unknown error') + '</div>';
        }
    })
    .catch(error => {
        const loadingDiv = document.querySelector('.loading');
        if (loadingDiv) loadingDiv.remove();
        resultsDiv.innerHTML += '<div class="alert alert-danger">Failed to generate roadmap: ' + error.message + '</div>';
    });
}

// Parse roadmap modules from AI response
function parseRoadmapModules(response, studentData) {
    console.log('Parsing AI response:', response); // Debug log
    
    const modules = [];
    const icons = ["🎯", "📚", "🚀"];
    const colors = ["blue", "green", "purple"];
    
    if (response && response.includes('|')) {
        console.log('Response contains pipes - parsing...'); // Debug log
        const lines = response.split('\n').filter(line => line.trim() && line.includes('|'));
        console.log('Pipe lines found:', lines); // Debug log
        
        // Filter out header lines that contain "Title|Duration|Lessons|Skills"
        const contentLines = lines.filter(line => 
            !line.toLowerCase().includes('title|duration|lessons|skills') &&
            !line.toLowerCase().includes('example') &&
            line.split('|').length >= 4
        );
        console.log('Content lines after filtering:', contentLines); // Debug log
        
        contentLines.forEach((line, index) => {
            if (index < 3) {
                const parts = line.trim().split('|');
                console.log(`Line ${index}:`, line); // Debug each line
                console.log(`Parts ${index}:`, parts); // Debug each part
                
                if (parts.length >= 4) {
                    const module = {
                        icon: icons[index],
                        color: colors[index],
                        title: parts[0].trim(),
                        duration: parts[1].trim(),
                        lessons: parts[2].trim(),
                        skills: parts[3].trim().split(',').map(s => s.trim()),
                        status: index === 0 ? "current" : "locked",
                        progress: index === 0 ? 0 : 0
                    };
                    console.log(`Module ${index}:`, module); // Debug final module
                    modules.push(module);
                } else {
                    console.log(`Line ${index} has insufficient parts:`, parts.length);
                }
            }
        });
    } else {
        console.log('Response does NOT contain pipes - using fallback'); // Debug log
        console.log('Response preview:', response ? response.substring(0, 200) + '...' : 'null/empty');
    }
    
    // Fallback modules if AI parsing fails
    if (modules.length === 0) {
        return [
            {
                icon: "🎯",
                color: "blue", 
                title: "Foundation Building",
                duration: "2 weeks",
                lessons: "12 lessons",
                skills: ["Basic Concepts", "Problem Solving"],
                status: "current",
                progress: 0
            },
            {
                icon: "📚",
                color: "green",
                title: "Skill Development", 
                duration: "3 weeks",
                lessons: "18 lessons",
                skills: ["Advanced Topics", "Practice"],
                status: "locked",
                progress: 0
            },
            {
                icon: "🚀",
                color: "purple",
                title: "Mastery & Assessment",
                duration: "2 weeks", 
                lessons: "15 lessons",
                skills: ["Test Taking", "Review"],
                status: "locked",
                progress: 0
            }
        ];
    }
    
    return modules.slice(0, 3);
}

// Helpers for roadmap UI extras
function computeDifficulty(module) {
    // Simple heuristic based on lessons count or duration words
    let lessons = parseInt(String(module.lessons || '').replace(/[^0-9]/g, ''), 10);
    if (isNaN(lessons)) lessons = 0;
    if (lessons <= 10) return { label: 'easy', color: 'green' };
    if (lessons <= 16) return { label: 'moderate', color: 'orange' };
    return { label: 'challenging', color: 'purple' };
}

function getModuleCompletionKey(title) {
    const dataSource = document.getElementById('dataSource').value;
    let key = 'lp_complete:';
    if (dataSource === 'demo') {
        const profile = document.getElementById('studentProfile').value;
        key += `demo:${profile}:${title}`;
    } else {
        const courseId = document.getElementById('courseSelect').value;
        const studentId = document.getElementById('studentSelect').value;
        key += `live:${courseId}:${studentId}:${title}`;
    }
    return key;
}

function isModuleCompleted(title) {
    return localStorage.getItem(getModuleCompletionKey(title)) === 'true';
}

function markModuleComplete(button, title) {
    const key = getModuleCompletionKey(title);
    const nowCompleted = !(localStorage.getItem(key) === 'true');
    localStorage.setItem(key, String(nowCompleted));
    const card = button.closest('.module-card');
    if (!card) return;
    const index = parseInt(card.getAttribute('data-index'), 10);
    const badge = card.querySelector('.status-badge');
    const fill = card.querySelector('.progress-fill');
    const text = card.querySelector('.progress-text');
    if (nowCompleted) {
        card.classList.add('completed');
        if (badge) badge.outerHTML = '<span class="status-badge completed">✓ Done</span>';
        if (fill) fill.style.width = '100%';
        if (text) text.textContent = '100% Complete';
        button.classList.remove('start-btn');
        button.classList.add('review-btn');
        button.textContent = '✓ Completed';
        // Update in-memory and persist
        if (currentRoadmapModules[index]) {
            currentRoadmapModules[index].completed = true;
        }
        saveRoadmapObject({
            meta: { scoresHash: getScoresHash(currentStudentData), model: 'llama3-8b', promptVersion: 1 },
            modules: currentRoadmapModules
        });
        // Unlock next module if present
        unlockNextModule(card, index);
    } else {
        card.classList.remove('completed');
        if (badge) badge.outerHTML = '<span class="status-badge current">Current</span>';
        if (fill) fill.style.width = '0%';
        if (text) text.textContent = '0% Complete';
        button.classList.add('start-btn');
        button.classList.remove('review-btn');
        button.textContent = 'Mark Complete';
        // Update in-memory and persist
        if (currentRoadmapModules[index]) {
            currentRoadmapModules[index].completed = false;
        }
        // Re-lock following modules to enforce sequence
        relockFollowingModules(card, index);
        saveRoadmapObject({
            meta: { scoresHash: getScoresHash(currentStudentData), model: 'llama3-8b', promptVersion: 1 },
            modules: currentRoadmapModules
        });
    }
}

function getModuleResources(module) {
    // Build 4 simple resource links derived from module title/skills
    const skills = module.skills || [];
    const q1 = encodeURIComponent(module.title);
    const q2 = encodeURIComponent((skills[0] || module.title) + ' tutorial');
    const q3 = encodeURIComponent((skills[1] || module.title) + ' practice');
    const q4 = encodeURIComponent((skills[2] || module.title) + ' course');
    return [
        { icon: '▶️', title: `${module.title} Explained`, platform: 'YouTube', url: `https://youtube.com/results?search_query=${q1}` },
        { icon: '📘', title: `Guide: ${skills[0] || 'Overview'}`, platform: 'Coursera', url: `https://coursera.org/search?query=${q2}` },
        { icon: '🧩', title: `${skills[1] || 'Practice'} Problems`, platform: 'YouTube', url: `https://youtube.com/results?search_query=${q3}` },
        { icon: '📝', title: `${skills[2] || 'Mastery'} Quiz`, platform: 'Open Courseware', url: `https://ocw.mit.edu/search/?q=${q4}` }
    ];
}

// Display roadmap modules with resources and completion control
function displayRoadmapModules(modules) {
    const resultsDiv = document.getElementById('roadmap-results');
    currentRoadmapModules = Array.isArray(modules) ? JSON.parse(JSON.stringify(modules)) : [];
    let modulesHtml = '<div class="roadmap-modules">';
    
    modules.forEach((module, index) => {
        const skillsHtml = module.skills.map(skill => `<span class="skill-tag">${skill}</span>`).join('');
        const diff = computeDifficulty(module);
        const completed = (typeof module.completed === 'boolean') ? module.completed : isModuleCompleted(module.title);
        const resources = getModuleResources(module);
        
        // Determine if this module should be unlocked based on previous completions
        let status = 'locked';
        if (index === 0) {
            status = completed ? 'completed' : 'current';
        } else {
            // Check if all previous modules are completed
            let allPreviousCompleted = true;
            for (let i = 0; i < index; i++) {
                const prevCompleted = (typeof modules[i].completed === 'boolean') ? modules[i].completed : isModuleCompleted(modules[i].title);
                if (!prevCompleted) {
                    allPreviousCompleted = false;
                    break;
                }
            }
            if (completed) {
                status = 'completed';
            } else if (allPreviousCompleted) {
                status = 'current';
            } else {
                status = 'locked';
            }
        }
        
        const statusBadge = (status === 'completed') ? '<span class="status-badge completed">✓ Done</span>' : (status === 'locked' ? '<span class="status-badge locked">🔒 Locked</span>' : '<span class="status-badge current">Current</span>');
        const progress = completed ? 100 : (module.progress || 0);
        const actionBtn = (status === 'locked')
            ? '<button class="locked-btn" disabled>Complete previous module first</button>'
            : `<button class="${completed ? 'review-btn' : 'start-btn'} complete-btn" onclick="markModuleComplete(this, '${module.title.replace(/'/g, "\'")}')">${completed ? '✓ Completed' : 'Mark Complete'}</button>`;

        modulesHtml += `
            <div class="module-card ${status} ${completed ? 'completed' : ''}" data-color="${module.color}" data-title="${module.title.replace(/"/g, '&quot;')}" data-index="${index}">
                <div class="module-topbar">
                    <div class="topbar-left">
                        <h3 class="module-title">${module.title}</h3>
                        <span class="difficulty-badge ${diff.color}">${diff.label}</span>
                    </div>
                    <div class="topbar-right">
                        ${actionBtn}
                    </div>
                </div>

                <div class="module-header">
                    <div class="module-icon">${module.icon}</div>
                    <div class="module-info">
                        <div class="module-meta">
                            <span class="duration">⏱️ ${module.duration}</span>
                            <span class="lessons">📖 ${module.lessons}</span>
                        </div>
                        <div class="module-skills">
                            <div class="skills-label">Skills you'll learn:</div>
                            <div class="skills-list">${skillsHtml}</div>
                        </div>
                    </div>
                    <div class="module-status">${statusBadge}</div>
                </div>

                <div class="module-progress">
                    <div class="progress-bar">
                        <div class="progress-fill" style="width: ${progress}%"></div>
                    </div>
                    <span class="progress-text">${progress}% Complete</span>
                </div>

                <div class="module-resources">
                    <div class="resources-title">Study Materials:</div>
                    <div class="resources-grid-inline">
                        ${resources.map(r => `
                            <a class="resource-item-inline" href="${r.url}" target="_blank" rel="noopener">
                                <span class="res-icn">${r.icon}</span>
                                <span class="res-title">${r.title}</span>
                                <span class="res-src">${r.platform}</span>
                            </a>
                        `).join('')}
                    </div>
                </div>
            </div>
        `;
    });
    modulesHtml += '</div>';
    resultsDiv.innerHTML = modulesHtml;
    // Persist after rendering
    saveRoadmapObject({
        meta: {
            courseName: (document.getElementById('courseSelect') ? (document.getElementById('courseSelect').options[document.getElementById('courseSelect').selectedIndex]?.text || 'Demo Course') : 'Demo Course'),
            studentName: (currentStudentData && currentStudentData.name) ? currentStudentData.name : 'Student',
            scoresHash: getScoresHash(currentStudentData),
            model: 'llama3-8b',
            promptVersion: 1
        },
        modules: currentRoadmapModules
    });
    setGenerateBtnLabel(true);
}

// Unlock the next module card (sequential gating)
function unlockNextModule(currentCard, currentIndex) {
    const container = currentCard.parentElement; // roadmap-modules
    if (!container) return;
    const cards = Array.from(container.querySelectorAll('.module-card'));
    const idx = (typeof currentIndex === 'number') ? currentIndex : cards.indexOf(currentCard);
    const next = cards[idx + 1];
    if (!next) return;
    if (next.classList.contains('locked')) {
        next.classList.remove('locked');
        const status = next.querySelector('.module-status');
        if (status) status.innerHTML = '<span class="status-badge current">Current</span>';
        const title = next.getAttribute('data-title') || '';
        const right = next.querySelector('.topbar-right');
        if (right) {
            right.innerHTML = `<button class="start-btn complete-btn" onclick="markModuleComplete(this, '${(title || '').replace(/'/g, "\\'")}')">Mark Complete</button>`;
        }
        const nIndex = parseInt(next.getAttribute('data-index'), 10);
        if (!isNaN(nIndex) && currentRoadmapModules[nIndex]) {
            currentRoadmapModules[nIndex].status = 'current';
            currentRoadmapModules[nIndex].completed = false;
            saveRoadmapObject({
                meta: { scoresHash: getScoresHash(currentStudentData), model: 'llama3-8b', promptVersion: 1 },
                modules: currentRoadmapModules
            });
        }
    }
}

// Re-lock all following modules when a module is un-completed
function relockFollowingModules(currentCard, currentIndex) {
    const container = currentCard.parentElement;
    if (!container) return;
    const cards = Array.from(container.querySelectorAll('.module-card'));
    const start = (typeof currentIndex === 'number') ? currentIndex : cards.indexOf(currentCard);
    for (let i = start + 1; i < cards.length; i++) {
        const card = cards[i];
        card.classList.remove('completed');
        card.classList.add('locked');
        const status = card.querySelector('.module-status');
        if (status) status.innerHTML = '<span class="status-badge locked">🔒 Locked</span>';
        const fill = card.querySelector('.progress-fill');
        const text = card.querySelector('.progress-text');
        if (fill) fill.style.width = '0%';
        if (text) text.textContent = '0% Complete';
        const right = card.querySelector('.topbar-right');
        if (right) right.innerHTML = '<button class="locked-btn" disabled>Complete previous module first</button>';
        const idx = parseInt(card.getAttribute('data-index'), 10);
        if (!isNaN(idx) && currentRoadmapModules[idx]) {
            currentRoadmapModules[idx].status = 'locked';
            currentRoadmapModules[idx].completed = false;
        }
    }
}

// ===== Roadmap persistence =====
function getRoadmapKey() {
    const dataSource = document.getElementById('dataSource').value;
    let key = 'lp_roadmap:';
    if (dataSource === 'demo') {
        const profile = document.getElementById('studentProfile').value;
        key += `demo:${profile}`;
    } else {
        const courseId = document.getElementById('courseSelect').value;
        const studentId = document.getElementById('studentSelect').value;
        key += `live:${courseId}:${studentId}`;
    }
    return key;
}

function saveRoadmap(modules) {
    try {
        localStorage.setItem(getRoadmapKey(), JSON.stringify(modules || []));
    } catch(e) { console.warn('saveRoadmap failed', e); }
}

function loadRoadmap() {
    try {
        const raw = localStorage.getItem(getRoadmapKey());
        if (!raw) return null;
        return JSON.parse(raw);
    } catch(e) { console.warn('loadRoadmap failed', e); return null; }
}

function tryLoadSavedRoadmap() {
    // Prefer object form (meta + modules); fallback to legacy array
    const savedObj = loadRoadmapObject();
    if (savedObj && Array.isArray(savedObj.modules) && savedObj.modules.length) {
        displayRoadmapModules(savedObj.modules);
        setGenerateBtnLabel(true);
        return true;
    }
    const saved = loadRoadmap();
    if (saved && saved.length) { displayRoadmapModules(saved); setGenerateBtnLabel(true); return true; }
    return false;
}

function setGenerateBtnLabel(hasSaved) {
    const btn = document.getElementById('generate-roadmap-btn');
    if (!btn) return;
    if (hasSaved) { btn.innerHTML = '<span class="btn-icon">🤖</span> Regenerate'; }
    else { btn.innerHTML = '<span class="btn-icon">🤖</span> Generate AI Roadmap'; }
}

function clearRoadmapDisplay() {
    const resultsDiv = document.getElementById('roadmap-results');
    if (resultsDiv) {
        resultsDiv.innerHTML = `
            <div class="roadmap-placeholder">
                <div class="placeholder-icon">🎯</div>
                <h3>Ready to create your learning path?</h3>
                <p>Click "Generate AI Roadmap" to get a personalized study plan based on your performance data.</p>
            </div>
        `;
    }
    currentRoadmapModules = [];
}

// Roadmap object persistence (meta + modules)
function getScoresHash(student) {
    try {
        const s = student && student.scores ? JSON.stringify(student.scores) : '{}';
        let h = 0; for (let i = 0; i < s.length; i++) { h = ((h << 5) - h) + s.charCodeAt(i); h |= 0; }
        return String(h);
    } catch (e) { return '0'; }
}

function saveRoadmapObject(obj) {
    try { localStorage.setItem(getRoadmapKey(), JSON.stringify(obj)); } catch (e) { console.warn('saveRoadmapObject failed', e); }
}

function loadRoadmapObject() {
    try {
        const raw = localStorage.getItem(getRoadmapKey());
        if (!raw) return null;
        const parsed = JSON.parse(raw);
        if (Array.isArray(parsed)) return { meta: null, modules: parsed }; // legacy support
        return parsed;
    } catch (e) { console.warn('loadRoadmapObject failed', e); return null; }
}

// Get AI recommendations
function getRecommendations() {
    if (!currentStudentData) {
        alert('Please select a student first!');
        return;
    }
    
    const resultsDiv = document.getElementById('resources-list');
    resultsDiv.innerHTML = '<div class="loading"><div class="spinner"></div>💡 AI is generating personalized recommendations...</div>';
    
    // Create AI prompt for resources
    const scores = currentStudentData.scores || {};
    const weakSubjects = Object.entries(scores).filter(([s, score]) => score < 60).map(([s]) => s);
    
    // Get course name for context
    const courseSelect = document.getElementById('courseSelect');
    const courseName = courseSelect ? courseSelect.options[courseSelect.selectedIndex]?.text || 'Unknown Course' : 'Demo Course';
    
    console.log('Generating recommendations for:', courseName); // Debug log
    console.log('Student scores:', scores); // Debug log
    console.log('Weak subjects:', weakSubjects); // Debug log
    
    // Get all subject names for better context
    const allSubjects = Object.keys(scores);
    const strongSubjects = Object.entries(scores).filter(([s, score]) => score >= 80).map(([s]) => s);
    
    console.log('All subjects:', allSubjects); // Debug log
    console.log('Strong subjects:', strongSubjects); // Debug log
    
    // Create guaranteed working resources using actual quiz/subject data
    const searchTerms = [];
    
    // Add course name
    searchTerms.push(courseName);
    
    // Add specific subjects from quizzes/tests
    if (allSubjects.length > 0) {
        searchTerms.push(...allSubjects);
    }
    
    // Add weak subjects for priority
    if (weakSubjects.length > 0) {
        searchTerms.push(...weakSubjects.map(s => s + " tutorial"));
    }
    
    console.log('Search terms for resources:', searchTerms); // Debug log
    
    // Skip AI generation and create guaranteed working resources directly
    const guaranteedResources = [
        {
            title: `${courseName} Complete Course`,
            platform: "Coursera",
            description: `Comprehensive ${courseName} course with certificates`,
            searchTerm: courseName
        },
        {
            title: `${courseName} Video Tutorials`,
            platform: "YouTube", 
            description: `Free video lessons on ${courseName} fundamentals`,
            searchTerm: courseName + " tutorial"
        }
    ];
    
    // Add subject-specific resources if available
    if (allSubjects.length > 0) {
        guaranteedResources.push({
            title: `${allSubjects[0]} Explained`,
            platform: "YouTube",
            description: `Learn ${allSubjects[0]} step by step`,
            searchTerm: allSubjects[0] + " explained"
        });
    }
    
    if (weakSubjects.length > 0) {
        guaranteedResources.push({
            title: `${weakSubjects[0]} Help`,
            platform: "Coursera", 
            description: `Master ${weakSubjects[0]} with expert guidance`,
            searchTerm: weakSubjects[0] + " course"
        });
    } else if (allSubjects.length > 1) {
        guaranteedResources.push({
            title: `${allSubjects[1]} Mastery`,
            platform: "Coursera",
            description: `Advanced ${allSubjects[1]} concepts and practice`,
            searchTerm: allSubjects[1] + " advanced"
        });
    } else {
        guaranteedResources.push({
            title: `${courseName} Practice`,
            platform: "YouTube",
            description: `Practice problems and solutions for ${courseName}`,
            searchTerm: courseName + " practice problems"
        });
    }
    
    console.log('Generated guaranteed resources:', guaranteedResources); // Debug log
    
    // Convert to resources format and return immediately
    // Define local icon/color sets (avoid undefined variable errors)
    const icons = ["🧠", "📺", "📚", "🎓"];
    const colors = ["blue", "orange", "green", "purple"];

    try {
        const finalResources = guaranteedResources.slice(0, 4).map((res, index) => {
            let url;
            if (res.platform === "Coursera") {
                url = `https://coursera.org/search?query=${encodeURIComponent(res.searchTerm)}`;
            } else if (res.platform === "YouTube") {
                url = `https://youtube.com/results?search_query=${encodeURIComponent(res.searchTerm)}`;
            }
            
            return {
                icon: icons[index % icons.length],
                iconColor: colors[index % colors.length],
                title: res.title,
                author: res.platform,
                description: res.description,
                link: url,
                tags: [res.platform, "Study"],
                rating: 4.5 + (index * 0.1),
                buttonText: "Start Learning",
                buttonColor: index % 2 === 0 ? "blue" : "orange"
            };
        });
        
        console.log('Final guaranteed resources:', finalResources); // Debug log
        
        // Skip AJAX call and display resources directly
        displayResources(finalResources);
    } catch (e) {
        console.error('Recommendations render error:', e);
        const loadingDiv = document.querySelector('.loading');
        if (loadingDiv) loadingDiv.remove();
        resultsDiv.innerHTML = '<div class="alert alert-danger">Could not render recommendations. Please try again.</div>';
    }
    return; // Exit early, don't make AJAX call
    
    const dataSource = document.getElementById('dataSource').value;
    let ajaxData;
    
    if (dataSource === 'demo') {
        const profile = document.getElementById('studentProfile').value;
        ajaxData = 'action=get_recommendations&profile=' + encodeURIComponent(profile) + '&prompt=' + encodeURIComponent(resourcesPrompt);
    } else {
        const courseId = document.getElementById('courseSelect').value;
        const studentId = document.getElementById('studentSelect').value;
        ajaxData = 'action=get_recommendations&userid=' + studentId + '&courseid=' + courseId + '&prompt=' + encodeURIComponent(resourcesPrompt);
    }
    
    fetch('ajax.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: ajaxData
    })
    .then(response => response.json())
    .then(data => {
        const loadingDiv = document.querySelector('.loading');
        if (loadingDiv) loadingDiv.remove();
        
        console.log('AI Recommendations Response:', data); // Debug log
        
        if (data.success) {
            console.log('🔍 AJAX Response Data:', data); // Debug full response
            console.log('🔍 AI Model used:', data.model); // Debug model
            console.log('🔍 AI Method:', data.method); // Debug method
            console.log('🔍 data.recommendations type:', typeof data.recommendations); // Debug type
            console.log('🔍 data.recommendations value:', data.recommendations); // Debug value
            console.log('🔍 data.recommendations length:', data.recommendations ? data.recommendations.length : 'null/undefined'); // Debug length
            
            const resources = parseResources(data.recommendations, currentStudentData);
            console.log('Parsed resources:', resources); // Debug log
            displayResources(resources);
        } else {
            console.error('Failed to get recommendations:', data.message); // Debug log
            resultsDiv.innerHTML += '<div class="alert alert-danger">Failed to get recommendations: ' + (data.message || 'Unknown error') + '</div>';
        }
    })
    .catch(error => {
        const loadingDiv = document.querySelector('.loading');
        if (loadingDiv) loadingDiv.remove();
        resultsDiv.innerHTML += '<div class="alert alert-danger">Failed to get recommendations: ' + error.message + '</div>';
    });
}

// Parse resources from AI response
function parseResources(response, studentData) {
    console.log('Parsing AI recommendations:', response); // Debug log
    
    const resources = [];
    const icons = ["🧬", "🔬", "📚", "🎓"]; // Biology-relevant icons
    const colors = ["red", "green", "blue", "purple"];
    
    if (response && response.includes('|')) {
        console.log('Response contains pipes - parsing recommendations...'); // Debug log
        const lines = response.split('\n').filter(line => line.trim() && line.includes('|'));
        console.log('Pipe lines found:', lines); // Debug log
        
        // SIMPLIFIED PARSING - Let's try a much simpler approach
        console.log('🔍 Starting simplified parsing...'); // Debug log
        
        // Try to reconstruct the original format from the debug output we saw
        // We know the AI returns: Title|Platform|Description|URL but split across lines
        
        // Method 1: Try to find complete resources by looking for URLs
        const allText = response.replace(/\n/g, ' '); // Replace newlines with spaces
        console.log('All text joined:', allText); // Debug log
        
        // Look for lines that match the pattern: Title|Platform|Description|URL
        const cleanLines = [];
        const textLines = allText.split(/\s+/); // Split by whitespace
        
        // Reconstruct lines by looking for pipe patterns
        let currentLine = '';
        let pipeCount = 0;
        
        for (let word of textLines) {
            currentLine += (currentLine ? ' ' : '') + word;
            pipeCount += (word.match(/\|/g) || []).length;
            
            // If we have 3 pipes and the line contains http, it's likely complete
            if (pipeCount >= 3 && currentLine.includes('http')) {
                cleanLines.push(currentLine);
                currentLine = '';
                pipeCount = 0;
            }
        }
        
        console.log('Reconstructed lines:', cleanLines); // Debug log
        
        cleanLines.forEach((line, index) => {
            if (index < 4) {
                const parts = line.split('|');
                console.log(`Line ${index} parts:`, parts); // Debug log
                
                if (parts.length >= 4) {
                    const title = parts[0].trim();
                    const platform = parts[1].trim();
                    const description = parts[2].trim();
                    const url = parts[3].trim();
                    
                    console.log(`Resource ${index}: Title="${title}", Platform="${platform}", Description="${description}", URL="${url}"`); // Debug log
                    
                    if (title && platform && url) {
                        // Ensure URL is valid or provide fallback
                        let finalUrl = url;
                        if (!url.startsWith('http')) {
                            finalUrl = 'https://' + url;
                        }
                        
                        // Add fallback URLs for common platforms if URL looks broken
                        if (platform.toLowerCase().includes('coursera') && !finalUrl.includes('coursera.org')) {
                            finalUrl = `https://coursera.org/search?query=${encodeURIComponent(title)}`;
                        } else if (platform.toLowerCase().includes('youtube') && !finalUrl.includes('youtube.com')) {
                            finalUrl = `https://youtube.com/results?search_query=${encodeURIComponent(title)}`;
                        } else if (platform.toLowerCase().includes('edx') && !finalUrl.includes('edx.org')) {
                            finalUrl = `https://edx.org/search?q=${encodeURIComponent(title)}`;
                        } else if (platform.toLowerCase().includes('khan') && !finalUrl.includes('khanacademy.org')) {
                            finalUrl = `https://khanacademy.org/search?page_search_query=${encodeURIComponent(title)}`;
                        }
                        
                        const resource = {
                            icon: icons[index % icons.length],
                            iconColor: colors[index % colors.length],
                            title: title,
                            author: platform,
                            description: description || 'Study resource',
                            link: finalUrl,
                            tags: [platform, "Study"],
                            rating: 4.5 + (index * 0.1),
                            buttonText: "Start Learning",
                            buttonColor: index % 2 === 0 ? "blue" : "orange"
                        };
                        console.log(`✅ Successfully created resource ${index}:`, resource); // Debug log
                        resources.push(resource);
                    } else {
                        console.log(`❌ Failed to create resource ${index}: missing required fields`); // Debug log
                    }
                } else {
                    console.log(`❌ Line ${index} doesn't have enough parts (${parts.length})`); // Debug log
                }
            }
        });
    } else {
        console.log('Response does NOT contain pipes - using fallback'); // Debug log
        console.log('Response preview:', response ? response.substring(0, 200) + '...' : 'null/empty');
    }
    
    // Temporarily removed fallback to see what's actually being parsed
    if (resources.length === 0) {
        console.log('❌ NO RESOURCES PARSED - Parsing failed completely!'); // Debug log
        console.log('This means the parsing logic has issues.'); // Debug log
        return [
            {
                icon: "❌", iconColor: "red", title: "PARSING FAILED", author: "Debug Mode",
                description: "No resources were successfully parsed from AI response", tags: ["Debug", "Error"],
                rating: 0, buttonText: "Check Console", buttonColor: "red",
                link: "#"
            }
        ];
    }
    
    return resources.slice(0, 4);
}

// ===== CHATBOT FUNCTIONS =====

// Handle chat input key press
function handleChatKeyPress(event) {
    if (event.key === 'Enter') {
        sendChatMessage();
    }
}

// Send quick message
function sendQuickMessage(message) {
    document.getElementById('chat-input').value = message;
    sendChatMessage();
}

// Create study plan - navigate to roadmap tab
function createStudyPlan() {
    if (!currentStudentData) {
        alert('Please select a student first!');
        return;
    }
    // Add a message to chat
    addChatMessage('I\'ll help you create a personalized study plan! Let me take you to the roadmap section.', 'bot');
    // Switch to roadmap tab after a short delay
    setTimeout(() => {
        switchTab('roadmap');
        // Try to load existing roadmap or show generate button
        if (!tryLoadSavedRoadmap()) {
            // If no saved roadmap, highlight the generate button
            const btn = document.getElementById('generate-roadmap-btn');
            if (btn) {
                btn.style.animation = 'pulse 2s infinite';
                setTimeout(() => {
                    btn.style.animation = '';
                }, 4000);
            }
        }
    }, 1000);
}

// Send chat message
function sendChatMessage() {
    const input = document.getElementById('chat-input');
    const message = input.value.trim();
    
    if (!message) return;
    
    // Clear input and disable send button
    input.value = '';
    const sendBtn = document.getElementById('send-btn');
    sendBtn.disabled = true;
    
    // Add user message to chat
    addChatMessage(message, 'user');
    
    // Show typing indicator
    showTypingIndicator();
    
    // Send message to AI
    sendToAI(message);
}

// Add message to chat
function addChatMessage(message, sender, isHTML = false) {
    const chatMessages = document.getElementById('chat-messages');
    if (!chatMessages) return;
    
    const messageDiv = document.createElement('div');
    messageDiv.className = `chat-message ${sender}-message`;
    
    const avatar = sender === 'user' ? '👤' : '🤖';
    const time = new Date().toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'});
    
    // Ensure message is properly escaped if not HTML
    const safeMessage = isHTML ? message : (typeof escapeHtml === 'function' ? escapeHtml(message) : String(message).replace(/</g, '&lt;').replace(/>/g, '&gt;'));
    
    messageDiv.innerHTML = `
        <div class="message-avatar">${avatar}</div>
        <div class="message-content">
            <div class="message-text">${safeMessage}</div>
            <div class="message-time">${time}</div>
        </div>
    `;
    
    chatMessages.appendChild(messageDiv);
    chatMessages.scrollTop = chatMessages.scrollHeight;
}

// Show typing indicator
function showTypingIndicator() {
    const chatMessages = document.getElementById('chat-messages');
    const typingDiv = document.createElement('div');
    typingDiv.className = 'typing-indicator';
    typingDiv.id = 'typing-indicator';
    
    typingDiv.innerHTML = `
        <div class="message-avatar">🤖</div>
        <div class="typing-dots">
            <div class="typing-dot"></div>
            <div class="typing-dot"></div>
            <div class="typing-dot"></div>
        </div>
    `;
    
    chatMessages.appendChild(typingDiv);
    chatMessages.scrollTop = chatMessages.scrollHeight;
}

// Hide typing indicator
function hideTypingIndicator() {
    const typingIndicator = document.getElementById('typing-indicator');
    if (typingIndicator) {
        typingIndicator.remove();
    }
}

// Send message to AI
function sendToAI(message) {
    // Create compact JSON-style prompt (short, structured output)
    const courseName = document.getElementById('courseSelect') ? 
        document.getElementById('courseSelect').options[document.getElementById('courseSelect').selectedIndex]?.text || 'Current Course' : 
        'Demo Course';
    
    const studentName = currentStudentData ? currentStudentData.name || 'Student' : 'Student';
    const scores = currentStudentData ? currentStudentData.scores || {} : {};
    const allSubjects = Object.keys(scores);
    const weakSubjects = Object.entries(scores).filter(([s, score]) => score < 60).map(([s]) => s);
    
    const aiPrompt = `You are an AI Study Assistant. Answer the student's question with a helpful, personalized response.

Student Context:
- Name: ${studentName}
- Course: ${courseName}
- Subjects: ${allSubjects.join(', ') || 'N/A'}
- Performance: ${JSON.stringify(scores)}
- Weak areas: ${weakSubjects.join(', ') || 'None identified'}

Student Question: "${message}"

Provide a concise, helpful response (2-3 sentences max). Be specific to their performance data when possible. Keep it conversational and encouraging.`;

    console.log('Sending to AI:', aiPrompt); // Debug log

    const dataSource = document.getElementById('dataSource').value;
    let ajaxData;
    
    if (dataSource === 'demo') {
        const profile = document.getElementById('studentProfile').value;
        ajaxData = 'action=chat_message&profile=' + encodeURIComponent(profile) + '&message=' + encodeURIComponent(message) + '&prompt=' + encodeURIComponent(aiPrompt);
    } else {
        const courseId = document.getElementById('courseSelect').value;
        const studentId = document.getElementById('studentSelect').value;
        ajaxData = 'action=chat_message&userid=' + studentId + '&courseid=' + courseId + '&message=' + encodeURIComponent(message) + '&prompt=' + encodeURIComponent(aiPrompt);
    }
    
    fetch('ajax.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: ajaxData
    })
    .then(response => response.json())
    .then(data => {
        hideTypingIndicator();
        
        if (data.success) {
            // For simple text responses, just add directly without JSON parsing
            addChatMessage(data.response, 'bot');
        } else {
            addChatMessage('Sorry, I encountered an error. Please try again.', 'bot');
            console.error('Chat error:', data.message);
        }
        
        // Re-enable send button
        document.getElementById('send-btn').disabled = false;
    })
    .catch(error => {
        hideTypingIndicator();
        addChatMessage('Sorry, I\'m having trouble connecting. Please try again.', 'bot');
        console.error('Chat fetch error:', error);
        document.getElementById('send-btn').disabled = false;
    });
}

// ===== Chat parsing helpers =====
function escapeHtml(str) {
    return String(str)
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#039;');
}

function extractJsonObject(text) {
    if (!text) return null;
    try {
        const trimmed = text.trim();
        if (trimmed.startsWith('{')) {
            return JSON.parse(trimmed);
        }
        const fenced = text.match(/```json[\s\S]*?```/i) || text.match(/```[\s\S]*?```/);
        if (fenced) {
            const inner = fenced[0].replace(/```json|```/gi, '').trim();
            return JSON.parse(inner);
        }
        const start = text.indexOf('{');
        const end = text.lastIndexOf('}');
        if (start !== -1 && end !== -1 && end > start) {
            return JSON.parse(text.substring(start, end + 1));
        }
    } catch (e) {
        console.warn('JSON parse failed:', e);
    }
    return null;
}

function renderAIChatResponse(responseText) {
    const obj = extractJsonObject(responseText);
    if (!obj) {
        // Fallback: show raw but escaped
        addChatMessage(responseText, 'bot');
        return;
    }
    const advice = obj.advice ? escapeHtml(obj.advice) : '';
    const list = a => Array.isArray(a) ? a : [];
    const weaknesses = list(obj.weaknesses).map(escapeHtml);
    const actions = list(obj.actions).map(escapeHtml);
    const practice = list(obj.practice).map(escapeHtml);
    const resources = list(obj.resources);

    const html = `
      <div class="ai-structured">
        ${advice ? `<div class="sec"><div class="sec-title">Advice</div><div class="sec-body">${advice}</div></div>` : ''}
        ${weaknesses.length ? `<div class="sec"><div class="sec-title">Weak Areas</div><ul class="sec-list">${weaknesses.map(x=>`<li>${x}</li>`).join('')}</ul></div>` : ''}
        ${actions.length ? `<div class="sec"><div class="sec-title">Next Actions</div><ol class="sec-list">${actions.map(x=>`<li>${x}</li>`).join('')}</ol></div>` : ''}
        ${practice.length ? `<div class="sec"><div class="sec-title">Practice</div><ul class="sec-list">${practice.map(x=>`<li>${x}</li>`).join('')}</ul></div>` : ''}
        ${resources.length ? `<div class="sec"><div class="sec-title">Resources</div><ul class="sec-list">${resources.map(r=>{
            const t = escapeHtml(r.title || 'Resource');
            const u = escapeHtml(r.url || '#');
            return `<li><a href="${u}" target="_blank" rel="noopener">${t}</a></li>`;
        }).join('')}</ul></div>` : ''}
      </div>`;

    addChatMessage(html, 'bot', true);
}

// Display resources
function displayResources(resources) {
    const resultsDiv = document.getElementById('resources-list');
    
    let resourcesHtml = '<div class="resources-grid">';
    
    resources.forEach(resource => {
        const tagsHtml = resource.tags.map(tag => `<span class="resource-tag">${tag}</span>`).join('');
        const stars = '★'.repeat(Math.floor(resource.rating)) + (resource.rating % 1 ? '☆' : '');
        
        resourcesHtml += `
            <div class="resource-card">
                <div class="resource-header">
                    <div class="resource-icon ${resource.iconColor}">${resource.icon}</div>
                    <div class="resource-rating">
                        <span class="stars">${stars}</span>
                        <span class="rating-number">${resource.rating}</span>
                    </div>
                </div>
                <div class="resource-content">
                    <h3 class="resource-title">${resource.title}</h3>
                    <p class="resource-author">by ${resource.author}</p>
                    <p class="resource-description">${resource.description}</p>
                    <div class="resource-tags">${tagsHtml}</div>
                </div>
                <div class="resource-actions">
                    <button class="resource-btn ${resource.buttonColor}" onclick="window.open('${resource.link || '#'}', '_blank')">${resource.buttonText}</button>
                </div>
            </div>
        `;
    });
    
    resourcesHtml += '</div>';
    resultsDiv.innerHTML = resourcesHtml;
}
