<?php
require_once __DIR__ . '/../db/db_connection.php';

class Customer {
	function establishConnection() {
		$db_connection = new DBConnection();
		return $db_connection->connect();
	}

	function loginCheck($email, $password) {
		$sql = "SELECT * FROM customer WHERE email = ?;";
		$connection = $this->establishConnection();
		$prepared_statement = $connection->prepare($sql);
		$prepared_statement->bind_param('s', $email);
		$prepared_statement->execute();
		$result = $prepared_statement->get_result();

		if ($result->num_rows > 0) {
			$row = $result->fetch_assoc();
			if ($row['password'] == $password) {
				return $row;
			}
		}
		return null;
	}

	function registerUser($name, $email, $phone, $password, $profile_pic = null, $profile_pic_type = null) {
		$sql = "INSERT INTO customer (name, email, phone, password, profile_pic, profile_pic_type) VALUES (?, ?, ?, ?, ?, ?);";
		$connection = $this->establishConnection();
		$prepared_statement = $connection->prepare($sql);
		$prepared_statement->bind_param('ssssss', $name, $email, $phone, $password, $profile_pic, $profile_pic_type);
		if ($prepared_statement->execute()) {
			return $connection->insert_id;
		} else {
			return false;
		}
	}

	function emailExists($email) {
		$connection = $this->establishConnection();

		$sql = "SELECT agent_id FROM agent WHERE email = ?;";
		$stmt = $connection->prepare($sql);
		$stmt->bind_param('s', $email);
		$stmt->execute();
		if ($stmt->get_result()->num_rows > 0) {
			return true;
		}

		$sql = "SELECT customer_id FROM customer WHERE email = ?;";
		$stmt = $connection->prepare($sql);
		$stmt->bind_param('s', $email);
		$stmt->execute();
		if ($stmt->get_result()->num_rows > 0) {
			return true;
		}

		$sql = "SELECT coordinator_id FROM coordinator WHERE email = ?;";
		$stmt = $connection->prepare($sql);
		$stmt->bind_param('s', $email);
		$stmt->execute();
		if ($stmt->get_result()->num_rows > 0) {
			return true;
		}

		$sql = "SELECT finance_id FROM finance_manager WHERE email = ?;";
		$stmt = $connection->prepare($sql);
		$stmt->bind_param('s', $email);
		$stmt->execute();
		return $stmt->get_result()->num_rows > 0;
	}

	function getById($id) {
		$sql = "SELECT * FROM customer WHERE customer_id = ?;";
		$connection = $this->establishConnection();
		$prepared_statement = $connection->prepare($sql);
		$prepared_statement->bind_param('i', $id);
		$prepared_statement->execute();
		$result = $prepared_statement->get_result();
		return $result->fetch_assoc();
	}

	function updateProfile($id, $name, $phone) {
		$sql = "UPDATE customer SET name = ?, phone = ? WHERE customer_id = ?;";
		$connection = $this->establishConnection();
		$prepared_statement = $connection->prepare($sql);
		$prepared_statement->bind_param('ssi', $name, $phone, $id);
		return $prepared_statement->execute();
	}

	function updateProfilePic($id, $profile_pic, $profile_pic_type) {
		$sql = "UPDATE customer SET profile_pic = ?, profile_pic_type = ? WHERE customer_id = ?;";
		$connection = $this->establishConnection();
		$prepared_statement = $connection->prepare($sql);
		$prepared_statement->bind_param('ssi', $profile_pic, $profile_pic_type, $id);
		return $prepared_statement->execute();
	}

	function updatePassword($id, $hashed_password) {
		$sql = "UPDATE customer SET password = ? WHERE customer_id = ?;";
		$connection = $this->establishConnection();
		$prepared_statement = $connection->prepare($sql);
		$prepared_statement->bind_param('si', $hashed_password, $id);
		return $prepared_statement->execute();
	}

	function deleteById($id) {
		$sql = "DELETE FROM customer WHERE customer_id = ?;";
		$connection = $this->establishConnection();
		$prepared_statement = $connection->prepare($sql);
		$prepared_statement->bind_param('i', $id);
		return $prepared_statement->execute();
	}
}
