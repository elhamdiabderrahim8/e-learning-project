<?php

declare(strict_types=1);

/**
 * Initialize support chat tables if they don't exist.
 * Call this once per app startup via bootstrap.
 */

function ensure_support_tables(): void
{
    try {
        $pdo = db();

        // Check if support_threads table exists
        $threadTable = $pdo->query("SHOW TABLES LIKE 'support_threads'")->fetchColumn();
        if (!$threadTable) {
            $pdo->exec(
                "CREATE TABLE `support_threads` (
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
        }

        // Check if support_messages table exists
        $msgTable = $pdo->query("SHOW TABLES LIKE 'support_messages'")->fetchColumn();
        if (!$msgTable) {
            $pdo->exec(
                "CREATE TABLE `support_messages` (
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
        }
    } catch (Throwable $e) {
        // Silent fail: tables may already exist or PDO connection is not ready.
    }
}
