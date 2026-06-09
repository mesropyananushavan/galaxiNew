<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\View\View;

class LandingPageController extends Controller
{
    public function __invoke(): View
    {
        $landingFoundation = config('landing-foundation', []);
        $landingDocs = config('landing-docs', []);
        $phaseOneSeamSources = config('phase-1-seam-sources', []);

        return view('welcome', [
            'landingFoundation' => $landingFoundation,
            'landingDocs' => $landingDocs,
            'phaseOneSeamSources' => $phaseOneSeamSources,
            'landingDocCount' => count(data_get($landingDocs, 'items', [])),
            'landingSeamSourceCount' => count(data_get($phaseOneSeamSources, 'items', [])),
        ]);
    }
}
