<?php 

function conectarDB() : mysqli {
    $db = new mysqli('localhost', 'root', 'root1', 'bienes_raices');

    if(!$db) {
        echo "Error no se pudo conectar";
        exit;
    } 

    return $db;
    
}