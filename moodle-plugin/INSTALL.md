# LearnPath Navigator - Installation Guide

## 🚀 Quick Installation Steps

### 1. Copy Plugin Files
```bash
# Copy the entire 'learnpath' folder to your Moodle local plugins directory

# Windows (XAMPP)
copy /s "local\learnpath" "C:\xampp\htdocs\moodle\local\"

# Linux/Snowflake Cloud
cp -r local/learnpath /var/www/html/moodle/local/

# macOS (MAMP)
cp -r local/learnpath /Applications/MAMP/htdocs/moodle/local/
```

### 2. Install via Moodle Admin
1. Login as Moodle administrator
2. Go to **Site Administration → Notifications**
3. Click **Install** when prompted for LearnPath Navigator
4. Follow installation wizard

### 3. 🏔️ Snowflake Configuration

#### **Step 3.1: Create Snowflake Connection**
```sql
-- Execute in Snowflake console
CREATE CONNECTION LEARNAIX_CONNECTION
  TYPE = 'SNOWFLAKE'
  ACCOUNT = 'your-account.snowflakecomputing.com'
  USER = 'your-username'
  PASSWORD = 'your-password'
  DATABASE = 'LEARNAIX_DB'
  SCHEMA = 'PUBLIC'
  WAREHOUSE = 'LEARNAIX_WH';
```

#### **Step 3.2: Setup Snowflake CLI**

**For Local Development (Windows):**
```bash
# Install Snowflake CLI
# Download from: https://docs.snowflake.com/en/user-guide/snowsql-install-config

# Create config directory
mkdir "C:\Users\%USERNAME%\AppData\Local\snowflake"

# Configure connection
snow connection add LEARNAIX_CONNECTION \
  --account your-account.snowflakecomputing.com \
  --user your-username \
  --password your-password
```

**For Snowflake Cloud Deployment:**
```bash
# Create config directory
sudo mkdir -p /opt/snowflake/config
sudo chown moodle:moodle /opt/snowflake/config

# Install Snowflake CLI
sudo wget -O /usr/local/bin/snow https://snowflake-cli-releases.s3.amazonaws.com/snow
sudo chmod +x /usr/local/bin/snow

# Configure connection
export SNOWFLAKE_HOME=/opt/snowflake/config
snow connection add LEARNAIX_CONNECTION \
  --account learnaix.snowflakecomputing.com \
  --user learnaix_service \
  --authenticator SNOWFLAKE_JWT
```

#### **Step 3.3: Configure Plugin Settings**
Go to **Site Administration → Plugins → Local plugins → LearnPath Navigator**

**For Local Development:**
- CLI Path: `C:\Program Files\Snowflake CLI\snow.exe`
- Connection: `LEARNAIX_CONNECTION`
- Config Path: `C:\Users\%USERNAME%\AppData\Local\snowflake`
- AI Model: `mistral-7b`

**For Snowflake Cloud:**
- CLI Path: `/usr/local/bin/snow`
- Connection: `LEARNAIX_CONNECTION`
- Config Path: `/opt/snowflake/config`
- AI Model: `mistral-7b`

### 4. 🧪 Test Installation & Snowflake Connection

#### **Test Plugin Access:**
Visit: `https://yourmoodle.com/local/learnpath/`

#### **Test Snowflake Connection:**
```bash
# Test CLI connectivity
snow connection test LEARNAIX_CONNECTION

# Test AI functionality
snow sql -c LEARNAIX_CONNECTION -q "SELECT SNOWFLAKE.CORTEX.COMPLETE('mistral-7b', 'Hello World') as test"
```

#### **Expected Results:**
- ✅ Plugin dashboard loads successfully
- ✅ Student data displays properly
- ✅ AI roadmap generation works (or shows intelligent fallback)
- ✅ Study resources load dynamically

## File Structure
```
local/learnpath/
├── version.php              # Plugin metadata
├── index.php               # Main interface
├── ajax.php                # AJAX handler
├── settings.php            # Admin settings
├── README.md               # Documentation
├── db/
│   └── access.php          # Permissions
├── lang/en/
│   └── local_learnpath.php # Language strings
└── classes/
    ├── snowflake_connector.php # AI integration
    └── student_data.php        # Data management
```

## Verification Checklist
- [ ] Plugin appears in Site Administration → Plugins
- [ ] No errors in Moodle notifications
- [ ] Settings page accessible
- [ ] Main interface loads at /local/learnpath/
- [ ] Student data loads successfully
- [ ] AI analysis works (or shows intelligent fallback)

## Troubleshooting
- Clear Moodle cache if plugin doesn't appear
- Check file permissions (755 for directories, 644 for files)
- Verify Snowflake CLI is installed and configured
- Enable debugging in Moodle for detailed error messages
