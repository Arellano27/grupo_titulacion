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
//      $this->usuario       = "abc";
//      $this->clave         = "123";
//      $this->source        = "jdbc/saugProcTmp";
//      $this->url           = "http://192.168.100.11:8080/";
//      $this->urlConsulta   = "consultas/ServicioWebConsultas?wsdl";
//      $this->urlProcedim   = "WSObjetosUgPre/ServicioWebObjetos?wsdl";
//      $this->urlWS         = "";
//      $this->host          = "192.168.100.11:8080";
      /* PARAMETROS PARA SERVIDORES LOCALES EN UNIVERSIDAD - FIN */
      
      /* PARAMETROS PARA SERVIDORES DISPONIBLES EN INTERNET - INICIO */
      $this->usuario       = "CapaVisualPhp";
      $this->clave         = "12CvP2015";
      $this->source        = "jdbc/saugProcTmp";
      $this->url           = "http://186.101.66.2:8080/";
      $this->urlConsulta   = "consultasTmp/ServicioWebConsultas?wsdl";
      $this->urlProcedim   = "WSObjetosUgPre/ServicioWebObjetos?wsdl";
      $this->urlWS         = "";
      $this->host          = "186.101.66.2:8080";
      /* PARAMETROS PARA SERVIDORES DISPONIBLES EN INTERNET - FIN */
   }
   public function getLogin($username,$password){
      $ws=new AcademicoSoap();
      $tipo       = "3";
      $usuario    = "abc";
      $clave      = "123";
      $source     = "jdbc/saugProcTmp";
      $url        = "http://186.101.66.2:8080/WSObjetosUgPre/ServicioWebObjetos?wsdl";
      $host       = "186.101.66.2:8080";
      $trama      = "<usuario>".$username."</usuario><contrasena>".$password."</contrasena>";
      $response=$ws->doRequestSreReceptaTransacionProcedimientos($trama,$source,$tipo,$usuario,$clave,$url,$host);
       //pruebas
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
      $this->source  = "jdbc/saugConsTmp";
      $this->urlWS   = $this->url.$this->urlConsulta;
      $trama      = "<usuario>".$idDocente."</usuario><rol>2</rol>";
      $XML        = NULL;

      $response=$this->ws->doRequestSreReceptaTransacionConsultasdoc($trama,$this->source,$this->tipo,$this->usuario,$this->clave,$this->urlWS,$this->host, $XML);
      return $response;
   }#end function Docentes_getCarreras()
   
   
   public function Docentes_getMaterias($idDocente, $idCarrera){
      /*informacion quemada - inicio*/
      //$idDocente  = 00;
      //$idCarrera  = 00;
      /*informacion quemada - fin*/
      
      $this->tipo    = "5";
      $this->source  = "jdbc/saugConsTmp";
      $this->urlWS   = $this->url.$this->urlConsulta;
      $trama         = "<usuario>".$idDocente."</usuario><carrera>".$idCarrera."</carrera>";
      $XML           = NULL;
    
      $response=$this->ws->doRequestSreReceptaTransacionConsultasdoc($trama,$this->source,$this->tipo,$this->usuario,$this->clave,$this->urlWS,$this->host,$XML);
           
      return $response;
   }#end function Docentes_getMaterias()
   
   public function Docentes_getAsistenciasMaterias($datosConsulta){
      $this->tipo    = "9";
      $this->source  = "jdbc/saugProcTmp";
      
      if( !isset($datosConsulta["fechaInicio"]) || !isset($datosConsulta["fechaFin"]) ){
         $day                          = date('w')-1;
         $datosConsulta["fechaInicio"] = date('d-m-Y', strtotime('-'.$day.' days'));
         $datosConsulta["fechaFin"]    = date('d-m-Y', strtotime('+'.(6-$day).' days'));
	 $datosConsulta["anio"]        = date('o');
      }

      /*informacion quemada - inicio*/
      // $this->source                 = "jdbc/saugProcTmp";
      // $this->urlProcedim            = "WSObjetosUgPre/ServicioWebObjetos?wsdl";
      // $datosConsulta["fechaInicio"] = '15/09/2015';
      // $datosConsulta["fechaFin"]    = '20/09/2015';
      // $datosConsulta["idDocente"]   = 31;
      // $datosConsulta["idMateria"]   = 51;
      // $datosConsulta["idParalelo"]  = 7;
      // $datosConsulta["anio"]        = 2015;
      // $datosConsulta["ciclo"]       = 4;
      /*informacion quemada - fin*/

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
      $this->source  = "jdbc/saugProcTmp";
      
      //quemado - inicio
      // $this->source              = "jdbc/saugProcTmp";
      // $this->urlProcedim         = "WSObjetosUgPre/ServicioWebObjetos?wsdl";
      // $datosConsulta["ciclo"]    = 18;
      // $datosConsulta["idDocente"]= 3;
      // $datosConsulta["idMateria"]= 54;
      //quemado - fin 
      
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
        $url        = "http://186.101.66.2:8080/WSObjetosUgPre/ServicioWebObjetos";
        $host       = "186.101.66.2:8080";
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

}#end class
     
	
	
	
	