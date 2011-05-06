<?php
function renderContent() {
    unset ($_SESSION['userID']);
    header("Location: index.php");
    exit;
}
?>
