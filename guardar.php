<?php

$conexion = mysqli_connect("localhost","root","","zoo");

if(!$conexion){
    die("Error de conexión");
}

$nombre = $_POST['nombre'];
$correo = $_POST['correo'];
$mensaje = $_POST['mensaje'];

$sql = "INSERT INTO contactos(nombre, correo, mensaje)
VALUES('$nombre', '$correo', '$mensaje')";

mysqli_query($conexion, $sql);

echo "Datos guardados correctamente";

?>