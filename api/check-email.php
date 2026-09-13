<?php
require_once __DIR__ . '/../model/Agent.php';
require_once __DIR__ . '/../model/Customer.php';
require_once __DIR__ . '/../model/Coordinator.php';
require_once __DIR__ . '/../model/FinanceManager.php';

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
	$email = isset($_GET['email']) ? $_GET['email'] : '';

	$models = [
		'agent' => new Agent(),
		'customer' => new Customer(),
		'coordinator' => new Coordinator(),
		'finance_manager' => new FinanceManager()
	];

	$role = isset($_GET['role']) ? $_GET['role'] : '';

	if ($email == '' || !array_key_exists($role, $models)) {
		echo json_encode(['available' => false, 'error' => 'Invalid request']);
	} else {
		$exists = $models[$role]->emailExists($email);
		echo json_encode(['available' => !$exists]);
	}
}
