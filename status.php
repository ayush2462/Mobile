<?php
include 'db.php';

$number = $_GET['mobile_number'] ?? '';

if (!preg_match('/^[0-9]{10}$/', $number)) {
    $error_message = "Invalid Mobile Number Format.";
    $entry = null;
} else {
    $stmt = $pdo->prepare("SELECT name, mobile_number, status, description FROM mobile_status WHERE mobile_number = ?");
    $stmt->execute([$number]);
    $entry = $stmt->fetch();

    $reviewStmt = $pdo->prepare("SELECT review, COUNT(*) AS total FROM reviews WHERE mobile_number = ? GROUP BY review");
    $reviewStmt->execute([$number]);

    $positiveCount = 0;
    $negativeCount = 0;

    while ($row = $reviewStmt->fetch()) {
        if ($row['review'] === 'positive') {
            $positiveCount = $row['total'];
        } elseif ($row['review'] === 'negative') {
            $negativeCount = $row['total'];
        }
    }

    $error_message = null;
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Status Result & Review</title>
    <link rel="stylesheet" href="style/status.css">

</head>

<body>

    <h1>Status Found</h1>
    <?php if ($entry): ?>
        <!-- Card 1 -->
        <div class="card">
            <a href="index.php" class="close-btn" title="Back to Home">&times;</a>
            <div><strong>Name: &nbsp;</strong><?= htmlspecialchars($entry['name']) ?></div>
            <div><strong>Mobile Number: &nbsp;</strong><?= htmlspecialchars($entry['mobile_number']) ?></div>
            <div><strong>Status: &nbsp;</strong>
                <?php
                $status = strtolower(trim($entry['status']));
                $statusClass = match ($status) {
                    'trusted' => 'status-trusted',
                    'fraud' => 'status-fraud',
                    'blacklisted' => 'status-blacklisted',
                    default => 'status-unknown',
                };
                $statusText = match ($status) {
                    'trusted' => '✅ Trusted',
                    'fraud' => '⛔ Fraud',
                    'blacklisted' => '🚫 Blacklisted',
                    default => '❓ Unknown',
                };
                ?>
                <span class="<?= $statusClass ?> status-blink"><?= $statusText ?></span>
            </div>
            <div><strong>Description: &nbsp;</strong><?= nl2br(htmlspecialchars($entry['description'])) ?></div>
        </div>

        <!-- Card 2 -->
        <div class="card">
            <h3>Review Summary</h3>
            <div class="review-row">
                <p>👍 Positive: <strong><?= $positiveCount ?></strong></p>
                <p>👎 Negative: <strong><?= $negativeCount ?></strong></p>
            </div>

        </div>

        <!-- Leave Review Prompt -->
        <div class="review-actions">
            <h3>Do you want to leave a review?</h3>
            <button class="yes-btn" onclick="openModal()">Yes</button>
            <button class="no-btn" onclick="window.location.href='index.php'">No</button>
        </div>

        <!-- Review Modal -->
        <div class="modal-overlay" id="reviewModal">
            <div class="modal">
                <span class="modal-close" onclick="closeModal()">&times;</span>
                <h3>Submit Your Review</h3>
                <form id="reviewForm">
                    <label><input type="radio" name="review" value="positive" required> Positive</label><br>
                    <label><input type="radio" name="review" value="negative"> Negative</label><br><br>
                    <button type="submit" class="whatsapp-link">Submit via WhatsApp</button>
                </form>
            </div>
        </div>

        <script>
            function openModal() {
                document.getElementById('reviewModal').style.display = 'flex';
            }

            function closeModal() {
                document.getElementById('reviewModal').style.display = 'none';
            }

            document.getElementById('reviewForm').addEventListener('submit', function(e) {
                e.preventDefault();
                const selected = document.querySelector('input[name="review"]:checked');
                if (!selected) return;

                const reviewType = selected.value;
                const phone = "918882292019";
                const message = `Review for mobile: <?= $number ?>\n${reviewType === 'positive' ? '✅ I want to mark this number as POSITIVE.' : '⛔ I want to mark this number as NEGATIVE.'
                }\n\nPlease verify my review.`;

                const whatsappUrl = `https://wa.me/${phone}?text=${encodeURIComponent(message)}`;
                window.open(whatsappUrl, '_blank');
                closeModal();
            });
        </script>

    <?php else: ?>
        <div class="card">
            <p><?= htmlspecialchars($error_message ?? "No data found for mobile number: $number") ?></p>
            <a href="index.php" class="whatsapp-link">Go Back</a>
        </div>
    <?php endif; ?>
</body>

</html>