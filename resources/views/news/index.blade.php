@extends('layouts.app')

@section('title', 'Michael Orizu | Global News Aggregator')

@section('content')
<div class="bg-gray-50 min-h-screen pb-20">
    <div class="bg-white border-b border-gray-200 mb-8 shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
            <div class="flex flex-col md:flex-row md:items-end md:justify-between gap-6">
                <div>
                    <div class="flex items-center gap-3 mb-4">
                        <span class="relative flex h-2 w-2">
                          <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-indigo-400 opacity-75"></span>
                          <span class="relative inline-flex rounded-full h-2 w-2 bg-indigo-600"></span>
                        </span>
                        <span class="text-[10px] font-black uppercase tracking-[0.3em] text-gray-400">Live Intelligence Feed</span>
                    </div>
                    
                    <h1 class="text-5xl md:text-6xl font-black text-gray-900 tracking-tighter">
                        Michael Orizu<span class="text-indigo-600">.</span>
                    </h1>
                    <p class="text-xl text-gray-400 font-medium mt-2">Global News Aggregator</p>
                </div>

                <div class="hidden lg:block text-right border-l-2 border-gray-100 pl-8">
                    <div class="text-[10px] uppercase tracking-widest font-bold text-gray-400 mb-1">Global Edition</div>
                    <div class="text-lg font-bold text-gray-900">{{ date('l, F d, Y') }}</div>
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <form method="GET" class="sticky top-4 z-20 mb-12">
            <div class="bg-white/80 backdrop-blur-xl shadow-2xl shadow-indigo-100/50 border border-white rounded-2xl p-4 flex flex-wrap items-end gap-6">
                <div class="flex-1 min-w-[200px]">
                    <label class="block text-[10px] uppercase tracking-widest font-bold text-gray-400 mb-1 ml-1">Search Archive</label>
                    <div class="relative">
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Keywords..." 
                            class="w-full pl-4 pr-10 py-3 bg-gray-100/50 border-transparent rounded-xl focus:ring-2 focus:ring-indigo-500 focus:bg-white transition-all">
                        <span class="absolute right-3 top-3.5 text-gray-400">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        </span>
                    </div>
                </div>

                <div class="w-full md:w-48">
    <label class="block text-[10px] uppercase tracking-widest font-bold text-gray-400 mb-1 ml-1">Source</label>
    <select name="source" class="w-full py-3 bg-gray-100/50 border-transparent rounded-xl focus:ring-2 focus:ring-indigo-500 appearance-none cursor-pointer">
        <option value="">All Sources</option>
        @foreach($sources as $source)
            <option value="{{ $source }}" {{ request('source') == $source ? 'selected' : '' }}>
                {{ ucwords(str_replace('.', ' ', $source)) }}
            </option>
        @endforeach
    </select>
</div>

                <div class="w-full md:w-48">
                    <label class="block text-[10px] uppercase tracking-widest font-bold text-gray-400 mb-1 ml-1">Category</label>
                    <select name="category" class="w-full py-3 bg-gray-100/50 border-transparent rounded-xl focus:ring-2 focus:ring-indigo-500 appearance-none cursor-pointer">
                        <option value="">All Topics</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat }}" {{ request('category') == $cat ? 'selected' : '' }}>{{ ucfirst($cat) }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="flex items-center gap-3 ml-auto">
                    <a href="{{ route('news.index') }}" class="px-4 py-3 text-xs font-bold text-gray-400 hover:text-indigo-600 transition uppercase tracking-widest">Reset</a>
                    <button type="submit" class="bg-gray-900 text-white px-8 py-3 rounded-xl font-bold hover:bg-indigo-600 hover:-translate-y-0.5 transition-all shadow-lg">
                        Update Feed
                    </button>
                </div>
            </div>
        </form>

        @if($articles->isEmpty())
            <div class="text-center py-24 bg-white rounded-3xl border-2 border-dashed border-gray-200">
                <h3 class="text-xl font-bold text-gray-900 italic">No stories found.</h3>
                <p class="text-gray-500 mt-2 font-medium">Try adjusting your filters or search keywords.</p>
            </div>
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-10">
                @foreach($articles as $article)
                    <article class="group bg-white rounded-3xl border border-gray-100 shadow-sm hover:shadow-2xl transition-all duration-500 overflow-hidden flex flex-col">
                        <div class="relative aspect-[16/9] overflow-hidden">
                            @if($article->image_url)
                                <img src="{{ $article->image_url }}" alt="{{ $article->title }}" 
                                    class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700">
                            @else
                                <div class="w-full h-full bg-gray-100 flex items-center justify-center">
                                    <span class="text-gray-300 font-black text-5xl tracking-tighter opacity-20 uppercase">Orizu</span>
                                </div>
                            @endif
                            <div class="absolute top-4 left-4">
                                <span class="bg-black/80 backdrop-blur-md text-white px-3 py-1 rounded-lg text-[10px] font-bold uppercase tracking-widest">
                                    {{ ucwords(str_replace('.', ' ', $article->source_name)) }}
                                </span>
                            </div>
                        </div>

                        <div class="p-8 flex flex-col flex-1">
                            <div class="text-[10px] font-bold text-indigo-600 mb-3 uppercase tracking-[0.2em]">
                                {{ $article->published_at->format('M d, Y') }} — {{ $article->author ?? 'Staff Writer' }}
                            </div>

                            <h3 class="text-2xl font-bold text-gray-900 leading-[1.15] mb-4 group-hover:text-indigo-600 transition-colors line-clamp-2">
                                <a href="{{ route('news.show', $article) }}">
                                    {{ $article->title }}
                                </a>
                            </h3>

                            <p class="text-gray-500 leading-relaxed line-clamp-3 mb-6 text-sm">
                                {{ $article->description ?? 'Full analysis and report available inside. Click to view the complete story.' }}
                            </p>

                            <div class="mt-auto pt-6 border-t border-gray-50">
                                <a href="{{ route('news.show', $article) }}" class="inline-flex items-center text-xs font-black uppercase tracking-widest text-gray-900 group/link">
                                    Read Full Story
                                    <svg class="ml-2 w-4 h-4 group-hover/link:translate-x-2 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                                    </svg>
                                </a>
                            </div>
                        </div>
                    </article>
                @endforeach
            </div>

            <div class="mt-20 flex justify-center">
                <div class="bg-white px-6 py-2 rounded-full shadow-sm border border-gray-100">
                    {{ $articles->links('pagination::tailwind') }}
                </div>
            </div>
        @endif
    </div>
</div>
@endsection