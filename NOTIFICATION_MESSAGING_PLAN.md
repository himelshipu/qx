# Notification and Messaging Plan

This is the single implementation plan for notification and messaging behavior across the dashboard and public site.

## Goals

- Show the right alerts to the right role.
- Keep dashboard and public behavior consistent.
- Use one unread-count rule for badges.
- Make badge counts decrease only when the user actually reads the item.
- Keep notification and message handling separate, but visually consistent.

## What To Show To Whom

### Admin and Dashboard Users

- Show system notifications for campaigns, orders, payments, support, and moderation.
- Show unread conversation count in the dashboard header and sidebar where conversations are reachable.
- Show badge counts for both notifications and unread messages.
- Do not show frontend-only brand/influencer messaging shortcuts that are not supported in dashboard UX.

### Brand Users

- Show campaign notifications, order notifications, payment/review notifications, and message notifications that belong to the brand.
- Show unread message count in the frontend header and conversations entry point.
- Show notification badge in the frontend bell dropdown.
- Keep brand-visible message actions limited to the brand-supported conversation flow.

### Influencer Users

- Show campaign application updates, work-status updates, brand review prompts, payout/payment updates, and message notifications where supported.
- Show unread notification badge in the frontend header.
- Show unread message count only where influencer messaging is supported.
- Do not show message buttons or conversation shortcuts where influencer messaging is intentionally disabled.

## Badge Rules

### Notification Badge

- Badge number = unread notifications for that user.
- Badge is shown on the bell icon in both dashboard and frontend headers.
- The count updates when notifications are fetched.
- The badge disappears when unread count is zero.

### Message Badge

- Badge number = unread conversations or unread messages, depending on the page surface.
- Dashboard sidebar/header may show a conversation count for admin, moderator, and brand users.
- Frontend header may show a message badge only on surfaces where messaging is supported.
- The message badge disappears when there are no unread messages.

## Read And Unread Rules

- Clicking a notification row marks that notification as read.
- Clicking a "View" action or opening the related page marks the notification as read.
- "Mark all as read" clears only the notification unread count.
- Opening a conversation should clear unread message state for that conversation participant.
- Badge counts must refresh immediately after read actions.

## Placement Rules

### Dashboard

- Use the backend header notification dropdown.
- Show notification badge on the bell.
- Show conversation badge in the dashboard sidebar or conversation entry.
- Keep notification and message counters visually separate.

### Public Site

- Use the frontend auth header notification dropdown.
- Show notification badge on the bell.
- Show message badge only on supported frontend surfaces.
- Keep the public header simple and role-aware.

## Data Rules

- Notifications come from the `notifications` table.
- Notification unread count is `is_read = false`.
- Message/unread conversation counts come from the conversation/message data model, not from notifications.
- Do not mix message unread counts into notification unread counts.

## Implementation Order

1. Centralize unread count calculation in one shared service or composer.
2. Pass badge counts into the dashboard header and frontend auth header.
3. Keep notification dropdown APIs for list content and unread count refresh.
4. Add message badge support to the relevant dashboard and frontend entry points.
5. Make read actions recalculate badge numbers immediately.
6. Validate that roles without messaging do not receive messaging badges.

## Acceptance Criteria

- Dashboard bell shows unread notification count.
- Frontend bell shows unread notification count.
- Dashboard conversation/message entry shows unread count where supported.
- Frontend message entry shows unread count only where supported.
- Badge numbers decrease to zero after read actions.
- No role sees a badge for a feature they cannot use.

## Implemented Notification Matrix (Role x Action)

This section is the authoritative summary of who receives what notification for each action across public site and dashboard.

### Campaign Actions

- Action: Admin assigns influencer(s) to a campaign (dashboard assign)
	- Recipient: Brand (campaign owner)
	- Notification: `type=campaign`, title `Influencers assigned to your campaign`
	- Action URL: campaign detail page

- Action: Admin assigns influencer(s) to a campaign (dashboard assign)
	- Recipient: Each assigned Influencer
	- Notification: `type=campaign`, title `You were invited to a campaign`
	- Action URL: campaign detail page

- Action: Admin creates or updates a campaign package for an influencer
	- Recipient: Package owner Influencer
	- Notification: `type=package`, titles vary by action
	- Action URL: package detail page

- Action: Admin toggles package active/inactive
	- Recipient: Package owner Influencer
	- Notification: `type=package`, title `Package status changed`
	- Action URL: package detail page

- Action: Admin deletes a package
	- Recipient: Package owner Influencer
	- Notification: `type=package`, title `Package deleted`
	- Action URL: none required

- Action: Influencer applies to a campaign
	- Recipient: Brand
	- Notification: `type=campaign`, title `New campaign application`
	- Action URL: campaign detail page

- Action: Influencer withdraws campaign application
	- Recipient: Brand
	- Notification: `type=campaign`, title `Campaign application withdrawn`
	- Action URL: campaign detail page

- Action: Brand accepts/counters/declines an application
	- Recipient: Influencer
	- Notification: `type=campaign`, titles vary by action
	- Action URL: campaign detail page

- Action: Influencer accepts/counters/declines brand offer
	- Recipient: Brand
	- Notification: `type=campaign`, titles vary by action
	- Action URL: campaign detail page

- Action: Influencer updates campaign task progress (in progress/delivered)
	- Recipient: Brand
	- Notification: `type=campaign`, title `Campaign work updated`
	- Action URL: campaign detail page

