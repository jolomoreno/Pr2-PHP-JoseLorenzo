<?php
/**
 * Created by PhpStorm.
 * User: jolo
 * Date: 23/12/18
 * Time: 12:46
 */

namespace App\Controller;

use App\Entity\User;
use App\Entity\Result;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class apiResultController
 *
 * @package App\Controller
 *
 * @Route(path=apiResultController::API_RESULT, name="api_result_")
 */
class apiResultController extends AbstractController
{
    public const API_RESULT = '/api/v1/results';

    /**
     * @Route(path="", name="optionsAllResults", methods={ Request::METHOD_OPTIONS })
     * @return JsonResponse
     */
    public function optionsAllResults(): JsonResponse
    {
        return new JsonResponse(null, Response::HTTP_OK, array("Allow" => "GET, POST, DELETE, OPTIONS"));
    }

    /**
     * @Route(path="/{id}", name="optionsOneResult", methods={ Request::METHOD_OPTIONS })
     * * @param Result|null $result
     * @return JsonResponse
     */
    public function optionsOneResult(?Result $result): JsonResponse
    {
        return (null === $result)
            ? $this->error(Response::HTTP_NOT_FOUND, 'NOT FOUND')
            : new JsonResponse(null, Response::HTTP_OK, array("Allow" => "GET, PUT, DELETE, OPTIONS"));
    }
    
    /**
     * @Route(path="", name="getAll", methods={ Request::METHOD_GET })
     * @return JsonResponse
     */
    public function getAllResults(): JsonResponse
    {
        /** @var Result[] results */
        $results = $this->getDoctrine()
            ->getRepository(Result::class)
            ->findAll();
        return (empty($results))
            ? $this->error(Response::HTTP_NOT_FOUND, 'NOT FOUND')
            : new JsonResponse($results);
    }

    /**
     * @Route(path="/{id}", name="getOne", methods={ Request::METHOD_GET })
     * @param Result|null $result
     * @return JsonResponse
     */
    public function getOneResult(?Result $result): JsonResponse
    {
        //  /** @var User $user */
        //  $user = $this->getDoctrine()
        //  ->getRepository(User::class)
        //  ->find($id);
        return (null === $result)
            ? $this->error(Response::HTTP_NOT_FOUND, 'NOT FOUND')
            : new JsonResponse($result);
    }

    /**
     * @param Request $request
     * @Route(path="", name="post", methods={ Request::METHOD_POST })
     * @return JsonResponse
     * @throws \Doctrine\ORM\ORMException
     */
    public function postResult(Request $request): JsonResponse
    {
        /** @var EntityManager $em */
        $em = $this->getDoctrine()->getManager();
        $datosPeticion = $request->getContent();
        $datos = json_decode($datosPeticion, true);
        $userId = $datos['user'] ?? null;
        $newResult = $datos['result'] ?? null;
        $newTimestamp = new \DateTime('now');

        // Error: falta USER
        if (null === $userId) {
            return $this->error(Response::HTTP_UNPROCESSABLE_ENTITY, 'Falta USER');
        }

        // Error: falta RESULT
        if (null === $newResult) {
            return $this->error(Response::HTTP_UNPROCESSABLE_ENTITY, 'Falta RESULT');
        }

        //Error: USER no existe
        $userDB = $this->getDoctrine()
            ->getRepository(User::class)
            ->find($userId);
        if (null === $userDB) {
            return $this->error(Response::HTTP_NOT_FOUND, 'USER NOT FOUND');
        }

        // Crear Result
        $result = new Result($newResult,$newTimestamp,$userDB);

        // Hacer persistente RESULT
        $em->persist($result);
        $em->flush();

        // devolver respuesta
        return new JsonResponse($result, Response::HTTP_CREATED);
    }

    /**
     * @param Request $request
     * @param Result $result
     * @Route(path="/{id}", name="put", methods={ Request::METHOD_PUT })
     * @return JsonResponse
     * @throws \Doctrine\ORM\ORMException
     */
    public function putResult(?Result $result, Request $request): JsonResponse
    {
        if (null === $result) {
            return $this->error(Response::HTTP_NOT_FOUND, 'NOT FOUND');
        }

        /** @var EntityManager $em */
        $em = $this->getDoctrine()->getManager();
        $datosPeticion = $request->getContent();
        $datos = json_decode($datosPeticion, true);
        $userId = $datos['user'] ?? null;
        $newResult = $datos['result'] ?? null;
        $newTimestamp = new \DateTime('now');

        // Error: falta USER
        if (null === $userId) {
            return $this->error(Response::HTTP_UNPROCESSABLE_ENTITY, 'Falta USER');
        }

        // Error: falta RESULT
        if (null === $newResult) {
            return $this->error(Response::HTTP_UNPROCESSABLE_ENTITY, 'Falta RESULT');
        }

        //Error: USER no existe
        $userDB = $this->getDoctrine()
            ->getRepository(User::class)
            ->find($userId);
        if (null === $userDB) {
            return $this->error(Response::HTTP_NOT_FOUND, 'USER NOT FOUND');
        }

        // Modificar Result
        $result->setResult($newResult);
        $result->setTime($newTimestamp);
        $result->setUser($userDB);

        // Hacerla persistente
        $em->persist($result);
        $em->flush();

        // devolver respuesta
        return new JsonResponse($result, Response::HTTP_OK);
    }

    /**
     * @Route(path="/{id}", name="deleteOne", methods={ Request::METHOD_DELETE })
     * @param Result|null $result
     * @return JsonResponse
     */
    public function deleteOneResult(?Result $result): JsonResponse
    {
        $em = $this->getDoctrine()->getManager();
        if($result === null) {
            return $this->error(Response::HTTP_NOT_FOUND, 'NOT FOUND');
        } else {
            $em->remove($result);
            $em->flush();
            return new JsonResponse( null, Response::HTTP_NO_CONTENT);
        }
    }

    /**
     * @Route(path="", name="deleteAll", methods={ Request::METHOD_DELETE })
     * @return JsonResponse
     */
    public function deleteAllResults(): JsonResponse
    {
        $em = $this->getDoctrine()->getManager();
        /** @var Result[] $results */
        $results = $this->getDoctrine()
            ->getRepository(User::class)
            ->findAll();

        foreach ($results as $result) {
            $em->remove($result);
            $em->flush();
        }
        return new JsonResponse( null, Response::HTTP_NO_CONTENT);
    }

    /**
     * @param int $statusCode
     * @param string $message
     *
     * @return JsonResponse
     */
    private function error(int $statusCode, string $message): JsonResponse
    {
        return new JsonResponse(
            [
                'message' => [
                    'code' => $statusCode,
                    'message' => $message
                ]
            ],
            $statusCode
        );
    }
}
