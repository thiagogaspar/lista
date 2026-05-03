<div class="infobox w-full">
    <div class="border-b-2 border-surface-300 dark:border-ink-600 px-3 py-2 bg-surface-100 dark:bg-ink-700">
        <h3 class="font-display font-bold text-xs uppercase tracking-wider text-surface-700 dark:text-ink-200">{{ $title ?? 'Info' }}</h3>
    </div>
    <table class="w-full">
        @foreach($items as $label => $value)
        @if($value)
        <tr class="border-b border-surface-200 dark:border-ink-700 last:border-0">
            <td class="infobox-label">{{ $label }}</td>
            <td class="infobox-value">{{ $value }}</td>
        </tr>
        @endif
        @endforeach
    </table>
</div>
