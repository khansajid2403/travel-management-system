<?php
require_once __DIR__ . '/../db/db_connection.php';

class Hotel {
	function establishConnection() {
		$db_connection = new DBConnection();
		return $db_connection->connect();
	}

	function createHotel($coordinator_id, $hotel_name, $location, $rooms) {
		$sql = "INSERT INTO hotel (coordinator_id, hotel_name, location, rooms) VALUES (?, ?, ?, ?);";
		$connection = $this->establishConnection();
		$prepared_statement = $connection->prepare($sql);
		$prepared_statement->bind_param('issi', $coordinator_id, $hotel_name, $location, $rooms);
		return $prepared_statement->execute();
	}

	function getHotelsByCoordinator($coordinator_id) {
		$sql = "SELECT * FROM hotel WHERE coordinator_id = ?;";
		$connection = $this->establishConnection();
		$prepared_statement = $connection->prepare($sql);
		$prepared_statement->bind_param('i', $coordinator_id);
		$prepared_statement->execute();
		$result = $prepared_statement->get_result();
		$hotels = [];
		while ($row = $result->fetch_assoc()) {
			$hotels[] = $row;
		}
		return $hotels;
	}

	function getHotelById($hotel_id) {
		$sql = "SELECT * FROM hotel WHERE hotel_id = ?;";
		$connection = $this->establishConnection();
		$prepared_statement = $connection->prepare($sql);
		$prepared_statement->bind_param('i', $hotel_id);
		$prepared_statement->execute();
		$result = $prepared_statement->get_result();
		return $result->fetch_assoc();
	}

	function updateHotel($hotel_id, $hotel_name, $location, $rooms) {
		$sql = "UPDATE hotel SET hotel_name = ?, location = ?, rooms = ? WHERE hotel_id = ?;";
		$connection = $this->establishConnection();
		$prepared_statement = $connection->prepare($sql);
		$prepared_statement->bind_param('ssii', $hotel_name, $location, $rooms, $hotel_id);
		return $prepared_statement->execute();
	}

	function deleteHotel($hotel_id) {
		$sql = "DELETE FROM hotel WHERE hotel_id = ?;";
		$connection = $this->establishConnection();
		$prepared_statement = $connection->prepare($sql);
		$prepared_statement->bind_param('i', $hotel_id);
		return $prepared_statement->execute();
	}
}
