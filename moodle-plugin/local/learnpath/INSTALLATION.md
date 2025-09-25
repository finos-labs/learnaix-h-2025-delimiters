# LearnPath Navigator - Installation Guide

## Prerequisites

1. **Moodle 4.0+** installed and running
2. **PHP 7.4+** with required extensions
3. **Snowflake CLI** installed on the server
4. **Snowflake account** with Cortex AI access

## Step-by-Step Installation

### 1. Download and Extract Plugin

```bash
# Clone or download the plugin
git clone https://github.com/finos-labs/learnaix-h-2025-delimiters.git
cd learnaix-h-2025-delimiters/moodle-plugin/local/
```

### 2. Copy to Moodle Directory

```bash
# Copy the learnpath directory to your Moodle local plugins directory
cp -r learnpath/ /path/to/your/moodle/local/

# Set proper permissions (Linux/macOS)
chown -R www-data:www-data /path/to/your/moodle/local/learnpath/
chmod -R 755 /path/to/your/moodle/local/learnpath/
```

### 3. Install via Moodle Admin

1. Log in to Moodle as an administrator
2. Navigate to **Site Administration → Notifications**
3. Moodle will detect the new plugin
4. Click **Install** to complete the installation

### 4. Configure Snowflake Connection

1. Go to **Site Administration → Plugins → Local plugins → LearnPath Navigator**
2. Configure these settings:

   | Setting | Description | Example |
   |---------|-------------|---------|
   | Snowflake CLI Path | Path to snow executable | `snow` or `/usr/local/bin/snow` |
   | Snowflake Connection | Your connection name | `my_connection` |
   | Snowflake Config Path | Config directory (optional) | `/home/user/.snowflake` |
   | AI Model | Preferred AI model | `mistral-7b` |
   | Enable AI Features | Enable/disable AI | ✓ Checked |

### 5. Set Up Snowflake CLI

Create a Snowflake CLI configuration file:

```bash
# Initialize Snowflake CLI
snow connection add

# Or manually create config file
mkdir -p ~/.snowflake
cat > ~/.snowflake/config.toml << EOF
[connections.default]
account = "your_account_id"
user = "your_username"
password = "your_password"
role = "your_role"
database = "your_database"
schema = "PUBLIC"
warehouse = "your_warehouse"
EOF
```

### 6. Configure Snowflake Permissions

Run these SQL commands in Snowflake:

```sql
-- Enable cross-region Cortex calls
USE ROLE ACCOUNTADMIN;
ALTER ACCOUNT SET ENABLE_CROSS_REGION_CORTEX_CALLS = TRUE;

-- Grant Cortex permissions to your role
GRANT DATABASE ROLE SNOWFLAKE.CORTEX_USER TO ROLE your_role;

-- Test Cortex access
SELECT SNOWFLAKE.CORTEX.COMPLETE('mistral-7b', 'Hello, how are you?') as test_response;
```

### 7. Test the Installation

1. Navigate to `/local/learnpath/` in your Moodle site
2. Try loading different student profiles
3. Test the AI chat functionality
4. Generate a learning roadmap

## Platform-Specific Notes

### Windows (XAMPP/WAMP)

```bash
# Copy plugin
copy learnpath C:\xampp\htdocs\moodle\local\

# Snowflake CLI path might be:
# C:\Program Files\Snowflake CLI\snow.exe
```

### Linux (Apache/Nginx)

```bash
# Copy plugin
sudo cp -r learnpath /var/www/html/moodle/local/

# Set permissions
sudo chown -R www-data:www-data /var/www/html/moodle/local/learnpath/
sudo chmod -R 755 /var/www/html/moodle/local/learnpath/

# Snowflake CLI path might be:
# /usr/local/bin/snow
```

### macOS (MAMP)

```bash
# Copy plugin
cp -r learnpath /Applications/MAMP/htdocs/moodle/local/

# Snowflake CLI path might be:
# /usr/local/bin/snow
```

## Troubleshooting

### Plugin Not Appearing

1. Clear Moodle cache: **Site Administration → Development → Purge all caches**
2. Check file permissions
3. Verify plugin is in correct directory structure

### Snowflake Connection Issues

1. Test CLI manually: `snow sql -q "SELECT 1"`
2. Check connection configuration
3. Verify Snowflake account permissions

### AI Features Not Working

1. Enable debug mode in plugin settings
2. Check Snowflake Cortex permissions
3. Try different AI models
4. Verify cross-region calls are enabled

## Security Considerations

1. **Never commit** Snowflake credentials to version control
2. Use **environment variables** for sensitive configuration
3. Set proper **file permissions** on the server
4. Enable **HTTPS** for production deployments

## Next Steps

After successful installation:

1. Configure user permissions
2. Set up course integrations
3. Train users on the interface
4. Monitor AI usage and performance

## Support

For installation issues:
- Check the main README.md
- Review Moodle logs
- Contact: prasukj123@gmail.com
