<?php
require_once "conexion.php";

if (!isset($_SESSION["id_usuario"])) {
    header("Location: login.php");
    exit;
}

$id_usuario = (int) $_SESSION["id_usuario"];
$es_admin = ($_SESSION["rol"] === "admin");
$mensaje = "";

// Agregar una tarea
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    if (isset($_POST["agregar"])) {
        $tarea = trim($_POST["tarea"] ?? "");

        if ($tarea === "" || strlen($tarea) > 255) {
            $mensaje = "Escribe una tarea de máximo 255 caracteres.";
        } else {
            $stmt = $conexion->prepare(
                "INSERT INTO tareas (id_usuario, tarea)
                 VALUES (?, ?)"
            );

            $stmt->bind_param("is", $id_usuario, $tarea);
            $stmt->execute();
            $stmt->close();

            header("Location: index.php");
            exit;
        }
    }

    // Completar o deshacer una tarea
    if (isset($_POST["completar"])) {
        $id = filter_input(
            INPUT_POST,
            "id_tarea",
            FILTER_VALIDATE_INT
        );

        if ($id) {
            if ($es_admin) {
                $stmt = $conexion->prepare(
                    "UPDATE tareas
                     SET completada = NOT completada
                     WHERE id = ?"
                );
                $stmt->bind_param("i", $id);
            } else {
                $stmt = $conexion->prepare(
                    "UPDATE tareas
                     SET completada = NOT completada
                     WHERE id = ? AND id_usuario = ?"
                );
                $stmt->bind_param("ii", $id, $id_usuario);
            }

            $stmt->execute();
            $stmt->close();
        }

        header("Location: index.php");
        exit;
    }

    // Eliminar: solamente administrador
    if (isset($_POST["eliminar"]) && $es_admin) {
        $id = filter_input(
            INPUT_POST,
            "id_tarea",
            FILTER_VALIDATE_INT
        );

        if ($id) {
            $stmt = $conexion->prepare(
                "DELETE FROM tareas WHERE id = ?"
            );

            $stmt->bind_param("i", $id);
            $stmt->execute();
            $stmt->close();
        }

        header("Location: index.php");
        exit;
    }
}

