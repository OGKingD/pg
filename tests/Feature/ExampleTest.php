<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function test_example()
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }

    public function testGetInspirationalText()
    {
        dd(bcrypt('12345678'));
        dd(inspirationalText());

    }

    public function testSendMailTemplate()
    {
        $ip_address = "127.0.0.1";
        send_email("abc@gmail.com", "David OG", 'Suspicious Login Attempt', 'Sorry your account was just accessed from an unknown IP address<br> ' .$ip_address. '<br>If this was you, please you can ignore this message or reset your account password.');


    }
}
