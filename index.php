<?php include 'db.php'; ?>
<!DOCTYPE html>
<html>

<head>
    <title>Mobile Status Checker</title>
    <link rel="stylesheet" href="/Ayush/style/main.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    
</head>

<body>
    <div class="status-form-container">
        <h2><i class="fas fa-mobile-alt"></i> Check Mobile Number Status</h2>
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
            <input type="text" id="mobile_number" name="mobile_number" pattern="[0-9]{10}" required placeholder="10-digit number">

            <button type="submit"><i class="fas fa-search"></i> Check Status</button>
        </form>
    </div>
</body>

</html>