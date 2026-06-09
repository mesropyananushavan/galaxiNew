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
            'landingDocGuideText' => $this->inlineCodeList(data_get($landingDocs, 'guide', [])),
            'landingDocSourceOfTruthText' => $this->inlineCodeList(data_get($landingDocs, 'source_of_truth', [])),
            'landingSeamSourceOfTruthText' => $this->inlineCodeList(data_get($phaseOneSeamSources, 'source_of_truth', [])),
        ]);
    }

    protected function inlineCodeList(array $items): string
    {
        return collect($items)
            ->filter(fn ($item): bool => filled($item))
            ->map(fn ($item): string => sprintf('<code>%s</code>', e((string) $item)))
            ->implode(', ');
    }
}
