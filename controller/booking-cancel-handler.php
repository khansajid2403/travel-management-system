<?php
session_start();
require_once __DIR__ . '/../model/Booking.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'customer') {
	header("Location: ../");
	exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
	$booking_id = $_POST['booking_id'];

	$booking = new Booking();
	$row = $booking->getBookingById($booking_id);

	if ($row && $row['customer_id'] == $_SESSION['user_id'] && $row['status'] == 'pending') {
		$booking->deleteBooking($booking_id);
	}
}

header("Location: ../view/customer/bookings.php");
