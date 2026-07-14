<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Dispatch lots entreprise : aucun bouton côté client ; rappel automatique pour les files / livreurs devenus dispo.
Schedule::command('dispatch:lunch-lots-auto')
    ->everyTenMinutes()
    ->withoutOverlapping(8);
