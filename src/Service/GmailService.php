<?php


namespace App\Service;

use Symfony\Component\Mime\Message;
use Symfony\Component\Mailer\Mailer;



class GmailService
{
    public function index($name, \Swift_Mailer $mailer)
    {
        // $messages = (new \Swift_Message('Hello Email'))
        //     ->setFrom('randriamampionona9@gmail')
        //     ->setTo('lrandriamampionona@meilleurtaux.com')
        //     ->setBody(
        //         $this->renderView(
        //             // templates/emails/registration.html.twig
        //             'emails/registration.html.twig',
        //             ['name' => $name]
        //         ),
        //         'text/html'
        //     );

        // $mailer->send($messages);

        // return $this->render(...);
    }
}
