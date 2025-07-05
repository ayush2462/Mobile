<?php
include 'db.php';

$number = $_GET['mobile_number'] ?? '';
$positiveCount = 0;
$negativeCount = 0;
$entry = null;

if (preg_match('/^[0-9]{10}$/', $number)) {
  $stmt = $pdo->prepare("SELECT name, mobile_number, status, description FROM mobile_status WHERE mobile_number = ?");
  $stmt->execute([$number]);
  $entry = $stmt->fetch();

  $reviewStmt = $pdo->prepare("SELECT review, COUNT(*) AS total FROM reviews WHERE mobile_number = ? GROUP BY review");
  $reviewStmt->execute([$number]);

  while ($row = $reviewStmt->fetch()) {
    if ($row['review'] === 'positive') $positiveCount = $row['total'];
    if ($row['review'] === 'negative') $negativeCount = $row['total'];
  }

  $error_message = null;
} else {
  $error_message = "Invalid Mobile Number Format.";
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Status Result & Review</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
  <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    body {
      font-family: 'Segoe UI', sans-serif;
      background: #f1f5f9;
      padding: 2rem 1rem;
      display: flex;
      flex-direction: column;
      align-items: center;
      color: #1e293b;
      min-height: 100vh;
      overflow: hidden;
      /* Disable scroll for desktop */
    }

    h1 {
      font-size: 2.2rem;
      margin-bottom: 1.5rem;
      color: #2563eb;
      text-align: center;
    }

    .card {
      background: #ffffff;
      border-radius: 1rem;
      padding: 2rem;
      box-shadow: 0 12px 24px rgba(0, 0, 0, 0.08);
      width: 100%;
      max-width: 500px;
      margin-bottom: 1.5rem;
      position: relative;
    }

    .close-btn {
      position: absolute;
      top: 12px;
      right: 16px;
      background: transparent;
      border: none;
      font-size: 1.5rem;
      color: #334155;
      cursor: pointer;
      transition: color 0.2s ease;
    }

    .close-btn:hover {
      color: #ef4444;
    }

    .card div {
      margin-bottom: 1rem;
      font-size: 1.3rem;
    }

    .status-badge {
      display: inline-block;
      padding: 0.5rem 1.1rem;
      border-radius: 0.6rem;
      font-weight: 600;
      font-size: 1.1rem;
      animation: blink 1.2s infinite alternate;
    }

    .status-trusted {
      background: #dcfce7;
      color: #15803d;
    }

    .status-fraud {
      background: #fee2e2;
      color: #b91c1c;
    }

    .status-blacklisted {
      background: #fef9c3;
      color: #92400e;
    }

    .status-unknown {
      background: #f3f4f6;
      color: #6b7280;
    }

    @keyframes blink {
      0% {
        opacity: 1;
      }

      100% {
        opacity: 0.6;
      }
    }

    .review-summary {
      display: flex;
      justify-content: space-between;
      gap: 1rem;
      margin-top: 1rem;
    }

    .review-box {
      flex: 1;
      background: #f8fafc;
      padding: 1rem;
      border-radius: 0.75rem;
      text-align: center;
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
    }

    .review-box h4 {
      font-size: 1.2rem;
      margin-bottom: 0.3rem;
    }

    .review-box strong {
      font-size: 1.6rem;
      color: #0f172a;
    }

    .positive {
      background: #ecfdf5;
      color: #047857;
    }

    .negative {
      background: #fef2f2;
      color: #b91c1c;
    }

    .review-actions {
      text-align: center;
    }

    .review-actions h3 {
      margin-bottom: 1rem;
    }

    .yes-btn,
    .no-btn {
      margin: 0.6rem;
      padding: 0.9rem 2rem;
      border: none;
      border-radius: 8px;
      font-weight: bold;
      cursor: pointer;
      color: #fff;
      font-size: 1.1rem;
      transition: background 0.3s ease;
    }

    .yes-btn {
      background: #2563eb;
    }

    .no-btn {
      background: #dc2626;
    }

    .yes-btn:hover {
      background: #1e40af;
    }

    .no-btn:hover {
      background: #b91c1c;
    }

    .modal-overlay {
      position: fixed;
      inset: 0;
      background: rgba(0, 0, 0, 0.4);
      backdrop-filter: blur(3px);
      display: none;
      align-items: center;
      justify-content: center;
      z-index: 999;
    }

    .modal {
      background: white;
      padding: 2rem;
      border-radius: 1rem;
      width: 90%;
      max-width: 420px;
      text-align: center;
      position: relative;
      box-shadow: 0 12px 30px rgba(0, 0, 0, 0.15);
    }

    .modal-close {
      position: absolute;
      top: 10px;
      right: 14px;
      background: transparent;
      border: none;
      font-size: 1.5rem;
      color: #333;
      cursor: pointer;
    }

    #reviewForm {
      margin-top: 1rem;
    }

    #reviewForm label {
      display: inline-flex;
      align-items: center;
      gap: 0.4rem;
      margin: 0 1rem;
      font-size: 1.1rem;
    }

    #reviewForm button {
      margin-top: 1.5rem;
      padding: 0.9rem 1.6rem;
      background: #25d366;
      color: white;
      border: none;
      border-radius: 10px;
      font-weight: bold;
      cursor: pointer;
      font-size: 1.1rem;
    }

    #reviewForm button:hover {
      background: #128c7e;
    }

    @media (max-width: 767px) {
      body {
        overflow-y: auto;
        /* Enable scroll only on mobile */
      }

      h1 {
        font-size: 1.8rem;
      }

      .card {
        padding: 1.5rem;
      }

      .review-summary {
        flex-direction: column;
        gap: 1rem;
      }

      .yes-btn,
      .no-btn {
        width: 100%;
      }

      .review-box h4 {
        font-size: 1.2rem;
      }

      .review-box strong {
        font-size: 1.5rem;
      }

      #reviewForm label {
        display: block;
        margin: 0.5rem 0;
      }
    }
  </style>

