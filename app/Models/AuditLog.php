<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AuditLog extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'action',
        'model_type',
        'model_id',
        'ip_address',
        'user_agent',
        'old_values',
        'new_values',
        'created_at',
    ];

    protected function casts(): array
    {
        return [
            'old_values' => 'array',
            'new_values' => 'array',
            'created_at' => 'datetime',
        ];
    }

    /**
     * Get the user who performed this action.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Log a create action.
     */
    public static function logCreate(User $user, string $modelType, int $modelId, array $newValues = []): static
    {
        return static::create([
            'user_id' => $user->id,
            'action' => 'create',
            'model_type' => $modelType,
            'model_id' => $modelId,
            'new_values' => $newValues,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'created_at' => now(),
        ]);
    }

    /**
     * Log an update action.
     */
    public static function logUpdate(User $user, string $modelType, int $modelId, array $oldValues, array $newValues): static
    {
        return static::create([
            'user_id' => $user->id,
            'action' => 'update',
            'model_type' => $modelType,
            'model_id' => $modelId,
            'old_values' => $oldValues,
            'new_values' => $newValues,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'created_at' => now(),
        ]);
    }

    /**
     * Log a delete action.
     */
    public static function logDelete(User $user, string $modelType, int $modelId, array $oldValues = []): static
    {
        return static::create([
            'user_id' => $user->id,
            'action' => 'delete',
            'model_type' => $modelType,
            'model_id' => $modelId,
            'old_values' => $oldValues,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'created_at' => now(),
        ]);
    }

    /**
     * Log a custom action.
     */
    public static function logAction(User $user, string $action, array $data = []): static
    {
        return static::create([
            'user_id' => $user->id,
            'action' => $action,
            'new_values' => $data,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'created_at' => now(),
        ]);
    }
}