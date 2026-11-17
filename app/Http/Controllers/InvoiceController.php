<?php

namespace App\Http\Controllers;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\StreamedResponse;

class InvoiceController extends Controller
{
    /**
     * Tampilkan lembar invoice keuangan berdasarkan periode (seminggu, sebulan, setahun).
     */
    public function show(Request $request)
    {
        /** @var User $user */
        $user = Auth::user();

        $period = $request->query('period', 'month');

        [$startDate, $endDate, $periodLabel] = $this->getPeriodRange($period);

        $transactions = $user->transactions()
            ->with('category')
            ->whereBetween('transaction_date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
            ->orderBy('transaction_date', 'desc')
            ->orderBy('id', 'desc')
            ->get();

        $totalIncome = (float) $transactions->where('type', 'income')->sum('amount');
        $totalExpense = (float) $transactions->where('type', 'expense')->sum('amount');
        $netCashFlow = $totalIncome - $totalExpense;
        $transactionCount = $transactions->count();

        // Nomor seri invoice unik
        $invoiceNumber = sprintf(
            'INV/MP/%s/%s/%04d',
            strtoupper($period),
            now()->format('Ymd'),
            $user->id
        );

        return view('invoices.show', compact(
            'user',
            'period',
            'periodLabel',
            'startDate',
            'endDate',
            'transactions',
            'totalIncome',
            'totalExpense',
            'netCashFlow',
            'transactionCount',
            'invoiceNumber'
        ));
    }

    /**
     * Unduh laporan transaksi dalam format CSV (kompatibel Excel).
     */
    public function exportCsv(Request $request): StreamedResponse
    {
        /** @var User $user */
        $user = Auth::user();

        $period = $request->query('period', 'month');
        [$startDate, $endDate, $periodLabel] = $this->getPeriodRange($period);

        $transactions = $user->transactions()
            ->with('category')
            ->whereBetween('transaction_date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
            ->orderBy('transaction_date', 'asc')
            ->get();

        $filename = sprintf(
            'Invoice-Keuangan-MatPeng-%s-%s.csv',
            $period,
            now()->format('Ymd-His')
        );

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        return response()->stream(function () use ($transactions, $user, $periodLabel, $startDate, $endDate) {
            $handle = fopen('php://output', 'w');

            // UTF-8 BOM untuk kompatibilitas Microsoft Excel
            fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF));

            // Header Dokumen
            fputcsv($handle, ['LEMBAR LAPORAN / INVOICE KEUANGAN MATPENG']);
            fputcsv($handle, ['Nama Pengguna', $user->name]);
            fputcsv($handle, ['Email', $user->email]);
            fputcsv($handle, ['Periode', $periodLabel]);
            fputcsv($handle, ['Rentang Waktu', $startDate->format('d/m/Y').' s/d '.$endDate->format('d/m/Y')]);
            fputcsv($handle, ['Tanggal Cetak', now()->format('d/m/Y H:i:s')]);
            fputcsv($handle, []);

            // Header Tabel
            fputcsv($handle, ['No', 'Tanggal', 'Tipe', 'Kategori', 'Keterangan', 'Nominal (Rp)']);

            $no = 1;
            $totalIncome = 0;
            $totalExpense = 0;

            foreach ($transactions as $t) {
                $amount = (float) $t->amount;
                if ($t->type === 'income') {
                    $totalIncome += $amount;
                } else {
                    $totalExpense += $amount;
                }

                fputcsv($handle, [
                    $no++,
                    $t->transaction_date ? Carbon::parse($t->transaction_date)->format('d/m/Y') : '-',
                    $t->type === 'income' ? 'Pemasukan' : 'Pengeluaran',
                    $t->category ? $t->category->name : 'Tanpa Kategori',
                    $t->description ?: '-',
                    $amount,
                ]);
            }

            fputcsv($handle, []);
            fputcsv($handle, ['', '', '', '', 'Total Pemasukan', $totalIncome]);
            fputcsv($handle, ['', '', '', '', 'Total Pengeluaran', $totalExpense]);
            fputcsv($handle, ['', '', '', '', 'Arus Kas Bersih', $totalIncome - $totalExpense]);

            fclose($handle);
        }, 200, $headers);
    }

    /**
     * Tentukan rentang tanggal dan label berdasarkan opsi periode.
     */
    protected function getPeriodRange(string $period): array
    {
        return match ($period) {
            'week' => [
                now()->subDays(6)->startOfDay(),
                now()->endOfDay(),
                '1 Minggu Terakhir (7 Hari)',
            ],
            'year' => [
                now()->startOfYear(),
                now()->endOfYear(),
                '1 Tahun Penuh (Tahun '.now()->format('Y').')',
            ],
            default => [
                now()->startOfMonth(),
                now()->endOfMonth(),
                '1 Bulan ('.now()->locale('id')->isoFormat('MMMM Y').')',
            ],
        };
    }
}
