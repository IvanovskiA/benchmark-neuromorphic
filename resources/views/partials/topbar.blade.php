<header class="border-b border-slate-200 bg-white shadow-sm">
    <div class="flex h-16 items-center gap-4 px-6 lg:px-8">
        <button
            id="sidebar-toggle"
            type="button"
            aria-expanded="true"
            aria-label="Collapse sidebar"
            class="inline-flex h-9 w-9 shrink-0 items-center justify-center rounded-lg border border-slate-200 text-slate-600 transition hover:bg-slate-100 hover:text-slate-900 focus:outline-none focus:ring-2 focus:ring-brand-500"
        >
            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
            </svg>
        </button>
        <div>
            <h1 class="text-lg font-semibold text-slate-900">{{ $heading ?? 'Dashboard' }}</h1>
            @isset($breadcrumb)
                <p class="text-sm text-slate-500">{{ $breadcrumb }}</p>
            @endisset
        </div>
    </div>
</header>
