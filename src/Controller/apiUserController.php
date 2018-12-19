<?php
/**
 * Created by PhpStorm.
 * User: jolo
 * Date: 19/12/18
 * Time: 21:31
 */

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class apiUserController
 *
 * @package App\Controller
 *
 * @Route(path=apiUserController::API_USER, name="api_user_")
 */
class apiUserController extends AbstractController
{
    public const API_USER = '/api/v1/users';

    /**
     * @Route(path="", name="getAll", methods={ Request::METHOD_GET })
     * @return JsonResponse
     */
    public function getAllUsers(): JsonResponse
    {
        /** @var User[] $users */
        $users = $this->getDoctrine()
            ->getRepository(User::class)
            ->findAll();
        return (null === $users)
            ? $this->error(Response::HTTP_NOT_FOUND, 'NOT FOUND')
            : new JsonResponse(
                [ 'users' => $users ]
            );
    }

    /**
     * @Route(path="/{id}", name="getOne", methods={ Request::METHOD_GET })
     * @param User|null $user
     * @return JsonResponse
     */
    public function getOneUser(?User $user): JsonResponse
    {
        //  /** @var User $user */
        //  $user = $this->getDoctrine()
        //  ->getRepository(User::class)
        //  ->find($id);
        return (null === $user)
            ? $this->error(Response::HTTP_NOT_FOUND, 'NOT FOUND')
            : new JsonResponse(
                $user
            );
    }

    /**
     * @param Request $request
     * @Route(path="", name="post", methods={ Request::METHOD_POST })
     * @return JsonResponse
     */
    public function postPersona(Request $request): JsonResponse
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