<?php
include_once 'app/complements/header.php';
session_start();
?>

<style>
    body {
        background-color: #f8f9fa;
        display: flex;
        justify-content: center;
        align-items: center;
        height: 100vh;
        margin: 0;
    }

    .login-container {
        background-color: #ffffff;
        padding: 30px;
        border-radius: 10px;
        box-shadow: 0px 0px 15px rgba(0, 0, 0, 0.1);
        max-width: 400px;
        width: 100%;
    }

    .login-header {
        margin-bottom: 20px;
        text-align: center;
    }

    .login-header h1 {
        font-size: 24px;
        color: #343a40;
        font-weight: bold;
    }

    .form-group label {
        font-weight: 500;
    }

    .form-control {
        border-radius: 5px;
    }

    .btn-primary {
        background-color: #007bff;
        border: none;
        border-radius: 5px;
        padding: 10px;
    }

    .btn-primary:hover {
        background-color: #0056b3;
    }
</style>

<div class="login-container">
    <div class="login-header">
        <h1>Ingreso Usuario</h1>
    </div>
    <form method="POST">
        <div class="form-group mb-3">
            <label for="username">Usuario</label>
            <input class="form-control" name="username" placeholder="Usuario" type="text" required="required">
        </div>
        <div class="form-group mb-3">
            <label for="password">Contraseña</label>
            <input class="form-control" name="password" placeholder="Contraseña" type="password" required="required">
        </div>
        <!-- Ruta corregida del archivo login_query.php -->
        <?php require_once __DIR__ . '/app/funcionts/login_query.php'; ?>
        <div class="form-group">
            <button class="btn btn-primary btn-block" name="login">
                <span class="glyphicon glyphicon-log-in"></span> Acceder
            </button>
        </div>
    </form>
</div>
