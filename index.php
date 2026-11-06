<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Taskly | Organiza tus tareas</title>
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: Arial, sans-serif;
            background: #f4f7f6;
            color: #263b36;
        }

        header {
            background: #173f35;
            color: white;
            padding: 22px 8%;
        }

        header h2 {
            font-size: 25px;
        }

        main {
            max-width: 1000px;
            margin: 70px auto;
            padding: 20px;
            text-align: center;
        }

        .etiqueta {
            color: #269477;
            font-weight: bold;
            letter-spacing: 2px;
            margin-bottom: 15px;
        }

        h1 {
            font-size: 42px;
            margin-bottom: 18px;
        }

        .descripcion {
            max-width: 650px;
            margin: 0 auto 30px;
            color: #66756f;
            line-height: 1.7;
        }

        .boton {
            display: inline-block;
            background: #269477;
            color: white;
            text-decoration: none;
            padding: 14px 25px;
            border-radius: 8px;
            font-weight: bold;
        }

        .boton:hover {
            background: #1c745d;
        }

        .caracteristicas {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(210px, 1fr));
            gap: 18px;
            margin-top: 55px;
            text-align: left;
        }

        .tarjeta {
            background: white;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 5px 18px rgba(0,0,0,.05);
        }

        .tarjeta h3 {
            margin-bottom: 10px;
            color: #1d6955;
        }

        .tarjeta p {
            color: #687771;
            line-height: 1.5;
        }

        footer {
            text-align: center;
            padding: 25px;
            color: #78857f;
        }
    </style>
</head>
<body>

    <header>
        <h2>Taskly</h2>
    </header>

    <main>
        <p class="etiqueta">PROGRAMACIÓN WEB AVANZADA</p>

        <h1>Lista de Tareas Interactiva</h1>

        <p class="descripcion">
            Sistema web que permite organizar actividades de manera sencilla.
            Cuenta con usuarios y permisos diferentes para agregar, completar
            y eliminar tareas, utilizando PHP y MySQL.
        </p>

        <a class="boton" href="ejercicio01/login.php">
            Ingresar al sistema
        </a>

        <section class="caracteristicas">
            <article class="tarjeta">
                <h3>Organización</h3>
                <p>Registra las actividades que necesitas realizar.</p>
            </article>

            <article class="tarjeta">
                <h3>Control de tareas</h3>
                <p>Marca las actividades cuando estén completadas.</p>
            </article>

            <article class="tarjeta">
                <h3>Usuarios y roles</h3>
                <p>Accede como administrador o como usuario.</p>
            </article>
        </section>
    </main>

    <footer>
        Tarea Práctica 4 · Programación Web Avanzada
    </footer>

</body>
</html>