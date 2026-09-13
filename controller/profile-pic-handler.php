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

	if (isset($_FILES['profile_pic']) && $_FILES['profile_pic']['error'] == UPLOAD_ERR_OK) {
		$allowed_type = ['image/jpeg', 'image/png'];

		if (in_array($_FILES['profile_pic']['type'], $allowed_type)) {
			$profile_pic = file_get_contents($_FILES['profile_pic']['tmp_name']);
			$profile_pic_type = $_FILES['profile_pic']['type'];

			$models = [
				'agent' => new Agent(),
				'customer' => new Customer(),
				'coordinator' => new Coordinator(),
				'finance_manager' => new FinanceManager()
			];

			$model = $models[$_SESSION['role']];
			$model->updateProfilePic($_SESSION['user_id'], $profile_pic, $profile_pic_type);
			$_SESSION['profile_success'] = 'Profile picture updated';
		} else {
			$_SESSION['profile_error'] = 'Only JPEG or PNG please';
		}
	} else {
		$_SESSION['profile_error'] = 'Pick a picture first';
	}

	header("Location: ../?target=profile");

} else {
	header("Location: ../?target=profile");
}
