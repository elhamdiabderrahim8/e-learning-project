<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../Projet/student/backend/includes/helpers.php';

final class HelpersTest extends TestCase
{
    protected function setUp(): void
    {
        $_SESSION = [];
    }

    // --- current_language() ---

    public function testCurrentLanguageDefaultsToEnWhenNoSession(): void
    {
        $this->assertSame('en', current_language());
    }

    public function testCurrentLanguageReturnsFrWhenSetToFr(): void
    {
        $_SESSION['preferred_language'] = 'fr';
        $this->assertSame('fr', current_language());
    }

    public function testCurrentLanguageReturnsEnWhenSetToEn(): void
    {
        $_SESSION['preferred_language'] = 'en';
        $this->assertSame('en', current_language());
    }

    public function testCurrentLanguageFallsBackToEnForInvalidLanguage(): void
    {
        $_SESSION['preferred_language'] = 'de';
        $this->assertSame('en', current_language());
    }

    public function testCurrentLanguageFallsBackToEnForEmptyString(): void
    {
        $_SESSION['preferred_language'] = '';
        $this->assertSame('en', current_language());
    }

    public function testCurrentLanguageFallsBackToEnForNumericValue(): void
    {
        $_SESSION['preferred_language'] = 42;
        $this->assertSame('en', current_language());
    }

    // --- lang_text() ---

    public function testLangTextReturnsEnglishWhenLanguageIsEn(): void
    {
        $_SESSION['preferred_language'] = 'en';
        $this->assertSame('Hello', lang_text('Bonjour', 'Hello'));
    }

    public function testLangTextReturnsFrenchWhenLanguageIsFr(): void
    {
        $_SESSION['preferred_language'] = 'fr';
        $this->assertSame('Bonjour', lang_text('Bonjour', 'Hello'));
    }

    public function testLangTextReturnsEnglishByDefault(): void
    {
        $this->assertSame('Hello', lang_text('Bonjour', 'Hello'));
    }

    public function testLangTextCastsNonStringArgs(): void
    {
        $_SESSION['preferred_language'] = 'en';
        $this->assertSame('123', lang_text(456, 123));
    }

    // --- set_flash / get_flash ---

    public function testSetFlashIsDisabledAndReturnsVoid(): void
    {
        $result = set_flash('error', 'Some error');
        $this->assertNull($result);
    }

    public function testGetFlashReturnsNullAlways(): void
    {
        set_flash('error', 'Some error');
        $this->assertNull(get_flash('error'));
    }

    // --- app_translation_map() ---

    public function testAppTranslationMapReturnsArray(): void
    {
        $map = app_translation_map();
        $this->assertIsArray($map);
        $this->assertNotEmpty($map);
    }

    public function testAppTranslationMapContainsKnownKeys(): void
    {
        $map = app_translation_map();
        $this->assertArrayHasKey('Connexion', $map);
        $this->assertSame('Login', $map['Connexion']);
    }

    public function testAppTranslationMapContainsLanguageSwitch(): void
    {
        $map = app_translation_map();
        $this->assertArrayHasKey('<html lang="fr">', $map);
        $this->assertSame('<html lang="en">', $map['<html lang="fr">']);
    }

    public function testAppTranslationMapIsCachedAcrossCalls(): void
    {
        $map1 = app_translation_map();
        $map2 = app_translation_map();
        $this->assertSame($map1, $map2);
    }

    // --- translate_output_by_language() ---

    public function testTranslateOutputReplacesWhenEnglish(): void
    {
        $_SESSION['preferred_language'] = 'en';
        $input = 'Connexion';
        $result = translate_output_by_language($input);
        $this->assertSame('Login', $result);
    }

    public function testTranslateOutputKeepsFrenchContentWhenFr(): void
    {
        $_SESSION['preferred_language'] = 'fr';
        $input = 'Connexion';
        $result = translate_output_by_language($input);
        $this->assertSame('Connexion', $result);
    }

    public function testTranslateOutputHandlesEmptyString(): void
    {
        $_SESSION['preferred_language'] = 'en';
        $this->assertSame('', translate_output_by_language(''));
    }

    public function testTranslateOutputReplacesMultipleOccurrences(): void
    {
        $_SESSION['preferred_language'] = 'en';
        $input = 'Connexion - Mes Cours';
        $result = translate_output_by_language($input);
        $this->assertStringContainsString('Login', $result);
        $this->assertStringContainsString('My Courses', $result);
    }

    public function testTranslateOutputLeavesUnknownTextUntouched(): void
    {
        $_SESSION['preferred_language'] = 'en';
        $input = 'This is some random text';
        $this->assertSame('This is some random text', translate_output_by_language($input));
    }

    public function testTranslateOutputCastsInputToString(): void
    {
        $_SESSION['preferred_language'] = 'en';
        $result = translate_output_by_language(12345);
        $this->assertSame('12345', $result);
    }

    public function testTranslateOutputHandlesHtmlContent(): void
    {
        $_SESSION['preferred_language'] = 'en';
        $input = '<html lang="fr">';
        $result = translate_output_by_language($input);
        $this->assertSame('<html lang="en">', $result);
    }
}
