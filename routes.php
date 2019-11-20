<?php

Route::middleware('web')->group(function () {
    Route::name('generate-sitemap')->get(
        'generate-sitemap',
        'OnionCreative\Sitemap\Classes\Sitemap@generateSitemap'
    );
});
