# Design Document - Sirkulib
# Sistem Informasi Perpustakaan Sekolah

**Versi:** 1.0
**Tanggal:** 2026-05-22
**Status:** DESIGN COMPLETE

---

## 1. Overview

### 1.1 Purpose

Document ini mendefinisikan arsitektur sistem, desain database, desain API, dan pedoman UI/UX untuk aplikasi Sirkulib. Document ini berfungsi sebagai referensi teknis untuk tim development.

### 1.2 Scope

- Sistem operasi: Platform agnostic (cloud-hosted)
- Framework: Laravel 13.x
- Database: PostgreSQL (Neon)
- Frontend: Blade + Livewire + Tailwind CSS
- File Storage: Cloudflare R2 / S3-compatible

---

## 2. Architecture

### 2.1 High-Level Architecture

```
┌─────────────────────────────────────────────────────────────────┐
│                         Client Layer                            │
│  ┌───────────┐  ┌───────────┐  ┌───────────┐  ┌───────────┐  │
│  │  Browser  │  │   iOS     │  │ Android   │  │  Mobile   │  │
│  │  (Web)    │  │   App     │  │   App     │  │   Web     │  │
│  └─────┬─────┘  └─────┬─────┘  └─────┬─────┘  └─────┬─────┘  │
└────────┼───────────────┼───────────────┼───────────────┼────────┘
         │               │               │               │
         └───────────────┴───────────────┴───────────────┘
                              │
                              ▼
┌─────────────────────────────────────────────────────────────────┐
│                      Presentation Layer                          │
│  ┌─────────────────────────────────────────────────────────────┐│
│  │              Blade Templates + Livewire Components          ││
│  └─────────────────────────────────────────────────────────────┘│
└─────────────────────────────────────────────────────────────────┘
                              │
                              ▼
┌─────────────────────────────────────────────────────────────────┐
│                       Application Layer                          │
│  ┌─────────────┐  ┌─────────────┐  ┌─────────────┐           │
│  │ Controllers │  │   Actions  │  │  Services   │           │
│  │             │  │  (Laravel   │  │             │           │
│  │ (Thin)      │  │   Actions)  │  │ (Business   │           │
│  │             │  │             │  │  Logic)     │           │
│  └─────────────┘  └─────────────┘  └─────────────┘           │
└─────────────────────────────────────────────────────────────────┘
                              │
                              ▼
┌─────────────────────────────────────────────────────────────────┐
│                        Domain Layer                             │
│  ┌─────────────┐  ┌─────────────┐  ┌─────────────┐           │
│  │   Models    │  │    Enums    │  │   Events    │           │
│  │  (Eloquent) │  │             │  │             │           │
│  └─────────────┘  └─────────────┘  └─────────────┘           │
└─────────────────────────────────────────────────────────────────┘
                              │
                              ▼
┌─────────────────────────────────────────────────────────────────┐
│                     Infrastructure Layer                         │
│  ┌─────────────┐  ┌─────────────┐  ┌─────────────┐           │
│  │  Database   │  │   Storage   │  │   External  │           │
│  │ (PostgreSQL)│  │   (R2/S3)   │  │   Services  │           │
│  └─────────────┘  └─────────────┘  └─────────────┘           │
└─────────────────────────────────────────────────────────────────┘
```

### 2.2 Request Lifecycle

```
Browser Request
      │
      ▼
┌─────────────────┐
│  Route Matching │
└────────┬────────┘
         │
         ▼
┌─────────────────┐
│   Middleware    │ ← Auth, Role Check, Rate Limit
│    Stack        │
└────────┬────────┘
         │
         ▼
┌─────────────────┐
│  Form Request   │ ← Validation
│   (if POST)     │
└────────┬────────┘
         │
         ▼
┌─────────────────┐
│   Controller    │ ← Thin, delegates to
│                 │    Service/Action
└────────┬────────┘
         │
         ▼
┌─────────────────┐
│  Service Layer  │ ← Business Logic
│                 │    Database Transaction
└────────┬────────┘
         │
         ▼
┌─────────────────┐
│    Model        │ ← Eloquent ORM
│                 │
└────────┬────────┘
         │
         ▼
┌─────────────────┐
│    Database     │ ← PostgreSQL
│                 │
└─────────────────┘
```

