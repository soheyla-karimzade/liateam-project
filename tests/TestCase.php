<?php

namespace Tests;

use App\Models\User;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    protected function getTokenUser(User $user,$password='password'){


        // Log in the user and get the JWT token
        $loginResponse = $this->postJson('/api/login', [
            'email' => $user->email,
            'password' =>$password,
        ]);

        $content = $loginResponse->getContent();
        $decodedContent = json_decode($content, true);
        $token = $decodedContent['token'];
        return ['token'=>$token];


    }
}
