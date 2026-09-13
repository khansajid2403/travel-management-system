<?php
session_start();
require_once __DIR__ . '/../model/Agent.php';
require_once __DIR__ . '/../model/Customer.php';
require_once __DIR__ . '/../model/Coordinator.php';
require_once __DIR__ . '/../model/FinanceManager.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
	$name = trim($_POST['name']);
	$email = trim($_POST['email']);
	$phone = trim($_POST['phone']);
	$password = $_POST['password'];
	$confirm_password = $_POST['confirm_password'];
	$role = $_POST['role'];

	$models = [
		'agent' => new Agent(),
		'customer' => new Customer()
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
		$_SESSION['register_error'] = $error;
		header("Location: ../view/create-account.php");
		exit();
	}

	$profile_pic = null;
	$profile_pic_type = null;

	if (isset($_FILES['profile_pic']) && $_FILES['profile_pic']['error'] == UPLOAD_ERR_OK) {
		$allowed_type = ['image/jpeg', 'image/png'];
		if (in_array($_FILES['profile_pic']['type'], $allowed_type)) {
			$profile_pic = file_get_contents($_FILES['profile_pic']['tmp_name']);
			$profile_pic_type = $_FILES['profile_pic']['type'];
		} else {
			$_SESSION['register_error'] = 'Only JPEG or PNG please';
			header("Location: ../view/create-account.php");
			exit();
		}
	}

	$result = $models[$role]->registerUser($name, $email, $phone, $password, $profile_pic, $profile_pic_type);

	if ($result) {
		$_SESSION['register_success'] = 'Profile created';
		header("Location: ../");
	} else {
		$_SESSION['register_error'] = 'Something went wrong, try again';
		header("Location: ../view/create-account.php");
	}

} else {
	header("Location: ../view/create-account.php");
}
