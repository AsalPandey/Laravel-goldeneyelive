<x-layouts::app :title="__('Editorial Management')">
    <div class="flex h-full w-full flex-1 flex-col gap-8 p-8">
        <div class="flex items-center justify-between pb-4 border-b border-neutral-100">
            <div class="space-y-1">
                <h1 class="text-3xl font-black text-neutral-900 tracking-tight uppercase">Editorial <span class="text-brand-gold">Hub</span></h1>
                <p class="text-neutral-500 text-sm">Create and manage academy news, updates, and articles.</p>
            </div>
            <a href="{{ route('admin.blog.create') }}" class="inline-flex items-center gap-2 rounded-xl bg-brand-gold text-brand-dark px-8 py-3.5 text-sm font-black uppercase shadow-xl hover:bg-brand-dark hover:text-brand-gold transition-all scale-105">
                <i class="fa fa-pen-nib"></i> Compose Article
            </a>
        </div>

        <div class="flex flex-col md:flex-row items-center justify-between gap-4 bg-white p-6 rounded-2xl border border-neutral-100 shadow-sm">
            <div class="relative flex-1 max-w-md w-full">
                <i class="fa fa-search absolute left-4 top-1/2 -translate-y-1/2 text-neutral-400"></i>
                <form action="{{ route('admin.blog.index') }}" method="GET">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search articles or authors..." 
                           class="w-full pl-12 pr-4 py-3 bg-neutral-50 border-neutral-100 rounded-xl text-sm focus:ring-0 focus:border-brand-gold transition-all">
                </form>
            </div>
            <div class="flex items-center gap-4 text-[11px] font-black uppercase text-neutral-400 tracking-widest">
                <div class="flex items-center gap-2">
                    <span class="h-2 w-2 rounded-full bg-emerald-500"></span> Published: {{ \App\Models\BlogPost::where('status', 'published')->count() }}
                </div>
                <div class="flex items-center gap-2 border-l border-neutral-100 pl-4">
                    <span class="h-2 w-2 rounded-full bg-amber-500"></span> Drafts: {{ \App\Models\BlogPost::where('status', 'draft')->count() }}
                </div>
            </div>
        </div>

        <div class="overflow-x-auto rounded-2xl border border-neutral-100 bg-white shadow-sm">
            <table class="min-w-full divide-y divide-neutral-200">
                <thead class="bg-neutral-50/50">
                    <tr>
                        <th scope="col" class="px-6 py-4 text-left text-[10px] font-black uppercase tracking-widest text-neutral-400">Article Content</th>
                        <th scope="col" class="px-6 py-4 text-left text-[10px] font-black uppercase tracking-widest text-neutral-400">Visibility Status</th>
                        <th scope="col" class="px-6 py-4 text-left text-[10px] font-black uppercase tracking-widest text-neutral-400">Contributors</th>
                        <th scope="col" class="px-6 py-4 text-left text-[10px] font-black uppercase tracking-widest text-neutral-400">Launch Date</th>
                        <th scope="col" class="relative px-6 py-4"><span class="sr-only">Actions</span></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-neutral-100">
                    @forelse($posts as $post)
                    <tr class="hover:bg-neutral-50/30 transition-all">
                        <td class="px-6 py-5">
                            <div class="flex items-center gap-5">
                                <div class="h-16 w-24 flex-shrink-0 relative group">
                                    <img class="h-full w-full rounded-xl shadow-sm object-cover border border-neutral-100 group-hover:opacity-75 transition-opacity" 
                                         src="{{ asset($post->image ?? 'site/img/carousel-1.png') }}" 
                                         onerror="this.src='{{ asset('site/img/carousel-1.png') }}'"
                                         alt="{{ $post->title }}">
                                </div>
                                <div class="space-y-1">
                                    <div class="text-sm font-black text-neutral-900 leading-tight">{{ $post->title }}</div>
                                    <div class="text-[10px] text-neutral-400 font-medium truncate max-w-sm">{{ Str::limit(strip_tags($post->content), 80) }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-5">
                            <form action="{{ route('admin.blog.toggle-status', $post->id) }}" method="POST">
                                @csrf @method('PATCH')
                                <button type="submit" class="group transition-all">
                                    @if($post->status === 'published')
                                        <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-50 text-emerald-700 px-3 py-1 text-[10px] font-black uppercase border border-emerald-100 group-hover:bg-emerald-100">
                                            <span class="h-1.5 w-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                                            Published
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1.5 rounded-full bg-amber-50 text-amber-700 px-3 py-1 text-[10px] font-black uppercase border border-amber-100 group-hover:bg-amber-100">
                                            Draft Mode
                                        </span>
                                    @endif
                                </button>
                            </form>
                        </td>
                        <td class="px-6 py-5">
                            <div class="flex items-center gap-2">
                                <div class="h-7 w-7 rounded-full bg-neutral-100 flex items-center justify-center text-[10px] font-black uppercase text-neutral-500 border border-neutral-200">
                                    {{ substr($post->author ?? 'AD', 0, 2) }}
                                </div>
                                <span class="text-[11px] font-bold text-neutral-700">{{ $post->author ?? 'Administrator' }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-5 text-[11px] font-bold text-neutral-500">
                            {{ $post->created_at->format('M d, Y') }}
                        </td>
                        <td class="px-6 py-5 text-right">
                            <div class="flex justify-end gap-2">
                                <a href="{{ route('admin.blog.edit', $post->id) }}" class="p-2.5 rounded-xl bg-neutral-100 border border-neutral-200 text-neutral-600 hover:text-emerald-600 hover:border-emerald-200 hover:shadow-lg transition-all">
                                    <i class="fa fa-highlighter"></i>
                                </a>
                                <form action="{{ route('admin.blog.destroy', $post->id) }}" method="POST" onsubmit="return confirm('Retract this article and delete permanently?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="p-2.5 rounded-xl bg-neutral-100 border border-neutral-200 text-neutral-600 hover:text-rose-500 hover:border-rose-200 hover:shadow-lg transition-all">
                                        <i class="fa fa-eraser"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="5" class="py-24 text-center text-neutral-400 italic">No articles found matching your criteria.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($posts->hasPages())
            <div class="px-8 py-4 bg-white rounded-2xl border border-neutral-100 shadow-sm">
                {{ $posts->links() }}
            </div>
        @endif
    </div>
</x-layouts::app>
