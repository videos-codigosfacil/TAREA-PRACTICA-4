<?php
$servidor = "sql103.infinityfree.com";
$usuario = "if0_43316380";
$contrasena = "gO42jHyswGV";
$base_datos = "if0_43316380_lista_tareas";

$conexion = new mysqli(
    $servidor,
    $usuario,
    $contrasena,
    $base_datos
);

if ($conexion->connect_error) {
    die("Error de conexión: " . $conexion->connect_error);
}

$conexion->set_charset("utf8mb4");

session_start();

?>
