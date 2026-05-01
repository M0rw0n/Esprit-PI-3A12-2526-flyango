<?php

namespace App\Tests\Service;

use App\Entity\User;
use App\Service\UserManager;
use PHPUnit\Framework\TestCase;

class UserManagerTest extends TestCase
{
    public function testValidUser()
    {
        $user = new User();
        $user->setNom('Dupont');
        $user->setPrenom('Jean');
        $user->setEmail('jean.dupont@email.com');

        $manager = new UserManager();

        $this->assertTrue($manager->validate($user));
    }

    public function testUserWithoutEmail()
    {
        $this->expectException(\InvalidArgumentException::class);

        $user = new User();
        $user->setNom('Dupont');
        $user->setPrenom('Jean');

        $manager = new UserManager();
        $manager->validate($user);
    }

    public function testUserWithoutNom()
    {
        $this->expectException(\InvalidArgumentException::class);

        $user = new User();
        $user->setPrenom('Jean');
        $user->setEmail('jean@email.com');

        $manager = new UserManager();
        $manager->validate($user);
    }

    public function testUserWithoutPrenom()
    {
        $this->expectException(\InvalidArgumentException::class);

        $user = new User();
        $user->setNom('Dupont');
        $user->setEmail('jean@email.com');

        $manager = new UserManager();
        $manager->validate($user);
    }

    public function testUserWithInvalidEmail()
    {
        $this->expectException(\InvalidArgumentException::class);

        $user = new User();
        $user->setNom('Dupont');
        $user->setPrenom('Jean');
        $user->setEmail('email_invalide');

        $manager = new UserManager();
        $manager->validate($user);
    }

    public function testUserWithEmptyEmail()
    {
        $this->expectException(\InvalidArgumentException::class);

        $user = new User();
        $user->setNom('Dupont');
        $user->setPrenom('Jean');
        $user->setEmail('');

        $manager = new UserManager();
        $manager->validate($user);
    }
}
