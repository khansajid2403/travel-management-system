<?php
session_start();
require_once __DIR__ . '/../model/Booking.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'agent') {
	header("Location: ../");
	exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
	$booking_id = $_POST['booking_id'];
	$status = $_POST['status'];

	if ($status == 'accepted' || $status == 'rejected') {
		$booking = new Booking();
		$booking->updateStatus($booking_id, $status);
	}
}

header("Location: ../view/agent/bookings.php");
