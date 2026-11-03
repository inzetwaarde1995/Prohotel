<?php

namespace App\Http\Controllers\Help;

use App\Http\Controllers\Controller;
use App\Services\Help\TicketService;
use App\Support\AuthenticatedUser;
use Illuminate\Http\Request;
use Illuminate\View\View;

class HelpCenterController extends Controller
{
    public function __invoke(Request $request, TicketService $tickets): View
    {
        return view('help-center.index', [
            'categories' => $tickets->categories(),
            'rules' => $tickets->rules(),
            'myTickets' => $tickets->tickets(AuthenticatedUser::from($request))->getCollection()->take(5),
        ]);
    }
}
