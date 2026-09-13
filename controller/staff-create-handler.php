<?php
session_start();
require_once __DIR__ . '/../model/Coordinator.php';
require_once __DIR__ . '/../model/FinanceManager.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'agent') {
	header("Location: ../");
	exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
	$name = trim($_POST['name']);
	$email = trim($_POST['email']);
	$phone = trim($_POST['phone']);
	$password = $_POST['password'];
	$confirm_password = $_POST['confirm_password'];
	$role = $_POST['role'];

	$models = [
		'coordinator' => new Coordinator(),
		'finance_manager' => new FinanceManager()
	];

	$error = '';

	if (!array_key_exists($role, $models)) {
		$error = 'Pick a role';
	} else if (strlen($name) < 3) {
		$error = 'Name\'s too short';
	} else if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
		$error = 'That email doesn\'t look right';
	} else if ($email !== strtolower($email)) {
		$error = 'Email must be lowercase';
	} else if (!preg_match('/^01[0-9]{9}$/', $phone)) {
		$error = 'Phone number must start with 01 and be 11 digits';
	} else if (strlen($password) < 6) {
		$error = '6 characters minimum';
	} else if ($password !== $confirm_password) {
		$error = 'Passwords don\'t match';
	} else if ($models[$role]->emailExists($email)) {
		$error = 'This email is already in use';
	}

	if ($error !== '') {
		$_SESSION['staff_error'] = $error;
		header("Location: ../view/agent/create-profile.php");
		exit();
	}

	$result = $models[$role]->registerUser($name, $email, $phone, $password);

	if ($result) {
		$_SESSION['staff_success'] = 'Profile created';
	} else {
		$_SESSION['staff_error'] = 'Something went wrong, try again';
	}

	header("Location: ../view/agent/create-profile.php");

} else {
	header("Location: ../view/agent/create-profile.php");
}
