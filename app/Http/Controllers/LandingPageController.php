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
            'landingHeroFrame' => $this->preparedLandingHeroFrame($landingFoundation),
            'landingHeroDescriptionHtml' => $this->preparedHeroDescriptionHtml(
                (string) data_get($landingFoundation, 'hero.description', ''),
                (array) data_get($landingFoundation, 'hero.description_tokens', [])
            ),
            'landingHeroActions' => $this->preparedHeroActions(data_get($landingFoundation, 'hero.actions', [])),
            'landingSnapshotRows' => $this->preparedLandingSnapshotRows($landingFoundation),
            'landingFoundationCards' => $this->preparedFoundationCards($landingFoundation),
            'landingDocsCard' => $this->preparedLandingDocsCard($landingDocs, $phaseOneSeamSources),
        ]);
    }

    protected function preparedLandingHeroFrame(array $landingFoundation): array
    {
        return [
            'eyebrow' => (string) data_get($landingFoundation, 'hero.eyebrow', ''),
            'title' => (string) data_get($landingFoundation, 'hero.title', ''),
            'snapshotTitle' => (string) data_get($landingFoundation, 'snapshot.title', ''),
            'snapshotDescription' => (string) data_get($landingFoundation, 'snapshot.description', ''),
        ];
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

    protected function preparedLandingSnapshotRows(array $landingFoundation): array
    {
        $rows = [[
            'label' => (string) data_get($landingFoundation, 'labels.focus', 'Landing focus'),
            'value' => (string) data_get($landingFoundation, 'focus', ''),
            'accent' => null,
        ]];

        return collect(array_merge($rows, data_get($landingFoundation, 'status_rows', [])))
            ->filter(fn ($row): bool => is_array($row) && filled($row['label'] ?? null) && filled($row['value'] ?? null))
            ->map(fn (array $row): array => [
                'label' => (string) $row['label'],
                'value' => (string) $row['value'],
                'accent' => filled($row['accent'] ?? null) ? (string) $row['accent'] : null,
            ])
            ->values()
            ->all();
    }

    protected function preparedFoundationCards(array $landingFoundation): array
    {
        return collect([
            [
                'title' => (string) data_get($landingFoundation, 'live_surfaces_title', ''),
                'items' => data_get($landingFoundation, 'live_surfaces', []),
            ],
            [
                'title' => (string) data_get($landingFoundation, 'working_rules_title', ''),
                'items' => data_get($landingFoundation, 'working_rules', []),
            ],
        ])
            ->filter(fn (array $card): bool => filled($card['title']) && is_array($card['items']))
            ->map(fn (array $card): array => [
                'title' => $card['title'],
                'items' => collect($card['items'])
                    ->filter(fn ($item): bool => filled($item))
                    ->map(fn ($item): string => (string) $item)
                    ->values()
                    ->all(),
            ])
            ->values()
            ->all();
    }

    protected function preparedLandingDocsCard(array $landingDocs, array $phaseOneSeamSources): array
    {
        return [
            'title' => (string) data_get($landingDocs, 'title', ''),
            'summaryRows' => $this->preparedLandingDocSummaryRows($landingDocs, $phaseOneSeamSources),
            'items' => $this->preparedLandingDocItems(data_get($landingDocs, 'items', [])),
        ];
    }

    protected function preparedLandingDocSummaryRows(array $landingDocs, array $phaseOneSeamSources): array
    {
        return [
            ['prefix' => (string) data_get($landingDocs, 'labels.doc_focus', ''), 'html' => e((string) data_get($landingDocs, 'focus', ''))],
            ['prefix' => (string) data_get($landingDocs, 'labels.doc_coverage', ''), 'html' => e(sprintf('%d %s', count(data_get($landingDocs, 'items', [])), (string) data_get($landingDocs, 'copy.coverage_suffix', '')))],
            ['prefix' => (string) data_get($landingDocs, 'labels.doc_baseline', ''), 'html' => sprintf('<code>%s</code> %s', e((string) data_get($landingDocs, 'copy.baseline_path', '')), e((string) data_get($landingDocs, 'copy.baseline_note', '')))],
            ['prefix' => (string) data_get($landingDocs, 'labels.seam_source_focus', ''), 'html' => e((string) data_get($phaseOneSeamSources, 'focus', ''))],
            ['prefix' => (string) data_get($landingDocs, 'labels.seam_source_coverage', ''), 'html' => sprintf('%s <code>%s</code>.', e(sprintf('%d %s', count(data_get($phaseOneSeamSources, 'items', [])), (string) data_get($landingDocs, 'copy.seam_source_coverage_suffix', ''))), e((string) data_get($landingDocs, 'copy.seam_source_source_doc', '')))],
            ['prefix' => (string) data_get($landingDocs, 'labels.seam_source_baseline', ''), 'html' => sprintf('<code>%s</code> %s', e((string) data_get($landingDocs, 'copy.seam_source_baseline_path', '')), e((string) data_get($landingDocs, 'copy.seam_source_baseline_note', '')))],
            ['prefix' => (string) data_get($landingDocs, 'labels.seam_source_posture', ''), 'html' => e((string) data_get($phaseOneSeamSources, 'posture', '')).'.'],
            ['prefix' => (string) data_get($landingDocs, 'labels.seam_source_source_of_truth', ''), 'html' => trim($this->inlineCodeList(data_get($phaseOneSeamSources, 'source_of_truth', [])).' '.e((string) data_get($landingDocs, 'copy.seam_source_source_of_truth_note', '')))],
            ['prefix' => (string) data_get($landingDocs, 'labels.doc_guide', ''), 'html' => trim($this->inlineCodeList(data_get($landingDocs, 'guide', [])).' '.e((string) data_get($landingDocs, 'copy.guide_note', '')))],
            ['prefix' => (string) data_get($landingDocs, 'labels.doc_posture', ''), 'html' => e((string) data_get($landingDocs, 'posture', '')).'.'],
            ['prefix' => (string) data_get($landingDocs, 'labels.source_of_truth', ''), 'html' => trim($this->inlineCodeList(data_get($landingDocs, 'source_of_truth', [])).' '.e((string) data_get($landingDocs, 'copy.source_of_truth_note', '')))],
            ['prefix' => (string) data_get($landingDocs, 'labels.reference_seam_bridge', ''), 'html' => sprintf('<code>%s</code> %s', e((string) data_get($landingDocs, 'copy.reference_seam_bridge_label_path', '')), e((string) data_get($landingDocs, 'copy.reference_seam_bridge', '')))],
        ];
    }

    protected function preparedLandingDocItems(array $items): array
    {
        return collect($items)
            ->filter(fn ($item): bool => is_array($item) && filled($item['label'] ?? null))
            ->map(fn (array $item): array => [
                'label' => (string) $item['label'],
                'external' => (bool) ($item['external'] ?? false),
                'href' => filled($item['href'] ?? null) ? (string) $item['href'] : null,
            ])
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
