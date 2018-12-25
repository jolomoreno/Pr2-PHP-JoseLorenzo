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
     * @covers ::postUser
     * @return int
     * @throws
     */
    public function testPostUser201(): int
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
        self::assertEquals(
            Response::HTTP_CREATED,
            $response->getStatusCode()
        );
        self::assertJson($response->getContent());
        $datosRecibidos = json_decode($response->getContent(), true);
        dump($datosRecibidos, '<<<< POST USER 201');
        self::assertEquals($username, $datosRecibidos["username"]);
        self::assertEquals($email, $datosRecibidos["email"]);
        self::assertEquals(true, $datosRecibidos["enabled"]);
        self::assertEquals(false, $datosRecibidos["admin"]);

        return $datosRecibidos['id'];
    }

    /**
     * Implements testGetAllUsers422
     * @covers ::postUser
     * @covers ::error
     * @return void
     * @throws
     */
    public function testPostUser422(): void
    {
        $username = "user_" . (string) random_int(0, 10E6);
        $email = $username . "@test.com";
        $password = "pass" . $username . "word";

        $datos = [
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
        self::assertEquals(
            Response::HTTP_UNPROCESSABLE_ENTITY,
            $response->getStatusCode()
        );
        self::assertJson($response->getContent());
        $datosRecibidos = json_decode($response->getContent(), true);
        self::assertEquals(422, $datosRecibidos["message"]["code"]);
        self::assertEquals("Falta USERNAME", $datosRecibidos["message"]["message"]);
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
        self::assertArrayHasKey('id', $datosRecibidos[0]);
        self::assertArrayHasKey('username', $datosRecibidos[0]);
        self::assertArrayHasKey('email', $datosRecibidos[0]);
        self::assertArrayHasKey('enabled', $datosRecibidos[0]);
        self::assertArrayHasKey('admin', $datosRecibidos[0]);
    }

    /**
     * Implements testGetOneUser200
     * @depends testPostUser201
     * @covers ::getOneUser
     * @param int $id
     * @return array $user
     */
    public function testGetOneUser200(int $id): array
    {
        self::$client->request(
            Request::METHOD_GET,
            apiUserController::API_USER . '/' . $id
        );
        /** @var Response $response */
        $response = self::$client->getResponse();
        self::assertEquals(
            Response::HTTP_OK,
            $response->getStatusCode()
        );
        self::assertJson($response->getContent());
        $user = json_decode($response->getContent(), true);
        dump($user, '<<<< GET ONE USER 200');
        self::assertArrayHasKey('id', $user);
        self::assertArrayHasKey('username', $user);
        self::assertArrayHasKey('email', $user);
        self::assertArrayHasKey('enabled', $user);
        self::assertArrayHasKey('admin', $user);
        self::assertEquals($id, $user['id']);

        return $user;
    }

    /**
     * Implements testGetOneUser404
     * @covers ::getOneUser
     */
    public function testGetOneUser404(): void
    {
        $id = random_int(0, 10E6);
        self::$client->request(
            Request::METHOD_GET,
            apiUserController::API_USER . '/' . $id
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
        self::assertEquals("NOT FOUND", $datosRecibidos["message"]["message"]);
        dump($datosRecibidos, '<<<<<< GET ONE USER 404');
    }

    /**
     * Implements testPostUser400
     * @covers ::postUser
     * @covers ::error
     * @param array $user
     * @depends testGetOneUser200
     * @return void
     * @throws
     */
    public function testPostUser400(array $user): void
    {
        $username = $user["username"];
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
        self::assertEquals(
            Response::HTTP_BAD_REQUEST,
            $response->getStatusCode()
        );
        self::assertJson($response->getContent());
        $datosRecibidos = json_decode($response->getContent(), true);
        dump($datosRecibidos, '<<<< POST USER 400');
        self::assertEquals(400, $datosRecibidos["message"]["code"]);
        self::assertEquals("USERNAME ya existe", $datosRecibidos["message"]["message"]);
    }

    /**
     * Implements testDeleteOneUser200
     * @depends testPostUser201
     * @covers ::deleteOneUser
     * @param int $id
     */
    public function testDeleteOneUser200(int $id): void
    {
        self::$client->request(
            Request::METHOD_DELETE,
            apiUserController::API_USER . '/' . $id
        );
        /** @var Response $response */
        $response = self::$client->getResponse();
        self::assertEquals(
            Response::HTTP_NO_CONTENT,
            $response->getStatusCode()
        );
        self:self::assertEquals("", $response->getContent());
        dump($response->getContent(), '<<<< DELETE ONE USER 200');
    }

    /**
     * Implements testDeleteOneUser404
     * @depends testPostUser201
     * @covers ::deleteOneUser
     */
    public function testDeleteOneUser404(): void
    {
        $id = random_int(0, 10E6);

        self::$client->request(
            Request::METHOD_DELETE,
            apiUserController::API_USER . '/' . $id
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
        self::assertEquals("NOT FOUND", $datosRecibidos["message"]["message"]);
        dump($datosRecibidos, '<<<< DELETE ONE USER 404');
    }

    /**
     * Implements testDeleteOneUser200
     * @depends testPostUser201
     * @covers ::deleteOneUser
     */
    public function testDeleteAllUsers200(): void
    {
        self::$client->request(
            Request::METHOD_DELETE,
            apiUserController::API_USER
        );
        /** @var Response $response */
        $response = self::$client->getResponse();
        self::assertEquals(
            Response::HTTP_NO_CONTENT,
            $response->getStatusCode()
        );
        self:self::assertEquals("", $response->getContent());
        dump($response->getContent(), '<<<< DELETE ALL USERs 200');
    }
}