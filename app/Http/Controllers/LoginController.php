<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class LoginController extends Controller
{

    /**
     * Handle an authentication attempt.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function authenticate(Request $request)
    {
        $user = User::where('user_name', $request->user['username'])
            ->where('password', $request->user['password'])
            ->first();
        if (!isset($user)) {
            return response(['errors' => 'Password does\'t match'], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        Auth::login($user);
        return ['user' => $user];
    }
}
 