<?php
session_start();
require_once __DIR__ . '/model/Agent.php';
require_once __DIR__ . '/model/Customer.php';
require_once __DIR__ . '/model/Coordinator.php';
require_once __DIR__ . '/model/FinanceManager.php';

if (isset($_SESSION['role'])) {

	if (isset($_GET['target']) && $_GET['target'] == 'profile') {

		$models = [
			'agent' => new Agent(),
			'customer' => new Customer(),
			'coordinator' => new Coordinator(),
			'finance_manager' => new FinanceManager()
		];
		$_SESSION['profile_data'] = $models[$_SESSION['role']]->getById($_SESSION['user_id']);
		require_once __DIR__ . '/view/profile.php';

	} else if (isset($_GET['target']) && $_GET['target'] == 'change-password') {

		require_once __DIR__ . '/view/change-password.php';

	} else {

		switch ($_SESSION['role']) {
			case 'agent':
				require_once __DIR__ . '/view/agent/dashboard.php';
				break;
			default:
				echo "This copy of the project only includes the Travel Agent role.";
				break;
		}
	}

} else {
	require_once __DIR__ . '/view/login.php';
}
