<?php

use Ardenthq\EnhancedMarkdown\Http\Controllers\AttachmentController;
use Illuminate\Support\Facades\Route;

Route::post('/{resource}/store/{field}', AttachmentController::class);
