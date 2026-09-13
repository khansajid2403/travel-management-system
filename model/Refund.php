<?php
require_once __DIR__ . '/../db/db_connection.php';

class Refund {
	function establishConnection() {
		$db_connection = new DBConnection();
		return $db_connection->connect();
	}

	function createRefund($payment_id, $finance_id, $amount) {
		$sql = "INSERT INTO refund (payment_id, finance_id, amount) VALUES (?, ?, ?);";
		$connection = $this->establishConnection();
		$prepared_statement = $connection->prepare($sql);
		$prepared_statement->bind_param('iid', $payment_id, $finance_id, $amount);
		if ($prepared_statement->execute()) {
			return $connection->insert_id;
		} else {
			return false;
		}
	}

	function getRefundByPaymentId($payment_id) {
		$sql = "SELECT * FROM refund WHERE payment_id = ?;";
		$connection = $this->establishConnection();
		$prepared_statement = $connection->prepare($sql);
		$prepared_statement->bind_param('i', $payment_id);
		$prepared_statement->execute();
		$result = $prepared_statement->get_result();
		return $result->fetch_assoc();
	}
}
