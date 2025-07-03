<?php
require 'vendor/autoload.php';
include 'db.php';

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Cell\DataType;

// --- Input from backend team form ---
$number = $_POST['mobile_number'] ?? '';
$review = $_POST['review'] ?? ''; // 'positive' or 'negative'

// --- Validate input ---
if (!preg_match('/^[0-9]{10}$/', $number)) {
    exit("❌ Invalid mobile number format.");
}

if (!in_array($review, ['positive', 'negative'])) {
    exit("❌ Review must be 'positive' or 'negative'.");
}

// --- Load the Excel File ---
$excelFile = __DIR__ . 'excel/data.xlsx';

try {
    $spreadsheet = IOFactory::load($excelFile);
    $sheet = $spreadsheet->getActiveSheet();
    $data = $sheet->toArray(null, true, true, true);

    $found = false;
    foreach ($data as $i => $row) {
        if ($i === 1) continue; // Skip header
        if (isset($row['B']) && trim($row['B']) === $number) {
            // Get current values
            $currentPositive = (int) ($row['E'] ?? 0);
            $currentNegative = (int) ($row['F'] ?? 0);

            // Update counts
            if ($review === 'positive') {
                $sheet->setCellValueExplicit("E$i", $currentPositive + 1, DataType::TYPE_STRING);
            } else {
                $sheet->setCellValueExplicit("F$i", $currentNegative + 1, DataType::TYPE_STRING);
            }

            $found = true;
            break;
        }
    }

    if (!$found) {
        // New number entry
        $lastRow = $sheet->getHighestRow() + 1;
        $positive = $review === 'positive' ? 1 : 0;
        $negative = $review === 'negative' ? 1 : 0;
        $sheet->fromArray(['', $number, '', '', $positive, $negative], null, "A$lastRow");
    }

    $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
    $writer->save($excelFile);
    echo "✅ Review successfully recorded for $number.";
} catch (Exception $e) {
    exit("❌ Failed to update Excel: " . $e->getMessage());
}
