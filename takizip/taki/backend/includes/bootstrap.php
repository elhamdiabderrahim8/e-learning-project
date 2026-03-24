<?php

declare(strict_types=1);

require_once __DIR__ . '/session.php';
require_once __DIR__ . '/helpers.php';
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/../../supabase/database.php';

if (!isset($_SESSION['preferred_language'])) {
	$_SESSION['preferred_language'] = 'en';
}

if (is_authenticated()) {
	try {
		$pdo = db();
		$pdo->exec("ALTER TABLE users ADD COLUMN IF NOT EXISTS preferred_language VARCHAR(10) NOT NULL DEFAULT 'en'");

		$stmt = $pdo->prepare('SELECT preferred_language FROM users WHERE id = :id LIMIT 1');
		$stmt->execute(['id' => user_id()]);
		$lang = (string) ($stmt->fetchColumn() ?: 'en');

		if (in_array($lang, ['en', 'fr'], true)) {
			$_SESSION['preferred_language'] = $lang;
		}
	} catch (Throwable $e) {
		// Ignore DB sync failures and keep session value.
	}
}

if (!defined('APP_TRANSLATION_BUFFER_ACTIVE') && current_language() === 'en') {
	define('APP_TRANSLATION_BUFFER_ACTIVE', true);
	ob_start('translate_output_to_english');
}
