<?php
// api/wishlist-toggle.php
header('Content-Type: application/json');
session_start();
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';
if (!isLoggedIn()) { echo json_encode(['error'=>'Non connecté']); exit; }
$data = json_decode(file_get_contents('php://input'), true);
$pid  = (int)($data['product_id'] ?? 0);
$uid  = $_SESSION['user_id'];
$stmt = $pdo->prepare("SELECT 1 FROM wishlist WHERE user_id=? AND product_id=?");
$stmt->execute([$uid,$pid]);
if ($stmt->fetchColumn()) {
    $pdo->prepare("DELETE FROM wishlist WHERE user_id=? AND product_id=?")->execute([$uid,$pid]);
    echo json_encode(['success'=>true,'wishlisted'=>false]);
} else {
    $pdo->prepare("INSERT IGNORE INTO wishlist (user_id,product_id) VALUES (?,?)")->execute([$uid,$pid]);
    echo json_encode(['success'=>true,'wishlisted'=>true]);
}
