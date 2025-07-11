<?php
include '../css/style.css';
include '../includes/functions.php';
session_start();
redirectIfNotLoggedIn();
?>
<head>
    <title>Scan2Health - Search</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<h2 style="text-align: center;">Search Food Product</h2>

<form id="search-form" action="result.php" method="GET" style="text-align: center;">
    <input id="barcode-input" name="barcode" placeholder="Enter Barcode Value" required>
    <button type="submit">Search</button>
</form>

<h4>Upload a barcode image:</h4>
<input type="file" id="barcode-file" accept="image/*"><br><br>

<p>
    <a href="profile.php">Go to Profile</a> | 
    <a href="logout.php">Logout</a>
</p>

<!-- ✅ ZXing UMD Version -->
<script src="https://unpkg.com/@zxing/library@latest"></script>
<script>
const fileInput = document.getElementById('barcode-file');
const barcodeInput = document.getElementById('barcode-input');
const form = document.getElementById('search-form');

fileInput.addEventListener('change', () => {
    const file = fileInput.files[0];
    if (!file) return;

    const reader = new FileReader();
    reader.onload = function (e) {
        const img = new Image();
        img.style.display = "none"; // hide the image
        document.body.appendChild(img); // required for decodeFromImageElement to work
        img.onload = function () {
            const codeReader = new ZXing.BrowserMultiFormatReader();
            codeReader.decodeFromImageElement(img)
                .then(result => {
                    barcodeInput.value = result.text;
                    form.submit();
                })
                .catch(err => {
                    console.error("Barcode not found:", err);
                    alert("Could not detect a barcode in this image.");
                });
        };
        img.src = e.target.result;
    };
    reader.readAsDataURL(file);
});
</script>
<link rel="stylesheet" href="../css/style.css">