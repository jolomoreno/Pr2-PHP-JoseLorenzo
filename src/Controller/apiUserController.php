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
     * @Route(path="/{id}", name="get", methods={ Request::METHOD_GET })
     * @param User|null $user
     * @return JsonResponse
     */
    public function getPersona(?User $user): JsonResponse
    {
//        /** @var Persona $persona */
//        $persona = $this->getDoctrine()
//            ->getRepository(Persona::class)
//            ->find($dni);
        return (null === $user)
            ? $this->error(Response::HTTP_NOT_FOUND, 'NOT FOUND')
            : new JsonResponse(
                $user
            );
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