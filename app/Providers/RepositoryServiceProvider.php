<?php

namespace App\Providers;

use App\Interfaces\AdminInterface;
use App\Interfaces\ResetPasswordInterface;
use App\Interfaces\SendGridInterface;
use App\Interfaces\UserBankInterface;
use App\Interfaces\UserRepositoryInterface;
use App\Interfaces\UserTokenInterface;
use App\Interfaces\ZeptoInterface;
use App\Models\Admin;
use App\Repositories\AdminRepository;
use App\Repositories\ResetPasswordRepository;
use App\Repositories\SendGridRepository;
use App\Repositories\UserBankRepository;
use App\Repositories\UserRepository;
use App\Repositories\UserTokenRepository;
use App\Repositories\ZeptoRepository;
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
        $this->app->bind(AdminInterface::class, AdminRepository::class);
        $this->app->bind(SendGridInterface::class, SendGridRepository::class);
        $this->app->bind(ZeptoInterface::class, ZeptoRepository::class);
        $this->app->bind(UserTokenInterface::class, UserTokenRepository::class);
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
