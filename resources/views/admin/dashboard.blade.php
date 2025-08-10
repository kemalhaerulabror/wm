@extends('admin.layouts.admin')

@section('title', 'Dashboard')

@section('content')
<div class="py-3 md:py-4">
    <!-- Header Dashboard dengan Rentang Tanggal -->
    <div class="mb-4 md:mb-6">
        <h1 class="text-xl md:text-2xl font-bold text-gray-800">Dashboard</h1>
        <div class="flex flex-col sm:flex-row mt-1 justify-between items-start sm:items-center">
            <p class="text-sm md:text-base text-gray-600">Selamat datang di panel admin Wipa Motor</p>
            
            @if(isset($startDateFormatted) && isset($endDateFormatted))
                <p class="text-xs md:text-sm text-indigo-600 font-medium">
                    <i class="fas fa-calendar-alt mr-1"></i> Data untuk periode: {{ \Carbon\Carbon::parse($startDateFormatted)->isoFormat('D MMM YYYY') }} - {{ \Carbon\Carbon::parse($endDateFormatted)->isoFormat('D MMM YYYY') }}
                </p>
            @endif
        </div>
    </div>
    
    <!-- Stats Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 md:gap-6 mb-4 md:mb-6">
        <div class="bg-white rounded-lg shadow-sm p-4 md:p-6">
            <div class="flex justify-between">
                <div>
                    <p class="text-gray-500 text-xs md:text-sm">Total Penjualan Periode</p>
                    <h3 class="text-lg md:text-2xl font-bold text-gray-800">Rp{{ number_format($totalSales, 0, ',', '.') }}</h3>
                    <p class="text-xs md:text-sm text-{{ $orderPercentage >= 0 ? 'green' : 'red' }}-500">{{ $orderPercentage >= 0 ? '+' : '' }}{{ $orderPercentage }}% dari periode sebelumnya</p>
                </div>
                <div class="flex items-center justify-center h-10 w-10 md:h-12 md:w-12 rounded-full bg-gray-100 text-gray-800">
                    <i class="fas fa-money-bill-wave"></i>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow-sm p-4 md:p-6">
            <div class="flex justify-between">
                <div>
                    <p class="text-gray-500 text-xs md:text-sm">Pesanan Periode</p>
                    <h3 class="text-lg md:text-2xl font-bold text-gray-800">{{ $totalOrders }}</h3>
                    <p class="text-xs md:text-sm text-{{ $orderPercentage >= 0 ? 'green' : 'red' }}-500">{{ $orderPercentage >= 0 ? '+' : '' }}{{ $orderPercentage }}% dari periode sebelumnya</p>
                </div>
                <div class="flex items-center justify-center h-10 w-10 md:h-12 md:w-12 rounded-full bg-gray-100 text-gray-800">
                    <i class="fas fa-shopping-cart"></i>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow-sm p-4 md:p-6">
            <div class="flex justify-between">
                <div>
                    <p class="text-gray-500 text-xs md:text-sm">Pelanggan Baru Periode</p>
                    <h3 class="text-lg md:text-2xl font-bold text-gray-800">{{ $totalCustomers }}</h3>
                    <p class="text-xs md:text-sm text-{{ $customerPercentage >= 0 ? 'green' : 'red' }}-500">{{ $customerPercentage >= 0 ? '+' : '' }}{{ $customerPercentage }}% dari periode sebelumnya</p>
                </div>
                <div class="flex items-center justify-center h-10 w-10 md:h-12 md:w-12 rounded-full bg-gray-100 text-gray-800">
                    <i class="fas fa-users"></i>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow-sm p-4 md:p-6">
            <div class="flex justify-between">
                <div>
                    <p class="text-gray-500 text-xs md:text-sm">Motor Terjual Periode</p>
                    <h3 class="text-lg md:text-2xl font-bold text-gray-800">{{ $motorsSold }}</h3>
                    <p class="text-xs md:text-sm text-{{ $soldPercentage >= 0 ? 'green' : 'red' }}-500">{{ $soldPercentage >= 0 ? '+' : '' }}{{ $soldPercentage }}% dari periode sebelumnya</p>
                </div>
                <div class="flex items-center justify-center h-10 w-10 md:h-12 md:w-12 rounded-full bg-gray-100 text-gray-800">
                    <i class="fas fa-motorcycle"></i>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Export Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
        <!-- Ekspor Data Harian -->
        <div class="bg-white p-4 rounded-lg shadow-sm border-l-4 border-blue-500">
            <div class="flex flex-col">
                <h3 class="text-base font-medium mb-2 flex items-center">
                    <i class="fas fa-calendar-day text-blue-500 mr-2"></i>
                    Ekspor Data Harian
                </h3>
                <p class="text-sm text-gray-600 mb-3">Ekspor laporan berdasarkan rentang tanggal tertentu</p>
                
                <form action="{{ route('admin.dashboard.export') }}" method="GET" class="flex flex-col gap-2 mb-3">
                    <input type="hidden" name="period_type" value="daily">
                    <div class="flex flex-col gap-1">
                        <label for="daily_start_date" class="text-xs font-medium text-gray-700">Tanggal Mulai:</label>
                        <input type="date" id="daily_start_date" name="start_date" class="text-sm border border-gray-300 rounded px-2 py-1" value="{{ $startDateFormatted }}" required>
                    </div>
                    <div class="flex flex-col gap-1">
                        <label for="daily_end_date" class="text-xs font-medium text-gray-700">Tanggal Akhir:</label>
                        <input type="date" id="daily_end_date" name="end_date" class="text-sm border border-gray-300 rounded px-2 py-1" value="{{ $endDateFormatted }}" required>
                    </div>
                    <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white py-2 px-4 rounded text-sm flex items-center justify-center mt-2">
                        <i class="fas fa-file-export mr-2"></i>
                        Ekspor Data
                    </button>
                </form>
            </div>
        </div>
        
        <!-- Ekspor Data Bulanan -->
        <div class="bg-white p-4 rounded-lg shadow-sm border-l-4 border-green-500">
            <div class="flex flex-col">
                <h3 class="text-base font-medium mb-2 flex items-center">
                    <i class="fas fa-calendar-alt text-green-500 mr-2"></i>
                    Ekspor Data Bulanan
                </h3>
                <p class="text-sm text-gray-600 mb-3">Ekspor laporan bulanan dengan data terkonsolidasi</p>
                
                <form id="monthlyExportForm" action="{{ route('admin.dashboard.export') }}" method="GET" class="flex flex-col gap-2 mb-3">
                    <input type="hidden" name="period_type" value="monthly">
                    <!-- Input sesungguhnya yang akan dikirim ke server -->
                    <input type="hidden" id="real_start_date" name="start_date">
                    <input type="hidden" id="real_end_date" name="end_date">
                    
                    <div class="flex flex-col gap-1">
                        <label for="display_start_month" class="text-xs font-medium text-gray-700">Bulan Awal:</label>
                        <input type="month" id="display_start_month" class="text-sm border border-gray-300 rounded px-2 py-1" value="{{ \Carbon\Carbon::parse($startDateFormatted)->format('Y-m') }}" required>
                    </div>
                    <div class="flex flex-col gap-1">
                        <label for="display_end_month" class="text-xs font-medium text-gray-700">Bulan Akhir:</label>
                        <input type="month" id="display_end_month" class="text-sm border border-gray-300 rounded px-2 py-1" value="{{ \Carbon\Carbon::parse($endDateFormatted)->format('Y-m') }}" required>
                    </div>
                    <button type="button" onclick="submitMonthlyForm()" class="bg-green-500 hover:bg-green-600 text-white py-2 px-4 rounded text-sm flex items-center justify-center mt-2">
                        <i class="fas fa-file-export mr-2"></i>
                        Ekspor Data
                    </button>
                </form>
            </div>
        </div>
        
        <!-- Ekspor Data Tahunan -->
        <div class="bg-white p-4 rounded-lg shadow-sm border-l-4 border-purple-500">
            <div class="flex flex-col">
                <h3 class="text-base font-medium mb-2 flex items-center">
                    <i class="fas fa-chart-line text-purple-500 mr-2"></i>
                    Ekspor Data Tahunan
                </h3>
                <p class="text-sm text-gray-600 mb-3">Ekspor laporan tahunan untuk analisis jangka panjang</p>
                
                <form action="{{ route('admin.dashboard.export') }}" method="GET" class="flex flex-col gap-2 mb-3">
                    <input type="hidden" name="period_type" value="yearly">
                    <div class="flex flex-col gap-1">
                        <label for="yearly_start_year" class="text-xs font-medium text-gray-700">Tahun Awal:</label>
                        <select id="yearly_start_year" name="start_date" class="text-sm border border-gray-300 rounded px-2 py-1" required>
                            @for($year = date('Y'); $year >= 2020; $year--)
                                <option value="{{ $year }}-01-01" {{ \Carbon\Carbon::parse($startDateFormatted)->format('Y') == $year ? 'selected' : '' }}>{{ $year }}</option>
                            @endfor
                        </select>
                    </div>
                    <div class="flex flex-col gap-1">
                        <label for="yearly_end_year" class="text-xs font-medium text-gray-700">Tahun Akhir:</label>
                        <select id="yearly_end_year" name="end_date" class="text-sm border border-gray-300 rounded px-2 py-1" required>
                            @for($year = date('Y'); $year >= 2020; $year--)
                                <option value="{{ $year }}-12-31" {{ \Carbon\Carbon::parse($endDateFormatted)->format('Y') == $year ? 'selected' : '' }}>{{ $year }}</option>
                            @endfor
                        </select>
                    </div>
                    <button type="submit" class="bg-purple-500 hover:bg-purple-600 text-white py-2 px-4 rounded text-sm flex items-center justify-center mt-2">
                        <i class="fas fa-file-export mr-2"></i>
                        Ekspor Data
                    </button>
                </form>
            </div>
        </div>
    </div>
    
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 md:gap-6">
        <!-- Sales Chart -->
        <div class="lg:col-span-2 bg-white p-4 md:p-6 rounded-lg shadow-sm">
            <div class="flex flex-col space-y-4 mb-4">
                <h3 class="text-base md:text-lg font-medium">Grafik Penjualan</h3>
                <div class="w-full">
                    <form id="dateFilterForm" action="{{ route('admin.dashboard') }}" method="GET" class="flex flex-col sm:flex-row items-start sm:items-center gap-2">
                        <div class="flex flex-wrap items-center gap-2">
                            <input 
                                type="date" 
                                name="start_date" 
                                id="start_date" 
                                value="{{ $startDateFormatted }}" 
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-xs md:text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 p-1.5 md:p-2 w-[120px] md:w-auto"
                            >
                            <span class="text-xs md:text-sm">s/d</span>
                            <input 
                                type="date" 
                                name="end_date" 
                                id="end_date" 
                                value="{{ $endDateFormatted }}" 
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-xs md:text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 p-1.5 md:p-2 w-[120px] md:w-auto"
                            >
                        </div>
                        <div class="flex items-center gap-2">
                            <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white py-1.5 md:py-2 px-3 md:px-4 rounded-lg text-xs md:text-sm">
                                Terapkan
                            </button>
                            <button type="button" id="resetDate" class="bg-gray-200 hover:bg-gray-300 text-gray-700 py-1.5 md:py-2 px-3 md:px-4 rounded-lg text-xs md:text-sm">
                                Reset
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            <div id="salesChart" class="h-60 md:h-80"></div>
        </div>
        
        <!-- Recent Orders -->
        <div class="bg-white p-4 md:p-6 rounded-lg shadow-sm">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-base md:text-lg font-medium">Pesanan Terbaru</h3>
                <a href="{{ route('admin.orders.index') }}" class="text-gray-600 hover:underline text-xs md:text-sm">Lihat Semua</a>
            </div>
            <div class="space-y-3 md:space-y-4 overflow-y-auto max-h-[500px]">
                @forelse($recentOrders as $order)
                <div class="border-b pb-3">
                    <div class="flex justify-between mb-1">
                        <p class="text-xs md:text-sm font-medium">#{{ $order->order_number }}</p>
                        @php
                            $statusClass = [
                                'pending' => 'bg-yellow-100 text-yellow-800',
                                'processing' => 'bg-blue-100 text-blue-800', 
                                'completed' => 'bg-green-100 text-green-800',
                                'cancelled' => 'bg-red-100 text-red-800'
                            ][$order->status] ?? 'bg-gray-100 text-gray-800';
                            
                            $statusLabel = [
                                'pending' => 'Menunggu',
                                'processing' => 'Diproses',
                                'completed' => 'Selesai',
                                'cancelled' => 'Dibatalkan'
                            ][$order->status] ?? 'Tidak Diketahui';
                        @endphp
                        <span class="text-xs px-2 py-0.5 rounded-full {{ $statusClass }}">{{ $statusLabel }}</span>
                    </div>
                    
                    <!-- Tampilkan nama pelanggan -->
                    <p class="text-xs md:text-sm text-gray-700 font-medium line-clamp-1 mb-0.5">
                        @if($order->customer_name)
                            {{ $order->customer_name }}
                        @elseif($order->user)
                            {{ $order->user->name }}
                        @else
                            Pelanggan
                        @endif
                    </p>
                    
                    <!-- Tampilkan produk yang dibeli -->
                    <p class="text-xs md:text-sm text-gray-500 mb-1 line-clamp-1">
                        @if($order->items->isNotEmpty())
                            {{ $order->items->first()->product_name }}
                            @if($order->items->count() > 1)
                                dan {{ $order->items->count() - 1 }} item lainnya
                            @endif
                        @else
                            Tidak ada item
                        @endif
                    </p>
                    
                    <p class="text-xs text-gray-500">{{ $order->created_at->setTimezone('Asia/Jakarta')->isoFormat('D MMM YYYY') }}</p>
                </div>
                @empty
                <div class="text-gray-500 text-center py-4">
                    <p class="text-xs md:text-sm">Belum ada pesanan</p>
                </div>
                @endforelse
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
    // Data chart penjualan
    var options = {
        series: [{
            name: 'Penjualan',
            data: {!! json_encode($salesData) !!}
        }],
        chart: {
            type: 'area',
            height: 320,
            zoom: {
                enabled: false
            },
            toolbar: {
                show: false
            }
        },
        dataLabels: {
            enabled: false
        },
        stroke: {
            curve: 'smooth',
            width: 3
        },
        colors: ['#4B5563'],
        fill: {
            type: 'gradient',
            gradient: {
                shadeIntensity: 1,
                opacityFrom: 0.7,
                opacityTo: 0.3,
                stops: [0, 90, 100]
            }
        },
        xaxis: {
            categories: {!! json_encode($dateLabels) !!},
            labels: {
                rotate: -45,
                rotateAlways: false,
                style: {
                    fontSize: '12px'
                }
            }
        },
        tooltip: {
            y: {
                formatter: function(val) {
                    return "Rp " + val.toLocaleString('id-ID')
                }
            }
        }
    };
    
    var salesChart = new ApexCharts(document.querySelector("#salesChart"), options);
    salesChart.render();
    
    // Event listener untuk reset button
    document.getElementById('resetDate').addEventListener('click', function() {
        // Set tanggal ke 7 hari terakhir
        var today = new Date();
        var lastWeek = new Date();
        lastWeek.setDate(today.getDate() - 6);
        
        document.getElementById('start_date').value = formatDate(lastWeek);
        document.getElementById('end_date').value = formatDate(today);
        
        // Submit form
        document.getElementById('dateFilterForm').submit();
    });
    
    // Helper function untuk format tanggal YYYY-MM-DD
    function formatDate(date) {
        var d = new Date(date),
            month = '' + (d.getMonth() + 1),
            day = '' + d.getDate(),
            year = d.getFullYear();
    
        if (month.length < 2) 
            month = '0' + month;
        if (day.length < 2) 
            day = '0' + day;
    
        return [year, month, day].join('-');
    }
    
    // Fungsi untuk submit form ekspor bulanan
    function submitMonthlyForm() {
        // Mengambil nilai dari form
        var startMonthInput = document.getElementById('display_start_month').value;
        var endMonthInput = document.getElementById('display_end_month').value;
        
        // Memastikan input tidak kosong
        if (!startMonthInput || !endMonthInput) {
            alert('Silakan pilih bulan awal dan akhir');
            return false;
        }
        
        // Mengekstrak tahun dan bulan dari input
        var [startYear, startMonth] = startMonthInput.split('-');
        var [endYear, endMonth] = endMonthInput.split('-');
        
        // Format untuk tanggal awal (selalu tanggal 1)
        var formattedStartDate = startYear + '-' + startMonth + '-01';
        
        // Format untuk tanggal akhir (hari terakhir bulan)
        var lastDayOfMonth = new Date(endYear, endMonth, 0).getDate();
        var formattedEndDate = endYear + '-' + endMonth + '-' + lastDayOfMonth;
        
        // Mengisi hidden input dengan nilai yang benar
        document.getElementById('real_start_date').value = formattedStartDate;
        document.getElementById('real_end_date').value = formattedEndDate;
        
        // Submit form
        document.getElementById('monthlyExportForm').submit();
    }
</script>
@endsection
