<?php

namespace App\Tests;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use App\Entity\User;
use Hautelook\AliceBundle\PhpUnit\ReloadDatabaseTrait;

class AuthenticationTest extends ApiTestCase
{
   use ReloadDatabaseTrait;

   public function testLogin(): void
   {
       $client = self::createClient();
       $container = self::getContainer();

       $user = new User();
       $user->setEmail("test@example.com");
       $user->setPassword(
         $container->get('security.password_hasher')->hashPassword($user, "$3CR3T")
       );

       $manager = $container->get('doctrine')->getManager();
       $manager->persist($user);
       $manager->flush();

       $response = $client->request(
         'POST',
         '/authentication_token',
           [
               'headers' => ['Content-Type' => 'application/json'],
               'json' => [
                   'email' => "test@example.com",
                   'password' => "$3CR3T"
               ]
           ]
       );

       $json = $response->toArray();
       $this->assertResponseIsSuccessful();
       $this->assertArrayHasKey('token', $json);
   }
}