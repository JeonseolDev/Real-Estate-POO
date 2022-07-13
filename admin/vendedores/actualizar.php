<?php 

require '../../includes/app.php';
use App\Vendedor;

estaAutenticado();

$id = $_GET['id'] ?? '';
$id = filter_var($id, FILTER_VALIDATE_INT);

if(!$id) {
    header('Location: /admin');
}

$vendedor = Vendedor::find($id);

$errores = Vendedor::getErrores();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    //Asignar los valores

    $args = $_POST['vendedor'];

    // Sincronizar objeto en memoria con lo que el usuario escribió
    $vendedor->sincronizar($args);

    // Validación

    $errores = $vendedor->validar();

    if(empty($errores)) {
        $vendedor->guardar();
    }
}

incluirTemplate('header');

?>

<main class="contenedor seccion">
        <h1>Actualizar Vendedores</h1>

        <a href="/bienesraices/admin/index.php" class="boton boton-verde">Volver</a>

        <?php foreach($errores as $error): ?>
        <div class="alerta error">
            <?php echo $error; ?>
        </div>
        <?php endforeach; ?>

        <form class="formulario" method="POST" action="/bienesraices/admin/vendedores/actualizar.php" enctype="multipart/form-data">
            <?php include '../../includes/templates/formulario_vendedores.php'; ?>

            <input type="submit" value="Actualizar Vendedor" class="boton boton-verde">
        </form>
        
    </main>

<?php 
    incluirTemplate('footer');
?> 