<?php

namespace App\Http\Controllers\Api;

use App\Actions\Articles\GetArticlesAction;
use App\Http\Controllers\Controller;
use App\Http\Resources\ArticleResource;
use Illuminate\Http\Request;

class ArticleController extends Controller
{
    public function index(Request $request, GetArticlesAction $action)
    {
       
        $result = $action($request);

        
        $articlesData = $result['articles'] ?? collect([]);
        $sources      = $result['sources'] ?? [];
        $categories   = $result['categories'] ?? [];
        $pagination   = $result['pagination'] ?? [];

        return response()->json([
            'data' => ArticleResource::collection($articlesData),

            'meta' => [
                'pagination' => $pagination,
                'filters' => [
                    'available_sources'    => $sources,
                    'available_categories' => $categories,
                ],
            ],
        ]);
    }

    public function show(\App\Models\Article $article)
    {
        return new ArticleResource($article);
    }
}