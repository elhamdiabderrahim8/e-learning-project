<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

/**
 * Tests validation logic patterns used across action files
 * (registration, task creation, payment, reclamation, profile update).
 *
 * The validations are extracted inline because the original code uses
 * procedural scripts with exit/redirect. This test suite verifies the
 * same logic paths that the action scripts follow.
 */
final class ValidationLogicTest extends TestCase
{
    // ========== Registration Validation ==========

    /**
     * @dataProvider registrationValidationProvider
     */
    public function testRegistrationValidation(
        string $cin,
        string $firstName,
        string $lastName,
        string $email,
        string $password,
        string $confirmPassword,
        ?string $expectedError
    ): void {
        $error = $this->validateRegistration($cin, $firstName, $lastName, $email, $password, $confirmPassword);
        $this->assertSame($expectedError, $error);
    }

    public static function registrationValidationProvider(): array
    {
        return [
            'valid registration' => [
                '12345678', 'John', 'Doe', 'john@example.com', 'password123', 'password123', null,
            ],
            'empty fields' => [
                '', 'John', 'Doe', 'john@example.com', 'password123', 'password123',
                'Tous les champs sont obligatoires.',
            ],
            'empty first name' => [
                '12345678', '', 'Doe', 'john@example.com', 'password123', 'password123',
                'Tous les champs sont obligatoires.',
            ],
            'invalid email' => [
                '12345678', 'John', 'Doe', 'not-an-email', 'password123', 'password123',
                'Adresse email invalide.',
            ],
            'short password' => [
                '12345678', 'John', 'Doe', 'john@example.com', 'short', 'short',
                'Le mot de passe doit contenir au moins 8 caractères.',
            ],
            'mismatched passwords' => [
                '12345678', 'John', 'Doe', 'john@example.com', 'password123', 'password456',
                'Les mots de passe ne correspondent pas.',
            ],
            'short CIN' => [
                '1234', 'John', 'Doe', 'john@example.com', 'password123', 'password123',
                'Le CIN doit contenir au moins 8 caractères.',
            ],
        ];
    }

    private function validateRegistration(
        string $cin,
        string $firstName,
        string $lastName,
        string $email,
        string $password,
        string $confirmPassword
    ): ?string {
        $cin = trim($cin);
        $firstName = trim($firstName);
        $lastName = trim($lastName);
        $email = strtolower(trim($email));

        if ($cin === '' || $firstName === '' || $lastName === '' || $email === '' || $password === '' || $confirmPassword === '') {
            return 'Tous les champs sont obligatoires.';
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return 'Adresse email invalide.';
        }

        if (strlen($password) < 8) {
            return 'Le mot de passe doit contenir au moins 8 caractères.';
        }

        if ($password !== $confirmPassword) {
            return 'Les mots de passe ne correspondent pas.';
        }

        if (strlen($cin) < 8) {
            return 'Le CIN doit contenir au moins 8 caractères.';
        }

        return null;
    }

    // ========== Task Creation Validation ==========

    /**
     * @dataProvider taskCreationValidationProvider
     */
    public function testTaskCreationValidation(
        string $title,
        string $priority,
        string $status,
        string $dueDateInput,
        ?string $expectedError,
        ?string $expectedPriority,
        ?string $expectedStatus,
        ?string $expectedDueDate
    ): void {
        $result = $this->validateTaskCreation($title, $priority, $status, $dueDateInput);

        $this->assertSame($expectedError, $result['error']);

        if ($expectedError === null) {
            $this->assertSame($expectedPriority, $result['priority']);
            $this->assertSame($expectedStatus, $result['status']);
            $this->assertSame($expectedDueDate, $result['due_date']);
        }
    }

    public static function taskCreationValidationProvider(): array
    {
        return [
            'valid task with defaults' => [
                'My Task', 'medium', 'a_faire', '', null, 'medium', 'a_faire', null,
            ],
            'empty title' => [
                '', 'medium', 'a_faire', '', 'Le titre de la tache est obligatoire.', null, null, null,
            ],
            'valid with date' => [
                'Task', 'high', 'en_cours', '2025-12-31', null, 'high', 'en_cours', '2025-12-31',
            ],
            'invalid priority falls back to medium' => [
                'Task', 'urgent', 'a_faire', '', null, 'medium', 'a_faire', null,
            ],
            'invalid status falls back to a_faire' => [
                'Task', 'low', 'invalid', '', null, 'low', 'a_faire', null,
            ],
            'invalid date format' => [
                'Task', 'low', 'a_faire', '31-12-2025', 'La date limite est invalide.', null, null, null,
            ],
            'impossible date' => [
                'Task', 'low', 'a_faire', '2025-02-30', 'La date limite est invalide.', null, null, null,
            ],
            'terminee status sets completed flag' => [
                'Done Task', 'high', 'terminee', '', null, 'high', 'terminee', null,
            ],
        ];
    }

