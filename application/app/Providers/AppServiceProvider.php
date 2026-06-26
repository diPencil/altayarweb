<?php

namespace App\Providers;

use App\Models\User;
use App\Models\Order;
use App\Models\Employee;
use App\Lib\Searchable;
use App\Models\Artwork;
use App\Models\Deposit;
use App\Models\Frontend;
use App\Models\Language;
use App\Models\Withdrawal;
use App\Models\TourBooking;
use App\Models\TourPackage;
use App\Models\ChatConversation;
use App\Models\SupportTicket;
use App\Models\AdminNotification;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\URL;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register(): void
    {
        Builder::mixin(new Searchable);
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot(): void
    {
        $general = gs();
        $activeTemplate = activeTemplate();
        $viewShare['general'] = $general;
        $viewShare['activeTemplate'] = $activeTemplate;
        $viewShare['activeTemplateTrue'] = activeTemplate(true);
        $viewShare['language'] = Language::all();
        $viewShare['emptyMessage'] = 'No data';
        $viewShare['rtl'] = is_rtl();
        view()->share($viewShare);


        view()->composer('admin.components.tabs.user', function ($view) {
            $view->with([
                'bannedUsersCount'           => User::banned()->count(),
                'emailUnverifiedUsersCount' => User::emailUnverified()->count(),
                'mobileUnverifiedUsersCount'   => User::mobileUnverified()->count(),
                'kycUnverifiedUsersCount'   => User::kycUnverified()->count(),
                'kycPendingUsersCount'   => User::kycPending()->count(),
            ]);
        });
        view()->composer('admin.components.tabs.employee', function ($view) {
            $view->with([
                'bannedEmployeesCount'           => Employee::banned()->count(),
                'emailUnverifiedEmployeesCount' => Employee::emailUnverified()->count(),
                'mobileUnverifiedEmployeesCount'   => Employee::mobileUnverified()->count(),
                'kycUnverifiedEmployeesCount'   => Employee::kycUnverified()->count(),
                'kycPendingEmployeesCount'   => Employee::kycPending()->count(),
            ]);
        });

        view()->composer('admin.components.tabs.tour_package', function ($view) {
            $view->with([
                'allTourPackages'      => TourPackage::count(),
                'myTourPackages'      =>  TourPackage::where('user_type','admin')->count(),
                'allEmployeeTourPackages'      => TourPackage::where('user_type','agent')->count(),
                'pendingTourPackages' => TourPackage::pending()->count(),
          
            ]);
        });
        view()->composer('admin.components.tabs.deposit', function ($view) {
            $view->with([
                'allDepositsCount'        => Deposit::count(),
                'approvedDepositsCount'   => Deposit::approved()->count(),
                'pendingDepositsCount'    => Deposit::pending()->count(),
                'successfulDepositsCount' => Deposit::successful()->count(),
                'rejectedDepositsCount'   => Deposit::rejected()->count(),
                'initiatedDepositsCount'  => Deposit::initiated()->count(),
            ]);
        });
        view()->composer('admin.components.tabs.withdrawal', function ($view) {
            $view->with([
                'pendingWithdrawCount'    => Withdrawal::pending()->count(),
            ]);
        });
        view()->composer('admin.components.tabs.ticket', function ($view) {
            $view->with([
                'pendingTicketCount'         => SupportTicket::whereIn('status', [0, 2])->count(),
            ]);
        });
        view()->composer('admin.components.tabs.employee_ticket', function ($view) {
            $view->with([
                'pendingTicketCount'    =>  SupportTicket::where('agent_id', '!=', 0)->whereIN('status', [0, 2])->count(),
            ]);
        });
      
        view()->composer('admin.components.sidenav', function ($view) {
            $pendingChatConversationsCount = Schema::hasTable('chat_conversations')
                ? ChatConversation::open()->where('unread_admin_count', '>', 0)->count()
                : 0;

            $view->with([
                'bannedUsersCount'           => User::banned()->count(),
                'emailUnverifiedUsersCount' => User::emailUnverified()->count(),
                'mobileUnverifiedUsersCount'   => User::mobileUnverified()->count(),
                'kycUnverifiedUsersCount'   => User::kycUnverified()->count(),
                'kycPendingUsersCount'   => User::kycPending()->count(),
                'pendingTicketCount'         => SupportTicket::whereIn('status', [0,2])->count(),
                'employeePendingTicketCount'  => SupportTicket::where('agent_id', '!=', 0)->whereIN('status', [0, 2])->count(),
                'pendingDepositsCount'    => Deposit::pending()->count(),
                'pendingWithdrawCount'    => Withdrawal::pending()->count(),
                'pendingChatConversationsCount' => $pendingChatConversationsCount,

                'employeeBannedUsersCount'           => Employee::banned()->count(),
                'employeeEmailUnverifiedUsersCount' => Employee::emailUnverified()->count(),
                'employeeMobileUnverifiedUsersCount'   => Employee::mobileUnverified()->count(),
                'employeeKycUnverifiedUsersCount'   => Employee::kycUnverified()->count(),
                'employeeKycPendingUsersCount'   => Employee::kycPending()->count(),
                'pendingTourPackages' => TourPackage::pending()->count()
               
            ]);
        });

        view()->composer('admin.components.topnav', function ($view) {
            $view->with([
                'adminNotifications'=>AdminNotification::where('read_status',0)->with('user')->orderBy('id','desc')->take(10)->get(),
                'adminNotificationCount'=>AdminNotification::where('read_status',0)->count(),
            ]);
        });

        view()->composer('includes.seo', function ($view) {
            $seo = Frontend::where('data_keys', 'seo.data')->first();
            $view->with([
                'seo' => $seo ? $seo->data_values : $seo,
            ]);
        });

        $appUrl = (string) config('app.url');
        $host = request()->getHost();
        $isProductionDomain = in_array($host, ['altayarvip.com', 'www.altayarvip.com'], true);

        if (
            app()->environment('production') ||
            str_starts_with($appUrl, 'https://') ||
            $isProductionDomain ||
            ($general->force_ssl && config('app.env') !== 'local')
        ) {
            URL::forceScheme('https');
        }


        Paginator::useBootstrapFour();
    }
}
