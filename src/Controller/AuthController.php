<?php

namespace App\Controller;

use App\Entity\Users;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use App\Repository\UsersRepository;
use App\Service\ResponseAuthService;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Serializer\Exception\NotEncodableValueException;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;


class AuthController extends AbstractController
{

    private $serializer;
    private $em;
    private $usersRepository;
    private $encoder;
    private $responseAuthService;

    public function __construct(
        SerializerInterface $serializer,
        EntityManagerInterface $em,
        UsersRepository $usersRepository,
        UserPasswordEncoderInterface $encoder,
        ResponseAuthService $responseAuthService
    ) {
        $this->serializer = $serializer;
        $this->em = $em;
        $this->usersRepository = $usersRepository;
        $this->encoder = $encoder;
        $this->responseAuthService = $responseAuthService;
    }


    /**
     * @Route("/api/login", name="login" , methods = {"POST"} , schemes = {"http", "https"})
     */
    public function security(Request $request): Response
    {
        $user = $this->getUser();

        $key = "monkeycode2020secure";
        $data_token =  $user->eraseCredentials();
        $jwt = JWT::encode($data_token, $key, 'HS256');

        $reponseSucces =  [
            'status' => 200,
            'username' => $user->getUserIdentifier(),
            'roles' => $user->getRoles(),
            'authentification' => true,
            'message' => "User authentifi??",
            'class' => "alert alert-primary",
            'token'  => $jwt,
        ];

        $response = new Response();
        $cookieName = "user-token";
        $cookieValue = $jwt;
        $expires = time() + 36000;
        $content = json_encode($reponseSucces);
        $cookie = Cookie::create($cookieName, $cookieValue,  $expires);

        $response->headers->setCookie($cookie);
        $response->setContent($content);
        $response->setStatusCode(200);
        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }

    /**
     * @Route("/api/verify-token", name="verify_token" , methods = {"POST"} , schemes = {"http", "https"})
     */
    public function verifyToken(Request $request): JsonResponse
    {
        $reponseFail =  ['status' => 400, 'token_verify' => false,];
        $reponseSucces =  ['status' => 200, 'token_verify' => true,];

        $datareq = $request->getContent();
        $data = json_decode($datareq, true);
        $token = $data['token'];

        $key = "monkeycode2020secure";
        $decoded = JWT::decode($token, new Key($key, 'HS256'));
        $user = $this->usersRepository->findby(['username' => $decoded->username]);

        if (!$decoded) {
            return $this->json($reponseFail, 400, []);
        }

        if (!$user) {
            return $this->json($reponseFail, 400, []);
        }

        foreach ($user as $u) {
            $usermail = $u->getEmail();
            $mail = $decoded->email;
            if ($mail == $usermail) {
                return $this->json($reponseSucces, 200, []);
            } else {
                return $this->json($reponseFail, 400, []);
            }
        }
    }

    /**
     * @Route("/auth/login", name="test_login" , methods = {"POST"} )
     */
    public function login(Request $request): JsonResponse
    {

        $datareq = $request->getContent();
        $user = $this->serializer->deserialize($datareq, Users::class, 'json');
        $encoded = $this->encoder->encodePassword($user, $user->getPassword());

        $data = json_decode($request->getContent(), true);
        $name = $data['username'];
        $pass = $data['password'];

        $user = $this->usersRepository->findby(['username' => $name]);

        if (!$user) {
            return $this->json($this->responseAuthService->reponseFailUser(), 400, []);
        }

        foreach ($user as $u) {
            $userpass = $u->getPassword();

            if ($encoded == $userpass) {
                return $this->json($this->responseAuthService->reponseSucces(), 200, []);
            } else {
                return $this->json($this->responseAuthService->reponsePassword(), 400, []);
            }
        }
    }
}
