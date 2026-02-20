<?php

namespace App\Http\Controllers;

use App\Actions\Articles\GetArticlesAction;
use Illuminate\Http\Request;

class NewsWebController extends Controller
{
    public function index(Request $request, GetArticlesAction $action)
    {
        $result = $action($request); 

        $articles   = $result['articles'] ?? collect([]);
        $sources    = $result['sources'] ?? [];
        $categories = $result['categories'] ?? [];
        $stats      = $result['stats'] ?? [];
        $pagination = $result['pagination'] ?? [];

        return view('news.index', compact(
            'articles',
            'sources',
            'categories',
            'stats',
            'pagination'
        ));
    }
}