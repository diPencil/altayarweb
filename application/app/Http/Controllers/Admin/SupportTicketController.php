<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\SupportMessage;
use App\Models\SupportTicket;
use App\Traits\SupportTicketManager;
use Illuminate\Http\Request;

class SupportTicketController extends Controller
{
    use SupportTicketManager;

    public function __construct()
    {
        $this->activeTemplate = activeTemplate();
        $this->middleware(function ($request, $next) {
            $this->user = auth()->guard('admin')->user();
            return $next($request);
        });

        $this->userType = 'admin';
        $this->column = 'admin_id';
    }

    public function tickets()
    {
        $pageTitle = __('Support Tickets');
        $items = SupportTicket::orderBy('id','desc')->with('user', 'employee')->paginate(getPaginate());
        return view('admin.support.tickets', compact('items', 'pageTitle'));
    }

    public function pendingTicket()
    {
        $pageTitle = __('Pending Tickets');
        $items = SupportTicket::whereIn('status', [0,2])->orderBy('id','desc')->with('user', 'employee')->paginate(getPaginate());
        return view('admin.support.tickets', compact('items', 'pageTitle'));
    }

    public function closedTicket()
    {
        $pageTitle = __('Closed Tickets');
        $items = SupportTicket::where('status',3)->orderBy('id','desc')->with('user', 'employee')->paginate(getPaginate());
        return view('admin.support.tickets', compact('items', 'pageTitle'));
    }

    public function answeredTicket()
    {
        $pageTitle = __('Answered Tickets');
        $items = SupportTicket::orderBy('id','desc')->with('user', 'employee')->where('status',1)->paginate(getPaginate());
        return view('admin.support.tickets', compact('items', 'pageTitle'));
    }

    public function ticketReply($id)
    {
        $ticket = SupportTicket::with('user', 'employee')->where('id', $id)->firstOrFail();
        $pageTitle = __('Reply Ticket');
        $messages = SupportMessage::with('ticket','admin','employee','attachments')->where('support_ticket_id', $ticket->id)->orderBy('id','desc')->get();
        $employees = Employee::active()->orderBy('firstname')->orderBy('lastname')->get();
        return view('admin.support.reply', compact('ticket', 'messages', 'pageTitle', 'employees'));
    }

    public function assignTicket(Request $request, $id)
    {
        $request->validate([
            'agent_id' => 'nullable|integer|exists:agents,id',
        ]);

        $ticket = SupportTicket::findOrFail($id);
        $ticket->agent_id = $request->agent_id ?: 0;
        $ticket->save();

        $notify[] = ['success', $ticket->agent_id ? __('Ticket assigned successfully') : __('Ticket assignment removed successfully')];
        return back()->withNotify($notify);
    }

     // agent
     public function employeePendingTicket()
     {
         $pageTitle = __('Pending Tickets');
         $items = SupportTicket::where('agent_id', '!=', 0)->whereIn('status', [0,2])->orderBy('id','desc')->with('employee')->paginate(getPaginate());
         return view('admin.employees.support.tickets', compact('items', 'pageTitle'));
     }
 
     public function employeeClosedTicket()
     {
         $pageTitle = __('Closed Tickets');
         $items = SupportTicket::where('agent_id', '!=', 0)->where('status',3)->orderBy('id','desc')->with('employee')->paginate(getPaginate());
         return view('admin.employees.support.tickets', compact('items', 'pageTitle'));
     }
 
     public function employeeAnsweredTicket()
     {
         $pageTitle = __('Answered Tickets');
         $items = SupportTicket::where('agent_id', '!=', 0)->orderBy('id','desc')->with('employee')->where('status',1)->paginate(getPaginate());
         return view('admin.employees.support.tickets', compact('items', 'pageTitle'));
     }
     public function employeeTickets()
     {
         $pageTitle = __('Support Tickets');
         $items = SupportTicket::where('agent_id', '!=', 0)->orderBy('id','desc')->with('employee')->paginate(getPaginate());
         return view('admin.employees.support.tickets', compact('items', 'pageTitle'));
     }

    public function ticketDelete($id)
    {
        $message = SupportMessage::findOrFail($id);
        $path = getFilePath('ticket');
        if ($message->attachments()->count() > 0) {
            foreach ($message->attachments as $attachment) {
                fileManager()->removeFile($path.'/'.$attachment->attachment);
                $attachment->delete();
            }
        }
        $message->delete();
        $notify[] = ['success', __('Support ticket has been deleted successfully')];
        return back()->withNotify($notify);

    }

    public function deleteSupportTicket($id)
    {
        $ticket = SupportTicket::findOrFail($id);
        
        \DB::transaction(function () use ($ticket) {
            $path = getFilePath('ticket');
            foreach ($ticket->supportMessage as $message) {
                if ($message->attachments()->count() > 0) {
                    foreach ($message->attachments as $attachment) {
                        fileManager()->removeFile($path.'/'.$attachment->attachment);
                        $attachment->delete();
                    }
                }
                $message->delete();
            }
            $ticket->delete();
        });

        $notify[] = ['success', __('Support ticket has been deleted successfully')];
        return back()->withNotify($notify);
    }

    public function bulkDeleteSupportTickets(Request $request)
    {
        $request->validate([
            'ids' => 'required|array|min:1',
            'ids.*' => 'integer|exists:support_tickets,id',
        ]);

        $ids = $request->ids;

        \DB::transaction(function () use ($ids) {
            $path = getFilePath('ticket');
            $tickets = SupportTicket::whereIn('id', $ids)->get();
            foreach ($tickets as $ticket) {
                foreach ($ticket->supportMessage as $message) {
                    if ($message->attachments()->count() > 0) {
                        foreach ($message->attachments as $attachment) {
                            fileManager()->removeFile($path.'/'.$attachment->attachment);
                            $attachment->delete();
                        }
                    }
                    $message->delete();
                }
                $ticket->delete();
            }
        });

        $notify[] = ['success', __('Selected support tickets deleted successfully')];
        return back()->withNotify($notify);
    }

}
