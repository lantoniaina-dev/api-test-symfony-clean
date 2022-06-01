<?php

namespace App\Controller;

use App\Entity\Users;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use App\Repository\UsersRepository;
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
    /**
     * @Route("/auth/login", name="login" , methods = {"POST"} )
     */
    public function login(
        Request $request,
        UsersRepository $usersRepository,
        ManagerRegistry $doctrine,
        SerializerInterface $serializer,
        UserPasswordEncoderInterface $encoder
    ): JsonResponse {
        $reponseSucces =  [
            'status' => 200,
            'authentification' => true,
            'message' => "User authentifié",
            'class' => "alert alert-primary"
        ];
        $reponseFailUser =  [
            'status' => 400,
            'authentification' => false,
            'message' => "No user with name",
            'class' => "alert alert-danger"
        ];
        $reponsePassword =  [
            'status' => 400,
            'authentification' => false,
            'message' => "Password incorrect",
            'class' => "alert alert-danger"
        ];

        $entityManager = $doctrine->getManager();

        $datareq = $request->getContent();
        $user = $serializer->deserialize($datareq, Users::class, 'json');
        // $encoder = $this->container->get('security.password_encoder');
        $encoded = $encoder->encodePassword($user, $user->getPassword());

        $data = json_decode($request->getContent(), true);
        $name = $data['name'];
        $pass = $data['password'];

        $user = $usersRepository->findby(['name' => $name]);
        if (!$user) {
            return $this->json($reponseFailUser, 400, []);
        }

        foreach ($user as $u) {
            $userpass = $u->getPassword();
            // dd($userpass , $encoded);
            if ($encoded == $userpass) {
                return $this->json($reponseSucces, 200, []);
            } else {
                return $this->json($reponsePassword, 400, []);
            }
        }
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
            'message' => "User authentifié",
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
    public function verifyToken(Request $request, SerializerInterface $serializer, UsersRepository $usersRepository): JsonResponse
    {
        $reponseFail =  ['status' => 400, 'token_verify' => false,];
        $reponseSucces =  ['status' => 200, 'token_verify' => true,];

        $datareq = $request->getContent();
        $data = json_decode($datareq, true);
        $token = $data['token'];

        $key = "monkeycode2020secure";
        $decoded = JWT::decode($token, new Key($key, 'HS256'));
        if (!$decoded) {
            return $this->json($reponseFail, 400, []);
        }

        $user = $usersRepository->findby(['username' => $decoded->username]);
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
}
