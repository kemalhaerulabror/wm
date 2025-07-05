<?php

namespace App\Livewire\Admin;

use App\Models\Order;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Carbon;

class OrderList extends Component
{
    use WithPagination;

    public $search = '';
    public $statusFilter = '';
    public $dateFilter = '';
    public $perPage = 10;
    public $sortField = 'created_at';
    public $sortDirection = 'desc';
    public $isPolling = true;
    public $pollingInterval = 10000; // 10 detik
    public $adminCreatedFilter = false; // Filter untuk pesanan yang dibuat oleh admin

    protected $queryString = [
        'search' => ['except' => ''],
        'statusFilter' => ['except' => ''],
        'dateFilter' => ['except' => ''],
        'sortField' => ['except' => 'created_at'],
        'sortDirection' => ['except' => 'desc'],
        'adminCreatedFilter' => ['except' => false],
    ];

    // Mendengarkan event jika ada pesanan baru masuk
    protected $listeners = ['orderAdded' => '$refresh'];

    public function getPollingStateProperty()
    {
        return $this->isPolling;
    }

    public function togglePolling()
    {
        $this->isPolling = !$this->isPolling;
    }

    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingStatusFilter()
    {
        $this->resetPage();
    }

    public function updatingDateFilter()
    {
        $this->resetPage();
    }
    
    public function resetFilters()
    {
        $this->search = '';
        $this->statusFilter = '';
        $this->dateFilter = '';
        $this->adminCreatedFilter = false;
        $this->resetPage();
    }

    public function render()
    {
        $query = Order::query()
            ->with('user')
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('order_number', 'like', '%' . $this->search . '%')
                      ->orWhereHas('user', function ($user) {
                          $user->where('name', 'like', '%' . $this->search . '%')
                               ->orWhere('email', 'like', '%' . $this->search . '%');
                      });
                });
            })
            ->when($this->statusFilter, function ($query) {
                $query->where('status', $this->statusFilter);
            })
            ->when($this->dateFilter, function ($query) {
                if ($this->dateFilter === 'today') {
                    $query->whereDate('created_at', Carbon::today());
                } elseif ($this->dateFilter === 'week') {
                    $query->whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()]);
                } elseif ($this->dateFilter === 'month') {
                    $query->whereMonth('created_at', Carbon::now()->month);
                }
            })
            ->when($this->adminCreatedFilter, function ($query) {
                $query->whereNotNull('created_by_admin_id');
            })
            ->orderBy($this->sortField, $this->sortDirection);

        $orders = $query->paginate($this->perPage);

        $totalOrders = Order::count();
        $pendingOrders = Order::where('status', 'pending')->count();
        $processingOrders = Order::where('status', 'processing')->count();
        $completedOrders = Order::where('status', 'completed')->count();
        $cancelledOrders = Order::where('status', 'cancelled')->count();

        return view('livewire.admin.order-list', [
            'orders' => $orders,
            'totalOrders' => $totalOrders,
            'pendingOrders' => $pendingOrders,
            'processingOrders' => $processingOrders,
            'completedOrders' => $completedOrders,
            'cancelledOrders' => $cancelledOrders,
        ]);
    }
} 