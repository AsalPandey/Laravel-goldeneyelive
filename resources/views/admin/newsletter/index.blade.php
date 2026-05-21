<x-layouts::app :title="__('Newsletter Subscribers')">
    <div class="flex h-full w-full flex-1 flex-col gap-6 p-6">
        <div class="flex items-center justify-between pb-4 border-b border-neutral-100">
            <div class="space-y-1">
                <h1 class="text-3xl font-black text-neutral-900 tracking-tight uppercase">Newsletter <span class="text-orange-600">Subscribers</span></h1>
                <p class="text-neutral-500 text-sm">Manage the institutional mailing list and subscribers.</p>
            </div>
            <div class="flex items-center gap-4">
                <form action="{{ route('admin.submissions.newsletter-display') }}" method="GET" class="flex items-center gap-2">
                    <div class="relative">
                        <i class="fa fa-search absolute left-4 top-1/2 -translate-y-1/2 text-neutral-400 text-xs"></i>
                        <input type="search" name="search" value="{{ request('search') }}" placeholder="Search emails..." class="pl-10 pr-4 py-2.5 rounded-xl border-neutral-200 bg-white text-xs w-64 focus:border-orange-500 focus:ring-0">
                    </div>
                    <button type="submit" class="inline-flex items-center gap-2 rounded-xl bg-neutral-900 px-4 py-2.5 text-xs font-black uppercase text-white hover:bg-orange-600 transition-all">
                        <i class="fa fa-search"></i> Search
                    </button>
                    @if(request('search'))
                        <a href="{{ route('admin.submissions.newsletter-display') }}" class="inline-flex items-center rounded-xl bg-neutral-100 px-4 py-2.5 text-xs font-black uppercase text-neutral-600 hover:bg-neutral-200 transition-all">Clear</a>
                    @endif
                </form>
            </div>
        </div>

        <div class="overflow-x-auto rounded-2xl border border-neutral-100 bg-white shadow-sm">
            <table class="min-w-full divide-y divide-neutral-200">
                <thead class="bg-neutral-50/50">
                    <tr>
                        <th scope="col" class="px-6 py-4 text-left">
                            <input type="checkbox" id="selectAll" class="rounded border-neutral-300 text-orange-600 focus:ring-orange-500">
                        </th>
                        <th scope="col" class="px-6 py-4 text-left text-xs font-black uppercase tracking-widest text-neutral-400">Subscriber Email</th>
                        <th scope="col" class="px-6 py-4 text-left text-xs font-black uppercase tracking-widest text-neutral-400">Join Date</th>
                        <th scope="col" class="relative px-6 py-4 tracking-widest text-xs font-black uppercase text-neutral-400 text-right pr-12">Manage</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-neutral-100">
                    @forelse($subscribers as $sub)
                    <tr class="hover:bg-neutral-50/30 transition-all select-row">
                        <td class="px-6 py-5">
                            <input type="checkbox" name="ids[]" value="{{ $sub->id }}" class="row-checkbox rounded border-neutral-300 text-orange-600 focus:ring-orange-500">
                        </td>
                        <td class="px-6 py-5">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-full bg-orange-100 flex items-center justify-center text-orange-600 text-[10px] font-black uppercase">
                                    {{ substr($sub->email, 0, 2) }}
                                </div>
                                <div class="text-sm font-black text-neutral-900 leading-tight">{{ $sub->email }}</div>
                            </div>
                        </td>
                        <td class="px-6 py-5">
                            <div class="text-[10px] text-neutral-500 font-bold uppercase tracking-wider">{{ $sub->created_at->format('M d, Y') }}</div>
                            <div class="text-[9px] text-neutral-400 italic mt-0.5">{{ $sub->created_at->diffForHumans() }}</div>
                        </td>
                        <td class="px-6 py-5 text-right">
                            <div class="flex justify-end gap-2 pr-4">
                                <form action="{{ route('admin.submissions.newsletter.destroy', $sub->id) }}" method="POST" onsubmit="return confirm('Remove this subscriber permanently?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="p-2.5 rounded-xl bg-neutral-100 border border-neutral-200 text-neutral-400 hover:text-red-500 hover:border-red-200 hover:shadow-lg transition-all">
                                        <i class="fa fa-user-minus text-xs"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="4" class="py-24 text-center text-neutral-400 font-medium italic">No active subscribers found in the database.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-6">
            {{ $subscribers->links() }}
        </div>

        <!-- Bulk Actions Bar -->
        <div id="bulkActionsBar" class="fixed bottom-8 left-1/2 -translate-x-1/2 bg-neutral-900 text-white px-8 py-4 rounded-2xl shadow-2xl flex items-center gap-6 z-[1000] transition-all transform translate-y-24 opacity-0">
            <span class="text-sm font-bold"><span id="selectedCount">0</span> subscribers selected</span>
            <div class="h-6 w-px bg-neutral-700"></div>
            <form action="{{ route('admin.submissions.bulk-delete') }}" method="POST" onsubmit="return confirm('Delete all selected subscribers forever?')">
                @csrf
                <input type="hidden" name="type" value="newsletter">
                <div id="bulkIdsContainer"></div>
                <button type="submit" class="flex items-center gap-2 text-rose-400 hover:text-rose-300 transition-colors text-sm font-black uppercase tracking-wider">
                    <i class="fa fa-trash-alt"></i>
                    Bulk Delete
                </button>
            </form>
            <button onclick="unselectAll()" class="text-neutral-400 hover:text-white transition-colors text-xs font-bold uppercase">Cancel</button>
        </div>
    </div>

    <script>
        if (typeof window.newsletterDisplayInited === 'undefined') {
            window.newsletterDisplayInited = true;

            function initNewsletterBulkActions() {
                const selectAllSubscribers = document.getElementById('selectAll');
                const checkboxes = document.querySelectorAll('.row-checkbox');
                const bulkBar = document.getElementById('bulkActionsBar');
                const selectedCount = document.getElementById('selectedCount');
                const bulkIdsContainer = document.getElementById('bulkIdsContainer');

                if (!selectAllSubscribers) return;

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
                        selectAllSubscribers.checked = false;
                    }
                }

                selectAllSubscribers.addEventListener('change', () => {
                    checkboxes.forEach(cb => cb.checked = selectAllSubscribers.checked);
                    updateBulkBar();
                });

                checkboxes.forEach(cb => {
                    cb.addEventListener('change', updateBulkBar);
                });

                window.unselectAll = function() {
                    checkboxes.forEach(cb => cb.checked = false);
                    selectAllSubscribers.checked = false;
                    updateBulkBar();
                };
            }

            document.addEventListener('livewire:navigated', initNewsletterBulkActions);
            document.addEventListener('DOMContentLoaded', initNewsletterBulkActions);
        }
    </script>
</x-layouts::app>
