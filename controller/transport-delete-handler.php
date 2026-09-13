<?php
session_start();
require_once __DIR__ . '/../model/Transport.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'coordinator') {
	header("Location: ../");
	exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
	$transport_id = $_POST['transport_id'];
	$transport = new Transport();
	$transport->deleteTransport($transport_id);
	$_SESSION['transport_success'] = 'Transport deleted';
}

header("Location: ../view/coordinator/transport.php");
