<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class EventRecord extends Model
{

    protected $fillable = [
        'event_id',
        'user_id',
        'process',
        'result',
    ];

    protected static function booted(): void
    {
        static::deleting(function (EventRecord $record) {
            $record->files()->select(['id', 'disk', 'path'])->get()->each(function (EventFile $file) use ($record) {
                $deleted = Storage::disk($file->disk)->delete($file->path);
                if (! $deleted && Storage::disk($file->disk)->exists($file->path)) {
                    Log::warning('记录删除时文件物理删除失败', ['record_id' => $record->id, 'file_id' => $file->id, 'disk' => $file->disk, 'path' => $file->path]);
                }
            });
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
