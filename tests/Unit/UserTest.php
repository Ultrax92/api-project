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

        $user->email = 'john@entreprise.com';
        $this->assertTrue($user->usesProfessionalEmail(), "L'email entreprise.com devrait être PRO");

        $user->email = 'etudiant@univ-lyon.fr';
        $this->assertTrue($user->usesProfessionalEmail(), "L'email universitaire devrait être PRO");
    }

    /**
     * Teste que usesProfessionalEmail renvoie FALSE pour les domaines publics.
     */
    public function test_uses_professional_email_returns_false_with_public_domains(): void
    {
        $user = new User();

        $badEmails = [
            'john@gmail.com',
            'paul@yahoo.fr',
            'jacques@hotmail.com',
            'marie@orange.fr',
            'lucie@sfr.fr',
            'pierre@free.fr',
            'mamie@wanadoo.fr',
            'papy@laposte.net',
            'adrien@outlook.com'
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
