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
            'landingHeroDescriptionHtml' => $this->preparedHeroDescriptionHtml(
                (string) data_get($landingFoundation, 'hero.description', ''),
                (array) data_get($landingFoundation, 'hero.description_tokens', [])
            ),
            'landingHeroActions' => $this->preparedHeroActions(data_get($landingFoundation, 'hero.actions', [])),
            'landingDocCount' => count(data_get($landingDocs, 'items', [])),
            'landingSeamSourceCount' => count(data_get($phaseOneSeamSources, 'items', [])),
            'landingDocGuideText' => $this->inlineCodeList(data_get($landingDocs, 'guide', [])),
            'landingDocSourceOfTruthText' => $this->inlineCodeList(data_get($landingDocs, 'source_of_truth', [])),
            'landingSeamSourceOfTruthText' => $this->inlineCodeList(data_get($phaseOneSeamSources, 'source_of_truth', [])),
        ]);
    }

    protected function preparedHeroDescriptionHtml(string $description, array $tokens): string
    {
        return strtr(e($description), $tokens);
    }

    protected function preparedHeroActions(array $actions): array
    {
        return collect($actions)
            ->filter(fn ($action): bool => is_array($action) && filled($action['label'] ?? null))
            ->map(function (array $action): array {
                $href = filled($action['route'] ?? null)
                    ? route($action['route'])
                    : url($action['href'] ?? '/');

                return [
                    'label' => (string) $action['label'],
                    'style' => (string) ($action['style'] ?? 'button-secondary'),
                    'href' => $href,
                ];
            })
            ->values()
            ->all();
    }

    protected function inlineCodeList(array $items): string
    {
        return collect($items)
            ->filter(fn ($item): bool => filled($item))
            ->map(fn ($item): string => sprintf('<code>%s</code>', e((string) $item)))
            ->implode(', ');
    }
}
