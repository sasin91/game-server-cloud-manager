<?php

namespace Tests\Unit;

use App\KeyPair;
use Tests\TestCase;
use App\Concerns\EncryptsAttributes;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class KeyPairTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_uses_the_trait()
    {
        $this->assertContains(
            EncryptsAttributes::class,
            class_uses(KeyPair::class)
        );
    }

    /**
     * Test that the attribute accessor & mutators function as intended.
     *
     * @test
     * @return void
     */
    public function test_it_encrypts_and_decrypts_the_keys()
    {
        $keyPair = factory(KeyPair::class)->create([
            'private_key' => 'abcdef1234',
            'public_key' => 'fedcba4321'
        ]);

        $this->assertEquals(
            'abcdef1234',
            $keyPair->private_key
        );
        $this->assertNotEquals(
            'abdef1234',
            $keyPair->getAttributes()['private_key']
        );

        $this->assertEquals(
            'fedcba4321',
            $keyPair->public_key
        );
        $this->assertNotEquals(
            'fedcba4321',
            $keyPair->getAttributes()['public_key']
        );
    }
}
