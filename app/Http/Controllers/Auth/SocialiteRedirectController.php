<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;

class SocialiteRedirectController extends Controller
{
    public function __invoke(): RedirectResponse
    {
        if (auth()->check() && auth()->user()->canAccessFilament()) {
            return redirect()->route('filament.pages.dashboard');
        }

        return redirect()->route('dashboard');
    }
}
