<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'customer') {
	header("Location: ../../");
	exit();
}
include __DIR__ . '/../layout/header.php';
require_once __DIR__ . '/../../model/Package.php';
require_once __DIR__ . '/../../model/Review.php';

$package = new Package();
$data = $package->getPackageById($_GET['id']);

$review = new Review();
$reviews = $review->getReviewsForPackage($_GET['id']);
?>
<!DOCTYPE html>
<html lang='en'>
<head>
	<meta charset='utf-8'>
	<meta name='viewport' content='width=device-width, initial-scale=1'>
	<title><?php echo htmlspecialchars($data['package_name']); ?></title>
	<style>
:root {
	--color-bg: #eef8f0;
	--color-card: #ffffff;
	--color-border: #cfe4d1;
	--color-heading: #1e3a2c;
	--color-text: #2f4a3a;
	--color-primary: #4c8a67;
	--color-primary-dark: #3d6f53;
	--color-danger: #c1554e;
	--color-danger-dark: #a54540;
	--radius: 12px;
}

* {
	box-sizing: border-box;
}

body {
	background: var(--color-bg);
	font-family: Arial, Helvetica, sans-serif;
	color: var(--color-text);
	margin: 0;
	padding: 20px;
}

.card {
	background: var(--color-card);
	border: 1px solid var(--color-border);
	border-radius: var(--radius);
	padding: 32px;
	max-width: 480px;
	margin: 40px auto;
}

.card h1 {
	color: var(--color-heading);
	text-align: center;
	font-size: 32px;
	font-weight: 700;
	margin: 0 0 24px;
}

.page-heading {
	color: var(--color-heading);
	font-size: 28px;
	font-weight: 700;
	margin: 20px 0;
}

label {
	display: block;
	font-weight: 700;
	color: var(--color-heading);
	margin: 18px 0 6px;
}

input[type="text"],
input[type="email"],
input[type="password"],
input[type="tel"],
input[type="date"],
input[type="number"],
select,
textarea {
	width: 100%;
	padding: 12px 14px;
	border: 1px solid var(--color-border);
	border-radius: 8px;
	font-size: 15px;
	background: #fff;
	color: var(--color-text);
}

.btn {
	display: block;
	width: 100%;
	padding: 14px;
	border-radius: 8px;
	font-weight: 700;
	font-size: 16px;
	text-align: center;
	border: none;
	cursor: pointer;
	margin-top: 20px;
	text-decoration: none;
}

.btn-primary {
	background: var(--color-primary);
	color: #fff;
}

.btn-primary:hover {
	background: var(--color-primary-dark);
}

.btn-outline {
	background: #fff;
	border: 1.5px solid var(--color-primary);
	color: var(--color-primary);
}

.btn-outline:hover {
	background: #f3faf5;
}

.btn-danger {
	background: var(--color-danger);
	color: #fff;
}

.btn-danger:hover {
	background: var(--color-danger-dark);
}

.error-text {
	color: var(--color-danger);
	font-size: 13px;
	display: block;
	margin-top: 4px;
}

.success-text {
	color: var(--color-primary-dark);
	font-size: 14px;
	text-align: center;
	margin-bottom: 16px;
}

.info-text {
	color: var(--color-text);
	font-size: 14px;
	text-align: center;
	margin-bottom: 16px;
}

.link-center {
	display: block;
	text-align: center;
	margin-top: 16px;
	color: var(--color-primary);
	font-weight: 700;
	text-decoration: none;
}

.link-center:hover {
	text-decoration: underline;
}

table.data-table {
	width: 100%;
	border-collapse: collapse;
	background: var(--color-card);
	border: 1px solid var(--color-border);
	border-radius: var(--radius);
	overflow: hidden;
}

table.data-table th {
	background: #f3faf5;
	text-align: left;
	padding: 14px 18px;
	color: var(--color-heading);
	font-weight: 700;
}

table.data-table td {
	padding: 14px 18px;
	border-top: 1px solid var(--color-border);
}

.btn-sm {
	padding: 8px 18px;
	border-radius: 8px;
	font-weight: 700;
	font-size: 14px;
	border: none;
	cursor: pointer;
}

.btn-sm.accept {
	background: var(--color-primary);
	color: #fff;
}

.btn-sm.reject {
	background: var(--color-danger);
	color: #fff;
}

.btn-sm.edit {
	background: #fff;
	border: 1px solid var(--color-border);
	color: var(--color-heading);
}

.btn-sm.delete {
	background: #fff;
	border: 1px solid var(--color-danger);
	color: var(--color-danger);
}
	</style>
