<x-layouts::app :title="__('Add Staff')">
    <div class="flex max-w-xl flex-col gap-6 p-4 sm:p-8">
        <flux:heading size="xl">{{ __('Add Staff') }}</flux:heading>
        <flux:text>Use an {{ '@'.config('goldeneye.organization_email_domain') }} email. The staff member will use Forgot Password to set their password.</flux:text>
        <form method="POST" action="{{ route('admin.staff.store') }}" class="flex flex-col gap-6">
            @csrf
            <flux:input name="name" :label="__('Name')" :value="old('name')" required maxlength="255" autofocus />
            <flux:input name="email" :label="__('Email')" type="email" :value="old('email')" required maxlength="255" />
            <flux:button type="submit" variant="primary">{{ __('Add Staff') }}</flux:button>
            <flux:link :href="route('admin.staff.index')">{{ __('Back to Staff Management') }}</flux:link>
        </form>
    </div>
</x-layouts::app>
