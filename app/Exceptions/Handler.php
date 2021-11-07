<?php

declare(strict_types=1);

namespace Minepic\Exceptions;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;
use Laravel\Lumen\Exceptions\Handler as ExceptionHandler;
use Minepic\Misc\SplashMessage;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that should not be reported.
     *
     * @var array
     */
    protected $dontReport = [
        AuthorizationException::class,
        HttpException::class,
        ModelNotFoundException::class,
        ValidationException::class,
    ];

    /**
     * Report or log an exception.
     *
     * This is a great spot to send exceptions to Sentry, Bugsnag, etc.
     *
     * @param \Throwable $e
     *
     * @throws \Exception
     */
    public function report(\Throwable $e)
    {
        parent::report($e);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Throwable               $e
     *
     * @throws \Throwable
     *
     * @return mixed
     */
    public function render($request, \Throwable $e)
    {
        if ($e instanceof NotFoundHttpException) {
            return response(
                view('public.template.header', [
                    'title' => 'Ooops 404! - Minepic',
                    'description' => 'Error content not found',
                    'keywords' => '404, error',
                    'randomMessage' => SplashMessage::get404(),
                ]).
                view('public.errors.404').
                view('public.template.footer'),
                Response::HTTP_NOT_FOUND
            );
        }

        if ($e instanceof NotFoundHttpJsonException) {
            return response(
                json_encode(['ok' => false, $e->getMessage()], JSON_THROW_ON_ERROR),
                Response::HTTP_NOT_FOUND
            );
        }

        return parent::render($request, $e);
    }
}
