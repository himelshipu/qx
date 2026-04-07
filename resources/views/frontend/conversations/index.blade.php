@extends('frontend.layouts.app')

@section('title', 'Conversations')

@section('content')
    @php
        $user = auth()->user();

        $sidebarConversations = \App\Models\Conversation::query()
            ->where('brand_user_id', $user->id)
            ->with([
                'influencer.user',
                'brandUser',
                'messages' => fn($query) => $query->latest()->limit(1),
            ])
            ->orderByDesc('updated_at')
            ->get();

        $getInitials = static function (?string $name): string {
            $name = trim((string) $name);
            if ($name === '') {
                return 'NA';
            }

            $parts = preg_split('/\s+/', $name) ?: [];
            $first = mb_substr($parts[0] ?? '', 0, 1);
            $last = count($parts) > 1 ? mb_substr($parts[count($parts) - 1], 0, 1) : '';

            return mb_strtoupper($first . $last);
        };
    @endphp

    <div class="h-[calc(100vh-120px)] rounded-2xl border border-gray-200 bg-white shadow-sm dark:border-gray-800 dark:bg-gray-900 overflow-hidden transition-all duration-200">
        <div class="flex h-full w-full">
            <!-- Sidebar -->
            <aside class="w-80 flex-shrink-0 border-r border-gray-200 dark:border-gray-800 bg-gray-50/40 dark:bg-gray-900/50 flex flex-col h-full">
                <header class="flex-shrink-0 border-b border-gray-200 bg-white/80 backdrop-blur-sm px-5 py-4 dark:border-gray-800 dark:bg-gray-900/80">
                    <div class="flex items-center justify-between">
                        <h2 class="text-base font-semibold text-gray-900 dark:text-white flex items-center gap-2">
                            <svg class="w-5 h-5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                            </svg>
                            Conversations
                            <span class="text-xs font-normal text-gray-500 dark:text-gray-400 bg-gray-100 dark:bg-gray-800 px-2 py-0.5 rounded-full">{{ $sidebarConversations->count() }}</span>
                        </h2>
                    </div>
                </header>

                <div class="flex-1 overflow-y-auto p-3 space-y-1.5">
                    @forelse ($sidebarConversations as $item)
                        @php
                            $party = $item->creator?->user;
                            $partyName = $party?->name ?? 'Unknown User';
                            $partyAvatar = filled($party?->profile_image_path ?? null) ? image_url($party->profile_image_path) : null;
                            $latestMessage = $item->messages->first();
                        @endphp

                        <a href="{{ route('frontend.conversations.show', $item) }}"
                            class="flex items-start gap-3 rounded-xl p-3 transition-all duration-150 group
                                hover:bg-white/60 dark:hover:bg-gray-800/40">
                            <div class="relative shrink-0">
                                <div class="h-12 w-12 rounded-full overflow-hidden bg-gradient-to-br from-indigo-100 to-purple-100 dark:from-indigo-900/30 dark:to-purple-900/30 flex items-center justify-center text-sm font-semibold text-indigo-700 dark:text-indigo-300 shadow-sm">
                                    @if ($partyAvatar)
                                        <img src="{{ $partyAvatar }}" alt="{{ $partyName }}" class="h-full w-full object-cover">
                                    @else
                                        <span>{{ $getInitials($partyName) }}</span>
                                    @endif
                                </div>
                                <span class="absolute bottom-0 right-0 block h-3 w-3 rounded-full bg-green-500 ring-2 ring-white dark:ring-gray-900"></span>
                            </div>
                            <div class="min-w-0 flex-1">
                                <div class="flex items-center justify-between gap-2">
                                    <h3 class="truncate text-sm font-semibold text-gray-900 dark:text-white">{{ $partyName }}</h3>
                                    <span class="text-[11px] font-medium text-gray-400 dark:text-gray-500">{{ $item->updated_at->diffForHumans() }}</span>
                                </div>
                                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400 line-clamp-1">
                                    {{ $latestMessage?->message ? \Illuminate\Support\Str::limit($latestMessage->message, 50) : '✨ No messages yet' }}
                                </p>
                            </div>
                        </a>
                    @empty
                        <div class="flex flex-col items-center justify-center py-12 px-4 text-center">
                            <div class="w-16 h-16 rounded-full bg-gray-100 dark:bg-gray-800 flex items-center justify-center mb-3">
                                <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                                </svg>
                            </div>
                            <p class="text-sm text-gray-500 dark:text-gray-400">No conversations found</p>
                            <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">When conversations start, they will appear here</p>
                        </div>
                    @endforelse
                </div>
            </aside>

            <!-- Main panel - Empty State -->
            <section class="flex-1 flex flex-col h-full bg-white dark:bg-gray-900 min-w-0">
                <div class="flex-1 flex items-center justify-center">
                    <div class="text-center px-8">
                        <div class="w-24 h-24 rounded-full bg-gradient-to-br from-indigo-100 to-purple-100 dark:from-indigo-900/30 dark:to-purple-900/30 flex items-center justify-center mx-auto mb-6">
                            <svg class="w-12 h-12 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                            </svg>
                        </div>
                        <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-2">Select a Conversation</h2>
                        <p class="text-gray-600 dark:text-gray-400 mb-8">
                            Click on a conversation from the left panel to start messaging with creators and negotiate package details.
                        </p>
                        
                        @if ($sidebarConversations->isEmpty())
                            <div class="bg-gradient-to-r from-indigo-50 to-purple-50 dark:from-indigo-950/30 dark:to-purple-950/30 rounded-xl p-6 mb-6">
                                <p class="text-sm text-gray-700 dark:text-gray-300 mb-4">
                                    You don't have any active conversations yet. Start by negotiating with creators!
                                </p>
                                <a href="{{ route('frontend.packages.index') }}" class="inline-flex items-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-2 px-6 rounded-xl transition">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                    </svg>
                                    Browse Creators
                                </a>
                            </div>
                        @else
                            <div class="flex items-center justify-center gap-2 text-sm text-gray-500 dark:text-gray-400">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13 10V3L4 14h7v7l9-11h-7z" />
                                </svg>
                                <span>You have {{ $sidebarConversations->count() }} conversation{{ $sidebarConversations->count() !== 1 ? 's' : '' }} available</span>
                            </div>
                        @endif
                    </div>
                </div>
            </section>
        </div>
    </div>
@endsection
