<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Todo extends Model
{
    /**
     * The available priority levels, in descending order of urgency.
     *
     * @var list<string>
     */
    public const PRIORITIES = ['high', 'medium', 'low'];

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'user_id',
        'category_id',
        'title',
        'notes',
        'due_date',
        'priority',
        'is_completed',
        'completed_at',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'due_date' => 'date:Y-m-d',
            'is_completed' => 'boolean',
            'completed_at' => 'datetime',
        ];
    }

    /**
     * Scope the query to the given status filter.
     */
    public function scopeStatus(Builder $query, string $status): Builder
    {
        return match ($status) {
            'active' => $query->where('is_completed', false),
            'completed' => $query->where('is_completed', true),
            default => $query,
        };
    }

    /**
     * Sort by due date (undated last), then priority, then newest first.
     */
    public function scopeSorted(Builder $query): Builder
    {
        return $query
            ->orderByRaw('due_date IS NULL')
            ->orderBy('due_date')
            ->orderByRaw("FIELD(priority, 'high', 'medium', 'low')")
            ->orderByDesc('created_at');
    }

    /**
     * Determine whether the todo is past its due date and still open.
     */
    public function isOverdue(): bool
    {
        return ! $this->is_completed
            && $this->due_date !== null
            && $this->due_date->lt(today());
    }

    /**
     * Determine whether the todo is due today.
     */
    public function isDueToday(): bool
    {
        return ! $this->is_completed
            && $this->due_date !== null
            && $this->due_date->isToday();
    }

    /**
     * Get the user that owns the todo.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the category the todo belongs to.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }
}
