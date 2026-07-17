<?php

namespace App\Http\Controllers\API\Customer;

use App\Http\Controllers\API\BaseApiController;
use App\Models\ServiceCategory;

class CategoryController extends BaseApiController
{
    public function index()
    {
        $categories = ServiceCategory::where('status', true)->select('id', 'name', 'slug', 'icon', 'description')->orderBy('name')->get();

        return $this->successResponse($categories, 'Categories fetched successfully.');
    }
}
