<x-layouts::app :title="__('Dashboard')">
    <div class="flex h-full w-full flex-1 flex-col gap-8 p-8 bg-zinc-50/50 dark:bg-zinc-950/50">
        {{-- Hero Header --}}
        <div class="relative overflow-hidden rounded-[2.5rem] bg-brand-dark p-10 shadow-2xl border border-brand-gold/10 group">
            <div class="absolute -top-24 -right-24 w-64 h-64 bg-brand-gold/10 rounded-full blur-[100px] animate-pulse"></div>
            <div class="absolute bottom-0 left-0 w-full h-1 bg-linear-to-r from-transparent via-brand-gold/50 to-transparent"></div>
            
            <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
                <div>
                    <div class="flex items-center gap-3 mb-4">
                        <div class="h-1px w-12 bg-brand-gold"></div>
                        <span class="text-[10px] font-black uppercase tracking-[0.3em] text-brand-gold">{{ $settings['site_name'] ?? 'GoldenEye' }} Academy CMS</span>
                    </div>
                    <h1 class="text-5xl font-heading font-black tracking-tighter text-white uppercase mb-2">
                        Command <span class="text-brand-gold">Center</span>
                    </h1>
                    <p class="text-zinc-400 font-medium italic text-sm">Welcome back, {{ auth()->user()->name }}. Operational overview for {{ now()->format('l, F d, Y') }}</p>
                </div>
                <div class="flex gap-3">
                    <a href="{{ route('home') }}" target="_blank" class="px-6 py-3 bg-white/5 hover:bg-white/10 border border-white/10 rounded-2xl text-xs font-black uppercase text-white tracking-widest transition-all flex items-center gap-2 backdrop-blur-md">
                        <i class="fa fa-external-link-alt text-[10px] text-brand-gold"></i> Live Site
                    </a>
                    @role('Admin')
                    <a href="{{ route('admin.branding.index') }}" class="px-6 py-3 bg-brand-gold text-brand-dark rounded-2xl text-xs font-black uppercase tracking-widest transition-all shadow-lg hover:scale-105 active:scale-95 flex items-center gap-2">
                        <i class="fa fa-palette text-[10px]"></i> Brand Hub
                    </a>
                    @endrole
                </div>
            </div>
        </div>

        @role('Admin|Staff')
        <!-- Statistics Grid -->
        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4">
            {{-- Courses --}}
            <div class="group relative overflow-hidden rounded-3xl bg-white p-8 shadow-sm border border-zinc-100 dark:bg-zinc-900 dark:border-zinc-800 hover:shadow-2xl hover:border-brand-gold/20 transition-all duration-500">
                <div class="absolute top-0 right-0 p-4 opacity-5 group-hover:opacity-10 transition-opacity">
                    <i class="fas fa-graduation-cap text-6xl text-brand-dark dark:text-white"></i>
                </div>
                <div class="flex flex-col gap-4">
                    <div class="w-12 h-12 rounded-2xl bg-brand-gold/10 flex items-center justify-center text-brand-gold shadow-inner">
                        <i class="fas fa-graduation-cap text-xl"></i>
                    </div>
                    <div>
                        <p class="text-[10px] font-black uppercase tracking-widest text-zinc-400 mb-1">Total Courses</p>
                        <h3 class="text-3xl font-heading font-black text-brand-dark dark:text-white">{{ $stats['courses'] }}</h3>
                    </div>
                </div>
            </div>

            {{-- Enrollments --}}
            <div class="group relative overflow-hidden rounded-3xl bg-white p-8 shadow-sm border border-zinc-100 dark:bg-zinc-900 dark:border-zinc-800 hover:shadow-2xl hover:border-brand-gold/20 transition-all duration-500">
                <div class="absolute top-0 right-0 p-4 opacity-5 group-hover:opacity-10 transition-opacity">
                    <i class="fas fa-user-plus text-6xl text-brand-dark dark:text-white"></i>
                </div>
                <div class="flex flex-col gap-4">
                    <div class="w-12 h-12 rounded-2xl bg-emerald-500/10 flex items-center justify-center text-emerald-600 shadow-inner relative">
                        <i class="fas fa-user-plus text-xl"></i>
                        <span class="absolute top-0 right-0 flex h-3 w-3 -mt-1 -mr-1">
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-3 w-3 bg-emerald-500"></span>
                        </span>
                    </div>
                    <div>
                        <p class="text-[10px] font-black uppercase tracking-widest text-zinc-400 mb-1">New Enrollments</p>
                        <h3 class="text-3xl font-heading font-black text-brand-dark dark:text-white">{{ $stats['enrollments'] }}</h3>
                    </div>
                </div>
            </div>

            {{-- Messages --}}
            <div class="group relative overflow-hidden rounded-3xl bg-white p-8 shadow-sm border border-zinc-100 dark:bg-zinc-900 dark:border-zinc-800 hover:shadow-2xl hover:border-brand-gold/20 transition-all duration-500">
                <div class="absolute top-0 right-0 p-4 opacity-5 group-hover:opacity-10 transition-opacity">
                    <i class="fas fa-envelope text-6xl text-brand-dark dark:text-white"></i>
                </div>
                <div class="flex flex-col gap-4">
                    <div class="w-12 h-12 rounded-2xl bg-blue-500/10 flex items-center justify-center text-blue-600 shadow-inner">
                        <i class="fas fa-envelope text-xl"></i>
                    </div>
                    <div>
                        <p class="text-[10px] font-black uppercase tracking-widest text-zinc-400 mb-1">Contact Queries</p>
                        <h3 class="text-3xl font-heading font-black text-brand-dark dark:text-white">{{ $stats['contacts'] }}</h3>
                    </div>
                </div>
            </div>

            {{-- Blog Posts --}}
            <div class="group relative overflow-hidden rounded-3xl bg-white p-8 shadow-sm border border-zinc-100 dark:bg-zinc-900 dark:border-zinc-800 hover:shadow-2xl hover:border-brand-gold/20 transition-all duration-500">
                <div class="absolute top-0 right-0 p-4 opacity-5 group-hover:opacity-10 transition-opacity">
                    <i class="fas fa-newspaper text-6xl text-brand-dark dark:text-white"></i>
                </div>
                <div class="flex flex-col gap-4">
                    <div class="w-12 h-12 rounded-2xl bg-brand-dark/10 dark:bg-white/10 flex items-center justify-center text-brand-dark dark:text-white shadow-inner">
                        <i class="fas fa-newspaper text-xl"></i>
                    </div>
                    <div>
                        <p class="text-[10px] font-black uppercase tracking-widest text-zinc-400 mb-1">Published Articles</p>
                        <h3 class="text-3xl font-heading font-black text-brand-dark dark:text-white">{{ $stats['blog_posts'] }}</h3>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Activity Panels -->
        <div class="grid grid-cols-1 gap-8 lg:grid-cols-2">
            {{-- Inquiries Feed --}}
            <div class="rounded-[2.5rem] bg-white shadow-2xl border border-zinc-100 dark:bg-zinc-900 dark:border-zinc-800 overflow-hidden flex flex-col h-full">
                <div class="px-8 py-6 border-b border-zinc-50 dark:border-zinc-800 flex justify-between items-center bg-zinc-50/50 dark:bg-zinc-900/50">
                    <div>
                        <h2 class="text-sm font-black uppercase tracking-widest text-brand-dark dark:text-white">Active Inquiries</h2>
                        <p class="text-[10px] text-zinc-400 font-bold mt-1 uppercase tracking-tighter italic">Latest messages from the public site</p>
                    </div>
                    <a href="{{ route('admin.submissions.contact-display') }}" class="w-10 h-10 rounded-xl bg-white dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 flex items-center justify-center text-brand-gold hover:bg-brand-gold hover:text-brand-dark transition-all">
                        <i class="fa fa-arrow-right text-xs"></i>
                    </a>
                </div>
                <div class="p-4 flex-1">
                    <div class="space-y-2">
                        @forelse($recentContacts as $contact)
                        <div class="group p-4 rounded-3xl hover:bg-zinc-50 dark:hover:bg-zinc-800 transition-all border border-transparent hover:border-zinc-100 dark:hover:border-zinc-700">
                            <div class="flex justify-between items-start mb-2">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-full bg-zinc-100 dark:bg-zinc-700 flex items-center justify-center text-[10px] font-black text-zinc-500">
                                        {{ strtoupper(substr($contact->name, 0, 1)) }}
                                    </div>
                                    <span class="text-sm font-black text-brand-dark dark:text-white">{{ $contact->name }}</span>
                                </div>
                                <span class="text-[9px] font-bold text-zinc-400 uppercase">{{ $contact->created_at->diffForHumans() }}</span>
                            </div>
                            <p class="text-xs text-zinc-500 dark:text-zinc-400 line-clamp-1 italic ml-11">"{{ $contact->subject }}"</p>
                        </div>
                        @empty
                        <div class="py-20 text-center flex flex-col items-center gap-4">
                            <i class="fa fa-inbox text-4xl text-zinc-200"></i>
                            <p class="text-sm text-zinc-400 font-medium italic">All caught up! No recent messages.</p>
                        </div>
                        @endforelse
                    </div>
                </div>
            </div>

            {{-- Enrollments Feed --}}
            <div class="rounded-[2.5rem] bg-white shadow-2xl border border-zinc-100 dark:bg-zinc-900 dark:border-zinc-800 overflow-hidden flex flex-col h-full">
                <div class="px-8 py-6 border-b border-zinc-50 dark:border-zinc-800 flex justify-between items-center bg-zinc-50/50 dark:bg-zinc-900/50">
                    <div>
                        <h2 class="text-sm font-black uppercase tracking-widest text-brand-dark dark:text-white">Recent Enrollments</h2>
                        <p class="text-[10px] text-zinc-400 font-bold mt-1 uppercase tracking-tighter italic">Students queuing for expert guidance</p>
                    </div>
                    <a href="{{ route('admin.submissions.join_now-display') }}" class="w-10 h-10 rounded-xl bg-white dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 flex items-center justify-center text-brand-gold hover:bg-brand-gold hover:text-brand-dark transition-all">
                        <i class="fa fa-arrow-right text-xs"></i>
                    </a>
                </div>
                <div class="p-4 flex-1">
                    <div class="space-y-2">
                        @forelse($recentEnrollments as $enroll)
                        <div class="group p-4 rounded-3xl hover:bg-zinc-50 dark:hover:bg-zinc-800 transition-all border border-transparent hover:border-zinc-100 dark:hover:border-zinc-700 flex items-center justify-between">
                            <div class="flex items-center gap-4">
                                <div class="w-10 h-10 rounded-2xl bg-brand-gold/10 flex items-center justify-center text-brand-gold text-xs font-black">
                                    <i class="fa fa-user-graduate"></i>
                                </div>
                                <div class="flex flex-col">
                                    <span class="text-sm font-black text-brand-dark dark:text-white">{{ $enroll->firstName }} {{ $enroll->lastName }}</span>
                                    @if($enroll->course_slug)
                                        <span class="text-[9px] font-black text-brand-gold uppercase tracking-widest">{{ $enroll->course }}</span>
                                    @else
                                        <span class="text-[9px] font-bold text-zinc-400 uppercase tracking-widest">General Enrollment</span>
                                    @endif
                                </div>
                            </div>
                            <span class="text-[9px] font-bold text-zinc-400 uppercase">{{ $enroll->created_at->diffForHumans() }}</span>
                        </div>
                        @empty
                        <div class="py-20 text-center flex flex-col items-center gap-4">
                            <i class="fa fa-user-clock text-4xl text-zinc-200"></i>
                            <p class="text-sm text-zinc-400 font-medium italic">Waiting for new aspirants to join.</p>
                        </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
        @else
        {{-- Premium Student Dashboard --}}
        <div class="relative overflow-hidden rounded-[3rem] bg-brand-dark p-16 text-center shadow-2xl border border-brand-gold/20 group">
            <div class="absolute inset-0 bg-linear-to-tr from-brand-gold/5 to-transparent"></div>
            <div class="relative z-10">
                <div class="w-24 h-24 mx-auto mb-8 rounded-3xl bg-brand-gold/10 border border-brand-gold/30 flex items-center justify-center text-brand-gold animate-bounce-subtle">
                    <i class="fas fa-graduation-cap text-4xl"></i>
                </div>
                <h2 class="text-4xl font-heading font-black text-white uppercase mb-4 tracking-tighter">Your Future <span class="text-brand-gold">Starts Here</span></h2>
                <p class="max-w-md mx-auto text-zinc-400 font-medium mb-10 leading-relaxed italic">Welcome to the GoldenEye Student Portal. Your educational journey is being prepared. Soon you'll access courses, assignments, and grades right here.</p>
                <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
                    <a href="{{ route('profile.edit') }}" class="px-8 py-4 bg-brand-gold text-brand-dark rounded-2xl text-xs font-black uppercase tracking-[0.2em] transition-all shadow-xl hover:scale-105 active:scale-95">
                        Complete Profile
                    </a>
                    <a href="{{ route('courses-all') }}" class="px-8 py-4 bg-white/5 border border-white/10 text-white rounded-2xl text-xs font-black uppercase tracking-[0.2em] transition-all hover:bg-white/10">
                        Explore Courses
                    </a>
                </div>
            </div>
        </div>
        @endrole
    </div>

    <style>
        @keyframes bounce-subtle {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-10px); }
        }
        .animate-bounce-subtle {
            animation: bounce-subtle 4s infinite ease-in-out;
        }
    </style>
</x-layouts::app>
