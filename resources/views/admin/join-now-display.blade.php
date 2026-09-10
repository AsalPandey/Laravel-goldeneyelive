<x-layouts::app :title="__('Enrollment Queries')">
    <div id="joinNowDisplayPage" class="flex h-full w-full flex-1 flex-col gap-6 p-6">
        <div class="flex flex-wrap items-center justify-between gap-4 pb-4 border-b border-neutral-100">
            <div class="space-y-1">
                <h1 class="text-3xl font-black text-neutral-900 tracking-tight uppercase">Enrollment <span class="text-brand-gold">Queries</span></h1>
                <p class="text-neutral-500 text-sm">Review and manage student course enrollment requests.</p>
            </div>
            <div class="flex items-center gap-4">
                <form action="{{ route('admin.submissions.join_now-display') }}" method="GET" class="flex flex-wrap items-center gap-2">
                    <input type="hidden" name="view" value="{{ $showArchived ? 'archived' : 'active' }}">
                    <div class="relative">
                        <i class="fa fa-search absolute left-4 top-1/2 -translate-y-1/2 text-neutral-400 text-xs"></i>
                        <input type="search" name="search" value="{{ request('search') }}" placeholder="Search name, phone, course, source..." class="pl-10 pr-4 py-2.5 rounded-xl border-neutral-200 bg-white text-xs w-72 focus:border-brand-gold focus:ring-0">
                    </div>
                    <select name="status" class="rounded-xl border-neutral-200 bg-white px-3 py-2.5 text-xs focus:border-brand-gold focus:ring-0">
                        <option value="">All statuses</option>
                        @foreach($statusOptions as $value => $label)
                            <option value="{{ $value }}" @selected(request('status') === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                    <button type="submit" class="inline-flex items-center gap-2 rounded-xl bg-neutral-900 px-4 py-2.5 text-xs font-black uppercase text-white hover:bg-brand-gold hover:text-brand-dark transition-all">
                        <i class="fa fa-search"></i> Search
                    </button>
                    @if(request('search') || request('status'))
                        <a href="{{ route('admin.submissions.join_now-display', $showArchived ? ['view' => 'archived'] : []) }}" class="inline-flex items-center rounded-xl bg-neutral-100 px-4 py-2.5 text-xs font-black uppercase text-neutral-600 hover:bg-neutral-200 transition-all">Clear</a>
                    @endif
                </form>
            </div>
        </div>

        <div class="flex gap-2">
            <a href="{{ route('admin.submissions.join_now-display') }}" class="rounded-xl px-4 py-2 text-xs font-black uppercase {{ $showArchived ? 'bg-neutral-100 text-neutral-600' : 'bg-neutral-900 text-white' }}">Active</a>
            <a href="{{ route('admin.submissions.join_now-display', ['view' => 'archived']) }}" class="rounded-xl px-4 py-2 text-xs font-black uppercase {{ $showArchived ? 'bg-neutral-900 text-white' : 'bg-neutral-100 text-neutral-600' }}">Archived</a>
        </div>

        <div class="overflow-x-auto rounded-2xl border border-neutral-100 bg-white shadow-sm">
            <table class="min-w-full divide-y divide-neutral-200">
                <thead class="bg-neutral-50/50">
                    <tr>
                        <th scope="col" class="px-6 py-4 text-left">
                            @if(! $showArchived && auth()->user()->hasRole('Admin'))
                                <input type="checkbox" id="selectAll" class="rounded border-neutral-300 text-brand-gold focus:ring-brand-gold">
                            @endif
                        </th>
                        <th scope="col" class="px-6 py-4 text-left text-[10px] font-black uppercase tracking-widest text-neutral-400">Student Profile</th>
                        <th scope="col" class="px-6 py-4 text-left text-[10px] font-black uppercase tracking-widest text-neutral-400">Target Course</th>
                        <th scope="col" class="px-6 py-4 text-left text-[10px] font-black uppercase tracking-widest text-neutral-400">Admin Audit</th>
                        <th scope="col" class="px-6 py-4 text-left text-[10px] font-black uppercase tracking-widest text-neutral-400">Current Status</th>
                        <th scope="col" class="relative px-6 py-4 tracking-widest text-[10px] font-black uppercase text-neutral-400 text-right pr-12">Manage</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-neutral-100">
                    @forelse($joinNowQueries as $data)
                    <tr class="hover:bg-neutral-50/30 transition-all select-row">
                        <td class="px-6 py-5">
                            @if(! $showArchived && auth()->user()->hasRole('Admin'))
                                <input type="checkbox" name="ids[]" value="{{ $data->id }}" class="row-checkbox rounded border-neutral-300 text-brand-gold focus:ring-brand-gold">
                            @endif
                        </td>
                        <td class="px-6 py-5">
                            <div class="text-sm font-black text-neutral-900 leading-tight">{{ $data->firstName }} {{ $data->lastName }}</div>
                            <div class="text-[10px] text-neutral-400 font-bold uppercase mt-0.5">{{ $data->email ?: 'No email yet' }} &bull; {{ $data->phone }}</div>
                            <div class="flex items-center gap-2 mt-1">
                                @if($data->address)
                                    <div class="text-[9px] text-neutral-400 italic opacity-60">From {{ $data->address }}</div>
                                @endif
                                @if($data->contactMethod)
                                    <span class="text-[8px] bg-neutral-100 text-neutral-600 px-1.5 py-0.5 rounded-md font-black uppercase">{{ $data->contactMethod }}</span>
                                @endif
                                <span class="text-[9px] text-neutral-400 italic opacity-60">&bull; {{ $data->created_at->format('M d, Y g:i A') }} &bull; {{ $data->created_at->diffForHumans() }}</span>
                            </div>
                            @if($data->lead_source || $data->cta_id)
                                <div class="flex flex-wrap items-center gap-1.5 mt-2">
                                    @if($data->lead_status)
                                        @php
                                            $leadStatusLabel = str_replace(' Lead', '', (string) $data->lead_status);
                                        @endphp
                                        <span class="text-[8px] px-1.5 py-0.5 rounded-md font-black uppercase border
                                            {{ $leadStatusLabel === 'Hot' ? 'bg-rose-50 text-rose-700 border-rose-100' : '' }}
                                            {{ $leadStatusLabel === 'Warm' ? 'bg-amber-50 text-amber-700 border-amber-100' : '' }}
                                            {{ $leadStatusLabel === 'Basic' ? 'bg-neutral-50 text-neutral-600 border-neutral-100' : '' }}">
                                            {{ $leadStatusLabel }}: {{ $data->lead_score ?? 0 }}
                                        </span>
                                    @endif
                                    @if($data->help_topic)
                                        <span class="text-[8px] bg-emerald-50 text-emerald-700 border border-emerald-100 px-1.5 py-0.5 rounded-md font-black uppercase">Help: {{ $data->help_topic }}</span>
                                    @endif
                                    @if($data->lead_source)
                                        <span class="text-[8px] bg-blue-50 text-blue-700 border border-blue-100 px-1.5 py-0.5 rounded-md font-black uppercase">Source: {{ $data->lead_source }}</span>
                                    @endif
                                    @if($data->cta_id)
                                        <span class="text-[8px] bg-amber-50 text-amber-700 border border-amber-100 px-1.5 py-0.5 rounded-md font-black uppercase">CTA: {{ $data->cta_id }}</span>
                                    @endif
                                </div>
                            @endif
                        </td>
                        <td class="px-6 py-5">
                            @if($data->course_slug)
                                <a href="{{ route('courses-detail', $data->course_slug) }}" target="_blank" class="inline-flex items-center rounded-lg bg-brand-gold/10 px-3 py-1.5 text-[9px] font-black uppercase text-brand-gold border border-brand-gold/10 shadow-sm hover:bg-brand-gold hover:text-brand-dark transition-all">
                                    <i class="fa fa-graduation-cap mr-1.5"></i> {{ $data->course }}
                                </a>
                            @else
                                <span class="inline-flex items-center rounded-lg bg-neutral-50 px-3 py-1.5 text-[9px] font-black uppercase text-neutral-500 border border-neutral-100">
                                    <i class="fa fa-history mr-1.5"></i> {{ $data->course }}
                                </span>
                            @endif
                            @if($data->selected_course)
                                <div class="mt-2 text-[9px] text-neutral-400 font-black uppercase">Selected: {{ $data->selected_course }}</div>
                            @endif
                            @if($data->preferred_batch_time)
                                <div class="mt-1 text-[9px] text-neutral-400 font-black uppercase">Batch: {{ $data->preferred_batch_time }}</div>
                            @endif
                            <div class="mt-2 max-w-md whitespace-pre-wrap text-[10px] italic text-neutral-500">{{ $data->queries ?: 'No additional message provided.' }}</div>
                        </td>
                        <td class="px-6 py-5">
                            @if($data->admin_notes)
                                <div class="text-[10px] text-neutral-600 font-medium line-clamp-2 italic">"{{ $data->admin_notes }}"</div>
                            @else
                                <div class="text-[9px] text-neutral-300 uppercase tracking-widest italic font-bold">No Audit Log</div>
                            @endif
                            @if($data->followed_up_at)
                                <div class="text-[8px] text-emerald-500 font-black uppercase mt-1">First follow-up {{ $data->followed_up_at->diffForHumans() }}</div>
                            @endif
                        </td>
                        <td class="px-6 py-5">
                            <span class="inline-flex items-center rounded-lg px-2.5 py-1 text-[10px] font-black uppercase tracking-tighter border
                                {{ $data->status == 'new' ? 'text-blue-700 border-blue-100 bg-blue-50' : '' }}
                                {{ $data->status == 'contacted' ? 'text-brand-gold border-brand-gold/10 bg-brand-gold/10' : '' }}
                                {{ $data->status == 'enrolled' ? 'text-emerald-700 border-emerald-100 bg-emerald-50' : '' }}
                                {{ $data->status == 'resolved' ? 'text-emerald-700 border-emerald-100 bg-emerald-50' : '' }}
                                {{ $data->status == 'invalid' ? 'text-rose-700 border-rose-100 bg-rose-50' : '' }}
                                {{ $data->status == 'rejected' ? 'text-rose-700 border-rose-100 bg-rose-50' : '' }}
                                {{ $data->status == 'reviewed' ? 'text-neutral-700 border-neutral-100 bg-neutral-50' : '' }}
                            ">
                                {{ $statusOptions[$data->status] ?? ucfirst($data->status) }}
                            </span>
                            @if($showArchived)
                                <span class="mt-1 block text-[9px] font-black uppercase text-neutral-500">Archived {{ $data->deleted_at?->format('M d, Y g:i A') }}</span>
                            @endif
                        </td>
                        <td class="px-6 py-5 text-right">
                            <div class="flex justify-end gap-2 pr-4">
                                <button onclick="openEnrollmentModal({{ json_encode($data) }})" class="p-2.5 rounded-xl bg-neutral-100 border border-neutral-200 text-neutral-600 hover:text-brand-gold hover:border-brand-gold/20 hover:shadow-lg transition-all">
                                    <i class="fa fa-eye text-xs"></i>
                                </button>
                                @if($showArchived)
                                    @role('Admin')
                                    <form action="{{ route('admin.submissions.join_now.restore', $data->id) }}" method="POST" onsubmit="return confirm('Restore this course inquiry to the active list?')">
                                        @csrf @method('PATCH')
                                        <button type="submit" title="Restore course inquiry" class="p-2.5 rounded-xl bg-emerald-50 border border-emerald-100 text-emerald-700 hover:bg-emerald-100 transition-all">
                                            <i class="fa fa-undo text-xs"></i>
                                        </button>
                                    </form>
                                    @endrole
                                @else
                                    @role('Admin')
                                    <form action="{{ route('admin.submissions.join_now.destroy', $data->id) }}" method="POST" onsubmit="return confirm('Archive this course inquiry? You can restore it later.')">
                                        @csrf @method('DELETE')
                                        <button type="submit" title="Archive course inquiry" class="p-2.5 rounded-xl bg-neutral-100 border border-neutral-200 text-neutral-400 hover:text-red-500 hover:border-red-200 hover:shadow-lg transition-all">
                                            <i class="fa fa-archive text-xs"></i>
                                        </button>
                                    </form>
                                    @endrole
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="py-24 text-center text-neutral-400 font-medium italic">{{ $showArchived ? 'No archived course inquiries found.' : 'No active course inquiries found.' }}</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-6">
            {{ $joinNowQueries->links() }}
        </div>

        @if(! $showArchived && auth()->user()->hasRole('Admin'))
        <!-- Bulk Actions Bar -->
        <div id="bulkActionsBar" class="fixed bottom-8 left-1/2 -translate-x-1/2 bg-neutral-900 text-white px-8 py-4 rounded-2xl shadow-2xl flex items-center gap-6 z-[1000] transition-all transform translate-y-24 opacity-0">
            <span class="text-sm font-bold"><span id="selectedCount">0</span> enrollments selected</span>
            <div class="h-6 w-px bg-neutral-700"></div>
            @role('Admin')
            <form action="{{ route('admin.submissions.bulk-delete') }}" method="POST" onsubmit="return confirm('Archive all selected course inquiries? You can restore them later.')">
                @csrf
                <input type="hidden" name="type" value="join_now">
                <div id="bulkIdsContainer"></div>
                <button type="submit" class="flex items-center gap-2 text-rose-400 hover:text-rose-300 transition-colors text-sm font-black uppercase tracking-wider">
                    <i class="fa fa-archive"></i>
                    Bulk Archive
                </button>
            </form>
            @endrole
            <button onclick="unselectEnrollmentAll()" class="text-neutral-400 hover:text-white transition-colors text-xs font-bold uppercase">Cancel</button>
        </div>
        @endif
    </div>

    <!-- Enrollment Modal -->
    <div id="enrollmentModal" class="fixed inset-0 z-[999] hidden flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-neutral-900/60 backdrop-blur-sm" onclick="closeEnrollmentModal()"></div>
        <div class="relative w-full max-w-2xl bg-white rounded-3xl shadow-2xl overflow-hidden border border-neutral-100">
            <form id="enrollmentForm" method="POST">
                @csrf @method('PATCH')
                <div class="px-8 py-6 border-b border-neutral-100 flex justify-between items-center bg-neutral-50/50">
                    <div>
                        <h3 class="text-xl font-black uppercase tracking-tight text-neutral-900">Enrollment <span class="text-brand-gold">Audit</span></h3>
                        <p id="modal_date" class="text-xs text-neutral-500 font-medium"></p>
                    </div>
                    <button type="button" onclick="closeEnrollmentModal()" class="p-2 text-neutral-400 hover:text-rose-500 transition-colors">
                        <i class="fa fa-times text-lg"></i>
                    </button>
                </div>
                <div class="p-8 space-y-6 max-h-[70vh] overflow-y-auto">
                    <!-- Student Info -->
                    <div class="grid grid-cols-2 gap-4">
                        <div class="p-4 bg-neutral-50 rounded-2xl border border-neutral-100">
                            <span class="text-[9px] font-black uppercase text-neutral-400 block mb-1">Applicant</span>
                            <p id="modal_name" class="text-sm font-black text-neutral-800"></p>
                            <p id="modal_contact" class="text-[10px] text-neutral-500"></p>
                            <div class="flex items-center gap-2 mt-1">
                                <p id="modal_address" class="text-[9px] text-neutral-400 italic font-bold"></p>
                                <span id="modal_contact_method" class="text-[8px] bg-white border border-neutral-200 text-neutral-600 px-1.5 py-0.5 rounded-md font-black uppercase"></span>
                            </div>
                        </div>
                        <div class="p-4 bg-brand-gold/10 rounded-2xl border border-brand-gold/10">
                            <span class="text-[9px] font-black uppercase text-brand-gold block mb-1">Desired Program</span>
                            <p id="modal_course" class="text-sm font-black text-brand-gold"></p>
                            <p id="modal_help_topic" class="text-[10px] text-brand-gold/80 font-bold mt-1"></p>
                            <p id="modal_batch_time" class="text-[10px] text-brand-gold/80 font-bold mt-1"></p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="p-4 bg-rose-50/60 border border-rose-100 rounded-2xl">
                            <span class="text-[9px] font-black uppercase text-rose-500 block mb-1">Lead Priority</span>
                            <p id="modal_lead_status" class="text-sm font-black text-rose-700"></p>
                        </div>
                        <div class="p-4 bg-neutral-50 border border-neutral-100 rounded-2xl">
                            <span class="text-[9px] font-black uppercase text-neutral-400 block mb-1">Audience / Intent</span>
                            <p id="modal_intent" class="text-xs text-neutral-700 font-bold"></p>
                        </div>
                    </div>

                    <!-- Queries -->
                    <div class="p-6 bg-neutral-50 border border-neutral-100 rounded-2xl">
                        <span class="text-[9px] font-black uppercase text-neutral-400 block mb-2">Student's Questions</span>
                        <p id="modal_queries" class="text-xs text-neutral-700 leading-relaxed italic"></p>
                    </div>

                    <div class="p-4 bg-blue-50/50 border border-blue-100 rounded-2xl">
                        <span class="text-[9px] font-black uppercase text-blue-400 block mb-2">Lead Source</span>
                        <p id="modal_source" class="text-xs text-neutral-700 leading-relaxed"></p>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Status Selection -->
                        <div class="space-y-2">
                            <label class="text-[11px] font-black uppercase text-neutral-500 tracking-wider">Application Stage</label>
                            <select name="status" @disabled(! auth()->user()->hasRole('Admin')) id="modal_status" class="w-full px-4 py-3 rounded-xl border-neutral-100 bg-neutral-50 text-xs font-black uppercase tracking-widest focus:ring-brand-gold focus:border-brand-gold transition-all cursor-pointer">
                                @foreach($statusOptions as $value => $label)
                                    <option value="{{ $value }}">{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Notes Area -->
                        <div class="space-y-2">
                            <label class="text-[11px] font-black uppercase text-neutral-500 tracking-wider">Internal Follow-up Log</label>
                            <textarea name="admin_notes" @readonly(! auth()->user()->hasRole('Admin')) id="modal_notes" rows="3" class="w-full px-4 py-3 rounded-xl border-neutral-100 bg-neutral-50 text-xs font-medium focus:ring-brand-gold focus:border-brand-gold transition-all resize-none" placeholder="Note down student interaction, call results, or documents needed..."></textarea>
                        </div>
                    </div>
                </div>
                <div class="px-8 py-6 border-t border-neutral-100 bg-neutral-50/50 flex justify-end gap-3">
                    <button type="button" onclick="closeEnrollmentModal()" class="px-6 py-3 text-xs font-black uppercase text-neutral-500 hover:text-neutral-800 transition-colors">Discard</button>
                    @if(! $showArchived && auth()->user()->hasRole('Admin'))
                        <button type="submit" class="px-8 py-3 rounded-xl bg-brand-gold text-brand-dark text-xs font-black uppercase shadow-lg hover:bg-brand-dark hover:text-brand-gold transition-all">Update Inquiry</button>
                    @endif
                </div>
            </form>
        </div>
    </div>

    <script>
        window.openEnrollmentModal = function(data) {
            const modal = document.getElementById('enrollmentModal');
            const form = document.getElementById('enrollmentForm');
            if (!modal || !form) return;
            
            const urlTemplate = "{{ route('admin.submissions.join_now.status.update', ['id' => ':id']) }}";
            form.action = urlTemplate.replace(':id', data.id);
            const nameEl = document.getElementById('modal_name');
            if (nameEl) nameEl.innerText = `${data.firstName || ''} ${data.lastName || ''}`.trim();
            const contactEl = document.getElementById('modal_contact');
            if (contactEl) contactEl.innerText = `${data.email || 'No email yet'} | ${data.phone || ''}`;
            const addressEl = document.getElementById('modal_address');
            if (addressEl) addressEl.innerText = data.address || '';
            const methodEl = document.getElementById('modal_contact_method');
            if (methodEl) methodEl.innerText = data.contactMethod || 'Phone Call';
            const courseEl = document.getElementById('modal_course');
            if (courseEl) courseEl.innerText = data.course || '';
            const helpEl = document.getElementById('modal_help_topic');
            if (helpEl) helpEl.innerText = data.help_topic ? `Help topic: ${data.help_topic}` : 'Help topic not captured';
            const batchEl = document.getElementById('modal_batch_time');
            if (batchEl) batchEl.innerText = data.preferred_batch_time ? `Preferred batch: ${data.preferred_batch_time}` : '';
            const leadEl = document.getElementById('modal_lead_status');
            if (leadEl) leadEl.innerText = `${(data.lead_status || 'Basic').replace(' Lead', '')} (${data.lead_score || 0})`;
            const intentEl = document.getElementById('modal_intent');
            if (intentEl) intentEl.innerText = [data.audience_type, data.inquiry_intent].filter(Boolean).join(' | ') || 'No audience or intent captured.';
            const queriesEl = document.getElementById('modal_queries');
            if (queriesEl) queriesEl.innerText = data.queries ? `"${data.queries}"` : 'No additional questions provided.';
            const sourceEl = document.getElementById('modal_source');
            if (sourceEl) sourceEl.innerText = [data.lead_source, data.source_page, data.source_section, data.cta_id, data.landing_page].filter(Boolean).join(' | ') || 'No source data captured.';
            const dateEl = document.getElementById('modal_date');
            if (dateEl) dateEl.innerText = `Received on ${new Date(data.created_at).toLocaleString()}`;
            const statusEl = document.getElementById('modal_status');
            if (statusEl) statusEl.value = data.status || '';
            const notesEl = document.getElementById('modal_notes');
            if (notesEl) notesEl.value = data.admin_notes || '';
            
            modal.classList.remove('hidden');
        };

        window.closeEnrollmentModal = function() {
            const modal = document.getElementById('enrollmentModal');
            if (modal) modal.classList.add('hidden');
        };
        window.closeModal = function() {
            window.closeContactModal?.();
            window.closeEnrollmentModal?.();
        };

        function initEnrollmentBulkActions() {
            const page = document.getElementById('joinNowDisplayPage');
            if (!page) return;

            const selectAllEnrollments = page.querySelector('#selectAll');
            const checkboxes = page.querySelectorAll('.row-checkbox');
            const bulkBar = page.querySelector('#bulkActionsBar');
            const selectedCount = page.querySelector('#selectedCount');
            const bulkIdsContainer = page.querySelector('#bulkIdsContainer');

            if (!selectAllEnrollments || !bulkBar || !selectedCount || !bulkIdsContainer) return;

            function updateBulkBar() {
                const checked = page.querySelectorAll('.row-checkbox:checked');
                selectedCount.innerText = checked.length;
                
                bulkIdsContainer.innerHTML = '';
                checked.forEach(cb => {
                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = 'ids[]';
                    input.value = cb.value;
                    bulkIdsContainer.appendChild(input);
                });

                if (checked.length > 0) {
                    bulkBar.classList.remove('translate-y-24', 'opacity-0');
                } else {
                    bulkBar.classList.add('translate-y-24', 'opacity-0');
                    selectAllEnrollments.checked = false;
                }
            }

            selectAllEnrollments.onchange = () => {
                checkboxes.forEach(cb => cb.checked = selectAllEnrollments.checked);
                updateBulkBar();
            };

            checkboxes.forEach(cb => {
                cb.onchange = updateBulkBar;
            });

            window.unselectEnrollmentAll = function() {
                const currentPage = document.getElementById('joinNowDisplayPage');
                if (!currentPage) return;
                currentPage.querySelectorAll('.row-checkbox').forEach(cb => cb.checked = false);
                const selAll = currentPage.querySelector('#selectAll');
                if (selAll) selAll.checked = false;
                updateBulkBar();
            };
            window.unselectAll = function() {
                window.unselectContactAll?.();
                window.unselectEnrollmentAll?.();
                window.unselectNewsletterAll?.();
            };
        }

        document.addEventListener('livewire:navigated', initEnrollmentBulkActions);
        document.addEventListener('DOMContentLoaded', initEnrollmentBulkActions);
        initEnrollmentBulkActions();
    </script>
</x-layouts::app>
