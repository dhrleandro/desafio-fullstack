<?php
 
namespace App\Exceptions;

use Symfony\Component\HttpFoundation\Response;
use Exception;
use Log;
 
class ResponseException extends Exception
{
    private int $statusCode;
    private array $context;

    public function __construct($message, ?array $context = [], $statusCode = Response::HTTP_NOT_FOUND, $code = 0, \Throwable $previous = null) {
        parent::__construct($message, $code, $previous);
        $this->statusCode = $statusCode;
        $this->context = $context ?? [];
    }
    
    /**
     * Report the exception.
     *
     * @return bool|null
     */
    public function report()
    {
        //
    }
 
    /**
     * Render the exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function render($request)
    {
        Log::warning("ResponseException: {$this->getMessage()}",[
            'status_code' => $this->statusCode,
            'message'=> $this->getMessage(),
            'file' => $this->getFile(),
            'line' => $this->getLine(),
            ...$this->context,
        ]);

        return response()->json(
            [
                'error' => true,
                'message' => $this->getMessage(),
            ],
            $this->statusCode
        );
    }
}