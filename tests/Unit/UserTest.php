<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UserTest extends TestCase
{
    use RefreshDatabase; // Resetea la BD después de cada prueba

    /** 🛠️ Prueba para crear un usuario exitosamente */
    public function test_can_create_user()
    {
        // Simular datos de usuario
        $userData = [
            'name' => 'John Doe',
            'email' => 'johndoe@example.com',
            'role' => 'user',
            'password' => bcrypt('password123')
        ];

        // Crear usuario
        $user = User::create($userData);

        // Verificar que el usuario se haya guardado en la BD
        $this->assertDatabaseHas('users', ['email' => 'johndoe@example.com']);
    }

    /** 🛠️ Prueba para actualizar un usuario */
    public function test_can_update_user()
    {
        // Crear un usuario inicial
        $user = User::factory()->create([
            'name' => 'Old Name',
            'email' => 'old@example.com',
            'role' => 'user',
        ]);

        // Actualizar datos del usuario
        $user->update(['name' => 'New Name']);

        // Verificar que el usuario fue actualizado
        $this->assertDatabaseHas('users', ['name' => 'New Name']);
    }
}
