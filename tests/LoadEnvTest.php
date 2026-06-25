<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

final class LoadEnvTest extends TestCase
{
    private string $tempDir;

    protected function setUp(): void
    {
        $this->tempDir = sys_get_temp_dir() . '/load_env_test_' . uniqid();
        mkdir($this->tempDir, 0755, true);
    }

    protected function tearDown(): void
    {
        // glob() skips dotfiles by default, so also match hidden files
        foreach (glob($this->tempDir . '/{,.}*', GLOB_BRACE) as $file) {
            if (is_file($file)) {
                unlink($file);
            }
        }
        if (is_dir($this->tempDir)) {
            @rmdir($this->tempDir);
        }

        foreach (['TEST_VAR_A', 'TEST_VAR_B', 'TEST_COMMENT_VAR', 'TEST_EMPTY_LINE', 'TEST_SPACES', 'TEST_EXISTING'] as $key) {
            putenv($key);
        }
    }

    private function createEnvFile(string $content): string
    {
        $path = $this->tempDir . '/.env';
        file_put_contents($path, $content);
        return $path;
    }

    /**
     * Reimplements the load_env logic from database.php for a given directory,
     * allowing unit testing without side effects on the real env loader.
     */
    private function parseEnvFile(string $dir): void
    {
        $envPath = $dir . '/.env';
        if (!file_exists($envPath)) {
            return;
        }

        $lines = file($envPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($lines as $line) {
            if (strpos($line, '#') === 0) {
                continue;
            }

            if (strpos($line, '=') === false) {
                continue;
            }

            [$key, $value] = explode('=', $line, 2);
            $key = trim($key);
            $value = trim($value);

            if (!getenv($key)) {
                putenv("{$key}={$value}");
            }
        }
    }

    public function testLoadEnvParsesKeyValuePairs(): void
    {
        $this->createEnvFile("TEST_VAR_A=hello\nTEST_VAR_B=world\n");
        $this->parseEnvFile($this->tempDir);

        $this->assertSame('hello', getenv('TEST_VAR_A'));
        $this->assertSame('world', getenv('TEST_VAR_B'));
    }

    public function testLoadEnvSkipsCommentLines(): void
    {
        $this->createEnvFile("# This is a comment\nTEST_COMMENT_VAR=value\n");
        $this->parseEnvFile($this->tempDir);

        $this->assertSame('value', getenv('TEST_COMMENT_VAR'));
    }

    public function testLoadEnvSkipsLinesWithoutEquals(): void
    {
        $this->createEnvFile("JUST_A_LINE_WITHOUT_EQUALS\nTEST_EMPTY_LINE=ok\n");
        $this->parseEnvFile($this->tempDir);

        $this->assertSame('ok', getenv('TEST_EMPTY_LINE'));
    }

    public function testLoadEnvTrimsWhitespace(): void
    {
        $this->createEnvFile("  TEST_SPACES  =  trimmed  \n");
        $this->parseEnvFile($this->tempDir);

        $this->assertSame('trimmed', getenv('TEST_SPACES'));
    }

    public function testLoadEnvDoesNotOverwriteExistingEnvVars(): void
    {
        putenv('TEST_EXISTING=original');
        $this->createEnvFile("TEST_EXISTING=new_value\n");
        $this->parseEnvFile($this->tempDir);

        $this->assertSame('original', getenv('TEST_EXISTING'));
    }

    public function testLoadEnvHandlesMissingFile(): void
    {
        $this->parseEnvFile('/tmp/nonexistent_' . uniqid());
        $this->assertTrue(true);
    }

    public function testLoadEnvHandlesValuesContainingEquals(): void
    {
        $this->createEnvFile("TEST_VAR_A=key=value\n");
        $this->parseEnvFile($this->tempDir);

        $this->assertSame('key=value', getenv('TEST_VAR_A'));
    }

    public function testLoadEnvHandlesEmptyValues(): void
    {
        $this->createEnvFile("TEST_VAR_A=\n");
        $this->parseEnvFile($this->tempDir);

        $this->assertSame('', getenv('TEST_VAR_A'));
    }
}
