<?php
require_once "../model/Auth.php";
$user = new User();
switch ($_GET["op"]) {
	case 'login':
		$username = $_POST['username'];
		$password = $_POST['password'];
		$clavehash = md5($password);
		$rspta = $user->login($username, $clavehash);
		$fetch = $rspta->fetch_object();
		if (isset($fetch)) {
			$_SESSION['id'] = $fetch->id;
			$_SESSION['nombre'] = $fetch->nombre;
		}
		echo json_encode($fetch);
		break;

	case 'exit':
		session_unset();
		session_destroy();
		header("Location: ../index.php");
		break;
		exit;
}
