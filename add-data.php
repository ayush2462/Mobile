<?php include 'db.php'; ?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <title>Add Mobile Status Data</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet" />
<style>
  :root {
    --primary: #2563eb;
    --accent: #3b82f6;
    --bg-light: #ffffff;
    --card-bg: #f9fafb;
    --text-dark: #1e293b;
    --muted: #6b7280;
    --border: #cbd5e1;
    --radius: 12px;
    --shadow: 0 8px 24px rgba(0, 0, 0, 0.08);
    --font: 'Segoe UI', sans-serif;
  }

  * {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
  }

  html,
  body {
    height: 100vh;
    overflow: hidden;
    background: grey;
    font-family: var(--font);
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 1rem;
    color: var(--text-dark);
  }

  .form-container {
    background-color: var(--card-bg);
    color: var(--text-dark);
    padding: 2rem;
    border-radius: var(--radius);
    box-shadow: var(--shadow);
    width: 100%;
    max-width: 480px;
    max-height: 100vh;
    overflow-y: auto;
    animation: fadeIn 0.5s ease;
  }

  .form {
    display: flex;
    flex-direction: column;
    gap: 1.2rem;
  }

  .form-container h2 {
    text-align: center;
    margin-bottom: 1rem;
    font-size: 1.7rem;
    color: var(--primary);
  }

  .inp {
    position: relative;
  }

  .inp i {
    position: absolute;
    top: 50%;
    left: 14px;
    transform: translateY(-50%);
    color: var(--muted);
    font-size: 1.2rem;
  }

  .inp input,
  .inp textarea {
    width: 100%;
    padding: 0.8rem 1rem 0.8rem 2.8rem;
    border: 2px solid var(--border);
    border-radius: var(--radius);
    font-size: 1rem;
    background-color: #fff;
    color: var(--text-dark);
    transition: 0.2s ease;
  }

  .inp input:focus,
  .inp textarea:focus {
    border-color: var(--primary);
    outline: none;
    box-shadow: 0 0 0 2px rgba(37, 99, 235, 0.2);
  }

  .radio-group {
    display: flex;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 0.5rem;
  }

  .radio-group label {
    font-size: 1rem;
    display: flex;
    align-items: center;
    gap: 0.3rem;
    color: var(--text-dark);
  }

  textarea {
    resize: vertical;
    background-color: #fff;
  }

  .button-row-vertical {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 0.7rem;
    margin-top: 1rem;
  }

  .status-button,
  .upload-button,
  .edit-button {
    background: linear-gradient(90deg, var(--primary), var(--accent));
    color: #fff;
    text-align: center;
    border: none;
    padding: 0.8rem 1.2rem;
    font-size: 1rem;
    font-weight: bold;
    border-radius: var(--radius);
    cursor: pointer;
    width: 100%;
    transition: 0.3s ease;
  }

  .status-button:hover,
  .upload-button:hover,
  .edit-button:hover {
    background: linear-gradient(90deg, #22d3ee, #4ade80);
    transform: scale(1.05);
    box-shadow: 0 4px 12px rgba(34, 211, 238, 0.4);
  }

  .edit-button {
    text-decoration: none;
    display: flex;
    justify-content: center;
    align-items: center;
    gap: 0.4rem;
  }

  .or-text {
    font-weight: bold;
    color: var(--muted);
    margin: 0.5rem 0;
  }

  @keyframes fadeIn {
    from {
      opacity: 0;
      transform: translateY(20px);
    }

    to {
      opacity: 1;
      transform: translateY(0);
    }
  }

  @media (max-width: 480px) {
    html,
    body {
      padding: 1rem;
    }

    .form-container {
      padding: 1.5rem;
    }

    .radio-group {
      flex-direction: column;
    }

    .status-button,
    .upload-button,
    .edit-button {
      font-size: 0.95rem;
    }
  }
</style>

</head>

<body>
  <div class="form-container">
    <h2><i class="bx bxs-mobile"></i> Add Mediator Status</h2>
    <form class="form" method="POST" action="insert.php">
      <div class="inp">
        <i class="bx bxs-user"></i>
        <input type="text" name="name" placeholder="Name" required />
      </div>

      <div class="inp">
        <i class="bx bxs-phone"></i>
        <input type="text" name="mobile_number" maxlength="10" placeholder="Enter phone number" pattern="[0-9]{10}" oninput="this.value = this.value.replace(/[^0-9]/g, '')" required />
      </div>

      <div class="radio-group">
        <label><input type="radio" name="status" value="trusted" required /> Trusted</label>
        <label><input type="radio" name="status" value="fraud" /> Fraud</label>
        <label><input type="radio" name="status" value="blacklisted" /> Blacklisted</label>
      </div>

      <div class="inp">
        <i class="bx bxs-edit-alt"></i>
        <textarea name="description" placeholder="Enter description" rows="3" cols="30"></textarea>
      </div>

      <div class="button-row-vertical">
        <button type="submit" class="status-button">SUBMIT</button>
        <div class="or-text">OR</div>
        <label for="excelFile" class="upload-button">Upload Excel File</label>
        <input type="file" id="excelFile" name="excel_file" style="display: none" accept=".xlsx, .xls" form="uploadForm" />
        <a href="edit.php" class="edit-button"><i class="bx bxs-edit-alt"></i> Edit Entries</a>
      </div>

    </form>

    <form id="uploadForm" action="upload.php" method="POST" enctype="multipart/form-data"></form>
  </div>

  <script>
    const fileInput = document.getElementById("excelFile");
    const uploadForm = document.getElementById("uploadForm");

    fileInput.addEventListener("change", () => {
      const file = fileInput.files[0];
      if (file && !file.name.match(/\.(xls|xlsx)$/i)) {
        alert("❌ Please upload a valid Excel file (.xls or .xlsx).");
        fileInput.value = "";
        return;
      }
      uploadForm.appendChild(fileInput);
      uploadForm.submit();
    });
  </script>
</body>

</html>