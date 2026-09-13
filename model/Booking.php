<?php
require_once __DIR__ . '/../db/db_connection.php';

class Booking {
	function establishConnection() {
		$db_connection = new DBConnection();
		return $db_connection->connect();
	}

	function createBooking($customer_id, $package_id, $travel_date, $num_travelers) {
		$sql = "INSERT INTO booking (customer_id, package_id, travel_date, num_travelers) VALUES (?, ?, ?, ?);";
		$connection = $this->establishConnection();
		$prepared_statement = $connection->prepare($sql);
		$prepared_statement->bind_param('iisi', $customer_id, $package_id, $travel_date, $num_travelers);
		if ($prepared_statement->execute()) {
			return $connection->insert_id;
		} else {
			return false;
		}
	}

	function getBookingsForAgent($agent_id) {
		$connection = $this->establishConnection();

		$sql = "SELECT package_id, package_name FROM package WHERE agent_id = ?;";
		$prepared_statement = $connection->prepare($sql);
		$prepared_statement->bind_param('i', $agent_id);
		$prepared_statement->execute();
		$package_result = $prepared_statement->get_result();

		$bookings = [];

		while ($package_row = $package_result->fetch_assoc()) {
			$sql2 = "SELECT * FROM booking WHERE package_id = ?;";
			$stmt2 = $connection->prepare($sql2);
			$stmt2->bind_param('i', $package_row['package_id']);
			$stmt2->execute();
			$booking_result = $stmt2->get_result();

			while ($booking_row = $booking_result->fetch_assoc()) {
				$sql3 = "SELECT name FROM customer WHERE customer_id = ?;";
				$stmt3 = $connection->prepare($sql3);
				$stmt3->bind_param('i', $booking_row['customer_id']);
				$stmt3->execute();
				$customer_row = $stmt3->get_result()->fetch_assoc();

				$booking_row['customer_name'] = $customer_row['name'];
				$booking_row['package_name'] = $package_row['package_name'];
				$bookings[] = $booking_row;
			}
		}

		usort($bookings, function($a, $b) {
			return $b['booking_id'] - $a['booking_id'];
		});

		return $bookings;
	}

	function getBookingsForCustomer($customer_id) {
		$connection = $this->establishConnection();

		$sql = "SELECT * FROM booking WHERE customer_id = ?;";
		$prepared_statement = $connection->prepare($sql);
		$prepared_statement->bind_param('i', $customer_id);
		$prepared_statement->execute();
		$result = $prepared_statement->get_result();

		$bookings = [];

		while ($row = $result->fetch_assoc()) {
			$sql2 = "SELECT package_name FROM package WHERE package_id = ?;";
			$stmt2 = $connection->prepare($sql2);
			$stmt2->bind_param('i', $row['package_id']);
			$stmt2->execute();
			$package_row = $stmt2->get_result()->fetch_assoc();

			$row['package_name'] = $package_row['package_name'];
			$bookings[] = $row;
		}

		usort($bookings, function($a, $b) {
			return $b['booking_id'] - $a['booking_id'];
		});

		return $bookings;
	}

	function getBookingById($booking_id) {
		$sql = "SELECT * FROM booking WHERE booking_id = ?;";
		$connection = $this->establishConnection();
		$prepared_statement = $connection->prepare($sql);
		$prepared_statement->bind_param('i', $booking_id);
		$prepared_statement->execute();
		$result = $prepared_statement->get_result();
		return $result->fetch_assoc();
	}

	function deleteBooking($booking_id) {
		$connection = $this->establishConnection();

		$sql = "DELETE FROM companion WHERE booking_id = ?;";
		$prepared_statement = $connection->prepare($sql);
		$prepared_statement->bind_param('i', $booking_id);
		$prepared_statement->execute();

		$sql = "DELETE FROM booking WHERE booking_id = ?;";
		$prepared_statement = $connection->prepare($sql);
		$prepared_statement->bind_param('i', $booking_id);
		return $prepared_statement->execute();
	}

	function requestCancellation($booking_id) {
		$sql = "UPDATE booking SET cancellation_requested = 1 WHERE booking_id = ?;";
		$connection = $this->establishConnection();
		$prepared_statement = $connection->prepare($sql);
		$prepared_statement->bind_param('i', $booking_id);
		return $prepared_statement->execute();
	}

	function updateStatus($booking_id, $status) {
		$sql = "UPDATE booking SET status = ? WHERE booking_id = ?;";
		$connection = $this->establishConnection();
		$prepared_statement = $connection->prepare($sql);
		$prepared_statement->bind_param('si', $status, $booking_id);
		return $prepared_statement->execute();
	}
}
