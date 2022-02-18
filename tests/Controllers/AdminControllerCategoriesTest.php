<?php

namespace App\Tests\Controllers;


use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use App\Entity\Category;

//class AdminControllerCategoriesTest extends WebTestCase
class AdminControllerCategoriesTest 
{
	public function setUp():void{
		parent::set();
		$this->client = static::createClient();
        $this->client->disableReboot();

        $this->em = $this->client->getContainer()->get('doctrine.orm.entity_manager');
        $this->em->beginTransaction();
        $this->em->getConnection()->setAutoCommit(false);
	}
	
    public function testTextOnPage():void
    {
        
        $crawler = $this->client->request('GET', '/admin/categories');
		$this->assertSame('Categories list', $crawler->filter('h2')->text());
		$this->assertContains('Movies', $this->client->getResponse()->getContent());
    }

    public function tearDown():void
    {
        parent::tearDown();
        $this->em->rollback();    
        $this->em->close();    
        $this->em = null; // avoid memory leaks   
    }

    public function testNewCategory():void
    {
        $crawler = $this->client->request('GET', '/admin/categories');

        $form = $crawler->selectButton('Add')->form([
            'category[parent]' => 1,
            'category[name]' => 'Docs',
        ]);
        $this->client->submit($form);

        $category = $this->em->getRepository(Category::class)->findOneBy(['name'=>'Docs']);

        $this->assertNotNull($category);
        $this->assertSame('Docs', $category->getName());

    }
}
