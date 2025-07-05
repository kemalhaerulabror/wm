<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Admin;
use App\Models\User;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class AdminController extends Controller
{
    public function index(Request $request)
    {
        // Default tanggal (7 hari terakhir)
        $endDate = Carbon::now();
        $startDate = Carbon::now()->subDays(6); // 7 hari termasuk hari ini
        
        // Jika ada request filter tanggal
        if ($request->has('start_date') && $request->has('end_date')) {
            $startDate = Carbon::parse($request->start_date);
            $endDate = Carbon::parse($request->end_date);
        }
        
        // Menghitung total penjualan (dengan filter tanggal)
        $totalSales = Order::where('payment_status', 'paid')
                    ->whereBetween('created_at', [
                        $startDate->copy()->startOfDay(),
                        $endDate->copy()->endOfDay()
                    ])
                    ->sum('total_amount');

        // Menghitung total pesanan (dengan filter tanggal)
        $totalOrders = Order::whereBetween('created_at', [
                    $startDate->copy()->startOfDay(),
                    $endDate->copy()->endOfDay()
                ])
                ->count();

        // Menghitung persentase kenaikan pesanan dari periode waktu sebelumnya
        $previousStartDate = (clone $startDate)->subDays($startDate->diffInDays($endDate) + 1);
        $previousEndDate = (clone $startDate)->subDay();
        
        $previousPeriodOrders = Order::whereBetween('created_at', [
                $previousStartDate->copy()->startOfDay(),
                $previousEndDate->copy()->endOfDay()
            ])
            ->count();
        
        $orderPercentage = 0;
        if ($previousPeriodOrders > 0) {
            $orderPercentage = round((($totalOrders - $previousPeriodOrders) / $previousPeriodOrders) * 100);
        }

        // Menghitung total pelanggan baru dalam periode (dengan filter tanggal)
        $totalCustomers = User::whereBetween('created_at', [
                    $startDate->copy()->startOfDay(),
                    $endDate->copy()->endOfDay()
                ])
                ->count();

        // Menghitung persentase kenaikan pelanggan dari periode waktu sebelumnya
        $previousPeriodCustomers = User::whereBetween('created_at', [
                $previousStartDate->copy()->startOfDay(),
                $previousEndDate->copy()->endOfDay()
            ])
            ->count();
        
        $customerPercentage = 0;
        if ($previousPeriodCustomers > 0) {
            $customerPercentage = round((($totalCustomers - $previousPeriodCustomers) / $previousPeriodCustomers) * 100);
        }

        // Menghitung motor terjual dalam periode 
        // Menggunakan OrderItem untuk menghitung jumlah motor terjual dalam periode
        $motorsSold = OrderItem::whereHas('order', function($query) use ($startDate, $endDate) {
                    $query->where('payment_status', 'paid')
                        ->whereBetween('created_at', [
                            $startDate->copy()->startOfDay(),
                            $endDate->copy()->endOfDay()
                        ]);
                })
                ->sum('quantity');

        // Menghitung persentase kenaikan motor terjual dari periode waktu sebelumnya
        $previousPeriodMotorsSold = OrderItem::whereHas('order', function($query) use ($previousStartDate, $previousEndDate) {
                $query->where('payment_status', 'paid')
                    ->whereBetween('created_at', [
                        $previousStartDate->copy()->startOfDay(),
                        $previousEndDate->copy()->endOfDay()
                    ]);
            })
            ->sum('quantity');
        
        $soldPercentage = 0;
        if ($previousPeriodMotorsSold > 0) {
            $soldPercentage = round((($motorsSold - $previousPeriodMotorsSold) / $previousPeriodMotorsSold) * 100);
        }

        // Data untuk grafik penjualan berdasarkan rentang tanggal
        $salesData = [];
        $dateLabels = [];
        
        // Hitung jumlah hari dalam rentang
        $diffInDays = $startDate->diffInDays($endDate) + 1;
        
        // Format label berdasarkan rentang waktu
        if ($diffInDays <= 31) {
            // Tampilkan data harian jika rentang 31 hari atau kurang
            $currentDate = clone $startDate;
            while ($currentDate <= $endDate) {
                $dateLabels[] = $currentDate->format('d M');
                $salesData[] = Order::whereDate('created_at', $currentDate->format('Y-m-d'))
                            ->where('payment_status', 'paid')
                            ->sum('total_amount');
                $currentDate->addDay();
            }
        } else if ($diffInDays <= 92) {
            // Tampilkan data mingguan jika rentang 3 bulan atau kurang
            $currentDate = clone $startDate;
            while ($currentDate <= $endDate) {
                $weekEndDate = (clone $currentDate)->addDays(6);
                if ($weekEndDate > $endDate) {
                    $weekEndDate = clone $endDate;
                }
                $dateLabels[] = $currentDate->format('d M') . ' - ' . $weekEndDate->format('d M');
                $salesData[] = Order::whereBetween('created_at', [
                                $currentDate->format('Y-m-d') . ' 00:00:00', 
                                $weekEndDate->format('Y-m-d') . ' 23:59:59'
                             ])
                             ->where('payment_status', 'paid')
                             ->sum('total_amount');
                $currentDate->addDays(7);
            }
        } else {
            // Tampilkan data bulanan jika rentang lebih dari 3 bulan
            $currentDate = clone $startDate->startOfMonth();
            $endMonth = clone $endDate->startOfMonth();
            
            while ($currentDate <= $endMonth) {
                $dateLabels[] = $currentDate->format('M Y');
                $salesData[] = Order::whereYear('created_at', $currentDate->year)
                              ->whereMonth('created_at', $currentDate->month)
                              ->where('payment_status', 'paid')
                              ->sum('total_amount');
                $currentDate->addMonth();
            }
        }
        
        // Format tanggal untuk datepicker
        $startDateFormatted = $startDate->format('Y-m-d');
        $endDateFormatted = $endDate->format('Y-m-d');

        // Mendapatkan pesanan terbaru
        $recentOrders = Order::with('items.product')
                       ->orderBy('created_at', 'desc')
                       ->take(4)
                       ->get();

        return view('admin.dashboard', compact(
            'totalSales', 
            'totalOrders', 
            'orderPercentage',
            'totalCustomers', 
            'customerPercentage',
            'motorsSold', 
            'soldPercentage',
            'salesData',
            'dateLabels',
            'startDateFormatted',
            'endDateFormatted',
            'recentOrders'
        ));
    }

    public function exportToExcel(Request $request)
    {
        // Default tanggal (7 hari terakhir)
        $endDate = Carbon::now();
        $startDate = Carbon::now()->subDays(6); // 7 hari termasuk hari ini
        $periodType = $request->period_type ?? 'daily'; // default: daily (harian)
        
        // Jika ada request filter tanggal
        if ($request->has('start_date') && $request->has('end_date')) {
            $startDate = Carbon::parse($request->start_date)->startOfDay();
            $endDate = Carbon::parse($request->end_date)->endOfDay();
        }
        
        // Membuat spreadsheet baru
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        
        // Judul laporan
        $sheet->setCellValue('A1', 'LAPORAN DASHBOARD MOTORSHOP');
        $sheet->mergeCells('A1:G1');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        
        // Periode waktu
        $sheet->setCellValue('A2', 'Periode: ' . $startDate->format('d M Y') . ' - ' . $endDate->format('d M Y'));
        $sheet->mergeCells('A2:G2');
        $sheet->getStyle('A2')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        
        // Header untuk ringkasan
        $sheet->setCellValue('A4', 'RINGKASAN');
        $sheet->mergeCells('A4:G4');
        $sheet->getStyle('A4')->getFont()->setBold(true);
        
        // Data ringkasan
        $sheet->setCellValue('A5', 'Total Penjualan:');
        
        // Menghitung total penjualan (dengan filter tanggal)
        $totalSales = Order::where('payment_status', 'paid')
                    ->whereBetween('created_at', [
                        $startDate->copy(),
                        $endDate->copy()
                    ])
                    ->sum('total_amount');
        $sheet->setCellValue('B5', 'Rp ' . number_format($totalSales, 0, ',', '.'));
        
        // Total pesanan
        $sheet->setCellValue('A6', 'Total Pesanan:');
        $totalOrders = Order::whereBetween('created_at', [
                    $startDate->copy(),
                    $endDate->copy()
                ])
                ->count();
        $sheet->setCellValue('B6', $totalOrders);
        
        // Total pesanan dibatalkan
        $sheet->setCellValue('A7', 'Pesanan Dibatalkan:');
        $cancelledOrders = Order::where('status', 'cancelled')
                    ->whereBetween('created_at', [
                        $startDate->copy(),
                        $endDate->copy()
                    ])
                    ->count();
        $sheet->setCellValue('B7', $cancelledOrders);
        
        // Total pelanggan baru
        $sheet->setCellValue('A8', 'Pelanggan Baru:');
        $totalCustomers = User::whereBetween('created_at', [
                    $startDate->copy(),
                    $endDate->copy()
                ])->count();
        $sheet->setCellValue('B8', $totalCustomers);
        
        // Motor terjual
        $sheet->setCellValue('A9', 'Motor Terjual:');
        $motorsSold = OrderItem::whereHas('order', function($query) use ($startDate, $endDate) {
            $query->where('payment_status', 'paid')
                ->whereBetween('created_at', [
                    $startDate->copy(),
                    $endDate->copy()
                ]);
        })->sum('quantity');
        $sheet->setCellValue('B9', $motorsSold);
        
        // Header untuk data penjualan
        $sheet->setCellValue('A11', 'DETAIL PENJUALAN');
        $sheet->mergeCells('A11:G11');
        $sheet->getStyle('A11')->getFont()->setBold(true);
        
        // Header tabel penjualan
        $sheet->setCellValue('A12', 'No');
        $sheet->setCellValue('B12', 'Periode');
        $sheet->setCellValue('C12', 'Penjualan');
        $sheet->setCellValue('D12', 'Pesanan');
        $sheet->setCellValue('E12', 'Pesanan Dibatalkan');
        $sheet->setCellValue('F12', 'Motor Terjual');
        $sheet->setCellValue('G12', 'Pelanggan Baru');
        
        $sheet->getStyle('A12:G12')->getFont()->setBold(true);
        $sheet->getStyle('A12:G12')->getFill()
            ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()->setRGB('D3D3D3');
        
        // Data untuk detail penjualan berdasarkan periode
        $row = 13;
        $no = 1;
        $salesDetail = [];
        
        // Validasi rentang waktu - untuk menghindari looping berlebihan
        $diffInDays = $startDate->diffInDays($endDate) + 1;
        if ($diffInDays > 366 && $periodType == 'daily') {
            $periodType = 'monthly'; // Konversi ke bulanan jika rentang terlalu lebar
        }
        
        if ($periodType == 'daily') {
            // Tampilkan data harian
            $currentDate = clone $startDate;
            while ($currentDate <= $endDate) {
                $dateStr = $currentDate->format('Y-m-d');
                $dateLabel = $currentDate->format('d M Y');
                
                // Hitung data untuk tanggal ini
                $dateSales = Order::whereDate('created_at', $dateStr)
                            ->where('payment_status', 'paid')
                            ->sum('total_amount');
                            
                $dateOrders = Order::whereDate('created_at', $dateStr)->count();
                
                $dateCancelledOrders = Order::whereDate('created_at', $dateStr)
                            ->where('status', 'cancelled')
                            ->count();
                
                $dateMotorsSold = OrderItem::whereHas('order', function($query) use ($dateStr) {
                    $query->where('payment_status', 'paid')
                        ->whereDate('created_at', $dateStr);
                })->sum('quantity');
                
                $dateCustomers = User::whereDate('created_at', $dateStr)->count();
                
                // Tambahkan ke array
                $salesDetail[] = [
                    'periode' => $dateLabel,
                    'penjualan' => $dateSales,
                    'pesanan' => $dateOrders,
                    'pesanan_dibatalkan' => $dateCancelledOrders,
                    'motor_terjual' => $dateMotorsSold,
                    'pelanggan_baru' => $dateCustomers
                ];
                
                $currentDate->addDay();
            }
        } elseif ($periodType == 'monthly' || ($periodType == 'auto' && $diffInDays > 31 && $diffInDays <= 366)) {
            // Tampilkan data bulanan
            $currentDate = clone $startDate->startOfMonth();
            $endMonth = clone $endDate->startOfMonth();
            
            while ($currentDate <= $endMonth) {
                $monthLabel = $currentDate->format('M Y');
                $year = $currentDate->year;
                $month = $currentDate->month;
                
                // Hitung data untuk bulan ini
                $monthSales = Order::whereYear('created_at', $year)
                            ->whereMonth('created_at', $month)
                            ->where('payment_status', 'paid')
                            ->sum('total_amount');
                            
                $monthOrders = Order::whereYear('created_at', $year)
                            ->whereMonth('created_at', $month)
                            ->count();
                
                $monthCancelledOrders = Order::whereYear('created_at', $year)
                            ->whereMonth('created_at', $month)
                            ->where('status', 'cancelled')
                            ->count();
                
                $monthMotorsSold = OrderItem::whereHas('order', function($query) use ($year, $month) {
                    $query->where('payment_status', 'paid')
                        ->whereYear('created_at', $year)
                        ->whereMonth('created_at', $month);
                })->sum('quantity');
                
                $monthCustomers = User::whereYear('created_at', $year)
                                ->whereMonth('created_at', $month)
                                ->count();
                
                // Tambahkan ke array
                $salesDetail[] = [
                    'periode' => $monthLabel,
                    'penjualan' => $monthSales,
                    'pesanan' => $monthOrders,
                    'pesanan_dibatalkan' => $monthCancelledOrders,
                    'motor_terjual' => $monthMotorsSold,
                    'pelanggan_baru' => $monthCustomers
                ];
                
                $currentDate->addMonth();
            }
        } elseif ($periodType == 'yearly' || ($periodType == 'auto' && $diffInDays > 366)) {
            // Tampilkan data tahunan
            $startYear = $startDate->year;
            $endYear = $endDate->year;
            
            for ($year = $startYear; $year <= $endYear; $year++) {
                // Hitung data untuk tahun ini
                $yearSales = Order::whereYear('created_at', $year)
                          ->where('payment_status', 'paid')
                          ->sum('total_amount');
                          
                $yearOrders = Order::whereYear('created_at', $year)->count();
                
                $yearCancelledOrders = Order::whereYear('created_at', $year)
                          ->where('status', 'cancelled')
                          ->count();
                
                $yearMotorsSold = OrderItem::whereHas('order', function($query) use ($year) {
                    $query->where('payment_status', 'paid')
                        ->whereYear('created_at', $year);
                })->sum('quantity');
                
                $yearCustomers = User::whereYear('created_at', $year)->count();
                
                // Tambahkan ke array
                $salesDetail[] = [
                    'periode' => $year,
                    'penjualan' => $yearSales,
                    'pesanan' => $yearOrders,
                    'pesanan_dibatalkan' => $yearCancelledOrders,
                    'motor_terjual' => $yearMotorsSold,
                    'pelanggan_baru' => $yearCustomers
                ];
            }
        }
        
        // Isi data ke excel
        foreach ($salesDetail as $detail) {
            $sheet->setCellValue('A' . $row, $no);
            $sheet->setCellValue('B' . $row, $detail['periode']);
            $sheet->setCellValue('C' . $row, 'Rp ' . number_format($detail['penjualan'], 0, ',', '.'));
            $sheet->setCellValue('D' . $row, $detail['pesanan']);
            $sheet->setCellValue('E' . $row, $detail['pesanan_dibatalkan']);
            $sheet->setCellValue('F' . $row, $detail['motor_terjual']);
            $sheet->setCellValue('G' . $row, $detail['pelanggan_baru']);
            
            $row++;
            $no++;
        }
        
        // Auto-size kolom
        foreach (range('A', 'G') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
        
        // Styling
        $styleArray = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                ],
            ],
        ];
        
        $sheet->getStyle('A12:G' . ($row - 1))->applyFromArray($styleArray);
        
        // Set nama file
        $filename = 'Laporan_Dashboard_' . $startDate->format('d-m-Y') . '_sd_' . $endDate->format('d-m-Y') . '.xlsx';
        
        // Simpan ke output
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        
        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit();
    }

    public function profile()
    {
        $adminData = Auth::guard('admin')->user();
        return view('admin.profile', compact('adminData'));
    }

    public function updateProfile(Request $request)
    {
        $id = Auth::guard('admin')->user()->id;
        $data = Admin::find($id);

        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:admins,email,'.$id,
        ]);

        $data->name = $request->name;
        $data->email = $request->email;

        if ($request->file('photo')) {
            $file = $request->file('photo');
            @unlink(public_path('upload/admin_images/'.$data->photo));
            $filename = date('YmdHi').$file->getClientOriginalName();
            $file->move(public_path('upload/admin_images'), $filename);
            $data->photo = $filename;
        }

        $data->save();

        return redirect()->back()->with('success', 'Profil Admin Berhasil Diperbarui');
    }

    public function changePassword()
    {
        return view('admin.change_password');
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'old_password' => 'required',
            'new_password' => 'required|confirmed|min:8',
        ]);

        if (!Hash::check($request->old_password, Auth::guard('admin')->user()->password)) {
            return back()->with('error', 'Password lama tidak cocok!');
        }

        Admin::whereId(Auth::guard('admin')->user()->id)->update([
            'password' => Hash::make($request->new_password)
        ]);

        return back()->with('success', 'Password berhasil diubah!');
    }
}
