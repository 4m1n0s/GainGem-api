<?php

namespace App\Exceptions;

use App\Models\Coupon;
use App\Models\GiftCard;
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
            $model = $exception->getModel();

            abort_if($model === Coupon::class, 422, 'Invalid or expired promo code!');
            abort_if($model === GiftCard::class, 422, 'Invalid gift card');

            $model = class_basename($model);
            abort(422, "{$model} not found");
        }

        if ($exception instanceof AuthenticationException) {
            abort(403, 'Unauthenticated');
        }

        return parent::render($request, $exception);
    }

    public function report(Throwable $exception)
    {
        if (app()->bound('sentry') && $this->shouldReport($exception)) {
            app('sentry')->captureException($exception);
        }

        parent::report($exception);
    }
}
