-- Snowflake Database Setup for LearnPath Navigator
-- Team Delimiters - NatWest Hack4aCause

-- Step 1: Create role and grant permissions
CREATE ROLE IF NOT EXISTS MOODLE_ROLE;
GRANT ROLE MOODLE_ROLE TO USER sukjain;

-- Step 2: Grant Cortex AI permissions (requires SECURITYADMIN role)
USE ROLE SECURITYADMIN;
GRANT DATABASE ROLE SNOWFLAKE.CORTEX_USER TO ROLE MOODLE_ROLE;

-- Step 3: Switch to MOODLE_ROLE and create database
USE ROLE MOODLE_ROLE;
CREATE DATABASE IF NOT EXISTS MOODLE_DB;
USE DATABASE MOODLE_DB;

-- Step 4: Create schema
CREATE SCHEMA IF NOT EXISTS MOODLE_SCHEMA;
USE SCHEMA MOODLE_SCHEMA;

-- Step 5: Create warehouse for compute
CREATE WAREHOUSE IF NOT EXISTS MOODLE_WH 
WITH 
    WAREHOUSE_SIZE = 'X-SMALL'
    AUTO_SUSPEND = 60
    AUTO_RESUME = TRUE
    MIN_CLUSTER_COUNT = 1
    MAX_CLUSTER_COUNT = 1;

-- Step 6: Use the warehouse
USE WAREHOUSE MOODLE_WH;

-- Step 7: Create image repository and stage for deployment
CREATE IMAGE REPOSITORY IF NOT EXISTS MOODLE_DB.PUBLIC.IMG;
CREATE STAGE IF NOT EXISTS MOODLE_DB.PUBLIC.MOUNTED;

-- Step 8: Create sample tables for LearnPath Navigator (optional)
CREATE TABLE IF NOT EXISTS STUDENT_PERFORMANCE (
    student_id VARCHAR(50),
    course_id VARCHAR(50),
    subject VARCHAR(100),
    score FLOAT,
    assessment_date TIMESTAMP_NTZ DEFAULT CURRENT_TIMESTAMP(),
    created_at TIMESTAMP_NTZ DEFAULT CURRENT_TIMESTAMP()
);

CREATE TABLE IF NOT EXISTS LEARNING_ROADMAPS (
    roadmap_id VARCHAR(50),
    student_id VARCHAR(50),
    generated_content TEXT,
    ai_model VARCHAR(50),
    created_at TIMESTAMP_NTZ DEFAULT CURRENT_TIMESTAMP()
);

-- Step 9: Test Cortex AI functionality
SELECT SNOWFLAKE.CORTEX.COMPLETE('mistral-7b', 'Generate a simple learning roadmap for mathematics') as ai_test;

-- Step 10: Verify setup
SELECT 
    CURRENT_ROLE() as current_role,
    CURRENT_DATABASE() as current_database,
    CURRENT_SCHEMA() as current_schema,
    CURRENT_WAREHOUSE() as current_warehouse,
    CURRENT_TIMESTAMP() as setup_time;

-- Step 11: Show available Cortex functions
SHOW FUNCTIONS LIKE 'SNOWFLAKE.CORTEX%';

-- Success message
SELECT 'LearnPath Navigator Snowflake setup completed successfully!' as status;
