@extends('layouts.app')

@section('title', 'Edit todo · ' . config('app.name'))

@section('content')
<div class="page-head">
    <div>
        <a class="back-link" href="{{ route('todos.index') }}">&larr; Back to todos</a>
        <h1>Edit todo</h1>
    </div>
</div>

<div class="card">
    <form method="POST" action="{{ route('todos.update', $todo) }}" class="stack">
        @csrf
        @method('PUT')

        <div class="field">
            <label for="title">Title</label>
            <input id="title" type="text" name="title" value="{{ old('title', $todo->title) }}"
                   required maxlength="255" autofocus>
        </div>

        <div class="field">
            <label for="notes">Notes</label>
            <textarea id="notes" name="notes" rows="3" maxlength="2000"
                      placeholder="Optional detail…">{{ old('notes', $todo->notes) }}</textarea>
        </div>

        <div class="field-row">
            <div class="field">
                <label for="category_id">Category</label>
                <select id="category_id" name="category_id">
                    <option value="">No category</option>
                    @foreach ($categories as $category)
                        <option value="{{ $category->id }}"
                            @selected((string) old('category_id', $todo->category_id) === (string) $category->id)>
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="field">
                <label for="due_date">Due date</label>
                <input id="due_date" type="date" name="due_date"
                       value="{{ old('due_date', $todo->due_date?->format('Y-m-d')) }}">
            </div>

            <div class="field">
                <label for="priority">Priority</label>
                <select id="priority" name="priority">
                    @foreach (\App\Models\Todo::PRIORITIES as $level)
                        <option value="{{ $level }}"
                            @selected(old('priority', $todo->priority) === $level)>
                            {{ ucfirst($level) }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary">Save changes</button>
            <a class="btn btn-ghost" href="{{ route('todos.index') }}">Cancel</a>
        </div>
    </form>
</div>
@endsection
