# LearnPath Navigator - Moodle Plugin

**AI-Powered Adaptive Learning Roadmaps**  
Team Delimiters - NatWest Hack4aCause

![AI Study Assistant](AI%20study%20Assistant.png)

## Overview

LearnPath Navigator is a Moodle plugin that leverages Snowflake Cortex AI to provide personalized learning analytics and adaptive roadmap generation for students. The plugin analyzes student performance data and generates customized study plans using real AI models.

## Features

- 🤖 **Real AI Analysis**: Powered by Snowflake Cortex AI (mistral-7b, llama3.1, etc.)
- 📊 **Performance Analytics**: Detailed analysis of student strengths and weaknesses
- 🧮 **Gradebook Integration**: Pulls live grades and activity completion data directly from Moodle course gradebooks to drive AI insights
- 🗺️ **Personalized Roadmaps**: Custom learning paths based on individual performance
- 🎯 **Multi-Profile Support**: Handles struggling, average, and advanced students
- ⚡ **Real-time Processing**: Live AI responses with intelligent fallbacks
- 🔧 **Configurable**: Admin settings for Snowflake connection and AI models

## Requirements

- Moodle 4.0+ (tested on 4.1+)
- PHP 7.4+
- Snowflake CLI installed and configured
- Snowflake account with Cortex AI access
- Cross-region Cortex AI enabled (if needed)

## Installation

### 1. Install the Plugin

Copy the plugin to your Moodle installation:

```bash
# For Windows (XAMPP)
cp -r learnpath/ C:\xampp\htdocs\moodle\local\

# For Linux
cp -r learnpath/ /var/www/html/moodle/local/

# For macOS (MAMP)
cp -r learnpath/ /Applications/MAMP/htdocs/moodle/local/
```

### 2. Complete Moodle Installation

1. Log in to Moodle as administrator
2. Navigate to **Site Administration → Notifications**
3. Moodle will detect the new plugin and prompt for installation
4. Click **Install** and follow the prompts

### 3. Configure Snowflake Connection

1. Go to **Site Administration → Plugins → Local plugins → LearnPath Navigator**
2. Configure the following settings:
   - **Snowflake CLI Path**: Path to snow executable (default: `snow`)
   - **Snowflake Connection**: Your connection name (default: `default`)
   - **Snowflake Config Path**: Path to config directory (optional)
   - **AI Model**: Select preferred model (mistral-7b recommended)
   - **Enable AI Features**: Check to enable AI functionality

### 4. Test the Installation

1. Navigate to **Site Administration → Development → LearnPath Navigator**
2. Or access directly: `https://yourmoodle.com/local/learnpath/`
3. Test with different student profiles
4. Verify AI responses are working

## Usage

### For Students
1. Access the plugin from the Moodle navigation
2. Select your learning profile (or have it auto-detected)
3. Click "Load Student Data" to see your performance summary
4. Use "Analyze Performance" for AI-powered insights
5. Generate personalized learning roadmaps

### For Teachers
1. Access student analytics and progress reports
2. View AI-generated recommendations for individual students
3. Use insights to customize course content and assignments

### For Administrators
1. Configure Snowflake connection settings
2. Monitor AI usage and performance
3. Manage plugin permissions and access

## Technical Architecture

```
Moodle Plugin → Snowflake CLI → Snowflake Cortex AI → AI Response
     ↓              ↓                    ↓              ↓
  PHP/AJAX    →  PowerShell     →    mistral-7b    →   JSON
```

### Key Components

- **`index.php`**: Main interface with student selection and AI controls
- **`ajax.php`**: AJAX handler for real-time AI requests
- **`classes/snowflake_connector.php`**: Snowflake CLI integration
- **`classes/student_data.php`**: Student data management and AI prompts
- **`settings.php`**: Admin configuration panel
- **`version.php`**: Plugin metadata and version info

### Moodle Gradebook Integration

- **Live Grade Sync**: `classes/student_data.php` calls Moodle's gradebook APIs to pull the latest course grades, quiz attempts, and activity completions for the selected learner.
- **Performance Hashing**: Roadmap persistence leverages a score hash to detect grade changes, automatically triggering roadmap regeneration when fresh gradebook data arrives.
- **Dashboards & AI Context**: The analytics dashboard and Snowflake prompts include grade averages, weak-topic detection, and completion gaps sourced directly from Moodle grade tables.
- **Role-Aware Access**: Capability checks in `index.php` ensure only permitted teachers or admins can view aggregated grade analytics, while students see only their own performance footprints.

## Configuration Files

### Snowflake CLI Setup
Ensure your Snowflake CLI is configured with:
```toml
[connections.your_connection_name]
account = "your_account_id"
user = "your_username"
password = "your_password"
role = "your_role"
database = "your_database"
schema = "PUBLIC"
warehouse = "your_warehouse"
```

### Required Snowflake Permissions
```sql
USE ROLE ACCOUNTADMIN;
ALTER ACCOUNT SET ENABLE_CROSS_REGION_CORTEX_CALLS = TRUE;
GRANT DATABASE ROLE SNOWFLAKE.CORTEX_USER TO ROLE your_role;
```

## Troubleshooting

### Common Issues

1. **"Connection not configured" error**
   - Check Snowflake CLI installation
   - Verify connection name in settings
   - Ensure config path is correct

2. **"AI model unavailable" error**
   - Enable cross-region Cortex calls
   - Try different AI model in settings
   - Check Snowflake account permissions

3. **Plugin not appearing**
   - Clear Moodle cache
   - Check file permissions
   - Verify plugin is in correct directory

### Debug Mode
Enable debug mode in settings to see detailed error messages and AI response parsing.

## Development

### Phase 1: Foundation ✅
- Basic Snowflake AI integration
- Real Cortex API calls
- Working student profiles

### Phase 2: Core AI (Next)
- Enhanced prompt engineering
- Multi-model support
- Advanced analytics

### Phase 3: Data Integration
- Real Moodle gradebook integration
- Historical performance tracking
- Batch processing

### Phase 4: Polish & Deploy
- UI improvements
- Snowflake cloud deployment
- Performance optimization

## Support

- **Team**: Delimiters
- **Contact**: prasukj123@gmail.com
- **Repository**: finos-labs/learnaix-h-2025-delimiters
- **Hackathon**: NatWest Hack4aCause

## License

Copyright 2025 Team Delimiters  
Licensed under the GNU GPL v3 or later.