### 2.3 Directory Structure

```
sirkulib/
├── app/
│   ├── Actions/                    # Laravel Actions
│   │   ├── Member/
│   │   │   ├── CreateMember.php
│   │   │   ├── UpdateMember.php
│   │   │   └── DeleteMember.php
│   │   ├── Book/
│   │   │   ├── CreateBook.php
│   │   │   ├── UpdateBook.php
│   │   │   └── ImportBooks.php
│   │   ├── Loan/
│   │   │   ├── CreateLoan.php
│   │   │   ├── ReturnLoan.php
│   │   │   └── ExtendLoan.php
│   │   └── Fine/
│   │       ├── CreateFine.php
│   │       ├── PayFine.php
│   │       └── WaiveFine.php
│   │
│   ├── Enums/
│   │   ├── UserRole.php
│   │   ├── MemberType.php
│   │   ├── MemberStatus.php
│   │   ├── BookStatus.php
│   │   ├── BookCopyStatus.php
│   │   ├── BookCopyCondition.php
│   │   ├── LoanStatus.php
│   │   ├── LoanItemStatus.php
│   │   ├── FineType.php
│   │   ├── FineStatus.php
│   │   └── SettingKey.php
│   │
│   ├── Events/
│   │   ├── BookBorrowed.php
│   │   ├── BookReturned.php
│   │   ├── FineCreated.php
│   │   └── MemberStatusChanged.php
│   │
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Auth/
│   │   │   │   ├── LoginController.php
│   │   │   │   └── ForgotPasswordController.php
│   │   │   ├── DashboardController.php
│   │   │   ├── MemberController.php
│   │   │   ├── BookController.php
│   │   │   ├── BookCopyController.php
│   │   │   ├── CategoryController.php
│   │   │   ├── LoanController.php
│   │   │   ├── FineController.php
│   │   │   ├── ReportController.php
│   │   │   ├── CatalogController.php
│   │   │   ├── SettingController.php
│   │   │   └── AuditLogController.php
│   │   │
│   │   ├── Middleware/
│   │   │   ├── RoleMiddleware.php
│   │   │   └── AuditMiddleware.php
│   │   │
│   │   ├── Requests/
│   │   │   ├── Member/
│   │   │   │   ├── StoreMemberRequest.php
│   │   │   │   └── UpdateMemberRequest.php
│   │   │   ├── Book/
│   │   │   │   ├── StoreBookRequest.php
│   │   │   │   └── UpdateBookRequest.php
│   │   │   ├── BookCopy/
│   │   │   │   ├── StoreBookCopyRequest.php
│   │   │   │   └── GenerateBookCopiesRequest.php
│   │   │   ├── Loan/
│   │   │   │   └── StoreLoanRequest.php
│   │   │   └── Setting/
│   │   │       └── UpdateSettingRequest.php
│   │   │
│   │   └── Resources/
│   │       ├── MemberResource.php
│   │       ├── BookResource.php
│   │       ├── LoanResource.php
│   │       └── DashboardResource.php
│   │
│   ├── Listeners/
│   │   ├── SendLoanNotification.php
│   │   └── LogAuditEvent.php
│   │
│   ├── Mail/
│   │   ├── LoanConfirmation.php
│   │   └── DueReminder.php
│   │
│   ├── Models/
│   │   ├── User.php
│   │   ├── Member.php
│   │   ├── Book.php
│   │   ├── BookCategory.php
│   │   ├── BookCopy.php
│   │   ├── LoanTransaction.php
│   │   ├── LoanItem.php
│   │   ├── Fine.php
│   │   ├── FinePayment.php
│   │   ├── LibrarySetting.php
│   │   ├── Import.php
│   │   └── AuditLog.php
│   │
│   ├── Notifications/
│   │   ├── LoanCreatedNotification.php
│   │   ├── DueReminderNotification.php
│   │   └── OverdueNotification.php
│   │
│   ├── Policies/
│   │   ├── MemberPolicy.php
│   │   ├── BookPolicy.php
│   │   ├── BookCopyPolicy.php
│   │   ├── LoanTransactionPolicy.php
│   │   └── FinePolicy.php
│   │
│   ├── Providers/
│   │   ├── AppServiceProvider.php
│   │   └── EventServiceProvider.php
│   │
│   └── Services/
│       ├── MemberService.php
│       ├── BookService.php
│       ├── BookCopyService.php
│       ├── LoanService.php
│       ├── ReturnService.php
│       ├── FineService.php
│       ├── ReportService.php
│       ├── SettingService.php
│       ├── AuditService.php
│       ├── BarcodeService.php
│       └── ImportExportService.php
│
├── config/
│   ├── app.php
│   ├── database.php
│   ├── filesystems.php
│   ├── library.php          # Custom config untuk library rules
│   └── ...
│
├── database/
│   ├── migrations/
│   ├── seeders/
│   │   ├── DatabaseSeeder.php
│   │   ├── RoleSeeder.php
│   │   ├── UserSeeder.php
│   │   ├── CategorySeeder.php
│   │   └── SettingSeeder.php
│   └── factories/
│       ├── MemberFactory.php
│       ├── BookFactory.php
│       └── ...
│
├── resources/
│   ├── views/
│   │   ├── components/
│   │   │   ├── layouts/
│   │   │   │   ├── app.blade.php
│   │   │   │   └── dashboard.blade.php
│   │   │   ├── ui/
│   │   │   │   ├── button.blade.php
│   │   │   │   ├── input.blade.php
│   │   │   │   ├── select.blade.php
│   │   │   │   ├── modal.blade.php
│   │   │   │   ├── table.blade.php
│   │   │   │   ├── badge.blade.php
│   │   │   │   └── card.blade.php
│   │   │   └── datatable/
│   │   │       └── datatable.blade.php
│   │   │
│   │   ├── auth/
│   │   │   ├── login.blade.php
│   │   │   └── forgot-password.blade.php
│   │   │
│   │   ├── layouts/
│   │   │   ├── partials/
│   │   │   │   ├── sidebar.blade.php
│   │   │   │   ├── header.blade.php
│   │   │   │   └── footer.blade.php
│   │   │   └── app.blade.php
│   │   │
│   │   ├── dashboard/
│   │   │   ├── librarian/
│   │   │   │   └── index.blade.php
│   │   │   └── principal/
│   │   │       └── index.blade.php
│   │   │
│   │   ├── members/
│   │   │   ├── index.blade.php
│   │   │   ├── create.blade.php
│   │   │   ├── edit.blade.php
│   │   │   ├── show.blade.php
│   │   │   └── import.blade.php
│   │   │
│   │   ├── books/
│   │   │   ├── index.blade.php
│   │   │   ├── create.blade.php
│   │   │   ├── edit.blade.php
│   │   │   ├── show.blade.php
│   │   │   └── import.blade.php
│   │   │
│   │   ├── book-copies/
│   │   │   ├── index.blade.php
│   │   │   ├── create.blade.php
│   │   │   ├── generate.blade.php
│   │   │   └── label.blade.php
│   │   │
│   │   ├── categories/
│   │   │   ├── index.blade.php
│   │   │   ├── create.blade.php
│   │   │   └── edit.blade.php
│   │   │
│   │   ├── loans/
│   │   │   ├── index.blade.php
│   │   │   ├── create.blade.php
│   │   │   ├── show.blade.php
│   │   │   ├── return.blade.php
│   │   │   └── extend.blade.php
│   │   │
│   │   ├── fines/
│   │   │   ├── index.blade.php
│   │   │   ├── show.blade.php
│   │   │   └── payment.blade.php
│   │   │
│   │   ├── catalog/
│   │   │   ├── index.blade.php
│   │   │   └── show.blade.php
│   │   │
│   │   ├── reports/
│   │   │   ├── books.blade.php
│   │   │   ├── loans.blade.php
│   │   │   ├── overdue.blade.php
│   │   │   ├── fines.blade.php
│   │   │   └── ...
│   │   │
│   │   ├── settings/
│   │   │   └── index.blade.php
│   │   │
│   │   ├── audit-logs/
│   │   │   └── index.blade.php
│   │   │
│   │   └── errors/
│   │       ├── 404.blade.php
│   │       ├── 500.blade.php
│   │       └── ...
│   │
│   ├── css/
│   │   └── app.css
│   │
│   └── js/
│       └── app.js
│
├── routes/
│   ├── web.php
│   ├── auth.php
│   └── api.php
│
└── tests/
    ├── Feature/
    │   ├── MemberTest.php
    │   ├── BookTest.php
    │   ├── LoanTest.php
    │   └── ReturnTest.php
    │
    └── Unit/
        ├── LoanServiceTest.php
        ├── FineServiceTest.php
        └── BarcodeServiceTest.php
```

