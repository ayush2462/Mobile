<?php
include 'db.php';
require 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $mobile = trim($_POST['mobile_number'] ?? '');
    $status = $_POST['status'] ?? 'trusted';
    $description = trim($_POST['description'] ?? '');

    if (!preg_match('/^[0-9]{10}$/', $mobile)) {
        echo "<script>alert('Invalid mobile number format.'); window.history.back();</script>";
        exit;
    }

    try {
        $stmt = $pdo->prepare("INSERT INTO mobile_status (name, mobile_number, status, description) VALUES (:name, :mobile, :status, :description)");
        $stmt->execute([
            ':name' => $name,
            ':mobile' => $mobile,
            ':status' => $status,
            ':description' => $description
        ]);

        $filePath = __DIR__ . '/excel/data.xlsx';

        if (file_exists($filePath)) {
            $spreadsheet = IOFactory::load($filePath);
            $sheet = $spreadsheet->getSheet(0); // Use first sheet
        } else {
            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            $sheet->fromArray(['Name', 'Mobile Number', 'Status', 'Description', 'Positive Reviews', 'Negative Reviews'], null, 'A1');
        }

        $lastRow = $sheet->getHighestRow() + 1;
        $sheet->fromArray([$name, $mobile, $status, $description, 0, 0], null, "A$lastRow");

        $writer = new Xlsx($spreadsheet);
        $writer->save($filePath);

        echo "<script>alert('Data inserted and saved to Excel.'); window.location.href='add-data.php';</script>";
    } catch (PDOException $e) {
        echo "<script>alert('Mobile number already exists or error occurred.'); window.history.back();</script>";
    }
}
