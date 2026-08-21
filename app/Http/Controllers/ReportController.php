<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use TCPDF;
use App\Models\Business;
use Carbon\Carbon;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        return Inertia::render('Admin/Report/Index');
    }


    public function generate(Request $request)
    {
        $dateRange = $request->input('date');
        $status = $request->input('status');
        $businessType = $request->input('business_type');

        // Load full data
        $query = Business::with([
            'user',
            'inspection.user',
            'submissions.requirement',
            'permit'
        ]);

        if (!empty($status)) {
            $query->where('remarks', $status);
        }

        if (!empty($businessType)) {
            $query->where('building_type', $businessType);
        }

        if (!empty($dateRange)) {
            $dates = explode(' to ', $dateRange);

            if (count($dates) === 2) {
                $startDate = Carbon::parse($dates[0])->startOfDay();
                $endDate = Carbon::parse($dates[1])->endOfDay();
            } else {
                $startDate = Carbon::parse($dates[0])->startOfDay();
                $endDate = Carbon::parse($dates[0])->endOfDay();
            }

            $query->whereBetween('created_at', [$startDate, $endDate]);
        }

        $businesses = $query->get();

        // --------------------------
        //     PDF CONFIG
        // --------------------------
        $pdf = new TCPDF('L', PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        $pdf->SetCreator('NMSC Venues');
        $pdf->SetAuthor('Admin');
        $pdf->SetTitle('Building Permit Report');
        $pdf->SetMargins(8, 8, 8);
        $pdf->AddPage();

        // ---------------------------------------
        //              HEADER
        // ---------------------------------------

        // LEFT LOGO (obo)
        $pdf->Image(public_path('imgs/obo.jpeg'), 20, 10, 30, 30, '', '', '', false);

        // RIGHT LOGO (tangubicon)
        $pdf->Image(public_path('imgs/tangubicon.png'), 250, 10, 30, 30, '', '', '', false);

        // CENTER TEXT
        $pdf->SetFont('helvetica', 'B', 14);
        $pdf->SetXY(0, 12);
        $pdf->Cell(0, 6, 'REPUBLIC OF THE PHILIPPINES', 0, 1, 'C');

        $pdf->Cell(0, 6, 'TANGUB CITY LGU', 0, 1, 'C');

        $pdf->SetFont('helvetica', '', 11);
        $pdf->Cell(0, 6, 'Barangay 1, Tangub City Misamis Occidental 7214 Philippines', 0, 1, 'C');

        $pdf->SetFont('helvetica', 'I', 11);
        $pdf->Cell(0, 6, 'Office of OBO', 0, 1, 'C');

        $pdf->Ln(3);

        // Divider line
        $pdf->Line(10, $pdf->GetY(), 287, $pdf->GetY());
        $pdf->Ln(5);

        // ---------------------------------------
        //              TITLE
        // ---------------------------------------
        $pdf->SetFont('helvetica', 'B', 18);
        $pdf->Cell(0, 12, 'Building Permit Report', 0, 1, 'C');
        $pdf->Ln(2);

        $pdf->SetFont('helvetica', '', 11);

        $formattedRange = $dateRange
            ? (count($dates) === 2
                ? Carbon::parse($dates[0])->format('F j, Y') . ' to ' . Carbon::parse($dates[1])->format('F j, Y')
                : Carbon::parse($dates[0])->format('F j, Y'))
            : 'All Dates';

        $pdf->MultiCell(0, 8, "Date Range: $formattedRange", 0, 'L');
        $pdf->MultiCell(0, 8, "Status: " . ($status ?: 'All Status'), 0, 'L');
        $pdf->MultiCell(0, 8, "Building Type: " . ($businessType ?: 'All Types'), 0, 'L');
        $pdf->Ln(3);

        // ------------------------------
        //       TABLE HEADER
        // ------------------------------

        $pdf->SetFont('helvetica', '', 9);

        $widths = [
            35,
            35,
            40,
            35,
            35,
            35,
            35,
            25
        ];

        $headers = [
            "Building Name",
            "Owner",
            "Type of Building",
            "Type of Permit",
            "Inspector",
            "Application Date",
            "Permit Release",
            "Status"
        ];

        foreach ($headers as $i => $header) {
            $pdf->Cell($widths[$i], 10, $header, 1, 0, 'C');
        }
        $pdf->Ln();

        // ------------------------------
        //         TABLE ROWS
        // ------------------------------

        $pdf->SetFont('helvetica', '', 8);

        if ($businesses->isEmpty()) {
            $pdf->Cell(array_sum($widths), 10, 'No data available', 1, 1, 'C');
            ob_end_clean();
            $pdf->Output('building_permit_report.pdf', 'I');
            exit;
        }

        foreach ($businesses as $biz) {

            $owner = $biz->user
                ? $biz->user->first_name . ' ' . $biz->user->last_name
                : '----';

            $requirement = $biz->submissions->first()?->requirement?->requirement_type ?? '----';

            $inspector = $biz->inspection?->user
                ? $biz->inspection->user->first_name . ' ' . $biz->inspection->user->last_name
                : '----';

            $applicationDate = $biz->created_at
                ? $biz->created_at->format('F j, Y')
                : '----';

            $permitRelease = $biz->permit?->release_date
                ? Carbon::parse($biz->permit->release_date)->format('F j, Y')
                : '----';

            $values = [
                $biz->business_name,
                $owner,
                $biz->type_of_business,
                $requirement,
                $inspector,
                $applicationDate,
                $permitRelease,
                ucfirst($biz->status),
            ];

            foreach ($values as $i => $val) {
                $pdf->Cell($widths[$i], 8, $val, 1, 0);
            }

            $pdf->Ln();
        }

        ob_end_clean();
        $pdf->Output('building_permit_report.pdf', 'I');
        exit;
    }
}
