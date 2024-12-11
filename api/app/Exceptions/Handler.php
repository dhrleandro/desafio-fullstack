<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Validation\ValidationException;
use \Illuminate\Http\Request;
use Log;
use \Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of exception types with their corresponding custom log levels.
     *
     * @var array<class-string<\Throwable>, \Psr\Log\LogLevel::*>
     */
    protected $levels = [
        //
    ];

    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<\Throwable>>
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     *
     * @return void
     */
    public function register()
    {
        $this->reportable(function (Throwable $e) {
            //
        });

        $this->renderable(function (Throwable $e, Request $request) {
            if ($e instanceof ValidationException) {
                return response()->json([
                    'error' => true,
                    'message' => $e->getMessage(),
                    'errors' => $e->errors()
                ], Response::HTTP_UNPROCESSABLE_ENTITY);
            }

            if ($e instanceof \DomainException) {
                return response()->json([
                    'error' => true,
                    'message' => $e->getMessage()
                ], Response::HTTP_CONFLICT);
            }

            if ($e instanceof NotFoundHttpException) {    
                return response()->json([], 404);
            }

            if ($request->is('api/*')) {
                return response()->json([
                    'error' => true,
                    'message' => 'Internal Server Error',
                ], Response::HTTP_INTERNAL_SERVER_ERROR);
            }
        });
    }
}
