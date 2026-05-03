<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class SocialAccount extends Model
{
    use HasFactory, SoftDeletes;

    public const PLATFORM_INSTAGRAM = 'instagram';
    public const PLATFORM_TIKTOK = 'tiktok';
    public const PLATFORM_THREADS = 'threads';
    public const PLATFORM_YOUTUBE = 'youtube';

    public const PLATFORMS = [
        self::PLATFORM_INSTAGRAM => 'Instagram',
        self::PLATFORM_TIKTOK => 'TikTok',
        self::PLATFORM_THREADS => 'Threads',
        self::PLATFORM_YOUTUBE => 'YouTube',
    ];

    public const STATUS_CONNECTED = 'connected';
    public const STATUS_DISCONNECTED = 'disconnected';
    public const STATUS_ERROR = 'error';

    public const STATUSES = [
        self::STATUS_CONNECTED => '連携中',
        self::STATUS_DISCONNECTED => '未連携',
        self::STATUS_ERROR => 'エラー',
    ];

    protected $fillable = [
        'client_id',
        'platform',
        'account_name',
        'external_account_id',
        'access_token',
        'refresh_token',
        'token_expires_at',
        'status',
        'memo',
    ];

    protected $casts = [
        'access_token' => 'encrypted',
        'refresh_token' => 'encrypted',
        'token_expires_at' => 'datetime',
    ];

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function platformLabel(): string
    {
        return self::PLATFORMS[$this->platform] ?? $this->platform;
    }

    public function statusLabel(): string
    {
        return self::STATUSES[$this->status] ?? $this->status;
    }
}
