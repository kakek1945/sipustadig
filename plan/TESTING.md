# Testing Plan - Sirkulib MVP

**Project:** Sirkulib - Sistem Informasi Perpustakaan Sekolah
**Version:** 1.0
**Last Updated:** 2026-05-22

---

## 1. Testing Philosophy

### 1.1 Our Approach

> "Test early, test often, but don't test for the sake of testing. Every test should provide value."

**Principles:**
1. **Confidence over coverage** - Better to have fewer tests that we trust than many that we don't
2. **Test the important things** - Core business logic (loan, return, fine calculation) deserves the most attention
3. **Automate what we can** - CI/CD runs tests on every push
4. **Manual testing for UX** - Automate logic, humans test experience

### 1.2 Testing Pyramid

```
           ┌───────────────┐
           │     E2E      │  ← Few, critical flows
           │   (Cypress)  │
           ├───────────────┤
           │   Feature    │  ← Controller workflows
           │   (Laravel)  │
           ├───────────────┤
           │    Unit      │  ← Business logic
           │   (PHPUnit)  │
           └───────────────┘
```

---

## 2. Test Coverage Goals

| Module | Target Coverage | Priority |
|--------|----------------|----------|
| LoanService | 90% | 🔴 Critical |
| ReturnService | 90% | 🔴 Critical |
| FineService | 85% | 🔴 Critical |
| BookCopyService | 70% | 🟠 High |
| MemberService | 70% | 🟠 High |
| ReportService | 60% | 🟡 Medium |
| Overall | 60% | 🟡 Medium |

---

## 3. Unit Tests

### 3.1 LoanServiceTest

```php
tests/Unit/LoanServiceTest.php
```

| Test Case | Description | Status |
|-----------|-------------|--------|
| test_can_create_loan_for_valid_member | Happy path: valid member, valid copies | [ ] |
| test_cannot_loan_when_member_inactive | Member status = inactive | [ ] |
| test_cannot_loan_when_member_has_unpaid_fine | unpaid fine exists | [ ] |
| test_cannot_loan_when_exceeds_max_borrow | Student with 2 active loans tries to borrow | [ ] |
| test_cannot_loan_when_copy_not_available | Book copy status != available | [ ] |
| test_can_loan_multiple_copies_same_transaction | Max 5 copies in one transaction | [ ] |
| test_due_date_calculation_for_student | 7 days from today | [ ] |
| test_due_date_calculation_for_teacher | 14 days from today | [ ] |
| test_loan_number_uniqueness | Duplicate loan numbers rejected | [ ] |
| test_transaction_rollback_on_failure | DB transaction rollback works | [ ] |

### 3.2 ReturnServiceTest

```php
tests/Unit/ReturnServiceTest.php
```

| Test Case | Description | Status |
|-----------|-------------|--------|
| test_can_return_book_on_time | No fine charged | [ ] |
| test_calculates_late_days_correctly | Due: May 1, Returned: May 5 = 4 days | [ ] |
| test_calculates_fine_correctly | 4 days × Rp1,000 = Rp4,000 | [ ] |
| test_does_not_charge_fine_for_zero_days_late | Returned same day as due | [ ] |
| test_updates_copy_status_to_available | After return | [ ] |
| test_updates_copy_status_to_damaged | When return condition is damaged | [ ] |
| test_updates_copy_status_to_lost | When marked as lost | [ ] |
| test_updates_loan_status_to_returned | After all items returned | [ ] |
| test_can_partial_return | Return 2 of 3 borrowed books | [ ] |

### 3.3 FineServiceTest

```php
tests/Unit/FineServiceTest.php
```

| Test Case | Description | Status |
|-----------|-------------|--------|
| test_can_create_fine_from_return | Auto-create fine | [ ] |
| test_can_create_manual_fine | Manual fine for damaged/lost | [ ] |
| test_can_pay_fine_fully | paid_amount = amount | [ ] |
| test_can_pay_fine_partially | paid_amount < amount | [ ] |
| test_can_waive_fine_with_reason | waived_reason required | [ ] |
| test_cannot_waive_without_reason | Validation error | [ ] |
| test_fine_status_transitions | unpaid → partial → paid | [ ] |
| test_calculates_remaining_amount | amount - paid_amount | [ ] |

