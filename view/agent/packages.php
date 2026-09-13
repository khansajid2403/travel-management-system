<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'agent') {
	header("Location: ../../");
	exit();
}
include __DIR__ . '/../layout/header.php';
require_once __DIR__ . '/../../model/Package.php';

$package = new Package();
$packages = $package->getPackagesByAgent($_SESSION['user_id']);
?>
<!DOCTYPE html>
<html lang='en'>
<head>
	<meta charset='utf-8'>
	<meta name='viewport' content='width=device-width, initial-scale=1'>
	<title>My Packages</title>
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
	<h1 class="page-heading">My Packages</h1>

	<?php if (isset($_SESSION['package_success'])): ?>
		<p class="success-text"><?php echo htmlspecialchars($_SESSION['package_success']); unset($_SESSION['package_success']); ?></p>
	<?php endif; ?>

	<a href="./package-form.php" class="btn btn-primary" style="max-width: 220px; display: inline-block; text-decoration: none; text-align: center;">Create New Package</a>

	<table class="data-table" style="margin-top: 20px;">
		<tr>
			<th>Package Name</th>
			<th>Destination</th>
			<th>Price</th>
			<th>Duration</th>
			<th>Actions</th>
		</tr>
		<?php foreach ($packages as $p): ?>
		<tr>
			<td><?php echo htmlspecialchars($p['package_name']); ?></td>
			<td><?php echo htmlspecialchars($p['destination']); ?></td>
			<td><?php echo htmlspecialchars($p['price']); ?> BDT</td>
			<td><?php echo htmlspecialchars($p['duration']); ?></td>
			<td>
				<a href="./package-form.php?id=<?php echo $p['package_id']; ?>" class="btn-sm edit" style="text-decoration: none;">Edit</a>
				<form action="../../controller/package-delete-handler.php" method="post" style="display: inline;">
					<input type="hidden" name="package_id" value="<?php echo $p['package_id']; ?>">
					<button type="submit" class="btn-sm delete">Delete</button>
				</form>
			</td>
		</tr>
		<?php endforeach; ?>
	</table>

	<a href="../../" class="btn btn-outline" style="display: block; text-align: center; text-decoration: none; max-width: 260px;">Back to Dashboard</a>
</body>
</html>
