<footer class="border-t border-neutral-200 bg-white px-4 py-3 dark:border-neutral-700 dark:bg-zinc-900 sm:px-6">
    <div class="flex flex-col items-center justify-between gap-2 sm:flex-row">
        <p class="text-xs text-neutral-500 dark:text-neutral-400">
            &copy; {{ date('Y') }} {{ config('app.name', 'Bewole Furniture') }}. Hak Cipta Dilindungi.
        </p>
        <p class="text-xs text-neutral-400 dark:text-neutral-500">
            Versi {{ config('app.version', '1.0.0') }}
        </p>
    </div>
</footer>
