<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

class EventRecord extends Model
{
    use HasFactory;

    protected $fillable = [
        'event_id',
        'user_id',
        'process',
        'result',
    ];

    protected static function booted(): void
    {
        static::deleting(function (EventRecord $record) {
            $record->files()
                ->get()
                ->each(fn (EventFile $file) => Storage::disk($file->disk)->delete($file->path));
        });
    }

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function files(): HasMany
    {
        return $this->hasMany(EventFile::class);
    }

    public function isRecorder(User|int|null $user): bool
    {
        $userId = $user instanceof User ? $user->id : $user;

        return $userId !== null && $this->user_id === $userId;
    }
}
