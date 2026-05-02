<div id="{{ $containerId }}" class="border border-surface-200 dark:border-surface-700 bg-surface-50 dark:bg-surface-800 overflow-hidden" style="height:400px">
    <div class="flex items-center justify-center h-full text-surface-400">
        <svg class="w-8 h-8 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
    </div>
</div>

@vite(['resources/js/genealogy-graph.js'])
<script>
document.addEventListener('DOMContentLoaded', function () {
    initBandGraph('{{ $containerId }}', @json($graph));
});
</script>
