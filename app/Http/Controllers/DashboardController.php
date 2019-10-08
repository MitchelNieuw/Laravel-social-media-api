<?php

namespace App\Http\Controllers;

use App\Repositories\MessageRepository;
use Illuminate\View\View;

/**
 * @package App\Http\Controllers
 */
class DashboardController extends Controller
{
    /**
     * @return View
     */
    public function index(): View
    {
        $messages = (new MessageRepository())->getMessagesFromFollowingUsers(
            auth()->user()->getAuthIdentifier()
        );
        return view('dashboard', compact('messages'));
    }
}