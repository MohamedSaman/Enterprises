# Monthly Salary Management System - Implementation Summary

## Overview

A comprehensive monthly salary management system has been implemented for the production module, replacing the batch-wise salary calculation with month-wise salary generation. The system includes advanced commission calculations, attendance tracking, and employee-specific salary components.

---

## Components Created

### 1. **Dummy Data Seeders**

#### File: `database/seeders/UserSeeder.php`

- Enhanced with production staff users (supervisor + 6 workers)
- Creates UserDetail records with salary information
- Assigns basic salary, allowance, and department details

#### File: `database/seeders/AttendanceSeeder.php`

- Generates attendance records for all staff users for the current month
- Creates realistic check-in/check-out times with break durations
- Assigns 90% attendance probability
- Includes overtime calculation based on 8-hour workday

#### File: `database/seeders/ProductionSalarySettingsSeeder.php`

- Initializes all salary-related settings in the Settings table
- Default working days: 25 days/month
- Commission thresholds and rates
- EPF/ETF deduction rates
- Overtime multipliers

#### File: `database/seeders/DatabaseSeeder.php`

- Updated to run all three seeders in sequence

---

### 2. **Database Model & Migration**

#### File: `database/migrations/2026_04_20_000000_create_monthly_salaries_table.php`

**Schema:**

- `id` - Primary key
- `user_id` - Foreign key to users table
- `year`, `month` - Date identifiers (unique constraint)
- Salary components:
  - `working_days`, `attendance_days`, `paid_leave_days`
  - `basic_salary`, `attendance_bonus`, `commission`
  - `overtime_hours`, `overtime_amount`
  - `gross_salary`, `net_salary`
- Deductions: `epf_employee`, `epf_employer`, `etf`, `other_deductions`
- `include_epf_etf` - Toggle for tax deductions
- `status` - enum: 'draft', 'generated', 'approved', 'paid'
- Soft deletes for audit trail

#### File: `app/Models/MonthlySalary.php`

- Eloquent model with proper relationships
- Scopes for querying by month and filtered status
- Automatic timestamp tracking

---

### 3. **Livewire Component**

#### File: `app/Livewire/Production/Admin/MonthlySalaryReport.php`

**Key Features:**

- Month/Year selection (only past months shown)
- Employee selection with production module access
- Real-time salary calculation
- Attendance tracking with status checks

**Methods:**

- `calculateSalary()` - Main calculation engine
- `calculateCommission()` - Complex commission logic
- `calculateBatchCommission()` - Production-based commission
- `generateSalary()` - Saves salary to database
- `editSalary()`, `deleteSalary()` - Management operations

**Commission Calculation Logic:**

```
1. For each production batch in the month:
   - Get total items produced
   - Calculate base commission: First 10,000 @ Rs.10/item, rest @ Rs.15/item
   - Get total team attendance days
   - Daily rate = Total Commission / Total Attendance Days

2. For each employee:
   - Attendance days in batch
   - If attended >= 20 days: get full daily rate commission
   - If attended < 20 days: get 50% of daily rate commission
   - Supervisor: multiply by 2x

3. Total employee commission across all batches
```

---

### 4. **Blade Template**

#### File: `resources/views/livewire/production/admin/monthly-salary-report.blade.php`

**UI Features:**

- Modern gradient design matching production module
- Month/Year/Employee selection at top
- Real-time salary breakdowns
- Statistics cards for key metrics
- Toggle switch for EPF/ETF inclusion
- Salary components table with all deductions
- Generated salaries table with actions
- Print-friendly layout
- Responsive design for all screen sizes

**Sections:**

1. Month selection (only past months)
2. Employee selection (production staff only)
3. Employee salary details display
4. Salary components breakdown
5. Deductions and net salary calculation
6. Commission rules reference
7. Generated salaries table with edit/delete actions

---

## Settings Configuration

The following settings are stored in the `settings` table and can be managed:

| Key                                                  | Default Value | Description                                 |
| ---------------------------------------------------- | ------------- | ------------------------------------------- |
| `production_salary_working_days_per_month`           | 25            | Working days per month                      |
| `production_salary_paid_leave_days`                  | 14            | Yearly paid leave days                      |
| `production_salary_attendance_bonus`                 | 500           | Attendance bonus per day                    |
| `production_commission_threshold_items`              | 10000         | Item threshold for commission tiers         |
| `production_commission_rate_upto_threshold`          | 10            | Commission rate per item (up to threshold)  |
| `production_commission_rate_after_threshold`         | 15            | Commission rate per item (after threshold)  |
| `production_salary_overtime_multiplier`              | 1.5           | Overtime hour multiplier                    |
| `production_salary_etf_rate`                         | 3             | ETF deduction percentage                    |
| `production_salary_epf_employee_rate`                | 8             | EPF employee contribution %                 |
| `production_salary_epf_employer_rate`                | 12            | EPF employer contribution %                 |
| `production_salary_supervisor_commission_multiplier` | 2             | Supervisor gets 2x commission               |
| `production_salary_min_attendance_full_commission`   | 20            | Min days for full commission (50% if below) |

---

## Route

#### File: `routes/web.php`

```php
Route::get('/monthly-salary', \App\Livewire\Production\Admin\MonthlySalaryReport::class)->name('monthly-salary');
```