### 3.4 MemberServiceTest

```php
tests/Unit/MemberServiceTest.php
```

| Test Case | Description | Status |
|-----------|-------------|--------|
| test_can_create_member | Happy path | [ ] |
| test_member_number_auto_generated | Format: YEAR-XXXXX | [ ] |
| test_member_number_uniqueness | Duplicate rejected | [ ] |
| test_can_soft_delete_member | Sets status = inactive | [ ] |
| test_inactive_member_cannot_borrow | Validation check | [ ] |
| test_can_count_active_loans | | [ ] |

### 3.5 BookCopyServiceTest

```php
tests/Unit/BookCopyServiceTest.php
```

| Test Case | Description | Status |
|-----------|-------------|--------|
| test_can_generate_copy_code | Format: BK-001-001 | [ ] |
| test_copy_code_uniqueness | Duplicate rejected | [ ] |
| test_can_generate_barcode | Unique barcode value | [ ] |
| test_status_changes | available ↔ borrowed | [ ] |
| test_can_update_condition | Condition history | [ ] |

### 3.6 BarcodeServiceTest

```php
tests/Unit/BarcodeServiceTest.php
```

| Test Case | Description | Status |
|-----------|-------------|--------|
| test_generate_qr_code | Returns valid QR image | [ ] |
| test_generate_barcode_image | Returns barcode image | [ ] |
| test_barcode_value_matches | Encode/decode consistency | [ ] |

---

## 4. Feature Tests

### 4.1 AuthenticationTest

```php
tests/Feature/AuthenticationTest.php
```

| Test Case | Description | Status |
|-----------|-------------|--------|
| test_user_can_login_with_valid_credentials | Happy path | [ ] |
| test_user_cannot_login_with_invalid_credentials | Wrong password | [ ] |
| test_inactive_user_cannot_login | status = inactive | [ ] |
| test_user_can_logout | Session destroyed | [ ] |
| test_unauthenticated_user_redirected_to_login | Middleware works | [ ] |
| test_user_cannot_access_other_role_routes | Role middleware | [ ] |

### 4.2 MemberCrudTest

```php
tests/Feature/MemberCrudTest.php
```

| Test Case | Description | Status |
|-----------|-------------|--------|
| test_librarian_can_view_members_list | GET /members | [ ] |
| test_librarian_can_create_member | POST /members | [ ] |
| test_librarian_can_edit_member | PUT /members/{id} | [ ] |
| test_librarian_can_soft_delete_member | DELETE /members/{id} | [ ] |
| test_validation_errors_shown | Required fields | [ ] |
| test_search_filters_members | | [ ] |
| test_student_cannot_access_member_routes | | [ ] |

### 4.3 BookCrudTest

```php
tests/Feature/BookCrudTest.php
```

| Test Case | Description | Status |
|-----------|-------------|--------|
| test_librarian_can_view_books_list | GET /books | [ ] |
| test_librarian_can_create_book | POST /books | [ ] |
| test_librarian_can_upload_cover | Cover image upload | [ ] |
| test_librarian_can_edit_book | PUT /books/{id} | [ ] |
| test_book_cannot_be_deleted_if_has_copies | Prevent orphan copies | [ ] |
| test_search_works_by_title | | [ ] |
| test_filter_works_by_category | | [ ] |

### 4.4 LoanWorkflowTest

```php
tests/Feature/LoanWorkflowTest.php
```

| Test Case | Description | Status |
|-----------|-------------|--------|
| test_complete_loan_workflow | Scan member → Scan books → Confirm | [ ] |
| test_loan_requires_active_member | Validation | [ ] |
| test_loan_requires_available_copies | Validation | [ ] |
| test_loan_respects_max_borrow_limit | Validation | [ ] |
| test_loan_creates_transaction_record | DB record created | [ ] |
| test_loan_updates_copy_status | to "borrowed" | [ ] |
| test_loan_number_format_correct | TRX-YYYYMMDD-XXXXX | [ ] |

### 4.5 ReturnWorkflowTest

```php
tests/Feature/ReturnWorkflowTest.php
```

