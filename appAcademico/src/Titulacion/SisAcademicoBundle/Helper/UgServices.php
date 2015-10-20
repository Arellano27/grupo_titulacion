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
   /*   $this->tipo       = "0";
      $this->source     = "";
      /* PARAMETROS PARA SERVIDORES LOCALES EN UNIVERSIDAD - INICIO */
    /* $this->usuario       = "abc";
     $this->clave         = "123";
     $this->source        = "jdbc/procedimientosSaug";
        $this->url           = "http://192.168.100.11:8080/";
     $this->urlConsulta   = "consultas/ServicioWebConsultas?wsdl";
     $this->urlProcedim   = "WSObjetosUg/ServicioWebObjetos?wsdl";
     $this->urlWS         = "";
     $this->host          = "192.168.100.11:8080";*/
      /* PARAMETROS PARA SERVIDORES LOCALES EN UNIVERSIDAD - FIN */

      /* PARAMETROS PARA SERVIDORES DISPONIBLES EN INTERNET - INICIO PRE*/
//       $this->usuario       = "CapaVisualPhp";
//       $this->clave         = "12CvP2015";
//       $this->sourcecons        = "jdbc/consultasSaug";
//       $this->sourcepro        = "jdbc/procedimientosSaug";
//       $this->url           = "http://186.101.66.2:8080/";
//       $this->urlConsulta   = "consultas/ServicioWebConsultas?wsdl";
//       $this->urlProcedim   = "WSObjetosUg/ServicioWebObjetos?wsdl";
//       $this->urlWS         = "";
//       $this->host          = "186.101.66.2:8080";
      /* PARAMETROS PARA SERVIDORES DISPONIBLES EN INTERNET - FIN */
        /* PARAMETROS PARA SERVIDORES DISPONIBLES EN INTERNET - INICIO PRO*/
       $this->usuario       = "CapaVisualPhp";
       $this->clave         = "12CvP2015";
       $this->usuariopro       = "CapaVisual";
       $this->clavepro         = "123";
       $this->sourcecons    = "jdbc/saugConsTmp";
       $this->sourcepro      = "jdbc/saugProcTmp";
       $this->url           = "http://186.101.66.2:8080/";
       $this->urlConsulta   = "consultas/ServicioWebConsultas?wsdl";
       $this->urlProcedim   = "WSObjetosUg/ServicioWebObjetos?wsdl";
       $this->urlWS         = "";
       $this->host          = "186.101.66.2:8080";
      /* PARAMETROS PARA SERVIDORES DISPONIBLES EN INTERNET - FIN */
   }
   public function getLogin($username,$password){
      $ws=new AcademicoSoap();
      $tipo       = "8";
      $usuario    = "abc";
      $clave      = "123";
      $source     = $this->sourcepro;
      $url        = $this->url.$this->urlProcedim;
      $host       = $this->host;
      $trama      = "<usuario>".$username."</usuario><contrasena>".$password."</contrasena>";
      $response=$ws->doRequestSreReceptaTransacionProcedimientos($trama,$source,$tipo,$usuario,$clave,$url,$host);
      // echo '<pre>'; var_dump($response); exit();
       //pruebas
      return $response;

   }#end function

   public function getConsultaNotas($servicio=""){
      $ws=new AcademicoSoap();
      $tipo       = "3";
      $usuario    = "abc";
      $clave      = "123";
      $source     = $this->sourcepro;
      $url        = $this->url.$this->urlProcedim;
      $host       = $this->host;
      $trama      = "<usuario>0924393861</usuario><contrasena>sinclave</contrasena>";
      $response=$ws->doRequestSreReceptaTransacionConsultas($trama,$source,$tipo,$usuario,$clave,$url,$host);
                          
      return $response;
   }#end function
   
   public function Docentes_getCarreras($idDocente){
      $this->tipo    = "3";
      $this->source  = $this->sourcecons;
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
      $this->source  = $this->sourcecons;
      $this->urlWS   = $this->url.$this->urlConsulta;
      $trama         = "<usuario>".$idDocente."</usuario><carrera>".$idCarrera."</carrera>";
      $XML           = NULL;
    
      $response=$this->ws->doRequestSreReceptaTransacionConsultasdoc($trama,$this->source,$this->tipo,$this->usuario,$this->clave,$this->urlWS,$this->host,$XML);
           
      return $response;
   }#end function Docentes_getMaterias()
   
   public function Docentes_getAsistenciasMaterias($datosConsulta){
      $this->tipo    = "9";
      $this->source  = "jdbc/saugProcTmp";
      
      /*informacion quemada - inicio*/
       $this->source                 = $this->sourcepro;
       //$this->urlProcedim            = "WSObjetosUgPre/ServicioWebObjetos?wsdl";
       //$datosConsulta["fechaInicio"] = '15/09/2015';
       //$datosConsulta["fechaFin"]    = '20/09/2015';
       //$datosConsulta["idDocente"]   = 31;
       //$datosConsulta["idMateria"]   = 51;
       //$datosConsulta["idParalelo"]  = 7;
       //$datosConsulta["anio"]        = 2015;
      /*informacion quemada - fin*/
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
      $this->source  = $this->sourcepro;
      
      //quemado - inicio
      // $this->source              = "jdbc/saugProcTmp";
      // $this->urlProcedim         = "WSObjetosUgPre/ServicioWebObjetos?wsdl";
       $datosConsulta["ciclo"]    = 0;    /* ES NECESARIO PARA LA TRAMA ACTUAL */
//       $datosConsulta["idDocente"]= 3;
//       $datosConsulta["idMateria"]= 54;
      //quemado - fin 
      
      $this->urlWS   = $this->url.$this->urlProcedim;
      
      $trama         =  "<PI_ID_CICLO_DETALLE>".$datosConsulta["ciclo"]."</PI_ID_CICLO_DETALLE>
                        <PI_ID_USUARIO_PROFESOR>".$datosConsulta["idDocente"]."</PI_ID_USUARIO_PROFESOR>
                        <PI_ID_MATERIA>".$datosConsulta["idMateria"]."</PI_ID_MATERIA>";
      $XML           = NULL;
//$XML        = <<<XML
//<soap:Envelope xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
//   <soap:Body>
//      <ns2:ejecucionObjetoResponse xmlns:ns2="http://servicios.ug.edu.ec/">
//         <return>
//            <codigoRespuesta>0</codigoRespuesta>
//            <estado>F</estado>
//            <idHistorico>30334</idHistorico>
//            <mensajeRespuesta>ok</mensajeRespuesta>
//            <resultadoObjeto>
//               <parametrosSalida>
//                  <PX_SALIDA><![CDATA[&lt;registros&gt;&lt;registro&gt;&lt;cantParciales&gt;2&lt;/cantParciales&gt;&lt;notaMinima&gt;6.50&lt;/notaMinima&gt;&lt;periodos&gt;&lt;periodo&gt;&lt;parcial&gt;1&lt;/parcial&gt;&lt;totalizar&gt;SI&lt;/totalizar&gt;&lt;componentePeriodo&gt;&lt;idNota&gt;51&lt;/idNota&gt;&lt;componente&gt;GESTIÓNFORMATIVA&lt;/componente&gt;&lt;idNota&gt;52&lt;/idNota&gt;&lt;componente&gt;GESTIÓNPRÁCTICA&lt;/componente&gt;&lt;idNota&gt;53&lt;/idNota&gt;&lt;componente&gt;ACREDITACIÓN&lt;/componente&gt;&lt;/componentePeriodo&gt;&lt;/periodo&gt;&lt;periodo&gt;&lt;parcial&gt;2&lt;/parcial&gt;&lt;totalizar&gt;SI&lt;/totalizar&gt;&lt;componentePeriodo&gt;&lt;idNota&gt;51&lt;/idNota&gt;&lt;componente&gt;GESTIÓNFORMATIVA&lt;/componente&gt;&lt;idNota&gt;52&lt;/idNota&gt;&lt;componente&gt;GESTIÓNPRÁCTICA&lt;/componente&gt;&lt;idNota&gt;53&lt;/idNota&gt;&lt;componente&gt;ACREDITACIÓN&lt;/componente&gt;&lt;/componentePeriodo&gt;&lt;/periodo&gt;&lt;/periodos&gt;&lt;idProfesor&gt;31&lt;/idProfesor&gt;&lt;profesor&gt;ACOSTAZAMBRANONANCYLENIS&lt;/profesor&gt;&lt;idMateria&gt;1&lt;/idMateria&gt;&lt;materia&gt;Matemática1&lt;/materia&gt;&lt;idParalelo&gt;1&lt;/idParalelo&gt;&lt;paralelo&gt;S1A&lt;/paralelo&gt;&lt;estudiantes&gt;&lt;estudiante&gt;&lt;idEstudiante&gt;17&lt;/idEstudiante&gt;&lt;estudiante&gt;MORAXAVIER&lt;/estudiante&gt;&lt;promedio&gt;9.50&lt;/promedio&gt;&lt;ciclo&gt;9&lt;/ciclo&gt;&lt;parciales&gt;&lt;Parcial&gt;1&lt;/Parcial&gt;&lt;total&gt;10.00&lt;/total&gt;&lt;notas&gt;&lt;nota&gt;&lt;idTipoNota&gt;51&lt;/idTipoNota&gt;&lt;tipoNota&gt;GESTIÓNFORMATIVA&lt;/tipoNota&gt;&lt;Nota&gt;3.00&lt;/Nota&gt;&lt;/nota&gt;&lt;nota&gt;&lt;idTipoNota&gt;52&lt;/idTipoNota&gt;&lt;tipoNota&gt;GESTIÓNPRÁCTICA&lt;/tipoNota&gt;&lt;Nota&gt;3.00&lt;/Nota&gt;&lt;/nota&gt;&lt;nota&gt;&lt;idTipoNota&gt;53&lt;/idTipoNota&gt;&lt;tipoNota&gt;ACREDITACIÓN&lt;/tipoNota&gt;&lt;Nota&gt;4.00&lt;/Nota&gt;&lt;/nota&gt;&lt;/notas&gt;&lt;/parciales&gt;&lt;parciales&gt;&lt;Parcial&gt;2&lt;/Parcial&gt;&lt;total&gt;10.00&lt;/total&gt;&lt;notas&gt;&lt;nota&gt;&lt;idTipoNota&gt;51&lt;/idTipoNota&gt;&lt;tipoNota&gt;GESTIÓNFORMATIVA&lt;/tipoNota&gt;&lt;Nota&gt;3.00&lt;/Nota&gt;&lt;/nota&gt;&lt;nota&gt;&lt;idTipoNota&gt;52&lt;/idTipoNota&gt;&lt;tipoNota&gt;GESTIÓNPRÁCTICA&lt;/tipoNota&gt;&lt;Nota&gt;3.00&lt;/Nota&gt;&lt;/nota&gt;&lt;nota&gt;&lt;idTipoNota&gt;53&lt;/idTipoNota&gt;&lt;tipoNota&gt;ACREDITACIÓN&lt;/tipoNota&gt;&lt;Nota&gt;4.00&lt;/Nota&gt;&lt;/nota&gt;&lt;/notas&gt;&lt;/parciales&gt;&lt;estadoCiclo&gt;A&lt;/estadoCiclo&gt;&lt;/estudiante&gt;&lt;estudiante&gt;&lt;idEstudiante&gt;6&lt;/idEstudiante&gt;&lt;estudiante&gt;FERNANDEZPALOMINOWILSONALBERTO&lt;/estudiante&gt;&lt;promedio&gt;7.00&lt;/promedio&gt;&lt;ciclo&gt;9&lt;/ciclo&gt;&lt;parciales&gt;&lt;Parcial&gt;1&lt;/Parcial&gt;&lt;total&gt;7.30&lt;/total&gt;&lt;notas&gt;&lt;nota&gt;&lt;idTipoNota&gt;51&lt;/idTipoNota&gt;&lt;tipoNota&gt;GESTIÓNFORMATIVA&lt;/tipoNota&gt;&lt;Nota&gt;2.30&lt;/Nota&gt;&lt;/nota&gt;&lt;nota&gt;&lt;idTipoNota&gt;52&lt;/idTipoNota&gt;&lt;tipoNota&gt;GESTIÓNPRÁCTICA&lt;/tipoNota&gt;&lt;Nota&gt;1.20&lt;/Nota&gt;&lt;/nota&gt;&lt;nota&gt;&lt;idTipoNota&gt;53&lt;/idTipoNota&gt;&lt;tipoNota&gt;ACREDITACIÓN&lt;/tipoNota&gt;&lt;Nota&gt;3.80&lt;/Nota&gt;&lt;/nota&gt;&lt;/notas&gt;&lt;/parciales&gt;&lt;parciales&gt;&lt;Parcial&gt;2&lt;/Parcial&gt;&lt;total&gt;6.60&lt;/total&gt;&lt;notas&gt;&lt;nota&gt;&lt;idTipoNota&gt;51&lt;/idTipoNota&gt;&lt;tipoNota&gt;GESTIÓNFORMATIVA&lt;/tipoNota&gt;&lt;Nota&gt;2.50&lt;/Nota&gt;&lt;/nota&gt;&lt;nota&gt;&lt;idTipoNota&gt;52&lt;/idTipoNota&gt;&lt;tipoNota&gt;GESTIÓNPRÁCTICA&lt;/tipoNota&gt;&lt;Nota&gt;3.20&lt;/Nota&gt;&lt;/nota&gt;&lt;nota&gt;&lt;idTipoNota&gt;53&lt;/idTipoNota&gt;&lt;tipoNota&gt;ACREDITACIÓN&lt;/tipoNota&gt;&lt;Nota&gt;0.90&lt;/Nota&gt;&lt;/nota&gt;&lt;/notas&gt;&lt;/parciales&gt;&lt;estadoCiclo&gt;A&lt;/estadoCiclo&gt;&lt;/estudiante&gt;&lt;/estudiantes&gt;&lt;/registro&gt;&lt;/registros&gt;]]></PX_SALIDA>
//                  <PI_ESTADO>1</PI_ESTADO>
//                  <PV_MENSAJE>CONSULTA CON DATOS</PV_MENSAJE>
//                  <PV_CODTRANS>7</PV_CODTRANS>
//                  <PV_MENSAJE_TECNICO/>
//               </parametrosSalida>
//            </resultadoObjeto>
//         </return>
//      </ns2:ejecucionObjetoResponse>
//   </soap:Body>
//</soap:Envelope>
//XML;
          
      $xmlData["XML_test"]          = $XML;
      $xmlData["bloqueRegistros"]   = 'registros';
      $xmlData["bloqueSalida"]      = 'px_salida';

      $response   =  $this->ws->doRequestSreReceptaTransacionObjetos_Registros($trama,$this->source,$this->tipo,$this->usuario,$this->clave,$this->urlWS,$this->host, $xmlData);
      return $response;
   }#end function Docentes_getNotasMaterias()
   

