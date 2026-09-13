<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'agent') {
	header("Location: ../../");
	exit();
}
include __DIR__ . '/../layout/header.php';
require_once __DIR__ . '/../../model/CommunicationLog.php';

$log = new CommunicationLog();
$customers = $log->getCustomersForAgent($_SESSION['user_id']);

$selected_customer_id = isset($_GET['with']) ? $_GET['with'] : null;
$conversation = [];
if ($selected_customer_id) {
	$conversation = $log->getConversation($_SESSION['user_id'], $selected_customer_id);
}
?>
<!DOCTYPE html>
<html lang='en'>
<head>
	<meta charset='utf-8'>
	<meta name='viewport' content='width=device-width, initial-scale=1'>
	<title>Messages</title>
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
	<h1 class="page-heading">Messages</h1>

	<label for="customer_select">Choose a customer</label>
	<select id="customer_select" onchange="window.location.href='./messages.php?with=' + this.value;">
		<option value="">-- Select Customer --</option>
		<?php foreach ($customers as $c): ?>
		<option value="<?php echo $c['customer_id']; ?>" <?php echo ($selected_customer_id == $c['customer_id']) ? 'selected' : ''; ?>>
			<?php echo htmlspecialchars($c['name']); ?>
		</option>
		<?php endforeach; ?>
	</select>

	<?php if ($selected_customer_id): ?>
		<div class="card" style="max-width: 600px;">
			<?php foreach ($conversation as $msg): ?>
				<p><strong><?php echo $msg['sender_role'] == 'agent' ? 'You' : 'Customer'; ?>:</strong> <?php echo htmlspecialchars($msg['message']); ?></p>
			<?php endforeach; ?>
			<?php if (empty($conversation)): ?>
				<p class="info-text">No messages yet</p>
			<?php endif; ?>

			<form action="../../controller/message-send-handler.php" method="post">
				<input type="hidden" name="agent_id" value="<?php echo $_SESSION['user_id']; ?>">
				<input type="hidden" name="customer_id" value="<?php echo $selected_customer_id; ?>">
				<label for="message">Message</label>
				<textarea id="message" name="message" rows="3"></textarea>
				<button type="submit" class="btn btn-primary">Send</button>
			</form>
		</div>
	<?php endif; ?>

	<a href="../../" class="btn btn-outline" style="display: block; text-align: center; text-decoration: none; max-width: 260px;">Back to Dashboard</a>
</body>
</html>
