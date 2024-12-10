<?php
 
namespace App\Exceptions;
 
use Exception;
use Log;
 
class ResponseException extends Exception
{
    private int $statusCode;
    private array $context;

    public function __construct($message, $statusCode = 404, ?array $context = [], $code = 0, \Throwable $previous = null) {
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
        Log::warning("Response exception.",[
            'status_code' => $this->statusCode,
            'message'=> $this->getMessage(),
            ...$this->context
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