---

## 3. Database Design

### 3.1 ER Diagram

```
┌─────────────────┐       ┌─────────────────┐
│      User       │       │     Member      │
├─────────────────┤       ├─────────────────┤
│ id (PK)         │──┐    │ id (PK)         │
│ name            │  │    │ user_id (FK)   │←─┐
│ email           │  │    │ member_number   │  │
│ password        │  │    │ type            │  │
│ role            │  │    │ name            │  │
│ status          │  │    │ identity_number │  │
│ last_login_at   │  │    │ class_name      │  │
│ created_at      │  │    │ major           │  │
│ updated_at      │  │    │ email           │  │
└─────────────────┘  │    │ phone           │  │
                     │    │ address         │  │
                     │    │ photo_path      │  │
                     │    │ status          │  │
                     │    │ created_at     │  │
                     │    │ updated_at     │  │
                     │    └────────┬────────┘  │
                     │             │          │
                     │             │          │
                     │    ┌────────▼────────┐  │
                     │    │    Loan         │  │
                     │    │  Transaction   │  │
                     │    ├────────────────┤  │
                     │    │ id (PK)         │  │
                     │    │ member_id (FK)  │←─┘
                     │    │ borrowed_by     │  │
                     │    │ borrowed_at     │  │
                     │    │ due_at          │  │
                     │    │ returned_at     │  │
                     │    │ status          │  │
                     │    │ notes          │  │
                     │    │ created_at     │  │
                     │    │ updated_at     │  │
                     │    └────────┬────────┘  │
                     │             │          │
                     │             │          │
                     │    ┌────────▼────────┐  │
                     │    │    Loan Item    │  │
                     │    ├────────────────┤  │
                     │    │ id (PK)         │  │
                     │    │ transaction_id  │  │
                     │    │ book_copy_id    │  │
                     │    │ borrowed_at     │  │
                     │    │ due_at         │  │
                     │    │ returned_at    │  │
                     │    │ return_condition│  │
                     │    │ extension_count│  │
                     │    │ status         │  │
                     │    │ fine_id (FK)   │←─┐│
                     │    │ created_at     │  ││
                     │    │ updated_at     │  ││
                     │    └────────────────┘  ││
                     │                        │
                     │    ┌────────────────────▼───────┐
                     │    │          Fine              │
                     │    ├────────────────────────────┤
                     │    │ id (PK)                   │
                     │    │ member_id (FK)            │
                     │    │ loan_item_id (FK)         │
                     │    │ type                      │
                     │    │ amount                   │
                     │    │ paid_amount              │
                     │    │ status                   │
                     │    │ notes                    │
                     │    │ waived_reason            │
                     │    │ created_by               │
                     │    │ paid_at                 │
                     │    │ created_at              │
                     │    │ updated_at              │
                     │    └────────────────────────────┘
                     │
                     │    ┌────────────────┐
                     │    │   FinePayment  │
                     │    ├────────────────┤
                     │    │ id (PK)        │
                     │    │ fine_id (FK)   │←─┐
                     │    │ amount         │  │
                     │    │ payment_method │  │
                     │    │ paid_by        │  │
                     │    │ paid_at        │  │
                     │    │ notes          │  │
                     │    │ created_at    │  │
                     │    └────────────────┘  │
                     │
                     │
┌────────────────────▼─────────────────┐
│              Book                    │
├──────────────────────────────────────┤
│ id (PK)                              │
│ category_id (FK)                     │
│ title                                │
│ isbn                                 │
│ author                               │
│ publisher                            │
│ published_year                       │
│ language                             │
│ pages                                │
│ description                          │
│ cover_path                           │
│ tags                                 │
│ status                               │
│ created_by                           │
│ created_at                           │
│ updated_at                           │
└────────────────────┬──────────────────┘
                   │
┌──────────────────▼──────────────────┐
│           BookCategory              │
├─────────────────────────────────────┤
│ id (PK)                             │
│ name                                │
│ slug                                │
│ description                         │
│ created_at                          │
│ updated_at                          │
└─────────────────────────────────────┘

┌────────────────────▼──────────────────┐
│            BookCopy                   │
├──────────────────────────────────────┤
│ id (PK)                              │
│ book_id (FK)                        │
│ copy_code                            │
│ barcode_value                        │
│ shelf_location                       │
│ condition                            │
│ status                               │
│ source                               │
│ acquisition_date                     │
│ price                                │
│ notes                                │
│ created_at                           │
│ updated_at                           │
└──────────────────────────────────────┘

┌────────────────────▼──────────────────┐
│         LibrarySetting                │
├──────────────────────────────────────┤
│ id (PK)                              │
│ key                                  │
│ value                                │
│ type                                 │
│ description                          │
│ group                                │
│ updated_by                           │
│ created_at                           │
│ updated_at                           │
└──────────────────────────────────────┘

┌────────────────────▼──────────────────┐
│            AuditLog                   │
├──────────────────────────────────────┤
│ id (PK)                              │
│ user_id (FK)                        │
│ action                               │
│ auditable_type                       │
│ auditable_id                         │
│ old_values                           │
│ new_values                           │
│ ip_address                           │
│ user_agent                           │
│ created_at                           │
└──────────────────────────────────────┘
```

