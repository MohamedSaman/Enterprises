<?php

namespace App\Livewire\Production\Admin;

use App\Models\Expense;
use App\Models\ProductionBatch;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.production.admin')]
#[Title('Production Expenses')]
class ProductionExpenses extends Component
{
    public $selectedBatchId = null;
    public string $date = '';
    public string $category = 'Electricity';
    public $amount = 0;
    public string $description = '';
    public ?int $editingExpenseId = null;

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
        $this->date = now()->format('Y-m-d');
        $this->selectedBatchId = $this->batches->first()?->id;
    }

    public function getBatchesProperty()
    {
        return ProductionBatch::query()
            ->orderByDesc('id')
            ->get();
    }

    public function getBatchExpensesProperty()
    {
        if (!$this->selectedBatchId) {
            return collect();
        }

        return Expense::query()
            ->where('module', 'production')
            ->where('production_batch_id', (int) $this->selectedBatchId)
            ->orderByDesc('date')
            ->orderByDesc('id')
            ->get();
    }

    public function getCategoryTotalsProperty(): array
    {
        $totals = [];

        foreach ($this->fixedCategories as $category) {
            $totals[$category] = (float) $this->batchExpenses
                ->where('category', $category)
                ->sum('amount');
        }

        return $totals;
    }

    public function saveExpense(): void
    {
        $this->validate([
            'selectedBatchId' => 'required|exists:production_batches,id',
            'date' => 'required|date',
            'category' => 'required|string|max:120',
            'amount' => 'required|numeric|min:0',
            'description' => 'nullable|string|max:1000',
        ]);

        Expense::updateOrCreate(
            ['id' => $this->editingExpenseId],
            [
                'category' => $this->category,
                'expense_type' => 'fixed',
                'module' => 'production',
                'production_batch_id' => (int) $this->selectedBatchId,
                'amount' => (float) $this->amount,
                'date' => $this->date,
                'status' => 'Paid',
                'description' => $this->description ?: null,
            ]
        );

        $this->resetForm();
        $this->dispatch('alert', ['message' => 'Production expense saved successfully.', 'type' => 'success']);
    }

    public function editExpense(int $expenseId): void
    {
        $expense = Expense::query()
            ->where('module', 'production')
            ->findOrFail($expenseId);

        $this->editingExpenseId = $expense->id;
        $this->selectedBatchId = (int) $expense->production_batch_id;
        $this->date = optional($expense->date)->format('Y-m-d') ?: now()->format('Y-m-d');
        $this->category = (string) $expense->category;
        $this->amount = (float) $expense->amount;
        $this->description = (string) ($expense->description ?? '');
    }

    public function deleteExpense(int $expenseId): void
    {
        Expense::query()
            ->where('module', 'production')
            ->findOrFail($expenseId)
            ->delete();

        if ($this->editingExpenseId === $expenseId) {
            $this->resetForm();
        }

        $this->dispatch('alert', ['message' => 'Production expense deleted successfully.', 'type' => 'success']);
    }

    public function resetForm(): void
    {
        $this->editingExpenseId = null;
        $this->date = now()->format('Y-m-d');
        $this->category = 'Electricity';
        $this->amount = 0;
        $this->description = '';
    }

    public function render()
    {
        return view('livewire.production.admin.production-expenses', [
            'batches' => $this->batches,
            'expenses' => $this->batchExpenses,
            'categoryTotals' => $this->categoryTotals,
        ]);
    }
}
