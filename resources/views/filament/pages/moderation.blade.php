<x-filament::page>
    <div class="space-y-6">
        <div>
            <h2 class="text-lg font-semibold mb-2">Pending Comments ({{ $pendingComments->count() }})</h2>
            <div class="space-y-2">
                @forelse($pendingComments as $comment)
                <div class="p-3 bg-white dark:bg-surface-800 border border-surface-200 dark:border-surface-700">
                    <p class="text-sm"><strong>{{ $comment->user?->name ?? 'Anonymous' }}</strong> on {{ class_basename($comment->commentable_type) }} #{{ $comment->commentable_id }}</p>
                    <p class="text-xs text-surface-500 mt-1">{{ Str::limit($comment->body, 100) }}</p>
                    <div class="flex gap-2 mt-2">
                        <a href="{{ url('/admin/comments/' . $comment->id . '/edit') }}" class="text-xs text-brand-600 hover:underline">Review</a>
                    </div>
                </div>
                @empty
                <p class="text-sm text-surface-400">No pending comments.</p>
                @endforelse
            </div>
        </div>

        <div>
            <h2 class="text-lg font-semibold mb-2">Pending Tags ({{ $pendingTags->count() }})</h2>
            <div class="flex flex-wrap gap-2">
                @forelse($pendingTags as $tag)
                <span class="px-2 py-1 text-sm bg-warning-100 dark:bg-warning-900/20 border border-warning-200 dark:border-warning-800">{{ $tag->name }}</span>
                @empty
                <p class="text-sm text-surface-400">No pending tags.</p>
                @endforelse
            </div>
        </div>

        <div>
            <h2 class="text-lg font-semibold mb-2">Pending Edit Suggestions ({{ $pendingSuggestions->count() }})</h2>
            <div class="space-y-2">
                @forelse($pendingSuggestions as $suggestion)
                <div class="p-3 bg-white dark:bg-surface-800 border border-surface-200 dark:border-surface-700">
                    <p class="text-sm"><strong>{{ $suggestion->user?->name ?? 'Anonymous' }}</strong> suggested changing <code>{{ $suggestion->field }}</code> on {{ class_basename($suggestion->suggestable_type) }}</p>
                    <p class="text-xs text-surface-500 mt-1">{{ Str::limit($suggestion->suggested_value, 100) }}</p>
                </div>
                @empty
                <p class="text-sm text-surface-400">No pending suggestions.</p>
                @endforelse
            </div>
        </div>
    </div>
</x-filament::page>
