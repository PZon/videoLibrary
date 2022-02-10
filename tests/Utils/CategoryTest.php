<?php

namespace App\Tests\Utils;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use App\Twig\AppExtension;

//class CategoryTest extends KernelTestCase
class CategoryTest 
{
    protected $mockedCatTreeAdminOptionList;
    protected $mockedCatTreeAdminPage;
    protected $mockedCatTreeFrontPage;

     public function setUp()
    {
        $kernel = self::bootKernel();
        $urlGI=$kernel->getContainer()->get('router');
		$testedClasses=['CatTreeFrontPage', 'CatTreeAdminOptionList', 'CatTreeAdminPage'];
		
		foreach($testedClasses as $class){
			$className = 'mocked'. $class;
			
			$this->className = $this->getMockedBuilder('App\Utils\\'.$class)->disableOriginalConstructor()->setMethods()->getMock();
			$this->className->urlGI->$urlGI;
		}
		
       // $em=$kernel->getContainer()->get('doctrine.orm.entity_manager');
    }


	/**
	* @dataProvider dataForCatTreeDropdownList
	*/
/*	public function testCatTreeDropdownList($arrayToCompare, $arrayDB){
		$this->mockedCatTreeAdminOptionList->catArrayDB=$arrayDB;
		$arrayDB = $this->mockedCatTreeAdminOptionList->buildTree();
		$this->assertSame($arrayToCompare, $this->mockedCatTreeAdminOptionList->getCategoryList($arrayDB));
		
	}
	
	public function dataForCatTreeDropdownList(){
		yield [
			[
			 ['name'=>'Movies', 'id'=>1],
			 ['name'=>'--Horror', 'id'=>12],
			 ['name'=>'--Comedy', 'id'=>13]
			],
			[
			 ['name'=>'Movies', 'id'=>1, 'parent_id'=>null],
			 ['name'=>'Horror', 'id'=>12, 'parent_id'=>1],
			 ['name'=>'Comedy', 'id'=>13, 'parent_id'=>1]
			]
		];
	}*/

    public function testSomething(){
        //dd($this->mockedCatTreeFrontPage);
        dd($this->className);
    }
}
