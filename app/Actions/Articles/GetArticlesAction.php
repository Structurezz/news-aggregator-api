<?php

namespace App\Actions\Articles;

use App\Models\Article;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Spatie\QueryBuilder\QueryBuilder;
use Spatie\QueryBuilder\AllowedFilter;

class GetArticlesAction
{
    public function __invoke(Request $request): array
    {
        return $this->execute($request);
    }

    public function execute(Request $request): array
    {
        $perPage = $request->integer('per_page', 20);

        $request->merge([
            'filter' => [
                'source_name' => $request->query('source'),
                'category'    => $request->query('category'),
                'author'      => $request->query('author'),
                'search'      => $request->query('search'),
                'from'        => $request->query('from'),
                'to'          => $request->query('to'),
            ]
        ]);

        $query = QueryBuilder::for(Article::class)
            ->allowedFilters([
                AllowedFilter::exact('source_name'),
                AllowedFilter::exact('category'),
                AllowedFilter::exact('author'),

              
                AllowedFilter::callback('search', fn($q, $value) =>
                    $q->where(fn($sub) => $sub
                        ->where('title', 'like', "%{$value}%")
                        ->orWhere('description', 'like', "%{$value}%")
                        ->orWhere('content', 'like', "%{$value}%")
                    )
                ),

         
                AllowedFilter::callback('from', fn($q, $value) => $value ? $q->whereDate('published_at', '>=', $value) : $q),
                AllowedFilter::callback('to', fn($q, $value) => $value ? $q->whereDate('published_at', '<=', $value) : $q),
            ])
            ->allowedSorts(['published_at'])
            ->defaultSort('-published_at');

        $paginated = $query->paginate($perPage)->withQueryString();


        $totalArticles  = Article::count();
        $lastUpdatedRaw = Article::max('updated_at');
        $lastUpdated    = $lastUpdatedRaw ? Carbon::parse($lastUpdatedRaw)->toIso8601String() : null;

        $sources = Article::distinct()->pluck('source_name')->filter()->values();
        $categories = Article::distinct()->pluck('category')->filter()->values();

        return [
            'articles'   => $paginated,
            'sources'    => $sources,
            'categories' => $categories,
            'stats'      => [
                'total_articles' => $totalArticles,
                'last_updated'   => $lastUpdated,
                'last_fetch'     => now()->toIso8601String(),
            ],
            'pagination' => [
                'current_page' => $paginated->currentPage(),
                'last_page'    => $paginated->lastPage(),
                'per_page'     => $paginated->perPage(),
                'total'        => $paginated->total(),
                'from'         => $paginated->firstItem(),
                'to'           => $paginated->lastItem(),
            ],
        ];
    }
}