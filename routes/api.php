<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SimulateController;

Route::get('/simulate', [SimulateController::class, 'simulate']);