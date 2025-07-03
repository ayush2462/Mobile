

<?php
require 'vendor/autoload.php';
include 'db.php';

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

// Function to export all DB data to a single Excel file in 'excel' folder
function exportToExcel($pdo, $filename = 'excel/data.xlsx')
{
    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();

    // Set header
    $sheet->fromArray(['Name', 'Mobile Number', 'Status', 'Description'], NULL, 'A1');

    // Fetch data
    $stmt = $pdo->query("SELECT name, mobile_number, status, description FROM mobile_status");
    $rows = $stmt->fetchAll(PDO::FETCH_NUM);

    // Write data
    if ($rows) {
        $sheet->fromArray($rows, NULL, 'A2');
    }

    // Save file
    $writer = new Xlsx($spreadsheet);
    $writer->save($filename);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['excel_file'])) {
    // Save uploaded file in 'excel' folder
    if (!is_dir('excel')) {
        mkdir('excel', 0777, true);
    }
    $uploadedFileName = $_FILES['excel_file']['tmp_name'];

    try {
        $spreadsheet = IOFactory::load($uploadedFileName);
        $sheet = $spreadsheet->getActiveSheet();
        $rows = $sheet->toArray();

        $inserted = 0;
        foreach ($rows as $index => $row) {
            if ($index === 0) continue; // skip header row

            $name = trim($row[0] ?? '');
            $mobile = trim($row[1] ?? '');
            $status = trim(strtolower($row[2] ?? 'trusted'));
            $description = trim($row[3] ?? '');

            if (!preg_match('/^[0-9]{10}$/', $mobile)) continue;

            $check = $pdo->prepare("SELECT COUNT(*) FROM mobile_status WHERE mobile_number = ?");
            $check->execute([$mobile]);
            if ($check->fetchColumn() > 0) continue;

            $stmt = $pdo->prepare("INSERT INTO mobile_status (name, mobile_number, status, description) VALUES (?, ?, ?, ?)");
            $stmt->execute([$name, $mobile, $status, $description]);
            $inserted++;
        }

        // Update Excel after all inserts (in the same file always: excel/data.xlsx)
        exportToExcel($pdo, 'excel/data.xlsx');

        echo "$inserted rows inserted successfully.<br><a href='add-data.php'>Go Back</a>";
        echo "<br>Uploaded file saved as: $uploadedFileName";
        echo "<br><a href='excel/data.xlsx'>Download Updated Excel</a>";
    } catch (Exception $e) {
        echo "Error reading Excel file: " . $e->getMessage();
    }
} else {
    echo "No file uploaded.";
}
