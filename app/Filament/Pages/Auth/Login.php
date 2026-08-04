<?php

namespace App\Filament\Pages\Auth;

use Filament\Pages\Auth\Login as BaseLogin;

class Login extends BaseLogin
{
    // Paksa mengarah ke file blade custom kita
    protected static string $view = 'filament.pages.auth.login';
}
