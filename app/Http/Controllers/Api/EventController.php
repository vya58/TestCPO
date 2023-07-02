<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;
use App\Http\Requests\CreateEventRequest;
use App\Http\Responses\ApiSuccessResponse;
use App\Http\Responses\ApiErrorResponse;
use App\Models\Event;

class EventController extends Controller
{
    /**
     * Получение списка событий
     *
     * @return ApiSuccessResponse|ApiErrorResponse
     */
    public function index()
    {
        // В ТЗ не указано, но можно добавить сортировку (например, по дате создания) и пагинацию
        // $events = Event::orderBy('created_at', 'desc')->paginate();

        $events = Event::all();

        return new ApiSuccessResponse($events);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Создание события.
     *
     * @param  CreateEventRequest  $request
     *
     * @return ApiSuccessResponse|ApiErrorResponse
     */
    public function store(CreateEventRequest $request): ApiSuccessResponse|ApiErrorResponse
    {
        $params = $request->safe();

        $params['user_id'] = Auth::id();

        $eventData = $params->toArray();

        try {
            $data = Event::create($eventData);
            return new ApiSuccessResponse($data, Response::HTTP_CREATED);
        } catch (\Exception $exception) {
            return new ApiErrorResponse([], Response::HTTP_INTERNAL_SERVER_ERROR, 'Событие создать не удалось. Попробуйте позже!');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Удаление события создателем.
     * Доступно только автору
     *
     * @param  int $id - id события
     * @return ApiSuccessResponse|ApiErrorResponse
     */
    public function destroy(string $id): ApiSuccessResponse|ApiErrorResponse
    {
        $event = Event::findOrFail($id);

        $this->authorize('delete', $event);

        try {
            $event->delete();
            return new ApiSuccessResponse([], Response::HTTP_NO_CONTENT);
        } catch (\Exception $exception) {
            return new ApiErrorResponse([], Response::HTTP_INTERNAL_SERVER_ERROR, 'Событие удалить не удалось. Попробуйте позже!');
        }
    }
}