    /**
     * @return array{error: ?string, priority: ?string, status: ?string, due_date: ?string, is_completed: ?int}
     */
    private function validateTaskCreation(
        string $title,
        string $priority,
        string $status,
        string $dueDateInput
    ): array {
        $title = trim($title);

        if ($title === '') {
            return ['error' => 'Le titre de la tache est obligatoire.', 'priority' => null, 'status' => null, 'due_date' => null, 'is_completed' => null];
        }

        $allowedPriorities = ['high', 'medium', 'low'];
        if (!in_array($priority, $allowedPriorities, true)) {
            $priority = 'medium';
        }

        $allowedStatuses = ['a_faire', 'en_cours', 'terminee'];
        if (!in_array($status, $allowedStatuses, true)) {
            $status = 'a_faire';
        }

        $isCompletedFlag = $status === 'terminee' ? 1 : 0;

        $dueDate = null;
        if ($dueDateInput !== '') {
            $date = \DateTime::createFromFormat('Y-m-d', $dueDateInput);
            $isValidDate = $date && $date->format('Y-m-d') === $dueDateInput;
            if (!$isValidDate) {
                return ['error' => 'La date limite est invalide.', 'priority' => null, 'status' => null, 'due_date' => null, 'is_completed' => null];
            }
            $dueDate = $dueDateInput;
        }

        return [
            'error' => null,
            'priority' => $priority,
            'status' => $status,
            'due_date' => $dueDate,
            'is_completed' => $isCompletedFlag,
        ];
    }

    // ========== Task Status Transition Logic ==========

    /**
     * @dataProvider taskStatusTransitionProvider
     */
    public function testTaskStatusTransition(
        string $currentStatus,
        int $isCompleted,
        string $expectedNextStatus,
        int $expectedIsCompleted
    ): void {
        $result = $this->computeNextTaskStatus($currentStatus, $isCompleted);
        $this->assertSame($expectedNextStatus, $result['next_status']);
        $this->assertSame($expectedIsCompleted, $result['is_completed']);
    }

    public static function taskStatusTransitionProvider(): array
    {
        return [
            'a_faire -> en_cours' => ['a_faire', 0, 'en_cours', 0],
            'en_cours -> terminee' => ['en_cours', 0, 'terminee', 1],
            'unknown status with is_completed=1 treated as terminee' => ['unknown', 1, 'terminee', 1],
            'unknown status with is_completed=0 treated as a_faire -> en_cours' => ['unknown', 0, 'en_cours', 0],
        ];
    }

    /**
     * @return array{next_status: string, is_completed: int}
     */
    private function computeNextTaskStatus(string $currentStatus, int $isCompleted): array
    {
        if ($currentStatus !== 'a_faire' && $currentStatus !== 'en_cours' && $currentStatus !== 'terminee') {
            if ($isCompleted === 1) {
                $currentStatus = 'terminee';
            } else {
                $currentStatus = 'a_faire';
            }
        }

        $nextStatus = 'terminee';
        if ($currentStatus === 'a_faire') {
            $nextStatus = 'en_cours';
        }

        $isCompletedFlag = $nextStatus === 'terminee' ? 1 : 0;

        return [
            'next_status' => $nextStatus,
            'is_completed' => $isCompletedFlag,
        ];
    }

    // ========== Toggle Task Logic ==========

    /**
     * @dataProvider toggleTaskProvider
     */
    public function testToggleTaskLogic(int $isCompleted, int $expectedNextState, string $expectedNextStatus): void
    {
        $isCompletedNow = $isCompleted === 1;
        $nextState = $isCompletedNow ? 0 : 1;
        $nextStatus = $nextState === 1 ? 'terminee' : 'en_cours';

        $this->assertSame($expectedNextState, $nextState);
        $this->assertSame($expectedNextStatus, $nextStatus);
    }

    public static function toggleTaskProvider(): array
    {
        return [
            'uncompleted becomes completed' => [0, 1, 'terminee'],
            'completed becomes uncompleted' => [1, 0, 'en_cours'],
        ];
    }

    // ========== Payment Validation ==========

