<?php

declare(strict_types=1);

require_once __DIR__ . '/session.php';
require_once __DIR__ . '/helpers.php';
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/../../database/database.php';

if (!isset($_SESSION['preferred_language'])) {
	$_SESSION['preferred_language'] = 'en';
}

if (is_authenticated()) {
	try {
		if (!isset($_SESSION['preferred_language_synced']) || $_SESSION['preferred_language_synced'] !== true) {
			$pdo = db();
			$stmt = $pdo->prepare('SELECT preferred_language FROM users WHERE id = :id LIMIT 1');
			$stmt->execute(['id' => user_id()]);
			$lang = (string) ($stmt->fetchColumn() ?: 'en');

			if (in_array($lang, ['en', 'fr'], true)) {
				$_SESSION['preferred_language'] = $lang;
			}

			$_SESSION['preferred_language_synced'] = true;
		}
	} catch (Throwable $e) {
		// Ignore DB sync failures and keep session value.
	}
}

if (
	!defined('APP_TRANSLATION_BUFFER_ACTIVE')
	&& current_language() === 'en'
	&& (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'GET')
) {
	define('APP_TRANSLATION_BUFFER_ACTIVE', true);
	ob_start('translate_output_to_english');
}
