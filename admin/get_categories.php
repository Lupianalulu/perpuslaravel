<?php
include 'config.php';

$categories = getCategories();
echo json_encode($categories);
?>
