<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'customer') {
	header("Location: ../../");
	exit();
}
include __DIR__ . '/../layout/header.php';
require_once __DIR__ . '/../../model/Booking.php';
require_once __DIR__ . '/../../model/Companion.php';

$booking = new Booking();
$all_bookings = $booking->getBookingsForCustomer($_SESSION['user_id']);

$companion = new Companion();

$pending_bookings = [];
$past_bookings = [];

foreach ($all_bookings as $b) {
	if ($b['status'] == 'pending') {
		$pending_bookings[] = $b;
	} else {
		$past_bookings[] = $b;
	}
}
?>
<!DOCTYPE html>
<html lang='en'>
<head>
	<meta charset='utf-8'>
	<meta name='viewport' content='width=device-width, initial-scale=1'>
	<title>My Bookings</title>
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
	<h1 class="page-heading">My Bookings</h1>

	<?php if (isset($_SESSION['companion_error'])): ?>
		<p class="error-text"><?php echo htmlspecialchars($_SESSION['companion_error']); unset($_SESSION['companion_error']); ?></p>
	<?php endif; ?>

	<?php foreach ($pending_bookings as $b): ?>
	<?php
		$companions = $companion->getCompanionsForBooking($b['booking_id']);
		$seats_for_companions = $b['num_travelers'] - 1;
		$seats_left = $seats_for_companions - count($companions);
	?>
	<div class="card" style="max-width: 100%;">
		<h1 style="font-size: 20px; text-align: left;"><?php echo htmlspecialchars($b['package_name']); ?></h1>
		<p>Travel Date: <?php echo date('d/m/Y', strtotime($b['travel_date'])); ?></p>
		<p>Travelers: <?php echo htmlspecialchars($b['num_travelers']); ?></p>
		<p>Status: <?php echo htmlspecialchars($b['status']); ?></p>

		<p><strong>Companions</strong></p>
		<?php foreach ($companions as $c): ?>
			<p>
				<?php echo htmlspecialchars($c['name']); ?> (<?php echo htmlspecialchars($c['relation']); ?>) - <?php echo htmlspecialchars($c['phone']); ?>
				<form action="../../controller/companion-delete-handler.php" method="post" style="display: inline;">
					<input type="hidden" name="traveler_id" value="<?php echo $c['traveler_id']; ?>">
					<input type="hidden" name="booking_id" value="<?php echo $b['booking_id']; ?>">
					<button type="submit" class="btn-sm delete">Remove</button>
				</form>
			</p>
		<?php endforeach; ?>
		<?php if (empty($companions)): ?>
			<p class="info-text">No companions added yet</p>
		<?php endif; ?>

		<?php if ($seats_left > 0): ?>
		<form action="../../controller/companion-add-handler.php" method="post" onsubmit="return validateCompanionForm(<?php echo $b['booking_id']; ?>);">
			<input type="hidden" name="booking_id" value="<?php echo $b['booking_id']; ?>">
			<label for="name_<?php echo $b['booking_id']; ?>">Companion Name</label>
			<input type="text" id="name_<?php echo $b['booking_id']; ?>" name="name">
			<span class="error-text" id="name_error_<?php echo $b['booking_id']; ?>"></span>
			<label for="relation_<?php echo $b['booking_id']; ?>">Relation</label>
			<input type="text" id="relation_<?php echo $b['booking_id']; ?>" name="relation">
			<label for="phone_<?php echo $b['booking_id']; ?>">Phone</label>
			<input type="text" id="phone_<?php echo $b['booking_id']; ?>" name="phone">
			<span class="error-text" id="phone_error_<?php echo $b['booking_id']; ?>"></span>
			<button type="submit" class="btn btn-outline">Add Companion (<?php echo $seats_left; ?> left)</button>
		</form>
		<?php else: ?>
			<p class="info-text">All <?php echo htmlspecialchars($b['num_travelers']); ?> traveler seat(s) are filled</p>
		<?php endif; ?>

		<form action="../../controller/booking-cancel-handler.php" method="post">
			<input type="hidden" name="booking_id" value="<?php echo $b['booking_id']; ?>">
			<button type="submit" class="btn btn-danger">Cancel Booking</button>
		</form>
	</div>
	<?php endforeach; ?>

	<?php if (empty($pending_bookings)): ?>
		<p class="info-text">You have no pending bookings</p>
	<?php endif; ?>

	<?php if (!empty($past_bookings)): ?>
	<h1 class="page-heading">Booking History</h1>

	<table class="data-table">
		<tr>
			<th>Package</th>
			<th>Travel Date</th>
			<th>Travelers</th>
			<th>Status</th>
			<th>Actions</th>
		</tr>
		<?php foreach ($past_bookings as $b): ?>
		<tr>
			<td><?php echo htmlspecialchars($b['package_name']); ?></td>
			<td><?php echo date('d/m/Y', strtotime($b['travel_date'])); ?></td>
			<td><?php echo htmlspecialchars($b['num_travelers']); ?></td>
			<td><?php echo htmlspecialchars($b['status']); ?></td>
			<td>
				<?php if ($b['status'] == 'accepted'): ?>
					<a href="./package-detail.php?id=<?php echo $b['package_id']; ?>" class="btn-sm accept" style="text-decoration: none;">Leave a Review</a>
				<?php endif; ?>
			</td>
		</tr>
		<?php endforeach; ?>
	</table>
	<?php endif; ?>

	<a href="../../" class="btn btn-outline" style="display: block; text-align: center; text-decoration: none; max-width: 260px;">Back to Dashboard</a>

	<script>
		function validateCompanionForm(bookingId) {
			var nameInput = document.getElementById("name_" + bookingId);
			var errorSpan = document.getElementById("name_error_" + bookingId);
			errorSpan.innerText = "";

			var phoneInput = document.getElementById("phone_" + bookingId);
			var phoneErrorSpan = document.getElementById("phone_error_" + bookingId);
			phoneErrorSpan.innerText = "";

			if (nameInput.value.length < 2) {
				errorSpan.innerText = "Enter a name";
				return false;
			}

			var phonePattern = /^01[0-9]{9}$/;
			if (!phonePattern.test(phoneInput.value)) {
				phoneErrorSpan.innerText = "Phone number must start with 01 and be 11 digits";
				return false;
			}

			return true;
		}
	</script>
</body>
</html>
