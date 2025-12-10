<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $categories = Category::with('parentCategory')
            ->withCount('books')
            ->orderBy('display_order')
            ->orderBy('name')
            ->paginate(20);

        return view('admin.categories.index', compact('categories'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $parentCategories = Category::whereNull('parent_category_id')
            ->orderBy('name')
            ->get();

        return view('admin.categories.create', compact('parentCategories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:categories,name',
            'description' => 'nullable|string',
            'parent_category_id' => 'nullable|exists:categories,id',
            'display_order' => 'nullable|integer|min:0',
        ]);

        $category = Category::create($validated);

        // Se for requisição AJAX, retornar JSON
        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Categoria criada com sucesso!',
                'category' => $category
            ]);
        }

        return redirect()->route('admin.categories.index')
            ->with('success', 'Categoria criada com sucesso!');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Category $category)
    {
        $parentCategories = Category::whereNull('parent_category_id')
            ->where('id', '!=', $category->id)
            ->orderBy('name')
            ->get();

        return view('admin.categories.edit', compact('category', 'parentCategories'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Category $category)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:categories,name,' . $category->id,
            'description' => 'nullable|string',
            'parent_category_id' => 'nullable|exists:categories,id',
            'display_order' => 'nullable|integer|min:0',
        ]);

        $category->update($validated);

        return redirect()->route('admin.categories.index')
            ->with('success', 'Categoria atualizada com sucesso!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Category $category)
    {
        if ($category->books()->exists()) {
            return back()->with('error', 'Não é possível excluir esta categoria pois ela possui livros vinculados.');
        }

        if ($category->subcategories()->exists()) {
            return back()->with('error', 'Não é possível excluir esta categoria pois ela possui subcategorias.');
        }

        $category->delete();

        return redirect()->route('admin.categories.index')
            ->with('success', 'Categoria excluída com sucesso!');
    }
}
