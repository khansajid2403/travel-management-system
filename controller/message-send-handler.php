<?php
session_start();
require_once __DIR__ . '/../model/CommunicationLog.php';

if (!isset($_SESSION['role']) || ($_SESSION['role'] !== 'agent' && $_SESSION['role'] !== 'customer')) {
	header("Location: ../");
	exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
	$message = trim($_POST['message']);
	$agent_id = $_POST['agent_id'];
	$customer_id = $_POST['customer_id'];

	if (strlen($message) > 0) {
		$log = new CommunicationLog();
		$log->sendMessage($agent_id, $customer_id, $_SESSION['role'], $message);
	}

	if ($_SESSION['role'] == 'agent') {
		header("Location: ../view/agent/messages.php?with=" . $customer_id);
	} else {
		header("Location: ../view/customer/messages.php?with=" . $agent_id);
	}

} else {
	header("Location: ../");
}
