<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Component\HttpFoundation\JsonResponse;

use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;
use Symfony\Component\Serializer\Exception\NotEncodableValueException;

use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Users;
use App\Repository\UsersRepository;

class UsersController extends AbstractController
{

    /**
     * @Route("/users/get", name="get_users" ,methods = {"GET"})
     */
    public function getAll(
        UsersRepository $usersRepository ,
        ManagerRegistry $doctrine 
        ): JsonResponse
    {
        $entityManager = $doctrine->getManager();
        $client = $usersRepository -> findAll();
        $response = $this->json($client , 200, [] );
        return $response;
    }
    /**
     * @Route("/users/add", name="add_users" , methods = {"POST"})
     */
    public function add(
        Request $request ,
        ManagerRegistry $doctrine , 
        SerializerInterface $serializer , 
        EntityManagerInterface $em,
        ValidatorInterface $validator
        ): JsonResponse
    {
        $data = $request->getContent();
        try{
            $post = $serializer->deserialize($data, Users::class, 'json');
            $error = $validator->validate($post);
            if(count($error)>0) {
               return $this->json($error , 400 );
            }
            $em->persist($post);
            $em->flush();
            return $this->json($post , 201 , []);
        }
        catch(NotEncodableValueException $e){
            return  $this->json([
                'status'=> 400,
                'message'=> $e->getMessage()
            ],400);
        }
    }
}
