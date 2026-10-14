<?php

namespace App\Providers;

use App\Models\Book;
use App\Models\Category;
use App\Models\Fine;
use App\Models\Loan;
use App\Models\Member;
use App\Policies\BookPolicy;
use App\Policies\CategoryPolicy;
use App\Policies\FinePolicy;
use App\Policies\LoanPolicy;
use App\Policies\MemberPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        Member::class => MemberPolicy::class,
        Category::class => CategoryPolicy::class,
        Book::class => BookPolicy::class,
        Loan::class => LoanPolicy::class,
        Fine::class => FinePolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();
    }
}