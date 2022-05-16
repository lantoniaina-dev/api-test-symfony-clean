<?php

namespace App\Controller;

use Symfony\Contracts\HttpClient\HttpClientInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\CalculeService;


class TestController 
{
    private $client;
    private $logger;

    public function __construct(HttpClientInterface $client , LoggerInterface $logger)
    {
        $this->client = $client;
        $this->logger = $logger;
    }

    
    /**
     * @Route("/test", name="blog_list")
     */
    public function list(Request $request , CalculeService $calcule):Response
    {
        $response = $this->client->request(
            'GET',
            'https://api.github.com/repos/symfony/symfony-docs'
        );

        $statusCode = $response->getStatusCode();
        // $statusCode = 200
        $contentType = $response->getHeaders()['content-type'][0];
        // $contentType = 'application/json'
        $content = $response->getContent();
        // $content = '{"id":521583, "name":"symfony-docs", ...}'
        $content = $response->toArray();
        $tab = json_encode($content);
        
        $this->logger->error("TEST TEST TEST TEST TEST ");
        $prix = $calcule->taxe(100,20);
        //  dd($tab, $statusCode , $contentType , $response);
        //  return new Response( 
        //      '<html><body>Lucky number: '.$content.'</body></html>'
        //  );
        // return new Response(
        //     "$tab"
        // );
       
    }
}