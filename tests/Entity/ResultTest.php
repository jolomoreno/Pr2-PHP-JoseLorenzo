<?php
/**
 * Created by PhpStorm.
 * User: jolo
 * Date: 23/12/18
 * Time: 13:28
 */

namespace App\Tests\Entity;

use App\Entity\User;
use App\Entity\Result;
use PHPUnit\Framework\TestCase;

/**
 * Class ResultTest
 *
 * @package App\Entity\User
 */
class ResultTest extends TestCase
{
    /**
     * @var User $user
     */
    private $user;

    /**
     * @var Result $result
     */
    private $result;

    private const USERNAME = 'uSeR ñ¿?Ñ';
    private const POINTS = 2018;

    /**
     * @var \DateTime $time
     */
    private $time;

    /**
     * Sets up the fixture.
     * This method is called before a test is executed.
     *
     * @return void
     */
    protected function setUp(): void
    {
        $this->user = new User('uSeR ñ¿?Ñ', 'prueba@prueba.com', false,null,'123456');
        $this->time = new \DateTime('now');
        $this->result = new Result(self::POINTS, $this->time, $this->user);
    }

    /**
     * Implement testConstructor
     *
     * @covers \App\Entity\Result::__construct()
     * @covers \App\Entity\Result::getId()
     * @covers \App\Entity\Result::getResult()
     * @covers \App\Entity\Result::getUser()
     * @covers \App\Entity\Result::getTime()
     *
     * @return void
     */
    public function testConstructor(): void
    {
        self::assertEquals(self::USERNAME, $this->result->getUser());
        self::assertEquals(self::POINTS, $this->result->getResult());
    }

    /**
     * Implement testUsername().
     *
     * @covers \App\Entity\Result::setResult
     * @covers \App\Entity\Result::getResult
     * @return void
     */
    public function testSetGetResult(): void
    {
        $this->result->setResult(15);
        self::assertEquals(15, $this->result->getResult());
    }

    /**
     * Implement testUser().
     *
     * @covers \App\Entity\Result::setUser()
     * @covers \App\Entity\Result::getUser()
     * @return void
     */
    public function testUser(): void
    {
        $this->user->setUsername(self::USERNAME.'UPDATED');
        self::assertEquals(self::USERNAME.'UPDATED', $this->result->getUser());
    }

    /**
     * Implement testTime().
     *
     * @covers \App\Entity\Result::setTime
     * @covers \App\Entity\Result::getTime
     * @return void
     */
    /*public function testTime(): void
    {
        self::markTestIncomplete(
            'This test has not been implemented yet.'
        );
    }*/

    /**
     * Implement testTo_String().
     *
     * @covers \App\Entity\Result::__toString
     * @return void
     */
    public function testToString(): void
    {
        $toString = $this->result->__toString();
        $result = strpos($toString, 'prueba');
        self::assertFalse($result);
    }

    /**
     * Implement testJson_Serialize().
     *
     * @covers \App\Entity\Result::jsonSerialize
     * @return void
     */
    public function testJsonSerialize(): void
    {
        $jsonSerialize = $this->result->jsonSerialize();
        self::assertTrue(is_array($jsonSerialize));
    }
}
