<?php
require_once __DIR__ . '/../db/db_connection.php';

class Review {
	function establishConnection() {
		$db_connection = new DBConnection();
		return $db_connection->connect();
	}

	function addReview($customer_id, $package_id, $rating, $comment) {
		$sql = "INSERT INTO review (customer_id, package_id, rating, comment) VALUES (?, ?, ?, ?);";
		$connection = $this->establishConnection();
		$prepared_statement = $connection->prepare($sql);
		$prepared_statement->bind_param('iiis', $customer_id, $package_id, $rating, $comment);
		return $prepared_statement->execute();
	}

	function getReviewsForPackage($package_id) {
		$connection = $this->establishConnection();

		$sql = "SELECT * FROM review WHERE package_id = ?;";
		$prepared_statement = $connection->prepare($sql);
		$prepared_statement->bind_param('i', $package_id);
		$prepared_statement->execute();
		$result = $prepared_statement->get_result();

		$reviews = [];

		while ($row = $result->fetch_assoc()) {
			$sql2 = "SELECT name FROM customer WHERE customer_id = ?;";
			$stmt2 = $connection->prepare($sql2);
			$stmt2->bind_param('i', $row['customer_id']);
			$stmt2->execute();
			$customer_row = $stmt2->get_result()->fetch_assoc();

			$row['customer_name'] = $customer_row['name'];
			$reviews[] = $row;
		}

		usort($reviews, function($a, $b) {
			return $b['review_id'] - $a['review_id'];
		});

		return $reviews;
	}
}
