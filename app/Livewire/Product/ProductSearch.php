<?php

namespace App\Livewire\Product;

use Livewire\Component;
use App\Models\Product;
use Livewire\WithPagination;

class ProductSearch extends Component
{
    use WithPagination;
    
    public $search = '';
    public $results = [];
    public $showResults = false;
    public $searchDebounce = 500; // milliseconds for debounce
    
    protected $listeners = ['clickOutside' => 'hideResults'];
    
    // Mengupdate hasil pencarian setiap kali nilai search berubah
    public function updatedSearch()
    {
        if (strlen($this->search) >= 2) {
            $this->results = Product::where('name', 'like', '%' . $this->search . '%')
                ->where('status', true)
                ->take(5)
                ->get();
            $this->showResults = true;
        } else {
            $this->results = [];
            $this->showResults = false;
        }
    }
    
    // Menyembunyikan hasil pencarian
    public function hideResults()
    {
        $this->showResults = false;
    }
    
    // Membuka halaman hasil pencarian lengkap
    public function searchAll()
    {
        if (strlen($this->search) >= 2) {
            return redirect()->route('search', ['query' => $this->search]);
        }
    }
    
    public function render()
    {
        return view('livewire.product.product-search');
    }
}
