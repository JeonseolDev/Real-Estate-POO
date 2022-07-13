<?php 

namespace App;

class ActiveRecord {
    protected static $db;
    protected static $columnasDB = [];
    protected static $tabla = '';
    // Errores

    protected static $errores = [];
    

    public function guardar() {
        if(isset($this->id)){
            $this->actualizar();
        } else {
            $this->crear();
        }
    }

    public function actualizar(){
        $atributos = $this->sanitizarDatos();
        $valores = [];
        foreach($atributos as $key => $value) {
            $valores[] = "{$key}= '{$value}'";
        }
        $query = " UPDATE " . static::$tabla . " SET ";
        $query .= join(', ', $valores);
        $query .= " WHERE id = '" . self::$db->escape_string($this->id). "' ";
        $query .= " LIMIT 1 ";

        $resultado = self::$db->query($query);

        return $resultado;
        
        if ($resultado) {
            $this->borrarImagen();
            header('Location: /bienesraices/admin?resultado=2');
        }
    }
    
    public function crear() {

        // Sanitizar los datos

        $atributos = $this->sanitizarDatos();

        $string = join(", ", array_keys($atributos));
        $stringvalues = join("', '", array_values($atributos));
        
        // Insertar en la base de datos
        $query = " INSERT INTO " . static::$tabla . " ( ";
        $query .= $string;
        $query .= " ) VALUES (' ";
        $query .= $stringvalues;
        $query .= " ') ";

        

        $resultado = self::$db->query($query);

        if($resultado) {
            header('Location: /bienesraices/admin?resultado=1');
        }
    }

    public static function setDB($database) {
        self::$db = $database;
    }

    public function atributos() {
        $atributos = [];
        foreach(static::$columnasDB as $columna){
            if($columna === 'id') continue;
            $atributos[$columna] = $this->$columna;
        }
        return $atributos;
    }

    // Eliminar un registro

    public function eliminar() {
        $query = " DELETE FROM " . static::$tabla . " WHERE id = " . self::$db->escape_string($this->id) . " LIMIT 1 ";
        $resultado = self::$db->query($query);
        return $resultado;
        
        if ($resultado) {
            header('Location: /bienesraices/admin?resultado=3');
        }
    }

    public function sanitizarDatos() {
        $atributos = $this->atributos();
        $sanitizado = [];

        foreach($atributos as $key => $value) {
            $sanitizado[$key] = self::$db->escape_string($value);
    }
    return $sanitizado;
}

    public function setImagen($imagen){

        // Elimina la imagen previa
        if($this->id) {
            $this->borrarImagen();
        }

        // Asignar al atributo de imagen el nombre de la imagen
        if($imagen){
            $this->imagen = $imagen;
        }
    }

    // Eliminar imagen

    public function borrarImagen() {
        $existeArchivo = file_exists(CARPETA_IMAGENES . $this->imagen);
        if($existeArchivo){
            unlink(CARPETA_IMAGENES . $this->imagen);
        }
    }

    public static function getErrores() {
        
        return self::$errores;
    }

    public function validar() {
        static::$errores = [];
        return static::$errores;
    }

    // Listar todas los registros

    public static function all() {
        $query = "SELECT * FROM " . static::$tabla;
        $resultado = self::consultarSQL($query);

        return $resultado;

    }

    // Obtiene determinado número de registros.

    public static function get($cantidad) {
        $query = "SELECT * FROM " . static::$tabla . " LIMIT " . $cantidad;
        $resultado = self::consultarSQL($query);

        return $resultado;

    }
    
    // Busca un registro por su id

    public static function find($id) {
        $query = "SELECT * FROM " . static::$tabla .  " WHERE id = $id";
        $resultado = self::consultarSQL($query);

        return array_shift($resultado);
    }

    public static function consultarSQL($query) {
        $resultado = self::$db->query($query);
        $array = [];
        while($registro = $resultado->fetch_assoc()) {
            $array[] = static::crearObjeto($registro);
        }

        // Liberar memoria

        $resultado->free();

        // Retornar resultados

        return $array;
    }

    protected static function crearObjeto($registro) {
        $objeto = new static;
        foreach($registro as $key => $value) {
            if (property_exists($objeto, $key)) {
                $objeto->$key = $value;
            }
        }
        return $objeto;
    }

    // Sincroniza el objeto en memoria con los cambios realizados por el usuario

    public function sincronizar($args = []) {
        foreach($args as $key => $value) {
            if (property_exists($this, $key) && is_null($value)) {
                $this->$key = $value;
            }
        }
    }
}