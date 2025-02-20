<?php

namespace App\Tests;

use App\DataFixtures\User\UserFixtures;

class AuthenticationTest extends AbstractTest
{

    public function setUp(): void
    {
        parent::setUp(); // TODO: Change the autogenerated stub
        $this->loadFixtures([
            UserFixtures::class,
        ]);
    }

    public function testLogin(): void
    {
        $client = self::createClient();
        // retrieve a token
        $response = $client->request('POST', '/api2/auth', [
            'headers' => ['Content-Type' => 'application/json'],
            'json' => [
                'username' => UserFixtures::USERNAME,
                'password' => UserFixtures::PASSWORD,
            ],
        ]);

        $json = $response->toArray();
        $this->assertResponseIsSuccessful();
        $this->assertArrayHasKey('token', $json);

        // test not authorized
        $client->request('GET', '/api/users/current');
        $this->assertResponseStatusCodeSame(401);

        // test authorized
        $client->request('GET', '/api/users/current', ['auth_bearer' => $json['token']]);
        $this->assertResponseIsSuccessful();
    }
}