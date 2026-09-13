<?php
session_start();
require_once __DIR__ . '/../model/Companion.php';
require_once __DIR__ . '/../model/Booking.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'customer') {
	header("Location: ../");
	exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
	$booking_id = $_POST['booking_id'];
	$name = trim($_POST['name']);
	$relation = trim($_POST['relation']);
	$phone = trim($_POST['phone']);

	$booking = new Booking();
	$row = $booking->getBookingById($booking_id);

	$companion = new Companion();
	$already_added = $companion->countCompanionsForBooking($booking_id);

	$seats_for_companions = $row['num_travelers'] - 1;

	$error = '';

	if (!$row || $row['customer_id'] != $_SESSION['user_id']) {
		$error = 'Booking not found';
	} else if ($row['status'] != 'pending') {
		$error = 'Can\'t add companions after it\'s accepted';
	} else if (strlen($name) < 2) {
		$error = 'Enter a name';
	} else if (!preg_match('/^01[0-9]{9}$/', $phone)) {
		$error = 'Phone number must start with 01 and be 11 digits';
	} else if ($already_added >= $seats_for_companions) {
		$error = 'No more room - you booked for ' . $row['num_travelers'] . ' people';
	}

	if ($error !== '') {
		$_SESSION['companion_error'] = $error;
		header("Location: ../view/customer/bookings.php");
		exit();
	}

	$companion->addCompanion($booking_id, $name, $relation, $phone);
}

header("Location: ../view/customer/bookings.php");
