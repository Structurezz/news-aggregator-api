@extends('layouts.app')

@section('title', ($article->title ?? 'Article') . ' | Michael Orizu')

@section('content')
<div class="bg-[#fafafa] min-h-screen pb-20 selection:bg-indigo-500 selection:text-white">
    
    {{-- Floating Header Logic --}}
    <nav class="sticky top-6 z-50 max-w-5xl mx-auto px-4 pointer-events-none">
        <div class="pointer-events-auto bg-white/80 backdrop-blur-2xl border border-white/20 shadow-sm rounded-2xl px-6 py-3 flex items-center justify-between">
            <a href="{{ route('news.index') }}" class="group flex items-center gap-2 text-xs font-black uppercase tracking-widest text-gray-900">
                <svg class="w-4 h-4 group-hover:-translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
                Back to Feed
            </a>
            <div class="flex items-center gap-3">
                <span class="text-[10px] font-bold text-indigo-600 uppercase tracking-widest">{{ $article->source_name }}</span>
                <div class="h-1 w-1 rounded-full bg-gray-300"></div>
                <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Article View</span>
            </div>
        </div>
    </nav>

    <article class="max-w-5xl mx-auto px-4 mt-12">
        {{-- Article Header --}}
        <header class="mb-12">
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-indigo-50 text-indigo-600 text-[10px] font-bold uppercase tracking-[0.2em] mb-6">
                <span class="relative flex h-2 w-2">
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-indigo-400 opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-2 w-2 bg-indigo-600"></span>
                </span>
                Verified Source
            </div>
            
            <h1 class="text-5xl md:text-7xl font-black text-gray-900 tracking-tighter leading-[0.95] mb-8 animate-in fade-in slide-in-from-bottom-6 duration-1000">
                {{ $article->title ?? 'Untitled Intelligence Report' }}
            </h1>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8 py-8 border-y border-gray-100">
                <div>
                    <p class="text-[10px] font-black uppercase tracking-widest text-gray-400 mb-1">Publisher</p>
                    <p class="text-sm font-bold text-gray-900 uppercase">{{ ucwords(str_replace('.', ' ', $article->source_name)) }}</p>
                </div>
                <div>
                    <p class="text-[10px] font-black uppercase tracking-widest text-gray-400 mb-1">Intelligence Date</p>
                    <p class="text-sm font-bold text-gray-900">{{ $article->published_at ? $article->published_at->format('F j, Y') : 'Pending' }}</p>
                </div>
                <div>
                    <p class="text-[10px] font-black uppercase tracking-widest text-gray-400 mb-1">Lead Correspondent</p>
                    <p class="text-sm font-bold text-gray-900">{{ $article->author ?? 'Innoscripta Global' }}</p>
                </div>
            </div>
        </header>

        {{-- Cinematic Hero Image --}}
        @if($article->image_url)
            <div class="relative group rounded-[2.5rem] overflow-hidden shadow-2xl mb-16 aspect-[21/9]">
                <img src="{{ $article->image_url }}" 
                     alt="{{ $article->title }}" 
                     class="w-full h-full object-cover transition-transform duration-1000 group-hover:scale-105">
                <div class="absolute inset-0 ring-1 ring-inset ring-black/10 rounded-[2.5rem]"></div>
            </div>
        @endif

        {{-- Content Grid --}}
        <div class="grid grid-cols-12 gap-12">
            {{-- Main Text --}}
            <div class="col-span-12 lg:col-span-8">
                <div class="prose prose-xl prose-indigo max-w-none text-gray-800 leading-relaxed font-serif italic">
                    {!! nl2br(e($article->content ?? $article->description ?? 'Initial data acquisition complete. Full detailed analysis pending from source.')) !!}
                </div>

                @if($article->url)
                    <div class="mt-16 p-8 rounded-[2rem] bg-gray-900 text-white flex flex-col md:flex-row items-center justify-between gap-6 transition-all hover:shadow-xl hover:shadow-indigo-200">
                        <div>
                            <h4 class="text-xl font-bold mb-1">Read the full investigation</h4>
                            <p class="text-gray-400 text-sm">You are viewing a normalized intelligence summary.</p>
                        </div>
                        <a href="{{ $article->url }}" target="_blank" rel="noopener" class="whitespace-nowrap px-8 py-4 bg-indigo-600 hover:bg-indigo-500 rounded-xl font-black uppercase tracking-widest text-xs transition-colors">
                            Original Source →
                        </a>
                    </div>
                @endif
            </div>

            {{-- Sidebar --}}
            <aside class="col-span-12 lg:col-span-4 space-y-8">
                <div class="sticky top-28 space-y-8">
                    <div class="p-8 rounded-[2rem] bg-white border border-gray-100 shadow-sm">
                        <h4 class="text-xs font-black uppercase tracking-widest text-gray-400 mb-6">Metadata</h4>
                        <ul class="space-y-4">
                            <li class="flex justify-between text-xs">
                                <span class="text-gray-400 font-bold uppercase">ID</span>
                                <span class="font-mono text-gray-900">#{{ str_pad($article->id, 5, '0', STR_PAD_LEFT) }}</span>
                            </li>
                            <li class="flex justify-between text-xs">
                                <span class="text-gray-400 font-bold uppercase">Topic</span>
                                <span class="text-indigo-600 font-bold uppercase">{{ $article->category ?? 'Global' }}</span>
                            </li>
                            <li class="flex justify-between text-xs">
                                <span class="text-gray-400 font-bold uppercase">Format</span>
                                <span class="text-gray-900 font-bold uppercase">Digital/JSON</span>
                            </li>
                        </ul>
                    </div>

                    <div class="p-8 rounded-[2rem] bg-indigo-50 border border-indigo-100">
                        <p class="text-xs text-indigo-900 leading-relaxed font-medium">
                            <strong class="block mb-2 uppercase tracking-widest">Disclaimer</strong>
                            This article was aggregated and normalized using Michael Orizu's Intelligence Feed. Data is refreshed every 30 minutes.
                        </p>
                    </div>
                </div>
            </aside>
        </div>
    </article>
</div>
@endsection