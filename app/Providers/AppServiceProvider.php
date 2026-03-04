<?php

namespace App\Providers;

<<<<<<< HEAD
=======
use App\Models\Book;
use App\Policies\BookPolicy;
use Illuminate\Support\Facades\Gate;
>>>>>>> c2c4e2082a3b629dcc5e9f32ee58a991798205af
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
<<<<<<< HEAD
        //
=======
        Gate::policy(Book::class, BookPolicy::class);
>>>>>>> c2c4e2082a3b629dcc5e9f32ee58a991798205af
    }
}
