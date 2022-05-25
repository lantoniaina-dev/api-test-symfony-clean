<?php

namespace App\Controller;

use Symfony\Contracts\HttpClient\HttpClientInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Service\CalculeService;
use App\Data\TestData;


class TestController extends AbstractController
{
    private $client;
    private $logger;

    public function __construct(HttpClientInterface $client , LoggerInterface $logger)
    {
        $this->client = $client;
        $this->logger = $logger;
    }

    
    /**
     *  Page de test
     * 
     * @Route("/test", name="blog_list")
     */
    public function list(Request $request , TestData $datajson):Response
    {
        // // $datareq = $data->getData();
        $data = $this->client->request(
            'GET',
            'https://api.github.com/repos/symfony/symfony-docs',
        );

        $array = array(
            "id" => "1",
            "title" => "mon title",
        );
        // $json = $serializer -> serialize($array , 'json' );
        // $statusCode = $data->getStatusCode();
        // $contentType = $data->getHeaders()['content-type'][0];
        // $content = $data->toArray();
        // dd($content);
        // $tab = json_encode($content);
        
        //  dd($response);
        // $response = $this -> json($data , 200);
        // return $respose ;
       

        //  return new JsonResponse($content , 200 , [] );
        // 
        phpInfo();
        die;
        
         
    }
}