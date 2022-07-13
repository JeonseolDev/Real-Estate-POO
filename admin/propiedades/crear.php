<?php 

    require '../../includes/app.php';

    use App\Propiedad;
    use App\Vendedor;
    use Intervention\Image\ImageManagerStatic as Image;

    
    estaAutenticado();

    // Base de datos
    
    $propiedad = new Propiedad;

    // Consultas para obtener todos los vendedores

    $vendedores = Vendedor::all();

    // Arreglo con mensajes de errores
    $errores = Propiedad::getErrores();


    // Ejecutar el código después de que el usuario envia el formulario
    if($_SERVER['REQUEST_METHOD'] === 'POST') {

        $propiedad = new Propiedad($_POST['propiedad']);

        // Generar un nombre único
        $nombreImagen = md5( uniqid( rand(), true ) ) . ".jpg";

        // Subir la imagen
        // Resize a la imagen
        if($_FILES['imagen']['tmp_name']){
            $image = Image::make($_FILES['propiedad']['tmp_name']['imagen'])->fit(800,600);
            $propiedad->setImagen($nombreImagen);
        }

        $errores = $propiedad->validar();

        // Revisar que el array de errores este vacio

        if(empty($errores)) {
            

            if(!is_dir(CARPETA_IMAGENES)) {
                mkdir(CARPETA_IMAGENES);
            }
            /** SUBIDA DE ARCHIVOS */ 
            // Guardar la imagen en el servidor

            $image->save(CARPETA_IMAGENES . $nombreImagen);

            $propiedad->guardar();

        }
    }

    incluirTemplate('header');
?>

    <main class="contenedor seccion">
        <h1>Crear</h1>

        

        <a href="/bienesraices/admin/index.php" class="boton boton-verde">Volver</a>

        <?php foreach($errores as $error): ?>
        <div class="alerta error">
            <?php echo $error; ?>
        </div>
        <?php endforeach; ?>

        <form class="formulario" method="POST" action="/bienesraices/admin/propiedades/crear.php" enctype="multipart/form-data">
            <?php include '../../includes/templates/formulario_propiedades.php'; ?>

            <input type="submit" value="Crear Propiedad" class="boton boton-verde">
        </form>
        
    </main>

<?php 
    incluirTemplate('footer');
?> 