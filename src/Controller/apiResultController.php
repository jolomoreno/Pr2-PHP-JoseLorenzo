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
     * @Route(path="", name="getAll", methods={ Request::METHOD_GET })
     * @return JsonResponse
     */
    public function getAllResults(): JsonResponse
    {
        /** @var Result[] results */
        $results = $this->getDoctrine()
            ->getRepository(Result::class)
            ->findAll();
        return (null === $results)
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
        $username = $datos['username'] ?? null;
        $email = $datos['email'] ?? null;
        $enabled = $datos['enabled'] ?? null;
        $password = $datos['password'] ?? null;
        $admin = $datos['admin'] ?? false;
        // Error: falta USERNAME
        if (null === $username) {
            return $this->error(Response::HTTP_UNPROCESSABLE_ENTITY, 'Falta USERNAME');
        }

        // Error: falta EMAIL
        if (null === $email) {
            return $this->error(Response::HTTP_UNPROCESSABLE_ENTITY, 'Falta EMAIL');
        }

        // Error: falta ENABLED
        if (null === $enabled) {
            return $this->error(Response::HTTP_UNPROCESSABLE_ENTITY, 'Falta ENABLED');
        }

        // Error: falta PASWORD
        if (null === $password) {
            return $this->error(Response::HTTP_UNPROCESSABLE_ENTITY, 'Falta PASSWORD');
        }

        // Error: USERNAME ya existe
        /** @var User $user */
        $user = $em->getRepository(User::class)->findOneBy(['username' => $username]);
        if (null !== $user) {
            return $this->error(Response::HTTP_BAD_REQUEST, 'USERNAME ya existe');
        }

        // Error: EMAIL ya existe
        /** @var User $user */
        $user = $em->getRepository(User::class)->findOneBy(['email' => $email]);
        if (null !== $user) {
            return $this->error(Response::HTTP_BAD_REQUEST, 'EMAIL ya existe');
        }

        // Crear User
        $user = new User($username,$email,$enabled,$admin,$password);

        // Hacerla persistente
        $em->persist($user);
        $em->flush();

        // devolver respuesta
        return new JsonResponse($user, Response::HTTP_CREATED);
    }

    /**
     * @param Request $request
     * @param User $user
     * @Route(path="/{id}", name="put", methods={ Request::METHOD_PUT })
     * @return JsonResponse
     * @throws \Doctrine\ORM\ORMException
     */
    public function putUser(?User $user, Request $request): JsonResponse
    {
        if (null === $user) {
            return $this->error(Response::HTTP_NOT_FOUND, 'NOT FOUND');
        }

        /** @var EntityManager $em */
        $em = $this->getDoctrine()->getManager();
        $datosPeticion = $request->getContent();
        $datos = json_decode($datosPeticion, true);
        $username = $datos['username'] ?? null;
        $email = $datos['email'] ?? null;
        $enabled = $datos['enabled'] ?? null;
        $password = $datos['password'] ?? null;
        $admin = $datos['admin'] ?? false;
        // Error: falta USERNAME
        if (null === $username) {
            return $this->error(Response::HTTP_UNPROCESSABLE_ENTITY, 'Falta USERNAME');
        }

        // Error: falta EMAIL
        if (null === $email) {
            return $this->error(Response::HTTP_UNPROCESSABLE_ENTITY, 'Falta EMAIL');
        }

        // Error: falta ENABLED
        if (null === $enabled) {
            return $this->error(Response::HTTP_UNPROCESSABLE_ENTITY, 'Falta ENABLED');
        }

        // Error: falta PASWORD
        if (null === $password) {
            return $this->error(Response::HTTP_UNPROCESSABLE_ENTITY, 'Falta PASSWORD');
        }


        // Error: USERNAME ya existe
        /** @var User $user */
        $userDB = $em->getRepository(User::class)->findOneBy(['username' => $username]);
        if (null !== $userDB) {
            return $this->error(Response::HTTP_BAD_REQUEST, 'USERNAME ya existe');
        }

        // Error: EMAIL ya existe
        /** @var User $user */
        $userDB = $em->getRepository(User::class)->findOneBy(['email' => $email]);
        if (null !== $userDB) {
            return $this->error(Response::HTTP_BAD_REQUEST, 'EMAIL ya existe');
        }

        // Modificar User
        $user->setUsername($username);
        $user->setEmail($email);
        $user->setEnabled($enabled);
        $user->setIsAdmin($admin);
        $user->setPassword($password);

        // Hacerla persistente
        $em->persist($user);
        $em->flush();

        // devolver respuesta
        return new JsonResponse($user, Response::HTTP_CREATED);
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