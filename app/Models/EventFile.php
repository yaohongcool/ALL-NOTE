<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EventFile extends Model
{
    use HasFactory;

    public const USAGE_INLINE = 'inline';
    public const USAGE_ATTACHMENT = 'attachment';

    public const CONTEXT_PROCESS = 'process';
    public const CONTEXT_RESULT = 'result';

    protected $fillable = [
        'event_id',
        'event_record_id',
        'user_id',
        'usage',
        'context',
        'disk',
        'path',
        'original_name',
        'mime_type',
        'size',
    ];

    protected function casts(): array
    {
        return [
            'size' => 'integer',
        ];
    }

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    public function record(): BelongsTo
    {
        return $this->belongsTo(EventRecord::class, 'event_record_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function isInline(): bool
    {
        return $this->usage === self::USAGE_INLINE;
    }

    public function isAttachment(): bool
    {
        return $this->usage === self::USAGE_ATTACHMENT;
    }

    public function isImage(): bool
    {
        return str_starts_with((string) $this->mime_type, 'image/');
    }
}
