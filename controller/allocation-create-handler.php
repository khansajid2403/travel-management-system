<?php
session_start();
require_once __DIR__ . '/../model/ResourceAllocation.php';
require_once __DIR__ . '/../model/Booking.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'coordinator') {
	header("Location: ../");
	exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
	$booking_id = $_POST['booking_id'];
	$hotel_id = $_POST['hotel_id'];
	$transport_id = $_POST['transport_id'];

	$booking = new Booking();
	$row = $booking->getBookingById($booking_id);

	if (!$row || $row['status'] != 'accepted') {
		$_SESSION['allocation_error'] = 'That booking is not ready to be assigned';
		header("Location: ../view/coordinator/bookings.php");
		exit();
	}

	if (empty($hotel_id) || empty($transport_id)) {
		$_SESSION['allocation_error'] = 'Pick a hotel and a transport';
		header("Location: ../view/coordinator/bookings.php");
		exit();
	}

	$allocation = new ResourceAllocation();
	$allocation->assign($booking_id, $hotel_id, $transport_id);
	$_SESSION['allocation_success'] = 'Assigned';
}

header("Location: ../view/coordinator/bookings.php");
