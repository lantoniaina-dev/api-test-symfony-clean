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

    private $serializer;
    private $em;
    private $validator;
    private $clientRepository;
    private $doctrine;

    public function __construct(
        SerializerInterface $serializer,
        EntityManagerInterface $em,
        ValidatorInterface $validator,
        ClientRepository $clientRepository,
        ManagerRegistry $doctrine
    ) {
        $this->serializer = $serializer;
        $this->em = $em;
        $this->validator = $validator;
        $this->clientRepository = $clientRepository;
        $this->doctrine = $doctrine;
    }

    /**
     * @Route("/api/client/get", name="get_client" ,methods = {"GET"})
     */
    public function getAll(): JsonResponse
    {
        $client = $this->clientRepository->findAll();
        $response = $this->json($client, 200, []);
        return $response;
    }

    /**
     * @Route("/api/client/add", name="add_client", methods = {"POST"} )
     */
    public function add(Request $request): JsonResponse
    {
        $data = $request->getContent();
        try {
            $post = $this->serializer->deserialize($data, Client::class, 'json');
            $error = $this->validator->validate($post);
            if (count($error) > 0) {
                return $this->json($error, 400);
            }
            $this->em->persist($post);
            $this->em->flush();
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
    public function view(int $id): JsonResponse
    {
        $client = $this->clientRepository->find($id);
        if (!$client) {
            return $this->json("Aucun client for ID :$id", 404);
        }
        $response = $this->json($client, 200, []);
        return $response;
    }

    /**
     * @Route("/api/client/update/{id}", name="update_client" ,methods = {"POST"})
     */
    public function update(int $id, Request $request): JsonResponse
    {
        $client = $this->clientRepository->find($id);
        if (!$client) {
            return $this->json("Aucun client for ID :$id", 404);
        }
        $data = json_decode($request->getContent(), true);
        $name = $data['name'];
        $age = $data['age'];

        $client->setName($name);
        $client->setAge($age);
        $this->em->flush();

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
    public function delete(int $id,  ManagerRegistry $doctrine): JsonResponse
    {
        // $entityManager = $doctrine->getManager();
        $client = $this->clientRepository->find($id);
        if (!$client) {
            return $this->json("Aucun client for ID :$id", 404);
        }
        $this->em->remove($client);
        $this->em->flush();

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
    public function search(string $search = ""): JsonResponse
    {
        if ($search == "") {
            $client = $this->clientRepository->findAll();
            return $this->json($client, 200, []);
        }
        $client = $this->clientRepository->findby(
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
