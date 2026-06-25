<?php

declare(strict_types=1);

require_once __DIR__ . '/database.php';

function create_support_thread(
    string $userId,
    string $userType,
    string $userName,
    string $subject,
    string $message
): int {
    $pdo = db();
    $pdo->beginTransaction();

    try {
        $threadStmt = $pdo->prepare(
            'INSERT INTO support_threads (user_id, user_type, user_name, subject) VALUES (:user_id, :user_type, :user_name, :subject)'
        );
        $threadStmt->execute([
            'user_id'   => $userId,
            'user_type' => $userType,
            'user_name' => $userName,
            'subject'   => $subject,
        ]);

        $threadId = (int) $pdo->lastInsertId();

        $msgStmt = $pdo->prepare(
            'INSERT INTO support_messages (thread_id, sender, message, admin_read) VALUES (:thread_id, :sender, :message, :admin_read)'
        );
        $msgStmt->execute([
            'thread_id'  => $threadId,
            'sender'     => $userType,
            'message'    => $message,
            'admin_read' => 0,
        ]);

        $pdo->commit();

        return $threadId;
    } catch (\Throwable $e) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        throw $e;
    }
}
