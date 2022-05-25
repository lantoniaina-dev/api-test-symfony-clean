<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\HttpClient\HttpClientInterface;

use App\Entity\Users;
use App\Repository\UsersRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\EntityManagerInterface;

class AuthController extends AbstractController
{
    /**
     * @Route("/auth/login", name="auth_login" , methods = {"POST"} , shemes= {"http","https"})
     */
    public function login(
        Request $request ,
        UsersRepository $usersRepository ,
        ManagerRegistry $doctrine 
        ): JsonResponse
    {
        $reponseSucces =  [
            'status' => 200,
            'authentification' => true,
            'message' => "User authentifier",
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

        $data = json_decode($request->getContent(), true);
        $name = $data['name'];
        $pass = $data['password'];
        
        $user = $usersRepository->findby(['name' => $name]);
        if (!$user) {
            return $this->json($reponseFailUser , 400, [] );
        }

        foreach($user as $u) { 
            $userpass = $u ->getPassword();
            if($pass == $userpass){
                return $this->json($reponseSucces , 200, [] );
            }
            else{
                return $this->json($reponsePassword , 400, [] );
            }
        }
    }
}