### 3.2 Indexes

**members**
- PRIMARY: id
- UNIQUE: member_number
- UNIQUE: user_id
- INDEX: identity_number
- INDEX: type, status
- INDEX: name

**books**
- PRIMARY: id
- INDEX: category_id
- INDEX: isbn
- INDEX: title
- INDEX: author
- INDEX: status

**book_copies**
- PRIMARY: id
- UNIQUE: copy_code
- UNIQUE: barcode_value
- INDEX: book_id
- INDEX: status
- INDEX: shelf_location

**loan_transactions**
- PRIMARY: id
- UNIQUE: loan_number
- INDEX: member_id
- INDEX: borrowed_at
- INDEX: due_at
- INDEX: status

**loan_items**
- PRIMARY: id
- INDEX: loan_transaction_id
- INDEX: book_copy_id
- INDEX: status
- INDEX: due_at

**fines**
- PRIMARY: id
- INDEX: member_id
- INDEX: loan_item_id
- INDEX: status
- INDEX: type

**audit_logs**
- PRIMARY: id
- INDEX: user_id
- INDEX: auditable_type, auditable_id
- INDEX: action
- INDEX: created_at

---

## 4. API Design

### 4.1 API Versioning

```
/api/v1/members
/api/v1/books
```

Future: `/api/v2/` when breaking changes needed.

