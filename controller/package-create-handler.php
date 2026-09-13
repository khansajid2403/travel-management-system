<?php
session_start();
require_once __DIR__ . '/../model/Package.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'agent') {
	header("Location: ../");
	exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
	$package_name = trim($_POST['package_name']);
	$destination = trim($_POST['destination']);
	$price = $_POST['price'];
	$duration = trim($_POST['duration']);
	$description = trim($_POST['description']);

	if (strlen($package_name) < 3 || !is_numeric($price) || $price <= 0) {
		$_SESSION['package_error'] = 'Check the form and try again';
		header("Location: ../view/agent/package-form.php");
		exit();
	}

	$package = new Package();
	$package->createPackage($_SESSION['user_id'], $package_name, $destination, $price, $duration, $description);
	$_SESSION['package_success'] = 'Package created';
	header("Location: ../view/agent/packages.php");

} else {
	header("Location: ../view/agent/package-form.php");
}
