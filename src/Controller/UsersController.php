<?php

namespace App\Controller;

use App\Entity\Users;
use App\Repository\UsersRepository;
use App\Service\MailerService;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

use Symfony\Component\Validator\Validator\ValidatorInterface;

use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Serializer\Exception\NotEncodableValueException;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
// use App\Repository\UsersRepository;

class UsersController extends AbstractController
{

    /**
     * @Route("/api/user/get", name="get_user" ,methods = {"GET"})
     */
    public function getAll(
        UsersRepository $userRepository,
        ManagerRegistry $doctrine
    ): JsonResponse {
        $entityManager = $doctrine->getManager();
        $client = $userRepository->findAll();
        $response = $this->json($client, 200, []);
        return $response;
    }
    /**
     * @Route("/api/user/add", name="add_user" , methods = {"POST"})
     */
    public function add(
        Request $request,
        ManagerRegistry $doctrine,
        SerializerInterface $serializer,
        EntityManagerInterface $em,
        ValidatorInterface $validator,
        UserPasswordEncoderInterface $encoder,
        MailerService $mailerService
    ): JsonResponse {
        $data = $request->getContent();
        try {

            $user = $serializer->deserialize($data, Users::class, 'json');
            $error = $validator->validate($user);
            if (count($error) > 0) {
                return $this->json($error, 400);
            }

            $encoded = $encoder->encodePassword($user, $user->getPassword());
            $user->setPassword($encoded);
            // dd($user);
            $em->persist($user);
            $em->flush();

            $mailerService->send(
                "New Email",
                "randriamampionona9@gmail.com",
                "randriamampiononaandritoky@gmail.com",
                "email/contact.html.twig",
                [
                    "name" => "toky",
                    "description" => "email message",
                    "email" => "mon email"
                ]
            );

            return $this->json($user, 201, []);
        } catch (NotEncodableValueException $e) {
            return  $this->json([
                'status' => 400,
                'message' => $e->getMessage()
            ], 400);
        }
    }
}
