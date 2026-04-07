@extends('backend.layouts.app')

@section('title', 'Conversation')

@section('content')
    @php
        $user = auth()->user();

        $sidebarConversations = match ($user->user_type) {
            'brand' => \App\Models\Conversation::query()
                ->where('brand_user_id', $user->id),
            'moderator' => \App\Models\Conversation::query()
                ->where('handled_by_user_id', $user->id),
            default => \App\Models\Conversation::query(),
        };

        $sidebarConversations = $sidebarConversations
            ->with([
                'influencer.user',
                'brandUser',
                'handledBy',
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

        $getPartyForSidebar = static function (\App\Models\Conversation $item) use ($user) {
            if ($user->user_type === 'brand') {
                return $item->influencer?->user;
            }

            return $item->brandUser;
        };

        $currentTitle =
            $user->user_type === 'brand'
                ? ($conversation->influencer->display_name ?? $conversation->influencer->user->name)
                : $conversation->brandUser->name;
        
        // Helper to get status color
        $getOrderStatusColor = function ($status) {
            return match($status) {
                'pending' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400',
                'processing' => 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400',
                'shipped' => 'bg-purple-100 text-purple-800 dark:bg-purple-900/30 dark:text-purple-400',
                'delivered' => 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400',
                'cancelled' => 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400',
                default => 'bg-gray-100 text-gray-800 dark:bg-gray-800 dark:text-gray-400',
            };
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
                        <button class="lg:hidden p-1.5 rounded-lg text-gray-500 hover:bg-gray-100 dark:hover:bg-gray-800 transition">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                </header>

                <div class="flex-1 overflow-y-auto p-3 space-y-1.5">
                    @forelse ($sidebarConversations as $item)
                        @php
                            $party = $getPartyForSidebar($item);
                            $partyName = $party?->name ?? 'Unknown User';
                            $partyAvatar = filled($party?->profile_image_path ?? null) ? image_url($party->profile_image_path) : null;
                            $latestMessage = $item->messages->first();
                            $isActive = $item->id === $conversation->id;
                        @endphp

                        <a href="{{ route('dashboard.conversations.show', $item) }}"
                            class="flex items-start gap-3 rounded-xl p-3 transition-all duration-150 group
                                {{ $isActive 
                                    ? 'bg-white dark:bg-gray-800/80 shadow-sm ring-1 ring-indigo-200 dark:ring-indigo-800' 
                                    : 'hover:bg-white/60 dark:hover:bg-gray-800/40' }}">
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

            <!-- Main chat panel -->
            <section class="flex-1 flex flex-col h-full bg-white dark:bg-gray-900 min-w-0">
                <!-- Chat header with order info -->
                <div class="flex-shrink-0 border-b border-gray-200 bg-white/80 backdrop-blur-sm px-5 py-3 dark:border-gray-800 dark:bg-gray-900/80">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3 min-w-0">
                            <button class="lg:hidden p-1.5 -ml-1 rounded-lg text-gray-500 hover:bg-gray-100 dark:hover:bg-gray-800 transition">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 6h16M4 12h16M4 18h7" />
                                </svg>
                            </button>
                            <div class="h-10 w-10 rounded-full overflow-hidden bg-gradient-to-br from-indigo-100 to-purple-100 dark:from-indigo-900/30 dark:to-purple-900/30 flex items-center justify-center flex-shrink-0">
                                <span class="text-sm font-semibold text-indigo-700 dark:text-indigo-300">{{ $getInitials($currentTitle) }}</span>
                            </div>
                            <div class="min-w-0">
                                <h3 class="text-base font-semibold text-gray-900 dark:text-white truncate">{{ $currentTitle }}</h3>
                                <div class="flex items-center gap-2 text-xs text-gray-500 dark:text-gray-400">
                                    <span class="inline-flex items-center gap-1">
                                        <span class="relative flex h-2 w-2">
                                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75"></span>
                                            <span class="relative inline-flex rounded-full h-2 w-2 bg-green-500"></span>
                                        </span>
                                        Active
                                    </span>
                                    <span>•</span>
                                    <span class="truncate">{{ ucfirst(str_replace('_', ' ', $conversation->conversation_type)) }}</span>
                                </div>
                            </div>
                        </div>
                        @if($conversation->order_id)
                            <button id="toggleOrderDetails" class="p-2 rounded-lg text-gray-500 hover:bg-gray-100 dark:hover:bg-gray-800 transition flex-shrink-0" title="View Order Details">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                                </svg>
                            </button>
                        @endif
                    </div>
                </div>

                <!-- Order Details Panel (Collapsible) -->
                @if($conversation->order_id)
                    <div id="orderDetailsPanel" class="flex-shrink-0 border-b border-gray-200 dark:border-gray-800 bg-gradient-to-r from-indigo-50/50 to-purple-50/50 dark:from-indigo-950/20 dark:to-purple-950/20 hidden transition-all duration-300">
                        <div class="p-4">
                            <div class="flex items-center justify-between mb-3">
                                <h4 class="text-sm font-semibold text-gray-900 dark:text-white flex items-center gap-2">
                                    <svg class="w-4 h-4 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                                    </svg>
                                    Order Information
                                </h4>
                                <a href="{{ route('dashboard.orders.show', $conversation->order_id) }}" class="text-xs text-indigo-600 hover:text-indigo-700 dark:text-indigo-400 flex items-center gap-1">
                                    View Full Order
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                    </svg>
                                </a>
                            </div>
                            <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
                                <div class="bg-white/60 dark:bg-gray-800/40 rounded-lg px-3 py-2 backdrop-blur-sm">
                                    <p class="text-[10px] uppercase tracking-wider text-gray-500 dark:text-gray-400">Order ID</p>
                                    <p class="text-sm font-mono font-semibold text-gray-900 dark:text-white">#{{ $conversation->order_id }}</p>
                                </div>
                                <div class="bg-white/60 dark:bg-gray-800/40 rounded-lg px-3 py-2 backdrop-blur-sm">
                                    <p class="text-[10px] uppercase tracking-wider text-gray-500 dark:text-gray-400">Total Amount</p>
                                    <p class="text-sm font-semibold text-gray-900 dark:text-white">${{ number_format($conversation->order?->total_amount ?? 0, 2) }}</p>
                                </div>
                                <div class="bg-white/60 dark:bg-gray-800/40 rounded-lg px-3 py-2 backdrop-blur-sm">
                                    <p class="text-[10px] uppercase tracking-wider text-gray-500 dark:text-gray-400">Status</p>
                                    <span class="inline-flex px-2 py-0.5 text-xs font-semibold rounded-full {{ $getOrderStatusColor($conversation->order?->status ?? 'pending') }}">
                                        {{ ucfirst($conversation->order?->status ?? 'Pending') }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Assign Moderator (Admin only) -->
                @if (auth()->user()->user_type === 'admin' && !$conversation->handled_by_user_id)
                    <div class="flex-shrink-0 border-b border-yellow-200 dark:border-yellow-900/30 bg-yellow-50/50 dark:bg-yellow-900/20 p-4 transition-all duration-300">
                        <div class="flex items-center justify-between">
                            <div>
                                <h4 class="font-semibold text-yellow-900 dark:text-yellow-100 flex items-center gap-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM9 19c-4.3 0-8 1.343-8 3v2h16v-2c0-1.657-3.7-3-8-3z" />
                                    </svg>
                                    Assign Moderator
                                </h4>
                                <p class="mt-1 text-xs text-yellow-800 dark:text-yellow-300">This conversation needs a moderator to handle influencer responses.</p>
                            </div>
                        </div>
                        <form action="{{ route('dashboard.conversations.assign-moderator', $conversation) }}" method="POST" class="mt-3 flex gap-2">
                            @csrf
                            <select name="moderator_user_id"
                                class="flex-1 rounded-lg border border-yellow-300 bg-white px-3 py-2 text-sm dark:border-yellow-700 dark:bg-gray-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-yellow-400 dark:focus:ring-yellow-600"
                                required>
                                <option value="">Select a moderator...</option>
                                @foreach (\App\Models\User::where('user_type', 'moderator')->get() as $mod)
                                    <option value="{{ $mod->id }}">{{ $mod->name }}</option>
                                @endforeach
                            </select>
                            <button type="submit"
                                class="rounded-lg bg-yellow-600 px-4 py-2 text-sm font-medium text-white transition hover:bg-yellow-700 focus:outline-none focus:ring-2 focus:ring-yellow-500 dark:bg-yellow-700 dark:hover:bg-yellow-800">
                                Assign
                            </button>
                        </form>
                    </div>
                @elseif (auth()->user()->user_type === 'admin' && $conversation->handled_by_user_id)
                    <div class="flex-shrink-0 border-b border-green-200 dark:border-green-900/30 bg-green-50/50 dark:bg-green-900/20 p-4">
                        <div class="flex items-center justify-between">
                            <div>
                                <h4 class="font-semibold text-green-900 dark:text-green-100 flex items-center gap-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    Moderator Assigned
                                </h4>
                                <p class="mt-1 text-sm text-green-800 dark:text-green-300">
                                    <span class="font-medium">{{ $conversation->handledBy->name }}</span> is handling this conversation
                                </p>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Messages area - scrollable -->
                <div id="messagesContainer" class="flex-1 overflow-y-auto p-5 space-y-4 bg-gradient-to-b from-gray-50 to-white dark:from-gray-950/30 dark:to-gray-900">
                    @forelse ($messages as $message)
                        @php
                            $isOwn = $message->sender_user_id === auth()->id();
                            $senderName = $message->sender?->name ?? 'Unknown';
                            $senderAvatar = filled($message->sender?->profile_image_path ?? null) ? image_url($message->sender->profile_image_path) : null;
                            $time = $message->created_at->format('g:i A');
                        @endphp

                        <div class="flex {{ $isOwn ? 'justify-end' : 'justify-start' }} animate-fade-in">
                            <div class="flex max-w-[85%] md:max-w-[75%] items-end gap-2 {{ $isOwn ? 'flex-row-reverse' : '' }}">
                                @if (!$isOwn)
                                    <div class="h-8 w-8 shrink-0 rounded-full overflow-hidden bg-gradient-to-br from-indigo-100 to-purple-100 dark:from-indigo-900/30 dark:to-purple-900/30 flex items-center justify-center text-xs font-semibold text-indigo-700">
                                        @if ($senderAvatar)
                                            <img src="{{ $senderAvatar }}" alt="{{ $senderName }}" class="h-full w-full object-cover">
                                        @else
                                            <span>{{ $getInitials($senderName) }}</span>
                                        @endif
                                    </div>
                                @endif
                                <div class="group relative">
                                    <div class="rounded-2xl px-4 py-2.5 shadow-sm transition-all duration-150
                                        {{ $isOwn 
                                            ? 'bg-indigo-600 text-white rounded-br-sm' 
                                            : 'bg-white text-gray-900 dark:bg-gray-800 dark:text-white rounded-bl-sm border border-gray-100 dark:border-gray-700' }}">
                                        <p class="text-sm leading-relaxed whitespace-pre-line break-words">{{ $message->message }}</p>
                                    </div>
                                    <div class="mt-1 flex items-center gap-1.5 text-[10px] font-medium px-1
                                        {{ $isOwn ? 'justify-end text-gray-400' : 'justify-start text-gray-400' }}">
                                        <span>{{ $time }}</span>
                                        <span>•</span>
                                        <span class="{{ $isOwn ? 'text-indigo-400' : 'text-gray-500' }}">{{ $isOwn ? 'You' : $senderName }}</span>
                                        @if ($isOwn)
                                            <svg class="w-3 h-3 text-indigo-400" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" fill-rule="evenodd"></path>
                                            </svg>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="flex flex-col items-center justify-center h-full text-center py-12">
                            <div class="w-20 h-20 rounded-full bg-gray-100 dark:bg-gray-800 flex items-center justify-center mb-4">
                                <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                                </svg>
                            </div>
                            <p class="text-sm font-medium text-gray-900 dark:text-white">No messages yet</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Send a message to start the conversation</p>
                        </div>
                    @endforelse
                </div>

                <!-- Typing indicator -->
                <div id="typingIndicator" class="flex-shrink-0 px-5 py-2 hidden">
                    <div class="flex items-center gap-2 text-xs text-gray-500 dark:text-gray-400">
                        <div class="flex gap-0.5">
                            <span class="w-1.5 h-1.5 bg-gray-400 rounded-full animate-bounce" style="animation-delay: 0s"></span>
                            <span class="w-1.5 h-1.5 bg-gray-400 rounded-full animate-bounce" style="animation-delay: 0.2s"></span>
                            <span class="w-1.5 h-1.5 bg-gray-400 rounded-full animate-bounce" style="animation-delay: 0.4s"></span>
                        </div>
                        <span>Someone is typing...</span>
                    </div>
                </div>

                @if ($messages->hasPages())
                    <div class="flex-shrink-0 border-t border-gray-200 bg-white px-4 py-2 dark:border-gray-800 dark:bg-gray-900">
                        {{ $messages->links() }}
                    </div>
                @endif

                <!-- Message input - always visible at bottom -->
                <div class="flex-shrink-0 border-t border-gray-200 bg-white/80 backdrop-blur-sm p-4 dark:border-gray-800 dark:bg-gray-900/80">
                    <form action="{{ route('dashboard.conversations.storeMessage', $conversation) }}" method="POST" class="flex gap-2">
                        @csrf
                        <div class="flex-1 relative">
                            <input type="text" name="message" placeholder="Write your message..." autocomplete="off"
                                class="w-full rounded-xl border border-gray-200 bg-gray-50 px-4 py-2.5 pr-12 text-sm focus:border-indigo-400 focus:outline-none focus:ring-2 focus:ring-indigo-100 dark:border-gray-700 dark:bg-gray-800 dark:text-white dark:focus:ring-indigo-900/50 transition">
                            <button type="button" class="absolute right-2 top-1/2 -translate-y-1/2 p-1.5 rounded-lg text-gray-400 hover:text-indigo-500 transition">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" />
                                </svg>
                            </button>
                        </div>
                        <button type="submit"
                            class="inline-flex items-center justify-center rounded-xl bg-indigo-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-900">
                            Send
                            <svg class="w-4 h-4 ml-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                            </svg>
                        </button>
                    </form>
                </div>
            </section>
        </div>
    </div>

    

    <script>
        // Auto-scroll to bottom
        const container = document.getElementById('messagesContainer');
        if (container) {
            container.scrollTop = container.scrollHeight;
        }
        
        // Auto-focus input on load
        const messageInput = document.querySelector('input[name="message"]');
        if (messageInput) {
            messageInput.focus();
        }
        
        // Toggle order details panel
        const toggleBtn = document.getElementById('toggleOrderDetails');
        const orderPanel = document.getElementById('orderDetailsPanel');
        
        if (toggleBtn && orderPanel) {
            toggleBtn.addEventListener('click', function() {
                orderPanel.classList.toggle('hidden');
            });
        }
        
        // Typing indicator simulation
        const typingIndicator = document.getElementById('typingIndicator');
        
        if (messageInput && typingIndicator) {
            let typingTimeout;
            messageInput.addEventListener('input', function() {
                typingIndicator.classList.remove('hidden');
                clearTimeout(typingTimeout);
                typingTimeout = setTimeout(() => {
                    typingIndicator.classList.add('hidden');
                }, 1000);
            });
        }
    </script>
@endsection