</head>
<body>
	<div style="display: flex; gap: 20px; flex-wrap: wrap;">
		<div class="card" style="flex: 1; min-width: 280px;">
			<h1><?php echo htmlspecialchars($data['package_name']); ?></h1>
			<p><strong>Destination</strong>: <?php echo htmlspecialchars($data['destination']); ?></p>
			<p><strong>Duration</strong>: <?php echo htmlspecialchars($data['duration']); ?></p>
			<p><strong>Price</strong>: <?php echo htmlspecialchars($data['price']); ?> BDT</p>
			<p><strong>Description:</strong></p>
			<p><?php echo nl2br(htmlspecialchars($data['description'])); ?></p>
		</div>

		<div class="card" style="flex: 1; min-width: 280px;">
			<h1>Book This Package</h1>

			<?php if (isset($_SESSION['booking_error'])): ?>
				<p class="error-text" style="text-align: center;"><?php echo htmlspecialchars($_SESSION['booking_error']); unset($_SESSION['booking_error']); ?></p>
			<?php endif; ?>

			<form action="../../controller/booking-create-handler.php" method="post" onsubmit="return validateBookingForm();">
				<input type="hidden" name="package_id" value="<?php echo $data['package_id']; ?>">

				<label for="travel_date">Travel Date</label>
				<input type="text" id="travel_date" name="travel_date" placeholder="dd/mm/yyyy">
				<span class="error-text" id="travel_date_error"></span>

				<label for="num_travelers">No. of Travelers</label>
				<input type="number" id="num_travelers" name="num_travelers" min="1" value="1">
				<span class="error-text" id="num_travelers_error"></span>

				<button type="submit" class="btn btn-primary">Book Now</button>
			</form>
		</div>
	</div>

	<div class="card" style="max-width: 100%; margin-top: 20px;">
		<h1>Reviews</h1>

		<?php if (isset($_SESSION['review_success'])): ?>
			<p class="success-text"><?php echo htmlspecialchars($_SESSION['review_success']); unset($_SESSION['review_success']); ?></p>
		<?php endif; ?>
		<?php if (isset($_SESSION['review_error'])): ?>
			<p class="error-text" style="text-align: center;"><?php echo htmlspecialchars($_SESSION['review_error']); unset($_SESSION['review_error']); ?></p>
		<?php endif; ?>

		<?php foreach ($reviews as $r): ?>
			<p><strong><?php echo htmlspecialchars($r['customer_name']); ?></strong> rated <?php echo htmlspecialchars($r['rating']); ?>/5</p>
			<p><?php echo htmlspecialchars($r['comment']); ?></p>
			<hr>
		<?php endforeach; ?>
		<?php if (empty($reviews)): ?>
			<p class="info-text">No reviews yet</p>
		<?php endif; ?>

		<form action="../../controller/review-add-handler.php" method="post" onsubmit="return validateReviewForm();">
			<input type="hidden" name="package_id" value="<?php echo $data['package_id']; ?>">

			<label for="rating">Rating (1 to 5)</label>
			<input type="number" id="rating" name="rating" min="1" max="5">
			<span class="error-text" id="rating_error"></span>

			<label for="comment">Comment</label>
			<textarea id="comment" name="comment" rows="3"></textarea>

			<button type="submit" class="btn btn-primary">Submit Review</button>
		</form>
	</div>

	<a href="./packages.php" class="btn btn-outline" style="display: block; text-align: center; text-decoration: none; max-width: 260px;">Back to Packages</a>

	<script>
		function validateBookingForm() {
			document.getElementById("travel_date_error").innerText = "";
			document.getElementById("num_travelers_error").innerText = "";

			var travelDate = document.getElementById("travel_date");
			if (travelDate.value == "") {
				document.getElementById("travel_date_error").innerText = "Pick a date";
				return false;
			}

			var datePattern = /^\d{2}\/\d{2}\/\d{4}$/;
			if (!datePattern.test(travelDate.value)) {
				document.getElementById("travel_date_error").innerText = "Use dd/mm/yyyy";
				return false;
			}

			var numTravelers = document.getElementById("num_travelers");
			if (numTravelers.value < 1) {
				document.getElementById("num_travelers_error").innerText = "Need at least 1 traveler";
				return false;
			}

			return true;
		}

		function validateReviewForm() {
			document.getElementById("rating_error").innerText = "";

			var rating = document.getElementById("rating");
			if (rating.value < 1 || rating.value > 5 || rating.value == "") {
				document.getElementById("rating_error").innerText = "Rating has to be 1-5";
				return false;
			}

			return true;
		}
	</script>
</body>
</html>
