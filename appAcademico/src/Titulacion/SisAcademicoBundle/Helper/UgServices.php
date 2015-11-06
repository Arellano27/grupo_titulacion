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
   public  $urlWS;
   private $host;
   private $sourceConsultas;


   public function __construct() {
      $this->ws         = new AcademicoSoap();
      $this->tipo       = "0";
      $this->source     = "";
      /* PARAMETROS PARA SERVIDORES LOCALES EN UNIVERSIDAD - INICIO */

//      $this->usuario       = "CapaVisualPhp";
//      $this->clave         = "T3pZx1520pHp";
//      $this->source        = "jdbc/procedimientosPre";
//      $this->url           = "http://186.101.66.2:8080/";
//      //$this->url           = "http://192.168.100.11:8080/";
//      $this->urlConsulta     = "consultas/ServicioWebConsultas?wsdl";
//      $this->urlProcedim     = "WSObjetosUg/ServicioWebObjetos?wsdl";
//      $this->urlWS           = "";
//      $this->host          = "186.101.66.2:8080";
//      //$this->host            = "192.168.100.11:8080";
//      $this->sourceConsultas = "jdbc/procedimientosPre";



//       $this->usuario         = "abc";
//       $this->clave           = "123";
//       $this->source          = "jdbc/procedimientosSaug";
//       $this->sourceConsultas = "jdbc/consultasSaug";
//       $this->url             = "http://192.168.100.11:8080/";
//       $this->urlConsulta     = "consultas/ServicioWebConsultas?wsdl";
//       $this->urlProcedim     = "WSObjetosUg/ServicioWebObjetos?wsdl";
//       $this->urlWS           = "";
//       $this->host            = "192.168.100.11:8080";
//      /* PARAMETROS PARA SERVIDORES LOCALES EN UNIVERSIDAD - FIN */


      /* PARAMETROS PARA SERVIDORES DISPONIBLES EN INTERNET - INICIO */
//      $this->usuario       = "CapaVisualPhp";
//      $this->clave         = "12CvP2015";
//
//      $this->url           = "http://186.101.66.2:8080/";
//
//      /*Saug Temporal*/
//      $this->source        = "jdbc/saugProcTmp";
//      $this->sourceConsultas  = "jdbc/saugConsTmp";
//      $this->urlConsulta   = "consultas/ServicioWebConsultas?wsdl";
//      $this->urlProcedim   = "WSObjetosUg/ServicioWebObjetos?wsdl";

      // $this->usuario         = "abc";
      // $this->clave           = "123";


    //  $this->usuario       = "abc";
    //  $this->clave         = "123";
    //  $this->source        = "jdbc/procedimientosSaug";
    //  //$this->source        = "jdbc/saugProcTmp";
    //  //$this->url           = "http://186.101.66.2:8080/";
    // $this->url           = "http://192.168.100.11:8080/";
    //  $this->urlConsulta   = "consultas/ServicioWebConsultas?wsdl";
    //  $this->urlProcedim   = "WSObjetosUg/ServicioWebObjetos?wsdl";
    //  $this->urlWS         = "";
    //  //$this->host          = "186.101.66.2:8080";
    //  $this->host          = "192.168.100.11:8080";
    //  $this->sourceConsultas= "jdbc/consultasSaug";
    //  //$this->sourceConsultas= "jdbc/saugConsTmp";


      // $this->usuario         = "CapaVisualPhp";
      // $this->clave           = "T3pZx1520pHp";


      // $this->source          = "jdbc/procedimientosSaug";
      // $this->sourceConsultas = "jdbc/consultasSaug";
      // $this->url             = "http://192.168.100.11:8080/";
      // $this->urlConsulta     = "consultas/ServicioWebConsultas?wsdl";
      // $this->urlProcedim     = "WSObjetosUg/ServicioWebObjetos?wsdl";
      // $this->urlWS           = "";
      // $this->host            = "192.168.100.11:8080";


      /* PARAMETROS PARA SERVIDORES LOCALES EN UNIVERSIDAD - FIN */

      /* PARAMETROS PARA SERVIDORES DISPONIBLES EN INTERNET - INICIO */

//      $this->usuario       = "usr_tesis";
//      $this->clave         = "Tesis2015";
//      $this->url           = "http://186.101.66.2:8080/";
//      /*Saug Temporal*/
//      // $this->source           = "jdbc/saugProcTmp";
//      // $this->sourceConsultas  = "jdbc/saugConsTmp";
//      $this->urlConsulta   = "consultas/ServicioWebConsultas?wsdl";
//      $this->urlProcedim   = "WSObjetosUg/ServicioWebObjetos?wsdl";
//      $this->host          = "186.101.66.2:8080";
//
//// //      /*Preproduccion*/
//     $this->source        = "jdbc/procedimientosSaug";
//     $this->sourceConsultas  = "jdbc/consultasSaug";

//      $this->usuario         = "abc";
//      $this->clave           = "123";
//      $this->source          = "jdbc/procedimientosSaug";
//      $this->sourceConsultas = "jdbc/consultasSaug";
////      $this->url             = "http://192.168.100.11:8080/";
////      $this->urlConsulta     = "consultas/ServicioWebConsultas?wsdl";
////      $this->urlProcedim     = "WSObjetosUg/ServicioWebObjetos?wsdl";
////      $this->urlWS           = "";
////      $this->host            = "192.168.100.11:8080";
//      /* PARAMETROS PARA SERVIDORES LOCALES EN UNIVERSIDAD - FIN */
//
//      /* PARAMETROS PARA SERVIDORES DISPONIBLES EN INTERNET - INICIO */
//       //$this->usuario       = "usr_tesis";
//       //$this->clave         = "Tesis2015";
//       $this->url           = "http://186.101.66.2:8080/";
//       /*Saug Temporal*/
//       //$this->source           = "jdbc/saugProcTmp";
//       //$this->sourceConsultas  = "jdbc/saugConsTmp";
//       $this->urlConsulta   = "consultas/ServicioWebConsultas?wsdl";
//       $this->urlProcedim   = "WSObjetosUg/ServicioWebObjetos?wsdl";
//       $this->host          = "186.101.66.2:8080";
//
//      /* PARAMETROS PARA SERVIDORES LOCALES EN UNIVERSIDAD - FIN */
//
//
//      /* PARAMETROS PARA SERVIDORES DISPONIBLES EN INTERNET - INICIO */
      $this->usuario       = "CapaVisualPhp";
      $this->clave         = "12CvP2015";
      $this->url           = "http://186.101.66.2:8080/";
      /*Saug Temporal*/
      $this->source        = "jdbc/saugProcTmp";
      $this->sourceConsultas  = "jdbc/saugConsTmp";
      $this->urlConsulta   = "consultas/ServicioWebConsultas?wsdl";
      $this->urlProcedim   = "WSObjetosUg/ServicioWebObjetos?wsdl";
      $this->urlWS         = "";
      $this->host          = "186.101.66.2:8080";
//
//
//// // //      /*Preproduccion*/
//     $this->source        = "jdbc/procedimientosSaug";
//      $this->sourceConsultas  = "jdbc/consultasSaug";
////
//     $this->urlConsulta   = "consultas/ServicioWebConsultas?wsdl";
//    $this->urlProcedim   = "WSObjetosUg/ServicioWebObjetos?wsdl";
//
//    $this->usuario       = "usr_tesis";
//     $this->clave         = "Tesis2015";
//      $this->url           = "http://186.101.66.2:8080/";
////
//      /*Saug Temporal*/
//      $this->source        = "jdbc/saugProcTmp";
//      $this->sourceConsultas  = "jdbc/saugConsTmp";
//      $this->urlConsulta   = "consultas/ServicioWebConsultas?wsdl";
//      $this->urlProcedim   = "WSObjetosUg/ServicioWebObjetos?wsdl";
//      $this->urlWS         = "";
//      $this->host          = "186.101.66.2:8080";


// //      /*Preproduccion*/
     // $this->source        = "jdbc/procedimientosSaug";
     // $this->sourceConsultas  = "jdbc/consultasSaug";
     // $this->urlConsulta   = "consultas/ServicioWebConsultas?wsdl";
     // $this->urlProcedim   = "WSObjetosUg/ServicioWebObjetos?wsdl";

// //
//       $this->urlWS         = "";


      /*Saug Temporal*/
      //      /*Preproduccion*/
      // $this->source        = "jdbc/procedimientosSaug";
      // $this->sourceConsultas  = "jdbc/consultasSaug";

//      $this->urlConsulta   = "consultas/ServicioWebConsultas?wsdl";
//      $this->urlProcedim   = "WSObjetosUg/ServicioWebObjetos?wsdl";

//
//      $this->urlWS         = "";
//      $this->host          = "186.101.66.2:8080";

      /* PARAMETROS PARA SERVIDORES DISPONIBLES EN INTERNET - FIN */
   }

   public function getLogin($username,$password){
       var_dump($username);
      $this->tipo    = "8";
      $this->urlWS   = $this->url.$this->urlProcedim;
      $trama         = "<usuario>".$username."</usuario><contrasena>".$password."</contrasena>";
      
      $response      = $this->ws->doRequestSreReceptaTransacionProcedimientos($trama,$this->source,$this->tipo,$this->usuario,$this->clave,$this->urlWS,$this->host);
// echo '<pre>'; var_dump($response); exit();
      return $response;
   }#end function

   public function getConsultaNotas($servicio=""){

      $this->tipo    = "3";
      $this->urlWS   = $this->url.$this->urlProcedim;
      $trama         = "<usuario>0924393861</usuario><contrasena>sinclave</contrasena>";
      $response      = $this->ws->doRequestSreReceptaTransacionConsultas($trama,$this->source,$this->tipo,$this->usuario,$this->clave,$this->urlWS,$this->host);

      return $response;
   }#end function

   public function Docentes_getCarreras($idDocente){
      $this->tipo    = "3";
      $this->source  = $this->sourceConsultas;

      $this->urlWS   = $this->url.$this->urlConsulta;
      $trama      = "<usuario>".$idDocente."</usuario><rol>2</rol>";
      $XML        = NULL;

      $response=$this->ws->doRequestSreReceptaTransacionConsultasdoc($trama,$this->sourceConsultas,$this->tipo,$this->usuario,$this->clave,$this->urlWS,$this->host, $XML);
      return $response;
   }#end function Docentes_getCarreras()


   public function Docentes_getMaterias($idDocente, $idCarrera){
      $this->tipo    = "5";

      $this->source  = $this->sourceConsultas;

      $this->urlWS   = $this->url.$this->urlConsulta;
      $trama         = "<usuario>".$idDocente."</usuario><carrera>".$idCarrera."</carrera>";
      $XML           = NULL;
      $response=$this->ws->doRequestSreReceptaTransacionConsultasdoc($trama,$this->sourceConsultas,$this->tipo,$this->usuario,$this->clave,$this->urlWS,$this->host,$XML);

      return $response;
   }#end function Docentes_getMaterias()

   public function Docentes_getAsistenciasMaterias($datosConsulta){
      $this->tipo    = "9";
      $this->urlWS   = $this->url.$this->urlProcedim;
      $datosConsulta["idParalelo"]  = 0;  /* ES NECESARIO PARA LA TRAMA ACTUAL */
      $datosConsulta["ciclo"]       = $datosConsulta["idCarrera"];  /* ESTE REEMPLAZO ES NECESARIO*/
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
     $this->urlWS   = $this->url.$this->urlProcedim;
       $datosConsulta["ciclo"]    = 0;    /* ES NECESARIO PARA LA TRAMA ACTUAL */

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

public function getConsultaCarreras($idEstudiante,$idRol){
  // echo '<pre>'; var_dump($idEstudiante,$idRol); exit();
        $this->tipo    = "3";
        $this->urlWS   = $this->url.$this->urlConsulta;
        /*$ws=new AcademicoSoap();
        $tipo       = "3";
        $usuario    = "CapaVisualPhp";
        $clave      = "12CvP2015";
        $source     = "jdbc/saugConsTmp";
        $url        = "http://186.101.66.2:8080/consultasTmp/ServicioWebConsultas?wsdl";
        $host       = "186.101.66.2:8080";
        $trama      = "<usuario>".$idEstudiante."</usuario><rol>".$idRol."</rol>";*/
        // $trama = "<usuario>";
         $trama      = "<usuario>".$idEstudiante."</usuario> <rol> ".$idRol." </rol>";
// echo 'aa<pre>'; var_dump($trama); exit();
        //$response=$ws->doRequestSreReceptaTransacionCarreras($trama,$this->source,$this->tipo,$usuario,$clave,$url,$host);
        $response=$this->ws->doRequestSreReceptaTransacionCarreras($trama,$this->sourceConsultas,$this->tipo,$this->usuario,$this->clave,$this->urlWS,$this->host);
        return $response;

}#end function


public function getConsultaNotas_act($idFacultad,$idCarrera,$idEstudiante){
       /* $ws=new AcademicoSoap();
        $tipo       = "11";
        $usuario    = "CapaVisual";
        $clave      = "123";
        $source     = "jdbc/saugProcTmp";
        $url        = "http://192.168.100.11:8080/WSObjetosUgPre/ServicioWebObjetos";
        $host       = "192.168.100.11:8080";*/

        $this->tipo       = "11";
        $this->urlWS   = $this->url.$this->urlProcedim;
        $tipoconsulta='A';
        $trama      = "<p_tipoConsulta>".$tipoconsulta."</p_tipoConsulta><p_codUsuario>".$idEstudiante."</p_codUsuario><p_idCarrera>".$idCarrera."</p_idCarrera>";
       $response=$this->ws->doRequestSreReceptaTransacionnotas_ac($trama,$this->source,$this->tipo,$this->usuario,$this->clave,$this->urlWS,$this->host);
        return $response;

}#end function

public function getConsultaNotas_nh($idFacultad,$idCarrera,$idEstudiante){
    /*    $ws=new AcademicoSoap();
        $tipo       = "11";
        $usuario    = "abc";
        $clave      = "123";

        $source     = "jdbc/saugProcTmp";
        $url        = "http://186.101.66.2:8080/WSObjetosUgPre/ServicioWebObjetos";
        $host       = "186.101.66.2:8080";*/

        $this->tipo       = "11";
        $this->urlWS   = $this->url.$this->urlProcedim;

        $tipoconsulta='H';
        $trama      = "<p_tipoConsulta>".$tipoconsulta."</p_tipoConsulta><p_codUsuario>".$idEstudiante."</p_codUsuario><p_idCarrera>".$idCarrera."</p_idCarrera>";
        $response=$this->ws->doRequestSreReceptaTransacionnotas_nh($trama,$this->source,$this->tipo,$this->usuario,$this->clave,$this->urlWS,$this->host);
        return $response;

}#end function

public function getConsultaAlumno_Asistencia($idEstudiante,$idCarrera,$ciclo,$anio){
        /*$ws=new AcademicoSoap();
        $tipo       = "13";
        $usuario    = "CapaVisual";
        $clave      = "123";

        $source     = "jdbc/saugProcTmp";
        $url        = "http://186.101.66.2:8080/WSObjetosUgPre/ServicioWebObjetos?wsdl";
        $host       = "186.101.66.2:8080";*/

        $this->tipo = "13";
        $this->urlWS   = $this->url.$this->urlProcedim;

        $trama      = "<pi_idEstudiante>".$idEstudiante."</pi_idEstudiante><pi_idCarrera>".$idCarrera."</pi_idCarrera>";
        $response=$this->ws->doRequestSreReceptaTransacionAsistencias($trama,$this->source,$this->tipo,$this->usuario,$this->clave,$this->urlWS,$this->host);
        return $response;

}
public function getConsultaCarreras_Matricula($idEstudiante){
        /*$ws=new AcademicoSoap();
        $tipo       = "8";
        $usuario    = "CapaVisualPhp";
        $clave      = "12CvP2015";

        $source     = "jdbc/consultasSaug";
        $url        = "http://192.168.100.11:8080/consultas/ServicioWebConsultas?wsdl";
        $host       = "192.168.100.11:8080";*/
        $this->tipo       = "8";
        $this->urlWS   = $this->url.$this->urlConsulta;
        $trama      = "<usuario>".$idEstudiante."</usuario>";
        //$response=$ws->doRequestSreReceptaCarrera_Matricula($trama,$source,$tipo,$usuario,$clave,$url,$host);
         $response=$this->ws->doRequestSreReceptaCarrera_Matricula($trama,$this->sourceConsultas,$this->tipo,$this->usuario,$this->clave,$this->urlWS,$this->host);
        return $response;
}#end function

public function getConsultaDatos_Matricula($idEstudiante,$idCarrera,$idCiclo){
        /*$ws=new AcademicoSoap();
        $tipo       = "17";
        $usuario    = "abc";
        $clave      = "123";
        $source     = "jdbc/procedimientosSaug";
        $url        = "http://192.168.100.11:8080/WSObjetosUg/ServicioWebObjetos?wsdl";
        $host       = "192.168.100.11:8080";*/
        $this->tipo       = "17";
        $this->urlWS   = $this->url.$this->urlProcedim;
        $trama      = "<pi_id_estudiante>".$idEstudiante."</pi_id_estudiante><pi_id_carrera>".$idCarrera."</pi_id_carrera><pi_id_ciclodetalle>".$idCiclo."</pi_id_ciclodetalle>";
        //$response=$ws->doRequestSreReceptaTransacion_matriculacion($trama,$this->source,$this->tipo,$usuario,$clave,$url,$host);
        $response=$this->ws->doRequestSreReceptaTransacion_matriculacion($trama,$this->source,$this->tipo,$this->usuario,$this->clave,$this->urlWS,$this->host);

        return $response;

}#end function

public function getConsultaDatos_Anulacion($idEstudiante,$idCarrera,$idCiclo,$idMateria){
       /* $ws=new AcademicoSoap();
        $tipo       = "7";
        $usuario    = "CapaVisualPhp";
        $clave      = "12CvP2015";
        $source     = "jdbc/consultasSaug";
        $url        = "http://192.168.100.11:8080/consultas/ServicioWebConsultas?wsdl";
        $host       = "192.168.100.11:8080";*/
        $this->tipo       = "19";
        $this->urlWS   = $this->url.$this->urlProcedim;
        $trama      = "<PI_ID_ESTUDIANTE>".$idEstudiante."</PI_ID_ESTUDIANTE><PI_ID_CARRERA>".$idCarrera."</PI_ID_CARRERA><PI_ID_CICLO>".$idCiclo."</PI_ID_CICLO><PI_ID_MATERIA>".$idMateria."</PI_ID_MATERIA>";
        //$response=$ws->doRequestSreReceptaTransacionRegistroMatricula($trama,$source,$tipo,$usuario,$clave,$url,$host);

        $response=$this->ws->doRequestSreReceptaTransacionAnulacionMaterias($trama,$this->source,$this->tipo,$this->usuario,$this->clave,$this->urlWS,$this->host);
        return $response;


}#end function


public function setMatricula_Estudiante($trama){
        /*$ws=new AcademicoSoap();
        $tipo       = "15";
        $usuario    = "CapaVisualPhp";
        $clave      = "12CvP2015";
        $source     = "jdbc/procedimientosSaug";
        $url        = "http://192.168.100.11:8080/WSObjetosUg/ServicioWebObjetos?wsdl";
        $host       = "192.168.100.11";*/

        //$trama      = "<usuario>".$idEstudiante."</usuario><rol>".$idRol."</rol>";
        $this->tipo       = "15";
        $this->urlWS   = $this->url.$this->urlProcedim;
        //$response=$ws->doSetMatricula($trama,$source,$tipo,$usuario,$clave,$url,$host);
        $response=$this->ws->doSetMatricula($trama,$this->source,$this->tipo,$this->usuario,$this->clave,$this->urlWS,$this->host);
        return $response;
}#end function
public function getConsultaDatos_Turno($idEstudiante,$idCarrera){
        /* $ws=new AcademicoSoap();
        $tipo       = "7";
        $usuario    = "CapaVisualPhp";
        $clave      = "12CvP2015";
        $source     = "jdbc/consultasSaug";
        $url        = "http://192.168.100.11:8080/consultas/ServicioWebConsultas?wsdl";
        $host       = "192.168.100.11:8080";*/

        $this->tipo       = "7";
        $this->urlWS   = $this->url.$this->urlConsulta;
        $trama      = "<usuario>".$idEstudiante."</usuario><carrera>".$idCarrera."</carrera>";
        //$response=$ws->doRequestSreReceptaTransacionTurno($trama,$source,$tipo,$usuario,$clave,$url,$host);
        $response=$this->ws->doRequestSreReceptaTransacionTurno($trama,$this->sourceConsultas,$this->tipo,$this->usuario,$this->clave,$this->urlWS,$this->host);
        return $response;

}#end function

public function getConsultaRegistro_Matricula($idEstudiante,$idCarrera,$idCiclo){
       /* $ws=new AcademicoSoap();
        $tipo       = "7";
        $usuario    = "CapaVisualPhp";
        $clave      = "12CvP2015";
        $source     = "jdbc/consultasSaug";
        $url        = "http://192.168.100.11:8080/consultas/ServicioWebConsultas?wsdl";
        $host       = "192.168.100.11:8080";*/
        $this->tipo       = "20";
        $this->urlWS   = $this->url.$this->urlProcedim;
        $trama      = "<PI_ID_ESTUDIANTE>".$idEstudiante."</PI_ID_ESTUDIANTE><PI_ID_CARRERA>".$idCarrera."</PI_ID_CARRERA><PI_ID_CICLO>".$idCiclo."</PI_ID_CICLO>";
        //$response=$ws->doRequestSreReceptaTransacionRegistroMatricula($trama,$source,$tipo,$usuario,$clave,$url,$host);
        $response=$this->ws->doRequestSreReceptaTransacionRegistroMatricula($trama,$this->source,$this->tipo,$this->usuario,$this->clave,$this->urlWS,$this->host);
        return $response;

  }#end function

//-----------------------------------------------------------------------------------------------------------------------------//
/*INICIO - ARELLANO SPRINT 4*/
    public function getConsultaCorreo($login){
    $this->tipo      = "10";
    $this->urlWS   = $this->url.$this->urlConsulta;
    $trama      = "<usuario>".$login."</usuario>";
    $XML        = NULL;
    $response=$this->ws->doRequestSreReceptaTransacionConsultasCorreo($trama,$this->sourceConsultas,$this->tipo,$this->usuario,$this->clave,$this->urlWS,$this->host, $XML);
    return $response;
  }#end function para obtener el correo del usuario()

  public function mantenimientoUsuario($username,$password,$idUsuario,$estado,$nuevoPassword,$opcion){
    //$ws=new AcademicoSoap();
    $this->tipo   = "21";
    $this->urlWS  = $this->url.$this->urlProcedim;
    $trama        = "<PX_XML><items><item><usuario>".$username."</usuario><contrasena>".$password."</contrasena><id_usuario>".$idUsuario."</id_usuario><estado>".$estado."</estado><nuevacontrasenia>".$nuevoPassword."</nuevacontrasenia></item></items></PX_XML><PC_OPCION>".$opcion."</PC_OPCION>";
    $response     = $this->ws->doRequestSreReceptaTransacionMantUsuario($trama,$this->source,$this->tipo,$this->usuario,$this->clave,$this->urlWS,$this->host);
     //pruebas
    return $response;

  }#end function

  /*FIN - ARELLANO SPRINT 4*/
  //-----------------------------------------------------------------------------------------------------------------------------//

public function getgeneraTurno($idEstudiante,$idCarrera,$idCiclo){
       /* $ws=new AcademicoSoap();
        $tipo       = "7";
        $usuario    = "CapaVisualPhp";
        $clave      = "12CvP2015";
        $source     = "jdbc/consultasSaug";
        $url        = "http://192.168.100.11:8080/consultas/ServicioWebConsultas?wsdl";
        $host       = "192.168.100.11:8080";*/
        $this->tipo       = "27";
        $this->urlWS   = $this->url.$this->urlProcedim;
        $trama      = "<PI_ID_USUARIO_ESTUDIANTE>".$idEstudiante."</PI_ID_USUARIO_ESTUDIANTE><PI_ID_CARRERA>".$idCarrera."</PI_ID_CARRERA><PI_ID_CICLO_DETALLE>".$idCiclo."</PI_ID_CICLO_DETALLE>";
        //$response=$ws->doRequestSreReceptaTransacionRegistroMatricula($trama,$source,$tipo,$usuario,$clave,$url,$host);
        $response=$this->ws->doGeneraTurno($trama,$this->source,$this->tipo,$this->usuario,$this->clave,$this->urlWS,$this->host);
        return $response;

}#end function




public function Mensajes_Enviados($idUsuario){

    
           $this->tipo       = "11";
           $this->urlWS   = $this->url.$this->urlConsulta;         
           $trama      = "<id_recepcion>".$idUsuario."</id_recepcion>";
           $response=$this->ws->doRequestSreReceptaTransacionConsultasMensajesEnviados($trama,$this->sourceConsultas,$this->tipo,$this->usuario,$this->clave,$this->urlWS,$this->host);
           return $response;
           
 }#end function

public function Mensajes_No_Leidos($idUsuario){
    
           $this->tipo       = "13";
           $this->urlWS   = $this->url.$this->urlConsulta;         
           $trama      = "<id_recepcion>".$idUsuario."</id_recepcion>";
           $response=$this->ws->doRequestSreReceptaTransacionConsultasMensajesNoLeidos($idUsuario,$this->sourceConsultas,$this->tipo,$this->usuario,$this->clave,$this->urlWS,$this->host);
           return $response;
           
 }#end function
 
 public function Eventos_Recividos($idUsuario){
    
           $this->tipo       = "17";
           $this->urlWS   = $this->url.$this->urlConsulta;         
           $trama      = "<id_recepcion>".$idUsuario."</id_recepcion>";
           $response=$this->ws->doRequestSreReceptaTransacionConsultasEventosNoLeidos($idUsuario,$this->sourceConsultas,$this->tipo,$this->usuario,$this->clave,$this->urlWS,$this->host);
           return $response;
           
 }#end function
 
  public function Notificaciones_Recividas($idUsuario){
    
           $this->tipo       = "15";
           $this->urlWS   = $this->url.$this->urlConsulta;         
           $trama      = "<id_recepcion>".$idUsuario."</id_recepcion>";
           $response=$this->ws->doRequestSreReceptaTransacionConsultasNotificcionesNoLeidos($idUsuario,$this->sourceConsultas,$this->tipo,$this->usuario,$this->clave,$this->urlWS,$this->host);
           return $response;
           
 }#end function
 
	

//           $ws         = new AcademicoSoap();
//           $tipo       = "3";
//           $usuario    = "abc";
//           $clave      = "123";
//           $source     = "jdbc/procedimientosSaug";
////           $url        = "http://192.168.100.11:8080/WSObjetosUg/ServicioWebObjetos?wsdl";
////           $host       = "192.168.100.11:8080";
//           $url  = "";
//           $host = "";
//           $trama      = "<idDocente>".$idUsuario."</idDocente>";
//
//
//               $XML        = <<<XML
//<soap:Envelope
//xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
//<soap:Body>
//<ns2:ejecucionConsultaResponse
//xmlns:ns2="http://servicios.ug.edu.ec/">
//<return>
//    <codigoRespuesta>0</codigoRespuesta>
//    <estado>F</estado>
//    <idHistorico>1089</idHistorico>
//    <mensajeRespuesta>ok</mensajeRespuesta>
//    <respuestaConsulta>
//        <Mensajes>
//            <Tipo>
//                    Mensaje
//            </Tipo>
//            <Asunto>
//                   Semestre Ciclo 1
//            </Asunto>
//            <Detalle>
//                    Empiezan Clases
//            </Detalle>
//            <Fecha>
//                    12/12/12
//            </Fecha>
//        </Mensajes>
//    </respuestaConsulta>
//</return>
//</ns2:ejecucionConsultaResponse>
//</soap:Body>
//</soap:Envelope>
//XML;
//
//           $response=$ws->doRequestSreReceptaTransacionConsultas($trama,$source,$tipo,$usuario,$clave,$url,$host, $XML);
//
//           return $response;
//   }#end function






public function getConsultaRegistro_OrdenPago($idEstudiante,$idCarrera,$idCiclo){
       /* $ws=new AcademicoSoap();
        $tipo       = "7";
        $usuario    = "CapaVisualPhp";
        $clave      = "12CvP2015";
        $source     = "jdbc/consultasSaug";
        $url        = "http://192.168.100.11:8080/consultas/ServicioWebConsultas?wsdl";
        $host       = "192.168.100.11:8080";*/
        $this->tipo       = "25";
        $this->urlWS   = $this->url.$this->urlProcedim;
        $trama      = "<PI_ID_SG_USUARIO_ESTUDIANTE>".$idEstudiante."</PI_ID_SG_USUARIO_ESTUDIANTE><PI_ID_CICLO_DETALLE>".$idCiclo."</PI_ID_CICLO_DETALLE><PI_ID_CARRERA>".$idCarrera."</PI_ID_CARRERA>";
        //$response=$ws->doRequestSreReceptaTransacionRegistroMatricula($trama,$source,$tipo,$usuario,$clave,$url,$host);
        $response=$this->ws->doRequestSreReceptaTransacionRegistroOrdenPago($trama,$this->source,$this->tipo,$this->usuario,$this->clave,$this->urlWS,$this->host);
        return $response;

}#end function


/**
 * [stalin-caiche: cosnsulta de eventos academicos]
 */
public function getConsultaSoloEventos($idEventos){
  $this->tipo       = "22";
  $this->urlWS   = $this->url.$this->urlConsulta;
  $trama      = "<catalogo>".$idEventos."</catalogo>";

  $response = $this->ws->doRequestReceptaSoloEventosAcademicos($trama,$this->sourceConsultas,$this->tipo,$this->usuario,$this->clave,$this->urlWS,$this->host);
  return $response;
}#end function

//INSCRIPCION ADMIN
public function getConsultaCarrerasInscripcion($idUsuario,$idRol)
{
        $this->tipo    = "3";
        $this->urlWS   = $this->url.$this->urlConsulta;
        $trama      = "<usuario>".$idUsuario."</usuario><rol>".$idRol."</rol>";
        $response=$this->ws->doRequestSreReceptaTransacionCarrerasInscripcion($trama,$this->sourceConsultas,$this->tipo,$this->usuario,$this->clave,$this->urlWS,$this->host);
        return $response;
}#end function
public function getConsulta_listado_inscripcion($idEstudiante,$idCarrera,$idCiclo)
{
        $this->tipo       = "36";
        $this->urlWS   = $this->url.$this->urlProcedim;
        $trama      = "<PV_ID_ESTUDIANTE>".$idEstudiante."</PV_ID_ESTUDIANTE><PI_ID_CARRERA>".$idCarrera."</PI_ID_CARRERA><PI_ID_CICLO>".$idCiclo."</PI_ID_CICLO>";
        $response=$this->ws->doRequestsListarInscripcion($trama,$this->source,$this->tipo,$this->usuario,$this->clave,$this->urlWS,$this->host);
        return $response; 
            
}#end function
public function setActualizaInscripcion($trama){
        $this->tipo       = "28";
        $this->urlWS   = $this->url.$this->urlProcedim;
        $response=$this->ws->doSetActualizaInscripcion($trama,$this->source,$this->tipo,$this->usuario,$this->clave,$this->urlWS,$this->host);
        return $response;
}#end function

//ANULACION ADMIN
public function getConsultaCarrerasAnulacion($idUsuario,$idRol)
{
        $this->tipo    = "3";
        $this->urlWS   = $this->url.$this->urlConsulta;
        $trama      = "<usuario>".$idUsuario."</usuario><rol>".$idRol."</rol>";
        $response=$this->ws->doRequestSreReceptaTransacionCarrerasAnulacion($trama,$this->sourceConsultas,$this->tipo,$this->usuario,$this->clave,$this->urlWS,$this->host);
        return $response;
}#end function
public function getConsulta_listado_anulacion($fechaInicio,$fechaFin,$idCarrera,$tipo_solicitud)
{
        $this->tipo    = "25";
        $this->urlWS   = $this->url.$this->urlConsulta;
        $trama      = "<fecha_desde>".$fechaInicio."</fecha_desde><fecha_hasta>".$fechaFin."</fecha_hasta><carrera>".$idCarrera."</carrera><tipo_solicitud>".$tipo_solicitud."</tipo_solicitud>";
        $response=$this->ws->doRequestSreReceptaTransacionAnulacion_Listar($trama,$this->sourceConsultas,$this->tipo,$this->usuario,$this->clave,$this->urlWS,$this->host);
        return $response;
}#end function
public function getConsulta_listado_anulacion_detalle($id_sa_solicitud,$id_tipo_solicitud){
        $this->tipo       = "34";
        $this->urlWS   = $this->url.$this->urlProcedim;
        $trama      = "<solicitud>".$id_sa_solicitud."</solicitud><id_tipo_solicitud>".$id_tipo_solicitud."</id_tipo_solicitud>";
        $response=$this->ws->doSetListado_Anulacion_Detalle($trama,$this->source,$this->tipo,$this->usuario,$this->clave,$this->urlWS,$this->host);
        return $response;
}#end function

 public function Docentes_gettareaxparcial($trama){
           $ws         = new AcademicoSoap();
           $tipo       = "24";
           $usuario    = $this->usuario;
           $clave      = $this->clave;
           $source     = $this->source;
           $url        = $this->url.$this->urlProcedim;
           $host       = $this->host;



               $XML        = <<<XML
<soap:Envelope xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
   <soap:Body>
      <ns2:ejecucionObjetoResponse xmlns:ns2="http://servicios.ug.edu.ec/">
         <return>
            <codigoRespuesta>0</codigoRespuesta>
            <estado>F</estado>
            <idHistorico>152821</idHistorico>
            <mensajeRespuesta>ok</mensajeRespuesta>
            <resultadoObjeto>
               <parametrosSalida>
                  <PX_SALIDA><![CDATA[<registros><registro><cantParciales>1</cantParciales><notaMinima>6.50</notaMinima><periodos><periodo><parcial>PARCIAL1</parcial><totalizar>SI</totalizar><componentePeriodo><idNota>51</idNota><componente>GESTIÓN FORMATIVA</componente><idNota>52</idNota><componente>GESTIÓN PRÁCTICA</componente><idNota>53</idNota><componente>ACREDITACIÓN</componente></componentePeriodo></periodo><periodo><parcial>PARCIAL2</parcial><totalizar>SI</totalizar></periodo><periodo><parcial>SUSPENSO</parcial><totalizar>NO</totalizar></periodo></periodos><idProfesor>5</idProfesor><profesor>BARRETO BARRETO KATIUSKA ELIZABETH </profesor><idMateria>30</idMateria><materia>Investigación Operaciones</materia><idParalelo>65</idParalelo><paralelo>S5K</paralelo></registro><registro><cantParciales>1</cantParciales><notaMinima>6.50</notaMinima><periodos><periodo><parcial>PARCIAL1</parcial><totalizar>SI</totalizar><componentePeriodo><idNota>51</idNota><componente>GESTIÓN FORMATIVA</componente><idNota>52</idNota><componente>GESTIÓN PRÁCTICA</componente><idNota>53</idNota><componente>ACREDITACIÓN</componente></componentePeriodo></periodo><periodo><parcial>PARCIAL2</parcial><totalizar>SI</totalizar></periodo><periodo><parcial>SUSPENSO</parcial><totalizar>NO</totalizar></periodo></periodos><idProfesor>5</idProfesor><profesor>BARRETO BARRETO KATIUSKA ELIZABETH </profesor><idMateria>30</idMateria><materia>Investigación Operaciones</materia><idParalelo>66</idParalelo><paralelo>S5L</paralelo></registro></registros>]]></PX_SALIDA>
                  <PI_ESTADO>1</PI_ESTADO>
                  <PV_MENSAJE>CONSULTA CON DATOS</PV_MENSAJE>
                  <PV_CODTRANS>7</PV_CODTRANS>
                  <PV_MENSAJE_TECNICO/>
               </parametrosSalida>
            </resultadoObjeto>
         </return>
      </ns2:ejecucionObjetoResponse>
   </soap:Body>
</soap:Envelope>
XML;

  /*             
$XML  = <<<XML
<registros>
    <registro>
        <cantParciales>1</cantParciales>
        <notaMinima>6.50</notaMinima>
        <periodos>
            <periodo>
                <parcial>PARCIAL1</parcial>
                <totalizar>SI</totalizar>
                <componentePeriodo>
                    <idNota>51</idNota>
                    <componente>GESTIÓN FORMATIVA</componente>
                    <idNota>52</idNota>
                    <componente>GESTIÓN PRÁCTICA</componente>
                    <idNota>53</idNota>
                    <componente>ACREDITACIÓN</componente>
                </componentePeriodo>
            </periodo>
        </periodos>
        <idProfesor>5</idProfesor>
        <profesor>BARRETO BARRETO KATIUSKA ELIZABETH </profesor>
        <idMateria>30</idMateria>
        <materia>Investigación Operaciones</materia>
        <idParalelo>65</idParalelo>
        <paralelo>S5K</paralelo>
    </registro>
</registros>
XML;*/
        
     $XML=null;              
           
           $response=$ws->doRequestSreReceptaTransacionConsultasdoc2($trama,$source,$tipo,$usuario,$clave,$url,$host, $XML);

           return $response;
   }#end function

      public function Docentes_getAlumnos($trama){
           $ws         = new AcademicoSoap();
           $tipo       = "9";
           $usuario    = $this->usuario;
           $clave      = $this->clave;
           $source     = $this->sourceConsultas;
           $url        = $this->url.$this->urlConsulta;
           $host       = $this->host;
           /*$trama      = "<idDocente>".$idDocente."</idDocente>";


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
XML;*/

           $XML=null;
              
           
           $response=$ws->doRequestConsultaAlumnos($trama,$source,$tipo,$usuario,$clave,$url,$host, $XML);


           return $response;
   }#end function

       public function Docentes_ingresoNotas($trama){
           $ws         = new AcademicoSoap();
           $tipo       = "14";
           $usuario    = "CapaVisual";
           $clave      = "123";
           $source     =$this->source;
           $url        = $this->url.$this->urlProcedim;
           $host       = $this->host;

           $XML=null;


           $response=$ws->doRequestIngresoNotas($trama,$source,$tipo,$usuario,$clave,$url,$host, $XML);

           return $response;
   }#end function
        public function Docentes_ingresoAsistencia($trama){
           $ws         = new AcademicoSoap();
           $tipo       = "16";
           $usuario    = "CapaVisual";
           $clave      = "123";
           $source     =$this->source;
           $url        = $this->url.$this->urlProcedim;
           $host       = $this->host;

           $XML=null;


           $response=$ws->doRequestIngresoNotas($trama,$source,$tipo,$usuario,$clave,$url,$host, $XML);

           return $response;
   }#end function

 public function Docentes_getfechasparcial($trama){
           $ws         = new AcademicoSoap();
           $tipo       = "21";
           $usuario    = $this->usuario;
           $clave      = $this->clave;
           $source     = $this->sourceConsultas;
           $url        = $this->url.$this->urlConsulta;
           $host       = $this->host;
          // $trama      = "<idDocente>".$Parcial."</idDocente>";


               $XML        = <<<XML
<soap:Envelope xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
   <soap:Body>
      <ns2:ejecucionConsultaResponse xmlns:ns2="http://servicios.ug.edu.ec/">
         <return>
            <codigoRespuesta>0</codigoRespuesta>
            <estado>F</estado>
            <idHistorico>12156</idHistorico>
            <mensajeRespuesta>exito</mensajeRespuesta>
            <respuestaConsulta>
               <registros>
                  <registro>
                     <fecha>2015-11-03</fecha>
                     <ingreso>1</ingreso>
                  </registro>
                  <registro>
                     <fecha>2015-10-28</fecha>
                     <ingreso>1</ingreso>
                  </registro>
               </registros>
            </respuestaConsulta>
         </return>
      </ns2:ejecucionConsultaResponse>
   </soap:Body>
</soap:Envelope>
XML;

           //$XML =null;
       
           $response=$ws->doRequestConsultaFechas($trama,$source,$tipo,$usuario,$clave,$url,$host, $XML);


           return $response;
   }#end function

public function crearEventos($evento){

  $idparametro = 0;
  $idtipoparametro = 1;
  $usuario = 1;
  $estado = "A";
  $opcion = "I";
  $this->tipo       = "31";
  $this->urlWS   = $this->url.$this->urlProcedim;
  $trama      = "<PX_XML><items><item><id_parametro>".$idparametro."</id_parametro><id_tipo_parametro>".$idtipoparametro."</id_tipo_parametro><nombre>".$evento."</nombre><valor1/><valor2/><usuario>".$usuario."</usuario><estado>".$estado."</estado></item></items></PX_XML><PC_OPCION>".$opcion."</PC_OPCION>";

  $response = $this->ws->doInsertEventosAcademicos($trama,$this->source,$this->tipo,$this->usuario,$this->clave,$this->urlWS,$this->host);
  return $response;

}#end function

public function insertarEventosCalendario($id_evento,$id_ciclo,$fec_desde,$fec_hasta,$id_usuario){
  $idparametro = 0;
  $idtipoparametro = 1;
  $usuario = 1;
  $estado = "A";
  $opcion = "I";
  $this->tipo       = "32";
  $this->urlWS   = $this->url.$this->urlProcedim;
  $trama      = "<PX_XML><items><item><id_sa_eventos_calendario_academico>".$id_evento."</id_sa_eventos_calendario_academico><id_sa_ciclo_detalle>".$id_ciclo."</id_sa_ciclo_detalle><fecha_desde>".$fec_desde."</fecha_desde><fecha_hasta>".$fec_hasta."</fecha_hasta><id_sg_usuario_registro>".$id_usuario."</id_sg_usuario_registro><id_sa_calendario_academico>0</id_sa_calendario_academico></item></items></PX_XML><PC_OPCION>".$opcion."</PC_OPCION>";
  // echo '<pre>'; var_dump($trama); exit();
  $response = $this->ws->doInsertEventosCalendario($trama,$this->source,$this->tipo,$this->usuario,$this->clave,$this->urlWS,$this->host);
  return $response;
}#end function


//-----------------------------------------------------------------------------------------------------------------------------//
/*INICIO - ARELLANO SPRINT 4*/
public function getConsultaEstudiantes_InscritosMatriculados($idCiclo, $idCarrera,$idEstadoMatricula,$identificacion){
        $ws=new AcademicoSoap();
        $tipo       = "23";
        $usuario    = "CapaVisualPhp";
        $clave      = "12CvP2015";
        $source     = "jdbc/saugConsTmp";
        $url        = "http://186.101.66.2:8080/consultas/ServicioWebConsultas?wsdl";
        $host       = "186.101.66.2:8080";
        $trama      = "<estadoMatricula>".$idEstadoMatricula."</estadoMatricula><identificacion>".$identificacion."</identificacion><idCiclo>".$idCiclo."</idCiclo><carrera>".$idCarrera."</carrera>";
        $this->urlWS   = $this->url.$this->urlConsulta;
        $XML        = NULL;
        $response=$this->ws->doRequestSreReceptaEstudiantes_InscritosMatriculados($trama,$this->sourceConsultas,$tipo,$this->usuario,$this->clave,$this->urlWS,$this->host,$XML);
        return $response;
        
}#end function

public function getEstadosMatricula(){
        $ws=new AcademicoSoap();
        $tipo       = "24";
        $usuario    = "CapaVisualPhp";
        $clave      = "12CvP2015";
        $source     = "jdbc/saugConsTmp";
        $url        = "http://186.101.66.2:8080/consultas/ServicioWebConsultas?wsdl";
        $host       = "186.101.66.2:8080";
        $trama      = "<parametro>16</parametro>";
        $XML        = NULL;
        $this->urlWS   = $this->url.$this->urlConsulta;
        $response=$this->ws->doRequestEstadosMatricula($trama,$this->sourceConsultas,$tipo,$this->usuario,$this->clave,$this->urlWS,$this->host ,$XML);
        return $response;

}#end function
/*FIN - ARELLANO SPRINT 4*/
//-----------------------------------------------------------------------------------------------------------------------------//

public function modificarEventos($evento,$idparametro,$estado){
// echo 'UGSERVICE<pre>'; var_dump($evento,$idparametro,$estado); exit();

    if ($estado == 'Activo') {
       $estado_evento = "A";
    }else{
      $estado_evento = "I";
    }

  $idtipoparametro = 1;
  $usuario = 1;

  $opcion = "A";
  $this->tipo       = "31";
  $this->urlWS   = $this->url.$this->urlProcedim;
  $trama      = "<PX_XML><items><item><id_parametro>".$idparametro."</id_parametro><id_tipo_parametro>".$idtipoparametro."</id_tipo_parametro><nombre>".$evento."</nombre><valor1/><valor2/><usuario>".$usuario."</usuario><estado>".$estado_evento."</estado></item></items></PX_XML><PC_OPCION>".$opcion."</PC_OPCION>";

  $response = $this->ws->doInsertEventosAcademicos($trama,$this->source,$this->tipo,$this->usuario,$this->clave,$this->urlWS,$this->host);
  return $response;


}#end function

public function cargarEventosCalendario($id_ciclo,$id_usuario){
  $idparametro = 0;
  $idtipoparametro = 1;
  $usuario = 1;
  $estado = "A";
  $opcion = "C";
  $this->tipo       = "32";
  $this->urlWS   = $this->url.$this->urlProcedim;
  $trama      = "<PX_XML><items><item><id_sa_parametro_actividad>0</id_sa_parametro_actividad><id_sa_ciclo_detalle>".$id_ciclo."</id_sa_ciclo_detalle><fecha_desde>0</fecha_desde><fecha_hasta>0</fecha_hasta><id_sg_usuario_registro>".$id_usuario."</id_sg_usuario_registro><id_sa_calendario_academico>0</id_sa_calendario_academico></item></items></PX_XML><PC_OPCION>".$opcion."</PC_OPCION>";
  // echo '<pre>'; var_dump($trama); exit();
  $response = $this->ws->doSelectEventosCalendario($trama,$this->source,$this->tipo,$this->usuario,$this->clave,$this->urlWS,$this->host);
  // echo '<pre>'; var_dump($response); exit();
  return $response;
}#end function

public function modificarEventosCalendario($id_evento,$id_ciclo,$fec_desde,$fec_hasta,$id_usuario,$id_calendario,$estado){
  $idparametro = 0;
  $idtipoparametro = 1;
  $usuario = 1;
  // $estado = "A";
  $opcion = "A";
  $this->tipo       = "32";
  $this->urlWS   = $this->url.$this->urlProcedim;
  $trama      = "<PX_XML><items><item><id_sa_eventos_calendario_academico>".$id_evento."</id_sa_eventos_calendario_academico><id_sa_ciclo_detalle>".$id_ciclo."</id_sa_ciclo_detalle><fecha_desde>".$fec_desde."</fecha_desde><fecha_hasta>".$fec_hasta."</fecha_hasta><id_sg_usuario_registro>".$id_usuario."</id_sg_usuario_registro><id_sa_calendario_academico>".$id_calendario."</id_sa_calendario_academico></item></items></PX_XML><PC_OPCION>".$opcion."</PC_OPCION>";
  // echo '<pre>'; var_dump($trama); exit();
  $response = $this->ws->doUpdateEventosCalendario($trama,$this->source,$this->tipo,$this->usuario,$this->clave,$this->urlWS,$this->host);
  return $response;
}#end function

public function getConsultaCarrerasorden($idUsuario,$idRol){
        $this->tipo    = "3";
        $this->urlWS   = $this->url.$this->urlConsulta;
        /*$ws=new AcademicoSoap();
        $tipo       = "3";
        $usuario    = "CapaVisualPhp";
        $clave      = "12CvP2015";
        $source     = "jdbc/saugConsTmp";
        $url        = "http://186.101.66.2:8080/consultasTmp/ServicioWebConsultas?wsdl";
        $host       = "186.101.66.2:8080";
        $trama      = "<usuario>".$idEstudiante."</usuario><rol>".$idRol."</rol>";*/
         $trama      = "<usuario>".$idUsuario."</usuario><rol>".$idRol."</rol>";

        //$response=$ws->doRequestSreReceptaTransacionCarreras($trama,$this->source,$this->tipo,$usuario,$clave,$url,$host);
        $response=$this->ws->doRequestSreReceptaTransacionCarrerasOrden($trama,$this->sourceConsultas,$this->tipo,$this->usuario,$this->clave,$this->urlWS,$this->host);
        return $response;
}#end function

public function getConsulta_listado_OrdenPago($idEstudiante,$idCarrera,$idCiclo,$modoConsulta,$idEstado){
       /* $ws=new AcademicoSoap();
        $tipo       = "7";
        $usuario    = "CapaVisualPhp";
        $clave      = "12CvP2015";
        $source     = "jdbc/consultasSaug";
        $url        = "http://192.168.100.11:8080/consultas/ServicioWebConsultas?wsdl";
        $host       = "192.168.100.11:8080";*/
        $this->tipo       = "29";
        $this->urlWS   = $this->url.$this->urlProcedim;
        $trama      = "<PV_TIPO_ID>".$modoConsulta."</PV_TIPO_ID><PV_ID>".$idEstudiante."</PV_ID><PI_ID_SA_PARAMETRO_ESTADO>".$idEstado."</PI_ID_SA_PARAMETRO_ESTADO>";
        //$response=$ws->doRequestSreReceptaTransacionRegistroMatricula($trama,$source,$tipo,$usuario,$clave,$url,$host);
        $response=$this->ws->doRequestsListarOrdenPago($trama,$this->source,$this->tipo,$this->usuario,$this->clave,$this->urlWS,$this->host);

        return $response;

}#end function

public function setActualizaOrden($trama){
        /*$ws=new AcademicoSoap();
        $tipo       = "15";
        $usuario    = "CapaVisualPhp";
        $clave      = "12CvP2015";
        $source     = "jdbc/procedimientosSaug";
        $url        = "http://192.168.100.11:8080/WSObjetosUg/ServicioWebObjetos?wsdl";
        $host       = "192.168.100.11";*/
        //$trama      = "<usuario>".$idEstudiante."</usuario><rol>".$idRol."</rol>";
        $this->tipo       = "28";
        $this->urlWS   = $this->url.$this->urlProcedim;
        //$response=$ws->doSetMatricula($trama,$source,$tipo,$usuario,$clave,$url,$host);
        $response=$this->ws->doSetActualizaOrden($trama,$this->source,$this->tipo,$this->usuario,$this->clave,$this->urlWS,$this->host);
        return $response;
}#end function

public function setSolicitudAnula($trama){
        /*$ws=new AcademicoSoap();
        $tipo       = "15";
        $usuario    = "CapaVisualPhp";
        $clave      = "12CvP2015";
        $source     = "jdbc/procedimientosSaug";
        $url        = "http://192.168.100.11:8080/WSObjetosUg/ServicioWebObjetos?wsdl";
        $host       = "192.168.100.11";*/
        //$trama      = "<usuario>".$idEstudiante."</usuario><rol>".$idRol."</rol>";
        $this->tipo       = "33";
        $this->urlWS   = $this->url.$this->urlProcedim;
        //$response=$ws->doSetMatricula($trama,$source,$tipo,$usuario,$clave,$url,$host);
        $response=$this->ws->doSetSolicitudAnula($trama,$this->source,$this->tipo,$this->usuario,$this->clave,$this->urlWS,$this->host);
        return $response;
}#end function

public function getConsultaHorario_examen($idEstudiante,$idCarrera,$idCiclo,$modoConsulta,$idEstado){
       /* $ws=new AcademicoSoap();
        $tipo       = "7";
        $usuario    = "CapaVisualPhp";
        $clave      = "12CvP2015";
        $source     = "jdbc/consultasSaug";
        $url        = "http://192.168.100.11:8080/consultas/ServicioWebConsultas?wsdl";
        $host       = "192.168.100.11:8080";*/
        $this->tipo       = "29";
        $this->urlWS   = $this->url.$this->urlProcedim;
        $trama      = "<PV_ID>".$idEstudiante."</PV_ID><PI_ID_SA_PARAMETRO_ESTADO>".$idEstado."</PI_ID_SA_PARAMETRO_ESTADO>";
        //$response=$ws->doRequestSreReceptaTransacionRegistroMatricula($trama,$source,$tipo,$usuario,$clave,$url,$host);
        $response=$this->ws->doRequestsEstudianteHorariosExamen($trama,$this->source,$this->tipo,$this->usuario,$this->clave,$this->urlWS,$this->host);

        return $response;

}#end function

   public function Docentes_getDocentes($idCarrera){
      $this->tipo    = "27";

      $this->source  = $this->sourceConsultas;

      $this->urlWS   = $this->url.$this->urlConsulta;
      $trama         = "<id_carrera>".$idCarrera."</id_carrera>";
      $XML           = NULL;
      $response=$this->ws->doRequestSreReceptaTransacionConsultasdoc($trama,$this->source,$this->tipo,$this->usuario,$this->clave,$this->urlWS,$this->host,$XML);

      return $response;
   }#end function Docentes_getMaterias()



   public function Guarda_Mensajes($trama){
       
        $this->tipo       = "37 ";
        $this->urlWS   = $this->url.$this->urlProcedim;
        //$response=$ws->doSetMatricula($trama,$source,$tipo,$usuario,$clave,$url,$host);
        $response=$this->ws->GuardaMensaje($trama,$this->source,$this->tipo,$this->usuario,$this->clave,$this->urlWS,$this->host);
        return $response;
}#end function

      public function Datos($idCarrera){
      $this->tipo    = "26";

      $this->source  = $this->sourceConsultas;

      $this->urlWS   = $this->url.$this->urlConsulta;
      $trama         = "<id_carrera>".$idCarrera."</id_carrera>";
      $XML           = NULL;
      $response=$this->ws->doRequestDatos($trama,'jdbc/saugConsTmp',$this->tipo,$this->usuario,$this->clave,$this->urlWS,$this->host,$XML);

      return $response;
   }#end function Docentes_getMaterias()


   
 public function getConsultaHorario_examendoc($trama){
       /* $ws=new AcademicoSoap();
        $tipo       = "7";
        $usuario    = "CapaVisualPhp";
        $clave      = "12CvP2015";
        $source     = "jdbc/consultasSaug";
        $url        = "http://192.168.100.11:8080/consultas/ServicioWebConsultas?wsdl";
        $host       = "192.168.100.11:8080";*/
        $this->tipo       = "43";
        $this->urlWS   = $this->url.$this->urlProcedim;
//           echo $trama."--".$this->source."--".$this->tipo."--".$this->usuario."--".$this->clave."--".$this->urlWS."--".$this->host;
//        exit();
        //$response=$ws->doRequestSreReceptaTransacionRegistroMatricula($trama,$source,$tipo,$usuario,$clave,$url,$host);
        $response=$this->ws->doRequestDocenteHorariosExamen($trama,$this->source,$this->tipo,$this->usuario,$this->clave,$this->urlWS,$this->host);
     
        
        return $response; 
            
}#end function



   public function getConsultaPorcentajeEstudianteCarrera($idCiclo, $idCarrera){

        $ws=new AcademicoSoap();
        $tipo       = "28";
        $usuario    = "CapaVisualPhp";
        $clave      = "12CvP2015";
        $source     = "jdbc/saugConsTmp";
        $url        = "http://186.101.66.2:8080/consultas/ServicioWebConsultas?wsdl";
        $host       = "186.101.66.2:8080";
        $trama      = "<idCiclo>".$idCiclo."</idCiclo><carrera>".$idCarrera."</carrera>";
        $this->urlWS   = $this->url.$this->urlConsulta;
        $response=$this->ws->doRequestEstadosMatricula($trama,$this->sourceConsultas,$tipo,$this->usuario,$this->clave,$this->urlWS,$this->host);

        return $response; 
            
}#end function

  
  public function getRolesAdmin($idUser,$idRol){

        $this->tipo       = "38";
        $this->urlWS      = $this->url.$this->urlProcedim;
        $trama      = "<PI_id_usuario>".$idUser."</PI_id_usuario><PI_rol>".$idRol."</PI_rol>";
        // echo '<pre>'; var_dump($trama,$this->source,$this->tipo,$this->usuario,$this->clave,$this->urlWS,$this->host); exit();
        $response=$this->ws->doSelectRolAdmin($trama,$this->source,$this->tipo,$this->usuario,$this->clave,$this->urlWS,$this->host);

        return $response;

  }#end function
  
    public function Docentes_Horarios($idUser){
        $this->tipo    = "30";

      $this->source  = $this->sourceConsultas;

      $this->urlWS   = $this->url.$this->urlConsulta;
       $trama      = "<id_sg_usuario>$idUser</id_sg_usuario><id_sa_ciclo_detalle>19</id_sa_ciclo_detalle>";
      $XML           = NULL;
      $response=$this->ws->doSelectHorariosDocente($trama,$this->source,$this->tipo,$this->usuario,$this->clave,$this->urlWS,$this->host,$XML);

        return $response;
       
  }#end function


/*INICIO - ARELLANO SPRINT 4.1*/
public function getConsultaMateriasAprobadasEstudianteAdmin($opcion,$identificacion, $idCarrera, $idCiclo, $nivel){
        $this->tipo       = "30";
        $this->urlWS   = $this->url.$this->urlProcedim;
        $trama      = "<PV_Opcion>".$opcion."</PV_Opcion><PV_Identificacion>".$identificacion."</PV_Identificacion><PI_ID_Ciclo>".$idCiclo."</PI_ID_Ciclo><PI_ID_Nivel>".$nivel."</PI_ID_Nivel><PI_Carrera>".$idCarrera."</PI_Carrera>";
        $response=$this->ws->doRequestConsultaMateriasAprobadasEstudianteAdmin($trama,$this->source,$this->tipo,$this->usuario,$this->clave,$this->urlWS,$this->host);
        return $response; 
}#end function
/*FIN - ARELLANO SPRINT 4.1*/

}#end class


