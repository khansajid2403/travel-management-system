<?php
require_once __DIR__ . '/../db/db_connection.php';

class Package {
	function establishConnection() {
		$db_connection = new DBConnection();
		return $db_connection->connect();
	}

	function createPackage($agent_id, $package_name, $destination, $price, $duration, $description) {
		$sql = "INSERT INTO package (agent_id, package_name, destination, price, duration, description) VALUES (?, ?, ?, ?, ?, ?);";
		$connection = $this->establishConnection();
		$prepared_statement = $connection->prepare($sql);
		$prepared_statement->bind_param('issdss', $agent_id, $package_name, $destination, $price, $duration, $description);
		return $prepared_statement->execute();
	}

	function getPackagesByAgent($agent_id) {
		$sql = "SELECT * FROM package WHERE agent_id = ?;";
		$connection = $this->establishConnection();
		$prepared_statement = $connection->prepare($sql);
		$prepared_statement->bind_param('i', $agent_id);
		$prepared_statement->execute();
		$result = $prepared_statement->get_result();
		$packages = [];
		while ($row = $result->fetch_assoc()) {
			$packages[] = $row;
		}
		return $packages;
	}

	function getAllPackages() {
		$sql = "SELECT * FROM package;";
		$connection = $this->establishConnection();
		$result = $connection->query($sql);
		$packages = [];
		while ($row = $result->fetch_assoc()) {
			$packages[] = $row;
		}
		return $packages;
	}

	function getPackageById($package_id) {
		$sql = "SELECT * FROM package WHERE package_id = ?;";
		$connection = $this->establishConnection();
		$prepared_statement = $connection->prepare($sql);
		$prepared_statement->bind_param('i', $package_id);
		$prepared_statement->execute();
		$result = $prepared_statement->get_result();
		return $result->fetch_assoc();
	}

	function updatePackage($package_id, $package_name, $destination, $price, $duration, $description) {
		$sql = "UPDATE package SET package_name = ?, destination = ?, price = ?, duration = ?, description = ? WHERE package_id = ?;";
		$connection = $this->establishConnection();
		$prepared_statement = $connection->prepare($sql);
		$prepared_statement->bind_param('ssdssi', $package_name, $destination, $price, $duration, $description, $package_id);
		return $prepared_statement->execute();
	}

	function deletePackage($package_id) {
		$sql = "DELETE FROM package WHERE package_id = ?;";
		$connection = $this->establishConnection();
		$prepared_statement = $connection->prepare($sql);
		$prepared_statement->bind_param('i', $package_id);
		return $prepared_statement->execute();
	}
}