### 4.2 Response Format

**Success Response:**
```json
{
  "success": true,
  "data": { ... },
  "meta": {
    "current_page": 1,
    "per_page": 15,
    "total": 100,
    "last_page": 7
  }
}
```

**Error Response:**
```json
{
  "success": false,
  "message": "Validation failed",
  "errors": {
    "email": ["The email field is required."],
    "name": ["The name must be at least 3 characters."]
  }
}
```

### 4.3 Authentication

```
Authorization: Bearer {token}
```

Or session-based for web (Laravel default).

### 4.4 Pagination

Default: 15 items per page
Max: 100 items per page

```
GET /api/members?page=1&per_page=15
```

### 4.5 Filtering

```
GET /api/books?category_id=1&status=active&search=harry
GET /api/loans?member_id=5&status=active&from=2026-01-01&to=2026-01-31
```

### 4.6 Sorting

```
GET /api/books?sort=title&direction=asc
GET /api/books?sort=created_at&direction=desc
```

---

## 5. UI/UX Design

### 5.1 Design Tokens

**Colors:**
```css
:root {
  /* Primary */
  --color-primary-50: #eff6ff;
  --color-primary-100: #dbeafe;
  --color-primary-200: #bfdbfe;
  --color-primary-300: #93c5fd;
  --color-primary-400: #60a5fa;
  --color-primary-500: #3b82f6;
  --color-primary-600: #2563eb;  /* Main */
  --color-primary-700: #1d4ed8;
  --color-primary-800: #1e40af;
  --color-primary-900: #1e3a8a;

  /* Secondary (Emerald) */
  --color-secondary-600: #059669;

  /* Accent (Amber) */
  --color-accent-500: #f59e0b;
  --color-accent-600: #d97706;

  /* Semantic */
  --color-success: #059669;
  --color-warning: #f59e0b;
  --color-error: #dc2626;
  --color-info: #2563eb;

  /* Neutral */
  --color-gray-50: #f9fafb;
  --color-gray-100: #f3f4f6;
  --color-gray-200: #e5e7eb;
  --color-gray-300: #d1d5db;
  --color-gray-400: #9ca3af;
  --color-gray-500: #6b7280;
  --color-gray-600: #4b5563;
  --color-gray-700: #374151;
  --color-gray-800: #1f2937;
  --color-gray-900: #111827;
}
```

