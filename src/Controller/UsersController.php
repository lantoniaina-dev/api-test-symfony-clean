<?php

namespace App\Controller;

use App\Entity\Users;
use App\Service\MailerService;
use App\Repository\UsersRepository;
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
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
// use App\Repository\UsersRepository;

class UsersController extends AbstractController
{
    private $serializer;
    private $em;
    private $validator;
    private $usersRepository;
    private $doctrine;
    private $encoder;
    private $mailerService;

    public function __construct(
        SerializerInterface $serializer,
        EntityManagerInterface $em,
        ValidatorInterface $validator,
        UsersRepository $usersRepository,
        ManagerRegistry $doctrine,
        MailerService $mailerService,
        UserPasswordEncoderInterface $encoder
    ) {
        $this->serializer = $serializer;
        $this->em = $em;
        $this->validator = $validator;
        $this->usersRepository = $usersRepository;
        $this->doctrine = $doctrine;
        $this->encoder = $encoder;
        $this->mailerService = $mailerService;
    }

    /**
     * @Route("/api/user/get", name="get_user" ,methods = {"GET"})
     */
    public function getAll(): JsonResponse
    {
        $client = $this->usersRepository->findAll();
        $response = $this->json($client, 200, []);
        return $response;
    }
    /**
     * @Route("/api/user/add", name="add_user" , methods = {"POST"})
     */
    public function add(Request $request): JsonResponse
    {
        $data = $request->getContent();
        try {

            $user = $this->serializer->deserialize($data, Users::class, 'json');
            $error = $this->validator->validate($user);
            if (count($error) > 0) {
                return $this->json($error, 400);
            }

            $encoded = $this->encoder->encodePassword($user, $user->getPassword());
            $user->setPassword($encoded);
            // dd($user);
            $this->em->persist($user);
            $this->em->flush();

            $this->mailerService->send(
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
