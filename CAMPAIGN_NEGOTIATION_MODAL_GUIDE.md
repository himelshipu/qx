# Campaign Application Negotiation Modal - Visual Guide

## BEFORE: Inline Buttons (Old Implementation)

```
Actions Column:
┌─────────────────────────────────────────────────────────────┐
│ [Message] [✓ Accept] [Counter $xxx] [✗ Decline]            │
│           Form       Form with input  Form                  │
│           Hidden     Visible input                          │
└─────────────────────────────────────────────────────────────┘

Issues:
- Cluttered layout in table column
- Counter form input takes up horizontal space
- Hard to see price context
- Three separate form submissions
- Poor mobile experience
- No clear visual hierarchy
```

## AFTER: Modal Dialog (New Implementation)

```
Actions Column:
┌──────────────────┐
│ [Message] [Negotiate] │
└──────────────────┘

When "Negotiate" clicked:
┌─────────────────────────────────────────┐
│ Negotiate with John Doe          [✕]    │
├─────────────────────────────────────────┤
│                                         │
│ Proposed Price                          │
│ ┌─────────────────────────────────────┐ │
│ │ $500.00                             │ │
│ └─────────────────────────────────────┘ │
│                                         │
│ Your Counter Offer (shown if exists)    │
│ ┌─────────────────────────────────────┐ │
│ │ $450.00                             │ │
│ └─────────────────────────────────────┘ │
│                                         │
│ Your Counter Offer (input field)        │
│ ┌─────────────────────────────────────┐ │
│ │ Enter your offer...                 │ │
│ └─────────────────────────────────────┘ │
│                                         │
├─────────────────────────────────────────┤
│ [✓ Accept] [Counter] [✕ Reject] [Close]│
└─────────────────────────────────────────┘

Benefits:
- Clean, focused modal dialog
- All information visible in one place
- Large input field for counter offer
- Clear action buttons with colors
- Loading states prevent double-submission
- Excellent mobile experience
- Single unified negotiation interface
- Clear price history context
```

## Modal States

### Default State
```
Modal Open, No Submission
- Counter input is empty (or pre-filled with last counter)
- All buttons are enabled
- User can make a decision
```

### While Submitting
```
User Clicked Accept/Counter/Reject
- All buttons show spinner + text
- Buttons are disabled
- Prevents accidental double submission
- Shows activity to user
```

### After Success
```
Server Responded Successfully
- Toast notification appears
- Modal automatically closes
- Page reloads after 500ms
- User sees updated status in table
```

### After Error
```
Server Returned Error
- Toast notification shows error message
- Modal stays open
- User can retry or close manually
- No page reload
```

## Input Validation

### Counter Button
```
Counter Input Validation:
- Required: Must have a value
- Type: Must be a number
- Range: Must be > 0
- Format: Currency (2 decimals)

Counter button:
- Disabled if input is empty or ≤ 0
- Enabled only with valid input
```

### Accept Button
```
No Input Required
- Always enabled (unless submitting)
- Accepts at current proposed rate
- Backend validates that a rate exists
```

### Reject Button
```
No Input Required
- Always enabled (unless submitting)
- Immediately declines application
```

## Backend Validation

Even though frontend validates, backend performs final validation:

```php
// Counter action
- Must be numeric and >= 0.01
- Sent to CampaignNegotiationService

// Accept action
- Checks that a valid offer exists
- Uses highest priority: influencer_offer → proposed_rate → brand_offer

// Reject action
- No additional validation needed
- Sets status to declined_by_brand
```

## Response Handling

### Success Response (200/201)
```json
{
  "success": true,
  "message": "Offer accepted and influencer approved successfully."
}
```
Action: Toast success, close modal, reload page

### Validation Error (422)
```json
{
  "success": false,
  "message": "Please enter a valid counter offer amount."
}
```
Action: Toast error, keep modal open, keep form data

### Server Error (500)
```json
{
  "success": false,
  "message": "Failed to update application. Please try again."
}
```
Action: Toast error, keep modal open, keep form data

## Accessibility Features

1. **Keyboard Navigation**
   - ESC key closes modal
   - Tab through form inputs and buttons
   - Enter submits focused button

2. **ARIA Attributes**
   - Modal has semantic role structure
   - Form inputs have labels
   - Buttons have clear text

3. **Focus Management**
   - Focus trapped in modal while open
   - Restored to trigger button after close

4. **Screen Readers**
   - Modal title announces purpose
   - Button text is descriptive
   - Status messages in toasts

## Color Scheme

```
Button Colors:
- Accept (Green):     bg-emerald-600 hover:bg-emerald-700
- Counter (Indigo):   bg-indigo-600 hover:bg-indigo-700  
- Reject (Red):       bg-red-600 hover:bg-red-700
- Close (Gray):       border-gray-300 text-gray-700

Dark Mode:
- All colors have dark: variants
- Modal background: dark:bg-gray-800
- Text: dark:text-white
```

## Performance Considerations

1. **Modal Creation**: Created dynamically in DOM, not lazy-loaded
2. **Event Delegation**: Single event listener for all negotiate buttons
3. **Alpine.js**: Lightweight reactive framework
4. **CSRF Token**: Retrieved from meta tag (no extra request)
5. **Toast System**: Uses existing Alpine store (no duplicate)
6. **Page Reload**: Optional, depends on success response

## Browser DevTools Debugging

### Console Checks
```javascript
// Check modal state
window.negotiationModalState

// Check if Alpine is initialized
window.Alpine

// Check for JS errors
// Look in Console tab for any red error messages
```

### Network Tab
```
POST /campaigns/{id}/applications/{appId}/update-status
- Headers: X-Requested-With: XMLHttpRequest
- Payload: action, brand_offer (if counter), _token
- Response: JSON with success/message
```

### Elements Inspector
```
Search for: data-app-id="123"
to find the negotiate button in DOM

Search for: x-data="negotiationModal()"
to find the modal component in DOM
```
