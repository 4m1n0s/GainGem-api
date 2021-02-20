<?php

namespace App\Http\Controllers;

use App\Http\Requests\IndexRobuxPlaceRequest;
use App\Models\User;
use App\Services\Robux;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Arr;

class RobuxPlaceController extends Controller
{
    public function index(IndexRobuxPlaceRequest $request): JsonResponse
    {
        $payload = $request->validated();

        /** @var User $user */
        $user = auth()->user();
        $user->loadAvailablePoints();

        abort_if($user->available_points < $payload['value'], 422, "You don't have enough points!");

        $places = collect(Robux::getPlacesByUsername($payload['username'])['data']);
        $placesIds = $places->pluck('rootPlace.id')->toArray();
        $thumbnails = Robux::getPlacesIconsByIds($placesIds)['data'];

        foreach ($thumbnails as &$thumbnail) {
            $place = Arr::first(Arr::where($places->toArray(), static fn ($place) => $place['rootPlace']['id'] === $thumbnail['targetId']));
            $thumbnail['name'] = $place['name'];
        }

        return response()->json($thumbnails);
    }
}
