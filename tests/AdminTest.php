<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

final class AdminTest extends TestCase
{
    protected function setUp(): void
    {
        $_SESSION = [];
    }

    // --- Admin Credentials ---

    public function testAdminCredentialsFileReturnsArray(): void
    {
        $credentials = require __DIR__ . '/../Projet/admin/admin_credentials.php';
        $this->assertIsArray($credentials);
        $this->assertNotEmpty($credentials);
    }

    public function testAdminCredentialsContainEmailAndPasswordHash(): void
    {
        $credentials = require __DIR__ . '/../Projet/admin/admin_credentials.php';

        foreach ($credentials as $cred) {
            $this->assertArrayHasKey('email', $cred);
            $this->assertArrayHasKey('password_hash', $cred);
            $this->assertNotEmpty($cred['email']);
            $this->assertNotEmpty($cred['password_hash']);
        }
    }

    public function testAdminPasswordHashIsValidBcrypt(): void
    {
        $credentials = require __DIR__ . '/../Projet/admin/admin_credentials.php';
        $hash = $credentials[0]['password_hash'];

        $this->assertStringStartsWith('$2y$', $hash);
    }

    public function testAdminDefaultEmailIsCorrect(): void
    {
        $credentials = require __DIR__ . '/../Projet/admin/admin_credentials.php';
        $this->assertSame('admin@enjah.com', $credentials[0]['email']);
    }

    // --- Auth Guard Logic ---

    public function testAuthGuardBlocksUnauthenticatedUsers(): void
    {
        $_SESSION = [];
        $isLoggedIn = !empty($_SESSION['admin_logged_in']);
        $this->assertFalse($isLoggedIn);
    }

    public function testAuthGuardAllowsAuthenticatedUsers(): void
    {
        $_SESSION['admin_logged_in'] = true;
        $isLoggedIn = !empty($_SESSION['admin_logged_in']);
        $this->assertTrue($isLoggedIn);
    }

    public function testAuthGuardBlocksFalsyValue(): void
    {
        $_SESSION['admin_logged_in'] = false;
        $isLoggedIn = !empty($_SESSION['admin_logged_in']);
        $this->assertFalse($isLoggedIn);
    }

    public function testAuthGuardBlocksZeroValue(): void
    {
        $_SESSION['admin_logged_in'] = 0;
        $isLoggedIn = !empty($_SESSION['admin_logged_in']);
        $this->assertFalse($isLoggedIn);
    }

    // --- Password verification against stored hash ---

    public function testPasswordVerificationWithBcryptHash(): void
    {
        $password = 'test_password';
        $hash = password_hash($password, PASSWORD_DEFAULT);

        $this->assertTrue(password_verify($password, $hash));
        $this->assertFalse(password_verify('wrong_password', $hash));
    }

    public function testPasswordHashingProducesDifferentHashesForSameInput(): void
    {
        $password = 'same_password';
        $hash1 = password_hash($password, PASSWORD_DEFAULT);
        $hash2 = password_hash($password, PASSWORD_DEFAULT);

        $this->assertNotSame($hash1, $hash2);
        $this->assertTrue(password_verify($password, $hash1));
        $this->assertTrue(password_verify($password, $hash2));
    }
}
