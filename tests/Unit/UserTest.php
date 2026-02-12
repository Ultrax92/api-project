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
        $result = $user->usesProfessionalEmail();
        $this->assertTrue($result, "L'email d'entreprise devrait renvoyer true");
    }

    /**
     * Test que usesProfessionalEmail renvoie FALSE pour un gmail.
     */
    public function test_uses_professional_email_returns_false_with_gmail(): void
    {
        $user = new User();
        $user->email = 'john@gmail.com';
        $result = $user->usesProfessionalEmail();
        $this->assertFalse($result, "L'email gmail devrait renvoyer false");
    }
}
