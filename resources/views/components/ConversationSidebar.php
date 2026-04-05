<?php

namespace App\View\Components;

use Illuminate\View\Component;
use Illuminate\View\View;

class ConversationSidebar extends Component
{
    public $conversations;
    public $currentConversation;
    public $user;

    public function __construct($conversations, $currentConversation, $user)
    {
        $this->conversations = $conversations;
        $this->currentConversation = $currentConversation;
        $this->user = $user;
    }

    public function getPartyForSidebar($item)
    {
        if ($this->user->user_type === 'brand') {
            return $item->creator?->user;
        }
        return $item->brandUser;
    }

    public function getInitials($name)
    {
        $name = trim((string) $name);
        if ($name === '') return 'NA';
        
        $parts = preg_split('/\s+/', $name) ?: [];
        $first = mb_substr($parts[0] ?? '', 0, 1);
        $last = count($parts) > 1 ? mb_substr($parts[count($parts) - 1], 0, 1) : '';
        
        return mb_strtoupper($first . $last);
    }

    public function render(): View
    {
        return view('components.conversation-sidebar');
    }
}