<?php

namespace App\Livewire\Production\Admin;

use App\Models\Expense;
use App\Models\ProductionBatch;
use App\Models\ProductionMaterialBatch;
use App\Models\Setting;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.production.admin')]
#[Title('Production Salary')]
class ProductionSalaryReport extends Component
{
    public $selectedBatchId = null;
    public $manufactured_qty = 0;
    public $sale_price_per_piece = 400;
    public $base_salary_pool = 255000;
    public $target_incentive = 175000;
    public $extra_salary = 0;
    public $epf_employee_rate = 8;
    public $epf_employer_rate = 12;
    public $etf_rate = 3;
    public array $commissionSettings = [
        'threshold_items' => 10000,
        'rate_upto_threshold' => 10,
        'rate_after_threshold' => 15,
    ];
    public array $fixedCategories = [
        'Electricity',
        'Packing Expenses',
        'Depreciation',
        'Transport',
        'Hosting Charges',
        'Sundry Expenses',
        'Supervisor Daily Expenses',
    ];

    public function mount(): void
    {
        $this->loadCommissionSettings();

        $batch = $this->batches->first();
        $this->selectedBatchId = $batch?->id;
        $this->syncBatchDefaults();
    }

    public function updatedSelectedBatchId(): void
    {
        $this->syncBatchDefaults();
    }

    private function loadCommissionSettings(): void
    {
        $settings = Setting::query()
            ->whereIn('key', [
                'production_commission_threshold_items',
                'production_commission_rate_upto_threshold',
                'production_commission_rate_after_threshold',
            ])
            ->pluck('value', 'key');

        $this->commissionSettings = [
            'threshold_items' => (int) ($settings['production_commission_threshold_items'] ?? 10000),
            'rate_upto_threshold' => (float) ($settings['production_commission_rate_upto_threshold'] ?? 10),
            'rate_after_threshold' => (float) ($settings['production_commission_rate_after_threshold'] ?? 15),
        ];
    }

    private function syncBatchDefaults(): void
    {
        $batch = $this->selectedBatch;

        if (!$batch) {
            $this->manufactured_qty = 0;
            return;
        }

        $this->manufactured_qty = (int) ($batch->completed_qty > 0 ? $batch->completed_qty : $batch->target_qty);
    }

    public function getBatchesProperty()
    {
        return ProductionBatch::with(['material', 'supervisor', 'days'])
            ->latest()
            ->get();
    }

    public function getSelectedBatchProperty()
    {
        if (!$this->selectedBatchId) {
            return null;
        }

        return ProductionBatch::with(['material', 'supervisor', 'days.staff_commissions'])->find($this->selectedBatchId);
    }

    public function getCostStatementProperty(): array
    {
        $batch = $this->selectedBatch;

        if (!$batch) {
            return [];
        }

        $producedQty = max((int) $this->manufactured_qty, 0);
        $plannedTon = (float) ($batch->planned_material_ton ?? 0);
        $materialBatchQuery = ProductionMaterialBatch::query()
            ->where('production_material_id', $batch->production_material_id)
            ->whereRaw('UPPER(COALESCE(size, "")) = ?', [strtoupper((string) $batch->size)]);

        $matchedMaterialBatches = $materialBatchQuery->get();
        $totalQty = (float) $matchedMaterialBatches->sum('quantity');
        $weightedCost = $totalQty > 0
            ? (float) $matchedMaterialBatches->sum(fn($row) => ((float) $row->quantity) * ((float) $row->cost_price)) / $totalQty
            : 0;

        $materialCost = round($plannedTon * $weightedCost, 2);
        $batchExpenses = (float) $batch->days->sum('expense_amount');
        $expenseRows = Expense::query()
            ->where('module', 'production')
            ->where('production_batch_id', $batch->id)
            ->get();

        $fixedExpenses = [];
        foreach ($this->fixedCategories as $category) {
            $fixedExpenses[$category] = (float) $expenseRows
                ->where('category', $category)
                ->sum('amount');
        }

        if ((float) ($fixedExpenses['Supervisor Daily Expenses'] ?? 0) <= 0 && $batchExpenses > 0) {
            $fixedExpenses['Supervisor Daily Expenses'] = $batchExpenses;
        }
        $staffCommission = (float) $batch->days->sum(function ($day) {
            return collect($day->staff_commissions ?? [])->sum('amount');
        });

        $threshold = (int) ($this->commissionSettings['threshold_items'] ?? 10000);
        $rateUpto = (float) ($this->commissionSettings['rate_upto_threshold'] ?? 10);
        $rateAfter = (float) ($this->commissionSettings['rate_after_threshold'] ?? 15);
        $targetCommission = ($producedQty <= $threshold)
            ? ($producedQty * $rateUpto)
            : (($threshold * $rateUpto) + (($producedQty - $threshold) * $rateAfter));

        $basicSalary = (float) $this->base_salary_pool;
        $grossSalary = $basicSalary + (float) $this->target_incentive + (float) $this->extra_salary;
        $employeeEpf = round($grossSalary * ((float) $this->epf_employee_rate / 100), 2);
        $employerEpf = round($grossSalary * ((float) $this->epf_employer_rate / 100), 2);
        $etf = round($grossSalary * ((float) $this->etf_rate / 100), 2);
        $netSalary = round($grossSalary - $employeeEpf, 2);

        $fixedCost = round((float) collect($fixedExpenses)->sum(), 2);

        $salaryCost = round($grossSalary + $employerEpf + $etf + $targetCommission + $staffCommission, 2);
        $materialPerPiece = $producedQty > 0 ? round($materialCost / $producedQty, 2) : 0;
        $saleRevenue = round($producedQty * (float) $this->sale_price_per_piece, 2);
        $totalCost = round($materialCost + $fixedCost + $salaryCost, 2);
        $profit = round($saleRevenue - $totalCost, 2);

        return [
            'batch' => $batch,
            'produced_qty' => $producedQty,
            'planned_ton' => $plannedTon,
            'material_cost' => $materialCost,
            'material_per_piece' => $materialPerPiece,
            'batch_expenses' => $batchExpenses,
            'fixed_expenses' => $fixedExpenses,
            'fixed_cost' => $fixedCost,
            'staff_commission' => $staffCommission,
            'target_commission' => $targetCommission,
            'basic_salary' => $basicSalary,
            'gross_salary' => $grossSalary,
            'employee_epf' => $employeeEpf,
            'employer_epf' => $employerEpf,
            'etf' => $etf,
            'net_salary' => $netSalary,
            'salary_cost' => $salaryCost,
            'revenue' => $saleRevenue,
            'total_cost' => $totalCost,
            'profit' => $profit,
        ];
    }

    public function render()
    {
        return view('livewire.production.admin.production-salary-report', [
            'batches' => $this->batches,
            'costStatement' => $this->costStatement,
        ]);
    }
}
