<x-layouts::app :title="__('Course Category Management')">
    <div class="flex h-full w-full flex-1 flex-col gap-8 p-8">
        <div class="flex items-center justify-between pb-4 border-b border-neutral-100">
            <div class="space-y-1">
                <h1 class="text-3xl font-black text-neutral-900 tracking-tight uppercase">Category <span class="text-brand-gold">Hub</span></h1>
                <p class="text-neutral-500 text-sm">Organize and manage departments within your academy.</p>
            </div>
            <div class="flex items-center gap-4">
                <form action="{{ route('admin.categories.index') }}" method="GET" class="flex items-center gap-2">
                    <div class="relative">
                        <i class="fa fa-search absolute left-4 top-1/2 -translate-y-1/2 text-neutral-400 text-xs"></i>
                        <input type="search" name="search" value="{{ $search ?? request('search') }}" placeholder="Search categories..." class="pl-10 pr-4 py-2.5 rounded-xl border-neutral-200 bg-white text-xs w-64 focus:border-brand-gold focus:ring-0">
                    </div>
                    <button type="submit" class="inline-flex items-center gap-2 rounded-xl bg-neutral-900 px-4 py-2.5 text-xs font-black uppercase text-white hover:bg-brand-gold hover:text-brand-dark transition-all">
                        <i class="fa fa-search"></i> Search
                    </button>
                    @if(request('search'))
                        <a href="{{ route('admin.categories.index') }}" class="inline-flex items-center rounded-xl bg-neutral-100 px-4 py-2.5 text-xs font-black uppercase text-neutral-600 hover:bg-neutral-200 transition-all">Clear</a>
                    @endif
                </form>
                <a href="{{ route('admin.categories.create') }}" class="inline-flex items-center gap-2 rounded-xl bg-brand-gold text-brand-dark px-6 py-3 text-sm font-black uppercase shadow-xl hover:bg-brand-dark hover:text-brand-gold transition-all">
                    <i class="fa fa-plus-circle"></i> Create New Category
                </a>
            </div>
        </div>

        <div class="overflow-x-auto rounded-2xl border border-neutral-100 bg-white shadow-sm">
            <table class="min-w-full divide-y divide-neutral-200">
                <thead class="bg-neutral-50/50">
                    <tr>
                        <th scope="col" class="px-6 py-4 text-left text-[10px] font-black uppercase tracking-widest text-neutral-400">Thumb</th>
                        <th scope="col" class="px-6 py-4 text-left text-[10px] font-black uppercase tracking-widest text-neutral-400">Category Name</th>
                        <th scope="col" class="px-6 py-4 text-left text-[10px] font-black uppercase tracking-widest text-neutral-400">Slug</th>
                        <th scope="col" class="px-6 py-4 text-left text-[10px] font-black uppercase tracking-widest text-neutral-400">Description</th>
                        <th scope="col" class="px-6 py-4 text-center text-[10px] font-black uppercase tracking-widest text-neutral-400">Course Count</th>
                        <th scope="col" class="px-6 py-4 text-center text-[10px] font-black uppercase tracking-widest text-neutral-400">Order</th>
                        <th scope="col" class="px-6 py-4 text-center text-[10px] font-black uppercase tracking-widest text-neutral-400">Status</th>
                        <th scope="col" class="relative px-6 py-4 tracking-widest text-[10px] font-black uppercase text-neutral-400 text-center">Manage</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-neutral-100">
                    @forelse($categories as $category)
                    <tr class="hover:bg-neutral-50/30 transition-all">
                        <td class="px-6 py-5">
                            <div class="h-12 w-16 bg-neutral-100 rounded-lg overflow-hidden border border-neutral-100 shadow-sm relative group">
                                <img class="h-full w-full object-cover group-hover:scale-110 transition-transform" 
                                     src="{{ asset($category->image ?? 'site/img/cat-1.jpg') }}" 
                                     onerror="this.src='{{ asset('site/img/cat-1.jpg') }}'"
                                     alt="{{ $category->name }}">
                            </div>
                        </td>
                        <td class="px-6 py-5">
                            <div class="text-sm font-black text-neutral-800 leading-tight">
                                {{ $category->name }}
                            </div>
                        </td>
                        <td class="px-6 py-5 font-mono text-[10px] text-neutral-400 uppercase tracking-tighter">
                            {{ $category->slug }}
                        </td>
                        <td class="px-6 py-5">
                            <div class="text-[11px] text-neutral-500 italic max-w-xs truncate">
                                {{ $category->description ?? 'No description provided.' }}
                            </div>
                        </td>
                        <td class="px-6 py-5 text-center">
                            <div class="flex flex-col items-center gap-1">
                                <span class="inline-flex items-center rounded-full bg-brand-gold/10 px-3 py-0.5 text-[9px] font-black text-brand-gold border border-brand-gold/10 shadow-sm">
                                    {{ $category->active_courses_count }} Active
                                </span>
                                @if($category->total_courses_count > $category->active_courses_count)
                                    <span class="text-[8px] font-bold text-neutral-400 uppercase tracking-tight">
                                        {{ $category->total_courses_count }} Total
                                    </span>
                                @endif
                            </div>
                        </td>
                        <td class="px-6 py-5 text-center font-mono text-xs text-neutral-500">
                            {{ $category->order_priority }}
                        </td>
                        <td class="px-6 py-5 text-center">
                            <form action="{{ route('admin.categories.toggle-status', $category->id) }}" method="POST">
                                @csrf @method('PATCH')
                                <button type="submit" class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-1 text-[10px] font-black tracking-tight uppercase transition-all shadow-sm border {{ $category->status === 'active' ? 'bg-emerald-50 text-emerald-700 border-emerald-100 hover:bg-emerald-100' : 'bg-rose-50 text-rose-700 border-rose-100 hover:bg-rose-100' }}">
                                    <div class="h-1.5 w-1.5 rounded-circle {{ $category->status === 'active' ? 'bg-emerald-500' : 'bg-rose-500 animate-pulse' }}"></div>
                                    {{ $category->status }}
                                </button>
                            </form>
                        </td>
                        <td class="px-6 py-5">
                            <div class="flex justify-center gap-2">
                                <a href="{{ route('admin.categories.edit', $category->id) }}" class="p-2.5 rounded-xl bg-neutral-100 border border-neutral-200 text-neutral-600 hover:text-orange-600 hover:border-orange-200 hover:shadow-lg transition-all" title="Edit Category" aria-label="Edit {{ $category->name }}">
                                    <i class="fa fa-pencil-alt text-xs"></i>
                                </a>
                                <form action="{{ route('admin.categories.destroy', $category->id) }}" method="POST" onsubmit="return confirm('Delete this category? This cannot be undone.')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="p-2.5 rounded-xl bg-neutral-100 border border-neutral-200 text-neutral-600 hover:text-red-500 hover:border-red-200 hover:shadow-lg transition-all {{ $category->total_courses_count > 0 ? 'opacity-30 cursor-not-allowed' : '' }}" title="Delete Category" aria-label="Delete {{ $category->name }}" {{ $category->total_courses_count > 0 ? 'disabled' : '' }}>
                                        <i class="fa fa-trash-alt text-xs"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="8" class="py-24 text-center text-neutral-400 font-medium italic">No departments have been established yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-6">
            {{ $categories->links() }}
        </div>
    </div>
</x-layouts::app>
