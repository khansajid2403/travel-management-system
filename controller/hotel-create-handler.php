<?php
session_start();
require_once __DIR__ . '/../model/Hotel.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'coordinator') {
	header("Location: ../");
	exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
	$hotel_name = trim($_POST['hotel_name']);
	$location = trim($_POST['location']);
	$rooms = $_POST['rooms'];

	if (strlen($hotel_name) < 3 || !is_numeric($rooms) || $rooms < 1) {
		$_SESSION['hotel_error'] = 'Check the form and try again';
		header("Location: ../view/coordinator/hotel-form.php");
		exit();
	}

	$hotel = new Hotel();
	$hotel->createHotel($_SESSION['user_id'], $hotel_name, $location, $rooms);
	$_SESSION['hotel_success'] = 'Hotel added';
	header("Location: ../view/coordinator/hotels.php");

} else {
	header("Location: ../view/coordinator/hotel-form.php");
}
