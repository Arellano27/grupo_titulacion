<?php
namespace Titulacion\SisAcademicoBundle\Helper;
include ('AcademicoSoap.php');


/**
* 
*/
class UgServices
{
   public function getLogin($username,$password){
           $ws=new AcademicoSoap();
           $tipo       = "3";
           $usuario    = "abc";
           $clave      = "123";
           $source     = "jdbc/procedimientosSaug";
           $url        = "http://192.168.100.11:8080/WSObjetosUg/ServicioWebObjetos?wsdl";
           $host       = "192.168.100.11:8080";
           $trama      = "<usuario>".$username."</usuario><contrasena>".$password."</contrasena>";
           $response=$ws->doRequestSreReceptaTransacionProcedimientos($trama,$source,$tipo,$usuario,$clave,$url,$host);

           return $response;

   }#end function

   public function getConsultaNotas($servicio=""){
           $ws=new AcademicoSoap();
           $tipo       = "3";
           $usuario    = "abc";
           $clave      = "123";
           $source     = "jdbc/procedimientosSaug";
           $url        = "http://192.168.100.11:8080/WSObjetosUg/ServicioWebObjetos?wsdl";
           $host       = "192.168.100.11:8080";
           $trama      = "<usuario>0924393861</usuario><contrasena>sinclave</contrasena>";
           $response=$ws->doRequestSreReceptaTransacionConsultas($trama,$source,$tipo,$usuario,$clave,$url,$host);

           return $response;
   }#end function
   