| Test Case | Description | Status |
|-----------|-------------|--------|
| test_complete_return_workflow | Scan copy → Confirm return | [ ] |
| test_return_on_time_no_fine | Due date not passed | [ ] |
| test_return_late_generates_fine | Days late × rate | [ ] |
| test_return_damaged_updates_copy | Status = damaged | [ ] |
| test_return_calculates_due_date | Days between borrow and return | [ ] |
| test_partial_return_works | Some items, not all | [ ] |

### 4.6 CatalogAccessTest

```php
tests/Feature/CatalogAccessTest.php
```

| Test Case | Description | Status |
|-----------|-------------|--------|
| test_authenticated_user_can_view_catalog | Login required | [ ] |
| test_guest_cannot_view_catalog | Redirect to login | [ ] |
| test_search_works | Title search | [ ] |
| test_filter_by_category_works | | [ ] |
| test_availability_shown | Available copies count | [ ] |
| test_inactive_books_hidden | | [ ] |

### 4.7 DashboardTest

```php
tests/Feature/DashboardTest.php
```

| Test Case | Description | Status |
|-----------|-------------|--------|
| test_librarian_sees_librarian_dashboard | Role-based view | [ ] |
| test_principal_sees_principal_dashboard | Different widgets | [ ] |
| test_stats_are_accurate | Counts match DB | [ ] |
| test_overdue_list_shows_correct_items | | [ ] |
| test_chart_data_structure_correct | JSON format | [ ] |

### 4.8 ReportGenerationTest

```php
tests/Feature/ReportGenerationTest.php
```

| Test Case | Description | Status |
|-----------|-------------|--------|
| test_book_report_generates | | [ ] |
| test_loan_report_filters_by_date | | [ ] |
| test_overdue_report_accuracy | | [ ] |
| test_excel_export_works | Download file | [ ] |
| test_csv_export_works | Download file | [ ] |

---

## 5. Browser Tests (E2E - Cypress)

### 5.1 Critical User Flows

| Test Case | Steps | Expected Result | Status |
|-----------|-------|-----------------|--------|
| E2E-001: Login Flow | Open / → Enter credentials → Submit | Redirected to dashboard | [ ] |
| E2E-002: Borrow Books | Scan member → Scan books → Confirm | Loan created, copies unavailable | [ ] |
| E2E-003: Return Books | Scan copy → Confirm return | Fine shown if late, copy available | [ ] |
| E2E-004: Catalog Search | Login → Open catalog → Search | Books displayed | [ ] |
| E2E-005: Export Report | Open reports → Select type → Export | Excel file downloads | [ ] |

### 5.2 Edge Cases

| Test Case | Description | Status |
|-----------|-------------|--------|
| E2E-010 | Member with max loans tries to borrow | Error shown |
| E2E-011 | Borrow already borrowed copy | Error shown |
| E2E-012 | Return with condition damaged | Fine modal appears |
| E2E-013 | Session timeout during transaction | Redirected to login |

---

## 6. Performance Tests

### 6.1 Load Testing Targets

| Endpoint | Target Response Time | Max Concurrent Users |
|----------|---------------------|---------------------|
| GET /dashboard | < 2s | 50 |
| GET /catalog | < 1s | 100 |
| POST /loans | < 1s | 20 |
| POST /loans/{id}/return | < 1s | 20 |
| GET /reports/books | < 5s | 10 |

### 6.2 Stress Test Scenarios

| Scenario | Description | Success Criteria |
|----------|-------------|------------------|
| ST-001 | 100 concurrent catalog searches | No timeouts, < 5% errors |
| ST-002 | 50 simultaneous loan transactions | All committed, < 3s response |
| ST-003 | Export 1000 books to Excel | Completes without crash |

---

## 7. Security Tests

### 7.1 OWASP Top 10 Checklist

| Vulnerability | Test | Status |
|---------------|------|--------|
| Injection | SQL injection via form inputs | [ ] |
| Broken Auth | Session hijacking attempt | [ ] |
| Sensitive Data Exposure | Password in responses | [ ] |
| XML External Entities | XXE in XML uploads | [ ] |
| Broken Access Control | Access other users' data | [ ] |
| Security Misconfiguration | Default credentials | [ ] |
| XSS | Script injection in search | [ ] |
| Insecure Deserialization | Import file parsing | [ ] |
| Known Vulnerabilities | Outdated dependencies | [ ] |
| Insufficient Logging | Missing audit events | [ ] |

