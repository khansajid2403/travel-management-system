<?php
session_start();
require_once __DIR__ . '/../model/Agent.php';
require_once __DIR__ . '/../model/Customer.php';
require_once __DIR__ . '/../model/Coordinator.php';
require_once __DIR__ . '/../model/FinanceManager.php';

if (!isset($_SESSION['role'])) {
	header("Location: ../");
	exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
	$name = trim($_POST['name']);
	$phone = trim($_POST['phone']);

	if (strlen($name) < 3) {
		$_SESSION['profile_error'] = 'Name\'s too short';
		header("Location: ../?target=profile");
		exit();
	}

	if (!preg_match('/^01[0-9]{9}$/', $phone)) {
		$_SESSION['profile_error'] = 'Phone number must start with 01 and be 11 digits';
		header("Location: ../?target=profile");
		exit();
	}

	$models = [
		'agent' => new Agent(),
		'customer' => new Customer(),
		'coordinator' => new Coordinator(),
		'finance_manager' => new FinanceManager()
	];

	$model = $models[$_SESSION['role']];
	$model->updateProfile($_SESSION['user_id'], $name, $phone);
	$_SESSION['name'] = $name;
	$_SESSION['profile_success'] = 'Profile updated';
	header("Location: ../?target=profile");

} else {
	header("Location: ../?target=profile");
}
