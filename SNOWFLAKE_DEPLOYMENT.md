# 🏔️ Snowflake Deployment Configuration

## LearnPath Navigator - Snowflake Setup Guide

### **Environment Configuration**

#### **1. Snowflake Connection Settings**
```bash
# Default Snowflake paths for cloud deployment
SNOWFLAKE_CLI_PATH=/usr/local/bin/snow
SNOWFLAKE_CONNECTION=LEARNAIX_CONNECTION
SNOWFLAKE_CONFIG_PATH=/opt/snowflake/config
```

#### **2. Required Snowflake Setup**
```sql
-- Create connection for LearnAIx platform
CREATE CONNECTION LEARNAIX_CONNECTION
  TYPE = 'SNOWFLAKE'
  ACCOUNT = 'your-account.snowflakecomputing.com'
  USER = 'your-username'
  PASSWORD = 'your-password'
  DATABASE = 'LEARNAIX_DB'
  SCHEMA = 'PUBLIC'
  WAREHOUSE = 'LEARNAIX_WH';
```

#### **3. AI Model Configuration**
```sql
-- Available Cortex models for LearnPath Navigator
SELECT SNOWFLAKE.CORTEX.COMPLETE('mistral-7b', 'Test prompt') as test_response;
SELECT SNOWFLAKE.CORTEX.COMPLETE('llama3.1-8b', 'Test prompt') as test_response;
SELECT SNOWFLAKE.CORTEX.COMPLETE('mixtral-8x7b', 'Test prompt') as test_response;
```

### **Deployment Steps**

#### **Step 1: Upload Plugin to Snowflake**
```bash
# Upload Moodle plugin files
snow stage put moodle-plugin/local/learnpath/* @LEARNAIX_STAGE/plugins/
```

#### **Step 2: Configure Environment Variables**
```bash
# Set Snowflake environment
export SNOWFLAKE_HOME=/opt/snowflake/config
export SNOWFLAKE_CONNECTION=LEARNAIX_CONNECTION
```

#### **Step 3: Test AI Integration**
```bash
# Test Cortex API connectivity
snow sql -c LEARNAIX_CONNECTION -q "SELECT SNOWFLAKE.CORTEX.COMPLETE('mistral-7b', 'Hello World') as test"
```

### **Plugin Configuration for Cloud**

#### **Moodle Settings (Admin Panel)**
```
Site Administration > Plugins > Local plugins > LearnPath Navigator

Snowflake CLI Path: /usr/local/bin/snow
Snowflake Connection: LEARNAIX_CONNECTION  
Snowflake Config Path: /opt/snowflake/config
AI Model: mistral-7b
Enable AI Features: ✓ Enabled
```

#### **File Permissions**
```bash
# Ensure proper permissions for Snowflake CLI
chmod +x /usr/local/bin/snow
chown moodle:moodle /opt/snowflake/config
```

### **Testing Deployment**

#### **1. Connection Test**
```php
// Test Snowflake connection
$connector = new \local_learnpath\snowflake_connector();
$result = $connector->test_connection();
```

#### **2. AI Query Test**
```php
// Test AI functionality
$response = $connector->execute_cortex_query('mistral-7b', 'Generate learning roadmap');
```

#### **3. Plugin Access**
```
URL: https://your-moodle-instance/local/learnpath/
Expected: LearnPath Navigator dashboard loads successfully
```

### **Environment Variables for Production**

```bash
# Production Snowflake Configuration
SNOWFLAKE_ACCOUNT=learnaix-prod.snowflakecomputing.com
SNOWFLAKE_USER=learnaix_service
SNOWFLAKE_DATABASE=LEARNAIX_PRODUCTION
SNOWFLAKE_WAREHOUSE=LEARNAIX_COMPUTE_WH
SNOWFLAKE_SCHEMA=MOODLE_PLUGINS
```

### **Security Configuration**

#### **1. Connection Security**
```sql
-- Use key-pair authentication for production
ALTER USER learnaix_service SET RSA_PUBLIC_KEY='your-public-key';
```

#### **2. Network Policies**
```sql
-- Restrict access to LearnAIx platform IPs
CREATE NETWORK POLICY learnaix_policy
  ALLOWED_IP_LIST = ('learnaix-platform-ip-range');
```

### **Monitoring & Logging**

#### **1. Query Monitoring**
```sql
-- Monitor Cortex API usage
SELECT * FROM SNOWFLAKE.ACCOUNT_USAGE.QUERY_HISTORY 
WHERE QUERY_TEXT LIKE '%CORTEX.COMPLETE%'
ORDER BY START_TIME DESC;
```

#### **2. Performance Metrics**
```sql
-- Track AI response times
SELECT 
    AVG(TOTAL_ELAPSED_TIME) as avg_response_time,
    COUNT(*) as total_queries
FROM SNOWFLAKE.ACCOUNT_USAGE.QUERY_HISTORY 
WHERE QUERY_TEXT LIKE '%CORTEX%';
```

### **Troubleshooting**

#### **Common Issues:**
1. **Connection Failed**: Check LEARNAIX_CONNECTION configuration
2. **CLI Not Found**: Verify `/usr/local/bin/snow` path
3. **AI Timeout**: Increase query timeout in Snowflake settings
4. **Permission Denied**: Check file permissions for config directory

#### **Debug Commands:**
```bash
# Test CLI connectivity
snow connection test LEARNAIX_CONNECTION

# Check configuration
snow config list

# Verify AI models
snow sql -c LEARNAIX_CONNECTION -q "SHOW FUNCTIONS LIKE 'CORTEX%'"
```

---

**Deployment Status**: ✅ Ready for Snowflake Cloud Deployment  
**Team**: delimiters - NatWest Hack4aCause 2025  
**Plugin**: LearnPath Navigator v1.0
