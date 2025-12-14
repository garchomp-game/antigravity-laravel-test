<?php

namespace App\Livewire\Audit;

use App\Models\AuditLog;
use App\Models\Tenant;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public string $actionFilter = '';
    public string $actorSearch = '';
    public ?string $dateFrom = null;
    public ?string $dateTo = null;

    // Detail modal state
    public bool $showDetailModal = false;
    public ?string $selectedLogId = null;

    protected $queryString = [
        'actionFilter' => ['except' => ''],
        'actorSearch' => ['except' => ''],
        'dateFrom' => ['except' => null],
        'dateTo' => ['except' => null],
    ];

    public function updatingActionFilter(): void
    {
        $this->resetPage();
    }

    public function updatingActorSearch(): void
    {
        $this->resetPage();
    }

    public function updatingDateFrom(): void
    {
        $this->resetPage();
    }

    public function updatingDateTo(): void
    {
        $this->resetPage();
    }

    public function showDetails(string $logId): void
    {
        $this->selectedLogId = $logId;
        $this->showDetailModal = true;
    }

    public function closeDetails(): void
    {
        $this->showDetailModal = false;
        $this->selectedLogId = null;
    }

    public function getSelectedLogProperty(): ?AuditLog
    {
        if (!$this->selectedLogId) {
            return null;
        }

        return AuditLog::with('actor')
            ->where('tenant_id', Tenant::current()?->id)
            ->find($this->selectedLogId);
    }

    public function render()
    {
        $tenant = Tenant::current();

        $query = AuditLog::query()
            ->where('tenant_id', $tenant?->id)
            ->with('actor');

        // Action filter
        if ($this->actionFilter) {
            $query->where('action', $this->actionFilter);
        }

        // Actor search (by name or email)
        if ($this->actorSearch) {
            $query->whereHas('actor', function ($q) {
                $q->where('name', 'like', '%' . $this->actorSearch . '%')
                    ->orWhere('email', 'like', '%' . $this->actorSearch . '%');
            });
        }

        // Date range filter
        if ($this->dateFrom) {
            $query->whereDate('created_at', '>=', $this->dateFrom);
        }
        if ($this->dateTo) {
            $query->whereDate('created_at', '<=', $this->dateTo);
        }

        $logs = $query->orderBy('created_at', 'desc')->paginate(15);

        // Get distinct actions for filter dropdown
        $actions = AuditLog::where('tenant_id', $tenant?->id)
            ->distinct()
            ->pluck('action')
            ->sort()
            ->values();

        return view('livewire.audit.index', [
            'logs' => $logs,
            'actions' => $actions,
        ]);
    }
}
