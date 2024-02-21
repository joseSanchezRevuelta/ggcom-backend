<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
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
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }

    public function render($request, Throwable $exception) {
        // ValidationException
        if ($exception instanceof ValidationException) {
            return response()->json([
                'errors' => collect($exception->errors())->map(function ($messages, $field) {
                    return [
                        'status' => 422,
                        'title' => 'Validation Error',
                        'details' => $messages[0],
                        'source' => [
                            'pointer' => '/data/attributes/' . $field
                        ]
                    ];
                })
            ], 422);
        }
        
        // NotFoundHttpException
        if ($exception instanceof NotFoundHttpException) {
            return response()->json([
                'error' => [
                    'status' => 404,
                    'title' => 'Not Found',
                    'details' => 'The resource was not found.'
                ]
            ], 404);
        }
    
        // HttpException
        if ($exception instanceof HttpException) {
            return response()->json([
                'error' => [
                    'status' => $exception->getStatusCode(),
                    'title' => 'Error',
                    'details' => $exception->getMessage()
                ]
            ], $exception->getStatusCode());
        }

        // QueryException
        if ($exception instanceof QueryException) {
            return response()->json([
                'error' => [
                    'status' => 500,
                    'title' => 'Database Error',
                    'details' => 'An error occurred while processing your request.'
                ]
            ], 500);
        }
    
        // Otras excepciones de tipo Throwable
        // return response()->json([
        //     'error' => [
        //         'status' => 500,
        //         'title' => 'Internal Server Error',
        //         'details' => 'An unexpected error occurred.'
        //     ]
        // ], 500);
    }
}
