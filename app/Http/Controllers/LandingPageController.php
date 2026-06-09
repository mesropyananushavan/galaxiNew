<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\View\View;

class LandingPageController extends Controller
{
    public function __invoke(): View
    {
        return view('welcome', [
            'landingFoundation' => config('landing-foundation', []),
            'landingDocs' => config('landing-docs', []),
            'phaseOneSeamSources' => config('phase-1-seam-sources', []),
        ]);
    }
}
