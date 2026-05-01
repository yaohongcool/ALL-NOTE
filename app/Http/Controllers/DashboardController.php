<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\Document;
use Illuminate\Contracts\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $user = auth()->user();

        $assetsOverview = $user->assets()
            ->oldest('updated_at')
            ->get();

        $recentEvents = $user->events()
            ->with(['tags', 'summaryRecord'])
            ->withCount('records')
            ->latest('created_at')
            ->latest('id')
            ->limit(5)
            ->get();

        $assetReminders = $user->assets()
            ->whereNotNull('due_date')
            ->orderBy('due_date')
            ->get()
            ->map(function (Asset $asset) {
                return [
                    'type' => 'asset',
                    'title' => $asset->name,
                    'category' => $asset->category,
                    'status' => $asset->computed_status,
                    'days_until_due_label' => $asset->days_until_due_label,
                    'due_date' => $asset->due_date,
                    'note' => $asset->note,
                ];
            });

        $documentReminders = $user->documents()
            ->whereNotNull('due_date')
            ->orderBy('due_date')
            ->get()
            ->map(function (Document $document) {
                return [
                    'type' => 'document',
                    'title' => $document->name,
                    'category' => $document->category,
                    'status' => $document->computed_status,
                    'days_until_due_label' => $document->days_until_due_label,
                    'due_date' => $document->due_date,
                    'note' => $document->note,
                ];
            });

        $reminders = $assetReminders
            ->concat($documentReminders)
            ->sortBy(fn ($item) => optional($item['due_date'])->timestamp ?? PHP_INT_MAX)
            ->values();

        return view('dashboard', [
            'stats' => [
                'passwords_count' => $user->passwords()->count(),
                'assets_count' => $user->assets()->count(),
                'documents_count' => $user->documents()->count(),
                'events_count' => $user->events()->count(),
            ],
            'assetsOverview' => $assetsOverview,
            'recentEvents' => $recentEvents,
            'reminders' => $reminders,
        ]);
    }
}
