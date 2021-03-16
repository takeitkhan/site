<?php

use Tritiyo\Site\Controllers\SiteController;

Route::group(['middleware' => ['web','role:1,3']], function () {
    Route::any('sites/search', [SiteController::class, 'search'])->name('sites.search');

    Route::resources([
        'sites' => SiteController::class,
    ]);
});