public function getConsultaCarreras($idEstudiante,$idRol){
        $ws=new AcademicoSoap();
        $tipo       = "3";
        $usuario    = "CapaVisualPhp";
        $clave      = "12CvP2015";
        $source     = $this->sourcecons;
        $url        = $this->url.$this->urlConsulta;
        $host       = $this->host;
        $trama      = "<usuario>".$idEstudiante."</usuario><rol>".$idRol."</rol>";
        $response=$ws->doRequestSreReceptaTransacionCarreras($trama,$source,$tipo,$usuario,$clave,$url,$host);
        return $response;     
}#end function


public function getConsultaNotas_act($idFacultad,$idCarrera,$idEstudiante){
        $ws=new AcademicoSoap();
        $tipo       = "11";
        $usuario    = "CapaVisual";
        $clave      = "123";
        $source     = $this->sourcepro;
        $url        = $this->url.$this->urlProcedim;
        $host       = $this->host;
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
        $source     = $this->sourcepro;
       // $url        = "http://186.101.66.2:8080/WSObjetosUg/ServicioWebObjetos?wsdl";
        $url        = $this->url.$this->urlProcedim;
        $host       = $this->host;
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
        $source     = $this->sourcepro;
        $url        = $this->url.$this->urlProcedim;
        $host       = $this->host;
        $trama      = "<pi_idEstudiante>".$idEstudiante."</pi_idEstudiante><pi_idCarrera>".$idCarrera."</pi_idCarrera>";
        $response=$ws->doRequestSreReceptaTransacionAsistencias($trama,$source,$tipo,$usuario,$clave,$url,$host);
        return $response;

}
public function getConsultaCarreras_Matricula($idEstudiante,$idRol){
        $ws=new AcademicoSoap();
        $tipo       = "3";
        $usuario    = "CapaVisualPhp";
        $clave      = "12CvP2015";
        $source     = $this->sourcecons;
        //$url        = "http://192.168.100.11:8080/consultas/ServicioWebConsultas?wsdl";
        //$host       = "192.168.100.11";
        $url        = $this->url.$this->urlConsulta;
        $host       = $this->host;
        $trama      = "<usuario>".$idEstudiante."</usuario><rol>".$idRol."</rol>";
        $response=$ws->doRequestSreReceptaTransacionCarreras($trama,$source,$tipo,$usuario,$clave,$url,$host);
         
        return $response;     
}#end function

