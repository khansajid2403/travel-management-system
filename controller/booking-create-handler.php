<?php
session_start();
require_once __DIR__ . '/../model/Booking.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'customer') {
	header("Location: ../");
	exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
	$package_id = $_POST['package_id'];
	$travel_date = $_POST['travel_date'];
	$num_travelers = $_POST['num_travelers'];

	$error = '';

	if (empty($travel_date)) {
		$error = 'Pick a date';
	} else if (!is_numeric($num_travelers) || $num_travelers < 1) {
		$error = 'Check the form and try again';
	}

	$date_for_db = '';
	if ($error == '') {
		$date_parts = explode('/', $travel_date);

		if (count($date_parts) != 3) {
			$error = 'Use dd/mm/yyyy';
		} else {
			$day = $date_parts[0];
			$month = $date_parts[1];
			$year = $date_parts[2];

			if (!checkdate($month, $day, $year)) {
				$error = 'That\'s not a real date';
			} else {
				$date_for_db = $year . '-' . $month . '-' . $day;
			}
		}
	}

	if ($error !== '') {
		$_SESSION['booking_error'] = $error;
		header("Location: ../view/customer/package-detail.php?id=" . $package_id);
		exit();
	}

	$booking = new Booking();
	$booking->createBooking($_SESSION['user_id'], $package_id, $date_for_db, $num_travelers);
	header("Location: ../view/customer/bookings.php");

} else {
	header("Location: ../view/customer/packages.php");
}
