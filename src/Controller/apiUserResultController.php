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
    * @Route(path="", name="options", methods={ Request::METHOD_OPTIONS })
    * @return JsonResponse
    */
    public function optionsUserResult(): JsonResponse
    {
        // devolver respuesta
        return new JsonResponse(null, Response::HTTP_OK, array("Allow" => "GET, DELETE, OPTIONS"));
    }
    
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

        return (empty($results))
            ? $this->error(Response::HTTP_NOT_FOUND, 'NOT FOUND')
            : new JsonResponse($results);
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
