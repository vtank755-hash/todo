<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Todo;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class TodoController extends Controller
{
    /**
     * List the user's todos with status, category and priority filters.
     */
    public function index(Request $request): View
    {
        $user = $request->user();

        $status = $request->query('status', 'all');
        $status = in_array($status, ['all', 'active', 'completed'], true) ? $status : 'all';

        $priority = $request->query('priority', 'all');
        $priority = in_array($priority, Todo::PRIORITIES, true) ? $priority : 'all';

        $categories = $user->categories()->orderBy('name')->get();
        $categoryId = $request->query('category');

        // Only honour a category filter that actually belongs to this user.
        $categoryId = $categories->contains('id', $categoryId) ? (int) $categoryId : null;

        $base = fn () => $user->todos()
            ->when($categoryId, fn ($q) => $q->where('category_id', $categoryId))
            ->when($priority !== 'all', fn ($q) => $q->where('priority', $priority));

        $todos = $base()
            ->with('category')
            ->status($status)
            ->sorted()
            ->get();

        return view('todos.index', [
            'todos' => $todos,
            'categories' => $categories,
            'status' => $status,
            'priority' => $priority,
            'categoryId' => $categoryId,
            'counts' => [
                'all' => $base()->count(),
                'active' => $base()->where('is_completed', false)->count(),
                'completed' => $base()->where('is_completed', true)->count(),
            ],
        ]);
    }

    /**
     * Persist a newly created todo.
     */
    public function store(Request $request): RedirectResponse
    {
        $user = $request->user();

        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'notes' => ['nullable', 'string', 'max:2000'],
            'category_id' => [
                'nullable',
                'integer',
                Rule::exists('categories', 'id')->where('user_id', $user->id),
            ],
            'due_date' => ['nullable', 'date'],
            'priority' => ['required', Rule::in(Todo::PRIORITIES)],
        ]);

        $user->todos()->create($data + ['is_completed' => false]);

        return redirect()
            ->to($this->currentUrl($request))
            ->with('success', 'Todo added.');
    }

    /**
     * Show the form for editing a todo.
     */
    public function edit(Request $request, Todo $todo): View
    {
        $this->authorizeTodo($request, $todo);

        return view('todos.edit', [
            'todo' => $todo,
            'categories' => $request->user()->categories()->orderBy('name')->get(),
        ]);
    }

    /**
     * Apply the edits made to a todo.
     */
    public function update(Request $request, Todo $todo): RedirectResponse
    {
        $this->authorizeTodo($request, $todo);

        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'notes' => ['nullable', 'string', 'max:2000'],
            'category_id' => [
                'nullable',
                'integer',
                Rule::exists('categories', 'id')->where('user_id', $request->user()->id),
            ],
            'due_date' => ['nullable', 'date'],
            'priority' => ['required', Rule::in(Todo::PRIORITIES)],
        ]);

        $todo->update($data);

        return redirect()->route('todos.index')->with('success', 'Todo updated.');
    }

    /**
     * Flip a todo between complete and not complete.
     */
    public function toggle(Request $request, Todo $todo): RedirectResponse
    {
        $this->authorizeTodo($request, $todo);

        $todo->update([
            'is_completed' => ! $todo->is_completed,
            'completed_at' => $todo->is_completed ? null : now(),
        ]);

        return redirect()
            ->to($this->currentUrl($request))
            ->with('success', $todo->is_completed ? 'Nice one — marked as done.' : 'Marked as not done.');
    }

    /**
     * Remove a todo.
     */
    public function destroy(Request $request, Todo $todo): RedirectResponse
    {
        $this->authorizeTodo($request, $todo);

        $todo->delete();

        return redirect()
            ->to($this->currentUrl($request))
            ->with('success', 'Todo deleted.');
    }

    /**
     * Make sure the todo belongs to the signed-in user.
     */
    private function authorizeTodo(Request $request, Todo $todo): void
    {
        abort_unless($todo->user_id === $request->user()->id, 404);
    }

    /**
     * Redirect back to the todo list, keeping any active filters intact.
     *
     * Reads query parameters only — the request body must never leak into the URL.
     */
    private function currentUrl(Request $request): string
    {
        $filters = array_intersect_key(
            $request->query(),
            array_flip(['status', 'category', 'priority'])
        );

        return route('todos.index', $filters);
    }
}