</head>

<body>

  <?php if ($entry): ?>
    <h1>Status Found</h1>

    <!-- Status Card -->
    <div class="card">
      <button class="close-btn" onclick="window.location.href='index.php'" title="Back to Home">×</button>
      <div><strong>Name:</strong> <?= htmlspecialchars($entry['name']) ?></div>
      <div><strong>Mobile Number:</strong> <?= htmlspecialchars($entry['mobile_number']) ?></div>
      <div><strong>Status:</strong>
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
        <span class="status-badge <?= $statusClass ?>"><?= $statusText ?></span>
      </div>
      <div><strong>Description:</strong> <?= nl2br(htmlspecialchars($entry['description'])) ?></div>
    </div>

    <!-- Review Summary -->
    <div class="card">
      <h3 style="text-align: center;">Review Summary</h3>
      <div class="review-summary">
        <div class="review-box positive">
          <h4>👍 Positive</h4>
          <strong><?= $positiveCount ?></strong>
        </div>
        <div class="review-box negative">
          <h4>👎 Negative</h4>
          <strong><?= $negativeCount ?></strong>
        </div>
      </div>
    </div>

    <!-- Review Actions -->
    <div class="review-actions">
      <h3>Do you want to leave a review?</h3>
      <button class="yes-btn" onclick="openModal()">Yes</button>
      <button class="no-btn" onclick="window.location.href='index.php'">No</button>
    </div>

    <!-- Modal -->
    <div class="modal-overlay" id="reviewModal">
      <div class="modal">
        <button class="yes-btn modal-close" onclick="closeModal()">Go Back</button>
        <h3>Submit Your Review</h3>
        <form id="reviewForm">
          <div style="display: flex; gap: 1rem; align-items: center; justify-content:center">
            <label style="display: flex; align-items: center; gap: 0.3rem;">
              <input type="radio" name="review" value="positive" required>Positive
            </label>
            <label style="display: flex; align-items: center; gap: 0.3rem;">
              <input type="radio" name="review" value="negative">Negative
            </label>
          </div>
          <button type="submit"><i class="fab fa-whatsapp"></i> SUBMIT</button>
          <p style="margin-top: 1rem;">Note: We will redirect you to WhatsApp for verification.</p>
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
        const phone = "919999851090";
        const message = `Review for mobile: <?= $number ?>\n` +
          (reviewType === 'positive' ? '✅ I want to mark this number as POSITIVE.' : '⛔ I want to mark this number as NEGATIVE.') +
          "\n\nPlease verify my review.";

        window.open(`https://wa.me/${phone}?text=${encodeURIComponent(message)}`, '_blank');
        closeModal();
      });
    </script>

  <?php else: ?>
    <h1>No Status Found</h1>

    <div class="card">
      <button class="close-btn" onclick="window.location.href='index.php'" title="Back to Home">×</button>
      <div><strong>Name:</strong> N/A</div>
      <div><strong>Mobile Number:</strong> <?= htmlspecialchars($number) ?></div>
      <div><strong>Status:</strong> <span class="status-badge status-unknown">❓ Unknown</span></div>
    </div>

    <!-- Review Summary -->
    <div class="card">
      <h3 style="text-align: center;">Review Summary</h3>
      <div class="review-summary">
        <div class="review-box positive">
          <h4>👍 Positive</h4>
          <strong><?= $positiveCount ?></strong>
        </div>
        <div class="review-box negative">
          <h4>👎 Negative</h4>
          <strong><?= $negativeCount ?></strong>
        </div>
      </div>
    </div>

    <!-- Review CTA -->
    <div class="review-actions">
      <h3>Do you want to leave a review?</h3>
      <button class="yes-btn" onclick="openModal()">Yes</button>
      <button class="no-btn" onclick="window.location.href='index.php'">No</button>
    </div>

    <!-- Modal -->
    <div class="modal-overlay" id="reviewModal">
      <div class="modal">
        <button class=" close-btn modal-close" onclick="closeModal()">x</button>
        <h3>Submit Your Review</h3>
        <form id="reviewForm">
          <div style="display: flex; gap: 1rem; align-items: center; justify-content:center">
            <label style="display: flex; align-items: center; gap: 0.3rem;">
              <input type="radio" name="review" value="positive" required>Positive
            </label>
            <label style="display: flex; align-items: center; gap: 0.3rem;">
              <input type="radio" name="review" value="negative">Negative
            </label>
          </div>
          <button type="submit"><i class="fab fa-whatsapp"></i> SUBMIT</button>
          <p style="margin-top: 1rem;">Note: We will redirect you to WhatsApp for verification.</p>
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
        const phone = "919999851090";
        const message = `Review for mobile: <?= $number ?>\n` +
          (reviewType === 'positive' ? '✅ I want to mark this number as POSITIVE.' : '⛔ I want to mark this number as NEGATIVE.') +
          "\n\nPlease verify my review.";

        window.open(`https://wa.me/${phone}?text=${encodeURIComponent(message)}`, '_blank');
        closeModal();
      });
    </script>
  <?php endif; ?>

</body>

</html>