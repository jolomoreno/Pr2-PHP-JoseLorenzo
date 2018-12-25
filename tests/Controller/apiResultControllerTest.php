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
     * Implements testGetAllResults404
     * @covers ::getAllResults
     * @covers ::error
     */
    public function testGetAllResults404()
    {
        self::$client->request(
            Request::METHOD_GET,
            apiResultController::API_RESULT
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
        dump($datosRecibidos, '<<<<<< GET ALL RESULTS 404');
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
     * Implements testGetAllResults200
     * @covers ::getAllResults
     */
    public function testGetAllResults200()
    {
        self::$client->request(
            Request::METHOD_GET,
            apiResultController::API_RESULT
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
        self::assertArrayHasKey('result', $datosRecibidos[0]);
        self::assertArrayHasKey('user', $datosRecibidos[0]);
        dump($datosRecibidos, '<<<< GET ALL RESULTS 200');
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

    /**
     * Implements testGetOneUser200
     * @depends testPostResult201
     * @covers ::getOneResult
     * @param array $resultCreado
     */
    public function testGetOneResult200(array $resultCreado)
    {
        $id = $resultCreado['id'];
        self::$client->request(
            Request::METHOD_GET,
            apiResultController::API_RESULT . '/' . $id
        );
        /** @var Response $response */
        $response = self::$client->getResponse();
        self::assertEquals(
            Response::HTTP_OK,
            $response->getStatusCode()
        );
        self::assertJson($response->getContent());
        $resultObtenido = json_decode($response->getContent(), true);
        dump($resultObtenido, '<<<< GET ONE RESULT 200');
        self::assertArrayHasKey('id', $resultObtenido);
        self::assertArrayHasKey('result', $resultObtenido);
        self::assertArrayHasKey('user', $resultObtenido);
        self::assertEquals($id, $resultObtenido['id']);
        self::assertEquals($resultCreado['result'], $resultObtenido['result']);
    }

    /**
     * Implements testGetOneResult404
     * @covers ::getOneResult
     */
    public function testGetOneResult404(): void
    {
        $id = random_int(0, 10E6);
        self::$client->request(
            Request::METHOD_GET,
            apiResultController::API_RESULT . '/' . $id
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
        dump($datosRecibidos, '<<<<<< GET ONE RESULT 404');
    }

    /**
     * Implements testPutResult404ResultNotFound
     * @depends testPostResult201
     * @covers ::putResult
     * @param array $resultCreado
     * @return void
     * @throws
     */
    public function testPutResult404ResultNotFound(array $resultCreado): void
    {
        $id = random_int(0, 10E6);
        $userId = self::$user['id'];;
        $result = random_int(0, 32);

        $datos = [
            'user' => $userId,
            'result' => $result
        ];
        self::$client->request(
            Request::METHOD_PUT,
            apiResultController::API_RESULT . '/' . $id,
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
        dump($datosRecibidos, '<<<< PUT RESULT 404 (ResultNotFound)');
        self::assertEquals(404, $datosRecibidos["message"]["code"]);
        self::assertEquals("NOT FOUND", $datosRecibidos["message"]["message"]);
    }

    /**
     * Implements testPutResult404UserNotFound
     * @depends testPostResult201
     * @covers ::putResult
     * @param array $resultCreado
     * @return void
     * @throws
     */
    public function testPutResult404UserNotFound(array $resultCreado): void
    {
        $id = $resultCreado["id"];
        $userId = random_int(0, 10E6);
        $result = random_int(0, 32);

        $datos = [
            'user' => $userId,
            'result' => $result
        ];
        self::$client->request(
            Request::METHOD_PUT,
            apiResultController::API_RESULT . '/' . $id,
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
        dump($datosRecibidos, '<<<< PUT RESULT 404 (UserNotFound)');
        self::assertEquals(404, $datosRecibidos["message"]["code"]);
        self::assertEquals("USER NOT FOUND", $datosRecibidos["message"]["message"]);
    }

    /**
     * Implements testPutResult422
     * @depends testPostResult201
     * @covers ::putResult
     * @param array $resultCreado
     * @return void
     * @throws
     */
    public function testPutResult422(array $resultCreado): void
    {
        $id = $resultCreado["id"];
        $result = random_int(0, 32);

        $datos = [
            'result' => $result
        ];
        self::$client->request(
            Request::METHOD_PUT,
            apiResultController::API_RESULT . '/' . $id,
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
        dump($datosRecibidos, '<<<< PUT RESULT 422');
        self::assertEquals(422, $datosRecibidos["message"]["code"]);
        self::assertEquals("Falta USER", $datosRecibidos["message"]["message"]);
    }

    /**
     * Implements testPutResult200
     * @depends testPostResult201
     * @covers ::putResult
     * @param array $resultCreado
     * @return void
     * @throws
     */
    public function testPutResult200(array $resultCreado): void
    {
        $id = $resultCreado["id"];
        $userId = self::$user['id'];;
        $result = random_int(0, 32);
        $newTimestamp = new \DateTime('now');

        $datos = [
            'user' => $userId,
            'result' => $result,
            'time' => $newTimestamp
        ];
        self::$client->request(
            Request::METHOD_PUT,
            apiResultController::API_RESULT . '/' . $id,
            [], [], [], json_encode($datos)
        );
        /** @var Response $response */
        $response = self::$client->getResponse();
        self::assertEquals(
            Response::HTTP_OK,
            $response->getStatusCode()
        );
        self::assertJson($response->getContent());
        $datosRecibidos = json_decode($response->getContent(), true);
        dump($datosRecibidos, '<<<< PUT RESULT 200');
        self::assertEquals($result, $datosRecibidos["result"]);
        self::assertEquals($userId, $datosRecibidos["user"]["id"]);
    }

    /**
     * Implements testDeleteOneResult404
     * @covers ::deleteOneResult
     */
    public function testDeleteOneResult404(): void
    {
        $id = random_int(0, 10E6);

        self::$client->request(
            Request::METHOD_DELETE,
            apiResultController::API_RESULT . '/' . $id
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
        dump($datosRecibidos, '<<<< DELETE ONE RESULT 404');
    }

    /**
     * Implements testDeleteOneResult200
     * @depends testPostResult201
     * @covers ::deleteOneResult
     * @param array $resultCreado
     */
    public function testDeleteOneResult200(array $resultCreado): void
    {
        $id = $resultCreado['id'];
        self::$client->request(
            Request::METHOD_DELETE,
            apiResultController::API_RESULT . '/' . $id
        );
        /** @var Response $response */
        $response = self::$client->getResponse();
        self::assertEquals(
            Response::HTTP_NO_CONTENT,
            $response->getStatusCode()
        );
        self:self::assertEquals("", $response->getContent());
        dump($response->getContent(), '<<<< DELETE ONE RESULT 204');
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