   public function Docentes_getCarreras($idDocente){
           $ws         = new AcademicoSoap();
           $tipo       = "3";
           $usuario    = "abc";
           $clave      = "123";
           $source     = "jdbc/procedimientosSaug";
           $url        = "http://192.168.100.11:8080/WSObjetosUg/ServicioWebObjetos?wsdl";
           $host       = "192.168.100.11:8080";
           $trama      = "<idDocente>".$idDocente."</idDocente>";
           $XML        = <<<XML
<soap:Envelope
xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
<soap:Body>
<ns2:ejecucionConsultaResponse
xmlns:ns2="http://servicios.ug.edu.ec/">
<return>
    <codigoRespuesta>0</codigoRespuesta>
    <estado>F</estado>
    <idHistorico>1089</idHistorico>
    <mensajeRespuesta>ok</mensajeRespuesta>
    <respuestaConsulta>
        <registros>
            <registro>
                    <idCarrera>14</idCarrera>
                    <nombreCarrera>Ingeniería en Sistemas Computaciones</nombreCarrera>
            </registro>
            <registro>
                    <idCarrera>16</idCarrera>
                    <nombreCarrera>Ingeniería Química</nombreCarrera>
            </registro>
            <registro>
                    <idCarrera>18</idCarrera>
                    <nombreCarrera>Ingeniería Civil</nombreCarrera>
            </registro>
        </registros>
    </respuestaConsulta>
</return>
</ns2:ejecucionConsultaResponse>
</soap:Body>
</soap:Envelope>
XML;
           $response=$ws->doRequestSreReceptaTransacionConsultas($trama,$source,$tipo,$usuario,$clave,$url,$host, $XML);

           return $response;
   }#end function
   
   
   public function Docentes_getMaterias($idDocente, $idCarrera){
           $ws         = new AcademicoSoap();
           $tipo       = "3";
           $usuario    = "abc";
           $clave      = "123";
           $source     = "jdbc/procedimientosSaug";
           $url        = "http://192.168.100.11:8080/WSObjetosUg/ServicioWebObjetos?wsdl";
           $host       = "192.168.100.11:8080";
           $trama      = "<idDocente>".$idDocente."</idDocente>";
           
           if($idCarrera==14) {
               $XML        = <<<XML
<soap:Envelope
xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
<soap:Body>
<ns2:ejecucionConsultaResponse
xmlns:ns2="http://servicios.ug.edu.ec/">
<return>
    <codigoRespuesta>0</codigoRespuesta>
    <estado>F</estado>
    <idHistorico>1089</idHistorico>
    <mensajeRespuesta>ok</mensajeRespuesta>
    <respuestaConsulta>
        <registros>
            <registro>
                    <idMateria>1001</idMateria>
                    <nombreMateria>Ingenería de Software</nombreMateria>
                    <paralelo>S4A</paralelo>
                    <horario>Diurno</horario>
                    <inscritos>35</inscritos>
            </registro>
            <registro>
                    <idMateria>1002</idMateria>
                    <nombreMateria>Matemáticas III</nombreMateria>
                    <paralelo>S4K</paralelo>
                    <horario>Nocturno</horario>
                    <inscritos>25</inscritos>
            </registro>
            <registro>
                    <idMateria>1003</idMateria>
                    <nombreMateria>Investigación de Operaciones</nombreMateria>
                    <paralelo>N7J</paralelo>
                    <horario>Nocturno</horario>
                    <inscritos>33</inscritos>
            </registro>
        </registros>
    </respuestaConsulta>
</return>
</ns2:ejecucionConsultaResponse>
</soap:Body>
</soap:Envelope>
XML;
           }
           elseif($idCarrera==16) {
              $XML        = <<<XML
<soap:Envelope
xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
<soap:Body>
<ns2:ejecucionConsultaResponse
xmlns:ns2="http://servicios.ug.edu.ec/">
<return>
    <codigoRespuesta>0</codigoRespuesta>
    <estado>F</estado>
    <idHistorico>1089</idHistorico>
    <mensajeRespuesta>ok</mensajeRespuesta>
    <respuestaConsulta>
        <registros>
            <registro>
                    <idMateria>1004</idMateria>
                    <nombreMateria>Química Inorgánica</nombreMateria>
                    <paralelo>Q3A</paralelo>
                    <horario>Diurno</horario>
                    <inscritos>38</inscritos>
            </registro>
            <registro>
                    <idMateria>1005</idMateria>
                    <nombreMateria>Principios de Biotecnología</nombreMateria>
                    <paralelo>Q4J</paralelo>
                    <horario>Nocturno</horario>
                    <inscritos>26</inscritos>
            </registro>
            <registro>
                    <idMateria>1006</idMateria>
                    <nombreMateria>Ingeniería de las Reacciones Químicas II</nombreMateria>
                    <paralelo>Q6L</paralelo>
                    <horario>Nocturno</horario>
                    <inscritos>24</inscritos>
            </registro>
        </registros>
    </respuestaConsulta>
</return>
</ns2:ejecucionConsultaResponse>
</soap:Body>
</soap:Envelope>
XML;
           }
   else{//if($idCarrera==18) {
              $XML        = <<<XML
<soap:Envelope
xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
<soap:Body>
<ns2:ejecucionConsultaResponse
xmlns:ns2="http://servicios.ug.edu.ec/">
<return>
    <codigoRespuesta>0</codigoRespuesta>
    <estado>F</estado>
    <idHistorico>1089</idHistorico>
    <mensajeRespuesta>ok</mensajeRespuesta>
    <respuestaConsulta>
        <registros>
            <registro>
                    <idMateria>1007</idMateria>
                    <nombreMateria>Cálculo Multivariable</nombreMateria>
                    <paralelo>C3A</paralelo>
                    <horario>Diurno</horario>
                    <inscritos>31</inscritos>
            </registro>
            <registro>
                    <idMateria>1008</idMateria>
                    <nombreMateria>Ecuaciones Diferenciales</nombreMateria>
                    <paralelo>C4J</paralelo>
                    <horario>Nocturno</horario>
                    <inscritos>28</inscritos>
            </registro>
            <registro>
                    <idMateria>1009</idMateria>
                    <nombreMateria>Resistencia de Materiales</nombreMateria>
                    <paralelo>C7L</paralelo>
                    <horario>Nocturno</horario>
                    <inscritos>36</inscritos>
            </registro>
        </registros>
    </respuestaConsulta>
</return>
</ns2:ejecucionConsultaResponse>
</soap:Body>
</soap:Envelope>
XML;
           }
                   
           
           $response=$ws->doRequestSreReceptaTransacionConsultas($trama,$source,$tipo,$usuario,$clave,$url,$host, $XML);

           return $response;
   }#end function
   
