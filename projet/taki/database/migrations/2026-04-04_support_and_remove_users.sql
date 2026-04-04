-- Migration: Support prototype + removal of `users` table
-- Date: 2026-04-04
--
-- Notes:
-- - Existing dumps may still contain `users` and foreign keys. This migration removes them.
-- - This project now authenticates students via `etudiant` (CIN) and professors via `professeur` (CIN).
-- - `tasks` / `reclamations` / `enrollments` / `certificates` keep `user_id` as a numeric identifier (CIN).

-- 1) Drop foreign keys that reference `users`
-- (These names match the constraints used in `elearning.sql`.)
ALTER TABLE certificates DROP FOREIGN KEY fk_certificates_user;
ALTER TABLE enrollments DROP FOREIGN KEY fk_enrollments_user;
ALTER TABLE reclamations DROP FOREIGN KEY fk_reclamations_user;
ALTER TABLE tasks DROP FOREIGN KEY fk_tasks_user;

-- 2) Remove the `users` table
DROP TABLE IF EXISTS users;

-- 3) Add Support prototype table
CREATE TABLE IF NOT EXISTS support_messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    student_cin INT NOT NULL,
    sender ENUM('student', 'admin') NOT NULL DEFAULT 'student',
    message TEXT NOT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    KEY idx_support_messages_student (student_cin)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

