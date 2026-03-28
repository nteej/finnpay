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
7. [Environment Variables](#environment-variables)
8. [Demo Credentials](#demo-credentials)
9. [User Flows](#user-flows)
   - [Freelancer Flow](#freelancer-flow)
   - [Customer Payment Flow](#customer-payment-flow)
   - [Payment Release Flow](#payment-release-flow)
10. [Routes Reference](#routes-reference)
11. [PayPal Integration](#paypal-integration)
12. [Business Rules](#business-rules)
13. [Exchange Rates](#exchange-rates)
14. [Payment Reference Format](#payment-reference-format)
15. [Deployment History](#deployment-history)

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
| **Payment References** | Generate unique reference codes per project to share with clients; edit or delete before payment |
| **Transactions** | Full payment history with filtering by status and date range, fee breakdown, LKR conversion |
| **Releases** | View release history, see included transactions, trigger manual releases |
| **Profile** | Manage personal details, multiple bank accounts (with currency), and password |
| **Public Profile** | Freelancer talent pool page with work history, skills, bio |

### Public (No Auth Required)

| Feature | Description |
|---|---|
| **Landing Page** | Exchange rate ticker, historical rate chart (Chart.js), How it Works section |
| **Talent Pool** | Browse public freelancer profiles at `/freelancers` |
| **Freelancer Profile** | Public view of work history at `/f/{id}` |
| **Customer Payment Page** | Clients visit `/pay/{reference}` to pay via PayPal direct link |

---

## Tech Stack

| Layer | Technology |
|---|---|
| Framework | Laravel 13 (PHP 8.3) |
| Database | SQLite (via `database/database.sqlite`) |
| Frontend | Blade templates + Tailwind CSS v4 |
| Build Tool | Vite 8 with `laravel-vite-plugin` |
| Authentication | Laravel built-in `Auth` facade (session-based) |
| Charts | Chart.js v4.4.6 (CDN) |
| Payments | PayPal `_xclick` direct links + IPN webhook verification |

---

## Project Structure

```
finnpay/
├── app/
│   ├── Http/Controllers/
│   │   ├── Auth/
│   │   │   ├── LoginController.php              # Login / logout
│   │   │   └── RegisterController.php           # Freelancer registration
│   │   ├── BankAccountController.php            # Add / remove / set-default bank accounts
│   │   ├── CustomerPaymentController.php        # Public customer payment page
│   │   ├── DashboardController.php              # Main dashboard
│   │   ├── FreelancerProfileController.php      # Public profile & work history
│   │   ├── PaymentReferenceController.php       # CRUD for payment references
│   │   ├── PaypalIpnController.php              # PayPal IPN webhook handler
│   │   ├── ProfileController.php                # Personal profile & password
│   │   ├── ReleaseController.php                # View & trigger payment releases
│   │   └── TransactionController.php            # Transaction history & filters
│   ├── Models/
│   │   ├── BankAccount.php                      # bank_name, bank_code, branch, currency, is_default
│   │   ├── ExchangeRate.php                     # buy_rate, sell_rate, rate_date
│   │   ├── FreelancerProfile.php                # Bio, skills, publicWorkHistory()
│   │   ├── PaymentReference.php                 # paypalUrl(), workHistoryEntry()
│   │   ├── Release.php                          # nextReleaseDate(), generateCode()
│   │   ├── Transaction.php                      # display_amount, status_badge, paypal_transaction_id
│   │   ├── User.php                             # pendingBalance(), hasBankDetails()
│   │   └── WorkHistoryEntry.php                 # is_public, payment_reference_id
│   ├── Observers/
│   │   └── PaymentReferenceObserver.php         # Auto work history on create/paid
│   ├── Policies/
│   │   ├── PaymentReferencePolicy.php           # update/delete only when status=active
│   │   └── ReleasePolicy.php                    # Ownership check for releases
│   └── Providers/
│       └── AppServiceProvider.php               # Policy & observer registration
├── database/
│   ├── migrations/
│   │   ├── 2026_03_26_000010_add_freelancer_fields_to_users_table.php
│   │   ├── 2026_03_26_000010_create_releases_table.php
│   │   ├── 2026_03_26_000011_create_payment_references_table.php
│   │   ├── 2026_03_26_000012_create_transactions_table.php
│   │   ├── 2026_03_28_112848_add_rate_date_to_exchange_rates_table.php
│   │   ├── 2026_03_28_113825_drop_rate_column_from_exchange_rates_table.php
│   │   ├── 2026_03_28_125341_add_payment_reference_to_work_history_entries_table.php
│   │   └── 2026_03_28_201405_add_bank_codes_to_bank_accounts_table.php
│   └── seeders/
│       ├── DatabaseSeeder.php                   # Seeds User-0001 with all 8 CSV transactions
│       └── ExchangeRateSeeder.php               # Seeds 482 rows from public/exchanges.csv
├── public/
│   ├── banks.json                               # Sri Lankan bank list (name + code)
│   ├── branches.json                            # Branch list keyed by bank ID (219 KB, lazy-loaded)
│   └── exchanges.csv                            # Historical USD/EUR buy+sell rates (2025-04 → 2026-03)
├── resources/
│   ├── css/app.css                              # Tailwind v4 entry point
│   ├── js/app.js                                # Axios bootstrap
│   └── views/
│       ├── layouts/
│       │   ├── app.blade.php                    # Authenticated layout (sidebar + topbar)
│       │   └── guest.blade.php                  # Guest layout (centered card)
│       ├── auth/
│       │   ├── login.blade.php
│       │   └── register.blade.php
│       ├── customer/
│       │   └── pay.blade.php                    # Public payment page (4 states: form/success/cancel/flash)
│       ├── dashboard/
│       │   └── index.blade.php
│       ├── freelancer/
│       │   ├── index.blade.php                  # Talent pool
│       │   ├── show.blade.php                   # Public profile (uses publicWorkHistory)
│       │   └── profile/
│       │       └── edit.blade.php               # Manage public profile & work history
│       ├── landing.blade.php                    # Homepage with exchange chart
│       ├── profile/
│       │   └── edit.blade.php                   # Personal info + bank accounts + password
│       ├── references/
│       │   ├── create.blade.php
│       │   ├── edit.blade.php                   # Edit active reference + danger zone
│       │   ├── index.blade.php
│       │   └── show.blade.php                   # PayPal link card + edit/delete actions
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
| `local_currency` | string(3) | Default: `LKR` |
| `is_active` | boolean | Default: `true` |

### `bank_accounts`
| Column | Type | Notes |
|---|---|---|
| `id` | bigint PK | |
| `user_id` | FK → users | |
| `bank_name` | string | Selected from banks.json |
| `bank_code` | smallint | Numeric bank code |
| `bank_branch` | string | Selected from branches.json |
| `branch_code` | smallint | Numeric branch code |
| `bank_account_number` | string | |
| `bank_account_holder` | string | |
| `currency` | string(3) | `LKR`, `USD`, or `EUR` |
| `is_default` | boolean | Default: `false` |
| `is_active` | boolean | Default: `true` (soft delete) |

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
| `paypal_transaction_id` | string UNIQUE nullable | Used for IPN deduplication |
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

### `exchange_rates`
| Column | Type | Notes |
|---|---|---|
| `id` | bigint PK | |
| `currency_from` | string(3) | `USD` or `EUR` |
| `currency_to` | string(3) | `LKR` |
| `buy_rate` | decimal(12,4) | Rate at which the bank buys foreign currency |
| `sell_rate` | decimal(12,4) | Rate at which the bank sells foreign currency |
| `rate_date` | date | Date of rate snapshot |
| `is_active` | boolean | `true` only for the latest date per currency |

### `work_history_entries`
| Column | Type | Notes |
|---|---|---|
| `id` | bigint PK | |
| `freelancer_profile_id` | FK → freelancer_profiles | |
| `project_title` | string | |
| `client_name` | string nullable | Populated from PayPal payer name on payment |
| `description` | text nullable | |
| `completed_at` | date nullable | Set when reference is marked paid |
| `is_public` | boolean | `false` until reference is paid |
| `is_featured` | boolean | |
| `payment_reference_id` | FK nullable → payment_references | Links auto-generated entries |

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

# 5. Seed historical exchange rates from exchanges.csv
php artisan db:seed --class=ExchangeRateSeeder

# 6. Install Node dependencies and build assets
npm install
npm run build

# 7. Start the development server
php artisan serve
```

Or run everything in one command using the configured script:

```bash
composer run dev
```

This starts Laravel, the queue worker, log tailing (Pail), and Vite dev server concurrently.

---

## Environment Variables

Add the following to your `.env` file:

```dotenv
# PayPal Integration
PAYPAL_BUSINESS_EMAIL=your-paypal-business@email.com
PAYPAL_SANDBOX=true
```

| Variable | Default | Description |
|---|---|---|
| `PAYPAL_BUSINESS_EMAIL` | — | The PayPal Business account email that receives payments |
| `PAYPAL_SANDBOX` | `true` | Set to `false` in production to use live PayPal IPN endpoint |

When `PAYPAL_SANDBOX=true`, IPN verification posts to `https://ipnpb.sandbox.paypal.com/cgi-bin/webscr`.
When `false`, it posts to `https://ipnpb.paypal.com/cgi-bin/webscr`.

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
php artisan db:seed --class=ExchangeRateSeeder
```

---

## User Flows

### Freelancer Flow

```
1. Register at /register
   └─ Unique Freelancer ID auto-assigned (e.g. FPL-A3B2C1)

2. Complete Profile at /profile
   └─ Add bank accounts (select bank + branch from dropdowns, choose currency)

3. Create Payment Reference at /references/create
   └─ Enter project title, optional amount & currency
   └─ System generates: FP-202603-AB12CD34
   └─ Work history entry auto-created (hidden until paid)

4. Share PayPal Link with Client
   └─ Reference detail page shows a styled PayPal payment card
   └─ Copy button for the direct PayPal URL with pre-filled amount and reference

5. Client pays via PayPal
   └─ PayPal IPN webhook fires → transaction auto-created
   └─ Reference marked as paid
   └─ Work history entry published (is_public = true)

6. Wait for release OR click "Release Now" in /releases
   └─ All cleared transactions converted to LKR
   └─ Release record created (e.g. REL-202603-001)
   └─ Transaction status → "Released"
```

### Customer Payment Flow

```
1. Freelancer sends PayPal direct link from /references/{id}

2. Customer clicks link → lands on PayPal.com with:
   - Recipient pre-filled (freelancer's PayPal email)
   - Amount pre-filled (if set on reference)
   - Reference number in the "custom" field for tracking

3. Customer completes payment on PayPal

4. PayPal sends IPN notification to /paypal/ipn
   └─ System verifies with PayPal IPN API (VERIFIED check)
   └─ Transaction recorded in FinnPay
   └─ Reference status updated to "paid"

5. Customer optionally redirected to /pay/{reference}?status=success
```

### Payment Release Flow

```
Automatic schedule: 1st and 16th of every month

OR manually via Dashboard → "Release to Bank" or /releases → "Release Now"

Release process:
1. Collect all "Cleared" transactions for the user
2. Sum total_usd and total_eur
3. Convert using active exchange rates from exchange_rates table
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
| `GET` | `/` | Landing page with exchange chart |
| `GET` | `/pay/{reference}` | Customer payment page |
| `POST` | `/paypal/ipn` | PayPal IPN webhook (CSRF exempt) |
| `GET` | `/freelancers` | Public talent pool |
| `GET` | `/f/{freelancer_id}` | Public freelancer profile |
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
| `GET` | `/references/{id}` | `references.show` | Reference detail + PayPal link card |
| `GET` | `/references/{id}/edit` | `references.edit` | Edit reference (active only) |
| `PUT/PATCH` | `/references/{id}` | `references.update` | Update reference (active only) |
| `DELETE` | `/references/{id}` | `references.destroy` | Cancel/delete reference (active only) |
| `GET` | `/transactions` | `transactions.index` | Transaction history (filterable) |
| `GET` | `/releases` | `releases.index` | Release list + pending balance |
| `GET` | `/releases/{id}` | `releases.show` | Release detail with transactions |
| `POST` | `/releases/process` | `releases.process` | Trigger manual release |
| `GET` | `/profile` | `profile.edit` | Edit profile & bank accounts |
| `PATCH` | `/profile` | `profile.update` | Save profile changes |
| `PATCH` | `/profile/password` | `profile.password` | Change password |
| `POST` | `/bank-accounts` | `bank-accounts.store` | Add bank account |
| `PATCH` | `/bank-accounts/{id}/default` | `bank-accounts.setDefault` | Set default account |
| `DELETE` | `/bank-accounts/{id}` | `bank-accounts.destroy` | Remove bank account |
| `GET` | `/freelancer/profile/edit` | `freelancer.profile.edit` | Edit public profile |
| `POST/PATCH` | `/freelancer/profile` | `freelancer.profile.update` | Save public profile |
| `POST` | `/freelancer/profile/work` | `freelancer.profile.work.store` | Add work history entry |
| `DELETE` | `/freelancer/profile/work/{id}` | `freelancer.profile.work.destroy` | Remove work history entry |

---

## PayPal Integration

### Direct Payment Link

When a freelancer creates a payment reference, the system generates a PayPal `_xclick` URL:

```
https://www.paypal.com/cgi-bin/webscr
  ?cmd=_xclick
  &business={PAYPAL_BUSINESS_EMAIL}
  &item_name=Payment+for+{title}
  &item_number={reference_number}
  &amount={amount_requested}
  &currency_code={currency}
  &custom={reference_number}
  &return={app_url}/pay/{reference_number}?status=success
  &cancel_return={app_url}/pay/{reference_number}?status=cancelled
  &notify_url={app_url}/paypal/ipn
```

The `custom` field carries the reference number, which PayPal includes in the IPN payload for automatic matching.

### IPN Webhook Flow

1. PayPal sends `POST /paypal/ipn` with transaction data
2. FinnPay re-POSTs the raw body + `cmd=_notify-validate` to PayPal's IPN verify URL
3. PayPal responds with `VERIFIED` or `INVALID`
4. On `VERIFIED`:
   - Check `payment_status === Completed`
   - Verify `receiver_email` matches `PAYPAL_BUSINESS_EMAIL`
   - Check `txn_id` is not already recorded (deduplication)
   - Find matching `PaymentReference` via `custom` field
   - Create `Transaction` record
   - Mark reference as `paid`
5. All steps logged to `storage/logs/paypal.log` (90 days retention)

### Testing PayPal Sandbox

1. Create sandbox accounts at [developer.paypal.com](https://developer.paypal.com)
2. Set `PAYPAL_SANDBOX=true` in `.env`
3. Use the sandbox business email for `PAYPAL_BUSINESS_EMAIL`
4. PayPal returns to `/pay/{reference}?status=success` after payment
5. IPN arrives at `/paypal/ipn` — check `storage/logs/paypal.log` for verification steps

---

## Business Rules

1. **Bank details required before releasing** — A freelancer must have at least one active bank account. The "Release Now" button is disabled and shows a warning until bank details are added.

2. **No empty releases** — The release process checks that at least one `cleared` transaction exists before proceeding.

3. **Bank details snapshot** — At the time a release is processed, the freelancer's default bank account details are copied onto the `Release` record.

4. **Reference edit/delete** — Only references with `status = active` can be edited or deleted. Policies (`PaymentReferencePolicy`) enforce this at the authorization layer.

5. **Reference statuses:**
   - `active` — accepting payments
   - `paid` — marked paid when IPN arrives for this reference
   - `expired` — past the `expires_at` date
   - `cancelled` — manually cancelled by the freelancer

6. **Transaction statuses:**
   - `cleared` — payment received and available for release
   - `released` — included in a completed release
   - `pending` — reserved for future use (e.g. PayPal holds)

7. **Release statuses:**
   - `completed` — money transferred to bank
   - `scheduled` — awaiting next cycle
   - `processing` — transfer in progress
   - `failed` — transfer failed

8. **IPN deduplication** — `paypal_transaction_id` has a unique constraint. Duplicate IPN deliveries are silently dropped.

9. **Work history lifecycle** — When a payment reference is created, a hidden work history entry (`is_public = false`) is auto-created via `PaymentReferenceObserver`. When the reference is paid, the entry is published (`is_public = true`) with the client name from the PayPal payer.

10. **Multiple bank accounts** — Freelancers can add multiple bank accounts in different currencies (LKR/USD/EUR). One account is designated as the default; it is used for LKR releases.

---

## Exchange Rates

Exchange rates are sourced from a historical CSV (`public/exchanges.csv`) and stored in the `exchange_rates` table. The seeder loads 482 rows (241 each for USD and EUR) covering April 2025 through March 2026.

```bash
# Re-seed exchange rates
php artisan db:seed --class=ExchangeRateSeeder
```

Only the latest date per currency pair has `is_active = true`. The `ExchangeRate::getRate()` method reads `buy_rate` from the active row.

The landing page displays a Chart.js chart of historical buy rates with:
- Time range tabs: 7D / 1M / 3M / All
- Currency toggles: USD / EUR
- Stat cards: current rate, 30-day high/low, period change

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

---

## Deployment History

### 2026-03-26 — Initial Release

- Laravel 13 project scaffolded with SQLite
- Core models: `User`, `PaymentReference`, `Transaction`, `Release`
- Freelancer registration with auto-generated `FPL-XXXXXX` IDs
- Dashboard with balance overview (USD/EUR/LKR), recent transactions, next release date
- Payment references: create, list, show, cancel
- Customer payment page (`/pay/{reference}`) with manual payment submission
- Transaction list with status/date filters
- Release system: manual trigger, LKR conversion, bank snapshot
- Profile page: personal info + bank details + password change
- Seeder: User-0001 with 8 real March 2026 PayPal transactions

### 2026-03-27 — Talent Pool & Public Profiles

- `FreelancerProfile` model with bio, skills, hourly rate, availability
- `WorkHistoryEntry` model linked to freelancer profiles
- Public talent pool at `/freelancers`
- Individual public profile pages at `/f/{freelancer_id}`
- Freelancer profile edit page: bio, skills, work history management
- Landing page created with exchange rate ticker and "How it Works" section
- Currency exchange rates table created

### 2026-03-28 (Morning) — Exchange Rate History

- Added `rate_date`, `buy_rate`, `sell_rate` columns to `exchange_rates` table
- Dropped redundant `rate` column (was identical to `buy_rate`)
- New unique key: `(currency_from, currency_to, rate_date)`
- `ExchangeRateSeeder` created: reads `public/exchanges.csv`, upserts 482 rows
- `ExchangeRate::getRate()` updated to read `buy_rate`
- Historical rate chart added to landing page (Chart.js v4.4.6 CDN)
  - Time range tabs: 7D / 1M / 3M / All
  - Currency toggles: USD / EUR / both
  - Stat cards: current rate, 30-day high/low, period change

### 2026-03-28 (Midday) — PayPal Direct Payment Links

- `PaymentReference::paypalUrl()` method generating `_xclick` URLs
- Reference detail page (`/references/{id}`) redesigned with:
  - Styled PayPal payment card
  - One-click copy button for the payment URL
  - "Open in PayPal" button
- Customer payment page (`/pay/{reference}`) updated with four render states:
  - Normal payment form
  - Success return from PayPal (`?status=success`)
  - Cancel return from PayPal (`?status=cancelled`)
  - Flash success (IPN-triggered confirmation)
- `CustomerPaymentController` updated to accept `active` and `paid` reference statuses

### 2026-03-28 (Afternoon) — Reference Edit / Delete & Work History Lifecycle

- `PaymentReferenceController` edit and update methods added
- `references/edit.blade.php` created with pre-filled form and danger zone
- `PaymentReferencePolicy` updated: update/delete restricted to `status = active`
- Routes added: `GET /references/{id}/edit`, `PATCH /references/{id}`
- Reference index and show views updated with edit pencil and delete buttons
- `PaymentReferenceObserver` created:
  - `created`: auto-creates hidden `WorkHistoryEntry` (`is_public = false`)
  - `updated`: when status → `paid`, publishes entry, sets client name from PayPal payer
- `WorkHistoryEntry` updated: `is_public`, `payment_reference_id` fields added
- `FreelancerProfile::publicWorkHistory()` relation added (filters `is_public = true`)
- Public profile view updated to use `publicWorkHistory`
- Work history edit view shows amber "Awaiting payment" badge on hidden entries
- `is_public` boolean column and `payment_reference_id` FK migration added

### 2026-03-28 (Evening) — PayPal IPN Webhook

- `PaypalIpnController` created with full verification pipeline:
  1. Re-POST `cmd=_notify-validate` to PayPal IPN verify endpoint
  2. Expect `VERIFIED` response
  3. Check `payment_status === Completed`
  4. Verify `receiver_email` matches `PAYPAL_BUSINESS_EMAIL`
  5. Deduplicate via `paypal_transaction_id` unique constraint
  6. Match reference via `custom` field
  7. Create `Transaction`, mark reference `paid`
- `config/services.php`: PayPal config block added
- `config/logging.php`: `paypal` daily log channel, 90 days retention
- `bootstrap/app.php`: CSRF exemption for `/paypal/ipn`
- `.env` variables added: `PAYPAL_BUSINESS_EMAIL`, `PAYPAL_SANDBOX`
- Route added: `POST /paypal/ipn`

### 2026-03-28 (Late Evening) — Bank Account Dropdowns & Multiple Accounts

- `bank_accounts` table refactored: multiple accounts per user, with `is_default`, `is_active`, `currency`
- `bank_code` and `branch_code` (smallint) columns added
- `BankAccountController` created: store, destroy, setDefault
- Profile page bank section redesigned:
  - Lists existing accounts with currency badge and default indicator
  - "Set default" and "Remove" actions per account
  - Add bank account form with dynamic dropdowns
- Bank dropdown: populated from `public/banks.json` (embedded via PHP `@json`)
- Branch dropdown: lazy-fetched from `public/branches.json` on bank selection, cached in memory
- `old()` value restoration after validation errors
- Routes added: `POST /bank-accounts`, `PATCH /bank-accounts/{id}/default`, `DELETE /bank-accounts/{id}`
- `app.blade.php`: `@stack('scripts')` added before `</body>`
