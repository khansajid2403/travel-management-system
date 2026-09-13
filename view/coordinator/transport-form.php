<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'coordinator') {
	header("Location: ../../");
	exit();
}
include __DIR__ . '/../layout/header.php';
require_once __DIR__ . '/../../model/Transport.php';

$transport = new Transport();
$editing = false;
$data = ['transport_id' => '', 'type' => '', 'capacity' => '', 'provider' => ''];

if (isset($_GET['id'])) {
	$existing = $transport->getTransportById($_GET['id']);
	if ($existing) {
		$editing = true;
		$data = $existing;
	}
}
?>
<!DOCTYPE html>
<html lang='en'>
<head>
	<meta charset='utf-8'>
	<meta name='viewport' content='width=device-width, initial-scale=1'>
	<title><?php echo $editing ? 'Edit Transport' : 'Add Transport'; ?></title>
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
	<div class="card">
		<h1><?php echo $editing ? 'Edit Transport' : 'Add Transport'; ?></h1>

		<?php if (isset($_SESSION['transport_error'])): ?>
			<p class="error-text" style="text-align: center;"><?php echo htmlspecialchars($_SESSION['transport_error']); unset($_SESSION['transport_error']); ?></p>
		<?php endif; ?>

		<form action="../../controller/transport-<?php echo $editing ? 'update' : 'create'; ?>-handler.php" method="post" onsubmit="return validateTransportForm();">
			<?php if ($editing): ?>
				<input type="hidden" name="transport_id" value="<?php echo $data['transport_id']; ?>">
			<?php endif; ?>

			<label for="type">Type</label>
			<input type="text" id="type" name="type" placeholder="e.g. Bus, Car" value="<?php echo htmlspecialchars($data['type']); ?>">
			<span class="error-text" id="type_error"></span>

			<label for="capacity">Capacity</label>
			<input type="number" id="capacity" name="capacity" min="1" value="<?php echo htmlspecialchars($data['capacity']); ?>">
			<span class="error-text" id="capacity_error"></span>

			<label for="provider">Provider</label>
			<input type="text" id="provider" name="provider" value="<?php echo htmlspecialchars($data['provider']); ?>">

			<button type="submit" class="btn btn-primary"><?php echo $editing ? 'Save Changes' : 'Add Transport'; ?></button>
		</form>

		<a href="./transport.php" class="btn btn-outline" style="display: block; text-align: center; text-decoration: none;">Back to Transport</a>
	</div>

	<script>
		function validateTransportForm() {
			document.getElementById("type_error").innerText = "";
			document.getElementById("capacity_error").innerText = "";

			var typeInput = document.getElementById("type");
			if (typeInput.value.length < 2) {
				document.getElementById("type_error").innerText = "Enter a type";
				return false;
			}

			var capacityInput = document.getElementById("capacity");
			if (capacityInput.value < 1) {
				document.getElementById("capacity_error").innerText = "Needs at least 1 seat";
				return false;
			}

			return true;
		}
	</script>
</body>
</html>
