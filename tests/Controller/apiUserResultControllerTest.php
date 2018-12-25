<?php
/**
 * Created by PhpStorm.
 * User: jolo
 * Date: 25/12/18
 * Time: 21:34
 */

namespace App\Tests\Controller;

use App\Controller\apiUserResultController;
use App\Controller\apiResultController;
use App\Controller\apiUserController;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\BrowserKit\Client;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class apiUserResultControllerTest
 *
 * @package App\Tests\Controller
 * @coversDefaultClass \App\Controller\apiUserResultController
 */
class apiUserResultControllerTest extends WebTestCase
{
    /** @var Client $client */
    private static $client;
    private static $user;
    private static $route_1 = apiUserResultController::API_USER_RESULT;
    private static $route_2 = '/results';

    public static function setUpBeforeClass()
    {
        self::$client = static::createClient();
        self::$user = self::proveedorUser();
    }

   // GET ALL RESULTS OF USER 404 - No existen results para el user creado
    /**
     * Implements testGetAllResultsOfUser404ResultsNotFound
     * @covers ::getAllResultsOfUser
     * @covers ::error
     */
    public function testGetAllResultsOfUser404ResultsNotFound()
    {
        $idUser = self::$user['id'];
        self::$client->request(
            Request::METHOD_GET,
            self::$route_1 . '/' . $idUser . self::$route_2
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
        dump($datosRecibidos, '<<<<<< GET ALL RESULTS OF USER 404 (ResultsNotFound)');
    }

    // GET ALL RESULTS OF USER 404 - No existe el user
    /**
     * Implements testGetAllResultsOfUser404
     * @covers ::getAllResultsOfUser
     * @covers ::error
     */
    public function testGetAllResultsOfUser404UserNotFound()
    {
        $idUser = random_int(0, 10E6);
        self::$client->request(
            Request::METHOD_GET,
            self::$route_1 . '/' . $idUser . self::$route_2
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
        self::assertEquals("USER DOESNT EXISTS IN DB", $datosRecibidos["message"]["message"]);
        dump($datosRecibidos, '<<<<<< GET ALL RESULTS OF USER 404 (UserNotFound)');
    }

   // GET ALL RESULTS OF USER 200
    /**
     * Implements testGetAllResultsOfUser200
     * @covers ::getAllResultsOfUser
     */
    public function testGetAllResultsOfUser200()
    {
        $idUser = self::$user['id'];

        // CREAR BATERIA RESULTS OF USER
        for ( $i = 0 ; $i < 10; $i++ ){
            self::proveedorResults($idUser);
        }

        self::$client->request(
            Request::METHOD_GET,
            self::$route_1 . '/' . $idUser . self::$route_2
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
        dump($datosRecibidos, '<<<< GET ALL RESULTS OF USER 200');
    }

   // DELETE ALL RESULTS OF USER
    /**
     * Implements testDeleteAllResultsOfUser200
     * @covers ::deleteAllResultsOfUser
     */
    public function testDeleteAllResultsOfUser200(): void
    {
        $idUser = self::$user['id'];

        self::$client->request(
            Request::METHOD_DELETE,
            self::$route_1 . '/' . $idUser . self::$route_2
        );
        /** @var Response $response */
        $response = self::$client->getResponse();
        self::assertEquals(
            Response::HTTP_NO_CONTENT,
            $response->getStatusCode()
        );
        self:self::assertEquals("", $response->getContent());
        dump($response->getContent(), '<<<< DELETE ALL RESULTS 204');
    }
   /*
    * Ejecución al final de los tests
    */
   public static function tearDownAfterClass()
   {
       self::deleteUser(self::$user['id']);
       dump('>>>>>>>>>>>>>>>>>>>>>>>>>> E2E USER RESULT TEST ENDS HERE');
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
     * Proveedor Results
     * @param int $userId
     * @throws
     */
    public static function proveedorResults(int $userId)
    {
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
        $result = json_decode($response->getContent(), true);
        dump( '<<<<<<<<<<<< POST RESULT ');
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