<?php

namespace App\Service;

class ResponseAuthService
{
    public function reponseSucces()
    {
        $reponseSucces =  [
            'status' => 200,
            'authentification' => true,
            'message' => "User authentifié",
            'class' => "alert alert-primary"
        ];
        return $reponseSucces;
    }

    public function reponseFailUser()
    {
        $reponseFailUser =  [
            'status' => 400,
            'authentification' => false,
            'message' => "No user with name",
            'class' => "alert alert-danger"
        ];
        return $reponseFailUser;
    }

    public function reponsePassword()
    {
        $reponsePassword =  [
            'status' => 400,
            'authentification' => false,
            'message' => "Password incorrect",
            'class' => "alert alert-danger"
        ];
        return $reponsePassword;
    }
}
