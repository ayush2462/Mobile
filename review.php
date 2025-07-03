<?php
require 'vendor/autoload.php';
include 'db.php';

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Cell\DataType;

// --- INPUT VALIDATION ---
$number = $_POST['mobile_number'] ?? '';
$review = $_POST['review'] ?? '';
$reason = $_POST['reason'] ?? ''; // New field for reason in WhatsApp

if (!preg_match('/^[0-9]{10}$/', $number)) {
    exit("❌ Invalid mobile number format.");
}

if (!in_array($review, ['positive', 'negative'])) {
    exit("❌ Invalid review type.");
}

if (empty($reason)) {
    exit("❌ Reason is required.");
}

// --- VERIFY MOBILE EXISTS ---
$check = $pdo->prepare("SELECT 1 FROM mobile_status WHERE mobile_number = ?");
$check->execute([$number]);

if (!$check->fetch()) {
    exit("❌ This mobile number does not exist in the master record.");
}

// --- INSERT VERIFIED REVIEW ---
$insert = $pdo->prepare("INSERT INTO reviews (mobile_number, review, reason) VALUES (?, ?, ?)");
$insert->execute([$number, $review, $reason]);

// --- FETCH UPDATED REVIEW COUNTS ---
$counts = $pdo->prepare("SELECT review, COUNT(*) AS total FROM reviews WHERE mobile_number = ? GROUP BY review");
$counts->execute([$number]);

$positive = 0;
$negative = 0;
while ($row = $counts->fetch()) {
    if ($row['review'] === 'positive') {
        $positive = $row['total'];
    } elseif ($row['review'] === 'negative') {
        $negative = $row['total'];
    }
}

// --- LOAD EXCEL AND UPDATE ---
$excelFile = __DIR__ . '/excel/data.xlsx';

try {
    $spreadsheet = IOFactory::load($excelFile);
    $sheet = $spreadsheet->getActiveSheet();
    $data = $sheet->toArray(null, true, true, true);

    $found = false;
    foreach ($data as $i => $row) {
        if ($i === 1) continue; // Skip header
        if (isset($row['B']) && trim($row['B']) === $number) {
            $sheet->setCellValueExplicit("E$i", $positive, DataType::TYPE_STRING); // Column E = Positive
            $sheet->setCellValueExplicit("F$i", $negative, DataType::TYPE_STRING); // Column F = Negative
            $sheet->setCellValueExplicit("G$i", $reason, DataType::TYPE_STRING);   // Column G = Reason (new)
            $found = true;
            break;
        }
    }

    if (!$found) {
        $lastRow = $sheet->getHighestRow() + 1;
        // ['', $number, '', '', $positive, $negative, $reason]
        $sheet->fromArray(['', $number, '', '', $positive, $negative, $reason], null, "A$lastRow");
    }

    $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
    $writer->save($excelFile);
} catch (Exception $e) {
    exit("❌ Excel update failed: " . $e->getMessage());
}

// --- REDIRECT TO STATUS PAGE ---
header("Location: status.php?mobile_number=$number");
exit;
