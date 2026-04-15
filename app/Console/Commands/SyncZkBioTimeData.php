<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use App\Models\ZkEmployee;
use App\Services\ZkBioService;
use Exception;

class SyncZkBioTimeData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'zkbio:sync
                            {--employees : Sync only employees}
                            {--departments : Sync only departments}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync employees and departments from ZKBio Zlink Cloud API';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('╔══════════════════════════════════════╗');
        $this->info('║  ZKBio Zlink Cloud  ─  Data Sync    ║');
        $this->info('╚══════════════════════════════════════╝');
        $this->newLine();

        try {
            $zkbio = new ZkBioService();

            // ───────────────────────────────────────────────
            // STEP 1 ─ Authentication (Tenant Token)
            // ───────────────────────────────────────────────
            $this->info('🔑  Authenticating with ZKBio Zlink Cloud...');
            $token = $zkbio->authenticate();
            $maskedToken = substr($token, 0, 8) . '...' . substr($token, -6);
            $this->info("✅  Authenticated — Token: {$maskedToken}");
            $this->newLine();

            $syncAll = !$this->option('employees') && !$this->option('departments');

            // ───────────────────────────────────────────────
            // STEP 2 ─ Departments
            // ───────────────────────────────────────────────
            if ($syncAll || $this->option('departments')) {
                $this->syncDepartments($zkbio);
            }

            // ───────────────────────────────────────────────
            // STEP 3 ─ Employees
            // ───────────────────────────────────────────────
            if ($syncAll || $this->option('employees')) {
                $this->syncEmployees($zkbio);
            }

            $this->newLine();
            $this->info('🎉  ZKBio Zlink sync completed successfully!');
            return Command::SUCCESS;

        } catch (Exception $e) {
            $this->error("Fatal error: {$e->getMessage()}");
            Log::error('ZKBio Sync Fatal Exception', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return Command::FAILURE;
        }
    }

    /**
     * Sync all employees from ZKBio Zlink Cloud.
     */
    private function syncEmployees(ZkBioService $zkbio): void
    {
        try {
            $this->info('👥  Fetching employees (all pages)...');

            $employees = $zkbio->getEmployees();
            $count     = count($employees);

            $this->info("   Found {$count} employees. Syncing...");

            if ($count === 0) {
                $this->warn('   No employees returned from API.');
                return;
            }

            $bar = $this->output->createProgressBar($count);
            $bar->start();

            foreach ($employees as $emp) {
                // ZKBio Zlink Cloud API response fields:
                // id, employeeCode, firstName, email, phone, gender,
                // joinDate, probationEnd, departmentId, designationId
                ZkEmployee::updateOrCreate(
                    ['emp_code' => (string) ($emp['employeeCode'] ?? '')],
                    [
                        'first_name'      => $emp['firstName'] ?? null,
                        'last_name'       => $emp['lastName'] ?? null,
                        'department_id'   => $emp['departmentId'] ?? null,
                        'department_code' => null, // Will be filled from department sync
                        'department_name' => null, // Will be filled from department sync
                        'hire_date'       => $emp['joinDate'] ?? null,
                    ]
                );

                $bar->advance();
            }

            $bar->finish();
            $this->newLine();
            $this->info("✅  Employees synced: {$count}");
            $this->newLine();

        } catch (Exception $e) {
            $this->error("Employee sync error: {$e->getMessage()}");
            Log::error('ZKBio Employee Sync Exception', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
        }
    }

    /**
     * Sync departments from ZKBio Zlink Cloud.
     */
    private function syncDepartments(ZkBioService $zkbio): void
    {
        try {
            $this->info('🏢  Fetching departments (all pages)...');

            $departments = $zkbio->getDepartments();
            $count       = count($departments);

            $this->info("   Found {$count} departments.");

            if ($count === 0) {
                $this->warn('   No departments returned from API.');
                return;
            }

            // Update employee records with department info
            foreach ($departments as $dept) {
                $deptId   = $dept['departmentId'] ?? $dept['id'] ?? null;
                $deptName = $dept['name'] ?? $dept['departmentName'] ?? null;
                $deptCode = $dept['code'] ?? $dept['departmentCode'] ?? null;

                if ($deptId) {
                    ZkEmployee::where('department_id', $deptId)
                        ->update([
                            'department_name' => $deptName,
                            'department_code' => $deptCode,
                        ]);
                }

                $this->line("   • {$deptName} ({$deptCode})");
            }

            $this->info("✅  Departments synced: {$count}");
            $this->newLine();

        } catch (Exception $e) {
            $this->error("Department sync error: {$e->getMessage()}");
            Log::error('ZKBio Department Sync Exception', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
        }
    }
}
