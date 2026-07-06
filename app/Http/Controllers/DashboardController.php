<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\Document;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Model;

class DashboardController extends Controller
{
    public function index(): View
    {
        $user = auth()->user();

        $assetsOverview = $user->assets()
            ->oldest('updated_at')
            ->limit(10)
            ->get();

        $assetReminders = $user->assets()
            ->whereNotNull('due_date')
            ->orderBy('due_date')
            ->limit(5)
            ->get()
            ->map(fn (Asset $asset) => $this->toReminderArray($asset, 'asset'));

        $documentReminders = $user->documents()
            ->whereNotNull('due_date')
            ->orderBy('due_date')
            ->limit(5)
            ->get()
            ->map(fn (Document $document) => $this->toReminderArray($document, 'document'));

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
                'fund_total' => $user->fundAccounts()->sum('balance'),
            ],
            'assetsOverview' => $assetsOverview,
            'reminders' => $reminders,
        ]);
    }

    private function toReminderArray(Model $model, string $type): array
    {
        return [
            'type' => $type,
            'title' => $type === 'asset' ? $model->name : $model->name,
            'category' => $model->category,
            'status' => $model->computed_status,
            'days_until_due_label' => $model->days_until_due_label,
            'due_date' => $model->due_date,
            'note' => $model->note,
        ];
    }
}
