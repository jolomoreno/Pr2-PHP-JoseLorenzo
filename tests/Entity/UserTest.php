<?php
/**
 * Created by PhpStorm.
 * User: jolo
 * Date: 19/12/18
 * Time: 21:31
 */

namespace App\Tests\Entity;

use App\Entity\User;
use PHPUnit\Framework\TestCase;

/**
 * Class UserTest
 *
 * @package MiW\Results\Tests\Entity
 * @group   users
 */
class UserTest extends TestCase
{
    /**
     * @var User $user
     */
    private $user;

    /**
     * Sets up the fixture.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->user = new User('Prueba', 'prueba@prueba.com', false,null,'123456');
    }

    /**
     * @covers \App\Entity\User::__construct()
     */
    public function testConstructor(): void
    {
        self::assertEquals('Prueba', $this->user->getUsername());
        self::assertEquals('prueba@prueba.com', $this->user->getEmail());
        self::assertEquals(true, $this->user->validatePassword('123456'));
    }

    /**
     * @covers \App\Entity\User::setUsername()
     * @covers \App\Entity\User::getUsername()
     */
    public function testGetSetUsername(): void
    {
        $this->user->setUsername('PruebaUpdated');
        self::assertEquals('PruebaUpdated', $this->user->getUsername());
    }

    /**
     * @covers \App\Entity\User::getEmail()
     * @covers \App\Entity\User::setEmail()
     */
    public function testGetSetEmail(): void
    {
        $this->user->setEmail('prueba@updated.com');
        self::assertEquals('prueba@updated.com', $this->user->getEmail());
    }

    /**
     * @covers \App\Entity\User::setEnabled()
     * @covers \App\Entity\User::isEnabled()
     */
    public function testIsSetEnabled(): void
    {
        $this->user->setEnabled(true);
        self::assertEquals(true, $this->user->isEnabled());
    }

    /**
     * @covers \App\Entity\User::setIsAdmin()
     * @covers \App\Entity\User::isAdmin
     */
    public function testIsSetAdmin(): void
    {
        $this->user->setIsAdmin(true);
        self::assertEquals(true, $this->user->isAdmin());
    }

    /**
     * @covers \App\Entity\User::setPassword()
     * @covers \App\Entity\User::validatePassword()
     */
    public function testSetValidatePassword(): void
    {
        self::assertEquals(true, $this->user->validatePassword('123456'));
    }

    /**
     * @covers \App\Entity\User::__toString()
     */
    public function testToString(): void
    {
        self::assertEquals('Prueba', $this->user->__toString());
    }

    /**
     * @covers \App\Entity\User::jsonSerialize()
     */
    public function testJsonSerialize(): void
    {
        $jsonSerialize = $this->user->jsonSerialize();
        self::assertTrue(is_array($jsonSerialize));
    }
}
