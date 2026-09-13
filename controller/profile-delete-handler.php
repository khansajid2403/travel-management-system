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
	$models = [
		'agent' => new Agent(),
		'customer' => new Customer(),
		'coordinator' => new Coordinator(),
		'finance_manager' => new FinanceManager()
	];

	$model = $models[$_SESSION['role']];
	$model->deleteById($_SESSION['user_id']);

	session_unset();
	session_destroy();
	header("Location: ../");

} else {
	header("Location: ../?target=profile");
}
