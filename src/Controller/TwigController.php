<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TwigController extends AbstractController
{
    /**
     * @Route("/twig", name="app_twig")
     */
    public function index(): Response
    {
        return $this->render('test/test.html.twig', [
            'name' => 'Moi meme',
        ]);
    }
}
