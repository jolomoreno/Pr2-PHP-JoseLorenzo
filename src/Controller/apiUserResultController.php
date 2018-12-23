<?php
/**
 * Created by PhpStorm.
 * User: jolo
 * Date: 23/12/18
 * Time: 13:46
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
 * Class apiUserResultController
 *
 * @package App\Controller
 *
 * @Route(path=apiUserResultController::API_USER_RESULT, name="api_user_result_")
 */
class apiUserResultController extends AbstractController
{
    public const API_USER_RESULT = '/api/v1/users';

    /**
     * @param User $user
     * @Route(path="/{id}/results", name="getAll", methods={ Request::METHOD_GET })
     * @return JsonResponse
     */
    public function getAllResultsOfUser(?User $user): JsonResponse
    {
        if (null === $user) {
            return $this->error(Response::HTTP_NOT_FOUND, 'USER DOESNT EXISTS IN DB');
        }

        /** @var Result[] results */
        $results = $this->getDoctrine()
            ->getRepository(Result::class)
            ->findBy(['user'=>$user->getId()]);

        return (null === $results)
            ? $this->error(Response::HTTP_NOT_FOUND, 'NOT FOUND')
            : new JsonResponse($results);
    }

    // TODO: Decidir si quitarlo, realiza lo mismo que el endpoint GET /results/{idResult}
    /**
     * @Route(path="/{user}/results/{result}", name="getOne", methods={ Request::METHOD_GET })
     * @param Result|null $result
     * @param User $user
     * @return JsonResponse
     */
    public function getOneResultOfUser(?User $user, ?Result $result): JsonResponse
    {
        if (null === $user) {
            return $this->error(Response::HTTP_NOT_FOUND, 'USER DOESNT EXISTS IN DB');
        }

        if (null === $result) {
            return $this->error(Response::HTTP_NOT_FOUND, 'RESULT DOESNT EXISTS IN DB');
        }

        /** @var Result resultDB */
        $resultsDB = $this->getDoctrine()
            ->getRepository(Result::class)
            ->findOneBy([
                'id'=>$result->getId(),
                'user'=>$user->getId()
            ]);

        return (null === $resultsDB)
            ? $this->error(Response::HTTP_NOT_FOUND, 'NOT FOUND')
            : new JsonResponse($resultsDB);
    }


    // TODO: Decidir si quitarlo, realiza lo mismo que el endpoint DELETE /results/{idResult}
    /**
     * @Route(path="/{user}/results/{result}", name="deleteOne", methods={ Request::METHOD_DELETE })
     * @param Result|null $result
     * * @param User|null $user
     * @return JsonResponse
     */
    public function deleteOneResultOfUser(?User $user, ?Result $result): JsonResponse
    {
        $em = $this->getDoctrine()->getManager();

        if (null === $user) {
            return $this->error(Response::HTTP_NOT_FOUND, 'USER DOESNT EXISTS IN DB');
        }

        if (null === $result) {
            return $this->error(Response::HTTP_NOT_FOUND, 'RESULT DOESNT EXISTS IN DB');
        }

        /** @var Result resultDB */
        $resultsDB = $this->getDoctrine()
            ->getRepository(Result::class)
            ->findOneBy([
                'id'=>$result->getId(),
                'user'=>$user->getId()
            ]);

        if($resultsDB === null) {
            return $this->error(Response::HTTP_NOT_FOUND, 'NOT FOUND');
        } else {
            $em->remove($resultsDB);
            $em->flush();
            return new JsonResponse( null, Response::HTTP_NO_CONTENT);
        }
    }

    /**
     * @param User $user
     * @Route(path="/{id}/results", name="deleteAll", methods={ Request::METHOD_DELETE })
     * @return JsonResponse
     */
    public function deleteAllResultsOfUser(?User $user): JsonResponse
    {
        $em = $this->getDoctrine()->getManager();
        if (null === $user) {
            return $this->error(Response::HTTP_NOT_FOUND, 'USER DOESNT EXISTS IN DB');
        }

        /** @var Result[] resultsDB */
        $resultsDB = $this->getDoctrine()
            ->getRepository(Result::class)
            ->findBy(['user'=>$user->getId()]);


        foreach ($resultsDB as $resultDB) {
            $em->remove($resultDB);
            $em->flush();
        }
        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }

    /*
     * TODO: Decidir si se implementan el ENDPOINT: POST /users/{userId}/results
     * TODO: Decidir si se implementan el ENDPOINT: PUT /users/{userId}/results/{resultId}
     */

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