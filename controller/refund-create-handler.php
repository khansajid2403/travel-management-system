<?php
session_start();
require_once __DIR__ . '/../model/Payment.php';
require_once __DIR__ . '/../model/Refund.php';
require_once __DIR__ . '/../model/TransactionHistory.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'finance_manager') {
	header("Location: ../");
	exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
	$payment_id = $_POST['payment_id'];

	$payment = new Payment();
	$row = $payment->getPaymentById($payment_id);

	if (!$row || $row['status'] != 'paid') {
		$_SESSION['finance_error'] = 'That payment is not eligible for a refund';
		header("Location: ../view/finance/refunds.php");
		exit();
	}

	$refund = new Refund();
	$refund_id = $refund->createRefund($payment_id, $_SESSION['user_id'], $row['amount']);

	if ($refund_id) {
		$payment->updateStatus($payment_id, 'refunded');
		$transaction = new TransactionHistory();
		$transaction->logRefund($refund_id, $row['amount']);
		$_SESSION['finance_success'] = 'Refund issued';
	} else {
		$_SESSION['finance_error'] = 'Something went wrong, try again';
	}
}

header("Location: ../view/finance/refunds.php");
