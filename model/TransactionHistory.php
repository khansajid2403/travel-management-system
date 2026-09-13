<?php
require_once __DIR__ . '/../db/db_connection.php';

class TransactionHistory {
	function establishConnection() {
		$db_connection = new DBConnection();
		return $db_connection->connect();
	}

	function logPayment($payment_id, $amount) {
		$sql = "INSERT INTO transaction_history (payment_id, refund_id, transaction_type, amount) VALUES (?, NULL, 'payment', ?);";
		$connection = $this->establishConnection();
		$prepared_statement = $connection->prepare($sql);
		$prepared_statement->bind_param('id', $payment_id, $amount);
		return $prepared_statement->execute();
	}

	function logRefund($refund_id, $amount) {
		$sql = "INSERT INTO transaction_history (payment_id, refund_id, transaction_type, amount) VALUES (NULL, ?, 'refund', ?);";
		$connection = $this->establishConnection();
		$prepared_statement = $connection->prepare($sql);
		$prepared_statement->bind_param('id', $refund_id, $amount);
		return $prepared_statement->execute();
	}

	function getAllTransactions() {
		$connection = $this->establishConnection();

		$sql = "SELECT * FROM transaction_history ORDER BY transaction_id DESC;";
		$result = $connection->query($sql);

		$transactions = [];

		while ($row = $result->fetch_assoc()) {
			if ($row['transaction_type'] == 'payment') {
				$sql2 = "SELECT booking_id FROM payment WHERE payment_id = ?;";
				$stmt2 = $connection->prepare($sql2);
				$stmt2->bind_param('i', $row['payment_id']);
				$stmt2->execute();
				$payment_row = $stmt2->get_result()->fetch_assoc();
				$booking_id = $payment_row['booking_id'];
			} else {
				$sql2b = "SELECT payment_id FROM refund WHERE refund_id = ?;";
				$stmt2b = $connection->prepare($sql2b);
				$stmt2b->bind_param('i', $row['refund_id']);
				$stmt2b->execute();
				$refund_row = $stmt2b->get_result()->fetch_assoc();

				$sql2c = "SELECT booking_id FROM payment WHERE payment_id = ?;";
				$stmt2c = $connection->prepare($sql2c);
				$stmt2c->bind_param('i', $refund_row['payment_id']);
				$stmt2c->execute();
				$payment_row = $stmt2c->get_result()->fetch_assoc();
				$booking_id = $payment_row['booking_id'];
			}

			$sql3 = "SELECT customer_id, package_id FROM booking WHERE booking_id = ?;";
			$stmt3 = $connection->prepare($sql3);
			$stmt3->bind_param('i', $booking_id);
			$stmt3->execute();
			$booking_row = $stmt3->get_result()->fetch_assoc();

			$sql4 = "SELECT name FROM customer WHERE customer_id = ?;";
			$stmt4 = $connection->prepare($sql4);
			$stmt4->bind_param('i', $booking_row['customer_id']);
			$stmt4->execute();
			$customer_row = $stmt4->get_result()->fetch_assoc();

			$sql5 = "SELECT package_name FROM package WHERE package_id = ?;";
			$stmt5 = $connection->prepare($sql5);
			$stmt5->bind_param('i', $booking_row['package_id']);
			$stmt5->execute();
			$package_row = $stmt5->get_result()->fetch_assoc();

			$row['customer_name'] = $customer_row['name'];
			$row['package_name'] = $package_row['package_name'];
			$transactions[] = $row;
		}

		return $transactions;
	}
}
