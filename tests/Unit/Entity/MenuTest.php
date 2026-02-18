<?php

namespace App\Tests\Unit\Entity;

use App\Entity\Menu;
use PHPUnit\Framework\TestCase;

class MenuTest extends TestCase
{
    public function testMenuCreation(): void
    {
        $menu = new Menu();
        $menu->setTitre('Menu Végétarien');
        $menu->setPrixParPersonne(15.99);
        $menu->setNombrePersonneMini(2);
        $menu->setDescription('Un délicieux menu végétarien équilibré');
        
        $this->assertEquals('Menu Végétarien', $menu->getTitre());
        $this->assertEquals(15.99, $menu->getPrixParPersonne());
        $this->assertEquals(2, $menu->getNombrePersonneMini());
        $this->assertEquals('Un délicieux menu végétarien équilibré', $menu->getDescription());
    }

}