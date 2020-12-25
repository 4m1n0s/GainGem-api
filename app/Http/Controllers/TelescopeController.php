<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginTelescopeRequest;
use Illuminate\Support\Facades\Cookie;

class TelescopeController extends Controller
{
    public function login(LoginTelescopeRequest $request)
    {
        $payload = $request->validated();

        Cookie::queue('token', $payload['token'], 60 * 24 * 365);

        return redirect('telescope');
    }
}
