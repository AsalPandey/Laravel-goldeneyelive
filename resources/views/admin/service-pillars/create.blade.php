<x-layouts::app :title="__('Create Service Pillar')">
    <div class="flex h-full w-full flex-1 flex-col gap-8 p-8">
        <div class="flex items-center justify-between pb-4 border-b border-neutral-100">
            <div class="space-y-1">
                <h1 class="text-3xl font-black text-neutral-900 tracking-tight uppercase">New <span class="text-brand-gold">Service Pillar</span></h1>
                <p class="text-neutral-500 text-sm">Add a CMS-managed homepage offer and conversion path.</p>
            </div>
            <a href="{{ route('admin.service-pillars.index') }}" class="inline-flex items-center gap-2 rounded-xl bg-neutral-100 text-neutral-600 px-6 py-3 text-sm font-black uppercase hover:bg-neutral-900 hover:text-white transition-all">
                <i class="fa fa-arrow-left"></i> Back
            </a>
        </div>

        <div class="bg-white rounded-3xl border border-neutral-100 shadow-xl overflow-hidden">
            <form action="{{ route('admin.service-pillars.store') }}" method="POST" class="p-10">
                @csrf
                @include('admin.service-pillars._form')
                <div class="mt-10 pt-8 border-t border-neutral-100 flex justify-end gap-4">
                    <button type="reset" class="px-8 py-4 rounded-xl bg-neutral-100 text-neutral-500 text-sm font-black uppercase hover:bg-neutral-200 transition-all">Reset</button>
                    <button type="submit" class="px-12 py-4 rounded-xl bg-brand-gold text-brand-dark text-sm font-black uppercase shadow-2xl hover:bg-neutral-900 hover:text-white transition-all">Create Pillar</button>
                </div>
            </form>
        </div>
    </div>
</x-layouts::app>
