<?php

namespace App\Http\Controllers;

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
            return response()->json(["message" => "User not found"], 404);
        }

        return response()->json(User::where("id", $userId)->first());
    }
}
