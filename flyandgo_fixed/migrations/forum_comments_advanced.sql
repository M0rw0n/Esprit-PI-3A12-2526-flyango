-- Migration: Forum Advanced Comments System
-- Date: 2026-04-06
-- Description: Add nested comments support, votes, and scores

-- Add new columns to forum_comment
ALTER TABLE `forum_comment` 
ADD COLUMN IF NOT EXISTS `parent_id` INT NULL DEFAULT NULL AFTER `post_id`,
ADD COLUMN IF NOT EXISTS `score` INT NOT NULL DEFAULT 0 AFTER `content`,
ADD COLUMN IF NOT EXISTS `likes` INT NOT NULL DEFAULT 0 AFTER `score`,
ADD COLUMN IF NOT EXISTS `dislikes` INT NOT NULL DEFAULT 0 AFTER `likes`,
ADD COLUMN IF NOT EXISTS `is_pinned` TINYINT(1) NOT NULL DEFAULT 0 AFTER `dislikes`;

-- Add foreign key for parent_id (self-referencing)
-- Note: Execute only if foreign key doesn't exist
ALTER TABLE `forum_comment`
ADD CONSTRAINT IF NOT EXISTS `fk_comment_parent`
FOREIGN KEY (`parent_id`) REFERENCES `forum_comment`(`id`) ON DELETE CASCADE;

-- Add indexes for better query performance
ALTER TABLE `forum_comment`
ADD INDEX IF NOT EXISTS `idx_parent` (`parent_id`),
ADD INDEX IF NOT EXISTS `idx_post_score` (`post_id`, `score`),
ADD INDEX IF NOT EXISTS `idx_post_created` (`post_id`, `created_at`);

-- Add TYPE_COMMENT to like_dislike if needed
-- (TYPE_POST and TYPE_ACTIVITY should already exist)
