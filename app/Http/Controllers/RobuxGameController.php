<?php

namespace App\Http\Controllers;

use App\Http\Requests\IndexRobuxPlaceRequest;
use App\Models\User;
use App\Services\Robux;
use Illuminate\Support\Collection;

class RobuxGameController extends Controller
{
    public function index(IndexRobuxPlaceRequest $request): Collection
    {
        $payload = $request->validated();

        /** @var User $user */
        $user = auth()->user();
        $user->loadAvailablePoints();

        abort_if($user->available_points < $payload['value'], 422, "You don't have enough points!");

        $games = collect(Robux::getGamesByUsername($payload['username']));
        $thumbnails = collect(Robux::getPlacesIconsByIds($games->pluck('rootPlace.id')->all()));

        return $games->map(static function (array $game) use ($thumbnails): array {
            $thumbnail = $thumbnails->firstWhere('targetId', $game['rootPlace']['id']);
            $game['rootPlace']['imageUrl'] = $thumbnail['imageUrl'];

            return $game;
        });
    }
}
