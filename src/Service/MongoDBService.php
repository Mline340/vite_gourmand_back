<?php
namespace App\Service;

use MongoDB\Client;

class MongoDBService
{
    private $client;
    private $database;

    public function __construct(string $mongodbUrl, string $mongodbDb)
    {
        $this->client = new Client($mongodbUrl);
        $this->database = $this->client->selectDatabase($mongodbDb);
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