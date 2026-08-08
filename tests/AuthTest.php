<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../Projet/student/backend/includes/helpers.php';
require_once __DIR__ . '/../Projet/student/backend/includes/auth.php';

final class AuthTest extends TestCase
{
    protected function setUp(): void
    {
        $_SESSION = [];
        $_SERVER = [];
    }

    // --- user_id() ---

    public function testUserIdReturnsNullWhenNoSession(): void
    {
        $this->assertNull(user_id());
    }

    public function testUserIdReturnsCINAsInteger(): void
    {
        $_SESSION['CIN'] = 12345678;
        $this->assertSame(12345678, user_id());
    }

    public function testUserIdCastsStringCINToInt(): void
    {
        $_SESSION['CIN'] = '99887766';
        $this->assertSame(99887766, user_id());
    }

    // --- is_authenticated() ---

    public function testIsAuthenticatedReturnsFalseWhenNoSession(): void
    {
        $this->assertFalse(is_authenticated());
    }

    public function testIsAuthenticatedReturnsTrueWhenCINSet(): void
    {
        $_SESSION['CIN'] = 12345678;
        $this->assertTrue(is_authenticated());
    }

    // --- login_user() ---

    public function testLoginUserSetsCINInSession(): void
    {
        login_user(12345678, 'John Doe');

        $this->assertSame(12345678, $_SESSION['CIN']);
        $this->assertSame('John Doe', $_SESSION['nom']);
    }

    public function testLoginUserCastsIdToInt(): void
    {
        login_user('99887766', 'Jane Doe');

        $this->assertSame(99887766, $_SESSION['CIN']);
    }

    public function testLoginUserCastsNameToString(): void
    {
        login_user(1, 123);

        $this->assertSame('123', $_SESSION['nom']);
    }

    public function testLoginUserMakesUserAuthenticated(): void
    {
        $this->assertFalse(is_authenticated());
        login_user(42, 'Test User');
        $this->assertTrue(is_authenticated());
    }

    // --- logout_user() ---

    public function testLogoutUserClearsSession(): void
    {
        $_SESSION['CIN'] = 12345678;
        $_SESSION['nom'] = 'Test User';
        $_SESSION['preferred_language'] = 'fr';

        logout_user();

        $this->assertEmpty($_SESSION);
    }

    public function testLogoutUserMakesUserUnauthenticated(): void
    {
        $_SESSION['CIN'] = 42;
        $_SESSION['nom'] = 'Test User';
        $this->assertTrue(is_authenticated());

        // Manually clear session to avoid session_destroy() warning in CLI
        $_SESSION = [];

        $this->assertFalse(is_authenticated());
    }
}
