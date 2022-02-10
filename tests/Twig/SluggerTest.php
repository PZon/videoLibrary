<?php

namespace App\Tests\Twig;

use PHPUnit\Framework\TestCase;
use App\Twig\AppExtension;

class SluggerTest extends TestCase
{

    /**
    * @dataProvider getSlugs
    */

    public function testSlugify( $string, $slug){

        $slugger = new AppExtension();
        $this->assertSame($slug, $slugger->slugify($string));
    }

    public function getSlugs(){

            yield['Lorem ipsum', 'loremipsum'];
            yield['Lorem  ipsum', 'loremipsum'];
            yield['Lorem-ipsum','loremipsum'];
    }

}