       public function Docentes_getAlumnos($idDocente, $idCarrera){
           $ws         = new AcademicoSoap();
           $tipo       = "3";
           $usuario    = "abc";
           $clave      = "123";
           $source     = "jdbc/procedimientosSaug";
           $url        = "http://192.168.100.11:8080/WSObjetosUg/ServicioWebObjetos?wsdl";
           $host       = "192.168.100.11:8080";
           $trama      = "<idDocente>".$idDocente."</idDocente>";
           
          
               $XML        = <<<XML
<soap:Envelope
xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
<soap:Body>
<ns2:ejecucionConsultaResponse
xmlns:ns2="http://servicios.ug.edu.ec/">
<return>
    <codigoRespuesta>0</codigoRespuesta>
    <estado>F</estado>
    <idHistorico>1089</idHistorico>
    <mensajeRespuesta>ok</mensajeRespuesta>
    <respuestaConsulta>
        <registros>
            <registro>
                    <Nombrealm>Carlos Quiñonez</Nombrealm>
            </registro>
            <registro>
                    <Nombrealm>Juan Romero</Nombrealm>
            </registro>
            <registro>
                    <Nombrealm>Daniel Verdesoto</Nombrealm>
            </registro>
            <registro>
                    <Nombrealm>Fernando Lopez</Nombrealm>
            </registro>
            <registro>
                    <Nombrealm>Alexandra Gutierrez</Nombrealm>
            </registro>
            <registro>
                    <Nombrealm>Roberto Carlos</Nombrealm>
            </registro>
            <registro>
                    <Nombrealm>Orlando Macias</Nombrealm>
            </registro>
            <registro>
                    <Nombrealm>Fernanda Montero</Nombrealm>
            </registro>
            <registro>
                    <Nombrealm>Ana Kam</Nombrealm>
            </registro>
            <registro>
                    <Nombrealm>Angel Fuentes</Nombrealm>
            </registro>
        </registros>
    </respuestaConsulta>
</return>
</ns2:ejecucionConsultaResponse>
</soap:Body>
</soap:Envelope>
XML;
           
           
                   
           
           $response=$ws->doRequestSreReceptaTransacionConsultas($trama,$source,$tipo,$usuario,$clave,$url,$host, $XML);

           return $response;
   }#end function


public function getConsultaCarreras($idEstudiante){
        $ws=new AcademicoSoap();
        $tipo       = "3";
        $usuario    = "abc";
        $clave      = "123";
        $source     = "jdbc/procedimientosSaug";
        $url        = "http://192.168.100.11:8080/consultas/ServicioWebConsultas?wsdl";
        $host       = "192.168.100.11:8080";
        $idEstudiante=1;
        $idRol=1;
        $trama      = "<usuario>".$idEstudiante."</usuario><rol>".$idRol."</rol>";
        //$trama      = "<usuario>0924393861rr</usuario><contrasena>sinclave</contrasena>";
        $response=$ws->doRequestSreReceptaTransacionCarreras($trama,$source,$tipo,$usuario,$clave,$url,$host);

        return $response;
                
}#end function


public function getConsultaNotas_act($idFacultad,$idCarrera,$idEstudiante){
        $ws=new AcademicoSoap();
        $tipo       = "5";
        $usuario    = "abc";
        $clave      = "123";
        $source     = "jdbc/procedimientosSaug";
        $url        = "http://192.168.100.11:8080/WSObjetosUg/ServicioWebObjetos?wsdl";
        $host       = "192.168.100.11:8080";
        //$trama      = "<usuario>0924393861rr</usuario><contrasena>sinclave</contrasena>";
        $idEstudiante=1;
        $idCarrera=6;
        $tipoconsulta='A';
        $trama      = "<p_tipoConsulta>".$tipoconsulta."</p_tipoConsulta><p_codUsuario>".$idEstudiante."</p_codUsuario><p_idCarrera>".$idCarrera."</p_idCarrera>";
        $response=$ws->doRequestSreReceptaTransacionnotas_ac($trama,$source,$tipo,$usuario,$clave,$url,$host);
       //var_dump($response);


        return $response;
                
}#end function

public function getConsultaNotas_nh($idFacultad,$idCarrera,$idEstudiante){
        $ws=new AcademicoSoap();
        $tipo       = "3";
        $usuario    = "abc";
        $clave      = "123";
        $source     = "jdbc/procedimientosSaug";
        $url        = "http://192.168.100.11:8080/WSObjetosUg/ServicioWebObjetos?wsdl";
        $host       = "192.168.100.11:8080";
        $idEstudiante=1;
        $idCarrera=3;
        $tipoconsulta='H';
        //$trama      = "<idFacultad>".$idFacultad." </idFacultad><idCarrera>".$idCarrera ." </idCarrera><idEstudiante>".$idEstudiante."</idEstudiante>";
        $trama      = "<p_tipoConsulta>".$tipoconsulta."</p_tipoConsulta><p_codUsuario>".$idEstudiante."</p_codUsuario><p_idCarrera>".$idCarrera."</p_idCarrera>";
        //$trama      = "<usuario>0924393861rr</usuario><contrasena>sinclave</contrasena>";
        $response=$ws->doRequestSreReceptaTransacionnotas_nh($trama,$source,$tipo,$usuario,$clave,$url,$host);
        return $response;
                
}#end function
}#end class
     
	
	
	
	