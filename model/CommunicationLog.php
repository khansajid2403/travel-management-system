<?php
require_once __DIR__ . '/../db/db_connection.php';

class CommunicationLog {
	function establishConnection() {
		$db_connection = new DBConnection();
		return $db_connection->connect();
	}

	function sendMessage($agent_id, $customer_id, $sender_role, $message) {
		$sql = "INSERT INTO communication_log (agent_id, customer_id, sender_role, message) VALUES (?, ?, ?, ?);";
		$connection = $this->establishConnection();
		$prepared_statement = $connection->prepare($sql);
		$prepared_statement->bind_param('iiss', $agent_id, $customer_id, $sender_role, $message);
		return $prepared_statement->execute();
	}

	function getConversation($agent_id, $customer_id) {
		$sql = "SELECT * FROM communication_log WHERE agent_id = ? AND customer_id = ? ORDER BY log_date ASC;";
		$connection = $this->establishConnection();
		$prepared_statement = $connection->prepare($sql);
		$prepared_statement->bind_param('ii', $agent_id, $customer_id);
		$prepared_statement->execute();
		$result = $prepared_statement->get_result();
		$messages = [];
		while ($row = $result->fetch_assoc()) {
			$messages[] = $row;
		}
		return $messages;
	}

	function getCustomersForAgent($agent_id) {
		$connection = $this->establishConnection();

		$sql = "SELECT package_id FROM package WHERE agent_id = ?;";
		$prepared_statement = $connection->prepare($sql);
		$prepared_statement->bind_param('i', $agent_id);
		$prepared_statement->execute();
		$package_result = $prepared_statement->get_result();

		$customers = [];
		$seen_ids = [];

		while ($package_row = $package_result->fetch_assoc()) {
			$sql2 = "SELECT DISTINCT customer_id FROM booking WHERE package_id = ?;";
			$stmt2 = $connection->prepare($sql2);
			$stmt2->bind_param('i', $package_row['package_id']);
			$stmt2->execute();
			$booking_result = $stmt2->get_result();

			while ($booking_row = $booking_result->fetch_assoc()) {
				$customer_id = $booking_row['customer_id'];

				if (in_array($customer_id, $seen_ids)) {
					continue;
				}
				$seen_ids[] = $customer_id;

				$sql3 = "SELECT customer_id, name FROM customer WHERE customer_id = ?;";
				$stmt3 = $connection->prepare($sql3);
				$stmt3->bind_param('i', $customer_id);
				$stmt3->execute();
				$customers[] = $stmt3->get_result()->fetch_assoc();
			}
		}

		return $customers;
	}

	function getAgentsForCustomer($customer_id) {
		$connection = $this->establishConnection();

		$sql = "SELECT DISTINCT package_id FROM booking WHERE customer_id = ?;";
		$prepared_statement = $connection->prepare($sql);
		$prepared_statement->bind_param('i', $customer_id);
		$prepared_statement->execute();
		$booking_result = $prepared_statement->get_result();

		$agents = [];
		$seen_ids = [];

		while ($booking_row = $booking_result->fetch_assoc()) {
			$sql2 = "SELECT agent_id FROM package WHERE package_id = ?;";
			$stmt2 = $connection->prepare($sql2);
			$stmt2->bind_param('i', $booking_row['package_id']);
			$stmt2->execute();
			$package_row = $stmt2->get_result()->fetch_assoc();

			$agent_id = $package_row['agent_id'];

			if (in_array($agent_id, $seen_ids)) {
				continue;
			}
			$seen_ids[] = $agent_id;

			$sql3 = "SELECT agent_id, name FROM agent WHERE agent_id = ?;";
			$stmt3 = $connection->prepare($sql3);
			$stmt3->bind_param('i', $agent_id);
			$stmt3->execute();
			$agents[] = $stmt3->get_result()->fetch_assoc();
		}

		return $agents;
	}
}
