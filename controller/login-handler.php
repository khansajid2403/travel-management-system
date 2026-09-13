<?php
session_start();
require_once __DIR__ . '/../model/Agent.php';
require_once __DIR__ . '/../model/Customer.php';
require_once __DIR__ . '/../model/Coordinator.php';
require_once __DIR__ . '/../model/FinanceManager.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
	$email = trim($_POST['email']);
	$password = $_POST['password'];

	if (!filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($password) == 0) {
		$_SESSION['login_error'] = 'Enter your email and password';
		header("Location: ../");
		exit();
	}

	$roles = [
		'agent' => new Agent(),
		'customer' => new Customer(),
		'coordinator' => new Coordinator(),
		'finance_manager' => new FinanceManager()
	];

	$id_columns = [
		'agent' => 'agent_id',
		'customer' => 'customer_id',
		'coordinator' => 'coordinator_id',
		'finance_manager' => 'finance_id'
	];

	$logged_in = false;

	foreach ($roles as $role => $model) {
		$user = $model->loginCheck($email, $password);
		if ($user) {
			$_SESSION['user_id'] = $user[$id_columns[$role]];
			$_SESSION['name'] = $user['name'];
			$_SESSION['email'] = $user['email'];
			$_SESSION['role'] = $role;
			$logged_in = true;
			break;
		}
	}

	if ($logged_in) {
		setcookie("last_login", date('Y-m-d H:i:s'), time() + 3600*24*7);
		unset($_SESSION['login_error']);
	} else {
		$_SESSION['login_error'] = 'Wrong email or password';
	}

	header("Location: ../");

} else {
	header("Location: ../");
}
