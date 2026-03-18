<?php

use App\Modules\Payment\Controllers\WebhookController;



Route::post('/stripe/webhook', [WebhookController::class, 'handle']);
