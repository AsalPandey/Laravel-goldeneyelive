<x-layouts::app :title="__('Faculty Management')">
    <div class="flex h-full w-full flex-1 flex-col gap-8 p-8">
        <div class="flex items-center justify-between pb-4 border-b border-neutral-100">
            <div class="space-y-1">
                <h1 class="text-3xl font-black text-neutral-900 tracking-tight uppercase">Faculty <span class="text-brand-gold">Hub</span></h1>
                <p class="text-neutral-500 text-sm">Manage academy instructors and staff profiles.</p>
            </div>
            <div class="flex items-center gap-4">
                <form action="{{ route('admin.teachers.index') }}" method="GET" class="relative">
                    <i class="fa fa-search absolute left-4 top-1/2 -translate-y-1/2 text-neutral-400 text-xs"></i>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search faculty..." class="pl-10 pr-4 py-2.5 rounded-xl border-neutral-200 bg-white text-xs w-64 focus:border-brand-gold focus:ring-0">
                </form>
                <a href="{{ route('admin.teachers.create') }}" class="inline-flex items-center gap-2 rounded-xl bg-brand-dark text-brand-gold px-6 py-3 text-sm font-black uppercase shadow-xl hover:bg-brand-gold hover:text-brand-dark transition-all">
                    <i class="fa fa-user-plus"></i> Recruit Faculty
                </a>
            </div>
        </div>

        <div class="overflow-x-auto rounded-2xl border border-neutral-100 bg-white shadow-sm">
            <table class="min-w-full divide-y divide-neutral-200">
                <thead class="bg-neutral-50/50">
                    <tr>
                        <th scope="col" class="px-6 py-4 text-left text-[10px] font-black uppercase tracking-widest text-neutral-400">Instructor Profile</th>
                        <th scope="col" class="px-6 py-4 text-left text-[10px] font-black uppercase tracking-widest text-neutral-400">Department / Role</th>
                        <th scope="col" class="px-6 py-4 text-left text-[10px] font-black uppercase tracking-widest text-neutral-400">Display Controls</th>
                        <th scope="col" class="px-6 py-4 text-left text-[10px] font-black uppercase tracking-widest text-neutral-400">Social Connectivity</th>
                        <th scope="col" class="relative px-6 py-4 text-right pr-12 text-[10px] font-black uppercase tracking-widest text-neutral-400">Manage</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-neutral-100">
                    @forelse($teachers as $teacher)
                    <tr class="hover:bg-neutral-50/30 transition-all">
                        <td class="px-6 py-5">
                            <div class="flex items-center gap-4">
                                <div class="h-14 w-14 flex-shrink-0">
                                    <img class="h-full w-full rounded-2xl shadow-sm object-cover border-2 border-white ring-1 ring-neutral-100" 
                                         src="{{ $teacher->photo ? asset($teacher->photo) : asset('site/img/team-1.jpg') }}" 
                                         onerror="this.src='{{ asset('site/img/team-1.jpg') }}'"
                                         alt="{{ $teacher->name }}">
                                </div>
                                <div class="space-y-0.5">
                                    <div class="text-sm font-black text-neutral-900 flex items-center gap-2">
                                        {{ $teacher->name }}
                                        @if($teacher->is_featured)
                                            <span class="inline-flex h-2 w-2 rounded-full bg-brand-gold" title="Featured on Homepage"></span>
                                        @endif
                                    </div>
                                    <div class="text-[10px] text-neutral-400 uppercase tracking-tighter">{{ Str::limit($teacher->bio, 40) }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-5">
                            <span class="inline-flex items-center rounded-lg bg-brand-gold/10 text-brand-gold px-3 py-1 text-[10px] font-black uppercase border border-brand-gold/20">
                                {{ $teacher->designation ?? 'Faculty' }}
                            </span>
                        </td>
                        <td class="px-6 py-5">
                            <div class="flex flex-col gap-2">
                                <form action="{{ route('admin.teachers.toggle-status', $teacher->id) }}" method="POST">
                                    @csrf @method('PATCH')
                                    <button type="submit" class="inline-flex items-center gap-1.5 rounded-lg border {{ $teacher->status === 'active' ? 'bg-green-50 text-green-700 border-green-100 hover:bg-green-100' : 'bg-red-50 text-red-700 border-red-100 hover:bg-red-100' }} px-2.5 py-1 text-[9px] font-black uppercase tracking-tighter transition-all shadow-sm w-24">
                                        <i class="fa {{ $teacher->status === 'active' ? 'fa-check-circle' : 'fa-times-circle' }}"></i>
                                        {{ $teacher->status }}
                                    </button>
                                </form>
                                <form action="{{ route('admin.teachers.toggle-featured', $teacher->id) }}" method="POST">
                                    @csrf @method('PATCH')
                                    <button type="submit" class="inline-flex items-center gap-1.5 rounded-lg border {{ $teacher->is_featured ? 'bg-brand-gold/10 text-brand-gold border-brand-gold/20 hover:bg-brand-gold/20' : 'bg-neutral-50 text-neutral-400 border-neutral-100 hover:bg-neutral-100' }} px-2.5 py-1 text-[9px] font-black uppercase tracking-tighter transition-all shadow-sm w-24">
                                        <i class="fa fa-star"></i>
                                        {{ $teacher->is_featured ? 'Featured' : 'Regular' }}
                                    </button>
                                </form>
                            </div>
                        </td>
                        <td class="px-6 py-5">
                            <div class="flex gap-2">
                                @if($teacher->facebook_url)
                                    <a href="{{ $teacher->facebook_url }}" target="_blank" class="h-8 w-8 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center hover:bg-blue-600 hover:text-white transition-all shadow-sm">
                                        <i class="fab fa-facebook-f text-xs"></i>
                                    </a>
                                @endif
                                @if($teacher->linkedin_url)
                                    <a href="{{ $teacher->linkedin_url }}" target="_blank" class="h-8 w-8 rounded-lg bg-sky-50 text-sky-600 flex items-center justify-center hover:bg-sky-600 hover:text-white transition-all shadow-sm">
                                        <i class="fab fa-linkedin-in text-xs"></i>
                                    </a>
                                @endif
                                @if(!$teacher->facebook_url && !$teacher->linkedin_url)
                                    <span class="text-[9px] text-neutral-300 font-bold uppercase tracking-widest italic">No Links</span>
                                @endif
                            </div>
                        </td>
                        <td class="px-6 py-5">
                            <div class="flex justify-end gap-2">
                                <a href="{{ route('admin.teachers.edit', $teacher->id) }}" class="p-2.5 rounded-xl bg-neutral-100 border border-neutral-200 text-neutral-600 hover:text-neutral-900 hover:border-neutral-900 hover:shadow-lg transition-all">
                                    <i class="fa fa-pencil-alt text-xs"></i>
                                </a>
                                <form action="{{ route('admin.teachers.destroy', $teacher->id) }}" method="POST" onsubmit="return confirm('Remove this faculty member from the directory?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="p-2.5 rounded-xl bg-neutral-100 border border-neutral-200 text-neutral-600 hover:text-red-500 hover:border-red-200 hover:shadow-lg transition-all">
                                        <i class="fa fa-user-minus text-xs"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="5" class="py-24 text-center text-neutral-400 font-medium italic">No faculty members found in the academy directory.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-6">
            {{ $teachers->links() }}
        </div>
    </div>
</x-layouts::app>
