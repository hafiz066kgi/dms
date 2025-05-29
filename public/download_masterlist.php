<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/roles.php';
require_once __DIR__ . '/../includes/functions.php';

// Restrict download access to admin and superadmin only
if (!is_admin() && !is_superadmin()) {
    $_SESSION['error'] = "Unauthorized access.";
    header("Location: index.php");
    exit;
}

// Load PhpSpreadsheet library (ensure you have installed via Composer)
require_once __DIR__ . '/../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

// Retrieve filters
$category = $_GET['category'] ?? null;
$search = $_GET['search'] ?? null;

// Fetch documents based on filter
$documents = get_documents($category);

// Apply search filtering if applicable
if (!empty($search)) {
    $search = strtolower($search);
    $documents = array_filter($documents, function($doc) use ($search) {
        return strpos(strtolower($doc['title']), $search) !== false
            || strpos(strtolower($doc['document_no']), $search) !== false
            || strpos(strtolower($doc['department']), $search) !== false;
    });
}

// Create new spreadsheet and get the active sheet
$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

// Set main title
$sheet->setCellValue('A1', 'Document Masterlist');
$sheet->mergeCells('A1:J1');
$sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
$sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

// Define table header row (A2:J2)
$header = [
    'No.',
    'Doc. No',
    'Rev.',
    'Document Name',
    'Retention (Years)',
    'Effective Date',
    'Date to Review',
    'Department',
    'Prepared by',
    'Category'
];
$sheet->fromArray($header, null, 'A2');

// Style header row
$headerCellRange = 'A2:J2';
$sheet->getStyle($headerCellRange)->getFill()
    ->setFillType(Fill::FILL_SOLID)
    ->getStartColor()->setARGB('FFDEEAF6'); // Light blue header
$sheet->getStyle($headerCellRange)->getFont()->setBold(true);

// Populate data starting from row 3
$count = 1;
$row = 3;
foreach ($documents as $doc) {
    $establish_date = $doc['establish_date'] ?? null;
    $retention = (int)($doc['retention'] ?? 0);
    $date_to_review = '-';
    if ($establish_date && $retention > 0) {
        try {
            $review_date = new DateTime($establish_date);
            $review_date->modify("+$retention years");
            $date_to_review = $review_date->format('Y-m-d');
        } catch (Exception $e) {
            $date_to_review = '-';
        }
    }

    // Prepare date in d-m-Y format for Effective Date
    $formatted_establish_date = $establish_date ? date('d-m-Y', strtotime($establish_date)) : '-';

    $sheet->fromArray([
        $count++,
        $doc['document_no'],
        $doc['revision'],
        $doc['title'],
        $doc['retention'],
        $formatted_establish_date,
        $date_to_review,
        $doc['department'],
        $doc['pic'],
        ucfirst($doc['category'])
    ], null, "A$row");
    $row++;
}

// Autosize all columns
foreach (range('A', 'J') as $col) {
    $sheet->getColumnDimension($col)->setAutoSize(true);
}

// Hide columns G, H, I (7, 8, 9: 'Date to Review', 'Department', 'Prepared by')
$sheet->getColumnDimension('G')->setVisible(false);
$sheet->getColumnDimension('H')->setVisible(false);
$sheet->getColumnDimension('I')->setVisible(false);

// Output to browser as XLSX
$filename = 'Document_Masterlist_' . date('Ymd_His') . '.xlsx';
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header("Content-Disposition: attachment; filename=\"$filename\"");
header('Cache-Control: max-age=0');

$writer = new Xlsx($spreadsheet);
$writer->save('php://output');
exit;
