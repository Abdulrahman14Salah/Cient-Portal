<?php

use App\Modules\Payment\Controllers\WebhookController;

/*
|--------------------------------------------------------------------------
| Webhook Routes
|--------------------------------------------------------------------------
*/

Route::post('/stripe/webhook', [WebhookController::class, 'handle']);
