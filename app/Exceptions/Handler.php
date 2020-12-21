<?php

namespace App\Exceptions;

use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;

class Handler extends ExceptionHandler
{
    /** @var string[] */
    protected $dontReport = [
    ];

    public function register(): void
    {
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Throwable  $exception
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws \Throwable
     */
    public function render($request, Throwable $exception)
    {
        if ($exception instanceof ModelNotFoundException) {
            $model = class_basename($exception->getModel());
            abort_if($model === 'Coupon', 422, 'Invalid or expired promo code!');
            abort_if($model === 'GiftCard', 422, 'Invalid or expired gift card');
            abort(422, "{$model} not found");
        }

        if ($exception instanceof AuthenticationException) {
            abort(403, 'Unauthenticated');
        }

        return parent::render($request, $exception);
    }
}
