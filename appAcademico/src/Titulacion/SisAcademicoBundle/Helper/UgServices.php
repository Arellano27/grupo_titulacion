<?php
namespace Titulacion\SisAcademicoBundle\Helper;
include ('AcademicoSoap.php');
 
class UgServices
{

   private $ws;
   private $tipo;
   private $usuario;
   private $clave;
   private $source;
   private $url;
   private $urlConsulta;
   private $urlProcedim;
   public $urlWS;
   private $host;


   public function __construct() {
      $this->ws         = new AcademicoSoap();
      $this->tipo       = "0";
      $this->source     = "";
      /* PARAMETROS PARA SERVIDORES LOCALES EN UNIVERSIDAD - INICIO */
//       $this->usuario         = "abc";
//       $this->clave           = "123";
//       $this->source          = "jdbc/procedimientosSaug";
//       $this->sourceConsultas = "jdbc/consultasSaug";
//       $this->url             = "http://192.168.100.11:8080/";
//       $this->urlConsulta     = "consultas/ServicioWebConsultas?wsdl";
//       $this->urlProcedim     = "WSObjetosUg/ServicioWebObjetos?wsdl";
//       $this->urlWS           = "";
//       $this->host            = "192.168.100.11:8080";
      /* PARAMETROS PARA SERVIDORES LOCALES EN UNIVERSIDAD - FIN */

      /* PARAMETROS PARA SERVIDORES DISPONIBLES EN INTERNET - INICIO */
      $this->usuario       = "CapaVisualPhp";
      $this->clave         = "12CvP2015";
      
      $this->url           = "http://186.101.66.2:8080/";
      
      /*Saug Temporal*/
      $this->source        = "jdbc/saugProcTmp";
      $this->sourceConsultas  = "jdbc/saugConsTmp";
      $this->urlConsulta   = "consultas/ServicioWebConsultas?wsdl";
      $this->urlProcedim   = "WSObjetosUg/ServicioWebObjetos?wsdl";
      
//      /*Preproduccion*/
//      $this->source        = "jdbc/procedimientosSaug";
//      $this->sourceConsultas  = "jdbc/consultasSaug";
//      $this->urlConsulta   = "consultas/ServicioWebConsultas?wsdl";
//      $this->urlProcedim   = "WSObjetosUg/ServicioWebObjetos?wsdl";
//      
      $this->urlWS         = "";
      $this->host          = "186.101.66.2:8080";
      /* PARAMETROS PARA SERVIDORES DISPONIBLES EN INTERNET - FIN */
   }
   
   public function getLogin($username,$password){
      $this->tipo    = "8";
      $this->urlWS   = $this->url.$this->urlProcedim;
      $trama         = "<usuario>".$username."</usuario><contrasena>".$password."</contrasena>";
      $response      = $this->ws->doRequestSreReceptaTransacionProcedimientos($trama,$this->source,$this->tipo,$this->usuario,$this->clave,$this->urlWS,$this->host);

      return $response;
   }#end function

   public function getConsultaNotas($servicio=""){
      $ws=new AcademicoSoap();
      $tipo       = "3";
      $usuario    = "abc";
      $clave      = "123";
      $source     = "jdbc/saugProcTmp";
      $url        = "http://192.168.100.11:8080/WSObjetosUgPre/ServicioWebObjetos?wsdl";
      $host       = "192.168.100.11:8080";
      $trama      = "<usuario>0924393861</usuario><contrasena>sinclave</contrasena>";
      $response=$ws->doRequestSreReceptaTransacionConsultas($trama,$source,$tipo,$usuario,$clave,$url,$host);

      return $response;
   }#end function

   public function Docentes_getCarreras($idDocente){
      $this->tipo    = "3";
      $this->urlWS   = $this->url.$this->urlConsulta;
      $trama      = "<usuario>".$idDocente."</usuario><rol>2</rol>";
      $XML        = NULL;

      $response=$this->ws->doRequestSreReceptaTransacionConsultasdoc($trama,$this->sourceConsultas,$this->tipo,$this->usuario,$this->clave,$this->urlWS,$this->host, $XML);
      return $response;
   }#end function Docentes_getCarreras()


