<?php
/**
 * Ensures all required columns exist in the `inscription` table.
 * Compatible with all MySQL versions (uses INFORMATION_SCHEMA instead of IF NOT EXISTS).
 * Safe to call multiple times (idempotent).
 */
function ensure_inscription_columns(PDO $pdo): void
{
    $dbName = (string) $pdo->query("SELECT DATABASE()")->fetchColumn();

    $columns = [
        'paiement_valide'    => "TINYINT(1) NOT NULL DEFAULT 0",
        'card_holder'        => "VARCHAR(150) NULL",
        'card_last4'         => "CHAR(4) NULL",
        'payment_status_note'=> "VARCHAR(255) NULL",
    ];

    foreach ($columns as $col => $definition) {
        $exists = (int) $pdo->query(
            "SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
             WHERE TABLE_SCHEMA = '$dbName'
               AND TABLE_NAME   = 'inscription'
               AND COLUMN_NAME  = '$col'"
        )->fetchColumn();

        if (!$exists) {
            $pdo->exec("ALTER TABLE inscription ADD COLUMN $col $definition");
        }
    }
}
