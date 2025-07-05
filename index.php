<?php include 'db.php'; ?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Mobile Status Checker</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" />
  <style>
    :root {
      --primary: #2563eb;
      --accent: #3b82f6;
      --bg: #f8fafc;
      --text: #1e293b;
      --radius: 12px;
      --shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
      --font: "Segoe UI", sans-serif;
    }

    * {
      box-sizing: border-box;
      margin: 0;
      padding: 0;
    }

    body {
      font-family: var(--font);
      background: var(--bg);
      color: var(--text);
      display: flex;
      align-items: center;
      justify-content: center;
      min-height: 100vh;
      padding: 1.5rem;
    }

    .status-form-container {
      background: #fff;
      padding: 2rem 2.5rem;
      border-radius: var(--radius);
      box-shadow: var(--shadow);
      width: 100%;
      max-width: 480px;
      animation: fadeIn 0.5s ease;
    }

    h2 {
      text-align: center;
      margin-bottom: 1rem;
      color: var(--primary);
      font-size: 1.8rem;
    }

    h3 {
      text-align: center;
      color: var(--primary);
      font-size: 1.2rem;
      margin-bottom: 2rem;
    }

    h2 i {
      margin-right: 0.6rem;
    }

    form {
      display: flex;
      flex-direction: column;
      gap: 1.2rem;
    }

    .form-group {
      display: flex;
      align-items: center;
      gap: 0.5rem;
      font-size: 1rem;
      font-weight: 600;
      color: var(--text);
    }

    input[type="text"] {
      width: 100%;
      padding: 0.9rem 1rem;
      font-size: 1rem;
      border: 2px solid #d1d5db;
      border-radius: var(--radius);
      background-color: #fff;
      transition: border-color 0.2s ease, box-shadow 0.2s ease;
    }

    input[type="text"]:focus {
      border-color: var(--primary);
      outline: none;
      box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.15);
    }

    button[type="submit"] {
      background: linear-gradient(90deg, var(--primary), var(--accent));
      color: white;
      padding: 1rem 1.2rem;
      font-size: 1rem;
      font-weight: bold;
      border: none;
      border-radius: var(--radius);
      cursor: pointer;
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 0.5rem;
      transition: background 0.3s ease, transform 0.2s ease;
    }

    button[type="submit"]:hover {
      background: linear-gradient(90deg, var(--accent), var(--primary));
      transform: scale(1.03);
    }

    @keyframes fadeIn {
      from {
        opacity: 0;
        transform: translateY(30px);
      }

      to {
        opacity: 1;
        transform: translateY(0);
      }
    }

    /* Responsive Styles */
    @media (max-width: 768px) {
      .status-form-container {
        padding: 1.5rem;
      }

      h2 {
        font-size: 1.6rem;
      }
    }

    @media (max-width: 425px) {
      .status-form-container {
        padding: 1.2rem;
      }

      h2 {
        font-size: 1.4rem;
      }

      input[type="text"],
      button[type="submit"] {
        font-size: 0.95rem;
        padding: 0.85rem 1rem;
      }

      .form-group {
        flex-direction: column;
        align-items: flex-start;
        gap: 0.2rem;
      }
    }

    @media (max-width: 375px) {
      h2 {
        font-size: 1.25rem;
      }

      input[type="text"] {
        font-size: 0.9rem;
      }

      button[type="submit"] {
        font-size: 0.9rem;
        padding: 0.8rem 1rem;
      }
    }

    @media (max-width: 320px) {
      body {
        padding: 1rem;
      }

      .status-form-container {
        padding: 1rem;
      }

      h2 {
        font-size: 1.1rem;
      }

      input[type="text"] {
        padding: 0.75rem;
      }

      button[type="submit"] {
        padding: 0.75rem 0.9rem;
      }
    }
  </style>
</head>

<body>
  <div class="status-form-container">
    <h2><i class="fas fa-mobile-alt"></i> Check Mediator Status</h2>
    <h3>(Trusted OR Not)</h3>
    <form action="status.php" method="GET">
      <div class="form-group">
        <i class="fas fa-user"></i>
        <label for="name">Name:</label>
      </div>
      <input type="text" id="name" name="name" placeholder="Name">

      <div class="form-group">
        <i class="fas fa-phone"></i>
        <label for="mobile_number">Mobile Number:</label>
      </div>
      <input type="text" id="mobile_number" name="mobile_number" pattern="[0-9]{10}" required placeholder="10-digit number" maxlength="10">

      <button type="submit"><i class="fas fa-search"></i> Check Status</button>
    </form>
  </div>
</body>

</html>