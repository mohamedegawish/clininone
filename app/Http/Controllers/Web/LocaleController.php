<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;

class LocaleController extends Controller
{
    public function switch(string $locale)
    {
        if (in_array($locale, ['en', 'ar'], true)) {
            session()->put('locale', $locale);
        }

        return redirect()->back();
    }
}
