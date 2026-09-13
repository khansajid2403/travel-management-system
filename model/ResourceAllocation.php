<?php
require_once __DIR__ . '/../db/db_connection.php';

class ResourceAllocation {
	function establishConnection() {
		$db_connection = new DBConnection();
		return $db_connection->connect();
	}

	function assign($booking_id, $hotel_id, $transport_id) {
		$sql = "INSERT INTO resource_allocation (booking_id, hotel_id, transport_id) VALUES (?, ?, ?);";
		$connection = $this->establishConnection();
		$prepared_statement = $connection->prepare($sql);
		$prepared_statement->bind_param('iii', $booking_id, $hotel_id, $transport_id);
		return $prepared_statement->execute();
	}

	function getAllocationForBooking($booking_id) {
		$connection = $this->establishConnection();

		$sql = "SELECT * FROM resource_allocation WHERE booking_id = ?;";
		$prepared_statement = $connection->prepare($sql);
		$prepared_statement->bind_param('i', $booking_id);
		$prepared_statement->execute();
		$row = $prepared_statement->get_result()->fetch_assoc();

		if (!$row) {
			return null;
		}

		$sql2 = "SELECT hotel_name FROM hotel WHERE hotel_id = ?;";
		$stmt2 = $connection->prepare($sql2);
		$stmt2->bind_param('i', $row['hotel_id']);
		$stmt2->execute();
		$hotel_row = $stmt2->get_result()->fetch_assoc();

		$sql3 = "SELECT type FROM transport WHERE transport_id = ?;";
		$stmt3 = $connection->prepare($sql3);
		$stmt3->bind_param('i', $row['transport_id']);
		$stmt3->execute();
		$transport_row = $stmt3->get_result()->fetch_assoc();

		$row['hotel_name'] = $hotel_row['hotel_name'];
		$row['transport_type'] = $transport_row['type'];

		return $row;
	}

	function getUnassignedAcceptedBookings() {
		$connection = $this->establishConnection();

		$sql = "SELECT * FROM booking WHERE status = 'accepted';";
		$result = $connection->query($sql);

		$bookings = [];

		while ($row = $result->fetch_assoc()) {
			$sql2 = "SELECT allocation_id FROM resource_allocation WHERE booking_id = ?;";
			$stmt2 = $connection->prepare($sql2);
			$stmt2->bind_param('i', $row['booking_id']);
			$stmt2->execute();
			$allocation_result = $stmt2->get_result();

			if ($allocation_result->num_rows > 0) {
				continue;
			}

			$sql3 = "SELECT name FROM customer WHERE customer_id = ?;";
			$stmt3 = $connection->prepare($sql3);
			$stmt3->bind_param('i', $row['customer_id']);
			$stmt3->execute();
			$customer_row = $stmt3->get_result()->fetch_assoc();

			$sql4 = "SELECT package_name FROM package WHERE package_id = ?;";
			$stmt4 = $connection->prepare($sql4);
			$stmt4->bind_param('i', $row['package_id']);
			$stmt4->execute();
			$package_row = $stmt4->get_result()->fetch_assoc();

			$row['customer_name'] = $customer_row['name'];
			$row['package_name'] = $package_row['package_name'];
			$bookings[] = $row;
		}

		return $bookings;
	}

	function getAssignedBookings($coordinator_id) {
		$connection = $this->establishConnection();

		$sql = "SELECT hotel_id, hotel_name FROM hotel WHERE coordinator_id = ?;";
		$prepared_statement = $connection->prepare($sql);
		$prepared_statement->bind_param('i', $coordinator_id);
		$prepared_statement->execute();
		$hotel_result = $prepared_statement->get_result();

		$bookings = [];

		while ($hotel_row = $hotel_result->fetch_assoc()) {
			$sql2 = "SELECT * FROM resource_allocation WHERE hotel_id = ?;";
			$stmt2 = $connection->prepare($sql2);
			$stmt2->bind_param('i', $hotel_row['hotel_id']);
			$stmt2->execute();
			$allocation_result = $stmt2->get_result();

			while ($allocation_row = $allocation_result->fetch_assoc()) {
				$sql3 = "SELECT * FROM booking WHERE booking_id = ?;";
				$stmt3 = $connection->prepare($sql3);
				$stmt3->bind_param('i', $allocation_row['booking_id']);
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

				$sql6 = "SELECT type FROM transport WHERE transport_id = ?;";
				$stmt6 = $connection->prepare($sql6);
				$stmt6->bind_param('i', $allocation_row['transport_id']);
				$stmt6->execute();
				$transport_row = $stmt6->get_result()->fetch_assoc();

				$bookings[] = [
					'booking_id' => $booking_row['booking_id'],
					'travel_date' => $booking_row['travel_date'],
					'customer_name' => $customer_row['name'],
					'package_name' => $package_row['package_name'],
					'hotel_name' => $hotel_row['hotel_name'],
					'transport_type' => $transport_row['type']
				];
			}
		}

		return $bookings;
	}
}
