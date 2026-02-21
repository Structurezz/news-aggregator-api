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
    
        return response()->json([
            'data' => ArticleResource::collection($result['articles']),
            'pagination' => $result['pagination'] ?? null,
            'filters' => [
                'sources'    => $result['sources'] ?? [],
                'categories' => $result['categories'] ?? [],
            ],
        ]);
    }

    public function show(\App\Models\Article $article)
    {
        return new ArticleResource($article);
    }
}