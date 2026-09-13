<?php
session_start();
require_once __DIR__ . '/../model/Package.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'agent') {
	header("Location: ../");
	exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
	$package_id = $_POST['package_id'];
	$package = new Package();
	$package->deletePackage($package_id);
	$_SESSION['package_success'] = 'Package deleted';
}

header("Location: ../view/agent/packages.php");