public function getConsultaDatos_Matricula($idEstudiante,$idRol,$idCarrera){
        $ws=new AcademicoSoap();
        $tipo       = "3";
        $usuario    = "CapaVisualPhp";
        $clave      = "12CvP2015";
        $source     = $this->sourcecons;
        $url        = $this->url.$this->urlConsulta;
        $host       = $this->host;
        $trama      = "<usuario>".$idEstudiante."</usuario><rol>".$idRol."</rol>";
        $response=$ws->doRequestSreReceptaTransacion_matriculacion($trama,$source,$tipo,$usuario,$clave,$url,$host);
        return $response;     
}#end function

public function setMatricula_Estudiante($trama){
        $ws=new AcademicoSoap();
        $tipo       = "3";
        $usuario    = "CapaVisualPhp";
        $clave      = "12CvP2015";
        $source     = $this->sourcecons;
        $url        = $this->url.$this->urlConsulta;
        $host       = $this->host;
        //$trama      = "<usuario>".$idEstudiante."</usuario><rol>".$idRol."</rol>";
        $response=$ws->doSetMatricula($trama,$source,$tipo,$usuario,$clave,$url,$host);
        return $response;     
}#end function

 public function Docentes_gettareaxparcial($trama){
           $ws         = new AcademicoSoap();
           $tipo       = "24";
           $usuario    = "CapaVisual";
           $clave      = "123";
           $source     = $this->sourcepro;
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
           
           
                   
           
           $response=$ws->doRequestSreReceptaTransacionConsultasdoc2($trama,$source,$tipo,$usuario,$clave,$url,$host, $XML);

           return $response;
   }#end function

      public function Docentes_getAlumnos($trama){
           $ws         = new AcademicoSoap();
           $tipo       = "9";
           $usuario    = $this->usuario;
           $clave      = $this->clave;
           $source     = $this->sourcecons;
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
           $source     =$this->sourcepro;
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
           $source     =$this->sourcepro;
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
           $source     = $this->sourcecons;
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
                     <fecha>2015-10-17</fecha>
                     <ingreso>1</ingreso>
                  </registro>
                  <registro>
                     <fecha>2015-10-18</fecha>
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

}#end class
     
	
	
	
	