<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'agent') {
	header("Location: ../../");
	exit();
}
include __DIR__ . '/../layout/header.php';
require_once __DIR__ . '/../../model/Package.php';

$package = new Package();
$editing = false;
$data = ['package_id' => '', 'package_name' => '', 'destination' => '', 'price' => '', 'duration' => '', 'description' => ''];

if (isset($_GET['id'])) {
	$existing = $package->getPackageById($_GET['id']);
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
	<title><?php echo $editing ? 'Edit Package' : 'Create Package'; ?></title>
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
		<h1><?php echo $editing ? 'Edit Package' : 'Create Package'; ?></h1>

		<?php if (isset($_SESSION['package_error'])): ?>
			<p class="error-text" style="text-align: center;"><?php echo htmlspecialchars($_SESSION['package_error']); unset($_SESSION['package_error']); ?></p>
		<?php endif; ?>

		<form action="../../controller/package-<?php echo $editing ? 'update' : 'create'; ?>-handler.php" method="post">
			<?php if ($editing): ?>
				<input type="hidden" name="package_id" value="<?php echo $data['package_id']; ?>">
			<?php endif; ?>

			<label for="package_name">Package Name</label>
			<input type="text" id="package_name" name="package_name" value="<?php echo htmlspecialchars($data['package_name']); ?>">

			<label for="destination">Destination</label>
			<input type="text" id="destination" name="destination" value="<?php echo htmlspecialchars($data['destination']); ?>">

			<label for="price">Price (BDT)</label>
			<input type="text" id="price" name="price" value="<?php echo htmlspecialchars($data['price']); ?>">

			<label for="duration">Duration</label>
			<input type="text" id="duration" name="duration" placeholder="e.g. 4 Days" value="<?php echo htmlspecialchars($data['duration']); ?>">

			<label for="description">Description</label>
			<textarea id="description" name="description" rows="4"><?php echo htmlspecialchars($data['description']); ?></textarea>

			<button type="submit" class="btn btn-primary"><?php echo $editing ? 'Save Changes' : 'Create Package'; ?></button>
		</form>

		<a href="./packages.php" class="btn btn-outline" style="display: block; text-align: center; text-decoration: none; max-width: 260px;">Back to My Packages</a>
	</div>
</body>
</html>
