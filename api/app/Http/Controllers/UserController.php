<?php

namespace App\Http\Controllers;

use App\Exceptions\ResponseException;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{

    /**
     * Display the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function show()
    {
        $userId = config("api.user_id");

        $user = User::find($userId);

        if (!$user) {
            throw new ResponseException(
                'User not found',
                404,
                ['user_id'=> $userId]
            );
        }

        return response()->json($user);
    }
}
