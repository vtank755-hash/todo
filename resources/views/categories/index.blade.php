@extends('layouts.app')

@section('title', 'Categories · ' . config('app.name'))

@section('content')
<div class="page-head">
    <div>
        <h1>Categories</h1>
        <p class="page-sub">Group your todos. Deleting a category keeps its todos — just uncategorised.</p>
    </div>
</div>

<div class="card add-card">
    <form method="POST" action="{{ route('categories.store') }}" class="cat-form">
        @csrf

        <div class="field grow">
            <label for="cat-name">New category name</label>
            <input id="cat-name" type="text" name="name" value="{{ old('name') }}"
                   required maxlength="100" placeholder="e.g. Health">
        </div>

        <div class="field">
            <label for="cat-color">Colour</label>
            <input id="cat-color" type="color" name="color" value="{{ old('color', '#8b5cf6') }}">
        </div>

        <button type="submit" class="btn btn-primary">Add category</button>
    </form>
</div>

@if ($categories->isEmpty())
    <div class="card empty">
        <p class="empty-title">No categories yet.</p>
        <p class="empty-sub">Create one above, then assign todos to it.</p>
    </div>
@else
    <ul class="cat-list">
        @foreach ($categories as $category)
            <li class="cat-item">
                <form method="POST" action="{{ route('categories.update', $category) }}" class="cat-edit">
                    @csrf
                    @method('PUT')

                    <span class="swatch" style="--chip: {{ $category->color }}"></span>

                    <input type="text" name="name" value="{{ old('name.' . $category->id, $category->name) }}"
                           class="cat-name-input" maxlength="100" aria-label="Category name" required>

                    <input type="color" name="color" value="{{ old('color.' . $category->id, $category->color) }}"
                           class="cat-color-input" aria-label="Category colour">

                    <span class="cat-count">{{ $category->todos_count }} {{ \Illuminate\Support\Str::plural('todo', $category->todos_count) }}</span>

                    <button type="submit" class="btn btn-ghost btn-sm">Save</button>
                </form>

                <form method="POST" action="{{ route('categories.destroy', $category) }}"
                      data-confirm="Delete &quot;{{ $category->name }}&quot;? Its todos will be kept but become uncategorised.">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                </form>
            </li>
        @endforeach
    </ul>
@endif
@endsection
