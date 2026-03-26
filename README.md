# FinnPay — Freelancer Payment Platform

FinnPay is a web-based payment management platform for freelancers who receive international payments (USD / EUR via PayPal) and need to release those earnings to a local bank account in **Sri Lankan Rupees (LKR)** on a scheduled basis.

---

## Table of Contents

1. [Overview](#overview)
2. [Features](#features)
3. [Tech Stack](#tech-stack)
4. [Project Structure](#project-structure)
5. [Database Schema](#database-schema)
6. [Installation & Setup](#installation--setup)
7. [Demo Credentials](#demo-credentials)
8. [User Flows](#user-flows)
   - [Freelancer Flow](#freelancer-flow)
   - [Customer Payment Flow](#customer-payment-flow)
   - [Payment Release Flow](#payment-release-flow)
9. [Routes Reference](#routes-reference)
10. [Business Rules](#business-rules)
11. [Exchange Rates](#exchange-rates)
12. [Payment Reference Format](#payment-reference-format)

---

## Overview

FinnPay solves the "last mile" problem for freelancers working with international clients: collecting multi-currency payments from platforms like PayPal and converting them into local currency on a predictable twice-monthly schedule.

The source data for this application is based on a real PayPal account export (`public/Paypal Freelance Account(User-0001).csv`) covering March 2026 transactions totalling **$1,197.44 USD / €1,002.11 EUR** across 8 payments from 7 international clients.

---

## Features

### Freelancer Portal (Authenticated)

| Feature | Description |
|---|---|
| **Dashboard** | Real-time balance in LKR/USD/EUR, recent transactions, next release date, quick-action shortcuts |
| **Payment References** | Generate unique reference codes per project to share with clients |
| **Transactions** | Full payment history with filtering by status and date range, fee breakdown, LKR conversion |
| **Releases** | View release history, see included transactions, trigger manual releases |
| **Profile** | Manage personal details, bank account information, and password |

### Public (No Auth Required)

| Feature | Description |
|---|---|
| **Customer Payment Page** | Clients visit `/pay/{reference}` to submit payments against a freelancer's reference |

---

## Tech Stack

| Layer | Technology |
|---|---|
| Framework | Laravel 13 (PHP 8.3) |
| Database | SQLite (via `database/database.sqlite`) |
| Frontend | Blade templates + Tailwind CSS v4 |
| Build Tool | Vite 8 with `laravel-vite-plugin` |
| Authentication | Laravel built-in `Auth` facade (session-based) |

---

## Project Structure

```
finnpay/
├── app/
│   ├── Http/Controllers/
│   │   ├── Auth/
│   │   │   ├── LoginController.php         # Login / logout
│   │   │   └── RegisterController.php      # Freelancer registration
│   │   ├── CustomerPaymentController.php   # Public customer payment page
│   │   ├── DashboardController.php         # Main dashboard
│   │   ├── PaymentReferenceController.php  # CRUD for payment references
│   │   ├── ProfileController.php           # Profile & bank account management
│   │   ├── ReleaseController.php           # View & trigger payment releases
│   │   └── TransactionController.php       # Transaction history & filters
│   ├── Models/
│   │   ├── PaymentReference.php            # Reference model (generateReference())
│   │   ├── Release.php                     # Release model (nextReleaseDate(), generateCode())
│   │   ├── Transaction.php                 # Transaction model (display_amount, status_badge)
│   │   └── User.php                        # Extended user model (pendingBalance(), hasBankDetails())
│   ├── Policies/
│   │   ├── PaymentReferencePolicy.php      # Ownership check for references
│   │   └── ReleasePolicy.php               # Ownership check for releases
│   └── Providers/
│       └── AppServiceProvider.php          # Policy registration
├── database/
│   ├── migrations/
│   │   ├── 2026_03_26_000010_add_freelancer_fields_to_users_table.php
│   │   ├── 2026_03_26_000010_create_releases_table.php
│   │   ├── 2026_03_26_000011_create_payment_references_table.php
│   │   └── 2026_03_26_000012_create_transactions_table.php
│   └── seeders/
│       └── DatabaseSeeder.php              # Seeds User-0001 with all 8 CSV transactions
├── resources/
│   ├── css/app.css                         # Tailwind v4 entry point
│   ├── js/app.js                           # Axios bootstrap
│   └── views/
│       ├── layouts/
│       │   ├── app.blade.php               # Authenticated layout (sidebar + topbar)
│       │   └── guest.blade.php             # Guest layout (centered card)
│       ├── auth/
│       │   ├── login.blade.php
│       │   └── register.blade.php
│       ├── customer/
│       │   └── pay.blade.php               # Public payment page
│       ├── dashboard/
│       │   └── index.blade.php
│       ├── profile/
│       │   └── edit.blade.php
│       ├── references/
│       │   ├── create.blade.php
│       │   ├── index.blade.php
│       │   └── show.blade.php              # Reference detail + copy link
│       ├── releases/
│       │   ├── index.blade.php
│       │   └── show.blade.php
│       └── transactions/
│           └── index.blade.php
└── routes/
    └── web.php
```

---

## Database Schema

### `users`
| Column | Type | Notes |
|---|---|---|
| `id` | bigint PK | |
| `freelancer_id` | string UNIQUE | Auto-generated, e.g. `FPL-000001` |
| `name` | string | |
| `email` | string UNIQUE | |
| `password` | string | Bcrypt hashed |
| `phone` | string nullable | |
| `bank_name` | string nullable | Required before releasing |
| `bank_branch` | string nullable | |
| `bank_account_number` | string nullable | Required before releasing |
| `bank_account_holder` | string nullable | |
| `local_currency` | string(3) | Default: `LKR` |
| `is_active` | boolean | Default: `true` |

### `payment_references`
| Column | Type | Notes |
|---|---|---|
| `id` | bigint PK | |
| `user_id` | FK → users | |
| `reference_number` | string UNIQUE | e.g. `FP-202603-AB12CD34` |
| `title` | string | Project/work description |
| `notes` | text nullable | Additional details |
| `amount_requested` | decimal(10,2) nullable | Expected payment amount |
| `currency` | string(3) | `USD` or `EUR` |
| `status` | enum | `active`, `paid`, `expired`, `cancelled` |
| `expires_at` | timestamp nullable | Optional expiry |

### `transactions`
| Column | Type | Notes |
|---|---|---|
| `id` | bigint PK | |
| `user_id` | FK → users | |
| `payment_reference_id` | FK nullable → payment_references | |
| `release_id` | FK nullable → releases | Set when released |
| `payer_name` | string | Client's full name |
| `payer_email` | string nullable | |
| `currency_type` | string(3) | `USD` or `EUR` |
| `amount_usd` | decimal(10,2) nullable | Gross amount in USD |
| `amount_eur` | decimal(10,2) nullable | Gross amount in EUR |
| `fee_usd` | decimal(10,2) | PayPal fee in USD |
| `fee_eur` | decimal(10,2) | PayPal fee in EUR |
| `final_usd` | decimal(10,2) nullable | Net amount after USD fee |
| `final_eur` | decimal(10,2) nullable | Net amount after EUR fee |
| `final_lkr` | decimal(10,2) nullable | LKR equivalent at time of receipt |
| `cv_rate` | decimal(8,4) nullable | USD→EUR conversion rate used |
| `lkr_rate` | decimal(10,2) nullable | Currency→LKR rate used |
| `paypal_transaction_id` | string nullable | |
| `status` | enum | `pending`, `cleared`, `released` |
| `received_at` | date | |

### `releases`
| Column | Type | Notes |
|---|---|---|
| `id` | bigint PK | |
| `user_id` | FK → users | |
| `release_code` | string UNIQUE | e.g. `REL-202603-001` |
| `period_start` | date | Earliest transaction date in this release |
| `period_end` | date | Latest transaction date in this release |
| `transaction_count` | integer | Number of transactions included |
| `total_usd` | decimal(10,2) | Sum of all `final_usd` |
| `total_eur` | decimal(10,2) | Sum of all `final_eur` |
| `total_lkr` | decimal(10,2) | Total converted to LKR |
| `exchange_rate_usd_lkr` | decimal(10,2) | Rate applied at release time |
| `exchange_rate_eur_lkr` | decimal(10,2) | Rate applied at release time |
| `bank_name` | string nullable | Snapshot of bank at release time |
| `bank_account` | string nullable | Snapshot of account number |
| `bank_account_holder` | string nullable | Snapshot of holder name |
| `status` | enum | `scheduled`, `processing`, `completed`, `failed` |
| `scheduled_at` | timestamp | When release was initiated |
| `processed_at` | timestamp nullable | When release completed |
| `notes` | text nullable | |

---

## Installation & Setup

### Prerequisites
- PHP 8.3+
- Composer
- Node.js 18+

### Steps

```bash
# 1. Install PHP dependencies
composer install

# 2. Copy environment file and generate app key
cp .env.example .env
php artisan key:generate

# 3. Create SQLite database file and run migrations
touch database/database.sqlite
php artisan migrate

# 4. Seed with demo data (User-0001 + 8 March 2026 transactions)
php artisan db:seed

# 5. Install Node dependencies and build assets
npm install
npm run build

# 6. Start the development server
php artisan serve
```

Or run everything in one command using the configured script:

```bash
composer run dev
```

This starts Laravel, the queue worker, log tailing (Pail), and Vite dev server concurrently.

---

## Demo Credentials

| Field | Value |
|---|---|
| **Email** | `user-0001@finnpay.test` |
| **Password** | `password` |
| **Freelancer ID** | `FPL-000001` |
| **Account Holder** | Alex Rivera |



To reset to a fresh state:
```bash
php artisan migrate:fresh --seed
```

---

## User Flows

### Freelancer Flow

```
1. Register at /register
   └─ Unique Freelancer ID auto-assigned (e.g. FPL-A3B2C1)

2. Complete Profile at /profile
   └─ Add bank name, branch, account number, account holder

3. Create Payment Reference at /references/create
   └─ Enter project title, optional amount & currency
   └─ System generates: FP-202603-AB12CD34

4. Share Reference with Client
   └─ Copy the reference number OR the full payment link (/pay/FP-202603-AB12CD34)

5. Client makes payment
   └─ Transaction appears in /transactions with status "Cleared"

6. Wait for release OR click "Release Now" in /releases
   └─ All cleared transactions converted to LKR
   └─ Release record created (e.g. REL-202603-001)
   └─ Transaction status → "Released"
   └─ LKR amount transferred to registered bank account
```

### Customer Payment Flow

```
1. Freelancer sends reference: FP-202603-AB12CD34

2. Customer visits: https://yourapp.com/pay/FP-202603-AB12CD34

3. Customer fills in:
   - Full Name
   - Email address
   - Amount
   - Currency (USD or EUR)

4. Submit → Payment recorded immediately as "Cleared"
   └─ PayPal fee (~4.9%) automatically deducted
   └─ LKR equivalent calculated and stored
   └─ Freelancer's balance updated
```

### Payment Release Flow

```
Automatic schedule: 1st and 16th of every month

OR manually via Dashboard → "Release to Bank" or /releases → "Release Now"

Release process:
1. Collect all "Cleared" transactions for the user
2. Sum total_usd and total_eur
3. Convert: total_usd × 295 + total_eur × 330 = total_lkr
4. Create Release record with release_code (REL-YYYYMM-NNN)
5. Snapshot bank account details onto the Release record
6. Update all included transactions: status → "Released", release_id set
7. Release appears in /releases with full breakdown
```

---

## Routes Reference

### Public Routes

| Method | URL | Description |
|---|---|---|
| `GET` | `/` | Redirect to dashboard (auth) or login (guest) |
| `GET` | `/pay/{reference}` | Customer payment page |
| `POST` | `/pay/{reference}` | Submit customer payment |
| `GET` | `/login` | Login form |
| `POST` | `/login` | Authenticate |
| `GET` | `/register` | Registration form |
| `POST` | `/register` | Create freelancer account |

### Authenticated Routes

| Method | URL | Name | Description |
|---|---|---|---|
| `POST` | `/logout` | `logout` | Sign out |
| `GET` | `/dashboard` | `dashboard` | Main dashboard |
| `GET` | `/references` | `references.index` | List all references |
| `GET` | `/references/create` | `references.create` | New reference form |
| `POST` | `/references` | `references.store` | Create reference |
| `GET` | `/references/{id}` | `references.show` | Reference detail + copy link |
| `DELETE` | `/references/{id}` | `references.destroy` | Cancel reference |
| `GET` | `/transactions` | `transactions.index` | Transaction history (filterable) |
| `GET` | `/releases` | `releases.index` | Release list + pending balance |
| `GET` | `/releases/{id}` | `releases.show` | Release detail with transactions |
| `POST` | `/releases/process` | `releases.process` | Trigger manual release |
| `GET` | `/profile` | `profile.edit` | Edit profile & bank details |
| `PATCH` | `/profile` | `profile.update` | Save profile changes |
| `PATCH` | `/profile/password` | `profile.password` | Change password |

---

## Business Rules

1. **Bank details required before releasing** — A freelancer must have `bank_name` and `bank_account_number` set in their profile. The "Release Now" button is disabled and shows a warning until these are added.

2. **No empty releases** — The release process checks that at least one `cleared` transaction exists before proceeding.

3. **Bank details snapshot** — At the time a release is processed, the freelancer's current bank account details are copied onto the `Release` record. This ensures the release history remains accurate even if the freelancer later updates their bank details.

4. **Reference statuses:**
   - `active` — accepting payments
   - `paid` — marked paid when received amount meets the requested amount
   - `expired` — past the `expires_at` date
   - `cancelled` — manually cancelled by the freelancer

5. **Transaction statuses:**
   - `cleared` — payment received and available for release
   - `released` — included in a completed release
   - `pending` — reserved for future use (e.g. PayPal holds)

6. **Release statuses:**
   - `completed` — money transferred to bank
   - `scheduled` — awaiting next cycle
   - `processing` — transfer in progress
   - `failed` — transfer failed

7. **Authorization** — Freelancers can only view and manage their own payment references and releases (enforced via `PaymentReferencePolicy` and `ReleasePolicy`).

---

## Exchange Rates

Exchange rates are currently **hardcoded** in `ReleaseController.php`:

```php
const USD_TO_LKR = 295.00;   // $1 USD = LKR 295
const EUR_TO_LKR = 330.00;   // €1 EUR = LKR 330
```

The PayPal USD→EUR rate used for incoming payments is approximately:
- €1 EUR = $1.1208 USD (as per PayPal's March 2026 rate)
- Inverse: $1 USD ≈ €0.8922 EUR

Customer-side fee approximation: **4.9%** flat rate (simulating PayPal's standard fee structure).

> To make rates configurable in a future version, move these constants to a database table or `.env` variables.

---

## Payment Reference Format

```
FP - YYYYMM - XXXXXXXX
│    │         └─ 8 random hex characters (uppercase)
│    └─ Year + Month of creation
└─ FinnPay prefix
```

**Example:** `FP-202603-A1B2C3D4`

Release code format:
```
REL - YYYYMM - NNN
│     │         └─ Sequential number (zero-padded to 3 digits)
│     └─ Year + Month
└─ Release prefix
```

**Example:** `REL-202603-001`

Freelancer ID format:
```
FPL - XXXXXX
│     └─ 6 random hex characters (uppercase), guaranteed unique
└─ FinnPay Freelancer prefix
```

**Example:** `FPL-A3B2C1`
