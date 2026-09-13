<?php
require_once __DIR__ . '/../db/db_connection.php';

class Payment {
	function establishConnection() {
		$db_connection = new DBConnection();
		return $db_connection->connect();
	}

	function createPayment($booking_id, $finance_id, $amount) {
		$sql = "INSERT INTO payment (booking_id, finance_id, amount, status) VALUES (?, ?, ?, 'paid');";
		$connection = $this->establishConnection();
		$prepared_statement = $connection->prepare($sql);
		$prepared_statement->bind_param('iid', $booking_id, $finance_id, $amount);
		if ($prepared_statement->execute()) {
			return $connection->insert_id;
		} else {
			return false;
		}
	}

	function getPaymentById($payment_id) {
		$sql = "SELECT * FROM payment WHERE payment_id = ?;";
		$connection = $this->establishConnection();
		$prepared_statement = $connection->prepare($sql);
		$prepared_statement->bind_param('i', $payment_id);
		$prepared_statement->execute();
		$result = $prepared_statement->get_result();
		return $result->fetch_assoc();
	}

	function getPaymentByBookingId($booking_id) {
		$sql = "SELECT * FROM payment WHERE booking_id = ?;";
		$connection = $this->establishConnection();
		$prepared_statement = $connection->prepare($sql);
		$prepared_statement->bind_param('i', $booking_id);
		$prepared_statement->execute();
		$result = $prepared_statement->get_result();
		return $result->fetch_assoc();
	}

	function updateStatus($payment_id, $status) {
		$sql = "UPDATE payment SET status = ? WHERE payment_id = ?;";
		$connection = $this->establishConnection();
		$prepared_statement = $connection->prepare($sql);
		$prepared_statement->bind_param('si', $status, $payment_id);
		return $prepared_statement->execute();
	}

	function getBookingsReadyForPayment() {
		$connection = $this->establishConnection();

		$sql = "SELECT * FROM booking WHERE status = 'accepted';";
		$result = $connection->query($sql);

		$bookings = [];

		while ($row = $result->fetch_assoc()) {
			$existing = $this->getPaymentByBookingId($row['booking_id']);
			if ($existing) {
				continue;
			}

			$sql2 = "SELECT name FROM customer WHERE customer_id = ?;";
			$stmt2 = $connection->prepare($sql2);
			$stmt2->bind_param('i', $row['customer_id']);
			$stmt2->execute();
			$customer_row = $stmt2->get_result()->fetch_assoc();

			$sql3 = "SELECT package_name, price FROM package WHERE package_id = ?;";
			$stmt3 = $connection->prepare($sql3);
			$stmt3->bind_param('i', $row['package_id']);
			$stmt3->execute();
			$package_row = $stmt3->get_result()->fetch_assoc();

			$row['customer_name'] = $customer_row['name'];
			$row['package_name'] = $package_row['package_name'];
			$row['amount'] = $package_row['price'] * $row['num_travelers'];
			$bookings[] = $row;
		}

		return $bookings;
	}

	function getPaidPayments() {
		$connection = $this->establishConnection();

		$sql = "SELECT * FROM payment WHERE status = 'paid';";
		$result = $connection->query($sql);

		$payments = [];

		while ($row = $result->fetch_assoc()) {
			$sql2 = "SELECT customer_id, package_id, cancellation_requested FROM booking WHERE booking_id = ?;";
			$stmt2 = $connection->prepare($sql2);
			$stmt2->bind_param('i', $row['booking_id']);
			$stmt2->execute();
			$booking_row = $stmt2->get_result()->fetch_assoc();

			$sql3 = "SELECT name FROM customer WHERE customer_id = ?;";
			$stmt3 = $connection->prepare($sql3);
			$stmt3->bind_param('i', $booking_row['customer_id']);
			$stmt3->execute();
			$customer_row = $stmt3->get_result()->fetch_assoc();

			$sql4 = "SELECT package_name FROM package WHERE package_id = ?;";
			$stmt4 = $connection->prepare($sql4);
			$stmt4->bind_param('i', $booking_row['package_id']);
			$stmt4->execute();
			$package_row = $stmt4->get_result()->fetch_assoc();

			$row['customer_name'] = $customer_row['name'];
			$row['package_name'] = $package_row['package_name'];
			$row['cancellation_requested'] = $booking_row['cancellation_requested'];
			$payments[] = $row;
		}

		usort($payments, function($a, $b) {
			return $b['cancellation_requested'] - $a['cancellation_requested'];
		});

		return $payments;
	}

	function getPaymentsForCustomer($customer_id) {
		$connection = $this->establishConnection();

		$sql = "SELECT booking_id, package_id, cancellation_requested FROM booking WHERE customer_id = ?;";
		$prepared_statement = $connection->prepare($sql);
		$prepared_statement->bind_param('i', $customer_id);
		$prepared_statement->execute();
		$booking_result = $prepared_statement->get_result();

		$payments = [];

		while ($booking_row = $booking_result->fetch_assoc()) {
			$sql2 = "SELECT * FROM payment WHERE booking_id = ?;";
			$stmt2 = $connection->prepare($sql2);
			$stmt2->bind_param('i', $booking_row['booking_id']);
			$stmt2->execute();
			$payment_row = $stmt2->get_result()->fetch_assoc();

			if (!$payment_row) {
				continue;
			}

			$sql3 = "SELECT package_name FROM package WHERE package_id = ?;";
			$stmt3 = $connection->prepare($sql3);
			$stmt3->bind_param('i', $booking_row['package_id']);
			$stmt3->execute();
			$package_row = $stmt3->get_result()->fetch_assoc();

			$payment_row['package_name'] = $package_row['package_name'];
			$payment_row['booking_id'] = $booking_row['booking_id'];
			$payment_row['cancellation_requested'] = $booking_row['cancellation_requested'];
			$payments[] = $payment_row;
		}

		return $payments;
	}
}
