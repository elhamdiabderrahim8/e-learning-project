<?php

declare(strict_types=1);

function validate_required_fields(array $fields): ?string
{
    foreach ($fields as $value) {
        if (trim((string) $value) === '') {
            return 'Tous les champs sont obligatoires.';
        }
    }

    return null;
}

function validate_password(string $password, string $confirmPassword): ?string
{
    if (strlen($password) < 8) {
        return 'Le mot de passe doit contenir au moins 8 caracteres.';
    }

    if ($password !== $confirmPassword) {
        return 'Les mots de passe ne correspondent pas.';
    }

    return null;
}

function validate_cin(string $cin): ?string
{
    if (strlen($cin) < 8) {
        return 'Le CIN doit contenir au moins 8 caracteres.';
    }

    return null;
}

function validate_email(string $email): ?string
{
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return 'Adresse email invalide.';
    }

    return null;
}
