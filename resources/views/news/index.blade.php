@extends('layouts.app')

@section('title', 'OrizuNews | Global Briefing')

@section('content')
<div class="bg-[#f8fafc] min-h-screen font-sans antialiased text-slate-900">
    {{-- High-End Navigation --}}
    <header class="sticky top-0 z-50 bg-white/70 backdrop-blur-xl border-b border-slate-200/60">
        <div class="max-w-[1600px] mx-auto px-6 flex items-center h-14">
            <a href="{{ route('news.index') }}" class="flex items-center gap-2 group">
                <div class="w-7 h-7 bg-indigo-600 rounded-lg flex items-center justify-center text-white font-black text-xs group-hover:rotate-12 transition-transform">O</div>
                <span class="text-base font-bold tracking-tight text-slate-900">ORIZU<span class="text-indigo-600 italic">NEWS</span></span>
            </a>
            
            <nav class="hidden xl:flex ml-10 items-center gap-1">
                @foreach($categories->take(8) as $cat)
                    <a href="?category={{ $cat }}" class="px-3 py-1.5 text-[11px] font-bold text-slate-500 hover:text-indigo-600 hover:bg-indigo-50 rounded-lg capitalize transition-all">
                        {{ $cat }}
                    </a>
                @endforeach
            </nav>

            <div class="ml-auto flex items-center gap-5">
                <div class="hidden md:flex items-center gap-2 px-3 py-1 bg-slate-100 rounded-full border border-slate-200">
                    <span class="relative flex h-2 w-2">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-2 w-2 bg-green-500"></span>
                    </span>
                    <span class="text-[10px] font-bold text-slate-600 uppercase tracking-tighter">Live Updates</span>
                </div>
                <button class="text-slate-400 hover:text-indigo-600 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </button>
            </div>
        </div>
    </header>

    <main class="max-w-[1600px] mx-auto px-6 py-8">
        {{-- Interactive Filter Bar --}}
        <div class="flex flex-wrap items-center justify-between gap-4 mb-10">
            <form method="GET" class="flex items-center bg-white p-1 rounded-2xl shadow-sm border border-slate-200 w-full lg:w-auto">
                <input type="text" name="search" value="{{ request('search') }}" 
                    placeholder="Search news..." 
                    class="bg-transparent pl-4 pr-2 py-2 text-sm outline-none w-full lg:w-64">
                
                <div class="h-6 w-[1px] bg-slate-200 hidden lg:block"></div>

                <select name="source" class="bg-transparent px-4 py-2 text-xs font-bold text-slate-600 outline-none cursor-pointer hidden lg:block">
                    <option value="">All Sources</option>
                    @foreach($sources as $source)
                        <option value="{{ $source }}" {{ request('source') == $source ? 'selected' : '' }}>
                            {{ ucwords(str_replace('.', ' ', $source)) }}
                        </option>
                    @endforeach
                </select>

                <button type="submit" class="bg-indigo-600 text-white p-2 rounded-xl hover:bg-indigo-700 transition-all">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                </button>
            </form>

            <div class="flex items-center gap-2 overflow-x-auto pb-2 lg:pb-0">
                <span class="text-[10px] font-black uppercase text-slate-400 tracking-widest mr-2">Trending:</span>
                @foreach(['Tech', 'Politics', 'Global', 'Finance'] as $trend)
                    <button class="whitespace-nowrap px-3 py-1 bg-white border border-slate-200 rounded-full text-[10px] font-bold text-slate-600 hover:border-indigo-400 transition-all">#{{ $trend }}</button>
                @endforeach
            </div>
        </div>

        @if($articles->isEmpty())
            <div class="bg-white rounded-3xl p-20 text-center border border-dashed border-slate-300">
                <div class="text-4xl mb-4">Empty folder icon</div>
                <h3 class="text-xl font-bold text-slate-800">No stories match your search.</h3>
                <p class="text-sm text-slate-500">Try broadening your filters.</p>
            </div>
        @else
            {{-- Modern Bento Hero --}}
            @php $hero = $articles->first(); @endphp
            <div class="grid grid-cols-12 gap-6 mb-12">
                <article class="col-span-12 lg:col-span-8 relative group rounded-3xl overflow-hidden shadow-2xl shadow-indigo-100 aspect-[16/9] lg:aspect-auto lg:h-[450px]">
                    <a href="{{ route('news.show', $hero) }}" class="block w-full h-full">
                        <img src="{{ $hero->image_url ?? 'https://images.unsplash.com/photo-1504711434969-e33886168f5c?w=1200' }}" 
                             class="w-full h-full object-cover transition-transform duration-1000 group-hover:scale-105">
                        <div class="absolute inset-0 bg-gradient-to-t from-slate-900 via-slate-900/40 to-transparent"></div>
                        <div class="absolute bottom-0 p-8 lg:p-12">
                            <div class="flex items-center gap-3 mb-4">
                                <span class="px-2.5 py-1 rounded-lg bg-indigo-600 text-white text-[10px] font-black uppercase tracking-widest">Featured</span>
                                <span class="text-white/70 text-[10px] font-bold uppercase tracking-widest">{{ $hero->source_name }}</span>
                            </div>
                            <h2 class="text-3xl lg:text-5xl font-extrabold text-white leading-[1.1] mb-4 group-hover:text-indigo-200 transition-colors">
                                {{ $hero->title }}
                            </h2>
                            <p class="text-slate-300 text-sm lg:text-base line-clamp-2 max-w-2xl font-medium">
                                {{ $hero->description }}
                            </p>
                        </div>
                    </a>
                </article>

                {{-- Sidebar/Spotlight inside Bento --}}
                <div class="hidden lg:flex lg:col-span-4 flex-col gap-6">
                    <div class="bg-indigo-600 rounded-3xl p-8 text-white relative overflow-hidden flex-1">
                        <div class="relative z-10">
                            <h4 class="text-xs font-black uppercase tracking-widest opacity-80 mb-2">Editor's Note</h4>
                            <p class="text-xl font-bold leading-tight">Curating the world's most vital headlines, delivered in real-time.</p>
                        </div>
                        <div class="absolute -right-10 -bottom-10 w-40 h-40 bg-white/10 rounded-full blur-3xl"></div>
                    </div>
                    <div class="bg-white border border-slate-200 rounded-3xl p-6 flex-1">
                        <h4 class="text-[10px] font-black uppercase tracking-widest text-slate-400 mb-4">Top Sources</h4>
                        <div class="space-y-3">
                            @foreach($sources->take(4) as $source)
                                <div class="flex items-center justify-between">
                                    <span class="text-xs font-bold text-slate-700">{{ ucwords(str_replace('.', ' ', $source)) }}</span>
                                    <div class="h-1 w-12 bg-slate-100 rounded-full overflow-hidden">
                                        <div class="h-full bg-indigo-500 w-2/3"></div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            {{-- 5-Column Clean Grid --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-x-6 gap-y-10">
                @foreach($articles->skip(1) as $article)
                    <article class="group flex flex-col">
                        <a href="{{ route('news.show', $article) }}" class="relative mb-4 block overflow-hidden rounded-2xl aspect-[4/3] bg-slate-200 shadow-sm">
                            <img src="{{ $article->image_url ?? 'https://images.unsplash.com/photo-1585829365234-781f34994269?w=400' }}" 
                                 class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110"
                                 loading="lazy">
                            <div class="absolute top-3 left-3">
                                <span class="px-2 py-1 bg-white/90 backdrop-blur shadow-sm rounded-lg text-[8px] font-black uppercase text-indigo-600 tracking-tighter">
                                    {{ $article->source_name }}
                                </span>
                            </div>
                        </a>
                        <div class="px-1">
                            <div class="flex items-center gap-2 mb-2 text-[10px] font-bold text-slate-400">
                                <span>{{ $article->published_at->diffForHumans() }}</span>
                                <span class="w-1 h-1 bg-slate-300 rounded-full"></span>
                                <span class="text-indigo-500/80">5 min read</span>
                            </div>
                            <h3 class="text-[14px] font-bold leading-[1.4] text-slate-800 group-hover:text-indigo-600 transition-colors line-clamp-3">
                                <a href="{{ route('news.show', $article) }}">{{ $article->title }}</a>
                            </h3>
                        </div>
                    </article>
                @endforeach
            </div>
        @endif

        {{-- Sophisticated Pagination --}}
        <div class="mt-20 pt-10 border-t border-slate-200">
            <div class="flex flex-col items-center gap-4">
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-[0.2em]">End of Briefing</p>
                <div class="inline-flex shadow-sm rounded-xl overflow-hidden border border-slate-200">
                    {{ $articles->links() }}
                </div>
            </div>
        </div>
    </main>
</div>
@endsection