<?php
require_once __DIR__ . '/../db/db_connection.php';

class Transport {
	function establishConnection() {
		$db_connection = new DBConnection();
		return $db_connection->connect();
	}

	function createTransport($coordinator_id, $type, $capacity, $provider) {
		$sql = "INSERT INTO transport (coordinator_id, type, capacity, provider) VALUES (?, ?, ?, ?);";
		$connection = $this->establishConnection();
		$prepared_statement = $connection->prepare($sql);
		$prepared_statement->bind_param('isis', $coordinator_id, $type, $capacity, $provider);
		return $prepared_statement->execute();
	}

	function getTransportByCoordinator($coordinator_id) {
		$sql = "SELECT * FROM transport WHERE coordinator_id = ?;";
		$connection = $this->establishConnection();
		$prepared_statement = $connection->prepare($sql);
		$prepared_statement->bind_param('i', $coordinator_id);
		$prepared_statement->execute();
		$result = $prepared_statement->get_result();
		$transport = [];
		while ($row = $result->fetch_assoc()) {
			$transport[] = $row;
		}
		return $transport;
	}

	function getTransportById($transport_id) {
		$sql = "SELECT * FROM transport WHERE transport_id = ?;";
		$connection = $this->establishConnection();
		$prepared_statement = $connection->prepare($sql);
		$prepared_statement->bind_param('i', $transport_id);
		$prepared_statement->execute();
		$result = $prepared_statement->get_result();
		return $result->fetch_assoc();
	}

	function updateTransport($transport_id, $type, $capacity, $provider) {
		$sql = "UPDATE transport SET type = ?, capacity = ?, provider = ? WHERE transport_id = ?;";
		$connection = $this->establishConnection();
		$prepared_statement = $connection->prepare($sql);
		$prepared_statement->bind_param('sisi', $type, $capacity, $provider, $transport_id);
		return $prepared_statement->execute();
	}

	function deleteTransport($transport_id) {
		$sql = "DELETE FROM transport WHERE transport_id = ?;";
		$connection = $this->establishConnection();
		$prepared_statement = $connection->prepare($sql);
		$prepared_statement->bind_param('i', $transport_id);
		return $prepared_statement->execute();
	}
}
