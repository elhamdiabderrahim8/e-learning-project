<?php

declare(strict_types=1);

/**
 * Migrate support tables to new chat schema (idempotent).
 * Only recreates tables if they don't have the correct structure.
 */

function migrate_support_tables(): void
{
    try {
        $pdo = db();

        // Check if support_threads exists with correct structure
        $threadTableExists = false;
        $threadTableValid = false;

        try {
            $result = $pdo->query("DESCRIBE `support_threads`");
            $threadTableExists = true;
            $cols = $result->fetchAll(PDO::FETCH_COLUMN, 0);
            // Check for expected columns
            $threadTableValid = in_array('id', $cols) && in_array('user_id', $cols)
                && in_array('user_type', $cols) && in_array('subject', $cols);
        } catch (Throwable $e) {
            // Table doesn't exist
        }

        // Check if support_messages exists with correct structure
        $msgTableExists = false;
        $msgTableValid = false;

        try {
            $result = $pdo->query("DESCRIBE `support_messages`");
            $msgTableExists = true;
            $cols = $result->fetchAll(PDO::FETCH_COLUMN, 0);
            // Check for thread_id (not student_cin)
            $msgTableValid = in_array('thread_id', $cols) && in_array('sender', $cols)
                && in_array('message', $cols);
        } catch (Throwable $e) {
            // Table doesn't exist
        }

        // Only migrate if tables don't exist or have wrong structure
        if (!$threadTableValid || !$msgTableValid) {
            // Drop old tables if they exist with wrong structure
            if ($threadTableExists && !$threadTableValid) {
                $pdo->exec("DROP TABLE IF EXISTS `support_messages`");
                $pdo->exec("DROP TABLE IF EXISTS `support_threads`");
            }
            if ($msgTableExists && !$msgTableValid) {
                $pdo->exec("DROP TABLE IF EXISTS `support_messages`");
            }

            // Create correct support_threads table
            $pdo->exec(
                "CREATE TABLE IF NOT EXISTS `support_threads` (
                    `id` INT(11) NOT NULL AUTO_INCREMENT,
                    `user_id` VARCHAR(50) NOT NULL,
                    `user_type` ENUM('etudiant','professeur') NOT NULL,
                    `user_name` VARCHAR(150) NOT NULL,
                    `subject` VARCHAR(255) NOT NULL DEFAULT 'Support',
                    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
                    PRIMARY KEY (`id`),
                    KEY `idx_user_id` (`user_id`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4"
            );

            // Create correct support_messages table
            $pdo->exec(
                "CREATE TABLE IF NOT EXISTS `support_messages` (
                    `id` INT(11) NOT NULL AUTO_INCREMENT,
                    `thread_id` INT(11) NOT NULL,
                    `sender` VARCHAR(50) NOT NULL,
                    `message` LONGTEXT NOT NULL,
                    `admin_read` TINYINT(1) NOT NULL DEFAULT 0,
                    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
                    PRIMARY KEY (`id`),
                    KEY `idx_thread_id` (`thread_id`),
                    CONSTRAINT `fk_sm_thread` FOREIGN KEY (`thread_id`) REFERENCES `support_threads` (`id`) ON DELETE CASCADE
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4"
            );

            error_log('Support tables migrated successfully.');
        }
    } catch (Throwable $e) {
        error_log('Migration error: ' . $e->getMessage());
        throw $e;
    }
}
