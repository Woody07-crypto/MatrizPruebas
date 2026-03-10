<?php

namespace Tests\Unit;

use App\Models\Book;
use App\Models\User;
use App\Policies\BookPolicy;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class BookPolicyTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function view_permite_ver_un_libro(): void
    {
        $user = User::factory()->create();
        $book = Book::factory()->create();
        $policy = new BookPolicy;

        $this->assertTrue($policy->view($user, $book));
    }

    #[Test]
    public function view_any_permite_listar_libros(): void
    {
        $user = User::factory()->create();
        $policy = new BookPolicy;

        $this->assertTrue($policy->viewAny($user));
    }

    #[Test]
    public function update_permite_actualizar_libro_a_usuario_con_rol_bibliotecario(): void
    {
        // 1. Crear el rol "Bibliotecario" en la BD (necesario para Spatie Permission)
        Role::create(['name' => 'Bibliotecario']);
        // 2. Crear un usuario y asignarle ese rol
        $user = User::factory()->create();
        $user->assignRole('Bibliotecario');
        // 3. Crear un libro de prueba
        $book = Book::factory()->create();
        // 4. Instanciar la política (es la que decide quién puede hacer qué)
        $policy = new BookPolicy;

        // 5. VERIFICACIÓN: La política debe devolver true = "sí puede actualizar"
        $this->assertTrue($policy->update($user, $book));
    }

    #[Test]
    public function update_deniega_actualizar_libro_a_usuario_sin_rol_bibliotecario(): void
    {
        // 1. Usuario NORMAL (sin rol Bibliotecario)
        $user = User::factory()->create();
        $book = Book::factory()->create();
        $policy = new BookPolicy;

        // 2. VERIFICACIÓN: La política debe devolver false = "no puede actualizar"
        $this->assertFalse($policy->update($user, $book));
    }

    #[Test]
    public function delete_permite_eliminar_libro_a_usuario_con_rol_bibliotecario(): void
    {
        // Igual que update_permite: Bibliotecario SÍ puede eliminar
        Role::create(['name' => 'Bibliotecario']);
        $user = User::factory()->create();
        $user->assignRole('Bibliotecario');
        $book = Book::factory()->create();
        $policy = new BookPolicy;

        $this->assertTrue($policy->delete($user, $book));
    }

    #[Test]
    public function delete_deniega_eliminar_libro_a_usuario_sin_rol_bibliotecario(): void
    {
        // Usuario normal NO puede eliminar libros
        $user = User::factory()->create();
        $book = Book::factory()->create();
        $policy = new BookPolicy;

        $this->assertFalse($policy->delete($user, $book));
    }
}
