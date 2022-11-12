<?php

namespace App\Exceptions;

use Illuminate\Database\Eloquent\MassAssignmentException;
use Illuminate\Validation\ValidationException;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
        'password',
        'password_confirmation',
    ];

    /**
     * Report or log an exception.
     *
     * @param \Throwable $exception
     * @return void
     *
     * @throws \Throwable
     */
    public function report(Throwable $exception)
    {
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Throwable $exception
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws \Throwable
     */
    public function render($request, Throwable $exception)
    {

        if ($request->is('api/*')) {
            if ($exception instanceof ValidationException) {
                $err_keeper = [];
                foreach ($exception->errors() as $index => $error) {
                    $err_keeper[] = ['code' => $index, 'message' => $error[0]];
                }
                return response()->json([
                    'success' => false,
                    'errors' => $err_keeper
                ], Response::HTTP_FORBIDDEN);
            }

            if ($exception instanceof AuthorizationException) {
                return response([
                    'status' => 'error',
                    'message' => $exception->getMessage()
                ], Response::HTTP_FORBIDDEN);
            }

            if ($exception instanceof MassAssignmentException) {
                return response([
                    'status' => 'error',
                    'message' => 'fillable property to allow mass assignment',
                ], Response::HTTP_INTERNAL_SERVER_ERROR);
            }

            if ($exception instanceof ModelNotFoundException ||
                $exception instanceof NotFoundHttpException
            ) {
                return response([
                    'status' => 'error',
                    'message' => '404 Not Found.'
                ], Response::HTTP_NOT_FOUND);
            }

            if ($exception instanceof \Illuminate\Auth\AuthenticationException) {
                return response([
                    'status' => 'error',
                    'message' => $exception->getMessage()
                ], 401);
            }
            if ($exception instanceof QueryException) {
                return response([
                    'status' => 'error',
                    'message' => $exception->getMessage()
                ], 401);
            }
            if ($exception instanceof \ParseError) {
                return response([
                    'status' => 'error',
                    'message' => $exception->getMessage()
                ], 401);
            }
            if ($exception instanceof BindingResolutionException) {
                return response([
                    'status' => 'error',
                    'message' => $exception->getMessage()
                ], 401);
            }
            if ($exception instanceof \BadMethodCallException) {
                return response([
                    'status' => 'error',
                    'message' => $exception->getMessage()
                ], 401);
            }
            if ($exception instanceof MethodNotAllowedHttpException) {
                return response([
                    'status' => 'error',
                    'message' => $exception->getMessage()
                ], Response::HTTP_METHOD_NOT_ALLOWED);
            }
            if ($exception instanceof \ArgumentCountError) {
                return response([
                    'status' => 'error',
                    'message' => 'Too few arguments to function'
                ], Response::HTTP_BAD_REQUEST);
            }

            if ($exception instanceof HttpException && $exception->getStatusCode() == 403) {
                return response([
                    'status' => 'error',
                    'message' => $exception->getMessage(),
                ], Response::HTTP_FORBIDDEN);
            }

            dd($exception);
            return response([
                'status' => 'Error',
                'message' => $exception->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
        return parent::render($request, $exception);
    }
}
