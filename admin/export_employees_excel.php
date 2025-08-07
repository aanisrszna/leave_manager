<?php
require '../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;

include('../includes/config.php');

$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

// Updated headers
$headers = [
    'Staff ID', 'First Name', 'Position', 'IC Number', 'Email', 'Phone Number', 'Date of Birth', 'Gender', 'Address',
    'Date Joined', 'Car Plate', 'Reporting To',
    'Emergency Name', 'Emergency Relation', 'Emergency Contact'
];

// Set headers
$sheet->fromArray($headers, NULL, 'A1');

// Header style
$headerStyle = $sheet->getStyle('A1:N1');
$headerStyle->getFont()->setBold(true)->getColor()->setARGB(Color::COLOR_WHITE);
$headerStyle->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('0000FF');
$headerStyle->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER)->setVertical(Alignment::VERTICAL_CENTER);
$sheet->getRowDimension(1)->setRowHeight(25);

// Fetch employee records excluding Admins
$sql = "SELECT Staff_ID, FirstName, Position_Staff, IC_Number, EmailId, Phonenumber, Dob, Gender, Address, date_joined, Car_Plate, Reporting_To, Emergency_Name, Emergency_Relation, Emergency_Contact FROM tblemployees WHERE role != 'Admin' AND Status != 'Inactive'";
$query = $dbh->prepare($sql);
$query->execute();
$results = $query->fetchAll(PDO::FETCH_ASSOC);

// Populate data rows
$rowNum = 2;
foreach ($results as $row) {
    $sheet->fromArray(array_values($row), NULL, 'A' . $rowNum);
    $sheet->getRowDimension($rowNum)->setRowHeight(25);
    $rowNum++;
}

// Apply wrapping, autosize, and borders
$lastColumn = count($headers);
$lastRow = $rowNum - 1;
for ($col = 1; $col <= $lastColumn; $col++) {
    $columnLetter = Coordinate::stringFromColumnIndex($col);
    $sheet->getStyle($columnLetter . '1:' . $columnLetter . $lastRow)
        ->getAlignment()->setWrapText(true);
    $sheet->getColumnDimension($columnLetter)->setAutoSize(true);
}

// Apply border to all cells with data
$sheet->getStyle("A1:" . Coordinate::stringFromColumnIndex($lastColumn) . $lastRow)
    ->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

// Output file
$filename = 'Employee_Details_' . date('Y-m-d') . '.xlsx';
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header("Content-Disposition: attachment;filename=\"$filename\"");
header('Cache-Control: max-age=0');

$writer = new Xlsx($spreadsheet);
$writer->save('php://output');
exit;
