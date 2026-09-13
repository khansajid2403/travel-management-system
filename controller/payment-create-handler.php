<?php
session_start();
require_once __DIR__ . '/../model/Payment.php';
require_once __DIR__ . '/../model/TransactionHistory.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'finance_manager') {
	header("Location: ../");
	exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
	$booking_id = $_POST['booking_id'];
	$amount = $_POST['amount'];

	$payment = new Payment();
	$payment_id = $payment->createPayment($booking_id, $_SESSION['user_id'], $amount);

	if ($payment_id) {
		$transaction = new TransactionHistory();
		$transaction->logPayment($payment_id, $amount);
		$_SESSION['finance_success'] = 'Payment recorded';
	} else {
		$_SESSION['finance_error'] = 'Something went wrong, try again';
	}
}

header("Location: ../view/finance/bookings.php");