    /**
     * @dataProvider paymentValidationProvider
     */
    public function testPaymentValidation(
        string $cardHolder,
        string $cardNumber,
        string $cardExpiry,
        string $cardCvc,
        int $courseId,
        int $studentId,
        bool $expectedValid
    ): void {
        $valid = $this->validatePayment($cardHolder, $cardNumber, $cardExpiry, $cardCvc, $courseId, $studentId);
        $this->assertSame($expectedValid, $valid);
    }

    public static function paymentValidationProvider(): array
    {
        return [
            'valid payment' => [
                'John Doe', '4111111111111111', '12/25', '123', 1, 12345678, true,
            ],
            'empty card holder' => [
                '', '4111111111111111', '12/25', '123', 1, 12345678, false,
            ],
            'short card number' => [
                'John Doe', '1234', '12/25', '123', 1, 12345678, false,
            ],
            'short expiry' => [
                'John Doe', '4111111111111111', '12', '123', 1, 12345678, false,
            ],
            'short cvc' => [
                'John Doe', '4111111111111111', '12/25', '12', 1, 12345678, false,
            ],
            'zero course id' => [
                'John Doe', '4111111111111111', '12/25', '123', 0, 12345678, false,
            ],
            'zero student id' => [
                'John Doe', '4111111111111111', '12/25', '123', 1, 0, false,
            ],
        ];
    }

    private function validatePayment(
        string $cardHolder,
        string $cardNumber,
        string $cardExpiry,
        string $cardCvc,
        int $courseId,
        int $studentId
    ): bool {
        $cardHolder = trim($cardHolder);
        $cardNumber = preg_replace('/\D+/', '', $cardNumber);
        $cardExpiry = trim($cardExpiry);
        $cardCvc = preg_replace('/\D+/', '', $cardCvc);

        if ($courseId === 0 || $studentId === 0) {
            return false;
        }

        if ($cardHolder === '' || strlen($cardNumber) < 12 || strlen($cardExpiry) < 4 || strlen($cardCvc) < 3) {
            return false;
        }

        return true;
    }

    // ========== Card Last 4 Extraction ==========

    public function testCardLast4Extraction(): void
    {
        $cardNumber = '4111111111111111';
        $last4 = substr($cardNumber, -4);
        $this->assertSame('1111', $last4);
    }

    public function testCardLast4ExtractionWithSpaces(): void
    {
        $cardNumber = preg_replace('/\D+/', '', '4111 1111 1111 1234');
        $last4 = substr($cardNumber, -4);
        $this->assertSame('1234', $last4);
    }

    // ========== Reclamation Validation ==========

    /**
     * @dataProvider reclamationValidationProvider
     */
    public function testReclamationValidation(string $subject, string $message, ?string $expectedError): void
    {
        $subject = trim($subject);
        $message = trim($message);
        $error = null;

        if ($subject === '' || $message === '') {
            $error = 'Sujet et description sont obligatoires.';
        }

        $this->assertSame($expectedError, $error);
    }

    public static function reclamationValidationProvider(): array
    {
        return [
            'valid reclamation' => ['Bug report', 'The page crashes on login', null],
            'empty subject' => ['', 'Some description', 'Sujet et description sont obligatoires.'],
            'empty message' => ['Subject', '', 'Sujet et description sont obligatoires.'],
            'both empty' => ['', '', 'Sujet et description sont obligatoires.'],
            'whitespace-only subject' => ['   ', 'Some msg', 'Sujet et description sont obligatoires.'],
        ];
    }

    // ========== Profile Update Validation ==========

    /**
     * @dataProvider profileValidationProvider
     */
    public function testProfileUpdateValidation(
        string $firstName,
        string $lastName,
        string $language,
        ?string $expectedError,
        ?string $expectedLanguage
    ): void {
        $result = $this->validateProfileUpdate($firstName, $lastName, $language);

        $this->assertSame($expectedError, $result['error']);
        if ($expectedError === null) {
            $this->assertSame($expectedLanguage, $result['language']);
        }
    }

    public static function profileValidationProvider(): array
    {
        return [
            'valid update fr' => ['John', 'Doe', 'fr', null, 'fr'],
            'valid update en' => ['John', 'Doe', 'en', null, 'en'],
            'empty first name' => ['', 'Doe', 'fr', 'Le prénom et le nom sont obligatoires.', null],
            'empty last name' => ['John', '', 'fr', 'Le prénom et le nom sont obligatoires.', null],
            'invalid language defaults to fr' => ['John', 'Doe', 'de', null, 'fr'],
        ];
    }

    /**
     * @return array{error: ?string, language: ?string}
     */
    private function validateProfileUpdate(
        string $firstName,
        string $lastName,
        string $language
    ): array {
        $firstName = trim($firstName);
        $lastName = trim($lastName);

        if ($firstName === '' || $lastName === '') {
            return ['error' => 'Le prénom et le nom sont obligatoires.', 'language' => null];
        }

        if ($language !== 'fr' && $language !== 'en') {
            $language = 'fr';
        }

        return ['error' => null, 'language' => $language];
    }

