<?php
namespace App\Service;

use MongoDB\Client;
use App\Entity\Commande;

class MongoDBService
{
    private $client;
    private $database;

    public function __construct()
    {
        $this->client = new Client("mongodb://localhost:27017");
        $this->database = $this->client->restaurant;
    }

    public function getCollection(string $name)
    {
        return $this->database->selectCollection($name);
    }

    public function getDatabase()
    {
        return $this->database;
    }
    
}