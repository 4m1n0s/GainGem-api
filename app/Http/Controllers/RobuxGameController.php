<?php

namespace App\Http\Controllers;

use App\Http\Requests\IndexRobuxPlaceRequest;
use App\Models\User;
use App\Services\Robux;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Arr;

class RobuxGameController extends Controller
{
    public function index(IndexRobuxPlaceRequest $request): JsonResponse
    {
        $payload = $request->validated();

        /** @var User $user */
        $user = auth()->user();
        $user->loadAvailablePoints();

        abort_if($user->available_points < $payload['value'], 422, "You don't have enough points!");

        $games = Robux::getGamesByUsername($payload['username'])['data'];
        $placesIds = collect($games)->pluck('rootPlace.id')->toArray();
        $thumbnails = Robux::getPlacesIconsByIds($placesIds)['data'];

        foreach ($games as &$game) {
            $thumbnail = Arr::first(Arr::where($thumbnails, static fn ($thumbnail) => $thumbnail['targetId'] === $game['rootPlace']['id']));
            $game['rootPlace']['imageUrl'] = $thumbnail['imageUrl'];
        }

        return response()->json($games);
    }
}
