<?php

namespace App\Support;

use App\Models\Category;
use App\Models\Todo;
use App\Models\User;

/**
 * Seeds a brand-new account with starter categories and example todos so the
 * app is never empty on first login.
 */
class DefaultData
{
    /**
     * Categories created for every new account, keyed by name.
     *
     * @var array<string, string>
     */
    private const CATEGORIES = [
        'Personal' => '#6366f1',
        'Work' => '#f59e0b',
        'Shopping' => '#10b981',
    ];

    /**
     * Create the default categories and example todos for a user.
     */
    public static function for(User $user): void
    {
        $categories = [];

        foreach (self::CATEGORIES as $name => $color) {
            $categories[$name] = Category::create([
                'user_id' => $user->id,
                'name' => $name,
                'color' => $color,
            ]);
        }

        $examples = [
            [
                'title' => 'Try ticking this off',
                'notes' => 'Click the checkbox to mark a task complete. Click it again to undo.',
                'category' => 'Personal',
                'priority' => 'medium',
                'due_date' => null,
                'is_completed' => false,
            ],
            [
                'title' => 'Rename me using the Edit button',
                'notes' => 'Every task can be retitled, re-prioritised and given a due date.',
                'category' => 'Personal',
                'priority' => 'low',
                'due_date' => null,
                'is_completed' => false,
            ],
            [
                'title' => 'Send the weekly status report',
                'notes' => 'This one is overdue so it shows in red.',
                'category' => 'Work',
                'priority' => 'high',
                'due_date' => '-3 days',
                'is_completed' => false,
            ],
            [
                'title' => 'Team standup',
                'notes' => null,
                'category' => 'Work',
                'priority' => 'medium',
                'due_date' => '+1 day',
                'is_completed' => false,
            ],
            [
                'title' => 'Buy milk and coffee',
                'notes' => null,
                'category' => 'Shopping',
                'priority' => 'medium',
                'due_date' => '+3 days',
                'is_completed' => false,
            ],
            [
                'title' => 'Set up this todo app',
                'notes' => 'Already done — completed tasks are kept for reference.',
                'category' => 'Personal',
                'priority' => 'high',
                'due_date' => '-1 day',
                'is_completed' => true,
            ],
        ];

        foreach ($examples as $example) {
            Todo::create([
                'user_id' => $user->id,
                'category_id' => $categories[$example['category']]->id,
                'title' => $example['title'],
                'notes' => $example['notes'],
                'priority' => $example['priority'],
                'due_date' => $example['due_date'] !== null
                    ? now()->startOfDay()->modify($example['due_date'])->format('Y-m-d')
                    : null,
                'is_completed' => $example['is_completed'],
                'completed_at' => $example['is_completed'] ? now() : null,
            ]);
        }
    }
}
