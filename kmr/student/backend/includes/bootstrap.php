<?php

require_once __DIR__ . '/session.php';
require_once __DIR__ . '/helpers.php';
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/../../database/database.php';

if (!isset($_SESSION['preferred_language'])) {
	$_SESSION['preferred_language'] = 'fr';
}

if (is_authenticated()) {
	try {
		$needSync = !isset($_SESSION['preferred_language_synced']) || $_SESSION['preferred_language_synced'] !== true;
		if ($needSync) {
			$pdo = db();
			$stmt = $pdo->prepare('SELECT preferred_language FROM etudiant WHERE CIN = :CIN LIMIT 1');
			$stmt->execute(['CIN' => user_id()]);

			$lang = (string) ($stmt->fetchColumn() ?: 'en');
			if ($lang !== 'en' && $lang !== 'fr') {
				$lang = 'en';
			}

			$_SESSION['preferred_language'] = $lang;
			$_SESSION['preferred_language_synced'] = true;
		}
	} catch (Throwable $e) {
		// Keep current session language if DB sync fails.
	}
}

if (
	!defined('APP_TRANSLATION_BUFFER_ACTIVE')
	&& (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'GET')
) {
	define('APP_TRANSLATION_BUFFER_ACTIVE', true);
	ob_start('translate_output_by_language');
}
