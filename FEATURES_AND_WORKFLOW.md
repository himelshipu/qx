# QX Platform - Features & Workflow Documentation

## Overview

QX is a Laravel-based influencer marketplace platform connecting brands with creators. It facilitates package purchasing, negotiations, and order management through a moderated communication system.

---

## Table of Contents

1. [Architecture](#architecture)
2. [User Types & Roles](#user-types--roles)
3. [Feature Workflows](#feature-workflows)
4. [Database Schema](#database-schema)
5. [Coding Patterns & Standards](#coding-patterns--standards)
6. [Component Communication](#component-communication)

---

## Architecture

### Technology Stack

- **Backend**: Laravel 12.56.0
- **Frontend**: Blade Templates + Alpine.js 3.x
- **Database**: MySQL
- **Authentication**: Laravel Breeze (custom user_type)
- **Asset Pipeline**: Vite

### Folder Structure

```
app/
├── Models/           # Eloquent models
├── Http/Controllers/ # Business logic
│   ├── Backend/     # Admin dashboard controllers
│   ├── CartController.php
│   └── ConversationController.php
├── Helpers/         # Utility helpers
└── Listeners/       # Event listeners
resources/
├── views/
│   ├── frontend/    # Public-facing pages
│   └── backend/     # Admin dashboard pages
└── js/
database/
├── migrations/      # Schema definitions
└── seeders/
routes/
├── web.php         # All routes (public + authenticated)
└── auth.php        # Authentication routes
```

---

## User Types & Roles

### 1. **Brand** (user_type = 'brand')

**Purpose**: Purchase creator packages and negotiate custom deals

**Permissions**:

- View creator profiles and packages
- Add packages to cart
- Proceed to checkout (create orders)
- Negotiate packages (start conversations)
- View own conversations
- Send messages in conversations
- View own carts

**Key Routes**:

- `GET /creator/{slug}` - View creator profile
- `POST /cart/add/{package}` - Add to cart
- `POST /cart/checkout` - Proceed to checkout
- `GET /dashboard/carts` - View own cart
- `GET /creator/{creator}/start-negotiation` - Start negotiation
- `GET /dashboard/conversations` - View conversations

---

### 2. **Creator** (user_type = 'creator')

**Purpose**: Offer packages and negotiate with brands (via moderators)

**Permissions**:

- Create and manage packages
- View portfolio items
- Cannot directly message brands (mediated by moderators)
- Cannot access conversations view

**Key Routes**:

- `GET /creator/{slug}` - Public profile
- Dashboard: Package & Portfolio management

---

### 3. **Moderator** (user_type = 'moderator')

**Purpose**: Mediate conversations between brands and creators

**Permissions**:

- View conversations assigned to them
- Send/receive messages
- Act as intermediary between brand and creator
- Cannot assign moderators (admin-only)

**Key Routes**:

- `GET /dashboard/conversations` - Only conversations assigned to them
- `GET /dashboard/conversations/{conversation}` - View conversation
- `POST /dashboard/conversations/{conversation}/messages` - Send message

---

### 4. **Admin/Superadmin** (user_type = 'admin')

**Purpose**: System administration and moderation oversight

**Permissions**:

- View ALL conversations (regardless of assignment)
- Send messages in any conversation
- Assign moderators to conversations
- Manage all carts and orders
- Full system access

**Key Routes**:

- `GET /dashboard/carts` - View all carts
- `GET /dashboard/carts/{cart}` - View specific cart
- `GET /dashboard/conversations` - View all conversations
- `POST /dashboard/conversations/{conversation}/assign-moderator` - Assign moderator

---

## Feature Workflows

### 1. **Shopping Cart & Checkout Workflow**

#### **User Flow: Brand Adds Package to Cart**

```
Brand views Creator Profile (/creator/{slug})
    ↓
Sees package options with "Add to Cart" button
    ↓
Clicks "Add to Cart"
    ↓
AJAX request: POST /cart/add/{packageId}
    ↓
CartController@addToCart() creates/updates Cart containing CartItems
    ↓
Browser shows success message
    ↓
Cart data stored in session/database
    ↓
Brand sees cart count updated in header
```

#### **Database Flow: Cart Creation**

```php
// Model: Cart
- id (PK)
- user_id (FK → users)
- status: 'active' | 'completed' | 'abandoned'
- created_at, updated_at

// Model: CartItem
- id (PK)
- cart_id (FK → carts)
- package_id (FK → packages)
- unit_price: float
- quantity: int
- currency: string (USD, EUR, etc.)
- created_at, updated_at
```

#### **Controller Logic**

```php
// CartController::addToCart()
1. Get authenticated brand user
2. Find or create cart for user with status 'active'
3. Check if package already in cart
4. Add or update CartItem
5. Return JSON response with updated cart count
```

#### **Checkout Flow**

```
Brand clicks "Proceed to Checkout"
    ↓
GET /cart/checkout
    ↓
CartController@checkout() validates cart is not empty
    ↓
Loads checkout view with cart items
    ↓
Brand reviews items, enters delivery details
    ↓
Clicks "Complete Order"
    ↓
POST /cart/complete-checkout
    ↓
CartController@completeCheckout():
  - For each CartItem:
    - Create Order with brand_user_id
    - Create SubOrder with creator_id
    - Create OrderItems with pricing details
    - Create Conversation for negotiation
  - Clear cart (mark as 'completed')
  - Redirect to conversations
    ↓
Brand lands in conversations dashboard
    ↓
Conversation with creator is active/pending moderator assignment
```

#### **Cart Management (Admin/Brand View)**

```
Admin: GET /dashboard/carts
  → CartManagerController@index()
  → Shows ALL brand carts with:
    - Brand name
    - Item count
    - Total value
    - Status
    - Created date
    → Grid with filters & search

Brand: GET /dashboard/carts
  → CartManagerController@index()
  → Shows ONLY own cart
  → Can view details, remove items

Cart Detail Page: GET /dashboard/carts/{cart}
  → CartManagerController@show()
  → Authorization: Brand sees own | Admin sees all
  → Displays:
    - Cart items with package details
    - Remove button (for cart owner)
    - Order summary
    - Checkout button (if not admin)
```

#### **Frontend Implementation**

**View**: `resources/views/backend/commerce/carts/show.blade.php`

```blade
<!-- Remove button for each item (brands only) -->
<form method="POST" action="{{ route('cart.remove', $item) }}">
    @csrf
    @method('DELETE')
    <button type="submit">Remove</button>
</form>

<!-- Checkout form (brands only) -->
<form method="POST" action="{{ route('cart.checkout') }}">
    @csrf
    <button type="submit">Proceed to Checkout</button>
</form>
```

---

### 2. **Creator Profile & Package Negotiation Workflow**

#### **Creator Profile Page Architecture**

**File**: `resources/views/frontend/pages/creator-profile.blade.php`

**Data Initialization**:

```php
// Controller prepares data
$displayName = $creator->display_name ?? $creator->user->name;
$profileImageUrl = image_url($creator->user?->profile_image_path);
$packageCards = $packages->map(fn($p) => [...package data...]);
$packageTabs = ['All', 'Instagram', 'TikTok', 'UGC'];
```

**Alpine.js Component**:

```javascript
// Function-based initialization (avoids inline x-data bloat)
function creatorProfileData() {
    return {
        // State
        openDropdown: false,
        selectedPackageKey: initialPackageKey,
        activeTab: 'All',
        packages: packageCards,
        packageTabs: packageTabs,
        isAuthenticated: bool,
        userType: 'brand' | 'creator' | 'moderator' | 'admin',

        // Computed properties
        get filteredPackages() {
            return this.activeTab === 'All'
                ? this.packages
                : this.packages.filter(p => p.category === this.activeTab);
        },

        get selectedPackage() {
            return this.packages.find(p => p.key === this.selectedPackageKey);
        },

        // Methods
        selectPackage(key) { ... },
        negotiatePackage() { ... }
    };
}
```

#### **Negotiation Flow**

```
Brand views Creator Profile
    ↓
Sees "Add to Cart" OR "Negotiate a Package" button
    ↓
Clicks "Negotiate a Package"
    ↓
Check authentication:
  - Not logged in? → Redirect to /login
  - Logged in as brand? → Proceed to negotiation
  - Logged in as non-brand? → Show alert
    ↓
GET /creator/{creator}/start-negotiation
    ↓
ConversationController@startNegotiation($creator):
  1. Verify user is brand
  2. Check if conversation exists with this creator
  3. Create new conversation if needed:
     - brand_user_id = current user
     - creator_id = creator being viewed
     - status = 'pending' (awaiting moderator)
  4. Redirect to conversation detail
    ↓
GET /dashboard/conversations/{conversation}
    ↓
ConversationController@show():
  - Load conversation with messages
  - Display brand/creator info
  - Show message thread
  - Message input box
    ↓
Admin/Moderator:
  - Can assign moderator to conversation
  - Moderator takes over as intermediary
  - Brand and Creator communicate through moderator
```

#### **Package Selection UI**

**Tabs** (Alpine):

```blade
<template x-for="tabName in packageTabs">
    <button @click="activeTab = tabName"
            :class="activeTab === tabName ? 'active' : ''"
            x-text="tabName">
    </button>
</template>
```

**Package List** (Dynamic):

```blade
<template x-for="p in filteredPackages" :key="p.key">
    <div @click="selectPackage(p.key)"
         :class="selectedPackageKey === p.key ? 'selected' : ''">
        <span x-text="p.name"></span>
        <span x-text="p.price"></span>
    </div>
</template>
```

**Pricing Card** (Right Sidebar):

```blade
<div class="sticky-card">
    <span x-text="price" class="text-4xl font-bold"></span>
    <p x-text="selectedPackageDescription" class="description"></p>
    <button @submit.prevent="addToCart(selectedPackage.id)">Add to Cart</button>
    <button @click="negotiatePackage()">Negotiate a Package</button>
</div>
```

---

### 3. **Conversation & Moderation Workflow**

#### **Conversation Model Architecture**

```php
// Model: Conversation
- id (PK)
- brand_user_id (FK → users) - Brand who initiated
- creator_id (FK → creators) - Creator being contacted
- handled_by_user_id (FK → users, nullable) - Assigned moderator
- status: 'pending' | 'active' | 'resolved'
- created_at, updated_at

// Model: Message
- id (PK)
- conversation_id (FK → conversations)
- sender_user_id (FK → users)
- sender_role: 'brand' | 'creator' | 'moderator' | 'admin'
- on_behalf_of_creator_id (FK → creators, nullable)
- message: longText
- attachment_path: string (nullable)
- read_at: timestamp (nullable)
- created_at, updated_at
```

#### **Access Control Logic**

```php
// ConversationController@index()

if (user_type === 'brand') {
    // See only conversations they initiated
    Conversation::where('brand_user_id', user_id)
        ->with('creator', 'handledBy', 'messages')
        ->orderByDesc('updated_at')
        ->paginate();
}
elseif (user_type === 'moderator') {
    // See only conversations assigned to them
    Conversation::where('handled_by_user_id', user_id)
        ->with('creator', 'brandUser', 'messages')
        ->orderByDesc('updated_at')
        ->paginate();
}
elseif (user_type === 'admin') {
    // See ALL conversations for management
    Conversation::with('creator', 'brandUser', 'handledBy', 'messages')
        ->orderByDesc('updated_at')
        ->paginate();
}
```

#### **Message Flow**

```
Brand/Moderator sends message:
    ↓
POST /dashboard/conversations/{conversation}/messages
    ↓
ConversationController@storeMessage():
    1. Validate request (message content)
    2. Check authorization:
       - Brand can only message own conversations
       - Moderator can only message assigned conversations
       - Admin can message any conversation
    3. Create Message record:
       - sender_user_id = current user
       - sender_role = determination based on user_type
       - on_behalf_of_creator_id = if moderator, which creator
    4. Update Conversation.updated_at
    5. Notify other participants
    ↓
Redirect back to conversation with success
```

#### **Moderator Assignment (Admin-Only)**

```
Admin views conversation without moderator assigned
    ↓
Sees "Assign Moderator" button
    ↓
Clicks button → Opens moderator selection dropdown
    ↓
Selects moderator from available list
    ↓
GET /dashboard/conversations/{conversation}/assign-moderator
    ↓
ConversationController@assignModerator():
    1. Verify user is admin
    2. Validate moderator exists and has moderator role
    3. Update Conversation.handled_by_user_id
    4. Log assignment in conversation history
    ↓
Conversation now "active" with moderator
    ↓
Moderator receives notification
    ↓
Brand/Creator can now communicate through moderator
```

#### **Conversation Views**

**Index (`/dashboard/conversations`)**:

```blade
<table>
    <thead>
        <tr>
            <th>Brand</th>
            <th>Creator</th>
            <th>Status</th>
            <th>Moderator</th>
            <th>Last Message</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($conversations as $conv)
            <tr>
                <td>{{ $conv->brandUser->name }}</td>
                <td>{{ $conv->creator->user->name }}</td>
                <td>
                    <span :class="statusColor({{ $conv->status }})">
                        {{ ucfirst($conv->status) }}
                    </span>
                </td>
                <td>
                    @if ($conv->handledBy)
                        {{ $conv->handledBy->name }}
                    @else
                        <span class="text-gray-400">Unassigned</span>
                    @endif
                </td>
                <td>{{ $conv->messages->first()?->message }}</td>
                <td>
                    <a href="{{ route('dashboard.conversations.show', $conv) }}">
                        View
                    </a>
                </td>
            </tr>
        @endforeach
    </tbody>
</table>
```

**Detail (`/dashboard/conversations/{conversation}`)**:

```blade
<!-- Header with brand/creator info -->
<div class="conversation-header">
    <img src="{{ image_url($conv->brandUser->profile_image_path) }}" />
    <h2>{{ $conv->brandUser->name }}</h2>
    <span class="status">{{ $conv->status }}</span>
    @if (admin)
        <form method="POST" action="{{ route('dashboard.conversations.assign-moderator', $conv) }}">
            @csrf
            <select name="moderator_user_id">
                @foreach ($moderators as $mod)
                    <option value="{{ $mod->id }}">{{ $mod->name }}</option>
                @endforeach
            </select>
            <button type="submit">Assign Moderator</button>
        </form>
    @endif
</div>

<!-- Message thread -->
<div class="message-thread">
    @foreach ($messages as $msg)
        <div class="message" :class="msg.sender_role">
            <strong>{{ $msg->sender->name }}</strong> (@{{ $msg->sender_role }})
            @if ($msg->on_behalf_of_creator_id)
                <span class="badge">On behalf of ${msg->creator->user->name}</span>
            @endif
            <p>{{ $msg->message }}</p>
            <small>{{ $msg->created_at->format('M d, Y H:i') }}</small>
        </div>
    @endforeach
</div>

<!-- Message input (if authorized) -->
@if (canMessage)
    <form method="POST" action="{{ route('dashboard.conversations.storeMessage', $conv) }}">
        @csrf
        <textarea name="message" placeholder="Type your message..."></textarea>
        <button type="submit">Send Message</button>
    </form>
@endif
```

---

## Database Schema

### Key Tables

```sql
-- Users (with type discrimination)
CREATE TABLE users (
    id BIGINT PRIMARY KEY,
    name VARCHAR,
    email VARCHAR UNIQUE,
    user_type ENUM('brand', 'creator', 'moderator', 'admin'),
    slug VARCHAR UNIQUE,
    profile_image_path VARCHAR,
    bio TEXT,
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);

-- Packages (created by creators or admins)
CREATE TABLE packages (
    id BIGINT PRIMARY KEY,
    creator_id BIGINT FK,
    name VARCHAR,
    description TEXT,
    platform ENUM('instagram', 'tiktok', 'ugc', 'youtube'),
    base_price DECIMAL(10, 2),
    currency VARCHAR(3),
    delivery_days INT,
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);

-- Carts
CREATE TABLE carts (
    id BIGINT PRIMARY KEY,
    user_id BIGINT FK (brands only),
    status ENUM('active', 'completed', 'abandoned'),
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);

-- Cart Items
CREATE TABLE cart_items (
    id BIGINT PRIMARY KEY,
    cart_id BIGINT FK,
    package_id BIGINT FK,
    unit_price DECIMAL(10, 2),
    quantity INT DEFAULT 1,
    currency VARCHAR(3),
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);

-- Orders (created from cart checkout)
CREATE TABLE orders (
    id BIGINT PRIMARY KEY,
    brand_user_id BIGINT FK,
    cart_id BIGINT FK,
    total_amount DECIMAL(10, 2),
    currency VARCHAR(3),
    status ENUM('pending', 'accepted', 'in-progress', 'delivered', 'completed'),
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);

-- Sub-orders (one per creator per order)
CREATE TABLE sub_orders (
    id BIGINT PRIMARY KEY,
    order_id BIGINT FK,
    creator_id BIGINT FK,
    amount DECIMAL(10, 2),
    currency VARCHAR(3),
    status ENUM('pending', 'accepted', 'in-progress', 'delivered', 'completed'),
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);

-- Order Items (individual packages in order)
CREATE TABLE order_items (
    id BIGINT PRIMARY KEY,
    sub_order_id BIGINT FK,
    package_id BIGINT FK,
    unit_price DECIMAL(10, 2),
    quantity INT,
    currency VARCHAR(3),
    status ENUM('pending', 'in-progress', 'delivered', 'completed'),
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);

-- Conversations (mediated communication)
CREATE TABLE conversations (
    id BIGINT PRIMARY KEY,
    brand_user_id BIGINT FK,
    creator_id BIGINT FK,
    handled_by_user_id BIGINT FK (moderator/admin),
    status ENUM('pending', 'active', 'resolved'),
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);

-- Messages
CREATE TABLE messages (
    id BIGINT PRIMARY KEY,
    conversation_id BIGINT FK,
    sender_user_id BIGINT FK,
    sender_role ENUM('brand', 'creator', 'moderator', 'admin'),
    on_behalf_of_creator_id BIGINT FK (nullable),
    message LONGTEXT,
    attachment_path VARCHAR,
    read_at TIMESTAMP,
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);
```

---

## Coding Patterns & Standards

### 1. **Controller Pattern**

```php
// Structure: Thin Controller, Service/Repository Layer
class CartController extends Controller {
    // Constructor injection
    public function __construct(
        protected CartService $cartService,
        protected CartRepository $cartRepository
    ) {}

    // Single responsibility: Get and return data
    public function show(Cart $cart): View {
        $this->authorize('view', $cart); // Policy authorization

        $cart->load('items.package.creator.user');

        return view('backend.commerce.carts.show', [
            'cart' => $cart,
            'isAdmin' => auth()->user()->isAdmin()
        ]);
    }
}
```

### 2. **Model Relationships**

```php
// Eloquent Models with proper relationships
class Cart extends Model {
    protected $fillable = ['user_id', 'status'];

    public function user() {
        return $this->belongsTo(User::class);
    }

    public function items() {
        return $this->hasMany(CartItem::class);
    }

    public function scopeActive($query) {
        return $query->where('status', 'active');
    }
}

class Conversation extends Model {
    protected $fillable = ['brand_user_id', 'creator_id', 'handled_by_user_id', 'status'];

    public function brandUser() {
        return $this->belongsTo(User::class, 'brand_user_id');
    }

    public function creator() {
        return $this->belongsTo(Creator::class);
    }

    public function handledBy() {
        return $this->belongsTo(User::class, 'handled_by_user_id');
    }

    public function messages() {
        return $this->hasMany(Message::class);
    }
}
```

### 3. **Alpine.js Component Pattern**

**Instead of inline x-data**:

```javascript
// ❌ WRONG: Bloated x-data attribute
<section x-data="{
    openDropdown: false,
    packages: [...large array...],
    // ... 100+ lines of code
}">
```

**Use function-based approach**:

```javascript
// ✅ CORRECT: Clean, maintainable
<section x-data="creatorProfileData()">

@push('scripts')
<script>
function creatorProfileData() {
    return {
        openDropdown: false,
        packages: @js($packageCards),

        // Computed properties
        get selectedPackage() { ... },

        // Methods
        selectPackage(key) { ... }
    };
}
</script>
@endpush
```

### 4. **Blade Template Pattern**

```blade
<!-- Top-level PHP for data preparation -->
@php
    $displayName = $creator->display_name ?? $creator->user->name;
    $profileImageUrl = image_url($creator->user?->profile_image_path);
    $packageCards = $packages->map(fn($p) => [
        'id' => $p->id,
        'name' => $p->name,
        'price' => format_price($p->base_price, $p->currency),
    ]);
@endphp

<!-- Alpine component initialization -->
<section x-data="creatorProfileData()">
    <!-- Conditional rendering based on authentication -->
    @auth
        @if (auth()->user()->user_type === 'brand')
            <!-- Brand-specific UI -->
        @endif
    @else
        <!-- Guest UI -->
    @endauth
</section>

<!-- Scripts at end in @push('scripts') -->
@push('scripts')
    <script>
        function creatorProfileData() { ... }
    </script>
@endpush
```

### 5. **Authorization Pattern**

```php
// Model Policies
class CartPolicy {
    public function view(User $user, Cart $cart) {
        // Admin sees all, users see own
        return $user->isAdmin() || $user->id === $cart->user_id;
    }
}

// Controller usage
public function show(Cart $cart) {
    $this->authorize('view', $cart); // Uses policy

    // ... rest of logic
}
```

### 6. **Routing Pattern**

```php
// Grouped routes with middleware
Route::middleware(['auth', 'verified'])->group(function () {
    // Dashboard routes
    Route::prefix('dashboard')->name('dashboard.')->group(function () {
        Route::get('/carts', [CartManagerController::class, 'index'])->name('carts.index');
        Route::get('/conversations', [ConversationController::class, 'index'])->name('conversations.index');
    });

    // Public features for auth users
    Route::get('/creator/{creator}/start-negotiation', [ConversationController::class, 'startNegotiation'])
        ->name('conversations.start-negotiation');
});

// Public routes (no auth required)
Route::get('/creator/{slug}', [CreatorProfileController::class, 'show'])->name('creator.profile');
```

---

## Component Communication

### 1. **Frontend State Management**

```
Alpine.js Component (creator-profile)
├── State (openDropdown, selectedPackageKey, packages, etc.)
├── Computed Properties (filteredPackages, selectedPackage, price)
└── Methods (selectPackage(), negotiatePackage(), addToCart())
    ├── GET requests (startNegotiation)
    ├── POST requests (addToCart via @submit.prevent)
    └── Navigation (window.location.href)
```

### 2. **Authentication Flow**

```
User visits page
    ↓
Blade passes auth checks: auth()->check(), auth()->user()->user_type
    ↓
JavaScript receives: isAuthenticated, userType
    ↓
Alpine methods check these before executing actions
    ↓
Unauthenticated users redirected to login before sensitive operations
```

### 3. **Database Transaction Flow**

```
Brand adds to cart
    └→ CartController@addToCart()
        ├→ Find/Create Cart
        ├→ Add CartItem
        └→ Return JSON response
                ↓
Add package to cart
    └→ POST /cart/add/{packageId}
        ├→ Validate package exists
        ├→ Check authorization
        ├→ Create CartItem record
        └→ Update frontend cart count

Checkout
    └→ POST /cart/checkout + POST /cart/complete-checkout
        ├→ Validate cart not empty
        ├→ Create Order
        ├→ For each CartItem:
        │   ├→ Create SubOrder
        │   ├→ Create OrderItems
        │   └→ Create Conversation
        ├→ Clear cart (mark completed)
        └→ Redirect to conversations
```

---

## Error Handling

### Frontend Validation

```blade
<!-- HTML5 validation -->
<form @submit.prevent="addToCart(selectedPackage.id)" method="POST">
    <input required type="number" min="1" name="quantity">
    <button type="submit">Add to Cart</button>
</form>
```

### Backend Validation

```php
public function addToCart(Request $request) {
    $request->validate([
        'package_id' => 'required|exists:packages,id',
        'quantity' => 'required|integer|min:1'
    ]);

    // Process...
}
```

### Authorization Failures

```php
// Returns 403 Forbidden if not authorized
$this->authorize('view', $cart);

// Or manual check with abort
if ($user->id !== $cart->user_id && !$user->isAdmin()) {
    abort(403, 'Unauthorized');
}
```

---

## Performance Considerations

### 1. **Query Optimization**

```php
// Use eager loading to prevent N+1 queries
$conversations = Conversation::with([
    'creator.user',
    'brandUser',
    'handledBy',
    'messages' => fn($q) => $q->orderByDesc('created_at')->limit(1)
])->paginate();
```

### 2. **Frontend Optimization**

```blade
<!-- Use x-cloak to prevent FOUC (Flash of Unstyled Content) -->
<div x-show="showGallery" x-cloak>
    <!-- Gallery content hidden until Alpine initializes -->
</div>

<!-- Lazy load images -->
<img src="..." loading="lazy" alt="...">
```

### 3. **Caching Strategy**

```php
// Cache frequently accessed data
$packages = Cache::remember('creator_packages_' . $creator->id, 3600, fn() =>
    $creator->packages->load('creator.user')
);
```

---

## Testing Scenarios

### Cart Testing

- [ ] Add package to empty cart
- [ ] Add different quantities of same package
- [ ] Add different packages
- [ ] Remove item from cart
- [ ] Checkout with empty cart (should redirect)
- [ ] Checkout with items (should create order)

### Conversation Testing

- [ ] Brand creates conversation with creator
- [ ] Admin assigns moderator
- [ ] Brand sends message
- [ ] Moderator replies
- [ ] Creator response visible to brand (via moderator)

### Authorization Testing

- [ ] Brand cannot view other brand's cart
- [ ] Non-admin cannot assign moderators
- [ ] Creator cannot add to cart
- [ ] Unauthenticated users redirected to login

---

## Common Issues & Solutions

| Issue                         | Cause                   | Solution                               |
| ----------------------------- | ----------------------- | -------------------------------------- |
| "openDropdown is not defined" | x-data malformed        | Use `x-data="functionName()"` approach |
| Cart not persisting           | Not authenticated       | Check auth middleware                  |
| Missing messages              | Wrong sorting           | Use `orderByDesc('created_at')`        |
| Admin sees no conversations   | Wrong authorization     | Changed to show ALL for admins         |
| JavaScript exposed in HTML    | Inline x-data too large | Move to `@push('scripts')` function    |

---

## Future Enhancements

1. **Real-time messaging** - WebSocket for live conversations
2. **Payment integration** - Stripe/PayPal for checkout
3. **Review system** - Brands rate creators after delivery
4. **Notification system** - Email/SMS alerts for messages
5. **Advanced search** - Full-text search for creators/packages
6. **Analytics dashboard** - Sales, conversations, trends

---

## Quick Reference Commands

```bash
# Create new migration
php artisan make:migration create_table_name

# Run migrations
php artisan migrate

# Create new controller
php artisan make:controller ControllerName

# Create new model with migration
php artisan make:model ModelName -m

# Run tests
php artisan test

# Clear caches
php artisan cache:clear
php artisan config:clear
php artisan view:clear
```

---

**Last Updated**: April 2026
**Version**: 1.0
**Status**: Production Ready
