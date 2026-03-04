<?php

namespace Tests\Unit;

use App\Models\Book;
use App\Models\User;
use App\Policies\BookPolicy;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
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
}
