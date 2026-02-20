@extends('layouts.app')

@section('title', $article->title ?? 'Article')

@section('content')
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
        <a href="{{ route('news.index') }}" class="inline-flex items-center text-indigo-600 hover:text-indigo-800 mb-6">
            ← Back to all news
        </a>

        <h1 class="text-4xl font-bold text-gray-900">{{ $article->title ?? 'Untitled Article' }}</h1>

        <div class="mt-4 flex flex-wrap gap-4 text-sm text-gray-600">
            <span class="font-medium">{{ ucwords(str_replace('.', ' ', $article->source_name ?? 'Unknown Source')) }}</span>
            @if($article->author)
                <span>By {{ $article->author }}</span>
            @endif
            @if($article->published_at)
                <span>{{ $article->published_at->format('F j, Y') }}</span>
            @else
                <span>Date not available</span>
            @endif
        </div>

        @if($article->image_url)
            <img src="{{ $article->image_url }}" alt="{{ $article->title ?? 'Article image' }}" class="mt-8 w-full rounded-xl shadow-lg object-cover max-h-96">
        @endif

        <div class="prose prose-lg mt-8 max-w-none">
            {!! nl2br(e($article->content ?? $article->description ?? 'Full content not available in aggregated data.')) !!}
        </div>

        @if($article->url)
            <a href="{{ $article->url }}" target="_blank" rel="noopener" class="mt-10 inline-flex items-center px-6 py-3 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 transition">
                Read original article on source site →
            </a>
        @endif
    </div>
@endsection