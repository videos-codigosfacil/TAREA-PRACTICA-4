<?php
require_once "conexion.php";

if (isset($_SESSION["id_usuario"])) {
    header("Location: index.php");
    exit;
}

$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $usuario = trim($_POST["usuario"] ?? "");
    $contrasena = $_POST["contrasena"] ?? "";

    if ($usuario === "" || $contrasena === "") {
        $error = "Completa todos los campos.";
    } else {
        $stmt = $conexion->prepare(
            "SELECT id, usuario, contrasena, rol
             FROM usuarios
             WHERE usuario = ?"
        );

        $stmt->bind_param("s", $usuario);
        $stmt->execute();
        $resultado = $stmt->get_result();

        if ($datos = $resultado->fetch_assoc()) {
            $claveValida = password_verify(
                $contrasena,
                $datos["contrasena"]
            );

            // Compatibilidad con las cuentas antiguas sin hash.
            if (!$claveValida &&
                hash_equals($datos["contrasena"], $contrasena)) {

                $nuevoHash = password_hash(
                    $contrasena,
                    PASSWORD_DEFAULT
                );

                $actualizar = $conexion->prepare(
                    "UPDATE usuarios SET contrasena = ? WHERE id = ?"
                );

                $actualizar->bind_param(
                    "si",
                    $nuevoHash,
                    $datos["id"]
                );

                $actualizar->execute();
                $actualizar->close();

                $claveValida = true;
            }

            if ($claveValida) {
                session_regenerate_id(true);

                $_SESSION["id_usuario"] = $datos["id"];
                $_SESSION["usuario"] = $datos["usuario"];
                $_SESSION["rol"] = $datos["rol"];

                header("Location: index.php");
                exit;
            }
        }

        $stmt->close();

        if ($error === "") {
            $error = "Usuario o contraseña incorrectos.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar sesión | Taskly</title>

    <style>
        * { box-sizing: border-box; }

        body {
            margin: 0;
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
            font-family: Arial, sans-serif;
            background: linear-gradient(135deg, #173f35, #269477);
        }

        .login-box {
            width: 100%;
            max-width: 390px;
            background: white;
            padding: 35px;
            border-radius: 14px;
            box-shadow: 0 10px 30px rgba(0,0,0,.15);
        }

        h1 {
            text-align: center;
            color: #173f35;
            margin-bottom: 8px;
        }

        .subtitulo {
            text-align: center;
            color: #78857f;
            margin-bottom: 25px;
        }

        label {
            display: block;
            margin: 15px 0 7px;
            font-weight: bold;
        }

        input {
            width: 100%;
            padding: 12px;
            border: 1px solid #d5dfda;
            border-radius: 7px;
        }

        button {
            width: 100%;
            padding: 13px;
            margin-top: 22px;
            background: #269477;
            color: white;
            border: none;
            border-radius: 7px;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
        }

        .error {
            padding: 11px;
            margin-top: 15px;
            background: #fdeaea;
            color: #b42318;
            border-radius: 7px;
            text-align: center;
        }

        a {
            display: block;
            margin-top: 18px;
            text-align: center;
            color: #269477;
            text-decoration: none;
        }
    </style>
</head>

<body>
    <div class="login-box">
        <h1>Bienvenido</h1>
        <p class="subtitulo">Ingresa a tu lista de tareas</p>

        <form method="POST">
            <label for="usuario">Usuario</label>
            <input type="text" id="usuario" name="usuario" required>

            <label for="contrasena">Contraseña</label>
            <input type="password" id="contrasena"
                   name="contrasena" required>

            <button type="submit">Iniciar sesión</button>
        </form>

        <?php if ($error !== ""): ?>
            <div class="error">
                <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>

        <a href="registro.php">¿No tienes cuenta? Regístrate aquí</a>
        <a href="../index.php">Volver a la página principal</a>
    </div>
</body>
</html>