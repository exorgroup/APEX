<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use Inertia\Response;

class CounterTestController extends Controller
{
    /**
     * Display the counter test page.
     */
    public function index(): Response
    {
        return Inertia::render('CounterTest');
    }
}
