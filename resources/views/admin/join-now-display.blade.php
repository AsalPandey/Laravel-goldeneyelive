<x-layouts::app :title="__('Enrollment Queries')">
    <div class="flex h-full w-full flex-1 flex-col gap-6 p-6">
        <div class="flex items-center justify-between pb-4 border-b border-neutral-100">
            <div class="space-y-1">
                <h1 class="text-3xl font-black text-neutral-900 tracking-tight uppercase">Enrollment <span class="text-brand-gold">Queries</span></h1>
                <p class="text-neutral-500 text-sm">Review and manage student course enrollment requests.</p>
            </div>
            <div class="flex items-center gap-4">
                <form action="{{ route('admin.submissions.join_now-display') }}" method="GET" class="relative">
                    <i class="fa fa-search absolute left-4 top-1/2 -translate-y-1/2 text-neutral-400 text-xs"></i>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search name or email..." class="pl-10 pr-4 py-2.5 rounded-xl border-neutral-200 bg-white text-xs w-64 focus:border-brand-gold focus:ring-0">
                </form>
            </div>
        </div>

        <div class="overflow-x-auto rounded-2xl border border-neutral-100 bg-white shadow-sm">
            <table class="min-w-full divide-y divide-neutral-200">
                <thead class="bg-neutral-50/50">
                    <tr>
                        <th scope="col" class="px-6 py-4 text-left">
                            <input type="checkbox" id="selectAll" class="rounded border-neutral-300 text-brand-gold focus:ring-brand-gold">
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
                            <input type="checkbox" name="ids[]" value="{{ $data->id }}" class="row-checkbox rounded border-neutral-300 text-brand-gold focus:ring-brand-gold">
                        </td>
                        <td class="px-6 py-5">
                            <div class="text-sm font-black text-neutral-900 leading-tight">{{ $data->firstName }} {{ $data->lastName }}</div>
                            <div class="text-[10px] text-neutral-400 font-bold uppercase mt-0.5">{{ $data->email }} &bull; {{ $data->phone }}</div>
                            <div class="flex items-center gap-2 mt-1">
                                @if($data->address)
                                    <div class="text-[9px] text-neutral-400 italic opacity-60">From {{ $data->address }}</div>
                                @endif
                                @if($data->contactMethod)
                                    <span class="text-[8px] bg-neutral-100 text-neutral-600 px-1.5 py-0.5 rounded-md font-black uppercase">{{ $data->contactMethod }}</span>
                                @endif
                                <span class="text-[9px] text-neutral-400 italic opacity-60">&bull; {{ $data->created_at->diffForHumans() }}</span>
                            </div>
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
                        </td>
                        <td class="px-6 py-5">
                            @if($data->admin_notes)
                                <div class="text-[10px] text-neutral-600 font-medium line-clamp-2 italic">"{{ $data->admin_notes }}"</div>
                            @else
                                <div class="text-[9px] text-neutral-300 uppercase tracking-widest italic font-bold">No Audit Log</div>
                            @endif
                            @if($data->followed_up_at)
                                <div class="text-[8px] text-emerald-500 font-black uppercase mt-1">Contacted {{ $data->followed_up_at->diffForHumans() }}</div>
                            @endif
                        </td>
                        <td class="px-6 py-5">
                            <span class="inline-flex items-center rounded-lg px-2.5 py-1 text-[10px] font-black uppercase tracking-tighter border
                                {{ $data->status == 'new' ? 'text-blue-700 border-blue-100 bg-blue-50' : '' }}
                                {{ $data->status == 'contacted' ? 'text-brand-gold border-brand-gold/10 bg-brand-gold/10' : '' }}
                                {{ $data->status == 'enrolled' ? 'text-emerald-700 border-emerald-100 bg-emerald-50' : '' }}
                                {{ $data->status == 'resolved' ? 'text-emerald-700 border-emerald-100 bg-emerald-50' : '' }}
                                {{ $data->status == 'rejected' ? 'text-rose-700 border-rose-100 bg-rose-50' : '' }}
                                {{ $data->status == 'reviewed' ? 'text-neutral-700 border-neutral-100 bg-neutral-50' : '' }}
                            ">
                                {{ $data->status }}
                            </span>
                        </td>
                        <td class="px-6 py-5 text-right">
                            <div class="flex justify-end gap-2 pr-4">
                                <button onclick="openEnrollmentModal({{ json_encode($data) }})" class="p-2.5 rounded-xl bg-neutral-100 border border-neutral-200 text-neutral-600 hover:text-brand-gold hover:border-brand-gold/20 hover:shadow-lg transition-all">
                                    <i class="fa fa-eye text-xs"></i>
                                </button>
                                <form action="{{ route('admin.submissions.join_now.destroy', $data->id) }}" method="POST" onsubmit="return confirm('Archive this inquiry forever?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="p-2.5 rounded-xl bg-neutral-100 border border-neutral-200 text-neutral-400 hover:text-red-500 hover:border-red-200 hover:shadow-lg transition-all">
                                        <i class="fa fa-trash-alt text-xs"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="py-24 text-center text-neutral-400 font-medium italic">No enrollment queries found in the website database.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-6">
            {{ $joinNowQueries->links() }}
        </div>

        <!-- Bulk Actions Bar -->
        <div id="bulkActionsBar" class="fixed bottom-8 left-1/2 -translate-x-1/2 bg-neutral-900 text-white px-8 py-4 rounded-2xl shadow-2xl flex items-center gap-6 z-[1000] transition-all transform translate-y-24 opacity-0">
            <span class="text-sm font-bold"><span id="selectedCount">0</span> enrollments selected</span>
            <div class="h-6 w-px bg-neutral-700"></div>
            <form action="{{ route('admin.submissions.bulk-delete') }}" method="POST" onsubmit="return confirm('Delete all selected enrollments forever?')">
                @csrf
                <input type="hidden" name="type" value="join_now">
                <div id="bulkIdsContainer"></div>
                <button type="submit" class="flex items-center gap-2 text-rose-400 hover:text-rose-300 transition-colors text-sm font-black uppercase tracking-wider">
                    <i class="fa fa-trash-alt"></i>
                    Bulk Delete
                </button>
            </form>
            <button onclick="unselectAll()" class="text-neutral-400 hover:text-white transition-colors text-xs font-bold uppercase">Cancel</button>
        </div>
    </div>

    <!-- Enrollment Modal -->
    <div id="enrollmentModal" class="fixed inset-0 z-[999] hidden flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-neutral-900/60 backdrop-blur-sm" onclick="closeModal()"></div>
        <div class="relative w-full max-w-2xl bg-white rounded-3xl shadow-2xl overflow-hidden border border-neutral-100">
            <form id="enrollmentForm" method="POST">
                @csrf @method('PATCH')
                <div class="px-8 py-6 border-b border-neutral-100 flex justify-between items-center bg-neutral-50/50">
                    <div>
                        <h3 class="text-xl font-black uppercase tracking-tight text-neutral-900">Enrollment <span class="text-brand-gold">Audit</span></h3>
                        <p id="modal_date" class="text-xs text-neutral-500 font-medium"></p>
                    </div>
                    <button type="button" onclick="closeModal()" class="p-2 text-neutral-400 hover:text-rose-500 transition-colors">
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
                        </div>
                    </div>

                    <!-- Queries -->
                    <div class="p-6 bg-neutral-50 border border-neutral-100 rounded-2xl">
                        <span class="text-[9px] font-black uppercase text-neutral-400 block mb-2">Student's Questions</span>
                        <p id="modal_queries" class="text-xs text-neutral-700 leading-relaxed italic"></p>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Status Selection -->
                        <div class="space-y-2">
                            <label class="text-[11px] font-black uppercase text-neutral-500 tracking-wider">Application Stage</label>
                            <select name="status" id="modal_status" class="w-full px-4 py-3 rounded-xl border-neutral-100 bg-neutral-50 text-xs font-black uppercase tracking-widest focus:ring-brand-gold focus:border-brand-gold transition-all cursor-pointer">
                                <option value="new">New Request</option>
                                <option value="reviewed">Reviewed</option>
                                <option value="contacted">Contacted</option>
                                <option value="enrolled">Enrolled</option>
                                <option value="rejected">Rejected</option>
                            </select>
                        </div>

                        <!-- Notes Area -->
                        <div class="space-y-2">
                            <label class="text-[11px] font-black uppercase text-neutral-500 tracking-wider">Internal Follow-up Log</label>
                            <textarea name="admin_notes" id="modal_notes" rows="3" class="w-full px-4 py-3 rounded-xl border-neutral-100 bg-neutral-50 text-xs font-medium focus:ring-brand-gold focus:border-brand-gold transition-all resize-none" placeholder="Note down student interaction, call results, or documents needed..."></textarea>
                        </div>
                    </div>
                </div>
                <div class="px-8 py-6 border-t border-neutral-100 bg-neutral-50/50 flex justify-end gap-3">
                    <button type="button" onclick="closeModal()" class="px-6 py-3 text-xs font-black uppercase text-neutral-500 hover:text-neutral-800 transition-colors">Discard</button>
                    <button type="submit" class="px-8 py-3 rounded-xl bg-brand-gold text-brand-dark text-xs font-black uppercase shadow-lg hover:bg-brand-dark hover:text-brand-gold transition-all">Update Audit Log</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        if (typeof window.enrollmentDisplayInited === 'undefined') {
            window.enrollmentDisplayInited = true;

            window.openEnrollmentModal = function(data) {
                const modal = document.getElementById('enrollmentModal');
                const form = document.getElementById('enrollmentForm');
                
                const urlTemplate = "{{ route('admin.submissions.join_now.status.update', ['id' => ':id']) }}";
                form.action = urlTemplate.replace(':id', data.id);
                document.getElementById('modal_name').innerText = `${data.firstName} ${data.lastName}`;
                document.getElementById('modal_contact').innerText = `${data.email} | ${data.phone}`;
                document.getElementById('modal_address').innerText = data.address;
                document.getElementById('modal_contact_method').innerText = data.contactMethod || 'Phone Call';
                document.getElementById('modal_course').innerText = data.course;
                document.getElementById('modal_queries').innerText = data.queries ? `"${data.queries}"` : 'No additional questions provided.';
                document.getElementById('modal_date').innerText = `Received on ${new Date(data.created_at).toLocaleString()}`;
                document.getElementById('modal_status').value = data.status;
                document.getElementById('modal_notes').value = data.admin_notes || '';
                
                modal.classList.remove('hidden');
            };

            window.closeModal = function() {
                document.getElementById('enrollmentModal').classList.add('hidden');
            };

            function initEnrollmentBulkActions() {
                const selectAllEnrollments = document.getElementById('selectAll');
                const checkboxes = document.querySelectorAll('.row-checkbox');
                const bulkBar = document.getElementById('bulkActionsBar');
                const selectedCount = document.getElementById('selectedCount');
                const bulkIdsContainer = document.getElementById('bulkIdsContainer');

                if (!selectAllEnrollments) return;

                function updateBulkBar() {
                    const checked = document.querySelectorAll('.row-checkbox:checked');
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

                selectAllEnrollments.addEventListener('change', () => {
                    checkboxes.forEach(cb => cb.checked = selectAllEnrollments.checked);
                    updateBulkBar();
                });

                checkboxes.forEach(cb => {
                    cb.addEventListener('change', updateBulkBar);
                });

                window.unselectAll = function() {
                    checkboxes.forEach(cb => cb.checked = false);
                    selectAllEnrollments.checked = false;
                    updateBulkBar();
                };
            }

            document.addEventListener('livewire:navigated', initEnrollmentBulkActions);
            document.addEventListener('DOMContentLoaded', initEnrollmentBulkActions);
        }
    </script>
</x-layouts::app>
