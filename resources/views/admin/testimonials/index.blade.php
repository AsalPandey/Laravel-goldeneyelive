<x-layouts::app :title="__('Student Voice Management')">
    <div class="flex h-full w-full flex-1 flex-col gap-8 p-8">
        <div class="flex items-center justify-between pb-4 border-b border-neutral-100">
            <div class="space-y-1">
                <h1 class="text-3xl font-black text-neutral-900 tracking-tight uppercase">Student <span class="text-[#C5A059]">Voices</span></h1>
                <p class="text-neutral-500 text-sm">Manage academy testimonials and social proof.</p>
            </div>
            <div class="flex items-center gap-4">
                <form action="{{ route('admin.testimonials.index') }}" method="GET" class="relative">
                    <i class="fa fa-search absolute left-4 top-1/2 -translate-y-1/2 text-neutral-400 text-xs"></i>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search testimonials..." class="pl-10 pr-4 py-2.5 rounded-xl border-neutral-200 bg-white text-xs w-64 focus:border-[#C5A059] focus:ring-0">
                </form>
                <a href="{{ route('admin.testimonials.create') }}" class="inline-flex items-center gap-2 rounded-xl bg-neutral-900 text-[#C5A059] px-6 py-3 text-sm font-black uppercase shadow-xl hover:bg-[#C5A059] hover:text-white transition-all">
                    <i class="fa fa-quote-left"></i> Publish Testimony
                </a>
            </div>
        </div>

        <div class="overflow-x-auto rounded-2xl border border-neutral-100 bg-white shadow-sm">
            <table class="min-w-full divide-y divide-neutral-200">
                <thead class="bg-neutral-50/50">
                    <tr>
                        <th scope="col" class="px-6 py-4 text-left text-[10px] font-black uppercase tracking-widest text-neutral-400">Student Profile</th>
                        <th scope="col" class="px-6 py-4 text-left text-[10px] font-black uppercase tracking-widest text-neutral-400">Course / Alumnus Of</th>
                        <th scope="col" class="px-6 py-4 text-left text-[10px] font-black uppercase tracking-widest text-neutral-400">Controls</th>
                        <th scope="col" class="px-6 py-4 text-left text-[10px] font-black uppercase tracking-widest text-neutral-400">Snippet</th>
                        <th scope="col" class="relative px-6 py-4 text-right pr-12 text-[10px] font-black uppercase tracking-widest text-neutral-400">Manage</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-neutral-100">
                    @forelse($testimonials as $testimonial)
                    <tr class="hover:bg-neutral-50/30 transition-all">
                        <td class="px-6 py-5">
                            <div class="flex items-center gap-4">
                                <div class="h-12 w-12 flex-shrink-0">
                                    <img class="h-full w-full rounded-full shadow-sm object-cover border-2 border-white ring-1 ring-neutral-100" 
                                         src="{{ $testimonial->photo ? asset($testimonial->photo) : asset('site/img/testimonial-1.jpg') }}" 
                                         onerror="this.src='{{ asset('site/img/testimonial-1.jpg') }}'"
                                         alt="{{ $testimonial->student_name }}">
                                </div>
                                <div class="space-y-0.5">
                                    <div class="text-sm font-black text-neutral-900 flex items-center gap-2">
                                        {{ $testimonial->student_name }}
                                        @if($testimonial->is_featured)
                                            <span class="inline-flex h-2 w-2 rounded-full bg-[#C5A059]" title="Featured on Homepage"></span>
                                        @endif
                                    </div>
                                    <div class="flex items-center gap-0.5">
                                        @for($i = 0; $i < 5; $i++)
                                            <i class="fa fa-star text-[8px] {{ $i < $testimonial->rating ? 'text-[#C5A059]' : 'text-neutral-200' }}"></i>
                                        @endfor
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-5">
                            <span class="inline-flex items-center rounded-lg bg-indigo-50 text-indigo-700 px-3 py-1 text-[10px] font-black uppercase border border-indigo-100">
                                {{ $testimonial->course_name ?? 'Academy Student' }}
                            </span>
                        </td>
                        <td class="px-6 py-5">
                            <div class="flex flex-col gap-2">
                                <form action="{{ route('admin.testimonials.toggle-status', $testimonial->id) }}" method="POST">
                                    @csrf @method('PATCH')
                                    <button type="submit" class="inline-flex items-center gap-1.5 rounded-lg border {{ $testimonial->status === 'active' ? 'bg-green-50 text-green-700 border-green-100 hover:bg-green-100' : 'bg-red-50 text-red-700 border-red-100 hover:bg-red-100' }} px-2.5 py-1 text-[9px] font-black uppercase tracking-tighter transition-all shadow-sm w-24">
                                        <i class="fa {{ $testimonial->status === 'active' ? 'fa-check-circle' : 'fa-times-circle' }}"></i>
                                        {{ $testimonial->status }}
                                    </button>
                                </form>
                                <form action="{{ route('admin.testimonials.toggle-featured', $testimonial->id) }}" method="POST">
                                    @csrf @method('PATCH')
                                    <button type="submit" class="inline-flex items-center gap-1.5 rounded-lg border {{ $testimonial->is_featured ? 'bg-[#C5A059]/10 text-[#A6803B] border-[#C5A059]/20 hover:bg-[#C5A059]/20' : 'bg-neutral-50 text-neutral-400 border-neutral-100 hover:bg-neutral-100' }} px-2.5 py-1 text-[9px] font-black uppercase tracking-tighter transition-all shadow-sm w-24">
                                        <i class="fa fa-star"></i>
                                        {{ $testimonial->is_featured ? 'Featured' : 'Regular' }}
                                    </button>
                                </form>
                            </div>
                        </td>
                        <td class="px-6 py-5">
                           <p class="text-[11px] text-neutral-500 italic max-w-xs truncate leading-relaxed">"{{ Str::limit($testimonial->content, 60) }}"</p>
                        </td>
                        <td class="px-6 py-5">
                            <div class="flex justify-end gap-2">
                                <a href="{{ route('admin.testimonials.edit', $testimonial->id) }}" class="p-2.5 rounded-xl bg-neutral-100 border border-neutral-200 text-neutral-600 hover:text-neutral-900 hover:border-neutral-900 hover:shadow-lg transition-all">
                                    <i class="fa fa-pencil-alt text-xs"></i>
                                </a>
                                <form action="{{ route('admin.testimonials.destroy', $testimonial->id) }}" method="POST" onsubmit="return confirm('Remove this testimonial from the public site?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="p-2.5 rounded-xl bg-neutral-100 border border-neutral-200 text-neutral-600 hover:text-red-500 hover:border-red-200 hover:shadow-lg transition-all">
                                        <i class="fa fa-trash-alt text-xs"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="5" class="py-24 text-center text-neutral-400 font-medium italic">No testimonials found in the directory.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-6">
            {{ $testimonials->links() }}
        </div>
    </div>
</x-layouts::app>
