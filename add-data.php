<?php include 'db.php'; ?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <title>Add Mobile Status Data</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <link
    href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css"
    rel="stylesheet" />
  <link rel="stylesheet" href="./style/add.css" />
</head>

<body>
  <div class="page">
    <div class="top-image"></div>

    <div class="form-container">
      <!-- Manual Entry Form -->
      <form class="form" method="POST" action="insert.php">
        <!-- Name Field -->
        <div class="inp">
          <i class="bx bxs-user"></i>
          <input type="text" name="name" placeholder="Name" required />
        </div>

        <!-- Mobile Number Field -->
        <div class="inp">
          <i class="bx bxs-phone"></i>
          <input
            type="text"
            name="mobile_number"
            maxlength="10"
            placeholder="Enter phone number"
            pattern="[0-9]{10}"
            oninput="this.value = this.value.replace(/[^0-9]/g, '')"
            required />
        </div>

        <!-- Status Radio Buttons -->
        <div class="radio-group">
          <label><input type="radio" name="status" value="trusted" required /> Trusted</label>
          <label><input type="radio" name="status" value="fraud" /> Fraud</label>
          <label><input type="radio" name="status" value="blacklisted" /> Blacklisted</label>
        </div>

        <!-- Description Field -->
        <div class="inp">
          <i class="bx bxs-edit-alt"></i>
          <textarea name="description" placeholder="Enter description " rows="3" cols="30"></textarea>
        </div>

        <!-- Buttons -->
        <div class="button-row-vertical">
          <button type="submit" class="status-button">ENTER DATA STATUS</button>

          <div class="or-text">OR</div>

          <label for="excelFile" class="upload-button">Upload Excel File</label>
          <input
            type="file"
            id="excelFile"
            name="excel_file"
            style="display: none"
            accept=".xlsx, .xls"
            form="uploadForm" />
        </div>
      </form>

      <!-- Excel Upload Form -->
      <form id="uploadForm" action="upload.php" method="POST" enctype="multipart/form-data"></form>
    </div>
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
      uploadForm.appendChild(fileInput); // move input into form
      uploadForm.submit();
    });
  </script>
</body>

</html>