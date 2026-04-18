# Communication System Reference (Admin Dashboard Canonical Pattern)

This document defines implementation rules for the Communication section in the admin dashboard sidebar.
Use it as the canonical reference for file/folder structure, naming, layering, badge behavior, and update patterns for Conversations, Support Tickets, and Notifications.

## 1) File and Folder Structure

### 1.1 Implemented Core Files

```text
app/
  Helpers/
    MenuHelper.php
  Http/
    Controllers/
      Backend/
        CommunicationBadgeController.php
        NotificationController.php
        SupportTicketController.php
      ConversationController.php
    Controllers/
      Backend/
        ConversationController.php
  Models/
    Conversation.php
    Message.php
    SupportTicket.php
    Notification.php
  Repositories/
    Contracts/
      CommunicationBadgeRepositoryInterface.php
      ConversationRepositoryInterface.php
      SupportTicketRepositoryInterface.php
      NotificationRepositoryInterface.php
    Eloquent/
      EloquentCommunicationBadgeRepository.php
      EloquentConversationRepository.php
      EloquentSupportTicketRepository.php
      EloquentNotificationRepository.php
  Services/
    Admin/
      CommunicationBadgeService.php
      SupportTicketService.php
      NotificationService.php
    Auth/
      ConversationService.php

  Http/
    Requests/
      Backend/
        Conversation/
          AssignModeratorRequest.php
          StoreConversationMessageRequest.php
        SupportTicket/
          BulkUpdateSupportTicketRequest.php
          UpdateSupportTicketRequest.php

config/
  communication.php

resources/
  views/
    components/
      backend/
        shell/
          sidebar.blade.php
    backend/
      pages/
        conversations/
          index.blade.php
          show.blade.php
        support-tickets/
          index.blade.php
        notifications/
          index.blade.php
  js/
    admin/
      communication-sidebar-badges.js
```

### 1.2 Target Standardized Structure

```text
app/
  Http/
    Controllers/
      Backend/
        CommunicationBadgeController.php
        NotificationController.php
        SupportTicketController.php
        ConversationController.php
    Requests/
      Backend/
        Communication/
          MarkNotificationReadRequest.php
          MarkNotificationUnreadRequest.php
          UpdateSupportTicketRequest.php
  Models/
    Conversation.php
    Message.php
    SupportTicket.php
    Notification.php
  Repositories/
    Contracts/
      CommunicationBadgeRepositoryInterface.php
    Eloquent/
      EloquentCommunicationBadgeRepository.php
  Services/
    Admin/
      CommunicationBadgeService.php
      NotificationService.php
      SupportTicketService.php
      ConversationService.php

config/
  communication.php

resources/
  views/
    components/
      backend/
        shell/
          sidebar.blade.php
    backend/
      pages/
        conversations/
          index.blade.php
          show.blade.php
        support-tickets/
          index.blade.php
        notifications/
          index.blade.php
  js/
    admin/
      communication-sidebar-badges.js
      conversations-dashboard.js
      support-tickets-dashboard.js
      notifications-dashboard.js
```

## 2) Required Pattern

1. Keep controller -> service -> repository -> model scope layering for sidebar badge counts.
2. Keep communication badge queries out of the sidebar Blade and out of MenuHelper business logic.
3. Use model scopes for unread/new filtering where possible.
4. Keep badge semantics focused on new/unread items, not total record counts.
5. Keep live refresh logic in a dedicated admin JS module.

## 3) Required Badge Behavior

1. Conversations badge shows conversations with unread messages requiring attention.
2. Support Tickets badge shows new/unassigned open tickets.
3. Notifications badge shows unread notifications for the current dashboard user.
4. Badge values must refresh automatically from the server.
5. Badge values must hide when zero.
6. Badge values should cap visually at `99+`.

## 4) Route and Naming Pattern

### 4.1 Required Dashboard Routes

1. dashboard.conversations.index
2. dashboard.conversations.show
3. dashboard.conversations.storeMessage
4. dashboard.support-tickets.index
5. dashboard.support-tickets.show
6. dashboard.support-tickets.update
7. dashboard.notifications.index
8. dashboard.notifications.mark-as-read
9. dashboard.notifications.mark-as-unread
10. dashboard.notifications.mark-all-as-read
11. dashboard.api.sidebar-badges

### 4.2 Naming Rules

1. Use singular names for controllers and models: CommunicationBadgeController, CommunicationBadgeService.
2. Use plural names for resource routes: conversations, support-tickets, notifications.
3. Use a separate badge API route for sidebar polling.
4. Use badge keys that match module intent: conversations, support_tickets, notifications.

## 5) Badge Source Rules

1. Conversations count must represent unread attention items, not total conversation rows.
2. Support ticket count must represent new/open tickets requiring triage, not all tickets.
3. Notification count must represent unread notifications for the signed-in dashboard user.
4. Counts should be computed server-side and refreshed by polling.
5. Avoid hard-coded values in Blade such as `22` or `42`.

## 6) Model and Query Rules

1. Conversation model should expose unread-message dashboard scopes.
2. SupportTicket model should expose a new-ticket dashboard scope.
3. Notification model should expose unread/read scopes.
4. Repository should compose the badge counts using those scopes.
5. Queries should be role-aware for conversations and support tickets.

## 7) Blade and JS Rules

1. Sidebar Blade should render count badges only from numeric values supplied by the menu builder.
2. Sidebar Blade should not contain hard-coded badge numbers.
3. Sidebar root should expose the badge refresh route and interval through `data-*` attributes.
4. communication-sidebar-badges.js should poll the badge endpoint and patch visible counts.
5. Badge nodes should be addressable by `data-sidebar-badge-key`.

## 8) AI Agent Instruction Block

1. Never hard-code sidebar badge numbers.
2. Never compute communication badge totals inside Blade.
3. Never use total record counts when the badge is meant to show unread/new items.
4. Use a service and repository pair for badge aggregation.
5. Use a dashboard API route for live updates.
6. Validate and build before completion.

## 9) Delivery Checklist

- [x] Communication badge repository exists.
- [x] Communication badge service exists.
- [x] Communication badge API route exists.
- [x] Sidebar uses live badge values instead of static numbers.
- [x] Sidebar badges update via polling JS.
- [x] Conversation/support ticket/notification dashboard scopes exist.
- [x] No hard-coded sidebar badge numbers remain.
- [x] JS build passes.
- [x] Diagnostics are clean for touched files.
- [x] Conversation, support ticket, and notification controllers now delegate to services/repositories.
