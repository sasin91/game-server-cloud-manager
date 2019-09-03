<?php

namespace Tests\Unit;

use App\Concerns\EncryptsAttributes;
use Tests\TestCase;
use App\Environment;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Contracts\Encryption\Encrypter;

class EnvironmentTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_uses_the_trait()
    {
        $this->assertContains(
            EncryptsAttributes::class,
            class_uses(Environment::class)
        );
    }

    public function test_it_returns_a_configured_encrypter()
    {
        $environment = new Environment;
        $environment->encryption_key = $environment->generateEncryptionKey();

        $this->assertInstanceOf(Encrypter::class, $environment->encrypter());
    }

    public function test_it_encrypts_the_given_variables()
    {
        $environment = factory(Environment::class)->create(['variables' => ['A' => 'B', 'C' => 'D']]);

        $this->assertNotEquals(
            'A=B'.PHP_EOL.'C=D',
            $environment->variables
        );
    }

    public function test_it_decrypts_the_variables()
    {
        $environment = factory(Environment::class)->create(['variables' => ['A' => 'B', 'C' => 'D']]);

        $this->assertEquals('B', $environment->variable('A'));
        $this->assertEquals('D', $environment->variable('C'));
    }
}