   public function Docentes_getMaterias($idDocente, $idCarrera){
      $this->tipo    = "5";
      $this->urlWS   = $this->url.$this->urlConsulta;
      $trama         = "<usuario>".$idDocente."</usuario><carrera>".$idCarrera."</carrera>";
      $XML           = NULL;
    
      $response=$this->ws->doRequestSreReceptaTransacionConsultasdoc($trama,$this->sourceConsultas,$this->tipo,$this->usuario,$this->clave,$this->urlWS,$this->host,$XML);

      return $response;
   }#end function Docentes_getMaterias()

   public function Docentes_getAsistenciasMaterias($datosConsulta){
      $this->tipo    = "9";
      $datosConsulta["idParalelo"]  = 0;  /* ES NECESARIO PARA LA TRAMA ACTUAL */
      $datosConsulta["ciclo"]       = $datosConsulta["idCarrera"];  /* ESTE REEMPLAZO ES NECESARIO*/
      $this->urlWS   = $this->url.$this->urlProcedim;

      $trama         =  "<fechaInicio>".$datosConsulta["fechaInicio"]."</fechaInicio><fechaFin>".$datosConsulta["fechaFin"]."</fechaFin>".
                        "<idProfesor>".$datosConsulta["idDocente"]."</idProfesor><idMateria>".$datosConsulta["idMateria"]."</idMateria><idParalelo>".$datosConsulta["idParalelo"]."</idParalelo>".
                        "<anio>".$datosConsulta["anio"]."</anio><ciclo>".$datosConsulta["ciclo"]."</ciclo><idCarrera>".$datosConsulta["idCarrera"]."</idCarrera>";
      $XML           = NULL;

      $xmlData["XML_test"] = $XML;
      $xmlData["bloqueRegistros"]   = 'asistencia';
      $xmlData["bloqueSalida"]      = 'px_salida';

      $response=$this->ws->doRequestSreReceptaTransacionObjetos_Registros($trama,$this->source,$this->tipo,$this->usuario,$this->clave,$this->urlWS,$this->host, $xmlData);

      return $response;
   }#end function Docentes_getAsistenciasMaterias()



   public function Docentes_getNotasMaterias($datosConsulta){
      $this->tipo    = "12";

       $datosConsulta["ciclo"]    = 0;    /* ES NECESARIO PARA LA TRAMA ACTUAL */

      $this->urlWS   = $this->url.$this->urlProcedim;

      $trama         =  "<PI_ID_CICLO_DETALLE>".$datosConsulta["ciclo"]."</PI_ID_CICLO_DETALLE>
                        <PI_ID_USUARIO_PROFESOR>".$datosConsulta["idDocente"]."</PI_ID_USUARIO_PROFESOR>
                        <PI_ID_MATERIA>".$datosConsulta["idMateria"]."</PI_ID_MATERIA>";

      $XML           = NULL;

      $xmlData["XML_test"]          = $XML;
      $xmlData["bloqueRegistros"]   = 'registros';
      $xmlData["bloqueSalida"]      = 'px_salida';

      $response   =  $this->ws->doRequestSreReceptaTransacionObjetos_Registros($trama,$this->source,$this->tipo,$this->usuario,$this->clave,$this->urlWS,$this->host, $xmlData);
      return $response;
   }#end function Docentes_getNotasMaterias()


   public function Docentes_getNotasMateriasPorParcial($datosConsulta){
      $this->tipo    = "23";
      $this->urlWS   = $this->url.$this->urlProcedim;
      
      $datosConsulta["ciclo"]    = 0;    /* ES NECESARIO PARA LA TRAMA ACTUAL, SE LO SACA DEL ID_MATERIA_CICLO */

      $trama         =  "<PI_ID_CICLO_DETALLE>".$datosConsulta["ciclo"]."</PI_ID_CICLO_DETALLE>
                        <PI_ID_USUARIO_PROFESOR>".$datosConsulta["idDocente"]."</PI_ID_USUARIO_PROFESOR>
                        <PI_ID_MATERIA>".$datosConsulta["idMateria"]."</PI_ID_MATERIA>
                        <PARCIAL>".$datosConsulta["idParcial"]."</PARCIAL>";;

      $XML           = NULL;

      $xmlData["XML_test"]          = $XML;
      $xmlData["bloqueRegistros"]   = 'registros';
      $xmlData["bloqueSalida"]      = 'px_salida';

      $response   =  $this->ws->doRequestSreReceptaTransacionObjetos_Registros($trama,$this->source,$this->tipo,$this->usuario,$this->clave,$this->urlWS,$this->host, $xmlData);
      return $response;
   }#end function Docentes_getNotasMateriasPorParcial()

