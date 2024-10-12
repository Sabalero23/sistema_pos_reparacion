<?php
session_start();
header('Content-Type: application/json');

$_SESSION['carrito'] = [];

echo json_encode(['success' => true]);