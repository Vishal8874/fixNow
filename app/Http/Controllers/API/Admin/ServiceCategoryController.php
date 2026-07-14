<?php

namespace App\Http\Controllers\API\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseApiController;
use App\Http\Requests\StoreServiceCategoryRequest;
use App\Http\Requests\UpdateServiceCategoryRequest;
use App\Models\ServiceCategory;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ServiceCategoryController extends BaseApiController
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $categories = ServiceCategory::latest()->get();

        return $this->successResponse($categories, 'Service categories fetched successfully.');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreServiceCategoryRequest $request)
    {
        $icon = null;

        if ($request->hasFile('icon')) {
            $icon = $request->file('icon')->store('service_categories', 'public');
        }

        $category = ServiceCategory::create([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'icon' => $icon,
            'description' => $request->description,
            'status' => $request->boolean('status', true),
        ]);

        return $this->successResponse($category, 'Service category created successfully.', 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(ServiceCategory $category)
    {
        return $this->successResponse($category, 'Service category fetched successfully.');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateServiceCategoryRequest $request, ServiceCategory $category)
    {
        // dd($request->all(), $request->file(), $category);

        if ($request->hasFile('icon')) {
            if ($category->icon) {
                Storage::disk('public')->delete($category->icon);
            }

            $category->icon = $request->file('icon')->store('service_categories', 'public');
        }

        if ($request->filled('name')) {
            $category->name = $request->name;

            $category->slug = Str::slug($request->name);
        }

        if ($request->filled('description')) {
            $category->description = $request->description;
        }

        if ($request->has('status')) {
            $category->status = $request->boolean('status');
        }

        $category->save();

        return $this->successResponse($category, 'Service category updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ServiceCategory $category)
    {
        $category->delete();

        return $this->successResponse(null, 'Service category deleted successfully.');
    }
}