### 7.2 Additional Security Tests

| Test | Description | Status |
|------|-------------|--------|
| SEC-001 | CSRF token required on all forms | [ ] |
| SEC-002 | File upload type validation | [ ] |
| SEC-003 | File upload size limit enforced | [ ] |
| SEC-004 | Rate limiting on login endpoint | [ ] |
| SEC-005 | Password hashing verified | [ ] |
| SEC-006 | Audit log captures sensitive actions | [ ] |

---

## 8. Test Environment Setup

### 8.1 Requirements

```
PHP 8.3+
PostgreSQL 14+
Composer 2.x
Node.js 20+
```

### 8.2 Setup Commands

```bash
# Clone & Install
git clone git@github.com:user/sirkulib.git
cd sirkulib
composer install
npm install

# Setup testing environment
cp .env.example .env.testing
php artisan key:generate --env=testing

# Create testing database
createdb sirkulib_testing

# Run migrations
php artisan migrate:fresh --env=testing

# (Optional) Seed minimal data
php artisan db:seed --env=testing

# Run tests
php artisan test

# With coverage
php artisan test --coverage
```

### 8.3 CI/CD Configuration

**.github/workflows/test.yml:**
```yaml
name: Tests
on: [push, pull_request]
jobs:
  test:
    runs-on: ubuntu-latest
    services:
      postgres:
        image: postgres:14
        env:
          POSTGRES_DB: sirkulib_testing
          POSTGRES_USER: postgres
          POSTGRES_PASSWORD: postgres
        options: >-
          --health-cmd pg_isready
          --health-interval 10s
          --health-timeout 5s
          --health-retries 5
    steps:
      - uses: actions/checkout@v4
      - uses: shivammathur/setup-php@v2
        with:
          php-version: '8.3'
      - uses: actions/cache@v3
        with:
          path: ~/.composer/cache
          key: composer-${{ hashFiles('**/composer.lock') }}
      - run: composer install --no-interaction
      - run: cp .env.example .env.testing
      - run: php artisan key:generate --env=testing
      - run: php artisan migrate --env=testing
      - run: php artisan test --env=testing
```

---

## 9. Test Reporting

### 9.1 Test Results Summary

After each test run:

| Metric | Value |
|--------|-------|
| Total Tests | |
| Passed | |
| Failed | |
| Skipped | |
| Coverage | |

### 9.2 Coverage Report

Generate with:
```bash
php artisan test --coverage-html coverage-report/
```

View in browser at `coverage-report/index.html`

---

## 10. Definition of Ready for Test

A feature is "Ready for Test" when:

1. ✅ Code is committed to feature branch
2. ✅ All unit tests pass locally
3. ✅ Feature tests written for happy path
4. ✅ Edge cases documented
5. ✅ No known bugs or todos in code
6. ✅ Review approved (if PR-based workflow)

---

## 11. Bug Reporting Template

```markdown
## Bug Report: [Short Title]

**Severity:** 🔴 Critical / 🟠 High / 🟡 Medium / 🟢 Low
**Status:** Open / In Progress / Resolved / Won't Fix

### Description
[Clear description of the bug]

### Steps to Reproduce
1. Go to...
2. Click on...
3. See error

### Expected Behavior
[What should happen]

### Actual Behavior
[What actually happens]

### Environment
- Browser:
- OS:
- Laravel Version:
- PHP Version:

### Screenshots
[If applicable]

### Stack Trace
```
[Error details]
```

### Additional Context
[Any other information]
```

---

## 12. Test Calendar

| Week | Focus | Milestone |
|------|-------|----------|
| Week 2 | Unit tests for services | Phase 0 complete |
| Week 4 | Feature tests for CRUD | Phase 1 complete |
| Week 6 | Transaction workflow tests | Phase 2 complete |
| Week 8 | E2E tests, performance tests | Phase 4 complete |
| Week 10 | Final regression testing | MVP launch ready |