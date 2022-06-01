<?php

namespace App\Controller;

use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;
use Symfony\Component\Serializer\Exception\NotEncodableValueException;

use App\Entity\Client;
use App\Repository\ClientRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;

class ClientController extends AbstractController
{
    /**
     * @Route("/api/client/get", name="get_client" ,methods = {"GET"})
     */
    public function getAll(
        ClientRepository $clientRepository,
        ManagerRegistry $doctrine
    ): JsonResponse {
        $entityManager = $doctrine->getManager();
        $client = $clientRepository->findAll();
        $response = $this->json($client, 200, []);
        return $response;
    }

    /**
     * @Route("/api/client/add", name="add_client", methods = {"POST"} )
     */
    public function add(
        Request $request,
        ManagerRegistry $doctrine,
        SerializerInterface $serializer,
        EntityManagerInterface $em,
        ValidatorInterface $validator
    ): JsonResponse {
        $data = $request->getContent();
        try {
            $post = $serializer->deserialize($data, Client::class, 'json');
            $error = $validator->validate($post);
            if (count($error) > 0) {
                return $this->json($error, 400);
            }
            $em->persist($post);
            $em->flush();
            return $this->json($post, 201, []);
        } catch (NotEncodableValueException $e) {
            return  $this->json([
                'status' => 400,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * @Route("/api/client/view/{id}", name="view_client" ,methods = {"GET"})
     */
    public function view(int $id, ClientRepository $clientRepository,  ManagerRegistry $doctrine): JsonResponse
    {
        $entityManager = $doctrine->getManager();
        $client = $clientRepository->find($id);
        if (!$client) {
            return $this->json("Aucun client for ID :$id", 404);
        }
        $response = $this->json($client, 200, []);
        return $response;
    }

    /**
     * @Route("/api/client/update/{id}", name="update_client" ,methods = {"POST"})
     */
    public function update(int $id, Request $request, ClientRepository $clientRepository,  ManagerRegistry $doctrine): JsonResponse
    {
        $entityManager = $doctrine->getManager();
        $client = $clientRepository->find($id);
        if (!$client) {
            return $this->json("Aucun client for ID :$id", 404);
        }
        $data = json_decode($request->getContent(), true);
        $name = $data['name'];
        $age = $data['age'];

        $client->setName($name);
        $client->setAge($age);
        $entityManager->flush();

        $dataclient =  [
            'id' => $client->getId(),
            'name' => $client->getName(),
            'age' => $client->getAge(),
        ];
        $response = $this->json($dataclient, 201, []);
        return $response;
    }

    /**
     * @Route("/api/client/delete/{id}", name="delete_client" ,methods = {"GET"})
     */
    public function delete(int $id, ClientRepository $clientRepository,  ManagerRegistry $doctrine): JsonResponse
    {
        $entityManager = $doctrine->getManager();
        $client = $clientRepository->find($id);
        if (!$client) {
            return $this->json("Aucun client for ID :$id", 404);
        }
        $entityManager->remove($client);
        $entityManager->flush();

        $response = $this->json([
            'delete ID ' => $id,
            'status' => 200,
            'message' => "Client delete  ID : $id",
        ], 200);
        return $response;
    }

    /**
     * @Route("/api/client/search/{search}", name="search_client" ,methods = {"GET"})
     */
    public function search(
        EntityManagerInterface $em,
        ManagerRegistry $doctrine,
        ClientRepository $clientRepository,
        string $search = ""
    ): JsonResponse {
        $entityManager = $doctrine->getManager();
        if ($search == "") {
            $client = $clientRepository->findAll();
            return $this->json($client, 200, []);
        }
        $client = $clientRepository->findby(
            ['name' => $search],
            ['age' => 'ASC']
        );
        if (!$client) {
            return $this->json("Aucun client ", 404);
        }

        $response = $this->json($client, 200, []);
        return $response;
    }


    /**
     * @Route("/api/client/test", name="client" )
     */
    public function index(ManagerRegistry $doctrine): Response
    {
        $entityManager = $doctrine->getManager();
        $client = new Client();
        $client->setName('Fanantenana');
        $client->setAge(56);
        $entityManager->persist($client);
        $entityManager->flush();
        return new Response('Saved new product with id ' . $client->getId());
    }
}
