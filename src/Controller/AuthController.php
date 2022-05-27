<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Component\Serializer\SerializerInterface;

use App\Entity\User;
use App\Repository\UsersRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Http\Attribute\CurrentUser;


class AuthController extends AbstractController
{
    /**
     * @Route("/auth/login", name="login" , methods = {"POST"} )
     */
    public function login(
        Request $request ,
        UsersRepository $usersRepository ,
        ManagerRegistry $doctrine,
        SerializerInterface $serializer ,
        UserPasswordEncoderInterface $encoder 
        ): JsonResponse
    {
        $reponseSucces =  [
            'status' => 200,
            'authentification' => true,
            'message' => "User authentifié",
            'class'=>"alert alert-primary"
        ];
        $reponseFailUser =  [
            'status' => 400,
            'authentification' => false,
            'message' => "No user with name",
            'class'=>"alert alert-danger"
        ];
        $reponsePassword =  [
            'status' => 400,
            'authentification' => false,
            'message' => "Password incorrect",
            'class'=>"alert alert-danger"
        ];

        $entityManager = $doctrine->getManager();

        $datareq = $request->getContent();
        $user = $serializer->deserialize($datareq, User::class, 'json');
        // $encoder = $this->container->get('security.password_encoder');
        $encoded = $encoder->encodePassword($user, $user->getPassword());

        $data = json_decode($request->getContent(), true);
        $name = $data['name'];
        $pass = $data['password'];
        
        $user = $usersRepository->findby(['name' => $name]);
        if (!$user) {
            return $this->json($reponseFailUser , 400, [] );
        }

        foreach($user as $u) { 
            $userpass = $u ->getPassword();
            // dd($userpass , $encoded);
            if($encoded == $userpass){
                return $this->json($reponseSucces , 200, [] );
            }
            else{
                return $this->json($reponsePassword , 400, [] );
            }
        }
    }

    /**
     * @Route("/login", name="login" , methods = {"POST"} )
     */
    public function security(Request $request ): JsonResponse
    {
       $user = $this->getUser();

       $reponseSucces =  [
        'status' => 200,
        'username' => $user->getUsername(),
        'roles' => $user->getRoles(),
        'authentification' => true,
        'token'=> "dfvbkjbkvjndkjgbkfkjnb",
        'message' => "User authentifié",
        'class'=>"alert alert-primary",
      ];

      return $this->json($reponseSucces , 200, [] );
    }
        

        

        
}
