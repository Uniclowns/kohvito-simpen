<?php

namespace Tests\Feature;

use App\Models\Meja;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic test example.
     */
    public function test_the_application_returns_a_successful_response(): void
    {
        Meja::create([
            'no_meja' => 'M01',
            'qr_code' => 'M01',
        ]);

        $response = $this->get('/M01');

        $response->assertStatus(200);
    }
}
