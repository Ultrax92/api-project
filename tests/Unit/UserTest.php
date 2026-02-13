<?php

namespace Tests\Unit;

use App\Models\User;
use PHPUnit\Framework\TestCase;

class UserTest extends TestCase
{
    /**
     * Teste que usesProfessionalEmail renvoie TRUE pour un email d'entreprise.
     */
    public function test_uses_professional_email_returns_true_with_enterprise_email(): void
    {
        $user = new User();

        $user->email = 'john@tesla.com';
        $this->assertTrue($user->usesProfessionalEmail(), "L'email tesla.com devrait être PRO");

        $user->email = 'etudiant@univ-lyon.fr';
        $this->assertTrue($user->usesProfessionalEmail(), "L'email universitaire devrait être PRO");
    }

    /**
     * Teste que usesProfessionalEmail renvoie FALSE pour les domaines de la prof.
     */
    public function test_uses_professional_email_returns_false_with_public_domains(): void
    {
        $user = new User();

        $badEmails = [
            'john@gmail.com',
            'paul@yahoo.fr',
            'jacques@hotmail.com',
            'adrien@outlook.com',
            'sarah@live.fr'
        ];

        foreach ($badEmails as $email) {
            $user->email = $email;
            $this->assertFalse(
                $user->usesProfessionalEmail(),
                "L'email $email devrait renvoyer FALSE (considéré comme public)"
            );
        }
    }
}