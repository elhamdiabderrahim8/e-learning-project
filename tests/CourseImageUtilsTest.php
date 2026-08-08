<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../Projet/professeur/course_image_utils.php';

final class CourseImageUtilsTest extends TestCase
{
    protected function setUp(): void
    {
        $_SESSION = [];
    }

    // --- set_course_flash() ---

    public function testSetCourseFlashStoresTypeAndMessage(): void
    {
        set_course_flash('success', 'Cours cree.');

        $this->assertIsArray($_SESSION['course_flash']);
        $this->assertSame('success', $_SESSION['course_flash']['type']);
        $this->assertSame('Cours cree.', $_SESSION['course_flash']['message']);
    }

    public function testSetCourseFlashOverwritesPreviousFlash(): void
    {
        set_course_flash('error', 'First error');
        set_course_flash('success', 'All good');

        $this->assertSame('success', $_SESSION['course_flash']['type']);
        $this->assertSame('All good', $_SESSION['course_flash']['message']);
    }

    // --- normalize_course_upload() ---

    public function testNormalizeCourseUploadThrowsOnNoFile(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Veuillez choisir une image valide.');

        normalize_course_upload(['error' => UPLOAD_ERR_NO_FILE]);
    }

    public function testNormalizeCourseUploadThrowsOnUploadError(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Veuillez choisir une image valide.');

        normalize_course_upload(['error' => UPLOAD_ERR_INI_SIZE]);
    }

    public function testNormalizeCourseUploadThrowsOnEmptyTmpName(): void
    {
        $this->expectException(RuntimeException::class);

        normalize_course_upload([
            'error' => UPLOAD_ERR_OK,
            'tmp_name' => '',
        ]);
    }

    public function testNormalizeCourseUploadThrowsWhenTmpNameNotUploadedFile(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Image invalide.');

        $tmpFile = tempnam(sys_get_temp_dir(), 'test_');
        file_put_contents($tmpFile, 'not a real upload');

        try {
            normalize_course_upload([
                'error' => UPLOAD_ERR_OK,
                'tmp_name' => $tmpFile,
                'name' => 'test.jpg',
            ]);
        } finally {
            @unlink($tmpFile);
        }
    }
}
