<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;
use Illuminate\Auth\AuthenticationException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<Throwable>>
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
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
    }

    public function render($request, Throwable $exception)
    {
        if( $request->is('api/*')){
            if ($exception instanceof AuthenticationException) {
                return response(['message' => $exception->getMessage()], 401);
            } else if ($exception instanceof NotFoundHttpException) {
                return response(['message' => $exception->getMessage()], 404);
            } else if ($exception instanceof BadRequestHttpException) {
                return response(['message' => $exception->getMessage()], 400);
            } else if ($exception instanceof MethodNotAllowedHttpException) {
                return response(['message' => $exception->getMessage()], 405);
            } else if ($exception instanceof UnprocessableEntityHttpException) {
                return response(['message' => $exception->getMessage()], 422);
            } else if ($exception instanceof AccessDeniedHttpException) {
                return response(['message' => $exception->getMessage()], 403);
            }
        }

        return parent::render($request, $exception);
    }
}
