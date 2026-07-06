<?php

namespace App\Models;

use App\Enums\EventStatus;
use App\Enums\EventVisibility;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class Event extends Model
{
    use HasFactory;

    public const STATUS_PROCESSED = '已处理';
    public const STATUS_PROCESSING = '处理中';
    public const STATUS_PENDING = '待处理';
    public const STATUS_NO_ACTION = '无需处理';

    public const VISIBILITY_PRIVATE = 'private';
    public const VISIBILITY_PUBLIC = 'public';

    public const STATUSES = [
        EventStatus::Processed->value,
        EventStatus::Processing->value,
        EventStatus::Pending->value,
        EventStatus::NoAction->value,
    ];

    public const VISIBILITIES = [
        EventVisibility::Private->value,
        EventVisibility::Public->value,
    ];

    protected $fillable = [
        'user_id',
        'title',
        'description',
        'status',
        'subject',
        'occurred_on',
        'visibility',
    ];

    protected $appends = [
        'visibility_label',
    ];

    protected function casts(): array
    {
        return [
            'occurred_on' => 'date',
        ];
    }

    protected static function booted(): void
    {
        static::deleting(function (Event $event) {
            $event->files()->select(['id', 'disk', 'path'])->get()->each(function (EventFile $file) {
                $deleted = Storage::disk($file->disk)->delete($file->path);
                if (! $deleted && Storage::disk($file->disk)->exists($file->path)) {
                    Log::warning('事件删除时文件物理删除失败', ['event_id' => $event->id, 'file_id' => $file->id, 'disk' => $file->disk, 'path' => $file->path]);
                }
            });
        });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function records(): HasMany
    {
        return $this->hasMany(EventRecord::class);
    }

    public function summaryRecord(): HasOne
    {
        return $this->hasOne(EventRecord::class)->latestOfMany('created_at');
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(EventTag::class, 'event_tag_relations')
            ->withTimestamps();
    }

    public function files(): HasMany
    {
        return $this->hasMany(EventFile::class);
    }

    public function isOwner(User|int|null $user): bool
    {
        $userId = $user instanceof User ? $user->id : $user;

        return $userId !== null && $this->user_id === $userId;
    }

    public function isPublic(): bool
    {
        return $this->visibility === EventVisibility::Public->value;
    }

    public function getVisibilityLabelAttribute(): string
    {
        $visibility = EventVisibility::tryFrom($this->visibility);

        return $visibility?->label() ?? EventVisibility::Private->label();
    }
}
