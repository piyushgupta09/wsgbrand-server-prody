<?php

namespace Fpaipl\Prody\Http\Controllers;

use Illuminate\Http\Request;
use Fpaipl\Prody\Models\Category;
use Fpaipl\Prody\Http\Requests\CategoryRequest;
use Fpaipl\Panel\Http\Controllers\PanelController;
use Fpaipl\Prody\Datatables\CategoryDatatable as Datatable;

class CategoryController extends PanelController
{
    public function __construct()
    {
        parent::__construct(new Datatable(), 'Fpaipl\Prody\Models\Category', 'category', 'categories.index');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CategoryRequest $request)
    {
        $category = Category::create($request->validated());

        // First check if category has parent, if not, it's a root category
        // but if it has parent, then it's a sub category, now check does its parent has parent or not
        // if not, then it's a sub category of root category, so we set display to true, otherwise false
        if ($category->hasParent()) {
            // If the parent category is a root category (no parent), set display to true
            $category->display = $category->parent?->parent ? false : true;
        } else {
            // If the category does not have a parent, it's a root category, set display to false
            $category->display = false;
        }

        $category->save();

        if (isset($category)) {

            $category
                ->addMedia($request->image)
                ->preservingOriginal()
                ->toMediaCollection(Category::MEDIA_COLLECTION_NAME);

            return redirect()->route('categories.index')->with('toast', [
                'class' => 'success',
                'text' => $this->messages['create_success']
            ]);
        } else {
            return redirect()->back()->withInput()->with('toast', [
                'class' => 'danger',
                'text' => $this->messages['create_error']
            ]);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(CategoryRequest $request, Category $category)
    {
        try {
            $category->update($request->validated());

            // Update display attribute based on parent category status
            if ($category->hasParent()) {
                // If the parent category is a root category (no parent), set display to true
                $category->display = $category->parent?->parent ? false : true;
            } else {
                // If the category does not have a parent, it's a root category, set display to false
                $category->display = false;
            }

            $category->save();

            if ($request->hasFile('image')) {
                $category
                    ->addMedia($request->image)
                    ->preservingOriginal()
                    ->toMediaCollection(Category::MEDIA_COLLECTION_NAME);
            }

            return redirect()->route('categories.edit', $category)->with('toast', [
                'class' => 'success',
                'text' => $this->messages['edit_success']
            ]);
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->with('toast', [
                'class' => 'danger',
                'text' => $this->messages['edit_error']
            ]);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, Category $category)
    {
        // Check if the category has child categories
        if ($category->hasChildren()) {
            return redirect()->back()->with('toast', [
                'class' => 'danger',
                'text' => 'Cannot delete category as it has child categories.'
            ]);
        }

        // Check if the category has associated products
        if ($category->hasProducts()) {
            return redirect()->back()->with('toast', [
                'class' => 'danger',
                'text' => 'Cannot delete category as it has associated products.'
            ]);
        }

        try {
            // if user is admin then force delete the category, otherwise soft delete the category

            if ($request->user()->hasRole('admin')) {
                $category->forceDelete();
            } else {
                $category->delete();
            }

            return redirect()->route('categories.index')->with('toast', [
                'class' => 'success',
                'text' => $this->messages['delete_success']
            ]);
        } catch (\Exception $e) {
            return redirect()->back()->with('toast', [
                'class' => 'danger',
                'text' => $this->messages['delete_error']
            ]);
        }
    }

}