**Typography:**
```css
:root {
  --font-sans: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;

  --text-xs: 0.75rem;    /* 12px */
  --text-sm: 0.875rem;   /* 14px */
  --text-base: 1rem;     /* 16px */
  --text-lg: 1.125rem;   /* 18px */
  --text-xl: 1.25rem;    /* 20px */
  --text-2xl: 1.5rem;    /* 24px */
  --text-3xl: 1.875rem;  /* 30px */
  --text-4xl: 2.25rem;   /* 36px */

  --leading-tight: 1.25;
  --leading-normal: 1.5;
  --leading-relaxed: 1.625;
}
```

**Spacing:**
```css
:root {
  --space-0: 0;
  --space-1: 0.25rem;   /* 4px */
  --space-2: 0.5rem;    /* 8px */
  --space-3: 0.75rem;   /* 12px */
  --space-4: 1rem;      /* 16px */
  --space-5: 1.25rem;   /* 20px */
  --space-6: 1.5rem;    /* 24px */
  --space-8: 2rem;      /* 32px */
  --space-10: 2.5rem;   /* 40px */
  --space-12: 3rem;     /* 48px */
  --space-16: 4rem;     /* 64px */
}
```

**Border Radius:**
```css
:root {
  --radius-none: 0;
  --radius-sm: 0.25rem;   /* 4px */
  --radius-md: 0.375rem;  /* 6px */
  --radius-lg: 0.5rem;    /* 8px */
  --radius-xl: 0.75rem;   /* 12px */
  --radius-2xl: 1rem;     /* 16px */
  --radius-full: 9999px;
}
```

**Shadows:**
```css
:root {
  --shadow-sm: 0 1px 2px 0 rgb(0 0 0 / 0.05);
  --shadow-md: 0 4px 6px -1px rgb(0 0 0 / 0.1), 0 2px 4px -2px rgb(0 0 0 / 0.1);
  --shadow-lg: 0 10px 15px -3px rgb(0 0 0 / 0.1), 0 4px 6px -4px rgb(0 0 0 / 0.1);
  --shadow-xl: 0 20px 25px -5px rgb(0 0 0 / 0.1), 0 8px 10px -6px rgb(0 0 0 / 0.1);
}
```

### 5.2 Component Guidelines

**Buttons:**
```
Primary:   bg-primary-600, text-white, hover:bg-primary-700
Secondary: bg-gray-100, text-gray-700, hover:bg-gray-200
Danger:    bg-red-600, text-white, hover:bg-red-700
Outline:   border-2 border-primary-600, text-primary-600, hover:bg-primary-50
Ghost:     text-primary-600, hover:bg-primary-50
```

**Inputs:**
```
Default: border-gray-300, focus:ring-2 focus:ring-primary-500
Error: border-red-500, focus:ring-2 focus:ring-red-500
Disabled: bg-gray-100, cursor-not-allowed
```

