<?php
require_once "conexion.php";

$error = "";
$exito = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $nombre = trim($_POST["nombre"] ?? "");
    $usuario = trim($_POST["usuario"] ?? "");
    $contrasena = $_POST["contrasena"] ?? "";

    if ($nombre === "" || $usuario === "" || $contrasena === "") {
        $error = "Completa todos los campos.";
    } elseif (strlen($nombre) > 50 || strlen($usuario) > 50) {
        $error = "El nombre y el usuario deben tener máximo 50 caracteres.";
    } elseif (strlen($contrasena) < 6) {
        $error = "La contraseña debe tener al menos 6 caracteres.";
    } else {
        $stmt = $conexion->prepare(
            "SELECT id FROM usuarios WHERE usuario = ?"
        );
        $stmt->bind_param("s", $usuario);
        $stmt->execute();
        $resultado = $stmt->get_result();
        $existe = $resultado->num_rows > 0;
        $stmt->close();

        if ($existe) {
            $error = "Ese nombre de usuario ya está registrado.";
        } else {
            $hash = password_hash($contrasena, PASSWORD_DEFAULT);

            $stmt = $conexion->prepare(
                "INSERT INTO usuarios (nombre, usuario, contrasena, rol)
                 VALUES (?, ?, ?, 'usuario')"
            );

            $stmt->bind_param("sss", $nombre, $usuario, $hash);

            if ($stmt->execute()) {
                $exito = "Cuenta creada correctamente. Ya puedes iniciar sesión.";
            } else {
                $error = "No se pudo crear la cuenta.";
            }

            $stmt->close();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crear cuenta | Taskly</title>

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

        .caja {
            width: 100%;
            max-width: 420px;
            background: white;
            padding: 35px;
            border-radius: 14px;
        }

        h1 {
            text-align: center;
            color: #173f35;
        }

        p {
            text-align: center;
            color: #78857f;
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
            margin-top: 22px;
            padding: 13px;
            border: none;
            border-radius: 7px;
            background: #269477;
            color: white;
            font-weight: bold;
            cursor: pointer;
        }

        .mensaje {
            padding: 12px;
            margin-top: 15px;
            border-radius: 7px;
            text-align: center;
        }

        .error {
            background: #fdeaea;
            color: #b42318;
        }

        .exito {
            background: #e5f6ed;
            color: #176b43;
        }

        a {
            display: block;
            margin-top: 20px;
            text-align: center;
            color: #269477;
            text-decoration: none;
        }
    </style>
</head>

<body>
    <div class="caja">
        <h1>Crear cuenta</h1>
        <p>Regístrate para organizar tus tareas.</p>

        <form method="POST">
            <label for="nombre">Nombre completo</label>
            <input type="text" id="nombre" name="nombre"
                   maxlength="50" required>

            <label for="usuario">Nombre de usuario</label>
            <input type="text" id="usuario" name="usuario"
                   maxlength="50" required>

            <label for="contrasena">Contraseña</label>
            <input type="password" id="contrasena" name="contrasena"
                   minlength="6" required>

            <button type="submit">Registrarme</button>
        </form>

        <?php if ($error !== ""): ?>
            <div class="mensaje error">
                <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>

        <?php if ($exito !== ""): ?>
            <div class="mensaje exito">
                <?php echo htmlspecialchars($exito); ?>
            </div>
        <?php endif; ?>

        <a href="login.php">Ya tengo una cuenta · Iniciar sesión</a>
    </div>
</body>
</html>