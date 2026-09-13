<?php
session_start();
require_once __DIR__ . '/../model/Hotel.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'coordinator') {
	header("Location: ../");
	exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
	$hotel_id = $_POST['hotel_id'];
	$hotel = new Hotel();
	$hotel->deleteHotel($hotel_id);
	$_SESSION['hotel_success'] = 'Hotel deleted';
}

header("Location: ../view/coordinator/hotels.php");
