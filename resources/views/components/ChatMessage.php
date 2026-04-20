<?php

namespace App\View\Components;

use Illuminate\View\Component;
use Illuminate\View\View;

class ChatMessage extends Component
{
    public $message;
    public $isOwn;

    public function __construct($message)
    {
        $this->message = $message;
        $this->isOwn = $message->sender_user_id === auth()->id();
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

    public function getStatusIcon()
    {
        if (!$this->isOwn) return null;
        
        if ($this->message->seen_at) return 'check-double';
        if ($this->message->sent_at || $this->message->created_at) return 'check';
        
        return null;
    }

    public function getStatusColor()
    {
        if (!$this->isOwn) return '';
        return $this->message->seen_at ? 'text-blue-500' : 'text-gray-400';
    }

    public function render(): View
    {
        return view('components.chat-message');
    }
}