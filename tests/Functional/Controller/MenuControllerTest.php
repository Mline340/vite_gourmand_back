<?php

namespace App\Tests\Functional\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class MenuControllerTest extends WebTestCase
{
    public function testGetMenusCollection(): void
    {
        $client = static::createClient();
        
        $client->request('GET', '/api/menus');
        
        $this->assertResponseIsSuccessful();
        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
    }

    public function testGetMenuById(): void
    {
        $client = static::createClient();
        
        // Tester si un menu existe (si votre BDD test a des données)
        $client->request('GET', '/api/menus/1');
        
        // Accepte 200 (existe) ou 404 (n'existe pas)
        $this->assertContains(
            $client->getResponse()->getStatusCode(),
            [200, 404]
        );
    }

    public function testCreateMenuWithoutAuth(): void
    {
        $client = static::createClient();
        
        $menuData = [
            'titre' => 'Test Menu',
            'nombre_personne_mini' => 2,
            'prix_par_personne' => 15.99,
            'description' => 'Menu de test'
        ];
        
        $client->request(
            'POST',
            '/api/menus',
            [],
            [],
            ['CONTENT_TYPE' => 'application/ld+json'],
            json_encode($menuData)
        );
        
        // Doit retourner 401 (non authentifié)
        $this->assertResponseStatusCodeSame(401);
    }
}