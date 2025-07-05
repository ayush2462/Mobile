<?php
include 'db.php';

$mobile_number = '';
$positive = '';
$negative = '';
$entry_found = false;
$update_success = null;

// Handle GET
if (isset($_GET['mobile_number'])) {
    $mobile_number = $_GET['mobile_number'];

    // Fetch review counts
    $stmt = $pdo->prepare("SELECT review, COUNT(*) AS total FROM reviews WHERE mobile_number = ? GROUP BY review");
    $stmt->execute([$mobile_number]);

    while ($row = $stmt->fetch()) {
        if ($row['review'] === 'positive') $positive = $row['total'];
        if ($row['review'] === 'negative') $negative = $row['total'];
    }

    $entry_found = true;

    if (isset($_GET['updated'])) {
        $update_success = "✅ Review summary updated successfully.";
    }
}

// Handle Search
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['search'])) {
    $mobile_number = $_POST['mobile_number'];
    header("Location: edit.php?mobile_number=" . urlencode($mobile_number));
    exit();
}

// Handle Update
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['update'])) {
    $mobile_number = $_POST['mobile_number'];
    $positive = (int) $_POST['positive_count'];
    $negative = (int) $_POST['negative_count'];

    $pdo->prepare("DELETE FROM reviews WHERE mobile_number = ?")->execute([$mobile_number]);

    $insertStmt = $pdo->prepare("INSERT INTO reviews (mobile_number, review) VALUES (?, ?)");
    for ($i = 0; $i < $positive; $i++) {
        $insertStmt->execute([$mobile_number, 'positive']);
    }
    for ($i = 0; $i < $negative; $i++) {
        $insertStmt->execute([$mobile_number, 'negative']);
    }

    header("Location: edit.php?mobile_number=" . urlencode($mobile_number) . "&updated=1");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Admin Panel - Review Management</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet" />
  <style>
    body {
      background: linear-gradient(135deg, #1e3a8a, #2563eb);
      font-family: 'Segoe UI', sans-serif;
      margin: 0;
      padding: 2rem;
      display: flex;
      align-items: center;
      justify-content: center;
      min-height: 100vh;
      color: white;
    }

    .admin-container {
      background: #1e293b;
      padding: 2rem;
      border-radius: 12px;
      max-width: 500px;
      width: 100%;
      box-shadow: 0 8px 24px rgba(0,0,0,0.3);
      position: relative;
    }

    .admin-container .close-btn {
      position: absolute;
      top: 16px;
      right: 20px;
      background: #334155;
      color: #fff;
      border: none;
      border-radius: 50%;
      width: 32px;
      height: 32px;
      font-size: 18px;
      cursor: pointer;
      display: flex;
      align-items: center;
      justify-content: center;
      transition: background 0.3s ease, transform 0.3s ease;
    }

    .admin-container .close-btn:hover {
      background: #ef4444;
      transform: rotate(90deg);
    }

    h2 {
      text-align: center;
      margin-bottom: 1.5rem;
      color: #60a5fa;
    }

    label {
      display: block;
      margin-bottom: 0.5rem;
      font-weight: bold;
    }

    input[type="text"],
    input[type="number"] {
      width: 100%;
      padding: 0.8rem;
      margin-bottom: 1.2rem;
      border: none;
      border-radius: 8px;
      background: #0f172a;
      color: white;
      font-size: 1rem;
    }

    button {
      background: linear-gradient(to right, #38bdf8, #2563eb);
      border: none;
      color: white;
      padding: 0.9rem 1.4rem;
      font-size: 1rem;
      font-weight: bold;
      border-radius: 8px;
      width: 100%;
      cursor: pointer;
      transition: 0.3s ease;
      margin-bottom: 1rem;
    }

    button:hover {
      background: linear-gradient(to right, #2563eb, #38bdf8);
      transform: scale(1.03);
    }

    .success {
      background-color: #16a34a;
      color: white;
      padding: 0.75rem;
      border-radius: 8px;
      text-align: center;
      margin-bottom: 1rem;
      animation: fadeOut 3s ease forwards;
    }

    @keyframes fadeOut {
      0% { opacity: 1; }
      80% { opacity: 1; }
      100% { opacity: 0; display: none; }
    }
  </style>
</head>
<body>
  <div class="admin-container">
    <a href="add-data.php" class="close-btn" title="Back to Search">×</a>

    <h2><i class="bx bxs-lock-alt"></i> Admin Review Editor</h2>

    <?php if ($update_success): ?>
      <div class="success" id="success-msg"><?= $update_success ?></div>
    <?php endif; ?>

    <!-- Search Form -->
    <form method="POST">
        
      <label for="mobile_number">Enter Mediator Number:</label>
      <input type="text" name="mobile_number" pattern="[0-9]{10}" maxlength="10" value="<?= htmlspecialchars($mobile_number) ?>" required>
      <button type="submit" name="search">🔍 Search</button>
    </form>

    <?php if ($entry_found): ?>
      <!-- Update Form -->
      <form method="POST">
        <a href="edit.php" class="close-btn" title="Back to Search">×</a>
        <input type="hidden" name="mobile_number" value="<?= htmlspecialchars($mobile_number) ?>">

        <label for="positive_count">👍 Positive Reviews:</label>
        <input type="number" name="positive_count" min="0" value="<?= $positive ?>">

        <label for="negative_count">👎 Negative Reviews:</label>
        <input type="number" name="negative_count" min="0" value="<?= $negative ?>">

        <button type="submit" name="update">💾 Update Review Summary</button>
      </form>
    <?php endif; ?>
  </div>

  <script>
    // Optional: remove success message after 4 seconds completely
    setTimeout(() => {
      const msg = document.getElementById("success-msg");
      if (msg) msg.style.display = "none";
    }, 4000);
  </script>
</body>
</html>