**Cards:**
```
bg-white, rounded-lg, shadow-sm, border border-gray-200
Padding: 16px-24px
```

**Tables:**
```
Header: bg-gray-50, font-semibold, text-gray-600
Row: hover:bg-gray-50
Border: border-gray-200
```

**Modals:**
```
Overlay: bg-black/50
Content: bg-white, rounded-xl, shadow-xl, max-w-md
```

### 5.3 Layout Specifications

**Sidebar (Desktop):**
- Width: 240px
- Collapsed: 64px
- Background: white
- Border: right border-gray-200

**Header:**
- Height: 64px
- Background: white
- Border: bottom border-gray-200
- Shadow: shadow-sm

**Content Area:**
- Max width: 1280px (centered)
- Padding: 24px (desktop), 16px (mobile)

**Card Grid:**
- Gap: 24px
- Columns: 1 (mobile), 2 (tablet), 3-4 (desktop)

### 5.4 Navigation Structure

```
📚 Dashboard
   ├── Librarian Dashboard
   └── Principal Dashboard

👥 Data Master
   ├── Anggota
   ├── Kategori
   ├── Buku
   └── Eksemplar

📖 Transaksi
   ├── Peminjaman
   └── Pengembalian

💰 Denda

📖 Katalog Online

📊 Laporan

⚙️ Pengaturan

📋 Audit Log
```

### 5.5 State Guidelines

**Loading States:**
- Use skeleton screens for content
- Use spinners for actions
- Never show raw loading text

**Empty States:**
```
┌─────────────────────────────────┐
│                                 │
│        📚 (illustration)         │
│                                 │
│   Belum ada data anggota        │
│                                 │
│   Tambahkan anggota pertama     │
│   untuk memulai.               │
│                                 │
│      [+ Tambah Anggota]        │
│                                 │
└─────────────────────────────────┘
```

**Error States:**
- Clear error message
- Suggested action
- Support contact if needed

---

## 6. Security Guidelines

### 6.1 Authentication

- Password minimum 8 characters
- Password hashed with bcrypt (cost factor 12)
- Rate limit: 5 login attempts per 5 minutes
- Session timeout: 2 hours
- CSRF token on all forms

### 6.2 Authorization

- Role-based access control
- Policy-based authorization for models
- Middleware for route protection

### 6.3 Input Validation

- All user input validated
- SQL injection prevented via Eloquent
- XSS prevented via Blade escaping
- File upload: validate type and size

### 6.4 Data Protection

- Sensitive data (passwords) never logged
- Audit log for sensitive actions
- Soft delete for recoverable data

---

## 7. Performance Guidelines

### 7.1 Database

- Use indexes for frequent queries
- Eager load relationships (avoid N+1)
- Paginate large result sets
- Use cursor pagination for large exports

### 7.2 Caching

- Cache settings in memory
- Cache expensive queries (dashboard stats)
- Use database queue for background jobs

### 7.3 Frontend

- Lazy load images
- Minify assets in production
- Use CDN for static assets
- Implement infinite scroll for long lists

---

## 8. Error Handling

### 8.1 User-Facing Errors

```php
// Validation errors - show inline under fields
// Business logic errors - show toast notification
// Server errors - show friendly error page
```

### 8.2 Logging

```php
// Log all exceptions to storage/logs
// Include: user_id, request_id, stack trace
// Rotate logs weekly
```

---

## 9. Testing Strategy

### 9.1 Unit Tests

- Service layer business logic
- Model scopes and accessors
- Form request validation
- Utility functions

### 9.2 Feature Tests

- Controller endpoints
- User workflows
- Authorization checks
- Form submissions

### 9.3 Coverage Target

- Core features (Loan, Return, Fine): 80%+
- Overall application: 60%+

---

## 10. Appendix

### A. Code Style

- Follow PSR-12 coding standard
- Use Laravel Pint for formatting
- Use PHPStan for static analysis

### B. Git Workflow

- Branch: feature/, fix/, chore/
- Commit: conventional commits format
- PR: require 1 approval, passing tests

### C. Deployment

- Preview deployments for PRs
- Production deployment on merge to main
- Database migrations automated
