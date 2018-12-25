<?php
/**
 * Created by PhpStorm.
 * User: jolo
 * Date: 25/12/18
 * Time: 12:34
 */

namespace App\Tests\Controller;

use App\Controller\apiUserController;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\BrowserKit\Client;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class apiUserControllerTest
 *
 * @package App\Tests\Controller
 * @coversDefaultClass \App\Controller\apiUserController
 */
class apiUserControllerTest extends WebTestCase
{
    /** @var Client $client */
    private static $client;

    public static function setUpBeforeClass()
    {
        self::$client = static::createClient();
    }

    /**
     * Implements testGetAllUsers200
     * @covers ::getAllUsers
     */
    public function testGetAllUsers200()
    {
        self::$client->request(
            Request::METHOD_GET,
            apiUserController::API_USER
        );
        /** @var Response $response */
        $response = self::$client->getResponse();
        self::assertEquals(
            Response::HTTP_OK,
            $response->getStatusCode()
        );
        self::assertJson($response->getContent());
        $datosRecibidos = json_decode($response->getContent(), true);
        self::assertEquals("JohnDoe", $datosRecibidos[0]["username"]);
        self::assertEquals("john@doe.com", $datosRecibidos[0]["email"]);
        self::assertEquals(true, $datosRecibidos[0]["enabled"]);
        self::assertEquals(true, $datosRecibidos[0]["admin"]);
    }
}