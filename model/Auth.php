<?php
//Incluímos inicialmente la conexión a la base de datos
require "../config/conexion.php";

class User
{
	//Implementamos nuestro constructor
	public function __construct()
	{
	}

	public function login($username, $password)
	{
		$sql = "SELECT id,nombre FROM usuarios WHERE usuario='$username' AND clave='$password' ";
		return ejecutarConsulta($sql);
	}
}