- Action: Brand approves/rejects campaign task work
	- Recipient: Influencer
	- Notification: `type=campaign`, title `Campaign task reviewed by brand`
	- Action URL: campaign detail page

- Action: Influencer submits campaign review for brand
	- Recipient: Brand
	- Notification: `type=review`, title `New brand review received`
	- Action URL: campaign detail page

### Order and Task Actions

- Action: Admin purchases a package for a brand from dashboard
	- Recipient: Brand who receives the package purchase
	- Notification: `type=package`, title `Package purchase recorded`
	- Action URL: frontend order detail page

- Action: Admin purchases a package for a brand from dashboard
	- Recipient: Influencer who owns the package
	- Notification: `type=package`, title `Your package was purchased`
	- Action URL: package detail page

- Action: Admin updates overall order status from dashboard
	- Recipient: Brand (order owner)
	- Notification: `type=order`, title `Order status updated by admin`
	- Action URL: frontend order detail page

- Action: Admin updates package order item status from dashboard
	- Recipient: Influencer (task owner)
	- Notification: `type=order`, title `Package task updated by admin`
	- Action URL: frontend order detail page

- Action: Admin updates package order item status from dashboard
	- Recipient: Brand (order owner)
	- Notification: `type=order`, title `Package item updated`
	- Action URL: frontend order detail page

- Action: Admin updates campaign sub-order status from dashboard
	- Recipient: Influencer (sub-order owner)
	- Notification: `type=order`, title `Campaign task updated by admin`
	- Action URL: frontend order detail page

- Action: Admin updates campaign sub-order status from dashboard
	- Recipient: Brand (order owner)
	- Notification: `type=order`, title `Campaign order task updated`
	- Action URL: frontend order detail page

- Action: Influencer updates package task status (in progress/delivered)
	- Recipient: Brand (order buyer)
	- Notification: `type=order`, title `Task status updated`
	- Action URL: order detail page

- Action: Brand approves/rejects delivered task (order item or sub-order)
	- Recipient: Influencer
	- Notification: `type=order`, title `Task decision received`
	- Action URL: order detail page

- Action: Brand submits task review for influencer
	- Recipient: Influencer
	- Notification: `type=review`, title `New task review received`
	- Action URL: order detail page

- Action: Admin marks package order item as paid
	- Recipient: Influencer
	- Notification: `type=payment`, title `Package payout recorded`
	- Action URL: frontend order detail page

- Action: Admin marks campaign sub-order as paid
	- Recipient: Influencer
	- Notification: `type=payment`, title `Campaign payout recorded`
	- Action URL: frontend order detail page

- Action: Influencer submits review for brand on completed order
	- Recipient: Brand
	- Notification: `type=review`, title `New brand review received`
	- Action URL: order detail page

### Conversation and Message Actions

- Action: Brand sends message in frontend conversation
	- Recipient: Assigned moderator (if assigned)
	- Notification: `type=message`, title `New conversation message`
	- Action URL: dashboard conversation page

- Action: Brand sends message in frontend conversation with no assigned moderator
	- Recipient: All admins and moderators
	- Notification: `type=message`, title `New conversation message`
	- Action URL: dashboard conversation page

- Action: User opens conversation thread
	- Recipient: N/A (state change)
	- Effect: unread messages for that user in that conversation are marked read (badge clears)

### Support Ticket Actions

- Action: Public user submits support ticket
	- Recipient: All admins and moderators
	- Notification: `type=support`, title `New support ticket submitted`
	- Action URL: dashboard support ticket detail page

- Action: Admin updates or reassigns a support ticket
	- Recipient: Ticket requester
	- Notification: `type=support`, title `Support ticket updated`
	- Action URL: support ticket page or support index

- Action: Admin updates or reassigns a support ticket
	- Recipient: Newly assigned admin/moderator
	- Notification: `type=support`, title `Support ticket assigned to you`
	- Action URL: dashboard support ticket detail page

### Role and Permission Actions

- Action: Admin assigns one or more roles to a user
	- Recipient: The affected user
	- Notification: `type=role`, title `Account access updated`
	- Action URL: none required; payload contains role names

- Action: Admin creates a user with an assigned role
	- Recipient: The created user
	- Notification: `type=role`, title `Account access updated`
	- Action URL: none required; payload contains role names

- Action: Admin updates a user’s role assignment
	- Recipient: The affected user
	- Notification: `type=role`, title `Account access updated`
	- Action URL: none required; payload contains role names

- Action: Admin assigns permissions to a role
	- Recipient: Every user who currently has that role
	- Notification: `type=permission`, title `Role permissions updated`
	- Action URL: none required; payload contains changed permission IDs

- Action: Admin toggles role active/inactive
	- Recipient: Every user who currently has that role
	- Notification: `type=role`, title `Role status changed`
	- Action URL: none required; payload contains role status

- Action: Admin toggles a user active/inactive
	- Recipient: The affected user
	- Notification: `type=account`, title `Account status updated`
	- Action URL: none required; payload contains status

## Read/Unread Behavior Summary

- Notifications:
	- Stored in `notifications` table with `is_read` and `read_at`.
	- `show` endpoint marks a notification as read before redirecting.
	- `markAsRead`, `markAsUnread`, and `markAllAsRead` are available on both frontend and dashboard notification controllers.

- Messages:
	- Unread state is message-level via `messages.read_at`.
	- Opening a conversation marks incoming unread messages in that conversation as read.
	- Header/sidebar message badges derive from unread conversation/message query scopes and drop as soon as read state is updated.