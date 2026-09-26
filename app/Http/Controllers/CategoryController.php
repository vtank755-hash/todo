<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class CategoryController extends Controller
{
    /**
     * List the user's categories alongside their todo counts.
     */
    public function index(Request $request): View
    {
        $categories = $request->user()
            ->categories()
            ->withCount('todos')
            ->orderBy('name')
            ->get();

        return view('categories.index', compact('categories'));
    }

    /**
     * Persist a new category.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->user()->categories()->create($this->validated($request));

        return redirect()->route('categories.index')->with('success', 'Category created.');
    }

    /**
     * Apply the edits made to a category.
     */
    public function update(Request $request, Category $category): RedirectResponse
    {
        $this->authorizeCategory($request, $category);

        $category->update($this->validated($request, $category));

        return redirect()->route('categories.index')->with('success', 'Category updated.');
    }

    /**
     * Delete a category. Its todos are kept, just left uncategorised.
     */
    public function destroy(Request $request, Category $category): RedirectResponse
    {
        $this->authorizeCategory($request, $category);

        $category->delete();

        return redirect()->route('categories.index')->with('success', 'Category deleted. Its todos were kept.');
    }

    /**
     * Validate an incoming category payload.
     *
     * @return array<string, mixed>
     */
    private function validated(Request $request, ?Category $category = null): array
    {
        return $request->validate([
            'name' => [
                'required',
                'string',
                'max:100',
                Rule::unique('categories', 'name')
                    ->where('user_id', $request->user()->id)
                    ->ignore($category?->id),
            ],
            'color' => ['required', 'string', 'regex:/^#[0-9A-Fa-f]{6}$/'],
        ]);
    }

    /**
     * Make sure the category belongs to the signed-in user.
     */
    private function authorizeCategory(Request $request, Category $category): void
    {
        abort_unless($category->user_id === $request->user()->id, 404);
    }
}
