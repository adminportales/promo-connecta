<?php

namespace Tests\Unit;

use App\Http\Controllers\AuthController;
use Illuminate\Http\Request;
use PHPUnit\Framework\TestCase;

class AuthControllerTest extends TestCase
{
    protected function createApplication()
    {
        $app = require __DIR__ . '/../bootstrap/app.php';
        $app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();
        return $app;
    }

    public function testRegister()
    {
        $this->createApplication();
        // Preparar la info para test
        $password = fake()->text(12);
        $request = new Request([
            'name' => fake()->name,
            'email' =>  fake()->unique()->safeEmail(),
            'password' => $password,
            'repeat_password' => $password,
        ]);

        // Ejecutar el metodo register
        $authController = new AuthController();
        $response =  $authController->register($request);

        /*
        $this->assertEquals('success', $response->status);
        $this->assertEquals('User registered successfully', $response->message); */
    }
}
