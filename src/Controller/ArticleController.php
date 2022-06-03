<?php

namespace App\Controller;

use App\Repository\ArticleRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ArticleController extends AbstractController
{

    private $articleRepository;

    public function __construct(ArticleRepository $articleRepository)
    {
        $this->articleRepository = $articleRepository;
    }

    /**
     * @Route("/api/article/get", name="app_article" , methods = {"GET"})
     */
    public function getAll(): JsonResponse
    {
        $article = $this->articleRepository->findAll();
        $response = $this->json($article, 200, []);
        return $response;
    }
    /**
     * @Route("/api/article/view/{id}", name="view_article" ,methods = {"GET"})
     */
    public function view(int $id): JsonResponse
    {
        $article = $this->articleRepository->find($id);
        if (!$article) {
            return $this->json("Aucun client for ID :$id", 404);
        }
        $data = [
            'id' => $article->getId(),
            'title' => $article->getTitle(),
            'description' => $article->getDescription(),
            'image' => $article->getImage(),
            'category' => $article->getCategory(),
            'comments' => $article->getComments(),
            'content' => $article->getContent(),
            'createdAt' => $article->getCreatedAt(),
            'updatedAt' => $article->getUpdatedAt(),
        ];
        $response = $this->json($data, 200, []);
        return $response;
    }
}
