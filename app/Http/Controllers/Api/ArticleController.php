<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ArticleResource;
use App\Models\Article;
use Illuminate\Http\Request;

class ArticleController extends Controller
{
    public function index(Request $request)
    {
        $query = Article::query()
            ->when($request->filled('search'), fn($q) => 
                $q->whereFullText(['title', 'description', 'content'], $request->search)
            )
            ->when($request->filled('source'), fn($q) => $q->where('source_name', $request->source))
            ->when($request->filled('category'), fn($q) => $q->where('category', $request->category))
            ->when($request->filled('author'), fn($q) => $q->where('author', $request->author))
            ->when($request->filled('from'), fn($q) => $q->whereDate('published_at', '>=', $request->from))
            ->when($request->filled('to'), fn($q) => $q->whereDate('published_at', '<=', $request->to))
            ->latest('published_at');

        $articles = $query->paginate(20);

        return ArticleResource::collection($articles);
    }

    public function show(Article $article)
    {
        return new ArticleResource($article);
    }
}