# Team Delimiters - Hackathon Setup Documentation

## Project Overview

**Team**: Delimiters  
**Members**: Aryan Dangwal, Prashuk Jain, Tuwshar Ahlawat, Prashuk Jain  
**Contact**: prasukj123@gmail.com  
**Event**: NatWest Hack4aCause Hackathon  
**Goal**: Create AI-enabled Moodle plugins for LearnAIx platform  
**Deployment Target**: Snowflake Cloud Platform  

---

## Completed Setup Tasks

### 1. **Project Structure & Repository Setup**
- **Repository**: `finos-labs/learnaix-h-2025-delimiters`
- **README.md**: Updated with hackathon submission template
- **Team Information**: Added all team member details
- **Git Configuration**: Initial commit completed
- **Development Progress Tracking**: Implemented todo system

### 2. **Snowflake Cloud Environment Setup**
- **Free Trial Account**: Created with 30-day trial ($400 credits)
- **Account Details**:
  - Account ID: `[REDACTED]`
  - Region: Asia Pacific (Singapore)
  - User: `[REDACTED]`
  - Role: `ACCOUNTADMIN` (primary), `MOODLE_ROLE` (project-specific)

### 3. **Snowflake CLI Configuration**
- **Installation**: Snowflake CLI v3.11.0.0 installed via winget
- **Connection Setup**: `Natwest_Delimiters` connection configured
- **Authentication**: Successfully tested and verified
- **Configuration File**: `C:\Users\HP\AppData\Local\snowflake\config.toml`

### 4. **Database & Role Configuration**
- **MOODLE_ROLE**: Created with appropriate permissions
- **MOODLE_APP Database**: Created and accessible
- **PUBLIC Schema**: Default schema configured
- **Cortex AI Permissions**: Granted for AI functionality
- **Image Repository**: `MOODLE_APP.PUBLIC.IMG` created for containers
- **File Stage**: `MOODLE_APP.PUBLIC.MOUNTED` created for data uploads

### 5. **Template Analysis**
- **Available Templates Reviewed**:
  - **With PHP**: `plugin-local-cortexanalyst`, `plugin-block-myplugin`, `plugin-local-helloworld`
  - **Without PHP**: `plugin-local-pyrunner`, `plugin-local-python-cortex`
- **Architecture Understanding**: Analyzed deployment patterns and requirements

---

## Technical Configuration Details

### Snowflake Connection Configuration
```toml
[connections.Natwest_Delimiters]
account = "[ACCOUNT_ID]"
user = "[USERNAME]"
password = "[PASSWORD_REDACTED]"
role = "ACCOUNTADMIN"
warehouse = "<none selected>"
database = "MOODLE_APP"
schema = "PUBLIC"
```

### Database Setup Commands Executed
```sql
-- Role and permissions setup
CREATE ROLE IF NOT EXISTS MOODLE_ROLE;
GRANT ROLE MOODLE_ROLE TO USER sukjain;
USE ROLE SECURITYADMIN;
GRANT DATABASE ROLE SNOWFLAKE.CORTEX_USER TO ROLE MOODLE_ROLE;

-- Database and infrastructure setup
USE ROLE MOODLE_ROLE;
CREATE DATABASE IF NOT EXISTS MOODLE_APP;
USE DATABASE MOODLE_APP;
CREATE IMAGE REPOSITORY IF NOT EXISTS MOODLE_APP.PUBLIC.IMG;
CREATE STAGE IF NOT EXISTS MOODLE_APP.PUBLIC.MOUNTED;
```

### CLI Testing Results
```bash
# Connection test successful
SELECT CURRENT_ROLE(), CURRENT_DATABASE(), CURRENT_SCHEMA();
# Result: MOODLE_ROLE | MOODLE_APP | PUBLIC
```

---

## Project Structure

```
learnaix-h-2025-delimiters/
├── README.md                          # Updated hackathon submission template
├── SETUP_DOCUMENTATION.md             # This documentation file
├── HACK4ACAUSE-TEMPLATE_README.md     # Original submission guidelines
├── GETTING_STARTED.md                 # Git and DCO setup instructions
├── example/
│   ├── plugin-development-templates/
│   │   ├── with-php/                  # PHP-based plugin templates
│   │   │   ├── plugin-local-cortexanalyst/
│   │   │   ├── plugin-block-myplugin/
│   │   │   └── plugin-local-helloworld/
│   │   └── without-php/               # Python-based plugin templates
│   │       ├── plugin-local-pyrunner/
│   │       └── plugin-local-python-cortex/
│   └── moodle-local-setup/            # Local Moodle setup guides
└── assets/                            # Project assets
```

---

## Next Steps (Ready to Execute)

### Immediate Tasks
1. **Define AI Plugin Concept** (In Progress)
   - Choose between: AI Study Assistant, Smart Content Analyzer, Learning Progress Predictor, or Automated Quiz Generator
   
2. **Select Template** (Pending)
   - Recommended: `plugin-local-cortexanalyst` for AI Study Assistant
   - Alternative: `plugin-local-python-cortex` for advanced AI features

3. **Development Phase** (Pending)
   - Customize chosen template
   - Implement AI functionality using Snowflake Cortex
   - Create user interface and interaction flow

4. **Deployment** (Pending)
   - Deploy to Snowflake using containers/services
   - Configure live demo URL
   - Test end-to-end functionality

### Recommended Plugin Choice
**AI Study Assistant** using `plugin-local-cortexanalyst` template:
- **Fastest development time**: 2-3 hours
- **High impact**: Students can ask questions and get AI-powered explanations
- **Proven technology**: Uses Snowflake Cortex AI (already configured)
- **Clear demo value**: Easy to showcase and understand

---

## Development Environment Status

| Component | Status | Details |
|-----------|--------|---------|
| Git Repository | Ready | Initial commit completed |
| Snowflake Account | Active | 30-day trial with $400 credits |
| CLI Tools | Configured | Snowflake CLI v3.11.0.0 working |
| Database | Ready | MOODLE_APP database accessible |
| AI Permissions | Granted | Cortex AI access configured |
| Templates | Available | 5 plugin templates analyzed |
| Team Setup | Complete | All team members documented |

---

## Support Information

**Primary Contact**: prasukj123@gmail.com  
**Snowflake Account**: [CONFIGURED]  
**Project Repository**: https://github.com/finos-labs/learnaix-h-2025-delimiters  

---

## Ready for Development!

All infrastructure and setup tasks are complete. The team is ready to proceed with AI plugin development and deployment on Snowflake platform.

**Total Setup Time**: ~2 hours  
**Status**: READY TO BUILD  
**Next Action**: Choose AI plugin concept and begin development
