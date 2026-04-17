<?php
/**
 * Diagnostic script to check reclamation data
 * Visit: http://localhost/projet/kmr/student/debug_reclamations.php
 */

require_once __DIR__ . '/backend/includes/bootstrap.php';
require_auth();

$userId = (string) user_id();
$pdo = db();

echo "<pre style='background:#f5f5f5; padding:20px; font-family:monospace;'>";
echo "User ID: " . htmlspecialchars($userId) . "\n\n";

// Check reclamations
echo "=== RECLAMATIONS TABLE ===\n";
try {
    $stmt = $pdo->prepare("SELECT id, user_id, subject, created_at FROM reclamations WHERE user_id = :uid ORDER BY created_at DESC LIMIT 5");
    $stmt->execute(['uid' => $userId]);
    $recs = $stmt->fetchAll();
    echo "Count: " . count($recs) . "\n";
    foreach ($recs as $r) {
        echo "  ID: " . $r['id'] . " | Subject: " . htmlspecialchars($r['subject']) . " | Date: " . $r['created_at'] . "\n";
    }
} catch (Throwable $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}

echo "\n=== SUPPORT_THREADS TABLE ===\n";
try {
    $stmt = $pdo->prepare("SELECT id, user_id, user_type, subject, created_at FROM support_threads WHERE user_id = :uid ORDER BY created_at DESC LIMIT 5");
    $stmt->execute(['uid' => $userId]);
    $threads = $stmt->fetchAll();
    echo "Count: " . count($threads) . "\n";
    foreach ($threads as $t) {
        echo "  Thread ID: " . $t['id'] . " | Type: " . $t['user_type'] . " | Subject: " . htmlspecialchars($t['subject']) . " | Date: " . $t['created_at'] . "\n";
    }
} catch (Throwable $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}

echo "\n=== SUPPORT_MESSAGES TABLE ===\n";
try {
    // Get all messages from user's threads
    $stmt = $pdo->prepare("
        SELECT sm.id, sm.thread_id, sm.sender, sm.message, sm.created_at 
        FROM support_messages sm
        INNER JOIN support_threads st ON st.id = sm.thread_id
        WHERE st.user_id = :uid
        ORDER BY sm.created_at DESC
        LIMIT 10
    ");
    $stmt->execute(['uid' => $userId]);
    $msgs = $stmt->fetchAll();
    echo "Count: " . count($msgs) . "\n";
    foreach ($msgs as $m) {
        echo "  Thread: " . $m['thread_id'] . " | From: " . $m['sender'] . " | Message: " . substr(htmlspecialchars($m['message']), 0, 40) . "... | Date: " . $m['created_at'] . "\n";
    }
} catch (Throwable $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}

echo "\n=== TABLE STRUCTURE CHECK ===\n";
try {
    $cols = $pdo->query("DESCRIBE support_messages")->fetchAll(PDO::FETCH_COLUMN, 0);
    echo "support_messages columns: " . implode(", ", $cols) . "\n";
    if (in_array('thread_id', $cols)) {
        echo "✓ thread_id column exists (correct structure)\n";
    } else {
        echo "✗ thread_id column MISSING (wrong structure)\n";
    }
} catch (Throwable $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}

echo "</pre>";
?>
