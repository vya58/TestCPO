<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Http\Responses\ApiSuccessResponse;
use App\Http\Responses\ApiErrorResponse;
use App\Models\Participation;
use App\Services\ParticipationService;

class ParticipationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Запись на участие в событии.
     *
     * @param  int $id - $id события
     *
     * @return ApiSuccessResponse|ApiErrorResponse
     */
    public function store(int $id): ApiSuccessResponse|ApiErrorResponse
    {
        $participation = ParticipationService::createParticipation($id);

        if (!$participation) {
            return new ApiErrorResponse([], Response::HTTP_UNPROCESSABLE_ENTITY, Participation::ERROR_MESSAGE['isParticipant']);
        }

        return new ApiSuccessResponse($participation, Response::HTTP_CREATED);
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
     * Удаление записи на участие в событии.
     *
     * @param  string $id - $id события
     *
     * @return ApiSuccessResponse|ApiErrorResponse
     */
    public function destroy(string $id): ApiSuccessResponse|ApiErrorResponse
    {
        $responseCode = ParticipationService::deleteParticipation((int) $id);

        if (!$responseCode) {
            return new ApiErrorResponse([], Response::HTTP_UNPROCESSABLE_ENTITY, Participation::ERROR_MESSAGE['isNotParticipant']);
        }

        if (Response::HTTP_INTERNAL_SERVER_ERROR === $responseCode) {
            return new ApiErrorResponse([], Response::HTTP_INTERNAL_SERVER_ERROR, Participation::ERROR_MESSAGE['deletionFailed']);
        }

        return new ApiSuccessResponse([], $responseCode);
    }
}
