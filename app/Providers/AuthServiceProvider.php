<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

// Import your Models
use App\Models\User;
use App\Models\Aduan;
use App\Models\DasarHukum;
use App\Models\Infographic;
use App\Models\KonfigurasiAplikasi;
use App\Models\PublicationCategory;
use App\Models\Signature;
use App\Models\SKL;
use App\Models\SKT;
use App\Models\SKTDocumentLabel;
use App\Models\Sppd;
use App\Models\Spt;
use App\Models\OtpVerification; // New OTP model

// Blog Models
use App\Models\Blog\Category as BlogPostCategory;
use App\Models\Blog\Post as BlogPost;

// Third-party Models
use Spatie\Activitylog\Models\Activity;
use BezhanSalleh\FilamentExceptions\Models\Exception;

// Import your Policies
use App\Policies\UserPolicy;
use App\Policies\AduanPolicy;
use App\Policies\DasarHukumPolicy;
use App\Policies\InfographicPolicy;
use App\Policies\KonfigurasiAplikasiPolicy;
use App\Policies\PublicationCategoryPolicy;
use App\Policies\SignaturePolicy;
use App\Policies\SKLPolicy;
use App\Policies\SKTPolicy;
use App\Policies\SKTDocumentLabelPolicy;
use App\Policies\SppdPolicy;
use App\Policies\SptPolicy;
use App\Policies\OtpVerificationPolicy; // New OTP policy

// Blog Policies
use App\Policies\Blog\CategoryPolicy as BlogPostCategoryPolicy;
use App\Policies\Blog\PostPolicy as BlogPostPolicy;

// Third-party Policies
use App\Policies\ActivityPolicy;
use App\Policies\ExceptionPolicy;

// Spatie Permission
use Spatie\Permission\Models\Permission;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        // Core Application Models
        User::class => UserPolicy::class,
        Aduan::class => AduanPolicy::class,
        DasarHukum::class => DasarHukumPolicy::class,
        Infographic::class => InfographicPolicy::class,
        KonfigurasiAplikasi::class => KonfigurasiAplikasiPolicy::class,
        PublicationCategory::class => PublicationCategoryPolicy::class,
        Signature::class => SignaturePolicy::class,
        
        // Document Management Models  
        SKL::class => SKLPolicy::class,
        SKT::class => SKTPolicy::class,
        SKTDocumentLabel::class => SKTDocumentLabelPolicy::class,
        
        // Travel Management Models
        Sppd::class => SppdPolicy::class,
        Spt::class => SptPolicy::class,
        
        // Authentication Models
        OtpVerification::class => OtpVerificationPolicy::class,
        
        // Blog Models
        BlogPostCategory::class => BlogPostCategoryPolicy::class,
        BlogPost::class => BlogPostPolicy::class,
        
        // Third-party Models
        Activity::class => ActivityPolicy::class,
        Exception::class => ExceptionPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();

    // Allow 'Super Admin' full access
    Gate::before(function ($user, $ability) {
        return $user->hasRole('super_admin') ? true : null;
    });

    // Dynamically register permissions from Spatie
    foreach (Permission::all() as $permission) {
        Gate::define($permission->name, function ($user) use ($permission) {
            return $user->hasPermissionTo($permission);
        });
    }
    }
}
