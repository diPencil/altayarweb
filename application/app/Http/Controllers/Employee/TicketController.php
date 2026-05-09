<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Traits\SupportTicketManager;

class TicketController extends Controller
{
    use SupportTicketManager;

    public $activeTemplate;
    public $layout;
    public $redirectLink;

    public function __construct()
    {
        $this->activeTemplate = activeTemplate();
        $this->layout = 'frontend';

        $this->middleware(function ($request, $next) {
            $this->user = employee();
            if ($this->user) {
                $this->layout = 'master';
            }
            return $next($request);
        });

        $this->redirectLink = 'employee.ticket.view';
        $this->userType     = 'employee';
        $this->column       = 'agent_id';
    }
}
