<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
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
        self::STATUS_PROCESSED,
        self::STATUS_PROCESSING,
        self::STATUS_PENDING,
        self::STATUS_NO_ACTION,
    ];

    public const VISIBILITIES = [
        self::VISIBILITY_PRIVATE,
        self::VISIBILITY_PUBLIC,
    ];

    protected $fillable = [
        'user_id',
        'title',
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
            $event->files()
                ->get()
                ->each(fn (EventFile $file) => Storage::disk($file->disk)->delete($file->path));
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

    public function scopeVisibleTo(Builder $query, User|int $user): Builder
    {
        $userId = $user instanceof User ? $user->id : $user;

        return $query->where(function (Builder $query) use ($userId) {
            $query->where('user_id', $userId)
                ->orWhere('visibility', self::VISIBILITY_PUBLIC);
        });
    }

    public function isOwner(User|int|null $user): bool
    {
        $userId = $user instanceof User ? $user->id : $user;

        return $userId !== null && $this->user_id === $userId;
    }

    public function isPublic(): bool
    {
        return $this->visibility === self::VISIBILITY_PUBLIC;
    }

    public function getVisibilityLabelAttribute(): string
    {
        return match ($this->visibility) {
            self::VISIBILITY_PUBLIC => '公开',
            default => '仅自己可见',
        };
    }
}