- Route: `/production/admin/monthly-salary`
- Name: `production.admin.monthly-salary`
- Accessible from: Navigation dropdown under "Salary" section

---

## Navigation Update

#### File: `resources/views/components/layouts/production/admin.blade.php`

- Added "Monthly Salary" link in Salary dropdown
- Updated active state detection for routing
- Link appears after "Batch Salary Report" and before "Staff List"

---

## Model Relationships

### User Model Update

```php
// Added relationship
public function monthlySalaries()
{
    return $this->hasMany(MonthlySalary::class, 'user_id', 'id');
}
```

---

## Usage Guide

### 1. **Accessing the System**

- Navigate to Production Admin Dashboard
- Go to Salary → Monthly Salary
- Or use URL: `/production/admin/monthly-salary`

### 2. **Generating Salary**

1. Select month (only past months available)
2. Select employee from dropdown
3. System auto-displays salary breakdown
4. Toggle EPF/ETF if needed
5. Click "Generate Salary"

### 3. **Salary Calculation Flow**

```
Basic Salary (from UserDetail)
+ Attendance Bonus (days_attended × bonus_per_day)
+ Commission (based on production batches)
+ Overtime (overtime_hours × hourly_rate × multiplier)
= Gross Salary

Gross Salary
- EPF Employee (if enabled)
= Net Salary

Company pays:
- EPF Employer
- ETF
```

### 4. **Commission Example**

```
Month: April 2026
Team size: 5 (Supervisor + 4 Workers)
Total working days: 25
Items produced: 15,000

Commission structure:
- First 10,000 items: Rs. 10/item = Rs. 100,000
- Remaining 5,000 items: Rs. 15/item = Rs. 75,000
- Total commission: Rs. 175,000

Attendance:
- Supervisor: 24 days
- Worker 1: 23 days
- Worker 2: 20 days
- Worker 3: 25 days
- Worker 4: 23 days
- Total: 115 days

Daily rate: Rs. 175,000 ÷ 115 = Rs. 1,521.74/day

Supervisor (24 days):
- Base: 24 × 1,521.74 = Rs. 36,521.76
- With 2x multiplier: Rs. 73,043.52

Worker 2 (20 days, at threshold):
- Gets 50% commission: 20 × 1,521.74 × 0.5 = Rs. 15,217.40

Worker 1 (23 days, above threshold):
- Gets full commission: 23 × 1,521.74 = Rs. 35,000.02
```

### 5. **Managing Generated Salaries**

- View all generated salaries in table below
- Status indicators: Draft, Generated, Approved, Paid
- Edit salary (if not paid) - click pencil icon
- Delete salary (if not paid) - click trash icon
- Cannot regenerate salary for same month (prevents duplicate entries)

### 6. **Paid Leave Handling**

- Automatic calculation: 14 days/year ÷ 12 months = ~1.17 days/month
- Deducted from working days
- Does not affect commission calculation

---

## Technical Details

### Attendance Status Enum Values

- Status field: 'present' or 'absent'
- Present_status field: 'late', 'early', 'ontime'

### Salary Status Workflow

```
Draft → Generated → Approved → Paid
                        ↓
                    (cannot regenerate)
```

### Commission Rules Implementation

1. **Production Item Tiers:**
   - Configurable threshold items count
   - Different rates for pre/post threshold

2. **Attendance-Based Commission:**
   - Total commission divided by total team days
   - Employees get daily rate × their attendance days
   - Minimum threshold for full commission

3. **Supervisor Multiplier:**
   - Supervisors automatically get 2x commission
   - Identified by supervisor_id in ProductionBatch

---

## Database Seeding

To generate test data:

```bash
php artisan migrate
php artisan db:seed DatabaseSeeder
```

**Generated Data:**

- 1 Admin user
- 1 Inventory staff user
- 1 Supervisor (production)
- 6 Production workers
- Full month attendance records
- All salary settings

---

## File Summary

| File                               | Type      | Purpose                     |
| ---------------------------------- | --------- | --------------------------- |
| AttendanceSeeder.php               | Seeder    | Generate attendance data    |
| UserSeeder.php                     | Seeder    | Generate staff with details |
| ProductionSalarySettingsSeeder.php | Seeder    | Initialize salary settings  |
| create_monthly_salaries_table.php  | Migration | Database schema             |
| MonthlySalary.php                  | Model     | Eloquent model              |
| MonthlySalaryReport.php            | Component | Livewire logic              |
| monthly-salary-report.blade.php    | View      | UI template                 |
| web.php                            | Routes    | URL routing                 |
| admin.blade.php                    | Layout    | Navigation update           |

---

## Future Enhancements

1. **Bulk salary generation** - Generate for all employees in one click
2. **Salary slip PDF** - Download individual salary slips
3. **Salary history** - View historical salary records
4. **Leave management** - Integrate leave request system
5. **Payroll export** - Export to accounting software
6. **Salary approvals** - Multi-level approval workflow
7. **Advance salary** - Track and deduct salary advances

---

## Notes

- All calculations are done in real-time as user selects options
- Commission calculations are complex and accurate
- Soft deletes allow salary history tracking
- System prevents duplicate salary generation for same month
- UI is fully responsive and print-friendly
- All settings are easily adjustable in Settings table
- Attendance bonus is configurable per day