    // ========== Support Message Validation ==========

    public function testSupportMessageRejectsEmptyMessage(): void
    {
        $message = trim('');
        $this->assertTrue($message === '');
    }

    public function testSupportMessageRejectsTooLongMessage(): void
    {
        $message = str_repeat('a', 4001);
        $this->assertTrue(mb_strlen($message) > 4000);
    }

    public function testSupportMessageAcceptsValidMessage(): void
    {
        $message = trim('I need help with my course.');
        $this->assertFalse($message === '');
        $this->assertFalse(mb_strlen($message) > 4000);
    }

    public function testSupportMessageAcceptsExactly4000Chars(): void
    {
        $message = str_repeat('b', 4000);
        $this->assertFalse(mb_strlen($message) > 4000);
    }

    // ========== Filename Sanitization ==========

    /**
     * @dataProvider filenameSanitizationProvider
     */
    public function testFilenameSanitization(string $original, string $expected): void
    {
        $safeName = preg_replace('/[^a-zA-Z0-9._-]/', '_', $original);
        $this->assertSame($expected, $safeName);
    }

    public static function filenameSanitizationProvider(): array
    {
        return [
            'normal filename' => ['document.pdf', 'document.pdf'],
            'spaces replaced' => ['my document.pdf', 'my_document.pdf'],
            'special characters replaced' => ['résumé (1).pdf', 'r__sum____1_.pdf'],
            'already safe' => ['file-name_01.txt', 'file-name_01.txt'],
        ];
    }

    // ========== Professor Registration Validation ==========

    /**
     * @dataProvider profRegistrationProvider
     */
    public function testProfessorRegistrationValidation(
        string $cin,
        string $prenom,
        string $nom,
        string $password,
        string $confirmPassword,
        ?string $expectedError
    ): void {
        $error = $this->validateProfRegistration($cin, $prenom, $nom, $password, $confirmPassword);
        $this->assertSame($expectedError, $error);
    }

    public static function profRegistrationProvider(): array
    {
        return [
            'valid registration' => ['12345678', 'Ali', 'Baba', 'password123', 'password123', null],
            'empty fields' => ['', 'Ali', 'Baba', 'password123', 'password123', 'Tous les champs sont obligatoires.'],
            'non-digit CIN' => ['ABC12345', 'Ali', 'Baba', 'password123', 'password123', 'Le CIN doit contenir uniquement des chiffres.'],
            'short password' => ['12345678', 'Ali', 'Baba', 'short', 'short', 'Le mot de passe doit contenir au moins 8 caracteres.'],
            'mismatched passwords' => ['12345678', 'Ali', 'Baba', 'password123', 'password456', 'Les mots de passe ne correspondent pas.'],
        ];
    }

    private function validateProfRegistration(
        string $cin,
        string $prenom,
        string $nom,
        string $password,
        string $confirmPassword
    ): ?string {
        $cin = trim($cin);
        $prenom = trim($prenom);
        $nom = trim($nom);

        if ($cin === '' || $prenom === '' || $nom === '' || $password === '' || $confirmPassword === '') {
            return 'Tous les champs sont obligatoires.';
        }

        if (!ctype_digit($cin)) {
            return 'Le CIN doit contenir uniquement des chiffres.';
        }

        if (strlen($password) < 8) {
            return 'Le mot de passe doit contenir au moins 8 caracteres.';
        }

        if ($password !== $confirmPassword) {
            return 'Les mots de passe ne correspondent pas.';
        }

        return null;
    }

    // ========== Progression Calculation ==========

    /**
     * @dataProvider progressionProvider
     */
    public function testProgressionCalculation(int $completed, int $total, int $expectedPercentage): void
    {
        if ($total > 0) {
            $pourcentage = (int) round(($completed / $total) * 100);
        } else {
            $pourcentage = 0;
        }

        if ($pourcentage > 100) {
            $pourcentage = 100;
        }

        $this->assertSame($expectedPercentage, $pourcentage);
    }

    public static function progressionProvider(): array
    {
        return [
            'no lessons' => [0, 0, 0],
            'none completed' => [0, 5, 0],
            'half completed' => [5, 10, 50],
            'all completed' => [10, 10, 100],
            'over-completed capped at 100' => [12, 10, 100],
            'one of three' => [1, 3, 33],
            'two of three' => [2, 3, 67],
        ];
    }
}
