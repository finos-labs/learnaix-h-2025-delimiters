@echo off
REM Quick Snowflake Setup for LearnPath Navigator
REM Team Delimiters - NatWest Hack4aCause

echo ========================================
echo LearnPath Navigator - Snowflake Setup
echo ========================================

echo Step 1: Installing Snowflake CLI...
winget install snowflake.cli
if %errorlevel% neq 0 (
    echo Failed to install Snowflake CLI
    pause
    exit /b 1
)

echo Step 2: Creating config directory...
mkdir "%USERPROFILE%\AppData\Local\snowflake" 2>nul

echo Step 3: Copying configuration template...
copy "snowflake_config.toml" "%USERPROFILE%\AppData\Local\snowflake\config.toml"

echo Step 4: Testing CLI installation...
snow --version
if %errorlevel% neq 0 (
    echo Snowflake CLI not properly installed
    pause
    exit /b 1
)

echo.
echo ========================================
echo NEXT STEPS:
echo ========================================
echo 1. Edit config file: %USERPROFILE%\AppData\Local\snowflake\config.toml
echo 2. Replace placeholders with your Snowflake credentials
echo 3. Run: snow connection test LEARNAIX_CONNECTION
echo 4. Execute: snow sql -c LEARNAIX_CONNECTION -f setup_database.sql
echo 5. Test AI: snow sql -c LEARNAIX_CONNECTION -q "SELECT SNOWFLAKE.CORTEX.COMPLETE('mistral-7b', 'Hello') as test"
echo ========================================

pause
