<?php
/**
 * Created by PhpStorm.
 * User: jolo
 * Date: 25/12/18
 * Time: 19:34
 */

namespace App\Tests\Controller;

use App\Controller\apiResultController;
use App\Controller\apiUserController;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\BrowserKit\Client;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class apiResultControllerTest
 *
 * @package App\Tests\Controller
 * @coversDefaultClass \App\Controller\apiResultController
 */
class apiResultControllerTest extends WebTestCase
{
    /** @var Client $client */
    private static $client;
    private static $user;

    public static function setUpBeforeClass()
    {
        self::$client = static::createClient();
        self::$user = self::proveedorUser();
    }

    /**
     * Implements testPostResult201
     * @covers ::postResult
     * @return array
     * @throws
     */
    public function testPostResult201(): array
    {
        $userId = self::$user['id'];
        $result = random_int(0, 32);

        $datos = [
            'user' => $userId,
            'result' => $result
        ];
        self::$client->request(
            Request::METHOD_POST,
            apiResultController::API_RESULT,
            [], [], [], json_encode($datos)
        );
        /** @var Response $response */
        $response = self::$client->getResponse();
        self::assertEquals(
            Response::HTTP_CREATED,
            $response->getStatusCode()
        );
        self::assertJson($response->getContent());
        $resultArray = json_decode($response->getContent(), true);
        dump($resultArray, '<<<< POST RESULT 201');
        self::assertEquals($result, $resultArray["result"]);
        self::assertEquals($userId, $resultArray["user"]["id"]);

        return $resultArray;
    }

    /**
     * Implements testPostResult422
     * @covers ::postResult
     * @covers ::error
     * @return void
     * @throws
     */
    public function testPostResult422(): void
    {
        $result = random_int(0, 32);

        $datos = [
            'result' => $result
        ];
        self::$client->request(
            Request::METHOD_POST,
            apiResultController::API_RESULT,
            [], [], [], json_encode($datos)
        );
        /** @var Response $response */
        $response = self::$client->getResponse();
        self::assertEquals(
            Response::HTTP_UNPROCESSABLE_ENTITY,
            $response->getStatusCode()
        );
        self::assertJson($response->getContent());
        $datosRecibidos = json_decode($response->getContent(), true);
        self::assertEquals(422, $datosRecibidos["message"]["code"]);
        self::assertEquals("Falta USER", $datosRecibidos["message"]["message"]);
        dump($datosRecibidos, '<<<<<< POST RESULT 422');
    }

    /**
     * Implements testPostResult404
     * @covers ::postResult
     * @covers ::error
     * @return void
     * @throws
     */
    public function testPostResult404(): void
    {
        $userId = random_int(0, 10E6);
        $result = random_int(0, 32);

        $datos = [
            'user' => $userId,
            'result' => $result
        ];
        self::$client->request(
            Request::METHOD_POST,
            apiResultController::API_RESULT,
            [], [], [], json_encode($datos)
        );
        /** @var Response $response */
        $response = self::$client->getResponse();
        self::assertEquals(
            Response::HTTP_NOT_FOUND,
            $response->getStatusCode()
        );
        self::assertJson($response->getContent());
        $datosRecibidos = json_decode($response->getContent(), true);
        self::assertEquals(404, $datosRecibidos["message"]["code"]);
        self::assertEquals("USER NOT FOUND", $datosRecibidos["message"]["message"]);
        dump($datosRecibidos, '<<<<<< POST RESULT 404');
    }

    /*
     * Ejecución al final de los tests
     */
    public static function tearDownAfterClass()
    {
        // self::deleteUser(self::$user['id']);
        dump('>>>>>>>>>>>>>>>>>>>>>>>>>> E2E RESULT TEST ENDS HERE');
    }

    /**
     * Proveedor User
     * @return array $user
     * @throws
     */
    public static function proveedorUser(): array
    {
        $username = "user_" . (string) random_int(0, 10E6);
        $email = $username . "@test.com";
        $password = "pass" . $username . "word";

        $datos = [
            'username' => $username,
            'email' => $email,
            'enabled' => true,
            'admin' => false,
            'password' => $password,
        ];
        self::$client->request(
            Request::METHOD_POST,
            apiUserController::API_USER,
            [], [], [], json_encode($datos)
        );
        /** @var Response $response */
        $response = self::$client->getResponse();
        $user = json_decode($response->getContent(), true);
        dump( '<<<<<<<<<<<< POST USER ');
        return $user;
    }

    /**
     * Delete User
     * @param int $id
     */
    public static function deleteUser(int $id): void
    {
        self::$client->request(
            Request::METHOD_DELETE,
            apiUserController::API_USER . '/' . $id
        );
        /** @var Response $response */
        $response = self::$client->getResponse();
        dump($id, '<<<<<<<<<< DELETE USER');
    }
}