   public function Docentes_getParcialesCarrera($datosConsulta){
      $this->tipo    = "19";
      $this->urlWS   = $this->url.$this->urlConsulta;
      
      $trama         = "<carrera>".$datosConsulta["idCarrera"]."</carrera>";
      $XML           = NULL;
    
      $response=$this->ws->doRequestSreReceptaTransacionConsultasdoc($trama,$this->sourceConsultas,$this->tipo,$this->usuario,$this->clave,$this->urlWS,$this->host,$XML);

      return $response;
   }#end function Docentes_getParcialesCarrera()
   
   
       public function Docentes_getAlumnos($idDocente, $idCarrera){
           $ws         = new AcademicoSoap();
           $tipo       = "3";
           $usuario    = "abc";
           $clave      = "123";
           $source     = "jdbc/saugProcTmp";
           $url        = "http://192.168.100.11:8080/WSObjetosUgPre/ServicioWebObjetos?wsdl";
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




           $response=$ws->doRequestSreReceptaTransacionConsultasdoc($trama,$source,$tipo,$usuario,$clave,$url,$host, $XML);

           return $response;
   }#end function


public function getConsultaCarreras($idEstudiante,$idRol){
        $ws=new AcademicoSoap();
        $tipo       = "3";
        $usuario    = "CapaVisualPhp";
        $clave      = "12CvP2015";
        $source     = "jdbc/saugConsTmp";
        $url        = "http://186.101.66.2:8080/consultasTmp/ServicioWebConsultas?wsdl";
        $host       = "186.101.66.2:8080";
        $trama      = "<usuario>".$idEstudiante."</usuario><rol>".$idRol."</rol>";

        $response=$ws->doRequestSreReceptaTransacionCarreras($trama,$source,$tipo,$usuario,$clave,$url,$host);
        return $response;
}#end function


public function getConsultaNotas_act($idFacultad,$idCarrera,$idEstudiante){
        $ws=new AcademicoSoap();
        $tipo       = "11";
        $usuario    = "CapaVisual";
        $clave      = "123";
        $source     = "jdbc/saugProcTmp";
        $url        = "http://192.168.100.11:8080/WSObjetosUgPre/ServicioWebObjetos";
        $host       = "192.168.100.11:8080";
        $tipoconsulta='A';
        $trama      = "<p_tipoConsulta>".$tipoconsulta."</p_tipoConsulta><p_codUsuario>".$idEstudiante."</p_codUsuario><p_idCarrera>".$idCarrera."</p_idCarrera>";
        $response=$ws->doRequestSreReceptaTransacionnotas_ac($trama,$source,$tipo,$usuario,$clave,$url,$host);
        return $response;

}#end function

public function getConsultaNotas_nh($idFacultad,$idCarrera,$idEstudiante){
        $ws=new AcademicoSoap();
        $tipo       = "11";
        $usuario    = "abc";
        $clave      = "123";
        //$source     = "jdbc/saugProcTmp";
        $source     = "jdbc/saugProcTmp";
       // $url        = "http://186.101.66.2:8080/WSObjetosUg/ServicioWebObjetos?wsdl";
        $url        = "http://186.101.66.2:8080/WSObjetosUgPre/ServicioWebObjetos";
        $host       = "186.101.66.2:8080";
       // $idEstudiante=17;
       // $idCarrera=4;
        $tipoconsulta='H';
        //$trama      = "<idFacultad>".$idFacultad." </idFacultad><idCarrera>".$idCarrera ." </idCarrera><idEstudiante>".$idEstudiante."</idEstudiante>";
        $trama      = "<p_tipoConsulta>".$tipoconsulta."</p_tipoConsulta><p_codUsuario>".$idEstudiante."</p_codUsuario><p_idCarrera>".$idCarrera."</p_idCarrera>";
        //$trama      = "<usuario>0924393861rr</usuario><contrasena>sinclave</contrasena>";
        $response=$ws->doRequestSreReceptaTransacionnotas_nh($trama,$source,$tipo,$usuario,$clave,$url,$host);
        return $response;

}#end function

public function getConsultaAlumno_Asistencia($idEstudiante,$idCarrera,$ciclo,$anio){
        $ws=new AcademicoSoap();
        $tipo       = "13";
        $usuario    = "CapaVisual";
        $clave      = "123";
        $source     = "jdbc/saugProcTmp";
        $url        = "http://186.101.66.2:8080/WSObjetosUgPre/ServicioWebObjetos?wsdl";
        $host       = "186.101.66.2:8080";
        $trama      = "<pi_idEstudiante>".$idEstudiante."</pi_idEstudiante><pi_idCarrera>".$idCarrera."</pi_idCarrera>";
        $response=$ws->doRequestSreReceptaTransacionAsistencias($trama,$source,$tipo,$usuario,$clave,$url,$host);
        return $response;

}
public function getConsultaCarreras_Matricula($idEstudiante){
        $ws=new AcademicoSoap();
        $tipo       = "8";
        $usuario    = "CapaVisualPhp";
        $clave      = "12CvP2015";

        $source     = "jdbc/consultasSaug";
        //ip=192.168.100.11
        //publica=192.168.100.11
       $url        = "http://192.168.100.11:8080/consultas/ServicioWebConsultas?wsdl";
        $host       = "192.168.100.11:8080";
        $trama      = "<usuario>".$idEstudiante."</usuario>";
        $response=$ws->doRequestSreReceptaCarrera_Matricula($trama,$source,$tipo,$usuario,$clave,$url,$host);
         
        return $response;     

}#end function

public function getConsultaDatos_Matricula($idEstudiante,$idCarrera,$idCiclo){
        $ws=new AcademicoSoap();
        $tipo       = "17";
        $usuario    = "abc";
        $clave      = "123";
        $source     = "jdbc/procedimientosSaug";
        $url        = "http://192.168.100.11:8080/WSObjetosUg/ServicioWebObjetos?wsdl";
        $host       = "192.168.100.11:8080";
        $trama      = "<pi_id_estudiante>".$idEstudiante."</pi_id_estudiante><pi_id_carrera>".$idCarrera."</pi_id_carrera><pi_id_ciclodetalle>".$idCiclo."</pi_id_ciclodetalle>";
        $response=$ws->doRequestSreReceptaTransacion_matriculacion($trama,$source,$tipo,$usuario,$clave,$url,$host);

        
        return $response;  

}#end function

public function setMatricula_Estudiante($trama){
        $ws=new AcademicoSoap();
        $tipo       = "15";
        $usuario    = "CapaVisualPhp";
        $clave      = "12CvP2015";
        $source     = "jdbc/procedimientosSaug";
        $url        = "http://192.168.100.11:8080/WSObjetosUg/ServicioWebObjetos?wsdl";
        $host       = "192.168.100.11";
        //$trama      = "<usuario>".$idEstudiante."</usuario><rol>".$idRol."</rol>";
        $response=$ws->doSetMatricula($trama,$source,$tipo,$usuario,$clave,$url,$host);
        return $response;
}#end function
public function getConsultaDatos_Turno($idEstudiante,$idCarrera){
        $ws=new AcademicoSoap();
        $tipo       = "7";
        $usuario    = "CapaVisualPhp";
        $clave      = "12CvP2015";
        $source     = "jdbc/consultasSaug";
        $url        = "http://192.168.100.11:8080/consultas/ServicioWebConsultas?wsdl";
        $host       = "192.168.100.11:8080";
        $trama      = "<usuario>".$idEstudiante."</usuario><carrera>".$idCarrera."</carrera>";
        $response=$ws->doRequestSreReceptaTransacionTurno($trama,$source,$tipo,$usuario,$clave,$url,$host);
        return $response; 
          
}#end function

public function getConsultaRegistro_Matricula($idEstudiante,$idCarrera,$idCiclo){
        $ws=new AcademicoSoap();
        $tipo       = "7";
        $usuario    = "CapaVisualPhp";
        $clave      = "12CvP2015";
        $source     = "jdbc/consultasSaug";
        $url        = "http://192.168.100.11:8080/consultas/ServicioWebConsultas?wsdl";
        $host       = "192.168.100.11:8080";
        $trama      = "<usuario>".$idEstudiante."</usuario><carrera>".$idCarrera."</carrera><idciclo>".$idCarrera."</idciclo>";
        $response=$ws->doRequestSreReceptaTransacionRegistroMatricula($trama,$source,$tipo,$usuario,$clave,$url,$host);
        return $response; 
            
}#end function
}#end class




