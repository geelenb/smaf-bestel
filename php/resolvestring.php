<?php
function respolvestring($txt) {
    $table = array('á' => 'a',
     'Á' => 'A',
     'à' => 'a',
     'À' => 'A',
     'ä' => 'ae',
     'Ä' => 'AE',
     'é' => 'e',
     'É' => 'E',
     'è' => 'e',
     'È' => 'E',
     'ê' => 'e',
     'Ê' => 'E',
     'ë' => 'e',
     'Ë' => 'E',
     'ï' => 'i',
     'Ï' => 'I',
     'ó' => 'o',
     'Ó' => 'O',
     'ò' => 'o',
     'Ò' => 'O',
     'ô' => 'o',
     'Ô' => 'O',
     'ö' => 'oe',
     'Ö' => 'OE',
     'ú' => 'u',
     'Ú' => 'U',
     'ù' => 'u',
     'Ù' => 'U',
     'û' => 'u',
     'Û' => 'U',
     'ü' => 'ue',
     'Ü' => 'UE');
    return str_replace(array_keys($table), array_values($table), $txt);
}
?>