<?php

namespace App\Controller;

use App\Entity\Article;
use App\Entity\Comment;
use App\Repository\ArticleRepository;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Controller\TokenAuthenticatedController;
use App\Service\SaveImageService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ArticleController extends AbstractController implements ApiKeyAuthenticatedController
{

    private $articleRepository;
    private $categoryRepository;
    private $em;
    private $saveImageService;

    public function __construct(
        ArticleRepository $articleRepository,
        CategoryRepository $categoryRepository,
        EntityManagerInterface $em,
        SaveImageService $saveImageService
    ) {
        $this->articleRepository = $articleRepository;
        $this->categoryRepository = $categoryRepository;
        $this->em = $em;
        $this->saveImageService = $saveImageService;
    }


    /**
     * @Route("/api/category/get", name="get_category" , methods = {"GET"})
     */
    public function getCategory(): JsonResponse
    {
        $category = $this->categoryRepository->findAll();
        $response = $this->json($category, 200, []);
        return $response;
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
     * @Route("/api/article/add", name="add_article" , methods = {"POST"})
     */
    public function addArticle(Request $request): JsonResponse
    {
        $data = $request->getContent();
        $datadecode = json_decode($data, true);

        $categoryId =  $datadecode['categoryId'];
        $title =  $datadecode['title'];
        $description =  $datadecode['description'];
        $content =  $datadecode['content'];
        $imageBase64  =  $datadecode['imageBase64'];
        $extension  =  $datadecode['extension'];

        $uploads_path = $this->getParameter('uploads_directory');

        $filename = $this->saveImageService->store($uploads_path, $imageBase64, $extension);

        $category = $this->categoryRepository->find($categoryId);
        $now = new \DateTime();

        $article = new Article();
        $article->setCategory($category)
            ->setTitle($title)
            ->setContent($content)
            ->setCreatedAt($now)
            ->setImage($filename)
            ->setDescription($description);
        $this->em->persist($article);
        $this->em->flush();

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

    /**
     * @Route("/api/comment/add/{id}", name="article_add_comment" ,methods = {"POST"})
     */
    public function articleAddComment(int $id, Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $content = $data['comment'];
        if (!$content) {
            return $this->json(" No comment post to api !", 404);
        }

        $article = $this->articleRepository->find($id);
        if (!$article) {
            return $this->json("Aucun client for ID :$id", 404);
        }
        $comment = new Comment();
        $now = new \DateTime();
        $comment->setAuthor("setAuthor Inconnu ")
            ->setContent($content)
            ->setCreatedAt($now)
            ->setArticle($article);

        $this->em->persist($comment);
        $this->em->flush();

        $response = $this->json($comment, 200, []);

        return $response;
    }
}
