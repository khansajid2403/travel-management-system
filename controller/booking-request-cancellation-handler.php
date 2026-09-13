<?php
session_start();
require_once __DIR__ . '/../model/Booking.php';
require_once __DIR__ . '/../model/Payment.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'customer') {
	header("Location: ../");
	exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
	$booking_id = $_POST['booking_id'];

	$booking = new Booking();
	$row = $booking->getBookingById($booking_id);

	$payment = new Payment();
	$payment_row = $payment->getPaymentByBookingId($booking_id);

	if (!$row || $row['customer_id'] != $_SESSION['user_id']) {
		$_SESSION['payment_error'] = 'Booking not found';
	} else if (!$payment_row || $payment_row['status'] != 'paid') {
		$_SESSION['payment_error'] = 'This booking has not been paid yet';
	} else if ($row['cancellation_requested']) {
		$_SESSION['payment_error'] = 'Already requested';
	} else {
		$booking->requestCancellation($booking_id);
		$_SESSION['payment_success'] = 'Cancellation requested';
	}
}

header("Location: ../view/customer/payments.php");
