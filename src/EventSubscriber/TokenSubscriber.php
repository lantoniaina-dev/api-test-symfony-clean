<?php

namespace App\EventSubscriber;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\KernelEvents;
use App\Controller\ApiKeyAuthenticatedController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class TokenSubscriber implements EventSubscriberInterface
{
    // private $apikey;

    // public function __construct($apikey)
    // {
    //     $this->apikey = $apikey;
    // }

    public function onKernelController(ControllerEvent $event)
    {
        $controller = $event->getController();

        // lorsqu'une classe de contrôleur définit plusieurs méthodes d'action, le contrôleur est renvoyé sous la forme [$controllerInstance, 'methodName']
        if (is_array($controller)) {
            $controller = $controller[0];
        }

        if ($controller instanceof ApiKeyAuthenticatedController) {
            $valide_apikey = "abcef";
            define("VALIDE_APIKEY", "abcef");

            $headers = $event->getRequest()->headers;
            $apikey = $headers->get('apikey');

            if ($apikey !== VALIDE_APIKEY) {
                $response = new JsonResponse();
                $response->setStatusCode(JsonResponse::HTTP_NOT_FOUND);
                $response->setData([
                    'message' => 'Api protected , Invalide ApiKey , Vous n avez pas d acces',
                    'status' => 406
                ]);
                $response->send();
                throw new AccessDeniedHttpException('This action needs a valid token!');
            }
        }
    }

    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::CONTROLLER => 'onKernelController',
        ];
    }
}
