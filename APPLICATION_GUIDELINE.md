# Application Guideline

This document is the working guideline for future changes in this application.

## What To Preserve

- Keep the dashboard separated from frontend brand/influencer flows.
- Keep campaign and package workflows consistent, but do not force them into identical storage models.
- Keep reviews immutable after submission.
- Keep task transitions forward-only for normal users.
- Keep user-facing copy clear and role-specific.

## How To Build Features

- Put business logic in services or controller actions, not in Blade.
- Put display shaping in view models.
- Use routes and controllers that match the role boundary.
- When a workflow appears on a page, show only the actions that role can actually perform.
- Reuse the same card, badge, and timeline language across pages.

## Campaign-Specific Rules

- Influencers should only see their own progress.
- Brands should see all campaign participants and can approve or reject delivered work.
- Influencer review of the brand should appear only after approval/completion.
- If a review already exists, show the submitted state instead of the form.
- Do not show messaging controls to influencer users where they are not supported.

## Order-Specific Rules

- Campaign orders should not show package-only summary blocks.
- Package and campaign tasks should not duplicate the same status in multiple places.
- The unified task section is the primary order workflow surface.

## Blade Rules

- Keep Blade declarative.
- Avoid raw PHP blocks unless there is no practical alternative.
- Do not query the database in views.
- Do not duplicate the same UI state in multiple sections.

## Status Rules

- Normalize legacy values before rendering.
- Store the canonical status once, then map it for display.
- Do not reintroduce deliverable wording in user-facing flows.

## Review Rules

- Brand review of influencer work belongs with the task card.
- Influencer review of brand belongs with the same task card once work is approved.
- Review forms should use the same fields: rating, optional title, optional comment.

## Change Checklist

Before shipping a workflow change:

1. Check role access.
2. Check status transitions.
3. Check whether the action appears in the correct card or section.
4. Check for duplicate UI paths.
5. Validate Blade and controller errors.