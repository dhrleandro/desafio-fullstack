<?php

namespace App\Http\Controllers;

use App\CQS\Queries;
use App\Exceptions\ResponseException;

class UserController extends Controller
{
    protected Queries $queries;

    public function __construct(Queries $queries)
    {
        $this->queries = $queries;
    }

    /**
     * Display the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function show()
    {
        $userId = config("api.user_id");

        $user = $this->queries->userById($userId);
        if (!$user) {
            throw new ResponseException(
                'User not found',
                ['user_id'=> $userId]
            );
        }

        return response()->json($user);
    }
}
