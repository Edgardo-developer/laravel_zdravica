<?php

namespace Tests\Feature;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class TestTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    public function test_connection(): void
    {
        $client = new Client(['verify'=>false]);
        $body = json_encode([
            "message" => "https://images.dog.ceo/breeds/setter-english/n02100735_1467.jpg",
            "status" => "success"
        ], JSON_THROW_ON_ERROR);
        $request = new Request('GET','https://dog.ceo/api/breeds/image/random', [], $body);
        $data = $client->sendAsync($request)->wait();
        dd($data->getStatusCode());
    }
}
