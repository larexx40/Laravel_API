<?php

namespace App\Providers;

use App\Interfaces\ResetPasswordInterface;
use App\Interfaces\UserBankInterface;
use App\Interfaces\UserRepositoryInterface;
use App\Repositories\UserBankRepository;
use App\Repositories\UserRepository;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        // register all database repositories (interfase and implementation)
        $this->app->bind(UserRepositoryInterface::class, UserRepository::class);
        $this->app->bind(UserBankInterface::class, UserBankRepository::class);
        $this->app->bind(ResetPasswordInterface::class, ResetPasswordRepository::class);
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