// Consultar las tareas según el rol
if ($es_admin) {
    $resultado = $conexion->query(
        "SELECT tareas.*, usuarios.usuario
         FROM tareas
         INNER JOIN usuarios ON tareas.id_usuario = usuarios.id
         ORDER BY tareas.id DESC"
    );
} else {
    $stmt = $conexion->prepare(
        "SELECT tareas.*, usuarios.usuario
         FROM tareas
         INNER JOIN usuarios ON tareas.id_usuario = usuarios.id
         WHERE tareas.id_usuario = ?
         ORDER BY tareas.id DESC"
    );

    $stmt->bind_param("i", $id_usuario);
    $stmt->execute();
    $resultado = $stmt->get_result();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Taskly | Mi lista de tareas</title>

    <style>
        * { box-sizing: border-box; }

        body {
            margin: 0;
            min-height: 100vh;
            padding: 30px 15px;
            font-family: Arial, sans-serif;
            background: #f1f6f3;
            color: #263b36;
        }

        .container {
            max-width: 750px;
            margin: auto;
            padding: 30px;
            background: white;
            border-radius: 14px;
            box-shadow: 0 8px 25px rgba(0,0,0,.07);
        }

        .encabezado {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 15px;
            flex-wrap: wrap;
            margin-bottom: 22px;
        }

        h1 {
            color: #173f35;
            margin: 0;
        }

        .usuario {
            color: #687771;
            margin-top: 10px;
        }

        .rol {
            display: inline-block;
            padding: 5px 11px;
            margin-left: 5px;
            border-radius: 20px;
            color: white;
            font-size: 13px;
            background: #269477;
        }

        .rol.admin { background: #b87932; }

        .salir {
            color: #b54747;
            text-decoration: none;
            font-weight: bold;
        }

        .formulario {
            display: flex;
            gap: 10px;
            margin: 25px 0;
        }

        .formulario input {
            flex: 1;
            min-width: 0;
            padding: 13px;
            border: 1px solid #d5dfda;
            border-radius: 7px;
            font-size: 15px;
        }

        button {
            border: none;
            border-radius: 7px;
            padding: 12px 18px;
            color: white;
            font-weight: bold;
            cursor: pointer;
        }

        .agregar { background: #269477; }
        .completar { background: #3484b7; }
        .eliminar { background: #c65353; }

        .tarea {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 15px;
            padding: 15px;
            margin-bottom: 12px;
            background: #f6f9f7;
            border-left: 4px solid #269477;
            border-radius: 7px;
        }

        .tarea.completada {
            border-left-color: #9aa9a1;
            background: #f0f2f1;
        }

        .tarea.completada .texto {
            text-decoration: line-through;
            color: #89958e;
        }

        .texto { overflow-wrap: anywhere; }

        .acciones {
            display: flex;
            align-items: center;
            gap: 8px;
            flex-wrap: wrap;
        }

        .vacio {
            text-align: center;
            color: #78857f;
            padding: 25px;
        }

        .aviso {
            padding: 12px;
            background: #fff2dc;
            color: #805b21;
            border-radius: 7px;
            margin-bottom: 15px;
        }

        .nota-rol {
            margin-top: 20px;
            color: #78857f;
            font-size: 14px;
        }

        @media (max-width: 550px) {
            .container { padding: 20px; }
            .formulario { flex-direction: column; }
            .tarea {
                align-items: flex-start;
                flex-direction: column;
            }
        }
    </style>
</head>

<body>

<main class="container">

    <div class="encabezado">
        <div>
            <h1>Taskly</h1>

            <p class="usuario">
                Bienvenido,
                <strong>
                    <?php echo htmlspecialchars($_SESSION["usuario"]); ?>
                </strong>

                <span class="rol <?php echo $es_admin ? 'admin' : ''; ?>">
                    <?php echo $es_admin ? "Administrador" : "Usuario"; ?>
                </span>
            </p>
        </div>

        <a class="salir" href="logout.php">Cerrar sesión</a>
    </div>

    <?php if ($mensaje !== ""): ?>
        <div class="aviso">
            <?php echo htmlspecialchars($mensaje); ?>
        </div>
    <?php endif; ?>

    <form method="POST" class="formulario">
        <input
            type="text"
            name="tarea"
            placeholder="Escribe una nueva tarea..."
            maxlength="255"
            required
        >

        <button class="agregar" type="submit" name="agregar">
            Agregar tarea
        </button>
    </form>

    <?php if ($resultado && $resultado->num_rows > 0): ?>

        <?php while ($tarea = $resultado->fetch_assoc()): ?>

            <div class="tarea <?php echo $tarea["completada"] ? "completada" : ""; ?>">

                <div class="texto">
                    <strong>
                        <?php echo htmlspecialchars($tarea["tarea"]); ?>
                    </strong>

                    <small>
                        <br>
                        Creada por:
                        <?php echo htmlspecialchars($tarea["usuario"]); ?>
                    </small>
                </div>

                <div class="acciones">

                    <form method="POST">
                        <input type="hidden" name="id_tarea"
                               value="<?php echo (int) $tarea["id"]; ?>">

                        <button
                            type="submit"
                            name="completar"
                            class="completar">
                            <?php echo $tarea["completada"]
                                ? "Deshacer" : "Completar"; ?>
                        </button>
                    </form>

                    <?php if ($es_admin): ?>
                        <form method="POST"
                              onsubmit="return confirm('¿Deseas eliminar esta tarea?');">

                            <input type="hidden" name="id_tarea"
                                   value="<?php echo (int) $tarea["id"]; ?>">

                            <button type="submit" name="eliminar"
                                    class="eliminar">
                                Eliminar
                            </button>
                        </form>
                    <?php endif; ?>

                </div>
            </div>

        <?php endwhile; ?>

    <?php else: ?>

        <p class="vacio">
            Todavía no tienes tareas registradas. ¡Agrega la primera!
        </p>

    <?php endif; ?>

    <p class="nota-rol">
        <?php if ($es_admin): ?>
            Como administrador puedes ver y gestionar todas las tareas.
        <?php else: ?>
            Como usuario puedes agregar y completar tus propias tareas.
        <?php endif; ?>
    </p>

</main>

</body>
</html>