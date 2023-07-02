<?php

namespace App\Services;

use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;
use App\Models\Event;
use App\Models\Participation;

/**
 * Прикладной сервис класса Participation
 *
 */
class ParticipationService
{
    /**
     * Метод проверки события на участие в нём текущего пользователя
     *
     * @param  int $eventId - id события
     * @param  int $userId - id текущего пользователя
     *
     * @return Participation|null
     */
    private static function checkForParticipation(int $eventId, int $userId): Participation|null
    {
        $participation = Participation::firstWhere(['event_id' => $eventId, 'user_id' => $userId]);

        if ($participation) {
            return $participation;
        }

        return null;
    }

    /**
     * Метод проверки события на авторство текущего пользователя
     *
     * @param  int $eventId - id события
     * @param  int $userId - id текущего пользователя
     * @return void
     */
    private static function checkForAuthorship(int $eventId, int $userId): void
    {
        Event::where('user_id', '!=', $userId)->findOrFail($eventId);
    }

    /**
     * Метод создания модели класса Participation
     *
     * @param  int $eventId - id события
     *
     * @return Event|null - массив с данными нового комментария
     */
    public static function createParticipation(int $eventId): Participation|null
    {
        $userId = Auth::id();

        self::checkForAuthorship($eventId, $userId);

        if (self::checkForParticipation($eventId, $userId)) {
            return null;
        }

        $participation = Participation::create([
            'event_id' => $eventId,
            'user_id' => $userId,
        ]);
        return $participation;
    }

    /**
     * Метод удаления модели класса Participation
     *
     * @param  int $eventId - id события
     *
     * @return int|null - код HTTP ответа или null
     */
    public static function deleteParticipation(int $eventId): int|null
    {
        $userId = Auth::id();
        self::checkForAuthorship($eventId, $userId);

        if (!$participation = self::checkForParticipation($eventId, $userId)) {
            return null;
        }

        try {
            $participation->delete($eventId);
            return Response::HTTP_NO_CONTENT;
        } catch (\Exception $exception) {
            return Response::HTTP_INTERNAL_SERVER_ERROR;
        }
    }
}
