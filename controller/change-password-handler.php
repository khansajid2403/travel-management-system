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
	$current_password = $_POST['current_password'];
	$new_password = $_POST['new_password'];
	$confirm_password = $_POST['confirm_password'];

	$models = [
		'agent' => new Agent(),
		'customer' => new Customer(),
		'coordinator' => new Coordinator(),
		'finance_manager' => new FinanceManager()
	];

	$model = $models[$_SESSION['role']];
	$user = $model->getById($_SESSION['user_id']);

	$error = '';

	if ($user['password'] != $current_password) {
		$error = 'Wrong current password';
	} else if (strlen($new_password) < 6) {
		$error = '6 characters minimum';
	} else if ($new_password !== $confirm_password) {
		$error = 'Passwords don\'t match';
	}

	if ($error !== '') {
		$_SESSION['password_error'] = $error;
		header("Location: ../?target=change-password");
		exit();
	}

	
	$model->updatePassword($_SESSION['user_id'], $new_password);
	$_SESSION['password_success'] = 'Password changed';
	header("Location: ../?target=change-password");

} else {
	header("Location: ../?target=change-password");
}
