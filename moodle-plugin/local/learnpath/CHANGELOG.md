# Changelog - LearnPath Navigator

All notable changes to this project will be documented in this file.

## [v1.0] - 2025-01-25

### 🚀 Major Features Added
- **AI-Powered Learning Analytics**: Complete integration with Snowflake Cortex AI
- **Personalized Roadmaps**: Dynamic learning path generation based on student performance
- **Interactive Chat Assistant**: Real-time AI-powered study guidance
- **Multi-Platform Support**: Cross-platform compatibility (Windows, Linux, macOS)
- **Configurable Settings**: Admin panel for Snowflake connection management

### ✨ UI/UX Improvements
- **Modern Interface**: Clean, responsive design with Khan Academy-inspired layout
- **Wide Mode Toggle**: Expandable UI for better screen utilization
- **Interactive Roadmap**: Sequential module unlocking with progress tracking
- **Button-Style Resources**: Clickable study material cards with hover effects
- **Chat Bubbles**: Proper user/bot message styling with timestamps
- **Progress Indicators**: Visual completion tracking and difficulty badges

### 🔧 Technical Enhancements
- **Removed Hardcoded Paths**: All static local paths replaced with configurable settings
- **Cross-Platform CLI Support**: Automatic OS detection for Snowflake CLI execution
- **Robust Error Handling**: Comprehensive fallback mechanisms and error messages
- **Persistent Storage**: LocalStorage-based roadmap and completion tracking
- **Grade-Change Detection**: Smart roadmap regeneration only when needed
- **Security Improvements**: Input validation and XSS protection

### 🗂️ File Structure
```
local/learnpath/
├── README.md                    # Main documentation
├── INSTALLATION.md             # Step-by-step installation guide
├── DEPLOYMENT_CHECKLIST.md     # Pre-deployment verification
├── CHANGELOG.md                # This file
├── .gitignore                  # Git ignore rules
├── config.template.toml        # Snowflake configuration template
├── version.php                 # Plugin metadata
├── settings.php                # Admin configuration panel
├── index.php                   # Main interface
├── ajax.php                    # AJAX request handler
├── scripts.js                  # Frontend JavaScript
├── styles.css                  # UI styling
├── classes/
│   ├── snowflake_connector.php # AI integration
│   └── student_data.php        # Data management
├── lang/en/
│   └── local_learnpath.php     # Language strings
└── db/
    └── access.php              # Capability definitions
```

### 🔒 Security & Configuration
- **Environment Variables**: Support for secure credential management
- **Configurable Paths**: No hardcoded system paths
- **Template Configuration**: Example Snowflake setup files
- **Permission Management**: Proper Moodle capability checks
- **Input Sanitization**: All user inputs properly validated

### 📱 Platform Compatibility
- **Windows**: PowerShell-based Snowflake CLI execution
- **Linux/Unix**: Shell-based command execution
- **macOS**: Native compatibility
- **Docker**: Container-ready configuration

### 🎯 Key Features
1. **Student Performance Dashboard**
   - Real-time grade analysis
   - Subject-wise performance breakdown
   - Strength and weakness identification

2. **AI-Generated Roadmaps**
   - Personalized learning paths
   - Sequential module unlocking
   - Progress tracking and persistence
   - Integrated study resources

3. **Interactive Chat Assistant**
   - Context-aware responses
   - Student performance integration
   - Quick action buttons
   - Study plan navigation

4. **Admin Configuration**
   - Snowflake connection settings
   - AI model selection
   - Feature toggles
   - Debug options

### 🐛 Bug Fixes
- Fixed chat bubble styling issues
- Resolved module unlocking logic
- Corrected user-specific roadmap persistence
- Fixed resource button overflow
- Resolved JavaScript escaping issues

### 📚 Documentation
- Comprehensive README with setup instructions
- Detailed installation guide
- Configuration templates
- Troubleshooting guide
- Deployment checklist

### 🔄 Migration Notes
- All hardcoded paths removed - requires configuration
- Snowflake CLI path must be set in admin settings
- Connection name configurable per environment
- Backward compatible with existing installations

### 🚧 Known Issues
- Requires Snowflake CLI to be installed separately
- AI features depend on Snowflake Cortex availability
- Cross-region Cortex calls may need enabling

### 🎉 Contributors
- **Team Delimiters** - NatWest Hack4aCause
- **Contact**: prasukj123@gmail.com
- **Repository**: finos-labs/learnaix-h-2025-delimiters

---

## Installation Requirements

- Moodle 4.0+
- PHP 7.4+
- Snowflake CLI
- Snowflake account with Cortex AI access

## Upgrade Instructions

1. Backup existing installation
2. Replace plugin files
3. Update configuration in admin settings
4. Test functionality
5. Clear Moodle caches

For detailed instructions, see INSTALLATION.md
