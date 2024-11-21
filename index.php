<?php
include_once 'app/complements/header.php';

include("conexion_db.php");


// Incluir el archivo de búsqueda si hay una solicitud POST
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    include 'search_user.php'; // Cambiado de 'search_employee.php' a 'search_user.php'
}

// Consulta SQL para obtener las opciones de cargos de trabajo desde la tabla `positions`
$query_positions = mysqli_query($conn, "SELECT * FROM `positions`");

// Manejo de errores en la consulta
if (!$query_positions) {
    die('Error en la consulta: ' . mysqli_error($conn));
}
?>

<!-- navegador principal -->
<?php include 'app/complements/navbar-main.php'; ?>
<!-- fin navegador principal -->

<!-- Modal de Noticias (incluido desde archivo separado) -->
<?php include 'news_modal.php'; ?>
<!-- fin Modal de Noticias -->

<!-- contenido -->
<br><br><br><br>
<div class="container center-content">
    <div class="text-center">
        <h2>Directorio de Teléfono</h2>
        <div class="row">
            <div class="col-md-6 d-flex justify-content-center">
                <img src="images/directorio.jpg" alt="Phonebook Icon" style="width:50%;margin-left: 150px;">
            </div>
            <div class="col-md-4">
                <form action="index.php" method="post" class="w-80">
                    <div class="form-group">
                        <input type="text" class="form-control" name="nombre" placeholder="Nombre">
                    </div>
                    <div class="form-group">
                        <input type="text" class="form-control" name="apellido" placeholder="Apellido">
                    </div>
                    <div class="form-group">
                        <input type="text" class="form-control" name="telefono" placeholder="Teléfono">
                    </div>
                    <div class="form-group">
                        <select class="form-control" name="position_id">
                            <option value="">Seleccione un Cargo</option>
                            <?php
                            while ($row = mysqli_fetch_assoc($query_positions)) {
                                echo '<option value="' . $row['position_id'] . '">' . $row['position_name'] . '</option>';
                            }
                            ?>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary btn-block">Buscar</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- lista de contactos -->
<?php
// Incluir el archivo de resultados si hay una solicitud POST
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    include 'result_search.php'; // Cambiado a result_search.php
}
?>
<!-- fin de lista de contactos -->

<div class="header-aux">
    ACCESOS DIRECTOS A NUESTROS SISTEMAS
</div>

<div class="container mt-4">
    <div class="row">
        <div class="col-6 col-sm-4 col-md-3 text-center mb-3">
            <a href="contacts-grid.php" class="text-decoration-none text-dark">
                <div class="card">
                    <div class="card-body">
                        <img src="images/contactos.png" alt="Contactos" class="card-img-top" style="width:60%; margin: 0 auto;">
                        <p class="card-text mt-3">Contactos</p>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-6 col-sm-4 col-md-3 text-center mb-3">
            <a href="https://mail.fundacionculturalbcb.gob.bo/" class="text-decoration-none text-dark">
                <div class="card">
                    <div class="card-body">
                        <img src="images/correo.jpg" alt="Correo Institucional" class="card-img-top" style="width:60%; margin: 0 auto;">
                        <p class="card-text mt-3">Correo Institucional</p>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-6 col-sm-4 col-md-3 text-center mb-3">
            <a href="documents_tree.php" class="text-decoration-none text-dark">
                <div class="card">
                    <div class="card-body">
                        <img src="images/normas.jpg" alt="Repositorio Documental" class="card-img-top" style="width:60%; margin: 0 auto;">
                        <p class="card-text mt-3">Repositorio Documental</p>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-6 col-sm-4 col-md-3 text-center mb-3">
            <a href="#" class="text-decoration-none text-dark">
                <div class="card">
                    <div class="card-body">
                        <img src="images/facturacion.jpg" alt="Sistema de Facturación" class="card-img-top" style="width:60%; margin: 0 auto;">
                        <p class="card-text mt-3">Sistema de Facturación</p>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-6 col-sm-4 col-md-3 text-center mb-3">
            <a href="https://tiendavirtual.fcbcb.gob.bo/" class="text-decoration-none text-dark">
                <div class="card">
                    <div class="card-body">
                        <img src="images/biblioteca.jpg" alt="Biblioteca Digital" class="card-img-top" style="width:60%; margin: 0 auto;">
                        <p class="card-text mt-3">Tienda Virtual FC BCB</p>
                    </div>
                </div>
            </a>
        </div>
    </div>
</div>

<!-- fin de contenido -->

<?php
include_once 'app/complements/footer.php';
?>

<!-- Validación en el lado del cliente -->
<script>
    document.querySelector('form').addEventListener('submit', function(e) {
        var nombre = document.querySelector('[name="nombre"]').value.trim();
        var apellido = document.querySelector('[name="apellido"]').value.trim();
        var telefono = document.querySelector('[name="telefono"]').value.trim();
        var position = document.querySelector('[name="position_id"]').value;

        if (nombre === '' && apellido === '' && telefono === '' && position === '') {
            alert('Por favor, complete al menos un campo para realizar la búsqueda.');
            e.preventDefault(); // Evita que el formulario se envíe si no se ha llenado ningún campo
        }
    });

    // Mostrar modal de noticias al cargar la página solo una vez
    $(document).ready(function () {
        // Verificar si el modal de noticias ya ha sido mostrado en esta sesión
        if (!localStorage.getItem('newsModalShown')) {
            $('#newsModal').modal('show');
            localStorage.setItem('newsModalShown', 'true');
        }

        // Actualizar barra de progreso según la posición del carrusel
        $('#newsCarousel').on('slid.bs.carousel', function (e) {
            var totalItems = $('.carousel-item').length;
            var currentIndex = $('div.active').index() + 1;
            var progressPercentage = (currentIndex / totalItems) * 100;
            $('#newsProgress').css('width', progressPercentage + '%');
        });
    });
</script>

<!-- CSS adicional para mejorar el estilo -->
<style>
    .form-control {
        margin-bottom: 10px;
    }

    .table-responsive {
        overflow-x: auto;
    }

    .table thead th {
        text-align: center;
    }

    .table tbody td {
        text-align: center;
    }

    .btn {
        font-size: 1rem;
        padding: 0.5rem 1rem;
    }

    .card-title {
        font-size: 1.3rem;
        font-weight: bold;
    }

    /* Estilo para los íconos del carrusel */
    .carousel-control-prev-icon,
    .carousel-control-next-icon {
        background-color: black;
        width: 40px;
        height: 40px;
        border-radius: 50%;
    }

    /* Asegurar que las imágenes en el modal se ajusten correctamente */
    .carousel-item img {
        object-fit: contain;
        width: 100%;
        height: auto;
    }

    .carousel-indicators li {
        background-color: black;
    }

    /* Estilos de tarjetas en accesos directos */
    .card {
        border: 1px solid #ccc;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
    }

    .card:hover {
        transform: scale(1.05);
        transition: transform 0.3s;
    }

    .card-text {
        font-size: 1rem;
    }

    @media (max-width: 768px) {
        .form-control, .btn {
            font-size: 0.9rem;
        }
    }
</style>
