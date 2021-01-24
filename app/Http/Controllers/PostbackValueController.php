<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdatePostbackValueRequest;
use Illuminate\Support\Facades\Cache;

class PostbackValueController extends Controller
{
    public function index(): int
    {
        return (int) Cache::get('postback-value');
    }

    public function update(UpdatePostbackValueRequest $request): int
    {
        $payload = $request->validated();

        Cache::forget('postback-value');

        return (int) Cache::rememberForever('postback-value', static fn () => $payload['postback']);
    }
}
