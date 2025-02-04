<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Post;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PostTest extends TestCase
{
    use RefreshDatabase; // Limpia la BD en cada prueba

    /** 🛠️ Prueba para crear un post exitosamente */
    public function test_can_create_post()
    {
        // Simular datos de un post
        $postData = [
            'title' => 'Mi Primer Post',
            'content' => 'Este es el contenido del post de prueba.'
        ];

        // Crear el post en la base de datos
        $post = Post::create($postData);

        // Verificar que el post se haya guardado en la BD
        $this->assertDatabaseHas('posts', [
            'title' => 'Mi Primer Post',
            'content' => 'Este es el contenido del post de prueba.'
        ]);
    }

    /** 🛠️ Prueba para actualizar un post exitosamente */
    public function test_can_update_post()
    {
        // Crear un post inicial en la BD
        $post = Post::create([
            'title' => 'Título Original',
            'content' => 'Contenido original del post.'
        ]);

        // Datos actualizados
        $updatedData = [
            'title' => 'Título Actualizado',
            'content' => 'Contenido actualizado del post.'
        ];

        // Actualizar el post
        $post->update($updatedData);

        // Verificar que los datos fueron actualizados en la BD
        $this->assertDatabaseHas('posts', [
            'id' => $post->id,
            'title' => 'Título Actualizado',
            'content' => 'Contenido actualizado del post.'
        ]);
    }
}
