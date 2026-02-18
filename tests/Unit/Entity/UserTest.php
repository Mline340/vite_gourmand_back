<?php

namespace App\Tests\Unit\Entity;

use App\Entity\User;
use PHPUnit\Framework\TestCase;

/**
 * Test unitaire pour l'entité User
 * Vérifie la création et validation des utilisateurs
 */
class UserTest extends TestCase
{
    public function testUserCreation(): void
    {
        
        $user = new User();
        $user->setEmail('violette@mail.fr');
        $user->setNom('Desbois');
        $user->setPrenom('Violette');
        
        
        $this->assertEquals('violette@mail.fr', $user->getEmail());
        $this->assertEquals('Desbois', $user->getNom());
        $this->assertEquals('Violette', $user->getPrenom());
    }
    public function testTheAutomaticApiTokenSettingWhenAnUserIsCreated(): void
    {

        $user = new User();

        $this->assertNotNull($user->getApiToken());

    }
}