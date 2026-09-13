<?php
session_start();
require_once __DIR__ . '/../model/Transport.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'coordinator') {
	header("Location: ../");
	exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
	$transport_id = $_POST['transport_id'];
	$type = trim($_POST['type']);
	$capacity = $_POST['capacity'];
	$provider = trim($_POST['provider']);

	if (strlen($type) < 2 || !is_numeric($capacity) || $capacity < 1) {
		$_SESSION['transport_error'] = 'Check the form and try again';
		header("Location: ../view/coordinator/transport-form.php?id=" . $transport_id);
		exit();
	}

	$transport = new Transport();
	$transport->updateTransport($transport_id, $type, $capacity, $provider);
	$_SESSION['transport_success'] = 'Transport updated';
	header("Location: ../view/coordinator/transport.php");

} else {
	header("Location: ../view/coordinator/transport.php");
}
