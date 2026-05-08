<x-layouts::app :title="__('Edit Service Pillar')">
    <div class="flex h-full w-full flex-1 flex-col gap-8 p-8">
        <div class="flex items-center justify-between pb-4 border-b border-neutral-100">
            <div class="space-y-1">
                <h1 class="text-3xl font-black text-neutral-900 tracking-tight uppercase">Edit <span class="text-brand-gold">Service Pillar</span></h1>
                <p class="text-neutral-500 text-sm">{{ $servicePillar->title }}</p>
            </div>
            <a href="{{ route('admin.service-pillars.index') }}" class="inline-flex items-center gap-2 rounded-xl bg-neutral-100 text-neutral-600 px-6 py-3 text-sm font-black uppercase hover:bg-neutral-900 hover:text-white transition-all">
                <i class="fa fa-arrow-left"></i> Back
            </a>
        </div>

        <div class="bg-white rounded-3xl border border-neutral-100 shadow-xl overflow-hidden">
            <form action="{{ route('admin.service-pillars.update', $servicePillar) }}" method="POST" class="p-10">
                @csrf
                @method('PUT')
                @include('admin.service-pillars._form', ['servicePillar' => $servicePillar])
                <div class="mt-10 pt-8 border-t border-neutral-100 flex justify-end gap-4">
                    <a href="{{ route('admin.service-pillars.index') }}" class="px-8 py-4 rounded-xl bg-neutral-100 text-neutral-500 text-sm font-black uppercase hover:bg-neutral-200 transition-all">Cancel</a>
                    <button type="submit" class="px-12 py-4 rounded-xl bg-brand-gold text-brand-dark text-sm font-black uppercase shadow-2xl hover:bg-neutral-900 hover:text-white transition-all">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</x-layouts::app>
