<x-layouts::app :title="__('Staff Management')">
    <div class="flex flex-col gap-6 p-4 sm:p-8">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <flux:heading size="xl">{{ __('Staff Management') }}</flux:heading>
            <flux:button :href="route('admin.staff.create')" variant="primary">{{ __('Add Staff') }}</flux:button>
        </div>
        <x-auth-session-status :status="session('success')" />
        @if (session('warning'))
            <div class="font-medium text-sm text-amber-600 dark:text-amber-400">
                {{ session('warning') }}
            </div>
        @endif
        <flux:error name="staff" />
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead><tr class="border-b border-zinc-200 dark:border-zinc-700">
                    <th class="p-3">Name</th><th class="p-3">Email</th><th class="p-3">Created</th><th class="p-3">Action</th>
                </tr></thead>
                <tbody>
                    @forelse ($staff as $member)
                        <tr class="border-b border-zinc-200 dark:border-zinc-700">
                            <td class="p-3">{{ $member->name }}</td>
                            <td class="p-3">{{ $member->email }}</td>
                            <td class="p-3">{{ $member->created_at->format('Y-m-d') }}</td>
                            <td class="p-3">
                                @if (! $member->isPermanentAdmin() && ! $member->hasRole('Admin'))
                                    <form method="POST" action="{{ route('admin.staff.destroy', $member) }}" onsubmit="return confirm('Delete this staff account?')">
                                        @csrf
                                        @method('DELETE')
                                        <flux:button type="submit" variant="danger" size="sm">{{ __('Delete Staff') }}</flux:button>
                                    </form>
                                @else
                                    {{ __('Protected Admin') }}
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="p-3">{{ __('No Staff accounts yet.') }}</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-layouts::app>
