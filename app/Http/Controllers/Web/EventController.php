<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\Event;
use App\Services\ParticipationService;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class EventController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $user = Auth::user();
        $allEvents = Event::paginate();
        $myEvents = $user->events;

        return view('event.index', compact('allEvents', 'myEvents'));
    }

    /**
     * Display a listing of the resource.
     */
    public function self(): View
    {
        $user = Auth::user();
        $allEvents = Event::paginate();
        $myEvents = $user->events;

        return view('event.self', compact('allEvents', 'myEvents'));
    }

    /**
     * Display the specified resource.
     *
     * @param  Request
     *
     * @return View
     */
    public function show(Request $request): View
    {
        $user = Auth::user();
        $event = Event::find((int) $request->id);
        $allEvents = Event::paginate();
        $myEvents = $user->events;
        $members = $event->users;

        return view('event.show', compact('allEvents', 'event', 'myEvents', 'members'));
    }

    /**
     * Принятие-отказ от участия в событии
     *
     * @param  Request
     *
     * @return RedirectResponse
     */
    public function create(Request $request): RedirectResponse
    {
        $eventId = (int) $request->id;

        if (!ParticipationService::createParticipation($eventId)) {
            ParticipationService::deleteParticipation($eventId);
        }
        return redirect()->action([EventController::class, 'show'],  ['id' => $eventId]);
    }
}
