<?php
include '../css/style.css';
session_start();
session_destroy();
header("Location: login.php");
exit();
?>
<link rel="stylesheet" href="../css/style.css">