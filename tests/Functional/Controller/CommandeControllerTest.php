<?php

namespace App\Tests\Functional\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class CommandeControllerTest extends WebTestCase
{
    public function testGetCommandesCollection(): void
    {
        $client = static::createClient();
        
        $client->request('GET', '/api/commandes');
        
        $this->assertResponseIsSuccessful();
        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
    }

    public function testGetCommandeById(): void
    {
        $client = static::createClient();
        
        $client->request('GET', '/api/commandes/1');
        
        // Accepte 200 (existe) ou 404 (n'existe pas)
        $this->assertContains(
            $client->getResponse()->getStatusCode(),
            [200, 404]
        );
    }

    public function testCreateCommandeWithoutAuth(): void
    {
        $client = static::createClient();
        
        $commandeData = [
            'date_prestation' => '2026-03-15',
            'heure_liv' => '12:00:00',
            'prix_menu' => 50.00,
            'nombre_personne' => 4,
            'prix_liv' => 10.00
        ];
        
        $client->request(
            'POST',
            '/api/commandes',
            [],
            [],
            ['CONTENT_TYPE' => 'application/ld+json'],
            json_encode($commandeData)
        );
        
        // Doit retourner 401 (non authentifié) car POST nécessite ROLE_USER
        $this->assertResponseStatusCodeSame(401);
    }
}