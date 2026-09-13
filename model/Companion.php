<?php
require_once __DIR__ . '/../db/db_connection.php';

class Companion {
	function establishConnection() {
		$db_connection = new DBConnection();
		return $db_connection->connect();
	}

	function addCompanion($booking_id, $name, $relation, $phone) {
		$sql = "INSERT INTO companion (booking_id, name, relation, phone) VALUES (?, ?, ?, ?);";
		$connection = $this->establishConnection();
		$prepared_statement = $connection->prepare($sql);
		$prepared_statement->bind_param('isss', $booking_id, $name, $relation, $phone);
		return $prepared_statement->execute();
	}

	function getCompanionsForBooking($booking_id) {
		$sql = "SELECT * FROM companion WHERE booking_id = ?;";
		$connection = $this->establishConnection();
		$prepared_statement = $connection->prepare($sql);
		$prepared_statement->bind_param('i', $booking_id);
		$prepared_statement->execute();
		$result = $prepared_statement->get_result();
		$companions = [];
		while ($row = $result->fetch_assoc()) {
			$companions[] = $row;
		}
		return $companions;
	}

	function countCompanionsForBooking($booking_id) {
		$sql = "SELECT COUNT(*) AS total FROM companion WHERE booking_id = ?;";
		$connection = $this->establishConnection();
		$prepared_statement = $connection->prepare($sql);
		$prepared_statement->bind_param('i', $booking_id);
		$prepared_statement->execute();
		$result = $prepared_statement->get_result();
		$row = $result->fetch_assoc();
		return $row['total'];
	}

	function deleteCompanion($traveler_id) {
		$sql = "DELETE FROM companion WHERE traveler_id = ?;";
		$connection = $this->establishConnection();
		$prepared_statement = $connection->prepare($sql);
		$prepared_statement->bind_param('i', $traveler_id);
		return $prepared_statement->execute();
	}
}
