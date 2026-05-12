<?php

namespace App\Http\Controllers\Web\Public;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LandingController extends Controller
{
    /**
     * Homepage - Doctors Listing
     */
    public function index(): View
    {
        return view('public.index');
    }

    /**
     * Doctor Profile Page
     */
    public function doctor(): View
    {
        return view('public.doctor');
    }

    public function booking(): View
    {
        return view('public.booking');
    }

    /**
     * Blood Bank Page
     */
    public function bloodBank(): View
    {
        return view('public.blood-bank.index');
    }
}
