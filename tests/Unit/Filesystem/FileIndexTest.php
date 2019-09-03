<?php

namespace Tests\Unit\Filesystem;

use App\Filesystem\FileIndex;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class FileIndexTest extends TestCase
{
    public function test_it_discovers_the_classes_in_the_given_path()
    {
        $discovered = FileIndex::scan(base_path('tests/__Fixtures__'));

        $this->assertEquals([
            [
                'file' => 'tests/__Fixtures__/DummyClass.php',
                'name' => 'DummyClass',
                'class' => 'Tests\__Fixtures__\DummyClass'
            ]
        ], $discovered->toArray());
    }
}
