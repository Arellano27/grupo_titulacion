<?php
namespace Titulacion\SisAcademicoBundle\Helper;
class procesarArchivos {
    public function moveFiles($files,$idEstudiante,$ruta)
    {
        $inputFileName = "archivo"; //nombre del Input origen (ejemplo name="archivo[]" --tomar solo--> archivo
        $uploadDirectory = $ruta;//"documentos/estudiantes"; //ubicacion y nombre del directorio donde se guarda
        $fileLocations = array();
        $validExtensions = array('gif', 'jpg', 'jpe', 'jpeg', 'png'); //extensiones permitidas

        if (file_exists($uploadDirectory) && is_writable($uploadDirectory)) { //comprueba si el directorio existe y si es posible escribir
            if (isset($files[$inputFileName]["error"])) {
                foreach ($files[$inputFileName]["error"] as $key => $error) {
                    if ($error == 0) {
                        $pieces = explode(".", $files[$inputFileName]["name"][$key]); //obtenemos la extensión
                        $extension = strtolower(end($pieces)); //la pasamos a minuscula

                        $validFileExtension = false;
                        foreach ($validExtensions as $type) { //comprobamos que sea una extensión permitida
                            if ($type == $extension) {
                                $validFileExtension = true;
                            }
                        }

                        if ($validFileExtension) { //si el archivo es valido lo intentamos mover
                            //$fileName = date("Ymd") . "_" . date("is") . "_img_" . $pieces[0] . "." . $extension; //generamos un nombre personalizable
                            $fileName = $idEstudiante. "-" . $pieces[0] . "." . $extension; //generamos un nombre personalizable
                            $currentLocation = $files[$inputFileName]["tmp_name"][$key]; //ubicacion original y temporal del archivo
                            if (!move_uploaded_file($currentLocation, "$uploadDirectory/$fileName")) {
                                echo "No se puede mover el archivo \n";
                            } else {
                                $fileLocations[] = $fileName;
                            }
                        } else {
                            echo "Extension de archivo no valida \n";
                        }
                    } else {
                        if ($error != 0 and $error != 4) { //Si no subieron archivos No enviar ninguna advertencia
                            $errorMessage = getErrorMessage($files[$inputFileName]["error"][$key]);
                            echo $errorMessage . " Intente nuevamente. \n";
                            die;
                        }
                    }
                }
                return $fileLocations;
            } //fin del existe error
            else {
                echo "Uno de los archivos sobrepasa la capacidad establecida por el servidor";
            }
        } else {
            echo "No existe la carpeta para subir archivos o no tiene los permisos suficientes.";
        }
    } // Termina la funcion

    public function getErrorMessage($error_code)
    { //Mensajes Personalizados
        switch ($error_code) {
            case UPLOAD_ERR_INI_SIZE:
                return 'El archivo excede el limite permitido por la directiva de PHP';
            case UPLOAD_ERR_FORM_SIZE:
                return 'El archivo excede el limite permitido por la directiva de HTML';
            case UPLOAD_ERR_PARTIAL:
                return 'El archivo solo se subio parcialmente, intentelo de nuevo';
            case UPLOAD_ERR_NO_FILE:
                return 'No hay archivo que subir';
            case UPLOAD_ERR_NO_TMP_DIR:
                return 'El folder temporal no existe';
            case UPLOAD_ERR_CANT_WRITE:
                return 'No tiene permisos para grabar archivos en el disco';
            case UPLOAD_ERR_EXTENSION:
                return 'El archivo tiene una extensión NO permitida';
            default:
                return 'Error desconocido al subir el archivo';
        }
    } // Termina funcion mensajesErrorArchivos
}

?>