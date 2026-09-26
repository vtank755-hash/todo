@extends('layouts.app')

@section('title', 'Todos · ' . config('app.name'))

@section('content')
<div class="page-head">
    <div>
        <h1>Todos</h1>
        <p class="page-sub">
            {{ $counts['active'] }} open &middot; {{ $counts['completed'] }} done &middot; {{ $counts['all'] }} total
        </p>
    </div>
</div>

{{-- Add form --}}
<div class="card add-card">
    <form method="POST" action="{{ route('todos.store') }}" class="add-form">
        @csrf

        <div class="add-row">
            <input type="text" name="title" value="{{ old('title') }}"
                   class="add-title" placeholder="What needs doing?" required maxlength="255">

            <button type="submit" class="btn btn-primary">Add todo</button>
        </div>

        <div class="add-meta">
            <label class="inline-field">
                <span>Category</span>
                <select name="category_id">
                    <option value="">No category</option>
                    @foreach ($categories as $category)
                        <option value="{{ $category->id }}"
                            @selected((string) old('category_id') === (string) $category->id)>
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>
            </label>

            <label class="inline-field">
                <span>Due date</span>
                <input type="date" name="due_date" value="{{ old('due_date') }}">
            </label>

            <label class="inline-field">
                <span>Priority</span>
                <select name="priority">
                    @foreach (\App\Models\Todo::PRIORITIES as $level)
                        <option value="{{ $level }}" @selected(old('priority', 'medium') === $level)>
                            {{ ucfirst($level) }}
                        </option>
                    @endforeach
                </select>
            </label>

            <label class="inline-field grow">
                <span>Notes <em>(optional)</em></span>
                <input type="text" name="notes" value="{{ old('notes') }}" maxlength="2000"
                       placeholder="Any detail worth keeping…">
            </label>
        </div>
    </form>
</div>

{{-- Filters --}}
<div class="filters">
    <div class="tabs" role="tablist" aria-label="Filter by status">
        @foreach (['all' => 'All', 'active' => 'Active', 'completed' => 'Completed'] as $key => $label)
            <a href="{{ route('todos.index', array_filter(['status' => $key === 'all' ? null : $key, 'category' => $categoryId, 'priority' => $priority === 'all' ? null : $priority])) }}"
               class="tab {{ $status === $key ? 'is-active' : '' }}">
                {{ $label }}
                <span class="tab-count">{{ $counts[$key] }}</span>
            </a>
        @endforeach
    </div>

    <form method="GET" action="{{ route('todos.index') }}" class="filter-form">
        <input type="hidden" name="status" value="{{ $status === 'all' ? '' : $status }}">

        <label class="inline-field">
            <span class="sr-only">Category</span>
            <select name="category" onchange="this.form.submit()">
                <option value="">All categories</option>
                @foreach ($categories as $category)
                    <option value="{{ $category->id }}" @selected($categoryId === $category->id)>
                        {{ $category->name }}
                    </option>
                @endforeach
            </select>
        </label>

        <label class="inline-field">
            <span class="sr-only">Priority</span>
            <select name="priority" onchange="this.form.submit()">
                <option value="">All priorities</option>
                @foreach (\App\Models\Todo::PRIORITIES as $level)
                    <option value="{{ $level }}" @selected($priority === $level)>
                        {{ ucfirst($level) }}
                    </option>
                @endforeach
            </select>
        </label>

        @if ($categoryId || $priority !== 'all' || $status !== 'all')
            <a class="btn btn-ghost btn-sm" href="{{ route('todos.index') }}">Clear</a>
        @endif
    </form>
</div>

{{-- List --}}
@if ($todos->isEmpty())
    <div class="card empty">
        <p class="empty-title">
            @if ($status === 'completed')
                Nothing completed yet.
            @elseif ($status === 'active')
                You're all caught up — no open todos.
            @else
                No todos here yet.
            @endif
        </p>
        <p class="empty-sub">Use the form above to add your first one.</p>
    </div>
@else
    <ul class="todo-list">
        @foreach ($todos as $todo)
            <li class="todo {{ $todo->is_completed ? 'is-done' : '' }}">
                <form method="POST" action="{{ route('todos.toggle', $todo) }}" class="todo-check">
                    @csrf
                    @method('PATCH')
                    <input type="checkbox"
                           class="visually-hidden"
                           onchange="this.form.submit()"
                           {{ $todo->is_completed ? 'checked' : '' }}
                           aria-label="Mark &quot;{{ $todo->title }}&quot; {{ $todo->is_completed ? 'not done' : 'done' }}">
                    <span class="box" aria-hidden="true"></span>
                </form>

                <div class="todo-body">
                    <div class="todo-line">
                        <span class="todo-title">{{ $todo->title }}</span>

                        @if ($todo->category)
                            <span class="chip" style="--chip: {{ $todo->category->color }}">
                                {{ $todo->category->name }}
                            </span>
                        @endif

                        <span class="badge priority-{{ $todo->priority }}">{{ $todo->priority }}</span>
                    </div>

                    @if ($todo->notes)
                        <p class="todo-notes">{{ $todo->notes }}</p>
                    @endif

                    @if ($todo->due_date)
                        <span class="due {{ $todo->isOverdue() ? 'is-overdue' : ($todo->isDueToday() ? 'is-today' : '') }}">
                            @if ($todo->isOverdue())
                                Overdue · {{ $todo->due_date->format('D j M Y') }}
                            @elseif ($todo->isDueToday())
                                Due today
                            @else
                                Due {{ $todo->due_date->format('D j M Y') }}
                            @endif
                        </span>
                    @endif
                </div>

                <div class="todo-actions">
                    <a class="btn btn-ghost btn-sm" href="{{ route('todos.edit', $todo) }}">Edit</a>

                    <form method="POST" action="{{ route('todos.destroy', $todo) }}"
                          data-confirm="Delete &quot;{{ $todo->title }}&quot;? This cannot be undone.">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                    </form>
                </div>
            </li>
        @endforeach
    </ul>
@endif
@endsection
