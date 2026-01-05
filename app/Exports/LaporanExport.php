<?php

namespace App\Exports;

use App\Models\Peminjaman;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Font;

class LaporanExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    protected $startDate;
    protected $endDate;
    protected $status;

    public function __construct($startDate = null, $endDate = null, $status = null)
    {
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->status = $status;
    }

    public function collection()
    {
        $query = Peminjaman::with(['user', 'item']);

        if ($this->startDate) {
            $query->whereDate('tanggal_pinjam', '>=', $this->startDate);
        }

        if ($this->endDate) {
            $query->whereDate('tanggal_pinjam', '<=', $this->endDate);
        }

        if ($this->status) {
            $query->where('status', $this->status);
        }

        return $query->orderBy('created_at', 'desc')->get();
    }

    public function headings(): array
    {
        return [
            'No',
            'Peminjam',
            'Email',
            'Barang',
            'Kategori',
            'Quantity',
            'Tgl Pinjam',
            'Tgl Kembali',
            'Tgl Dikembalikan',
            'Status',
            'Denda (Rp)',
        ];
    }

    public function map($peminjaman): array
    {
        // Status translation
        $statusLabels = [
            'diajukan' => 'Menunggu',
            'disetujui' => 'Dipinjam',
            'ditolak' => 'Ditolak',
            'dikembalikan' => 'Dikembalikan',
        ];

        return [
            $peminjaman->id,
            $peminjaman->user->name ?? '-',
            $peminjaman->user->email ?? '-',
            $peminjaman->item->name ?? '-',
            $peminjaman->item->category->name ?? '-',
            $peminjaman->quantity,
            $peminjaman->tanggal_pinjam ? \Carbon\Carbon::parse($peminjaman->tanggal_pinjam)->format('d/m/Y') : '-',
            $peminjaman->tanggal_kembali ? \Carbon\Carbon::parse($peminjaman->tanggal_kembali)->format('d/m/Y') : '-',
            $peminjaman->tanggal_pengembalian_aktual ? \Carbon\Carbon::parse($peminjaman->tanggal_pengembalian_aktual)->format('d/m/Y') : '-',
            $statusLabels[$peminjaman->status] ?? ucfirst($peminjaman->status),
            $peminjaman->denda > 0 ? number_format($peminjaman->denda, 0, ',', '.') : '0',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        // Header style
        $sheet->getStyle('A1:K1')->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['argb' => 'FFFFFF'],
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['argb' => '4F46E5'],
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['argb' => 'CCCCCC'],
                ],
            ],
        ]);

        // Row style
        $sheet->getStyle('A2:K' . ($sheet->getHighestRow()))->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['argb' => 'DDDDDD'],
                ],
            ],
            'alignment' => [
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
            ],
        ]);

        // Alternating row colors
        $totalRows = $sheet->getHighestRow();
        for ($row = 2; $row <= $totalRows; $row++) {
            if ($row % 2 == 0) {
                $sheet->getStyle('A' . $row . ':K' . $row)->applyFromArray([
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['argb' => 'F9FAFB'],
                    ],
                ]);
            }
        }

        return [
            1 => ['font' => ['size' => 12]],
        ];
    }
}

