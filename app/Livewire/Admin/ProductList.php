<?php

namespace App\Livewire\Admin;

use App\Models\Product;
use Livewire\Component;
use Livewire\WithPagination;

class ProductList extends Component
{
    use WithPagination;

    protected $paginationTheme = 'tailwind';
    
    // Filter properties
    public $search = '';
    public $categoryFilter = '';
    public $brandFilter = '';
    public $priceMin = '';
    public $priceMax = '';
    public $status = '';
    public $sortField = 'created_at';
    public $sortDirection = 'desc';
    
    // Categories and brands
    public $categories = ['Motor Matic', 'Motor Bebek', 'Motor Sport'];
    public $brands = ['Honda', 'Yamaha', 'Suzuki', 'Kawasaki'];
    
    protected $queryString = [
        'search' => ['except' => ''],
        'categoryFilter' => ['except' => ''],
        'brandFilter' => ['except' => ''],
        'status' => ['except' => ''],
        'sortField' => ['except' => 'created_at'],
        'sortDirection' => ['except' => 'desc'],
    ];
    
    public function updatingSearch()
    {
        $this->resetPage();
    }
    
    public function updatingCategoryFilter()
    {
        $this->resetPage();
    }
    
    public function updatingBrandFilter()
    {
        $this->resetPage();
    }
    
    public function updatingStatus()
    {
        $this->resetPage();
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
    
    public function render()
    {
        $products = Product::query()
            ->when($this->search, function ($query) {
                return $query->where('name', 'like', '%' . $this->search . '%')
                    ->orWhere('description', 'like', '%' . $this->search . '%');
            })
            ->when($this->categoryFilter, function ($query) {
                return $query->where('category', $this->categoryFilter);
            })
            ->when($this->brandFilter, function ($query) {
                return $query->where('brand', $this->brandFilter);
            })
            ->when($this->priceMin !== '', function ($query) {
                return $query->where('price', '>=', (float) $this->priceMin);
            })
            ->when($this->priceMax !== '', function ($query) {
                return $query->where('price', '<=', (float) $this->priceMax);
            })
            ->when($this->status !== '', function ($query) {
                return $query->where('status', $this->status === '1');
            })
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate(10);
            
        return view('livewire.admin.product-list', [
            'products' => $products
        ]);
    }
    
    public function resetFilters()
    {
        $this->reset(['search', 'categoryFilter', 'brandFilter', 'priceMin', 'priceMax', 'status']);
        $this->resetPage();
    }
}
