<?php

use Illuminate\Support\Facades\Route;

Route::middleware(['api', 'auth:sanctum'])->name('api.')->prefix('api')->group(function () {

});
