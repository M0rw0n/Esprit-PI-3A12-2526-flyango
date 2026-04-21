-- Fly&Go Share System SQL
-- Run this to create necessary tables for the sharing system

-- Add forum_post_id column to message table if not exists
ALTER TABLE message 
ADD COLUMN IF NOT EXISTS forum_post_id INT NULL
COMMENT 'ID of shared forum post';

-- Create index for faster queries
ALTER TABLE message 
ADD INDEX IF NOT EXISTS idx_message_forum_post (forum_post_id);

-- Grant permissions (run as admin if needed)
-- GRANT ALL PRIVILEGES ON your_database.* TO 'your_user'@'localhost';
-- FLUSH PRIVILEGES;
