<?php
session_start();
require_once __DIR__ . '/../model/Review.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'customer') {
	header("Location: ../");
	exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
	$package_id = $_POST['package_id'];
	$rating = $_POST['rating'];
	$comment = trim($_POST['comment']);

	if (!is_numeric($rating) || $rating < 1 || $rating > 5) {
		$_SESSION['review_error'] = 'Rating has to be 1-5';
		header("Location: ../view/customer/package-detail.php?id=" . $package_id);
		exit();
	}

	$review = new Review();
	$review->addReview($_SESSION['user_id'], $package_id, $rating, $comment);
	$_SESSION['review_success'] = 'Review submitted';
}

header("Location: ../view/customer/package-detail.php?id=" . $package_id);
