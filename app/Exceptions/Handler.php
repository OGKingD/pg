<?php

namespace App\Exceptions;

use Illuminate\Auth\AuthenticationException;
use Illuminate\Contracts\Support\Responsable;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Response;
use Illuminate\Routing\Router;
use Illuminate\Validation\ValidationException;
use Throwable;

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


    public function render($request, Throwable $e)
    {
        if (method_exists($e, 'render') && $response = $e->render($request)) {
            return Router::toResponse($request, $response);
        }

        if ($e instanceof Responsable) {
            return $e->toResponse($request);
        }
        $e = $this->prepareException($this->mapException($e));

        foreach ($this->renderCallbacks as $renderCallback) {
            foreach ($this->firstClosureParameterTypes($renderCallback) as $type) {
                if (is_a($e, $type)) {
                    $response = $renderCallback($e, $request);

                    if (!is_null($response)) {
                        return $response;
                    }
                }
            }
        }
        if ($e instanceof HttpResponseException) {
            return $e->getResponse();
        }

        if ($e instanceof AuthenticationException) {
            return $this->shouldReturnJson($request, $e)
                ? response()->json(
                    [
                        'message' => $e->getMessage(),
                        'errors' =>
                            ['Authorization' =>
                                ["Bearer Token missing / Invalid"]
                            ]
                    ], 401)
                : redirect()->guest($e->redirectTo() ?? route('login'));
        }

        if ($e instanceof ValidationException) {
            return $this->convertValidationExceptionToResponse($e, $request);
        }
        $statusCode = 500;
        if (method_exists($e,"getStatusCode")){
            $statusCode = $e->getStatusCode();
        }
        $message = empty($e->getMessage()) ? Response::$statusTexts[$statusCode] : $e->getMessage();

        if ($statusCode === 500){
            //something internal;
            $message = "😢  Oops! Something Unexpected happened! Please contact " .config('app.support_email')." ";
            //report internal error to Admin;
            logger()->error("Error in file: {$e->getFile()} ", ['cause'=>$e->getMessage(), 'trace'=>$e->getTraceAsString()]);
        }
        logger()->error("Error in file: {$e->getFile()} ", ['cause'=>$e->getMessage(), 'trace'=>$e->getTraceAsString()]);

        return $this->shouldReturnJson($request, $e)
            ? response()->json([
                'message' => $message,
                'errors' => [],
            ], $statusCode)
            : $this->prepareResponse($request, $e);



    }

}
