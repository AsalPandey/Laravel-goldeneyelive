<x-layouts::app :title="__('Course Catalog Management')">
    <div class="flex h-full w-full flex-1 flex-col gap-8 p-8">
        <div class="flex items-center justify-between pb-4 border-b border-neutral-100">
            <div class="space-y-1">
                <h1 class="text-3xl font-black text-neutral-900 tracking-tight uppercase">Course <span class="text-brand-gold">Table</span></h1>
                <p class="text-neutral-500 text-sm">Full administrative control over your academy curriculum.</p>
            </div>
            <div class="flex items-center gap-4">
                <form action="{{ route('admin.courses.index') }}" method="GET" class="relative">
                    <i class="fa fa-search absolute left-4 top-1/2 -translate-y-1/2 text-neutral-400 text-xs"></i>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search courses..." class="pl-10 pr-4 py-2.5 rounded-xl border-neutral-200 bg-white text-xs w-64 focus:border-brand-gold focus:ring-0">
                </form>
                <a href="{{ route('admin.courses.create') }}" class="inline-flex items-center gap-2 rounded-xl bg-brand-gold text-brand-dark px-6 py-3 text-sm font-black uppercase shadow-xl hover:bg-brand-dark hover:text-brand-gold transition-all">
                    <i class="fa fa-plus-circle"></i> Create New Course
                </a>
            </div>
        </div>

        <div class="overflow-x-auto rounded-2xl border border-neutral-100 bg-white shadow-sm">
            <table class="min-w-full divide-y divide-neutral-200">
                <thead class="bg-neutral-50/50">
                    <tr>
                        <th scope="col" class="px-6 py-4 text-left text-[10px] font-black uppercase tracking-widest text-neutral-400">Thumb</th>
                        <th scope="col" class="px-6 py-4 text-left text-[10px] font-black uppercase tracking-widest text-neutral-400">Course Name</th>
                        <th scope="col" class="px-6 py-4 text-left text-[10px] font-black uppercase tracking-widest text-neutral-400">Category</th>
                        <th scope="col" class="px-6 py-4 text-left text-[10px] font-black uppercase tracking-widest text-neutral-400">Controls</th>
                        <th scope="col" class="px-6 py-4 text-left text-[10px] font-black uppercase tracking-widest text-neutral-400">Order</th>
                        <th scope="col" class="px-6 py-4 text-left text-[10px] font-black uppercase tracking-widest text-neutral-400">Investment</th>
                        <th scope="col" class="px-6 py-4 text-left text-[10px] font-black uppercase tracking-widest text-neutral-400">Instructor</th>
                        <th scope="col" class="relative px-6 py-4 tracking-widest text-[10px] font-black uppercase text-neutral-400 text-center">Manage</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-neutral-100">
                    @forelse($courses as $course)
                    <tr class="hover:bg-neutral-50/30 transition-all">
                        <td class="px-6 py-5">
                            <div class="h-14 w-20 flex-shrink-0 bg-neutral-100 rounded-lg overflow-hidden border border-neutral-100 shadow-sm relative group">
                                <img class="h-full w-full object-cover group-hover:scale-110 transition-transform" 
                                     src="{{ asset($course->photo) }}" 
                                     onerror="this.src='{{ asset('site/img/carousel-1.png') }}'"
                                     alt="{{ $course->name }}">
                            </div>
                        </td>
                        <td class="px-6 py-5">
                            <div class="text-sm font-black text-neutral-800 leading-tight">
                                {{ $course->name }}
                                @if($course->is_featured)
                                    <span class="inline-flex h-2 w-2 rounded-full bg-brand-gold ml-1" title="Featured on Homepage"></span>
                                @endif
                            </div>
                            <div class="text-[9px] text-neutral-400 font-mono mt-1 opacity-60">SLUG: {{ $course->slug }}</div>
                        </td>
                        <td class="px-6 py-5">
                            @php
                                $catLower = strtolower($course->category ?? '');
                                $catColor = match(true) {
                                    str_contains($catLower, 'computer') => 'bg-blue-50 text-blue-700 border-blue-100',
                                    str_contains($catLower, 'language') => 'bg-purple-50 text-purple-700 border-purple-100',
                                    default => 'bg-neutral-50 text-neutral-500 border-neutral-200'
                                };
                            @endphp
                            <span class="inline-flex items-center rounded-lg border {{ $catColor }} px-3 py-1 text-[9px] font-black uppercase tracking-tighter">
                                {{ $course->category }}
                            </span>
                        </td>
                        <td class="px-6 py-5">
                            <div class="flex flex-col gap-2">
                                <form action="{{ route('admin.courses.toggle-status', $course->id) }}" method="POST">
                                    @csrf @method('PATCH')
                                    <button type="submit" class="inline-flex items-center gap-1.5 rounded-lg border {{ $course->status === 'active' ? 'bg-green-50 text-green-700 border-green-100 hover:bg-green-100' : 'bg-red-50 text-red-700 border-red-100 hover:bg-red-100' }} px-2.5 py-1 text-[9px] font-black uppercase tracking-tighter transition-all shadow-sm w-24">
                                        <i class="fa {{ $course->status === 'active' ? 'fa-check-circle' : 'fa-times-circle' }}"></i>
                                        {{ $course->status }}
                                    </button>
                                </form>
                                <form action="{{ route('admin.courses.toggle-featured', $course->id) }}" method="POST">
                                    @csrf @method('PATCH')
                                    <button type="submit" class="inline-flex items-center gap-1.5 rounded-lg border {{ $course->is_featured ? 'bg-brand-gold/10 text-brand-gold border-brand-gold/10 hover:bg-brand-gold/20' : 'bg-neutral-50 text-neutral-400 border-neutral-100 hover:bg-neutral-100' }} px-2.5 py-1 text-[9px] font-black uppercase tracking-tighter transition-all shadow-sm w-24">
                                        <i class="fa fa-star"></i>
                                        {{ $course->is_featured ? 'Featured' : 'Regular' }}
                                    </button>
                                </form>
                            </div>
                        </td>
                        <td class="px-6 py-5">
                            <div class="text-sm font-black text-neutral-900">{{ $course->display_order ?? 100 }}</div>
                            <div class="text-[9px] text-neutral-400 uppercase tracking-widest mt-0.5">LOWER FIRST</div>
                        </td>
                        <td class="px-6 py-5">
                            <div class="text-sm font-black text-neutral-900">{{ $course->price }}</div>
                            <div class="text-[9px] text-neutral-400 uppercase tracking-widest mt-0.5">{{ $course->duration }}</div>
                        </td>
                        <td class="px-6 py-5">
                            <div class="text-[11px] font-bold text-neutral-600">{{ $course->instructor }}</div>
                            <div class="text-[9px] text-neutral-400 uppercase tracking-tighter mt-0.5">CAP: {{ $course->capacity }}</div>
                        </td>
                        <td class="px-6 py-5">
                            <div class="flex justify-center gap-2">
                                @if($course->slug)
                                <a href="{{ route('courses-detail', $course->slug) }}" target="_blank" class="p-2.5 rounded-xl bg-neutral-100 border border-neutral-200 text-neutral-600 hover:text-blue-600 hover:border-blue-200 hover:shadow-lg transition-all" title="View on Site">
                                    <i class="fa fa-globe text-xs"></i>
                                </a>
                                @endif
                                <a href="{{ route('admin.courses.edit', $course->id) }}" class="p-2.5 rounded-xl bg-neutral-100 border border-neutral-200 text-neutral-600 hover:text-brand-gold hover:border-brand-gold/20 hover:shadow-lg transition-all" title="Edit Course">
                                    <i class="fa fa-pencil-alt text-xs"></i>
                                </a>
                                <form action="{{ route('admin.courses.destroy', $course->id) }}" method="POST" onsubmit="return confirm('Retract this course from the public curriculum?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="p-2.5 rounded-xl bg-neutral-100 border border-neutral-200 text-neutral-600 hover:text-red-500 hover:border-red-200 hover:shadow-lg transition-all" title="Delete Course">
                                        <i class="fa fa-trash-alt text-xs"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="8" class="py-24 text-center text-neutral-400 font-medium italic">No courses found in the curriculum.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-6">
            {{ $courses->links() }}
        </div>
    </div>
</x-layouts::app>
