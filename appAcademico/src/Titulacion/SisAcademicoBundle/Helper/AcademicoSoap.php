<?php
namespace Titulacion\SisAcademicoBundle\Helper;
#purueba de git
include ('XmlParsero.php');

class AcademicoSoap {
    private $host   =NULL;
    private $url    =NULL;
    private $v_axis =NULL;

/**
 * [funcion que permite receptar el xml del webservice de los procedimientos]
 */
function doRequestSreReceptaTransacionProcedimientos($datosCuenta,$source,$tipo,$usuario,$clave,$url,$host){
// echo '<pre>'; var_dump($datosCuenta,$source,$tipo,$usuario,$clave,$url,$host); exit();
    $post_string="
    <soapenv:Envelope xmlns:soapenv=\"http://schemas.xmlsoap.org/soap/envelope/\" xmlns:ser=\"http://servicios.ug.edu.ec/\">
    <soapenv:Header/>
    <soapenv:Body>
                <ser:ejecucionObjeto>
                    <dataSource>".$source."</dataSource>
                    <idServicio>".$tipo."</idServicio>
                    <usuario>".$usuario."</usuario>
                    <clave>".$clave."</clave>
                    <parametrosObjeto>
                        <parametros>
                            <px_xml>
                            <items>
                                <item>
                                   ".$datosCuenta."
                                </item>
                            </items>
                            </px_xml>
                        </parametros>
                    </parametrosObjeto>
                </ser:ejecucionObjeto>
    </soapenv:Body>
    </soapenv:Envelope>";

            $headers=array('Content-Length: '.strlen($post_string),'Content-Type: text/xml;charset=UTF-8','SOAPAction: "http://servicios.ug.edu.ec//ejecucionConsulta"','Host:'.$host,'Proxy-Connection: Keep-Alive','User-Agent: Apache-HttpClient/4.1.1 (java 1.5)' );
            $soap_do = curl_init();
            curl_setopt ($soap_do, CURLOPT_VERBOSE , true );
            curl_setopt($soap_do, CURLOPT_URL,            $url );
            curl_setopt($soap_do, CURLOPT_CONNECTTIMEOUT, 10);
            curl_setopt($soap_do, CURLOPT_TIMEOUT,        5*60);
            curl_setopt($soap_do, CURLOPT_RETURNTRANSFER, true );
            curl_setopt($soap_do, CURLOPT_PORT,8080);
            curl_setopt($soap_do, CURLOPT_POST, true);
            curl_setopt($soap_do, CURLOPT_POSTFIELDS,$post_string);
            curl_setopt($soap_do, CURLOPT_HTTPHEADER,$headers);
            $result = curl_exec($soap_do);

        // echo '<pre>'; var_dump($result); exit();

// echo '<pre>'; var_dump($result); exit();

// $result =  <<<XML
// <soap:Envelope xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
//    <soap:Body>
//       <ns2:ejecucionObjetoResponse xmlns:ns2="http://servicios.ug.edu.ec/">
//          <return>
//             <codigoRespuesta>0</codigoRespuesta>
//             <estado>F</estado>
//             <idHistorico>53</idHistorico>
//             <mensajeRespuesta>ok</mensajeRespuesta>
//             <resultadoObjeto>
//                <parametrosSalida>
//                   <pi_estado>1</pi_estado>
//                   <pv_mensaje>USUARIO CORRECTO</pv_mensaje>
//                   <pv_codTrans>1</pv_codTrans>
//                   <PX_SALIDA><![CDATA[<registros><Usuario>1</Usuario><nombreUsuario>USUARIO PRIMER</nombreUsuario><cedula>0924393861</cedula><mail>ALGUIEN@USUARIO.COM</mail><idRol>3</idRol><descRol>ESTUDIANTE</descRol><carrrera>CARRERA DE INGENIERIA EN SISTEMAS</carrrera><idCarrera>3</idCarrera></registros><registros><Usuario>1</Usuario><nombreUsuario>USUARIO PRIMER</nombreUsuario><cedula>0924393861</cedula><mail>ALGUIEN@USUARIO.COM</mail><idRol>2</idRol><descRol>DOCENTE</descRol><carrrera>CARRERA DE INGENIERIA EN SISTEMAS</carrrera><idCarrera>3</idCarrera></registros>]]></PX_SALIDA>
//                </parametrosSalida>
//             </resultadoObjeto>
//          </return>
//       </ns2:ejecucionObjetoResponse>
//    </soap:Body>
// </soap:Envelope>
// XML;


// echo '<pre>'; var_dump($result); exit();

    if(!$result){
        return "error";
    }else{

        $response = preg_replace("/(<\/?)(\w+):([^>]*>)/", "$1$2$3", $result);
        $xml = new \SimpleXMLElement($response);
        $body = $xml->xpath('//soapBody')[0];
        $return = $xml->xpath('//return')[0];

        $resultadoObjeto = $xml->xpath('//PX_SALIDA')[0];
        $resultadoObjeto = $this->Response("<elements>".$resultadoObjeto."</elements>");
        // $cabecera   = new Cabeceras();
        // $respuesta  = $cabecera->eliminaCabecerasRespuesta($result);
        // $respuesta  = $this->Response("<elements>".$respuesta."</elements>");
        // $respuesta   = $cabecera->ReemplazaCaracteres($respuesta);
        // return $respuesta;

        return $resultadoObjeto;
    }
}


/**
 * [funcion que permite receptar el xml del webservice de los consultas]
 */
function doRequestSreReceptaTransacionConsultas($datosCuenta,$source,$tipo,$usuario,$clave,$url,$host, $XML=NULL){

$post_string="
        <soapenv:Envelope xmlns:soapenv=\"http://schemas.xmlsoap.org/soap/envelope/\" xmlns:ser=\"http://servicios.ug.edu.ec/\">
        <soapenv:Header/>
        <soapenv:Body>
            <ser:ejecucionConsulta>
                <dataSource>".$source."</dataSource>
                <idServicio>".$tipo."</idServicio>
                <usuario>".$usuario."</usuario>
                <clave>".$clave."</clave>
                <parametrosConsulta>
                    <parametros>
                        ".$datosCuenta."
                    </parametros>
                </parametrosConsulta>
            </ser:ejecucionConsulta>
        </soapenv:Body>
        </soapenv:Envelope>";

        $headers=array('Content-Length: '.strlen($post_string),'Content-Type: text/xml;charset=UTF-8','SOAPAction: "http://servicios.ug.edu.ec//ejecucionConsulta"','Host:'.$host,'Proxy-Connection: Keep-Alive','User-Agent: Apache-HttpClient/4.1.1 (java 1.5)' );
        $soap_do = curl_init();
        curl_setopt ($soap_do, CURLOPT_VERBOSE , true );
        curl_setopt($soap_do, CURLOPT_URL,            $url );
        curl_setopt($soap_do, CURLOPT_CONNECTTIMEOUT, 10);
        curl_setopt($soap_do, CURLOPT_TIMEOUT,        5*60);
        curl_setopt($soap_do, CURLOPT_RETURNTRANSFER, true );
        curl_setopt($soap_do, CURLOPT_PORT,8080);
        curl_setopt($soap_do, CURLOPT_POST, true);
        curl_setopt($soap_do, CURLOPT_POSTFIELDS,$post_string);
        curl_setopt($soap_do, CURLOPT_HTTPHEADER,$headers);
        // $result = curl_exec($soap_do);

$result = $XML;





    if(!$result){
        return "error";
    }else{

        $response = preg_replace("/(<\/?)(\w+):([^>]*>)/", "$1$2$3", $result);
        $xml = new \SimpleXMLElement($response);
        $body = $xml->xpath('//soapBody')[0];
        $return = $xml->xpath('//return')[0];

        $respuestaConsulta = $xml->xpath('//respuestaConsulta')[0];
        // $cabecera   = new Cabeceras();
        // $respuesta  = $cabecera->eliminaCabecerasRespuesta($result);
        // $respuesta  = $this->Response("<elements>".$respuesta."</elements>");
        // $respuesta   = $cabecera->ReemplazaCaracteres($respuesta);
        // return $respuesta;
        return $respuestaConsulta;
    }
}#end function

function doRequestSreReceptaTransacionConsultasdoc($datosCuenta,$source,$tipo,$usuario,$clave,$url,$host, $XML=NULL){
//echo "-----------------------------------------".$datosCuenta."--".$source."--".$tipo."--".$usuario."--".$clave."--".$url."--".$host."--";
   $post_string="
        <soapenv:Envelope xmlns:soapenv=\"http://schemas.xmlsoap.org/soap/envelope/\" xmlns:ser=\"http://servicios.ug.edu.ec/\">
        <soapenv:Header/>
        <soapenv:Body>
            <ser:ejecucionConsulta>
                <dataSource>".$source."</dataSource>
                <idServicio>".$tipo."</idServicio>
                <usuario>".$usuario."</usuario>
                <clave>".$clave."</clave>
                <parametrosConsulta>
                    <parametros>
                        ".$datosCuenta."
                    </parametros>
                </parametrosConsulta>
            </ser:ejecucionConsulta>
        </soapenv:Body>
        </soapenv:Envelope>";

   $headers=array('Content-Length: '.strlen($post_string),'Content-Type: text/xml;charset=UTF-8','SOAPAction: "http://servicios.ug.edu.ec//ejecucionConsulta"','Host:'.$host,'Proxy-Connection: Keep-Alive','User-Agent: Apache-HttpClient/4.1.1 (java 1.5)' );
   $soap_do = curl_init();
   curl_setopt ($soap_do, CURLOPT_VERBOSE , true );
   curl_setopt($soap_do, CURLOPT_URL,            $url );
   curl_setopt($soap_do, CURLOPT_CONNECTTIMEOUT, 10);
   curl_setopt($soap_do, CURLOPT_TIMEOUT,        5*60);
   curl_setopt($soap_do, CURLOPT_RETURNTRANSFER, true );
   curl_setopt($soap_do, CURLOPT_PORT,8080);
   curl_setopt($soap_do, CURLOPT_POST, true);
   curl_setopt($soap_do, CURLOPT_POSTFIELDS,$post_string);
   curl_setopt($soap_do, CURLOPT_HTTPHEADER,$headers);

   if($XML==NULL){
      $result = curl_exec($soap_do);
   }
   else {
      $result = $XML;
   }
//var_dump($result);
   if(!$result){
        return "error";
   }else{
         /*$response = preg_replace("/(<\/?)(\w+):([^>]*>)/", "$1$2$3", $result);
         $xml = new \SimpleXMLElement($response);
         $body = $xml->xpath('//soapBody')[0];
         $return = $xml->xpath('//return')[0];
         $respuestaConsulta = $xml->xpath('//respuestaConsulta')[0];
         return $respuestaConsulta; */

         $response  = preg_replace("/(<\/?)(\w+):([^>]*>)/", "$1$2$3", $result);
         $respuesta  = $this->eliminaCabecerasAcademicoConsultas($response);
         $respuesta  = $this->Response("<registros>".$respuesta."</registros>");

         //var_dump($respuesta);

         return $respuesta;




    }
}#end function


function eliminaCabecerasAcademicoConsultas($result)
{
   $palabraEtiq      = "registros";
   $etiquetaAbre     = "<".$palabraEtiq.">";
   $etiquetaCierra   = "</".$palabraEtiq.">";
   $countEtiquetaAbre = count($etiquetaAbre);

   $cadena=substr($result,strpos($result,$etiquetaAbre)+$countEtiquetaAbre, strlen($result));
   $cadena=substr($cadena,0,strpos($cadena,$etiquetaCierra));
   return $cadena;
}



function doRequestSreReceptaTransacionObjetos($datosCuenta,$source,$tipo,$usuario,$clave,$url,$host,$xpath=NULL, $XML=NULL){

$post_string="
         <soapenv:Envelope xmlns:soapenv=\"http://schemas.xmlsoap.org/soap/envelope/\" xmlns:ser=\"http://servicios.ug.edu.ec/\">
            <soapenv:Header/>
            <soapenv:Body>
               <ser:ejecucionObjeto>
                  <dataSource>".$source."</dataSource>
                  <idServicio>".$tipo."</idServicio>
                  <usuario>".$usuario."</usuario>
                  <clave>".$clave."</clave>
                  <parametrosObjeto>
                     <parametros>
                     ".$datosCuenta."
                 </parametros>
                  </parametrosObjeto>
               </ser:ejecucionObjeto>
            </soapenv:Body>
         </soapenv:Envelope>";

        $headers=array('Content-Length: '.strlen($post_string),'Content-Type: text/xml;charset=UTF-8','SOAPAction: "http://servicios.ug.edu.ec//ejecucionObjeto"','Host:'.$host,'Proxy-Connection: Keep-Alive','User-Agent: Apache-HttpClient/4.1.1 (java 1.5)' );
        $soap_do = curl_init();
        curl_setopt($soap_do, CURLOPT_VERBOSE ,       true );
        curl_setopt($soap_do, CURLOPT_URL,            $url );
        curl_setopt($soap_do, CURLOPT_CONNECTTIMEOUT, 10);
        curl_setopt($soap_do, CURLOPT_TIMEOUT,        5*60);
        curl_setopt($soap_do, CURLOPT_RETURNTRANSFER, true );
        curl_setopt($soap_do, CURLOPT_PORT,           8080);
        curl_setopt($soap_do, CURLOPT_POST,           true);
        curl_setopt($soap_do, CURLOPT_POSTFIELDS,     $post_string);
        curl_setopt($soap_do, CURLOPT_HTTPHEADER,     $headers);
        if($XML==NULL){
            $result = curl_exec($soap_do);
        }
        else {
           $result = $XML;
        }
    if(!$result){
        return "error";
    }else{
         $response  = preg_replace("/(<\/?)(\w+):([^>]*>)/", "$1$2$3", $result);
         $respuesta  = $this->eliminaCabecerasAcademico($response);
         $respuesta  = $this->Response("<elements>".$respuesta."</elements>");
         $respuesta  = $this->ReemplazaCaracteres($respuesta[0]['salida']);
         $respuesta  = $this->Response($respuesta);
//         echo "<pre>";
//         var_dump($respuesta);
//         echo "</pre>";
//         exit();
         return $respuesta;
    }
}#end function

function doRequestSreReceptaTransacionObjetos_Registros($datosCuenta,$source,$tipo,$usuario,$clave,$url,$host, $xmlData){


$post_string="
         <soapenv:Envelope xmlns:soapenv=\"http://schemas.xmlsoap.org/soap/envelope/\" xmlns:ser=\"http://servicios.ug.edu.ec/\">
            <soapenv:Header/>
            <soapenv:Body>
               <ser:ejecucionObjeto>
                  <dataSource>".$source."</dataSource>
                  <idServicio>".$tipo."</idServicio>
                  <usuario>".$usuario."</usuario>
                  <clave>".$clave."</clave>
                  <parametrosObjeto>
                     <parametros>
                     ".$datosCuenta."
                 </parametros>
                  </parametrosObjeto>
               </ser:ejecucionObjeto>
            </soapenv:Body>
         </soapenv:Envelope>";
   //echo $post_string;
        $headers=array('Content-Length: '.strlen($post_string),'Content-Type: text/xml;charset=UTF-8','SOAPAction: "http://servicios.ug.edu.ec//ejecucionObjeto"','Host:'.$host,'Proxy-Connection: Keep-Alive','User-Agent: Apache-HttpClient/4.1.1 (java 1.5)' );
        $soap_do = curl_init();
        curl_setopt($soap_do, CURLOPT_VERBOSE ,       true );
        curl_setopt($soap_do, CURLOPT_URL,            $url );
        curl_setopt($soap_do, CURLOPT_CONNECTTIMEOUT, 10);
        curl_setopt($soap_do, CURLOPT_TIMEOUT,        5*60);
        curl_setopt($soap_do, CURLOPT_RETURNTRANSFER, true );
        curl_setopt($soap_do, CURLOPT_PORT,           8080);
        curl_setopt($soap_do, CURLOPT_POST,           true);
        curl_setopt($soap_do, CURLOPT_POSTFIELDS,     $post_string);
        curl_setopt($soap_do, CURLOPT_HTTPHEADER,     $headers);

        if($xmlData["XML_test"]==NULL){
            $result = curl_exec($soap_do);
        }
        else {
           $result = $xmlData["XML_test"];
        }
        //var_dump($result);
    if(!$result){
        return "error";
    }else{
         $response  = preg_replace("/(<\/?)(\w+):([^>]*>)/", "$1$2$3", $result);
         $respuesta  = $this->eliminaCabecerasAcademico($response);
         $respuesta  = $this->Response("<".$xmlData["bloqueRegistros"].">".$respuesta."</".$xmlData["bloqueRegistros"].">");
         $respuesta  = $this->ReemplazaCaracteres($respuesta[0][$xmlData["bloqueSalida"]]);
         //$respuesta  = $this->Response($respuesta);
         $xml              = simplexml_load_string($respuesta, "SimpleXMLElement", LIBXML_NOCDATA);
         $json             = json_encode($xml);
         $arrayRespuesta   = json_decode($json,TRUE);

         return $arrayRespuesta;
    }
}#end function


function doRequestSreReceptaTransacionCarreras($datosCuenta,$source,$tipo,$usuario,$clave,$url,$host)
{
// echo '<pre>'; var_dump($datosCuenta,$source,$tipo,$usuario,$clave,$url,$host); exit();
$post_string=" <soapenv:Envelope xmlns:soapenv='http://schemas.xmlsoap.org/soap/envelope/' xmlns:ser='http://servicios.ug.edu.ec/'>
   <soapenv:Header/>
   <soapenv:Body>
      <ser:ejecucionConsulta>
         <dataSource>".$source."</dataSource>
         <idServicio>".$tipo."</idServicio>
         <usuario>".$usuario."</usuario>
         <clave>".$clave."</clave>
         <parametrosConsulta>
            <parametros>
                ".$datosCuenta."
            </parametros>
         </parametrosConsulta>
      </ser:ejecucionConsulta>
   </soapenv:Body>
</soapenv:Envelope> ";

        $headers=array('Content-Length: '.strlen($post_string),'Content-Type: text/xml;charset=UTF-8','SOAPAction: "http://servicios.ug.edu.ec//ejecucionConsulta"','Host:'.$host,'Proxy-Connection: Keep-Alive','User-Agent: Apache-HttpClient/4.1.1 (java 1.5)' );
        $soap_do = curl_init();
        curl_setopt ($soap_do, CURLOPT_VERBOSE , true );
        curl_setopt($soap_do, CURLOPT_URL,            $url );
        curl_setopt($soap_do, CURLOPT_CONNECTTIMEOUT, 10);
        curl_setopt($soap_do, CURLOPT_TIMEOUT,        5*60);
        curl_setopt($soap_do, CURLOPT_RETURNTRANSFER, true );
        curl_setopt($soap_do, CURLOPT_PORT,8080);
        curl_setopt($soap_do, CURLOPT_POST, true);
        curl_setopt($soap_do, CURLOPT_POSTFIELDS,$post_string);
        curl_setopt($soap_do, CURLOPT_HTTPHEADER,$headers);
        $result = curl_exec($soap_do);


// $result =  <<<XML
// <soap:Envelope xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
//    <soap:Body>
//       <ns2:ejecucionConsultaResponse xmlns:ns2="http://servicios.ug.edu.ec/">
//          <return>
//             <codigoRespuesta>0</codigoRespuesta>
//             <estado>F</estado>
//             <idHistorico>1079</idHistorico>
//             <mensajeRespuesta>ok</mensajeRespuesta>
//             <respuestaConsulta>
//                <registros>
//                   <registro>
//                      <id_sa_carrera>3</id_sa_carrera>
//                      <nombre>CARRERA DE INGENIERIA EN SISTEMAS</nombre>
//                      <id_sa_facultad>3</id_sa_facultad>
//                   </registro>

//                </registros>
//             </respuestaConsulta>
//          </return>
//       </ns2:ejecucionConsultaResponse>
//    </soap:Body>
// </soap:Envelope>
// XML;



    if(!$result)
    {
        return "error";
    }
    else
    {
// echo '<pre>'; var_dump($result); exit();
        $response = preg_replace("/(<\/?)(\w+):([^>]*>)/", "$1$2$3", $result);
        $xml = new \SimpleXMLElement($response);
        $body = $xml->xpath('//soapBody')[0];
        $return = $xml->xpath('//return')[0];
        $respuestaConsulta = $xml->xpath('//respuestaConsulta')[0];
        return $respuestaConsulta;
    }
}#end function

function doRequestSreReceptaTransacionnotas_ac($datosCuenta,$source,$tipo,$usuario,$clave,$url,$host){

 $post_string="
            <soapenv:Envelope xmlns:soapenv='http://schemas.xmlsoap.org/soap/envelope/' xmlns:ser='http://servicios.ug.edu.ec/'>
               <soapenv:Header/>
               <soapenv:Body>
                  <ser:ejecucionObjeto>
                      <dataSource>".$source."</dataSource>
                     <idServicio>".$tipo."</idServicio>
                     <usuario>".$usuario."</usuario>
                     <clave>".$clave."</clave>
                     <parametrosObjeto>
                        <parametros>
                           ".$datosCuenta."
                      </parametros>
                     </parametrosObjeto>
                  </ser:ejecucionObjeto>
               </soapenv:Body>
            </soapenv:Envelope>";
                    $headers=array('Content-Length: '.strlen($post_string),'Content-Type: text/xml;charset=UTF-8','SOAPAction: "http://servicios.ug.edu.ec//ejecucionObjeto"','Host:'.$host,'Proxy-Connection: Keep-Alive','User-Agent: Apache-HttpClient/4.1.1 (java 1.5)' );
                    $soap_do = curl_init();
                    curl_setopt ($soap_do, CURLOPT_VERBOSE , true );
                    curl_setopt($soap_do, CURLOPT_URL,            $url );
                    curl_setopt($soap_do, CURLOPT_CONNECTTIMEOUT, 10);
                    curl_setopt($soap_do, CURLOPT_TIMEOUT,        5*60);
                    curl_setopt($soap_do, CURLOPT_RETURNTRANSFER, true );
                    curl_setopt($soap_do, CURLOPT_PORT,8080);
                    curl_setopt($soap_do, CURLOPT_POST, true);
                    curl_setopt($soap_do, CURLOPT_POSTFIELDS,$post_string);
                    curl_setopt($soap_do, CURLOPT_HTTPHEADER,$headers);
                    $result = curl_exec($soap_do);

    if(!$result)
    {
        return "error";
    }
    else
    {
        $response  = $this->ReemplazaCaracteres($result);
        $response= preg_replace("/(<\/?)(\w+):([^>]*>)/", "$1$2$3", $response);
        $xml = new \SimpleXMLElement($response);
         $respuesta = $xml->xpath('//resultadoObjeto')[0];
        $respuesta = $xml->xpath('//parametrosSalida')[0];
        //$respuesta = $xml->xpath('//PX_Salida')[0];
        return $respuesta;
    }
}#end function



function doRequestSreReceptaTransacionnotas_nh($datosCuenta,$source,$tipo,$usuario,$clave,$url,$host){

 $post_string="
            <soapenv:Envelope xmlns:soapenv='http://schemas.xmlsoap.org/soap/envelope/' xmlns:ser='http://servicios.ug.edu.ec/'>
               <soapenv:Header/>
               <soapenv:Body>
                  <ser:ejecucionObjeto>
                     <dataSource>".$source."</dataSource>
                     <idServicio>".$tipo."</idServicio>
                     <usuario>".$usuario."</usuario>
                     <clave>".$clave."</clave>
                     <parametrosObjeto>
                        <parametros>
                           ".$datosCuenta."
                      </parametros>
                     </parametrosObjeto>
                  </ser:ejecucionObjeto>
               </soapenv:Body>
            </soapenv:Envelope>";
                    $headers=array('Content-Length: '.strlen($post_string),'Content-Type: text/xml;charset=UTF-8','SOAPAction: "http://servicios.ug.edu.ec//ejecucionObjeto"','Host:'.$host,'Proxy-Connection: Keep-Alive','User-Agent: Apache-HttpClient/4.1.1 (java 1.5)' );
                    $soap_do = curl_init();
                    curl_setopt ($soap_do, CURLOPT_VERBOSE , true );
                    curl_setopt($soap_do, CURLOPT_URL,            $url );
                    curl_setopt($soap_do, CURLOPT_CONNECTTIMEOUT, 10);
                    curl_setopt($soap_do, CURLOPT_TIMEOUT,        5*60);
                    curl_setopt($soap_do, CURLOPT_RETURNTRANSFER, true );
                    curl_setopt($soap_do, CURLOPT_PORT,8080);
                    curl_setopt($soap_do, CURLOPT_POST, true);
                    curl_setopt($soap_do, CURLOPT_POSTFIELDS,$post_string);
                    curl_setopt($soap_do, CURLOPT_HTTPHEADER,$headers);
                    $result = curl_exec($soap_do);


    if(!$result){
        return "error";
    }else{

        $response  = $this->ReemplazaCaracteres($result);

        $response= preg_replace("/(<\/?)(\w+):([^>]*>)/", "$1$2$3", $response);
        $xml = new \SimpleXMLElement($response);
        $return = $xml->xpath('//resultadoObjeto')[0];
        $respuesta = $xml->xpath('//parametrosSalida')[0];
        $respuesta = $xml->xpath('//PX_Salida')[0];
        //$respuesta = $xml->xpath('//ciclos')[0];

        return $respuesta;
    }
}#end function

function doRequestSreReceptaTransacionAsistencias ($datosCuenta,$source,$tipo,$usuario,$clave,$url,$host)
{
  $post_string="<soapenv:Envelope xmlns:soapenv='http://schemas.xmlsoap.org/soap/envelope/' xmlns:ser='http://servicios.ug.edu.ec/'>
               <soapenv:Header/>
               <soapenv:Body>
                  <ser:ejecucionObjeto>
                     <dataSource>".$source."</dataSource>
                     <idServicio>".$tipo."</idServicio>
                     <usuario>".$usuario."</usuario>
                     <clave>".$clave." </clave>
                     <parametrosObjeto>
                        <parametros>
                             ".$datosCuenta."
                      </parametros>
                     </parametrosObjeto>
                  </ser:ejecucionObjeto>
               </soapenv:Body>
              </soapenv:Envelope>";

    $headers=array('Content-Length: '.strlen($post_string),'Content-Type: text/xml;charset=UTF-8','SOAPAction: "http://servicios.ug.edu.ec//ejecucionObjeto"','Host:'.$host,'Proxy-Connection: Keep-Alive','User-Agent: Apache-HttpClient/4.1.1 (java 1.5)' );
    $soap_do = curl_init();
    curl_setopt ($soap_do, CURLOPT_VERBOSE , true );
    curl_setopt($soap_do, CURLOPT_URL,            $url );
    curl_setopt($soap_do, CURLOPT_CONNECTTIMEOUT, 10);
    curl_setopt($soap_do, CURLOPT_TIMEOUT,        5*60);
    curl_setopt($soap_do, CURLOPT_RETURNTRANSFER, true );
    curl_setopt($soap_do, CURLOPT_PORT,8080);
    curl_setopt($soap_do, CURLOPT_POST, true);
    curl_setopt($soap_do, CURLOPT_POSTFIELDS,$post_string);
    curl_setopt($soap_do, CURLOPT_HTTPHEADER,$headers);
    $result = curl_exec($soap_do);
    if(!$result)
    {
        return "error";
    }
    else
    {
        $response  = $this->ReemplazaCaracteres($result);
        $response= preg_replace("/(<\/?)(\w+):([^>]*>)/", "$1$2$3", $response);
        $xml = new \SimpleXMLElement($response);
        $return = $xml->xpath('//resultadoObjeto')[0];
        $respuesta = $xml->xpath('//parametrosSalida')[0];
        return $respuesta;
    }
} #end function





#================================================================================================================
     function doRequest2($idMetodo,$param1="?",$param2="?",$param3="?",$param4="?",$param5="?")
    {
        $v_produccion = 2;//variable q indica para que apunte a pre-produccion(0), produccionVersion1(1) o produccionVersion2(2)
        switch($v_produccion){
                case 0:
                        $host = 'ip:port';
                        $url ="http://ip:port/eis/eisSoapHttpPort?wsdl";//cambiar ip port
                        $v_axis = "jdbc/gyed";
                        break;
                case 1:
                        $host = 'ip:port';
                        $url = "http://ip:port/eis/eisSoapHttpPort?wsdl";//cambiar ip port
                        $v_axis = "jdbc/axis";
                        break;
                case 2:
                        $host = 'ip:port';
                        $v_axis = "jdbc/gye";
                        $url    = "http://ip:port/eismultiregistro/eisSoapHttpPort?wsdl";//cambiar ip port

                        break;
        }
        $this->host=$host;
        $this->url=$url;
        $this->v_axis=$v_axis;
        $post_string="
            <soapenv:Envelope xmlns:soapenv=\"http://schemas.xmlsoap.org/soap/envelope/\" xmlns:typ=\"http://axis/EISApiOnlineWS.wsdl/types/\">
                <soapenv:Header/>
                <soapenv:Body>
                       <typ:eipConsumeServicioElement>
                              <typ:dsId>$v_axis</typ:dsId>
                              <typ:pnIdServicioInformacion>$idMetodo</typ:pnIdServicioInformacion>
                              <typ:pvParametroBind1>$param1</typ:pvParametroBind1>
                              <typ:pvParametroBind2>$param2</typ:pvParametroBind2>
                              <typ:pvParametroBind3>$param3</typ:pvParametroBind3>
                              <typ:pvParametroBind4>$param4</typ:pvParametroBind4>
                              <typ:pvParametroBind5>$param5</typ:pvParametroBind5>
                       </typ:eipConsumeServicioElement>
                </soapenv:Body>
             </soapenv:Envelope>
             ";

        //echo $post_string;
        $headers=array('Content-Length: '.strlen($post_string),'Content-Type: text/xml;charset=UTF-8','SOAPAction: "http://axis/EISApiOnlineWS.wsdl/types//eipConsumeServicio"','Host: '.$host,'Proxy-Connection: Keep-Alive','User-Agent: Apache-HttpClient/4.1.1 (java 1.5)' );
                $soap_do = curl_init();
        curl_setopt ($soap_do, CURLOPT_VERBOSE , true );
        curl_setopt($soap_do, CURLOPT_URL,            $url );
        curl_setopt($soap_do, CURLOPT_CONNECTTIMEOUT, 10);
        curl_setopt($soap_do, CURLOPT_TIMEOUT,        5*60);
        curl_setopt($soap_do, CURLOPT_RETURNTRANSFER, true );
        curl_setopt($soap_do,CURLOPT_PORT,7777);
        curl_setopt($soap_do, CURLOPT_POST, true);
        curl_setopt($soap_do, CURLOPT_POSTFIELDS,$post_string);
        curl_setopt($soap_do, CURLOPT_HTTPHEADER,$headers);
        $result = curl_exec($soap_do);

        if(!$result){
                return "error";
        }else{


                return $this->Response($this->eliminaPadre($this->ReemplazaCaracteres($this->eliminaCabeceras($result))));
                //return $this->Response("<elements>".$this->ReemplazaCaracteres($this->eliminaCabeceras($result))."</elements>");
                // return $this->ReemplazaCaracteres($this->eliminaCabeceras($result));
                //return $this->Response($this->ReemplazaCaracteres($this->eliminaCabeceras($result)));
        }
    }

    function doRequestSR($idMetodo,$param1="?",$param2="?",$param3="?",$param4="?",$param5="?"){

        $v_produccion = 2;//variable q indica para que apunte a pre-produccion(0), produccionVersion1(1) o produccionVersion2(2)
        switch($v_produccion){
                case 0:
                        $host = 'ip:7777';
                        $url ="http://ip:7777/eis/eisSoapHttpPort?wsdl";
                        $v_axis = "jdbc/gyed";
                        break;
                case 1:
                        $host = 'ip:7777';
                        $url = "http://ip:7777/eis/eisSoapHttpPort?wsdl";
                        $v_axis = "jdbc/axis";
                        break;
                case 2:
                        $host = 'ip:7777';
                        $v_axis = "jdbc/gye";
                        $url    = "http://ip:port/eismultiregistro/eisSoapHttpPort?wsdl";
                        break;
        }
    $this->host=$host;
        $this->url=$url;
        $this->v_axis=$v_axis;

        $post_string="
        <soapenv:Envelope xmlns:soapenv=\"http://schemas.xmlsoap.org/soap/envelope/\" xmlns:typ=\"http://axis/EISApiOnlineWS.wsdl/types/\">
        <soapenv:Header/>
        <soapenv:Body>
            <typ:eipConsumeServicioElement>
                <typ:dsId>$v_axis</typ:dsId>
                <typ:pnIdServicioInformacion>$idMetodo</typ:pnIdServicioInformacion>
                <typ:pvParametroBind1>$param1</typ:pvParametroBind1>
                <typ:pvParametroBind2>$param2</typ:pvParametroBind2>
                <typ:pvParametroBind3>$param3</typ:pvParametroBind3>
                <typ:pvParametroBind4>$param4</typ:pvParametroBind4>
                <typ:pvParametroBind5>$param5</typ:pvParametroBind5>
            </typ:eipConsumeServicioElement>
        </soapenv:Body>
        </soapenv:Envelope>
        ";
        //echo $post_string;

        $headers=array('Content-Length: '.strlen($post_string),'Content-Type: text/xml;charset=UTF-8','SOAPAction: "http://axis/EISApiOnlineWS.wsdl/types//eipConsumeServicio"','Host: '.$host,'Proxy-Connection: Keep-Alive','User-Agent: Apache-HttpClient/4.1.1 (java 1.5)' );
        $soap_do = curl_init();
        curl_setopt ($soap_do, CURLOPT_VERBOSE , true );
        curl_setopt($soap_do, CURLOPT_URL,            $url );
        curl_setopt($soap_do, CURLOPT_CONNECTTIMEOUT, 10);
        curl_setopt($soap_do, CURLOPT_TIMEOUT,        5*60);
        curl_setopt($soap_do, CURLOPT_RETURNTRANSFER, true );
        curl_setopt($soap_do,CURLOPT_PORT,7777);
        curl_setopt($soap_do, CURLOPT_POST, true);
        curl_setopt($soap_do, CURLOPT_POSTFIELDS,$post_string);
        curl_setopt($soap_do, CURLOPT_HTTPHEADER,$headers);
        $result = curl_exec($soap_do);
        //echo $result;
        if(!$result){
            return "error";
        }else{
        return $this->ReemplazaCaracteres($this->eliminaCabeceras($result));
        }


    }


       function eliminaCabecerasAcademico($result)
   {
      $cadena=substr($result,strpos($result,"<parametrosSalida>")+18, strlen($result));
      $cadena=substr($cadena,0,strpos($cadena,"</parametrosSalida>"));
      return $cadena;
   }


    function eliminaCabecerasRegistros($result)
    {
        $cadena=substr($result,strpos($result,"<registros>")+9, strlen($result));
        $cadena=substr($cadena,0,strpos($cadena,"</registros>"));
        return $cadena;
    }


    function ReemplazaCaracteres($result)
    {
            $find = array("&lt;", "&gt;");
            $caracteres   = array("<", ">");
            return str_replace($find,$caracteres,$result);
    }

    function eliminaCabeceras($result)
    {
            $cadena=substr($result,strpos($result,"<ns0:pvresultadoOut>")+20, strlen($result));
            $cadena=substr($cadena,0,strpos($cadena,"</ns0:pvresultadoOut>"));
            return $cadena;
    }

    function eliminaPadre($result)
    {
        if($result!="")
        {
            $pos1=strpos($result,"<planes>");
            $pos2=strpos($result,"</planes>");

            $l=  strlen($result);

            $cadena = substr($result, $pos1, $pos2-$pos1);
            return $cadena."</planes>";
        }
        else
        {
            return $result;
        }
    }


    function Response ( $response )
    {
            $parser = new XmlParsero();
            $arrOutput = $parser->parse ($response);
            $childs = ( isset( $arrOutput[0]["children"] ) ? $arrOutput[0]["children"] : array( ) ) ;

            $results = array();
            for ($i = 0; $i < count($childs); $i++) {
                    $results[] = $this->toHashTable($childs[$i], NULL);
            }
            return $results;
    }

    function toHashTable ($root, $startPath)
    {
            $result = array();

            if ( isset($root["children"]))  {
                    $startPath = ( is_null($startPath) ? "" : $startPath . strtolower ($root["name"]) . "." );
                    $childs = $root["children"];
                    for ($i = 0; $i < count($childs); $i++) {
                            $result = array_merge ($result, $this->toHashTable($childs[$i], $startPath));
                    }
            }
            else{
                    /*lohana*/
                    if (count($root["attrs"])>0){
                            foreach($root['attrs'] as $key=> $value){
                                    $result[$startPath . strtolower ($root["name"]."_".$key)] = $startPath . strtolower ($value);
                            }
                    }
                    /*lohana*/
                    $result[$startPath . strtolower ($root["name"])] = (isset($root["tagData"]))? $root["tagData"] : "";
            }
            return $result;
    }

    function doRequestSreReceptaConsulta ($datosCuenta,$source,$tipo,$usuario,$clave,$url,$host)
    {
      $post_string=" <soapenv:Envelope xmlns:soapenv='http://schemas.xmlsoap.org/soap/envelope/' xmlns:ser='http://servicios.ug.edu.ec/'>
   <soapenv:Header/>
   <soapenv:Body>
      <ser:ejecucionConsulta>
         <dataSource>jdbc/consultasSaug</dataSource>
         <idServicio>6</idServicio>
         <usuario>CapaVisualPhp</usuario>
         <clave>12CvP2015</clave>
         <parametrosConsulta>
            <parametros>
                ".$datosCuenta."
            </parametros>
         </parametrosConsulta>
      </ser:ejecucionConsulta>
   </soapenv:Body>
</soapenv:Envelope> ";


        $headers=array('Content-Length: '.strlen($post_string),'Content-Type: text/xml;charset=UTF-8','SOAPAction: "http://servicios.ug.edu.ec//ejecucionConsulta"','Host:'.$host,'Proxy-Connection: Keep-Alive','User-Agent: Apache-HttpClient/4.1.1 (java 1.5)' );
        $soap_do = curl_init();
        curl_setopt ($soap_do, CURLOPT_VERBOSE , true );
        curl_setopt($soap_do, CURLOPT_URL,            $url );
        curl_setopt($soap_do, CURLOPT_CONNECTTIMEOUT, 10);
        curl_setopt($soap_do, CURLOPT_TIMEOUT,        5*60);
        curl_setopt($soap_do, CURLOPT_RETURNTRANSFER, true );
        curl_setopt($soap_do, CURLOPT_PORT,8080);
        curl_setopt($soap_do, CURLOPT_POST, true);
        curl_setopt($soap_do, CURLOPT_POSTFIELDS,$post_string);
        curl_setopt($soap_do, CURLOPT_HTTPHEADER,$headers);
        $result = curl_exec($soap_do);

      /*$result =  <<<XML
<soap:Envelope xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
   <soap:Body>
      <ns2:ejecucionConsultaResponse xmlns:ns2="http://servicios.ug.edu.ec/">
         <return>
            <codigoRespuesta>0</codigoRespuesta>
            <estado>F</estado>
            <idHistorico>2299</idHistorico>
            <mensajeRespuesta>exito</mensajeRespuesta>
            <respuestaConsulta>
               <registros>
                  <registros>
                     <id_sa_estudiante_carrera_materia>5</id_sa_estudiante_carrera_materia>
                     <idMateriaParalelo>3</idMateriaParalelo>
                     <materia>MATEMATICA 1</materia>
                     <curso>S1A</curso>
                     <promedio>7.00</promedio>
                     <idEstado>42</idEstado>
                     <destado>APROBADA</destado>
                     <idusuario>1</idusuario>
                     <dusaurio>FERNANDO</dusaurio>
                     <totalAistencia>3</totalAistencia>
                     <totalCumplida>2</totalCumplida>
                     <totalIncumplida>1</totalIncumplida>
                     <Porcentaje>0.666666</Porcentaje>
                  </registros>
                  <registros>
                     <id_sa_estudiante_carrera_materia>5</id_sa_estudiante_carrera_materia>
                     <idMateriaParalelo>3</idMateriaParalelo>
                     <materia>Programacion</materia>
                     <curso>S1A</curso>
                     <promedio>7.00</promedio>
                     <idEstado>42</idEstado>
                     <destado>APROBADA</destado>
                     <idusuario>1</idusuario>
                     <dusaurio>FERNANDO</dusaurio>
                     <totalAistencia>3</totalAistencia>
                     <totalCumplida>2</totalCumplida>
                     <totalIncumplida>1</totalIncumplida>
                     <Porcentaje>0.9</Porcentaje>
                  </registros>
               </registros>
            </respuestaConsulta>
         </return>
      </ns2:ejecucionConsultaResponse>
   </soap:Body>
</soap:Envelope>
XML;*/


    if(!$result){
        return "error";
    }else{

        $response = preg_replace("/(<\/?)(\w+):([^>]*>)/", "$1$2$3", $result);
        $xml = new \SimpleXMLElement($response);
        $body = $xml->xpath('//soapBody')[0];
        $return = $xml->xpath('//return')[0];
        $respuestaConsulta = $xml->xpath('//respuestaConsulta')[0];
        return $respuestaConsulta;
    }
    }



function doRequestSreReceptaTransacion_matriculacion($datosCuenta,$source,$tipo,$usuario,$clave,$url,$host){

 $post_string="
            <soapenv:Envelope xmlns:soapenv='http://schemas.xmlsoap.org/soap/envelope/' xmlns:ser='http://servicios.ug.edu.ec/'>
               <soapenv:Header/>
               <soapenv:Body>
                  <ser:ejecucionObjeto>
                      <dataSource>".$source."</dataSource>
                     <idServicio>".$tipo."</idServicio>
                     <usuario>".$usuario."</usuario>
                     <clave>".$clave."</clave>
                     <parametrosObjeto>
                        <parametros>
                           ".$datosCuenta."
                      </parametros>
                     </parametrosObjeto>
                  </ser:ejecucionObjeto>
               </soapenv:Body>
            </soapenv:Envelope>";
           // echo (string) $post_string;

                    $headers=array('Content-Length: '.strlen($post_string),'Content-Type: text/xml;charset=UTF-8','SOAPAction: "http://servicios.ug.edu.ec//ejecucionObjeto"','Host:'.$host,'Proxy-Connection: Keep-Alive','User-Agent: Apache-HttpClient/4.1.1 (java 1.5)' );
                    $soap_do = curl_init();
                    curl_setopt ($soap_do, CURLOPT_VERBOSE , true );
                    curl_setopt($soap_do, CURLOPT_URL,            $url );
                    curl_setopt($soap_do, CURLOPT_CONNECTTIMEOUT, 10);
                    curl_setopt($soap_do, CURLOPT_TIMEOUT,        5*60);
                    curl_setopt($soap_do, CURLOPT_RETURNTRANSFER, true );
                    curl_setopt($soap_do, CURLOPT_PORT,8080);
                    curl_setopt($soap_do, CURLOPT_POST, true);
                    curl_setopt($soap_do, CURLOPT_POSTFIELDS,$post_string);
                    curl_setopt($soap_do, CURLOPT_HTTPHEADER,$headers);
                    $result = curl_exec($soap_do);






    if(!$result)
    {
        return "error";
    }
    else
    {
        $response  = $this->ReemplazaCaracteres($result);
        $response= preg_replace("/(<\/?)(\w+):([^>]*>)/", "$1$2$3", $response);
        $xml = new \SimpleXMLElement($response);
         $respuesta = $xml->xpath('//resultadoObjeto')[0];
        $respuesta = $xml->xpath('//parametrosSalida')[0];
        //$respuesta = $xml->xpath('//PX_Salida')[0];
        return $respuesta;
    }





}#end function


function doSetMatricula($datosCuenta,$source,$tipo,$usuario,$clave,$url,$host){

 $post_string="
            <soapenv:Envelope xmlns:soapenv='http://schemas.xmlsoap.org/soap/envelope/' xmlns:ser='http://servicios.ug.edu.ec/'>
               <soapenv:Header/>
               <soapenv:Body>
                  <ser:ejecucionObjeto>
                      <dataSource>".$source."</dataSource>
                     <idServicio>".$tipo."</idServicio>
                     <usuario>".$usuario."</usuario>
                     <clave>".$clave."</clave>
                     <parametrosObjeto>
                        <parametros>
                            <PX_Entrada>
                                ".$datosCuenta."
                            </PX_Entrada>
                      </parametros>
                     </parametrosObjeto>
                  </ser:ejecucionObjeto>
               </soapenv:Body>
            </soapenv:Envelope>";
            $headers=array('Content-Length: '.strlen($post_string),'Content-Type: text/xml;charset=UTF-8','SOAPAction: "http://servicios.ug.edu.ec//ejecucionObjeto"','Host:'.$host,'Proxy-Connection: Keep-Alive','User-Agent: Apache-HttpClient/4.1.1 (java 1.5)' );
            $soap_do = curl_init();
            curl_setopt ($soap_do, CURLOPT_VERBOSE , true );
            curl_setopt($soap_do, CURLOPT_URL,            $url );
            curl_setopt($soap_do, CURLOPT_CONNECTTIMEOUT, 10);
            curl_setopt($soap_do, CURLOPT_TIMEOUT,        5*60);
            curl_setopt($soap_do, CURLOPT_RETURNTRANSFER, true );
            curl_setopt($soap_do, CURLOPT_PORT,8080);
            curl_setopt($soap_do, CURLOPT_POST, true);
            curl_setopt($soap_do, CURLOPT_POSTFIELDS,$post_string);
            curl_setopt($soap_do, CURLOPT_HTTPHEADER,$headers);
            $result = curl_exec($soap_do);


                    if(!$result)
                    {
                        return "error";
                    }
                    else
                    {

                        $response = preg_replace("/(<\/?)(\w+):([^>]*>)/", "$1$2$3", $result);
                        $xml = new \SimpleXMLElement($response);
                        $body = $xml->xpath('//soapBody')[0];
                        $return = $xml->xpath('//return')[0];
                        $respuestaConsulta = $xml->xpath('//resultadoObjeto')[0];
                        return $respuestaConsulta;
                    }

}#end function

function doRequestSreReceptaTransacionTurno($datosCuenta,$source,$tipo,$usuario,$clave,$url,$host)
{

$post_string=" <soapenv:Envelope xmlns:soapenv='http://schemas.xmlsoap.org/soap/envelope/' xmlns:ser='http://servicios.ug.edu.ec/'>
   <soapenv:Header/>
   <soapenv:Body>
      <ser:ejecucionConsulta>
         <dataSource>".$source."</dataSource>
         <idServicio>".$tipo."</idServicio>
         <usuario>".$usuario."</usuario>
         <clave>".$clave."</clave>
         <parametrosConsulta>
            <parametros>
                ".$datosCuenta."
            </parametros>
         </parametrosConsulta>
      </ser:ejecucionConsulta>
   </soapenv:Body>
</soapenv:Envelope> ";

        $headers=array('Content-Length: '.strlen($post_string),'Content-Type: text/xml;charset=UTF-8','SOAPAction: "http://servicios.ug.edu.ec//ejecucionConsulta"','Host:'.$host,'Proxy-Connection: Keep-Alive','User-Agent: Apache-HttpClient/4.1.1 (java 1.5)' );
        $soap_do = curl_init();
        curl_setopt ($soap_do, CURLOPT_VERBOSE , true );
        curl_setopt($soap_do, CURLOPT_URL,            $url );
        curl_setopt($soap_do, CURLOPT_CONNECTTIMEOUT, 10);
        curl_setopt($soap_do, CURLOPT_TIMEOUT,        5*60);
        curl_setopt($soap_do, CURLOPT_RETURNTRANSFER, true );
        curl_setopt($soap_do, CURLOPT_PORT,8080);
        curl_setopt($soap_do, CURLOPT_POST, true);
        curl_setopt($soap_do, CURLOPT_POSTFIELDS,$post_string);
        curl_setopt($soap_do, CURLOPT_HTTPHEADER,$headers);
        $result = curl_exec($soap_do);

    if(!$result)
    {
        return "error";
    }
    else
    {

        $response = preg_replace("/(<\/?)(\w+):([^>]*>)/", "$1$2$3", $result);
        $xml = new \SimpleXMLElement($response);
        $body = $xml->xpath('//soapBody')[0];
        $return = $xml->xpath('//return')[0];
        $respuestaConsulta = $xml->xpath('//respuestaConsulta')[0];
        return $respuestaConsulta;
    }
}#end function

function doRequestSreReceptaTransacionAnulacionMaterias($datosCuenta,$source,$tipo,$usuario,$clave,$url,$host){

// echo '<pre>'; var_dump($datosCuenta,$source,$tipo,$usuario,$clave,$url,$host); exit();
 $post_string="
            <soapenv:Envelope xmlns:soapenv='http://schemas.xmlsoap.org/soap/envelope/' xmlns:ser='http://servicios.ug.edu.ec/'>
               <soapenv:Header/>
               <soapenv:Body>
                  <ser:ejecucionObjeto>
                      <dataSource>".$source."</dataSource>
                     <idServicio>".$tipo."</idServicio>
                     <usuario>".$usuario."</usuario>
                     <clave>".$clave."</clave>
                     <parametrosObjeto>
                        <parametros>
                           ".$datosCuenta."
                      </parametros>
                     </parametrosObjeto>
                  </ser:ejecucionObjeto>
               </soapenv:Body>
            </soapenv:Envelope>";
                    $headers=array('Content-Length: '.strlen($post_string),'Content-Type: text/xml;charset=UTF-8','SOAPAction: "http://servicios.ug.edu.ec//ejecucionObjeto"','Host:'.$host,'Proxy-Connection: Keep-Alive','User-Agent: Apache-HttpClient/4.1.1 (java 1.5)' );
                    $soap_do = curl_init();
                    curl_setopt ($soap_do, CURLOPT_VERBOSE , true );
                    curl_setopt($soap_do, CURLOPT_URL,            $url );
                    curl_setopt($soap_do, CURLOPT_CONNECTTIMEOUT, 10);
                    curl_setopt($soap_do, CURLOPT_TIMEOUT,        5*60);
                    curl_setopt($soap_do, CURLOPT_RETURNTRANSFER, true );
                    curl_setopt($soap_do, CURLOPT_PORT,8080);
                    curl_setopt($soap_do, CURLOPT_POST, true);
                    curl_setopt($soap_do, CURLOPT_POSTFIELDS,$post_string);
                    curl_setopt($soap_do, CURLOPT_HTTPHEADER,$headers);
                    $result = curl_exec($soap_do);
/*$result =  <<<XML
 <soap:Envelope xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
    <soap:Body>
        <ns2:ejecucionObjetoResponse xmlns:ns2="http://servicios.ug.edu.ec/">
            <return>
                <codigoRespuesta>0</codigoRespuesta>
                <estado>F</estado>
                <idHistorico>58</idHistorico>
                <mensajeRespuesta>ok</mensajeRespuesta>
                <resultadoObjeto>
                    <parametrosSalida>
                        <PX_SALIDA>&lt;registro>&lt;mensaje>ANULACION DE MATERIA HA SIDO REGISTRADA CORRECTAMENTE&lt;/mensaje>&lt;/registro></PX_SALIDA>
                      <PI_ESTADO>1</PI_ESTADO>
                      <PV_MENSAJE>CONSULTA CON DATOS</PV_MENSAJE>
                      <PV_CODTRANS>7</PV_CODTRANS>
                      <PV_MENSAJE_TECNICO>&lt;registro>&lt;mensaje>ANULACION DE MATERIA HA SIDO REGISTRADA CORRECTAMENTE&lt;/mensaje>&lt;/registro></PV_MENSAJE_TECNICO>
               
                    </parametrosSalida>
                </resultadoObjeto>
            </return>
        </ns2:ejecucionObjetoResponse>
    </soap:Body>
</soap:Envelope>
XML;*/




    if(!$result)
    {
        return "error";
    }
    else
    {
        $response  = $this->ReemplazaCaracteres($result);
        $response= preg_replace("/(<\/?)(\w+):([^>]*>)/", "$1$2$3", $response);
        $xml = new \SimpleXMLElement($response);
         $respuesta = $xml->xpath('//resultadoObjeto')[0];
        $respuesta = $xml->xpath('//parametrosSalida')[0];
        //$respuesta = $xml->xpath('//PX_Salida')[0];
        return $respuesta;
    }





}#end function


    
   function eliminaCabecerasConsultas2($result)
    {
       $cadena=substr($result,strpos($result,"<registros>")+11, strlen($result));
       $cadena=substr($cadena,0,strpos($cadena,"</registros>"));
       return $cadena;
    }
    
    function eliminaCabecerasConsultas($result)
    {
       $cadena=substr($result,strpos($result,"<respuestaConsulta>")+19, strlen($result));
       $cadena=substr($cadena,0,strpos($cadena,"</respuestaConsulta>"));
       return $cadena;
    }
    
      


function doRequestSreReceptaCarrera_Matricula($datosCuenta,$source,$tipo,$usuario,$clave,$url,$host)
{

$post_string=" <soapenv:Envelope xmlns:soapenv='http://schemas.xmlsoap.org/soap/envelope/' xmlns:ser='http://servicios.ug.edu.ec/'>
   <soapenv:Header/>
   <soapenv:Body>
      <ser:ejecucionConsulta>
         <dataSource>".$source."</dataSource>
         <idServicio>".$tipo."</idServicio>
         <usuario>".$usuario."</usuario>
         <clave>".$clave."</clave>
         <parametrosConsulta>
            <parametros>
                ".$datosCuenta."
            </parametros>
         </parametrosConsulta>
      </ser:ejecucionConsulta>
   </soapenv:Body>
</soapenv:Envelope> ";

        $headers=array('Content-Length: '.strlen($post_string),'Content-Type: text/xml;charset=UTF-8','SOAPAction: "http://servicios.ug.edu.ec//ejecucionConsulta"','Host:'.$host,'Proxy-Connection: Keep-Alive','User-Agent: Apache-HttpClient/4.1.1 (java 1.5)' );
        $soap_do = curl_init();
        curl_setopt ($soap_do, CURLOPT_VERBOSE , true );
        curl_setopt($soap_do, CURLOPT_URL,            $url );
        curl_setopt($soap_do, CURLOPT_CONNECTTIMEOUT, 10);
        curl_setopt($soap_do, CURLOPT_TIMEOUT,        5*60);
        curl_setopt($soap_do, CURLOPT_RETURNTRANSFER, true );
        curl_setopt($soap_do, CURLOPT_PORT,8080);
        curl_setopt($soap_do, CURLOPT_POST, true);
        curl_setopt($soap_do, CURLOPT_POSTFIELDS,$post_string);
        curl_setopt($soap_do, CURLOPT_HTTPHEADER,$headers);
        $result = curl_exec($soap_do);

//  $result =  <<<XML
// <soap:Envelope xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
//     <soap:Body>
//       <ns2:ejecucionConsultaResponse xmlns:ns2="http://servicios.ug.edu.ec/">
//           <return>
//              <codigoRespuesta>0</codigoRespuesta>
//              <estado>F</estado>
//             <idHistorico>1079</idHistorico>
//             <mensajeRespuesta>ok</mensajeRespuesta>
//            <respuestaConsulta>
//                <registros>
//                   <registro>
//                       <id_sa_carrera>3</id_sa_carrera>
//                      <nombreCarrera>CARRERA DE INGENIERIA EN SISTEMAS</nombreCarrera>
//                      <id_sa_facultad>3</id_sa_facultad>
//                    </registro>

//                 </registros>
//              </respuestaConsulta>
//           </return>
//        </ns2:ejecucionConsultaResponse>
//     </soap:Body>
// </soap:Envelope>
// XML;



    if(!$result)
    {
        return "error";
    }
    else
    {

        $response = preg_replace("/(<\/?)(\w+):([^>]*>)/", "$1$2$3", $result);
        $xml = new \SimpleXMLElement($response);
        $body = $xml->xpath('//soapBody')[0];
        $return = $xml->xpath('//return')[0];
        $respuestaConsulta = $xml->xpath('//respuestaConsulta')[0];
        return $respuestaConsulta;
    }
}#end function



function doRequestSreReceptaTransacionRegistroMatricula($datosCuenta,$source,$tipo,$usuario,$clave,$url,$host){


 $post_string="
            <soapenv:Envelope xmlns:soapenv='http://schemas.xmlsoap.org/soap/envelope/' xmlns:ser='http://servicios.ug.edu.ec/'>
               <soapenv:Header/>
               <soapenv:Body>
                  <ser:ejecucionObjeto>
                      <dataSource>".$source."</dataSource>
                     <idServicio>".$tipo."</idServicio>
                     <usuario>".$usuario."</usuario>
                     <clave>".$clave."</clave>
                     <parametrosObjeto>
                        <parametros>
                           ".$datosCuenta." 
                      </parametros>
                     </parametrosObjeto>
                  </ser:ejecucionObjeto>
               </soapenv:Body>
            </soapenv:Envelope>";

        $headers=array('Content-Length: '.strlen($post_string),'Content-Type: text/xml;charset=UTF-8','SOAPAction: "http://servicios.ug.edu.ec//ejecucionConsulta"','Host:'.$host,'Proxy-Connection: Keep-Alive','User-Agent: Apache-HttpClient/4.1.1 (java 1.5)' );
        $soap_do = curl_init(); 
        curl_setopt ($soap_do, CURLOPT_VERBOSE , true );
        curl_setopt($soap_do, CURLOPT_URL,            $url );   
        curl_setopt($soap_do, CURLOPT_CONNECTTIMEOUT, 10); 
        curl_setopt($soap_do, CURLOPT_TIMEOUT,        5*60); 
        curl_setopt($soap_do, CURLOPT_RETURNTRANSFER, true );
        curl_setopt($soap_do, CURLOPT_PORT,8080);
        curl_setopt($soap_do, CURLOPT_POST, true);
        curl_setopt($soap_do, CURLOPT_POSTFIELDS,$post_string); 
        curl_setopt($soap_do, CURLOPT_HTTPHEADER,$headers);
        $result = curl_exec($soap_do);
        if(!$result)
        {
            return "error";
        }
        else
        {
            $response  = $this->ReemplazaCaracteres($result);
            $response= preg_replace("/(<\/?)(\w+):([^>]*>)/", "$1$2$3", $response);
            $xml = new \SimpleXMLElement($response);
             $respuesta = $xml->xpath('//resultadoObjeto')[0];
            $respuesta = $xml->xpath('//parametrosSalida')[0];
            //$respuesta = $xml->xpath('//PX_Salida')[0];
            return $respuesta;
        }
        
   }#end function   
   
   
  function doRequestSreReceptaTransacionConsultasdoc2($datosCuenta,$source,$tipo,$usuario,$clave,$url,$host, $XML=NULL){  

 $post_string="
            <soapenv:Envelope xmlns:soapenv='http://schemas.xmlsoap.org/soap/envelope/' xmlns:ser='http://servicios.ug.edu.ec/'>
               <soapenv:Header/>
               <soapenv:Body>
                  <ser:ejecucionObjeto>
                      <dataSource>".$source."</dataSource>
                     <idServicio>".$tipo."</idServicio>
                     <usuario>".$usuario."</usuario>
                     <clave>".$clave."</clave>
                     <parametrosObjeto>
                        <parametros>
                           ".$datosCuenta." 
                      </parametros>
                     </parametrosObjeto>
                  </ser:ejecucionObjeto>
               </soapenv:Body>
            </soapenv:Envelope>";

        $headers=array('Content-Length: '.strlen($post_string),'Content-Type: text/xml;charset=UTF-8','SOAPAction: "http://servicios.ug.edu.ec//ejecucionConsulta"','Host:'.$host,'Proxy-Connection: Keep-Alive','User-Agent: Apache-HttpClient/4.1.1 (java 1.5)' );
        $soap_do = curl_init(); 
        curl_setopt ($soap_do, CURLOPT_VERBOSE , true );
        curl_setopt($soap_do, CURLOPT_URL,            $url );   
        curl_setopt($soap_do, CURLOPT_CONNECTTIMEOUT, 10); 
        curl_setopt($soap_do, CURLOPT_TIMEOUT,        5*60); 
        curl_setopt($soap_do, CURLOPT_RETURNTRANSFER, true );
        curl_setopt($soap_do, CURLOPT_PORT,8080);
        curl_setopt($soap_do, CURLOPT_POST, true);
        curl_setopt($soap_do, CURLOPT_POSTFIELDS,$post_string); 
        curl_setopt($soap_do, CURLOPT_HTTPHEADER,$headers);
        $result = curl_exec($soap_do);
      
        //var_dump($result);
         $result = $XML;
         
         
         $xmlData["bloqueRegistros"]   = 'registros';
         $xmlData["bloqueSalida"]      = 'px_salida';
        
      $response	= preg_replace("/(<\/?)(\w+):([^>]*>)/", "$1$2$3", $result);
      
         $respuesta  = $this->eliminaCabecerasAcademico($response);
         
         $respuesta  = $this->Response("<".$xmlData["bloqueRegistros"].">".$respuesta."</".$xmlData["bloqueRegistros"].">");

         $respuesta  = $this->ReemplazaCaracteres($respuesta[0][$xmlData["bloqueSalida"]]);
         $respuesta =new \SimpleXMLElement($respuesta);
     
         return $respuesta;
        
        


    }  
    
    
    function doRequestConsultaAlumnos($datosCuenta,$source,$tipo,$usuario,$clave,$url,$host, $XML=NULL){  

                $post_string="
                        <soapenv:Envelope xmlns:soapenv=\"http://schemas.xmlsoap.org/soap/envelope/\" xmlns:ser=\"http://servicios.ug.edu.ec/\"> 
                        <soapenv:Header/>
                        <soapenv:Body>
                            <ser:ejecucionConsulta>
                                <dataSource>".$source."</dataSource>
                                <idServicio>".$tipo."</idServicio>
                                <usuario>".$usuario."</usuario>
                                <clave>".$clave."</clave>
                                <parametrosConsulta>
                                    <parametros>
                                        ".$datosCuenta."
                                    </parametros>
                                </parametrosConsulta>
                            </ser:ejecucionConsulta>
                        </soapenv:Body>
                        </soapenv:Envelope>";

                        $headers=array('Content-Length: '.strlen($post_string),'Content-Type: text/xml;charset=UTF-8','SOAPAction: "http://servicios.ug.edu.ec//ejecucionConsulta"','Host:'.$host,'Proxy-Connection: Keep-Alive','User-Agent: Apache-HttpClient/4.1.1 (java 1.5)' );
                        $soap_do = curl_init(); 
                        curl_setopt ($soap_do, CURLOPT_VERBOSE , true );
                        curl_setopt($soap_do, CURLOPT_URL,            $url );   
                        curl_setopt($soap_do, CURLOPT_CONNECTTIMEOUT, 10); 
                        curl_setopt($soap_do, CURLOPT_TIMEOUT,        5*60); 
                        curl_setopt($soap_do, CURLOPT_RETURNTRANSFER, true );
                        curl_setopt($soap_do, CURLOPT_PORT,8080);
                        curl_setopt($soap_do, CURLOPT_POST, true);
                        curl_setopt($soap_do, CURLOPT_POSTFIELDS,$post_string); 
                        curl_setopt($soap_do, CURLOPT_HTTPHEADER,$headers);
                        $result = curl_exec($soap_do);
                      //  var_dump($result);
                      //  exit();
                      if (isset($XML)){
                     $result = $XML;
                      }

                        $response = preg_replace("/(<\/?)(\w+):([^>]*>)/", "$1$2$3", $result);
                        
                       //  $respuesta =new \SimpleXMLElement($response);
                        $respuesta =$this->eliminaCabecerasConsultas($response);
                       /* var_dump($this->Response($respuesta));
                         exit();*/
                          
                        return $this->Response($respuesta);
      }
      
    function doRequestConsultaFechas($datosCuenta,$source,$tipo,$usuario,$clave,$url,$host, $XML=NULL){  

                $post_string="
                        <soapenv:Envelope xmlns:soapenv=\"http://schemas.xmlsoap.org/soap/envelope/\" xmlns:ser=\"http://servicios.ug.edu.ec/\"> 
                        <soapenv:Header/>
                        <soapenv:Body>
                            <ser:ejecucionConsulta>
                                <dataSource>".$source."</dataSource>
                                <idServicio>".$tipo."</idServicio>
                                <usuario>".$usuario."</usuario>
                                <clave>".$clave."</clave>
                                <parametrosConsulta>
                                    <parametros>
                                        ".$datosCuenta."
                                    </parametros>
                                </parametrosConsulta>
                            </ser:ejecucionConsulta>
                        </soapenv:Body>
                        </soapenv:Envelope>";

                        $headers=array('Content-Length: '.strlen($post_string),'Content-Type: text/xml;charset=UTF-8','SOAPAction: "http://servicios.ug.edu.ec//ejecucionConsulta"','Host:'.$host,'Proxy-Connection: Keep-Alive','User-Agent: Apache-HttpClient/4.1.1 (java 1.5)' );
                        $soap_do = curl_init(); 
                        curl_setopt ($soap_do, CURLOPT_VERBOSE , true );
                        curl_setopt($soap_do, CURLOPT_URL,            $url );   
                        curl_setopt($soap_do, CURLOPT_CONNECTTIMEOUT, 10); 
                        curl_setopt($soap_do, CURLOPT_TIMEOUT,        5*60); 
                        curl_setopt($soap_do, CURLOPT_RETURNTRANSFER, true );
                        curl_setopt($soap_do, CURLOPT_PORT,8080);
                        curl_setopt($soap_do, CURLOPT_POST, true);
                        curl_setopt($soap_do, CURLOPT_POSTFIELDS,$post_string); 
                        curl_setopt($soap_do, CURLOPT_HTTPHEADER,$headers);
                        $result = curl_exec($soap_do);
                      //  var_dump($result);
                      //  exit();
                      if (isset($XML)){
                     $result = $XML;
                      }

                        $response = preg_replace("/(<\/?)(\w+):([^>]*>)/", "$1$2$3", $result);
                        
                        $respuesta =$this->eliminaCabecerasConsultas($response);
                        
//                        var_dump($this->Response($respuesta));
//                         exit();
                       //  $respuesta =new \SimpleXMLElement($response);
                        
                       /* var_dump($this->Response($respuesta));
                         exit();*/
                          
                        return $this->Response($respuesta);
      }
      
  function doRequestIngresoNotas($datosCuenta,$source,$tipo,$usuario,$clave,$url,$host, $XML){  
                
  $post_string="<soapenv:Envelope xmlns:soapenv='http://schemas.xmlsoap.org/soap/envelope/' xmlns:ser='http://servicios.ug.edu.ec/'>
               <soapenv:Header/>
               <soapenv:Body>
                  <ser:ejecucionObjeto>
                     <dataSource>".$source."</dataSource>
                     <idServicio>".$tipo."</idServicio>
                     <usuario>".$usuario."</usuario>
                     <clave>".$clave." </clave>
                     <parametrosObjeto>
                        <parametros>
            				 ".$datosCuenta." 
            		  </parametros>
                     </parametrosObjeto>
                  </ser:ejecucionObjeto>
               </soapenv:Body>
              </soapenv:Envelope>";

                        $headers=array('Content-Length: '.strlen($post_string),'Content-Type: text/xml;charset=UTF-8','SOAPAction: "http://servicios.ug.edu.ec//ejecucionConsulta"','Host:'.$host,'Proxy-Connection: Keep-Alive','User-Agent: Apache-HttpClient/4.1.1 (java 1.5)' );
                        $soap_do = curl_init(); 
                        curl_setopt ($soap_do, CURLOPT_VERBOSE , true );
                        curl_setopt($soap_do, CURLOPT_URL,            $url );   
                        curl_setopt($soap_do, CURLOPT_CONNECTTIMEOUT, 10); 
                        curl_setopt($soap_do, CURLOPT_TIMEOUT,        5*60); 
                        curl_setopt($soap_do, CURLOPT_RETURNTRANSFER, true );
                        curl_setopt($soap_do, CURLOPT_PORT,8080);
                        curl_setopt($soap_do, CURLOPT_POST, true);
                        curl_setopt($soap_do, CURLOPT_POSTFIELDS,$post_string); 
                        curl_setopt($soap_do, CURLOPT_HTTPHEADER,$headers);
                        $result = curl_exec($soap_do);
//                        var_dump($result);
//                        exit();
               // $result = $XML;
                    $response = preg_replace("/(<\/?)(\w+):([^>]*>)/", "$1$2$3", $result);
                     $respuesta =new \SimpleXMLElement($response);
                    // var_dump($respuesta);
                    //     exit();
                    return $respuesta;
                    
            
}   



function doRequestSreReceptaTransacionConsultasCorreo($datosCuenta,$source,$tipo,$usuario,$clave,$url,$host, $XML=NULL){

    $post_string="
        <soapenv:Envelope xmlns:soapenv=\"http://schemas.xmlsoap.org/soap/envelope/\" xmlns:ser=\"http://servicios.ug.edu.ec/\">
        <soapenv:Header/>
        <soapenv:Body>
            <ser:ejecucionConsulta>
                <dataSource>".$source."</dataSource>
                <idServicio>".$tipo."</idServicio>
                <usuario>".$usuario."</usuario>
                <clave>".$clave."</clave>
                <parametrosConsulta>
                    <parametros>
                        ".$datosCuenta."
                    </parametros>
                </parametrosConsulta>
            </ser:ejecucionConsulta>
        </soapenv:Body>
        </soapenv:Envelope>";

    $headers=array('Content-Length: '.strlen($post_string),'Content-Type: text/xml;charset=UTF-8','SOAPAction: "http://servicios.ug.edu.ec//ejecucionConsulta"','Host:'.$host,'Proxy-Connection: Keep-Alive','User-Agent: Apache-HttpClient/4.1.1 (java 1.5)' );
    $soap_do = curl_init();
    curl_setopt ($soap_do, CURLOPT_VERBOSE , true );
    curl_setopt($soap_do, CURLOPT_URL,            $url );
    curl_setopt($soap_do, CURLOPT_CONNECTTIMEOUT, 10);
    curl_setopt($soap_do, CURLOPT_TIMEOUT,        5*60);
    curl_setopt($soap_do, CURLOPT_RETURNTRANSFER, true );
    curl_setopt($soap_do, CURLOPT_PORT,8080);
    curl_setopt($soap_do, CURLOPT_POST, true);
    curl_setopt($soap_do, CURLOPT_POSTFIELDS,$post_string);
    curl_setopt($soap_do, CURLOPT_HTTPHEADER,$headers);

    if($XML==NULL){
        $result = curl_exec($soap_do);
    }
    else {
        $result = $XML;
    }

    if(!$result){
        return "error";
    }else{

        $response  = preg_replace("/(<\/?)(\w+):([^>]*>)/", "$1$2$3", $result);
        $respuesta  = $this->eliminaCabecerasAcademicoConsultas($response);
        $respuesta  = $this->Response("<registros>".$respuesta."</registros>");

        return $respuesta;


    }
}#end function

function doRequestSreReceptaTransacionMantUsuario($datosCuenta,$source,$tipo,$usuario,$clave,$url,$host){

 $post_string="
            <soapenv:Envelope xmlns:soapenv='http://schemas.xmlsoap.org/soap/envelope/' xmlns:ser='http://servicios.ug.edu.ec/'>
               <soapenv:Header/>
               <soapenv:Body>
                    <ser:ejecucionObjeto>
                        <dataSource>".$source."</dataSource>
                        <idServicio>".$tipo."</idServicio>
                        <usuario>".$usuario."</usuario>
                        <clave>".$clave."</clave>
                        <parametrosObjeto>
                            <parametros>
                                ".$datosCuenta."
                            </parametros>
                        </parametrosObjeto>
                    </ser:ejecucionObjeto>
                </soapenv:Body>
            </soapenv:Envelope>";
                    $headers=array('Content-Length: '.strlen($post_string),'Content-Type: text/xml;charset=UTF-8','SOAPAction: "http://servicios.ug.edu.ec//ejecucionObjeto"','Host:'.$host,'Proxy-Connection: Keep-Alive','User-Agent: Apache-HttpClient/4.1.1 (java 1.5)' );
                    $soap_do = curl_init();
                    curl_setopt($soap_do, CURLOPT_VERBOSE , true );
                    curl_setopt($soap_do, CURLOPT_URL,            $url );
                    curl_setopt($soap_do, CURLOPT_CONNECTTIMEOUT, 10);
                    curl_setopt($soap_do, CURLOPT_TIMEOUT,        5*60);
                    curl_setopt($soap_do, CURLOPT_RETURNTRANSFER, true );
                    curl_setopt($soap_do, CURLOPT_PORT,8080);
                    curl_setopt($soap_do, CURLOPT_POST, true);
                    curl_setopt($soap_do, CURLOPT_POSTFIELDS,$post_string);
                    curl_setopt($soap_do, CURLOPT_HTTPHEADER,$headers);
                    $result = curl_exec($soap_do);
    if(!$result)
    {
        return "error";
    }
    else
    {
        $response  = $this->ReemplazaCaracteres($result);
        $response= preg_replace("/(<\/?)(\w+):([^>]*>)/", "$1$2$3", $response);
        $xml = new \SimpleXMLElement($response);
        $respuesta = $xml->xpath('//parametrosSalida')[0];
        // echo "<pre>";
        // var_dump($respuesta);
        // echo "</pre>";
        // exit();
        return $respuesta;
    }
}#end function



function doRequestSreReceptaTransacionRegistroOrdenPago($datosCuenta,$source,$tipo,$usuario,$clave,$url,$host){

 $post_string="
            <soapenv:Envelope xmlns:soapenv='http://schemas.xmlsoap.org/soap/envelope/' xmlns:ser='http://servicios.ug.edu.ec/'>
               <soapenv:Header/>
               <soapenv:Body>
                  <ser:ejecucionObjeto>
                      <dataSource>".$source."</dataSource>
                     <idServicio>".$tipo."</idServicio>
                     <usuario>".$usuario."</usuario>
                     <clave>".$clave."</clave>
                     <parametrosObjeto>
                        <parametros>
                           ".$datosCuenta."
                      </parametros>
                     </parametrosObjeto>
                  </ser:ejecucionObjeto>
               </soapenv:Body>
            </soapenv:Envelope>";
                    $headers=array('Content-Length: '.strlen($post_string),'Content-Type: text/xml;charset=UTF-8','SOAPAction: "http://servicios.ug.edu.ec//ejecucionObjeto"','Host:'.$host,'Proxy-Connection: Keep-Alive','User-Agent: Apache-HttpClient/4.1.1 (java 1.5)' );
                    $soap_do = curl_init();
                    curl_setopt ($soap_do, CURLOPT_VERBOSE , true );
                    curl_setopt($soap_do, CURLOPT_URL,            $url );
                    curl_setopt($soap_do, CURLOPT_CONNECTTIMEOUT, 10);
                    curl_setopt($soap_do, CURLOPT_TIMEOUT,        5*60);
                    curl_setopt($soap_do, CURLOPT_RETURNTRANSFER, true );
                    curl_setopt($soap_do, CURLOPT_PORT,8080);
                    curl_setopt($soap_do, CURLOPT_POST, true);
                    curl_setopt($soap_do, CURLOPT_POSTFIELDS,$post_string);
                    curl_setopt($soap_do, CURLOPT_HTTPHEADER,$headers);
                    $result = curl_exec($soap_do);


    if(!$result)
    {
        return "error";
    }
    else
    {
        $response  = $this->ReemplazaCaracteres($result);
        $response= preg_replace("/(<\/?)(\w+):([^>]*>)/", "$1$2$3", $response);
        $xml = new \SimpleXMLElement($response);
         $respuesta = $xml->xpath('//resultadoObjeto')[0];
        $respuesta = $xml->xpath('//parametrosSalida')[0];
        //$respuesta = $xml->xpath('//PX_Salida')[0];
        return $respuesta;
    }





}#end function

function doGeneraTurno($datosCuenta,$source,$tipo,$usuario,$clave,$url,$host){

 $post_string="
            <soapenv:Envelope xmlns:soapenv='http://schemas.xmlsoap.org/soap/envelope/' xmlns:ser='http://servicios.ug.edu.ec/'>
               <soapenv:Header/>
               <soapenv:Body>
                  <ser:ejecucionObjeto>
                      <dataSource>".$source."</dataSource>
                     <idServicio>".$tipo."</idServicio>
                     <usuario>".$usuario."</usuario>
                     <clave>".$clave."</clave>
                     <parametrosObjeto>
                        <parametros>
                           ".$datosCuenta."
                      </parametros>
                     </parametrosObjeto>
                  </ser:ejecucionObjeto>
               </soapenv:Body>
            </soapenv:Envelope>";
                    $headers=array('Content-Length: '.strlen($post_string),'Content-Type: text/xml;charset=UTF-8','SOAPAction: "http://servicios.ug.edu.ec//ejecucionObjeto"','Host:'.$host,'Proxy-Connection: Keep-Alive','User-Agent: Apache-HttpClient/4.1.1 (java 1.5)' );
                    $soap_do = curl_init();
                    curl_setopt ($soap_do, CURLOPT_VERBOSE , true );
                    curl_setopt($soap_do, CURLOPT_URL,            $url );
                    curl_setopt($soap_do, CURLOPT_CONNECTTIMEOUT, 10);
                    curl_setopt($soap_do, CURLOPT_TIMEOUT,        5*60);
                    curl_setopt($soap_do, CURLOPT_RETURNTRANSFER, true );
                    curl_setopt($soap_do, CURLOPT_PORT,8080);
                    curl_setopt($soap_do, CURLOPT_POST, true);
                    curl_setopt($soap_do, CURLOPT_POSTFIELDS,$post_string);
                    curl_setopt($soap_do, CURLOPT_HTTPHEADER,$headers);
                    $result = curl_exec($soap_do);

    if(!$result)
    {
        return "error";
    }
    else
    {
       $response = preg_replace("/(<\/?)(\w+):([^>]*>)/", "$1$2$3", $result);
        $xml = new \SimpleXMLElement($response);
        $body = $xml->xpath('//soapBody')[0];
        $return = $xml->xpath('//return')[0];
        $respuestaConsulta = $xml->xpath('//resultadoObjeto')[0];
        return $respuestaConsulta;
    }
}#end function

/**
 * [Funcion retorna todos los eventos creados]
 */
function  doRequestReceptaSoloEventosAcademicos($datosCuenta,$source,$tipo,$usuario,$clave,$url,$host){
  $post_string=" <soapenv:Envelope xmlns:soapenv='http://schemas.xmlsoap.org/soap/envelope/' xmlns:ser='http://servicios.ug.edu.ec/'>
           <soapenv:Header/>
           <soapenv:Body>
              <ser:ejecucionConsulta>
                 <dataSource>".$source."</dataSource>
                 <idServicio>".$tipo."</idServicio>
                 <usuario>".$usuario."</usuario>
                 <clave>".$clave."</clave>
                 <parametrosConsulta>
                    <parametros>
                        ".$datosCuenta."
                    </parametros>
                 </parametrosConsulta>
              </ser:ejecucionConsulta>
           </soapenv:Body>
        </soapenv:Envelope> ";

        $headers=array('Content-Length: '.strlen($post_string),'Content-Type: text/xml;charset=UTF-8','SOAPAction: "http://servicios.ug.edu.ec//ejecucionConsulta"','Host:'.$host,'Proxy-Connection: Keep-Alive','User-Agent: Apache-HttpClient/4.1.1 (java 1.5)' );
                $soap_do = curl_init();
                curl_setopt ($soap_do, CURLOPT_VERBOSE , true );
                curl_setopt($soap_do, CURLOPT_URL,            $url );
                curl_setopt($soap_do, CURLOPT_CONNECTTIMEOUT, 10);
                curl_setopt($soap_do, CURLOPT_TIMEOUT,        5*60);
                curl_setopt($soap_do, CURLOPT_RETURNTRANSFER, true );
                curl_setopt($soap_do, CURLOPT_PORT,8080);
                curl_setopt($soap_do, CURLOPT_POST, true);
                curl_setopt($soap_do, CURLOPT_POSTFIELDS,$post_string);
                curl_setopt($soap_do, CURLOPT_HTTPHEADER,$headers);
                // echo '<pre>'; var_dump($soap_do); exit();
                $result = curl_exec($soap_do);
                // echo '<pre>'; var_dump($result); exit();
        if(!$result)
          {
              return "error";
          }
          else
          {
              // echo '<pre>'; var_dump($result); exit();
            $response = preg_replace("/(<\/?)(\w+):([^>]*>)/", "$1$2$3", $result);
              $xml = new \SimpleXMLElement($response);
              // $body = $xml->xpath('//soapBody')[0];
              // $return = $xml->xpath('//registro')[0];
              // echo '<pre>'; var_dump($xml); exit();
              $resultadoObjeto = $xml->xpath('//registros')[0];
              // $resultadoObjeto = $xml->xpath('//registro')[0];
              // $deJson = json_encode($resultadoObjeto);
              $resultadoObjeto = json_encode($resultadoObjeto);
              $xml_array = json_decode($resultadoObjeto,TRUE);
              // $resultadoObjeto = $this->Response($resultadoObjeto);
              return $xml_array;
          }

}#end function
/**
 * [Funcion para crear un evento]
 */
function doInsertEventosAcademicos($datosCuenta,$source,$tipo,$usuario,$clave,$url,$host){
  // echo '<pre>'; var_dump($datosCuenta,$source,$tipo,$usuario,$clave,$url,$host); exit();
   $post_string="
            <soapenv:Envelope xmlns:soapenv='http://schemas.xmlsoap.org/soap/envelope/' xmlns:ser='http://servicios.ug.edu.ec/'>
               <soapenv:Header/>
               <soapenv:Body>
                  <ser:ejecucionObjeto>
                      <dataSource>".$source."</dataSource>
                     <idServicio>".$tipo."</idServicio>
                     <usuario>".$usuario."</usuario>
                     <clave>".$clave."</clave>
                     <parametrosObjeto>
                        <parametros>
                           ".$datosCuenta."
                      </parametros>
                     </parametrosObjeto>
                  </ser:ejecucionObjeto>
               </soapenv:Body>
            </soapenv:Envelope>";
                    $headers=array('Content-Length: '.strlen($post_string),'Content-Type: text/xml;charset=UTF-8','SOAPAction: "http://servicios.ug.edu.ec//ejecucionObjeto"','Host:'.$host,'Proxy-Connection: Keep-Alive','User-Agent: Apache-HttpClient/4.1.1 (java 1.5)' );
                    $soap_do = curl_init();
                    curl_setopt ($soap_do, CURLOPT_VERBOSE , true );
                    curl_setopt($soap_do, CURLOPT_URL,            $url );
                    curl_setopt($soap_do, CURLOPT_CONNECTTIMEOUT, 10);
                    curl_setopt($soap_do, CURLOPT_TIMEOUT,        5*60);
                    curl_setopt($soap_do, CURLOPT_RETURNTRANSFER, true );
                    curl_setopt($soap_do, CURLOPT_PORT,8080);
                    curl_setopt($soap_do, CURLOPT_POST, true);
                    curl_setopt($soap_do, CURLOPT_POSTFIELDS,$post_string);
                    curl_setopt($soap_do, CURLOPT_HTTPHEADER,$headers);
                    $result = curl_exec($soap_do);

                    $response = preg_replace("/(<\/?)(\w+):([^>]*>)/", "$1$2$3", $result);
                    $xml = new \SimpleXMLElement($response);
                    $body = $xml->xpath('//soapBody')[0];
                    $return = $xml->xpath('//return')[0];

                    $resultadoObjeto = $xml->xpath('//parametrosSalida')[0];

                    $resultadoObjeto = json_encode($resultadoObjeto);
                    $xml_array = json_decode($resultadoObjeto,TRUE);
                    // $resultadoObjeto = $this->Response($resultadoObjeto);
                    // $resultadoObjeto = $xml->xpath('//PV_MENSAJE')[0];
                    // $resultadoObjeto = $this->Response($resultadoObjeto);
                    // echo '<pre>'; var_dump($xml_array); exit();
                    // return $xml_array;
                    return $xml_array["PI_ESTADO"];
}#end function


//INSCRIPCION ADMIN
function doRequestSreReceptaTransacionCarrerasInscripcion($datosCuenta,$source,$tipo,$usuario,$clave,$url,$host)
{

$post_string=" <soapenv:Envelope xmlns:soapenv='http://schemas.xmlsoap.org/soap/envelope/' xmlns:ser='http://servicios.ug.edu.ec/'>
   <soapenv:Header/>
   <soapenv:Body>
      <ser:ejecucionConsulta>
         <dataSource>".$source."</dataSource>
         <idServicio>".$tipo."</idServicio>
         <usuario>".$usuario."</usuario>
         <clave>".$clave."</clave>
         <parametrosConsulta>
            <parametros>
                ".$datosCuenta."
            </parametros>
         </parametrosConsulta>
      </ser:ejecucionConsulta>
   </soapenv:Body>
</soapenv:Envelope> ";

        $headers=array('Content-Length: '.strlen($post_string),'Content-Type: text/xml;charset=UTF-8','SOAPAction: "http://servicios.ug.edu.ec//ejecucionConsulta"','Host:'.$host,'Proxy-Connection: Keep-Alive','User-Agent: Apache-HttpClient/4.1.1 (java 1.5)' );
        $soap_do = curl_init();
        curl_setopt ($soap_do, CURLOPT_VERBOSE , true );
        curl_setopt($soap_do, CURLOPT_URL,            $url );
        curl_setopt($soap_do, CURLOPT_CONNECTTIMEOUT, 10);
        curl_setopt($soap_do, CURLOPT_TIMEOUT,        5*60);
        curl_setopt($soap_do, CURLOPT_RETURNTRANSFER, true );
        curl_setopt($soap_do, CURLOPT_PORT,8080);
        curl_setopt($soap_do, CURLOPT_POST, true);
        curl_setopt($soap_do, CURLOPT_POSTFIELDS,$post_string);
        curl_setopt($soap_do, CURLOPT_HTTPHEADER,$headers);
        $result = curl_exec($soap_do);

    if(!$result)
    {
        return "error";
    }
    else
    {

        $response = preg_replace("/(<\/?)(\w+):([^>]*>)/", "$1$2$3", $result);
        $xml = new \SimpleXMLElement($response);
        $body = $xml->xpath('//soapBody')[0];
        $return = $xml->xpath('//return')[0];
        $respuestaConsulta = $xml->xpath('//respuestaConsulta')[0];
        return $respuestaConsulta;
    }
}#end function

function doRequestsListarInscripcion($datosCuenta,$source,$tipo,$usuario,$clave,$url,$host)
{

$post_string="
            <soapenv:Envelope xmlns:soapenv='http://schemas.xmlsoap.org/soap/envelope/' xmlns:ser='http://servicios.ug.edu.ec/'>
               <soapenv:Header/>
               <soapenv:Body>
                  <ser:ejecucionObjeto>
                      <dataSource>".$source."</dataSource>
                     <idServicio>".$tipo."</idServicio>
                     <usuario>".$usuario."</usuario>
                     <clave>".$clave."</clave>
                     <parametrosObjeto>
                        <parametros>
                           ".$datosCuenta."
                      </parametros>
                     </parametrosObjeto>
                  </ser:ejecucionObjeto>
               </soapenv:Body>
            </soapenv:Envelope>";
                    $headers=array('Content-Length: '.strlen($post_string),'Content-Type: text/xml;charset=UTF-8','SOAPAction: "http://servicios.ug.edu.ec//ejecucionObjeto"','Host:'.$host,'Proxy-Connection: Keep-Alive','User-Agent: Apache-HttpClient/4.1.1 (java 1.5)' );
                    $soap_do = curl_init();
                    curl_setopt ($soap_do, CURLOPT_VERBOSE , true );
                    curl_setopt($soap_do, CURLOPT_URL,            $url );
                    curl_setopt($soap_do, CURLOPT_CONNECTTIMEOUT, 10);
                    curl_setopt($soap_do, CURLOPT_TIMEOUT,        5*60);
                    curl_setopt($soap_do, CURLOPT_RETURNTRANSFER, true );
                    curl_setopt($soap_do, CURLOPT_PORT,8080);
                    curl_setopt($soap_do, CURLOPT_POST, true);
                    curl_setopt($soap_do, CURLOPT_POSTFIELDS,$post_string);
                    curl_setopt($soap_do, CURLOPT_HTTPHEADER,$headers);
                    $result = curl_exec($soap_do);


    if(!$result)
    {
        return "error";
    }
    else
    {
        $response  = $this->ReemplazaCaracteres($result);
        $response = preg_replace("/(<\/?)(\w+):([^>]*>)/", "$1$2$3", $response);
        $xml = new \SimpleXMLElement($response);
        $body = $xml->xpath('//soapBody')[0];
        $return = $xml->xpath('//return')[0];
        $respuestaConsulta = $xml->xpath('//resultadoObjeto')[0];
        $respuestaConsulta = $xml->xpath('//parametrosSalida')[0];
        return $respuestaConsulta;
    }
}#end function
function doSetActualizaInscripcion($datosCuenta,$source,$tipo,$usuario,$clave,$url,$host){

 $post_string="
            <soapenv:Envelope xmlns:soapenv='http://schemas.xmlsoap.org/soap/envelope/' xmlns:ser='http://servicios.ug.edu.ec/'>
               <soapenv:Header/>
               <soapenv:Body>
                  <ser:ejecucionObjeto>
                      <dataSource>".$source."</dataSource>
                     <idServicio>".$tipo."</idServicio>
                     <usuario>".$usuario."</usuario>
                     <clave>".$clave."</clave>
                     <parametrosObjeto>
                        <parametros>
                            ".$datosCuenta."
                      </parametros>
                     </parametrosObjeto>
                  </ser:ejecucionObjeto>
               </soapenv:Body>
            </soapenv:Envelope>";
            $headers=array('Content-Length: '.strlen($post_string),'Content-Type: text/xml;charset=UTF-8','SOAPAction: "http://servicios.ug.edu.ec//ejecucionObjeto"','Host:'.$host,'Proxy-Connection: Keep-Alive','User-Agent: Apache-HttpClient/4.1.1 (java 1.5)' );
            $soap_do = curl_init();
            curl_setopt ($soap_do, CURLOPT_VERBOSE , true );
            curl_setopt($soap_do, CURLOPT_URL,            $url );
            curl_setopt($soap_do, CURLOPT_CONNECTTIMEOUT, 10);
            curl_setopt($soap_do, CURLOPT_TIMEOUT,        5*60);
            curl_setopt($soap_do, CURLOPT_RETURNTRANSFER, true );
            curl_setopt($soap_do, CURLOPT_PORT,8080);
            curl_setopt($soap_do, CURLOPT_POST, true);
            curl_setopt($soap_do, CURLOPT_POSTFIELDS,$post_string);
            curl_setopt($soap_do, CURLOPT_HTTPHEADER,$headers);
            $result = curl_exec($soap_do);


            if(!$result)
            {
                return "error";
            }
            else
            {

                $response = preg_replace("/(<\/?)(\w+):([^>]*>)/", "$1$2$3", $result);
                $xml = new \SimpleXMLElement($response);
                $body = $xml->xpath('//soapBody')[0];
                $return = $xml->xpath('//return')[0];
                $respuestaConsulta = $xml->xpath('//resultadoObjeto')[0];
                return $respuestaConsulta;
            }

}#end function

//ANULACION ADMIN
function doRequestSreReceptaTransacionCarrerasAnulacion($datosCuenta,$source,$tipo,$usuario,$clave,$url,$host)
{

$post_string=" <soapenv:Envelope xmlns:soapenv='http://schemas.xmlsoap.org/soap/envelope/' xmlns:ser='http://servicios.ug.edu.ec/'>
   <soapenv:Header/>
   <soapenv:Body>
      <ser:ejecucionConsulta>
         <dataSource>".$source."</dataSource>
         <idServicio>".$tipo."</idServicio>
         <usuario>".$usuario."</usuario>
         <clave>".$clave."</clave>
         <parametrosConsulta>
            <parametros>
                ".$datosCuenta."
            </parametros>
         </parametrosConsulta>
      </ser:ejecucionConsulta>
   </soapenv:Body>
</soapenv:Envelope> ";

        $headers=array('Content-Length: '.strlen($post_string),'Content-Type: text/xml;charset=UTF-8','SOAPAction: "http://servicios.ug.edu.ec//ejecucionConsulta"','Host:'.$host,'Proxy-Connection: Keep-Alive','User-Agent: Apache-HttpClient/4.1.1 (java 1.5)' );
        $soap_do = curl_init();
        curl_setopt ($soap_do, CURLOPT_VERBOSE , true );
        curl_setopt($soap_do, CURLOPT_URL,            $url );
        curl_setopt($soap_do, CURLOPT_CONNECTTIMEOUT, 10);
        curl_setopt($soap_do, CURLOPT_TIMEOUT,        5*60);
        curl_setopt($soap_do, CURLOPT_RETURNTRANSFER, true );
        curl_setopt($soap_do, CURLOPT_PORT,8080);
        curl_setopt($soap_do, CURLOPT_POST, true);
        curl_setopt($soap_do, CURLOPT_POSTFIELDS,$post_string);
        curl_setopt($soap_do, CURLOPT_HTTPHEADER,$headers);
        $result = curl_exec($soap_do);

    if(!$result)
    {
        return "error";
    }
    else
    {

        $response = preg_replace("/(<\/?)(\w+):([^>]*>)/", "$1$2$3", $result);
        $xml = new \SimpleXMLElement($response);
        $body = $xml->xpath('//soapBody')[0];
        $return = $xml->xpath('//return')[0];
        $respuestaConsulta = $xml->xpath('//respuestaConsulta')[0];
        return $respuestaConsulta;
    }
}#end function


function doRequestSreReceptaTransacionCarrerasOrden($datosCuenta,$source,$tipo,$usuario,$clave,$url,$host)
{

$post_string=" <soapenv:Envelope xmlns:soapenv='http://schemas.xmlsoap.org/soap/envelope/' xmlns:ser='http://servicios.ug.edu.ec/'>
   <soapenv:Header/>
   <soapenv:Body>
      <ser:ejecucionConsulta>
         <dataSource>".$source."</dataSource>
         <idServicio>".$tipo."</idServicio>
         <usuario>".$usuario."</usuario>
         <clave>".$clave."</clave>
         <parametrosConsulta>
            <parametros>
                ".$datosCuenta."
            </parametros>
         </parametrosConsulta>
      </ser:ejecucionConsulta>
   </soapenv:Body>
</soapenv:Envelope> ";

        $headers=array('Content-Length: '.strlen($post_string),'Content-Type: text/xml;charset=UTF-8','SOAPAction: "http://servicios.ug.edu.ec//ejecucionConsulta"','Host:'.$host,'Proxy-Connection: Keep-Alive','User-Agent: Apache-HttpClient/4.1.1 (java 1.5)' );
        $soap_do = curl_init();
        curl_setopt ($soap_do, CURLOPT_VERBOSE , true );
        curl_setopt($soap_do, CURLOPT_URL,            $url );
        curl_setopt($soap_do, CURLOPT_CONNECTTIMEOUT, 10);
        curl_setopt($soap_do, CURLOPT_TIMEOUT,        5*60);
        curl_setopt($soap_do, CURLOPT_RETURNTRANSFER, true );
        curl_setopt($soap_do, CURLOPT_PORT,8080);
        curl_setopt($soap_do, CURLOPT_POST, true);
        curl_setopt($soap_do, CURLOPT_POSTFIELDS,$post_string);
        curl_setopt($soap_do, CURLOPT_HTTPHEADER,$headers);
        $result = curl_exec($soap_do);


    if(!$result)
    {
        return "error";
    }
    else
    {

        $response = preg_replace("/(<\/?)(\w+):([^>]*>)/", "$1$2$3", $result);
        $xml = new \SimpleXMLElement($response);
        $body = $xml->xpath('//soapBody')[0];
        $return = $xml->xpath('//return')[0];
        $respuestaConsulta = $xml->xpath('//respuestaConsulta')[0];
        return $respuestaConsulta;
    }
}#end function


function doSetListado_Anulacion_Detalle($datosCuenta,$source,$tipo,$usuario,$clave,$url,$host)
{

$post_string="
            <soapenv:Envelope xmlns:soapenv='http://schemas.xmlsoap.org/soap/envelope/' xmlns:ser='http://servicios.ug.edu.ec/'>
               <soapenv:Header/>
               <soapenv:Body>
                  <ser:ejecucionObjeto>
                      <dataSource>".$source."</dataSource>
                     <idServicio>".$tipo."</idServicio>
                     <usuario>".$usuario."</usuario>
                     <clave>".$clave."</clave>
                     <parametrosObjeto>
                        <parametros>
                           ".$datosCuenta." 
                      </parametros>
                     </parametrosObjeto>
                  </ser:ejecucionObjeto>
               </soapenv:Body>
            </soapenv:Envelope>";
                    $headers=array('Content-Length: '.strlen($post_string),'Content-Type: text/xml;charset=UTF-8','SOAPAction: "http://servicios.ug.edu.ec//ejecucionObjeto"','Host:'.$host,'Proxy-Connection: Keep-Alive','User-Agent: Apache-HttpClient/4.1.1 (java 1.5)' );
                    $soap_do = curl_init();
                    curl_setopt ($soap_do, CURLOPT_VERBOSE , true );
                    curl_setopt($soap_do, CURLOPT_URL,            $url );
                    curl_setopt($soap_do, CURLOPT_CONNECTTIMEOUT, 10);
                    curl_setopt($soap_do, CURLOPT_TIMEOUT,        5*60);
                    curl_setopt($soap_do, CURLOPT_RETURNTRANSFER, true );
                    curl_setopt($soap_do, CURLOPT_PORT,8080);
                    curl_setopt($soap_do, CURLOPT_POST, true);
                    curl_setopt($soap_do, CURLOPT_POSTFIELDS,$post_string);
                    curl_setopt($soap_do, CURLOPT_HTTPHEADER,$headers);
                    $result = curl_exec($soap_do);

    if(!$result)
    {
        return "error";
    }
    else
    {
        $response  = $this->ReemplazaCaracteres($result);
        $response = preg_replace("/(<\/?)(\w+):([^>]*>)/", "$1$2$3", $response);
        $xml = new \SimpleXMLElement($response);
        $body = $xml->xpath('//soapBody')[0];
        $return = $xml->xpath('//return')[0];
        $respuestaConsulta = $xml->xpath('//resultadoObjeto')[0];
        $respuestaConsulta = $xml->xpath('//parametrosSalida')[0];
        return $respuestaConsulta;
    }
}#end function

/**
 * [Funcion para insertar un evento al calendario]
 */
function doInsertEventosCalendario($datosCuenta,$source,$tipo,$usuario,$clave,$url,$host){
  $post_string="
            <soapenv:Envelope xmlns:soapenv='http://schemas.xmlsoap.org/soap/envelope/' xmlns:ser='http://servicios.ug.edu.ec/'>
               <soapenv:Header/>
               <soapenv:Body>
                  <ser:ejecucionObjeto>
                      <dataSource>".$source."</dataSource>
                     <idServicio>".$tipo."</idServicio>
                     <usuario>".$usuario."</usuario>
                     <clave>".$clave."</clave>
                     <parametrosObjeto>
                        <parametros>
                           ".$datosCuenta."
                      </parametros>
                     </parametrosObjeto>
                  </ser:ejecucionObjeto>
               </soapenv:Body>
            </soapenv:Envelope>";
                    $headers=array('Content-Length: '.strlen($post_string),'Content-Type: text/xml;charset=UTF-8','SOAPAction: "http://servicios.ug.edu.ec//ejecucionObjeto"','Host:'.$host,'Proxy-Connection: Keep-Alive','User-Agent: Apache-HttpClient/4.1.1 (java 1.5)' );
                    $soap_do = curl_init();
                    curl_setopt ($soap_do, CURLOPT_VERBOSE , true );
                    curl_setopt($soap_do, CURLOPT_URL,            $url );
                    curl_setopt($soap_do, CURLOPT_CONNECTTIMEOUT, 10);
                    curl_setopt($soap_do, CURLOPT_TIMEOUT,        5*60);
                    curl_setopt($soap_do, CURLOPT_RETURNTRANSFER, true );
                    curl_setopt($soap_do, CURLOPT_PORT,8080);
                    curl_setopt($soap_do, CURLOPT_POST, true);
                    curl_setopt($soap_do, CURLOPT_POSTFIELDS,$post_string);
                    curl_setopt($soap_do, CURLOPT_HTTPHEADER,$headers);
                    $result = curl_exec($soap_do);

                    $response = preg_replace("/(<\/?)(\w+):([^>]*>)/", "$1$2$3", $result);
                    $xml = new \SimpleXMLElement($response);
                    $body = $xml->xpath('//soapBody')[0];
                    $return = $xml->xpath('//return')[0];

                    $resultadoObjeto = $xml->xpath('//parametrosSalida')[0];

                    $resultadoObjeto = json_encode($resultadoObjeto);
                    $xml_array = json_decode($resultadoObjeto,TRUE);
                    // $resultadoObjeto = $this->Response($resultadoObjeto);
                    // $resultadoObjeto = $xml->xpath('//PV_MENSAJE')[0];
                    // $resultadoObjeto = $this->Response($resultadoObjeto);
                    // echo '<pre>'; var_dump($xml_array); exit();
                    // return $xml_array;
                    return $xml_array["PI_ESTADO"];
}#end function   


function doRequestSreReceptaTransacionAnulacion_Listar($datosCuenta,$source,$tipo,$usuario,$clave,$url,$host)
{

$post_string=" <soapenv:Envelope xmlns:soapenv='http://schemas.xmlsoap.org/soap/envelope/' xmlns:ser='http://servicios.ug.edu.ec/'>
   <soapenv:Header/>
   <soapenv:Body>
      <ser:ejecucionConsulta>
         <dataSource>".$source."</dataSource>
         <idServicio>".$tipo."</idServicio>
         <usuario>".$usuario."</usuario>
         <clave>".$clave."</clave>
         <parametrosConsulta>
            <parametros>
                ".$datosCuenta."
            </parametros>
         </parametrosConsulta>
      </ser:ejecucionConsulta>
   </soapenv:Body>
</soapenv:Envelope> ";

        $headers=array('Content-Length: '.strlen($post_string),'Content-Type: text/xml;charset=UTF-8','SOAPAction: "http://servicios.ug.edu.ec//ejecucionConsulta"','Host:'.$host,'Proxy-Connection: Keep-Alive','User-Agent: Apache-HttpClient/4.1.1 (java 1.5)' );
        $soap_do = curl_init();
        curl_setopt ($soap_do, CURLOPT_VERBOSE , true );
        curl_setopt($soap_do, CURLOPT_URL,            $url );
        curl_setopt($soap_do, CURLOPT_CONNECTTIMEOUT, 10);
        curl_setopt($soap_do, CURLOPT_TIMEOUT,        5*60);
        curl_setopt($soap_do, CURLOPT_RETURNTRANSFER, true );
        curl_setopt($soap_do, CURLOPT_PORT,8080);
        curl_setopt($soap_do, CURLOPT_POST, true);
        curl_setopt($soap_do, CURLOPT_POSTFIELDS,$post_string);
        curl_setopt($soap_do, CURLOPT_HTTPHEADER,$headers);
        $result = curl_exec($soap_do);

    if(!$result)
    {
        return "error";
    }
    else
    {

        $response = preg_replace("/(<\/?)(\w+):([^>]*>)/", "$1$2$3", $result);
        $xml = new \SimpleXMLElement($response);
        $body = $xml->xpath('//soapBody')[0];
        $return = $xml->xpath('//return')[0];
        $respuestaConsulta = $xml->xpath('//respuestaConsulta')[0];
        return $respuestaConsulta;
    }
}#end function

function doRequestsListarOrdenPago($datosCuenta,$source,$tipo,$usuario,$clave,$url,$host)
{

$post_string="
            <soapenv:Envelope xmlns:soapenv='http://schemas.xmlsoap.org/soap/envelope/' xmlns:ser='http://servicios.ug.edu.ec/'>
               <soapenv:Header/>
               <soapenv:Body>
                  <ser:ejecucionObjeto>
                      <dataSource>".$source."</dataSource>
                     <idServicio>".$tipo."</idServicio>
                     <usuario>".$usuario."</usuario>
                     <clave>".$clave."</clave>
                     <parametrosObjeto>
                        <parametros>
                           ".$datosCuenta." 
                      </parametros>
                     </parametrosObjeto>
                  </ser:ejecucionObjeto>
               </soapenv:Body>
            </soapenv:Envelope>";
                    $headers=array('Content-Length: '.strlen($post_string),'Content-Type: text/xml;charset=UTF-8','SOAPAction: "http://servicios.ug.edu.ec//ejecucionObjeto"','Host:'.$host,'Proxy-Connection: Keep-Alive','User-Agent: Apache-HttpClient/4.1.1 (java 1.5)' );
                    $soap_do = curl_init();
                    curl_setopt ($soap_do, CURLOPT_VERBOSE , true );
                    curl_setopt($soap_do, CURLOPT_URL,            $url );
                    curl_setopt($soap_do, CURLOPT_CONNECTTIMEOUT, 10);
                    curl_setopt($soap_do, CURLOPT_TIMEOUT,        5*60);
                    curl_setopt($soap_do, CURLOPT_RETURNTRANSFER, true );
                    curl_setopt($soap_do, CURLOPT_PORT,8080);
                    curl_setopt($soap_do, CURLOPT_POST, true);
                    curl_setopt($soap_do, CURLOPT_POSTFIELDS,$post_string);
                    curl_setopt($soap_do, CURLOPT_HTTPHEADER,$headers);
                    $result = curl_exec($soap_do);

    if(!$result)
    {
        return "error";
    }
    else
    {
        $response  = $this->ReemplazaCaracteres($result);
        $response = preg_replace("/(<\/?)(\w+):([^>]*>)/", "$1$2$3", $response);
        $xml = new \SimpleXMLElement($response);
        $body = $xml->xpath('//soapBody')[0];
        $return = $xml->xpath('//return')[0];
        $respuestaConsulta = $xml->xpath('//resultadoObjeto')[0];
        $respuestaConsulta = $xml->xpath('//parametrosSalida')[0];
        return $respuestaConsulta;
    }




}#end function
/**
 * [Funcion que permite consultar los eventos que han sido insertados en el calendario]
 */
function doSelectEventosCalendario($datosCuenta,$source,$tipo,$usuario,$clave,$url,$host){
  $post_string="
            <soapenv:Envelope xmlns:soapenv='http://schemas.xmlsoap.org/soap/envelope/' xmlns:ser='http://servicios.ug.edu.ec/'>
               <soapenv:Header/>
               <soapenv:Body>
                  <ser:ejecucionObjeto>
                      <dataSource>".$source."</dataSource>
                     <idServicio>".$tipo."</idServicio>
                     <usuario>".$usuario."</usuario>
                     <clave>".$clave."</clave>
                     <parametrosObjeto>
                        <parametros>
                           ".$datosCuenta."
                      </parametros>
                     </parametrosObjeto>
                  </ser:ejecucionObjeto>
               </soapenv:Body>
            </soapenv:Envelope>";
                    $headers=array('Content-Length: '.strlen($post_string),'Content-Type: text/xml;charset=UTF-8','SOAPAction: "http://servicios.ug.edu.ec//ejecucionObjeto"','Host:'.$host,'Proxy-Connection: Keep-Alive','User-Agent: Apache-HttpClient/4.1.1 (java 1.5)' );
                    $soap_do = curl_init();
                    curl_setopt ($soap_do, CURLOPT_VERBOSE , true );
                    curl_setopt($soap_do, CURLOPT_URL,            $url );
                    curl_setopt($soap_do, CURLOPT_CONNECTTIMEOUT, 10);
                    curl_setopt($soap_do, CURLOPT_TIMEOUT,        5*60);
                    curl_setopt($soap_do, CURLOPT_RETURNTRANSFER, true );
                    curl_setopt($soap_do, CURLOPT_PORT,8080);
                    curl_setopt($soap_do, CURLOPT_POST, true);
                    curl_setopt($soap_do, CURLOPT_POSTFIELDS,$post_string);
                    curl_setopt($soap_do, CURLOPT_HTTPHEADER,$headers);
                    $result = curl_exec($soap_do);
                    $response = preg_replace("/(<\/?)(\w+):([^>]*>)/", "$1$2$3", $result);
                    $xml = new \SimpleXMLElement($response);
                    $body = $xml->xpath('//soapBody')[0];
                    $return = $xml->xpath('//return')[0];

                    $resultadoObjeto = $xml->xpath('//parametrosSalida')[0];
                    $resultadoObjeto = $xml->xpath('//PX_Salida')[0];
                    // $resultadoObjeto = $xml->xpath('//item')[0];
                    $resultadoObjeto = $this->Response($resultadoObjeto);
                    return $resultadoObjeto;
}#end function
function doSetActualizaOrden($datosCuenta,$source,$tipo,$usuario,$clave,$url,$host){

 $post_string="
            <soapenv:Envelope xmlns:soapenv='http://schemas.xmlsoap.org/soap/envelope/' xmlns:ser='http://servicios.ug.edu.ec/'>
               <soapenv:Header/>
               <soapenv:Body>
                  <ser:ejecucionObjeto>
                      <dataSource>".$source."</dataSource>
                     <idServicio>".$tipo."</idServicio>
                     <usuario>".$usuario."</usuario>
                     <clave>".$clave."</clave>
                     <parametrosObjeto>
                        <parametros>
                            ".$datosCuenta."
                      </parametros>
                     </parametrosObjeto>
                  </ser:ejecucionObjeto>
               </soapenv:Body>
            </soapenv:Envelope>";
            $headers=array('Content-Length: '.strlen($post_string),'Content-Type: text/xml;charset=UTF-8','SOAPAction: "http://servicios.ug.edu.ec//ejecucionObjeto"','Host:'.$host,'Proxy-Connection: Keep-Alive','User-Agent: Apache-HttpClient/4.1.1 (java 1.5)' );
            $soap_do = curl_init();
            curl_setopt ($soap_do, CURLOPT_VERBOSE , true );
            curl_setopt($soap_do, CURLOPT_URL,            $url );
            curl_setopt($soap_do, CURLOPT_CONNECTTIMEOUT, 10);
            curl_setopt($soap_do, CURLOPT_TIMEOUT,        5*60);
            curl_setopt($soap_do, CURLOPT_RETURNTRANSFER, true );
            curl_setopt($soap_do, CURLOPT_PORT,8080);
            curl_setopt($soap_do, CURLOPT_POST, true);
            curl_setopt($soap_do, CURLOPT_POSTFIELDS,$post_string);
            curl_setopt($soap_do, CURLOPT_HTTPHEADER,$headers);
            $result = curl_exec($soap_do);


            if(!$result)
            {
                return "error";
            }
            else
            {

                $response = preg_replace("/(<\/?)(\w+):([^>]*>)/", "$1$2$3", $result);
                $xml = new \SimpleXMLElement($response);
                $body = $xml->xpath('//soapBody')[0];
                $return = $xml->xpath('//return')[0];
                $respuestaConsulta = $xml->xpath('//resultadoObjeto')[0];
                return $respuestaConsulta;
            }

}#end function
function doSetSolicitudAnula($datosCuenta,$source,$tipo,$usuario,$clave,$url,$host){

 $post_string="
            <soapenv:Envelope xmlns:soapenv='http://schemas.xmlsoap.org/soap/envelope/' xmlns:ser='http://servicios.ug.edu.ec/'>
               <soapenv:Header/>
               <soapenv:Body>
                  <ser:ejecucionObjeto>
                      <dataSource>".$source."</dataSource>
                     <idServicio>".$tipo."</idServicio>
                     <usuario>".$usuario."</usuario>
                     <clave>".$clave."</clave>
                     <parametrosObjeto>
                        <parametros>
                            <px_xml>
                                <items>
                                    ".$datosCuenta."
                                </items>
                            </px_xml>
                            <pc_opcion>I</pc_opcion>
                      </parametros>
                     </parametrosObjeto>
                  </ser:ejecucionObjeto>
               </soapenv:Body>
            </soapenv:Envelope>";
            $headers=array('Content-Length: '.strlen($post_string),'Content-Type: text/xml;charset=UTF-8','SOAPAction: "http://servicios.ug.edu.ec//ejecucionObjeto"','Host:'.$host,'Proxy-Connection: Keep-Alive','User-Agent: Apache-HttpClient/4.1.1 (java 1.5)' );
            $soap_do = curl_init();
            curl_setopt ($soap_do, CURLOPT_VERBOSE , true );
            curl_setopt($soap_do, CURLOPT_URL,            $url );
            curl_setopt($soap_do, CURLOPT_CONNECTTIMEOUT, 10);
            curl_setopt($soap_do, CURLOPT_TIMEOUT,        5*60);
            curl_setopt($soap_do, CURLOPT_RETURNTRANSFER, true );
            curl_setopt($soap_do, CURLOPT_PORT,8080);
            curl_setopt($soap_do, CURLOPT_POST, true);
            curl_setopt($soap_do, CURLOPT_POSTFIELDS,$post_string);
            curl_setopt($soap_do, CURLOPT_HTTPHEADER,$headers);

            $result = curl_exec($soap_do);
                    if(!$result)
                    {
                        return "error";
                    }
                    else
                    {
                        $response = preg_replace("/(<\/?)(\w+):([^>]*>)/", "$1$2$3", $result);
                        $xml = new \SimpleXMLElement($response);
                        $body = $xml->xpath('//soapBody')[0];
                        $return = $xml->xpath('//return')[0];
                        $respuestaConsulta = $xml->xpath('//resultadoObjeto')[0];
                        return $respuestaConsulta;
                    }

}#end function

/**
 * [Funcion que permite actualizar un evento del calendario]
 */
function doUpdateEventosCalendario($datosCuenta,$source,$tipo,$usuario,$clave,$url,$host){
  $post_string="
            <soapenv:Envelope xmlns:soapenv='http://schemas.xmlsoap.org/soap/envelope/' xmlns:ser='http://servicios.ug.edu.ec/'>
               <soapenv:Header/>
               <soapenv:Body>
                  <ser:ejecucionObjeto>
                      <dataSource>".$source."</dataSource>
                     <idServicio>".$tipo."</idServicio>
                     <usuario>".$usuario."</usuario>
                     <clave>".$clave."</clave>
                     <parametrosObjeto>
                        <parametros>
                           ".$datosCuenta."
                      </parametros>
                     </parametrosObjeto>
                  </ser:ejecucionObjeto>
               </soapenv:Body>
            </soapenv:Envelope>";
                    $headers=array('Content-Length: '.strlen($post_string),'Content-Type: text/xml;charset=UTF-8','SOAPAction: "http://servicios.ug.edu.ec//ejecucionObjeto"','Host:'.$host,'Proxy-Connection: Keep-Alive','User-Agent: Apache-HttpClient/4.1.1 (java 1.5)' );
                    $soap_do = curl_init();
                    curl_setopt ($soap_do, CURLOPT_VERBOSE , true );
                    curl_setopt($soap_do, CURLOPT_URL,            $url );
                    curl_setopt($soap_do, CURLOPT_CONNECTTIMEOUT, 10);
                    curl_setopt($soap_do, CURLOPT_TIMEOUT,        5*60);
                    curl_setopt($soap_do, CURLOPT_RETURNTRANSFER, true );
                    curl_setopt($soap_do, CURLOPT_PORT,8080);
                    curl_setopt($soap_do, CURLOPT_POST, true);
                    curl_setopt($soap_do, CURLOPT_POSTFIELDS,$post_string);
                    curl_setopt($soap_do, CURLOPT_HTTPHEADER,$headers);
                    $result = curl_exec($soap_do);

                    $response = preg_replace("/(<\/?)(\w+):([^>]*>)/", "$1$2$3", $result);
                    $xml = new \SimpleXMLElement($response);
                    $body = $xml->xpath('//soapBody')[0];
                    $return = $xml->xpath('//return')[0];

                    $resultadoObjeto = $xml->xpath('//parametrosSalida')[0];

                    $resultadoObjeto = json_encode($resultadoObjeto);
                    $xml_array = json_decode($resultadoObjeto,TRUE);
                    // $resultadoObjeto = $this->Response($resultadoObjeto);
                    // $resultadoObjeto = $xml->xpath('//PV_MENSAJE')[0];
                    // $resultadoObjeto = $this->Response($resultadoObjeto);
                    // echo '<pre>'; var_dump($xml_array); exit();
                    // return $xml_array;
                    return $xml_array["PI_ESTADO"];
}#end function
function doRequestsEstudianteHorariosExamen($datosCuenta,$source,$tipo,$usuario,$clave,$url,$host)
{

$post_string="
            <soapenv:Envelope xmlns:soapenv='http://schemas.xmlsoap.org/soap/envelope/' xmlns:ser='http://servicios.ug.edu.ec/'>
               <soapenv:Header/>
               <soapenv:Body>
                  <ser:ejecucionObjeto>
                      <dataSource>".$source."</dataSource>
                     <idServicio>".$tipo."</idServicio>
                     <usuario>".$usuario."</usuario>
                     <clave>".$clave."</clave>
                     <parametrosObjeto>
                        <parametros>
                                ".$datosCuenta." 
                      </parametros>
                     </parametrosObjeto>
                  </ser:ejecucionObjeto>
               </soapenv:Body>
            </soapenv:Envelope>";
                    $headers=array('Content-Length: '.strlen($post_string),'Content-Type: text/xml;charset=UTF-8','SOAPAction: "http://servicios.ug.edu.ec//ejecucionObjeto"','Host:'.$host,'Proxy-Connection: Keep-Alive','User-Agent: Apache-HttpClient/4.1.1 (java 1.5)' );
                    $soap_do = curl_init();
                    curl_setopt ($soap_do, CURLOPT_VERBOSE , true );
                    curl_setopt($soap_do, CURLOPT_URL,            $url );
                    curl_setopt($soap_do, CURLOPT_CONNECTTIMEOUT, 10);
                    curl_setopt($soap_do, CURLOPT_TIMEOUT,        5*60);
                    curl_setopt($soap_do, CURLOPT_RETURNTRANSFER, true );
                    curl_setopt($soap_do, CURLOPT_PORT,8080);
                    curl_setopt($soap_do, CURLOPT_POST, true);
                    curl_setopt($soap_do, CURLOPT_POSTFIELDS,$post_string);
                    curl_setopt($soap_do, CURLOPT_HTTPHEADER,$headers);
                    //$result = curl_exec($soap_do);


$result =  <<<XML
 <soap:Envelope xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
    <soap:Body>
        <ns2:ejecucionObjetoResponse xmlns:ns2="http://servicios.ug.edu.ec/">
            <return>
                <codigoRespuesta>0</codigoRespuesta>
                <estado>F</estado>
                <idHistorico>58</idHistorico>
                <mensajeRespuesta>ok</mensajeRespuesta>
                <resultadoObjeto>
                    <parametrosSalida>
                        <PX_SALIDA>
                            <horarios>
                                <dias>
                                    <dia> <nombre>Lunes</nombre> <id_dia>1</id_dia> </dia>
                                    <dia> <nombre>Martes</nombre> <id_dia>2</id_dia> </dia>
                                    <dia> <nombre>Miercoles</nombre> <id_dia>3</id_dia> </dia>
                                </dias>
                                <horas>
                                    <hora> <descripcion_hora>8:00 - 9:00</descripcion_hora> <id_hora>1</id_hora> </hora>
                                    <hora> <descripcion_hora>9:00 - 10:00</descripcion_hora> <id_hora>2</id_hora> </hora>
                                    <hora> <descripcion_hora>10:00 - 11:00</descripcion_hora> <id_hora>3</id_hora> </hora>
                                </horas>
                                <materias>
                                    <materia> <id_materia>1</id_materia> <descripcion_materia>Lenguaje</descripcion_materia> <id_hora>1</id_hora> <id_dia>1</id_dia></materia>
                                    <materia> <id_materia>2</id_materia> <descripcion_materia>Matematicas</descripcion_materia> <id_hora>2</id_hora> <id_dia>1</id_dia></materia>
                                    <materia> <id_materia>3</id_materia> <descripcion_materia>Programacion</descripcion_materia> <id_hora>3</id_hora> <id_dia>2</id_dia> </materia>
                                </materias>
                                <profesores>
                                    <profesor> <nombre_profesor>8:00 - 9:00</nombre_profesor> <id_materia>1</id_materia> </profesor>
                                    <profesor> <nombre_profesor>8:00 - 9:00</nombre_profesor> <id_materia>2</id_materia> </profesor>
                                    <profesor> <nombre_profesor>8:00 - 9:00</nombre_profesor> <id_materia>3</id_materia> </profesor>
                                </profesores>
                            </horarios>
                 </PX_SALIDA>
                     </parametrosSalida>
                 </resultadoObjeto>
             </return>
         </ns2:ejecucionObjetoResponse>
     </soap:Body>
 </soap:Envelope>
XML;


    if(!$result)
    {
        return "error";
    }
    else
    {
        $response  = $this->ReemplazaCaracteres($result);
        $response = preg_replace("/(<\/?)(\w+):([^>]*>)/", "$1$2$3", $response);
        $xml = new \SimpleXMLElement($response);
        $body = $xml->xpath('//soapBody')[0];
        $return = $xml->xpath('//return')[0];
        $respuestaConsulta = $xml->xpath('//resultadoObjeto')[0];
        $respuestaConsulta = $xml->xpath('//parametrosSalida')[0];
        return $respuestaConsulta;
    }




}#end function

//----------------------------------------------------------------------------------------------------------------------------//
//----------------------------------------------------------------------------------------------------------------------------//
//----------------------------------------------------------------------------------------------------------------------------//

function doRequestSreReceptaEstudiantes_InscritosMatriculados($datosCuenta,$source,$tipo,$usuario,$clave,$url,$host, $XML=NULL)
{  

    $post_string="
        <soapenv:Envelope xmlns:soapenv=\"http://schemas.xmlsoap.org/soap/envelope/\" xmlns:ser=\"http://servicios.ug.edu.ec/\"> 
        <soapenv:Header/>
        <soapenv:Body>
            <ser:ejecucionConsulta>
                <dataSource>".$source."</dataSource>
                <idServicio>".$tipo."</idServicio>
                <usuario>".$usuario."</usuario>
                <clave>".$clave."</clave>
                <parametrosConsulta>
                    <parametros>
                        ".$datosCuenta."
                    </parametros>
                </parametrosConsulta>
            </ser:ejecucionConsulta>
        </soapenv:Body>
        </soapenv:Envelope>";

    $headers=array('Content-Length: '.strlen($post_string),'Content-Type: text/xml;charset=UTF-8','SOAPAction: "http://servicios.ug.edu.ec//ejecucionConsulta"','Host:'.$host,'Proxy-Connection: Keep-Alive','User-Agent: Apache-HttpClient/4.1.1 (java 1.5)' );
    $soap_do = curl_init(); 
    curl_setopt ($soap_do, CURLOPT_VERBOSE , true );
    curl_setopt($soap_do, CURLOPT_URL,            $url );   
    curl_setopt($soap_do, CURLOPT_CONNECTTIMEOUT, 10); 
    curl_setopt($soap_do, CURLOPT_TIMEOUT,        5*60); 
    curl_setopt($soap_do, CURLOPT_RETURNTRANSFER, true );
    curl_setopt($soap_do, CURLOPT_PORT,8080);
    curl_setopt($soap_do, CURLOPT_POST, true);
    curl_setopt($soap_do, CURLOPT_POSTFIELDS,$post_string); 
    curl_setopt($soap_do, CURLOPT_HTTPHEADER,$headers);
     $result = curl_exec($soap_do);
     // echo "<pre>";
     //    var_dump($result);
     //    echo "</pre>";
     //    exit();
    if(!$result){
        return "error";
    }else{
       
        $response  = preg_replace("/(<\/?)(\w+):([^>]*>)/", "$1$2$3", $result);
        $respuesta  = $this->eliminaCabecerasAcademicoConsultas($response);
        $respuesta  = $this->Response("<registros>".$respuesta."</registros>");
                
        return $respuesta;        
    }

}#end function   

function doRequestEstadosMatricula($datosCuenta,$source,$tipo,$usuario,$clave,$url,$host, $XML=NULL){  
    $post_string="
        <soapenv:Envelope xmlns:soapenv=\"http://schemas.xmlsoap.org/soap/envelope/\" xmlns:ser=\"http://servicios.ug.edu.ec/\"> 
        <soapenv:Header/>
        <soapenv:Body>
            <ser:ejecucionConsulta>
                <dataSource>".$source."</dataSource>
                <idServicio>".$tipo."</idServicio>
                <usuario>".$usuario."</usuario>
                <clave>".$clave."</clave>
                <parametrosConsulta>
                    <parametros>
                        ".$datosCuenta."
                    </parametros>
                </parametrosConsulta>
            </ser:ejecucionConsulta>
        </soapenv:Body>
        </soapenv:Envelope>";

    $headers=array('Content-Length: '.strlen($post_string),'Content-Type: text/xml;charset=UTF-8','SOAPAction: "http://servicios.ug.edu.ec//ejecucionConsulta"','Host:'.$host,'Proxy-Connection: Keep-Alive','User-Agent: Apache-HttpClient/4.1.1 (java 1.5)' );
    $soap_do = curl_init(); 
    curl_setopt ($soap_do, CURLOPT_VERBOSE , true );
    curl_setopt($soap_do, CURLOPT_URL,            $url );   
    curl_setopt($soap_do, CURLOPT_CONNECTTIMEOUT, 10); 
    curl_setopt($soap_do, CURLOPT_TIMEOUT,        5*60); 
    curl_setopt($soap_do, CURLOPT_RETURNTRANSFER, true );
    curl_setopt($soap_do, CURLOPT_PORT,8080);
    curl_setopt($soap_do, CURLOPT_POST, true);
    curl_setopt($soap_do, CURLOPT_POSTFIELDS,$post_string); 
    curl_setopt($soap_do, CURLOPT_HTTPHEADER,$headers);
    //echo '<pre>'; var_dump($post_string); exit();
   if($XML==NULL){
     $result = curl_exec($soap_do);
   }
   else {
       $result = $XML;
   }  

    if(!$result){
        return "error";
    }else{
       
        $response  = preg_replace("/(<\/?)(\w+):([^>]*>)/", "$1$2$3", $result);
        $respuesta  = $this->eliminaCabecerasAcademicoConsultas($response);
        $respuesta  = $this->Response("<registros>".$respuesta."</registros>");
        return $respuesta;
    }

}#end function   

function doRequestConsultaPorcentajeEstudianteCarrera($datosCuenta,$source,$tipo,$usuario,$clave,$url,$host){  
    $post_string="
        <soapenv:Envelope xmlns:soapenv=\"http://schemas.xmlsoap.org/soap/envelope/\" xmlns:ser=\"http://servicios.ug.edu.ec/\"> 
        <soapenv:Header/>
        <soapenv:Body>
            <ser:ejecucionConsulta>
                <dataSource>".$source."</dataSource>
                <idServicio>".$tipo."</idServicio>
                <usuario>".$usuario."</usuario>
                <clave>".$clave."</clave>
                <parametrosConsulta>
                    <parametros>
                        ".$datosCuenta."
                    </parametros>
                </parametrosConsulta>
            </ser:ejecucionConsulta>
        </soapenv:Body>
        </soapenv:Envelope>";

    $headers=array('Content-Length: '.strlen($post_string),'Content-Type: text/xml;charset=UTF-8','SOAPAction: "http://servicios.ug.edu.ec//ejecucionConsulta"','Host:'.$host,'Proxy-Connection: Keep-Alive','User-Agent: Apache-HttpClient/4.1.1 (java 1.5)' );
    $soap_do = curl_init(); 
    curl_setopt ($soap_do, CURLOPT_VERBOSE , true );
    curl_setopt($soap_do, CURLOPT_URL,            $url );   
    curl_setopt($soap_do, CURLOPT_CONNECTTIMEOUT, 10); 
    curl_setopt($soap_do, CURLOPT_TIMEOUT,        5*60); 
    curl_setopt($soap_do, CURLOPT_RETURNTRANSFER, true );
    curl_setopt($soap_do, CURLOPT_PORT,8080);
    curl_setopt($soap_do, CURLOPT_POST, true);
    curl_setopt($soap_do, CURLOPT_POSTFIELDS,$post_string); 
    curl_setopt($soap_do, CURLOPT_HTTPHEADER,$headers);
    //echo '<pre>'; var_dump($post_string); exit();
     $result = curl_exec($soap_do);

    if(!$result){
        return "error";
    }else{
       
        $response  = preg_replace("/(<\/?)(\w+):([^>]*>)/", "$1$2$3", $result);
        $respuesta  = $this->eliminaCabecerasAcademicoConsultas($response);
        $respuesta  = $this->Response("<registros>".$respuesta."</registros>");
       
        return $respuesta;   
    }

}#end function   



function doSelectRolAdmin($datosCuenta,$source,$tipo,$usuario,$clave,$url,$host)
{
// echo '<pre>'; var_dump($datosCuenta,$source,$tipo,$usuario,$clave,$url,$host); exit();
$post_string="
            <soapenv:Envelope xmlns:soapenv='http://schemas.xmlsoap.org/soap/envelope/' xmlns:ser='http://servicios.ug.edu.ec/'>
               <soapenv:Header/>
               <soapenv:Body>
                  <ser:ejecucionObjeto>
                      <dataSource>".$source."</dataSource>
                     <idServicio>".$tipo."</idServicio>
                     <usuario>".$usuario."</usuario>
                     <clave>".$clave."</clave>
                     <parametrosObjeto>
                        <parametros>
                           ".$datosCuenta." 
                      </parametros>
                     </parametrosObjeto>
                  </ser:ejecucionObjeto>
               </soapenv:Body>
            </soapenv:Envelope>";
            // echo '<pre>'; var_dump($post_string); exit();
                    $headers=array('Content-Length: '.strlen($post_string),'Content-Type: text/xml;charset=UTF-8','SOAPAction: "http://servicios.ug.edu.ec//ejecucionObjeto"','Host:'.$host,'Proxy-Connection: Keep-Alive','User-Agent: Apache-HttpClient/4.1.1 (java 1.5)' );
                    $soap_do = curl_init();
                    curl_setopt ($soap_do, CURLOPT_VERBOSE , true );
                    curl_setopt($soap_do, CURLOPT_URL,            $url );
                    curl_setopt($soap_do, CURLOPT_CONNECTTIMEOUT, 10);
                    curl_setopt($soap_do, CURLOPT_TIMEOUT,        5*60);
                    curl_setopt($soap_do, CURLOPT_RETURNTRANSFER, true );
                    curl_setopt($soap_do, CURLOPT_PORT,8080);
                    curl_setopt($soap_do, CURLOPT_POST, true);
                    curl_setopt($soap_do, CURLOPT_POSTFIELDS,$post_string);
                    curl_setopt($soap_do, CURLOPT_HTTPHEADER,$headers);
                    $result = curl_exec($soap_do);
    if(!$result)
    {
        return "error";
    }
    else
    {
        $response  = $this->ReemplazaCaracteres($result);
        $response = preg_replace("/(<\/?)(\w+):([^>]*>)/", "$1$2$3", $response);

        $xml = new \SimpleXMLElement($response);
        $respuestaConsulta = $xml->xpath('//opciones')[0];

        $resultadoObjeto = json_encode($respuestaConsulta);
        $xml_array = json_decode($resultadoObjeto,TRUE);

        return $xml_array;
    }


}#end function

/* INICIO SPRINT ARELLANO 4.1 */
function doRequestConsultaMateriasAprobadasEstudianteAdmin($datosCuenta,$source,$tipo,$usuario,$clave,$url,$host){  

 $post_string="
            <soapenv:Envelope xmlns:soapenv='http://schemas.xmlsoap.org/soap/envelope/' xmlns:ser='http://servicios.ug.edu.ec/'>
               <soapenv:Header/>
               <soapenv:Body>
                  <ser:ejecucionObjeto>
                      <dataSource>".$source."</dataSource>
                     <idServicio>".$tipo."</idServicio>
                     <usuario>".$usuario."</usuario>
                     <clave>".$clave."</clave>
                     <parametrosObjeto>
                        <parametros>
                           ".$datosCuenta." 
                      </parametros>
                     </parametrosObjeto>
                  </ser:ejecucionObjeto>
               </soapenv:Body>
            </soapenv:Envelope>";
                    $headers=array('Content-Length: '.strlen($post_string),'Content-Type: text/xml;charset=UTF-8','SOAPAction: "http://servicios.ug.edu.ec//ejecucionObjeto"','Host:'.$host,'Proxy-Connection: Keep-Alive','User-Agent: Apache-HttpClient/4.1.1 (java 1.5)' );
                    $soap_do = curl_init();
                    curl_setopt ($soap_do, CURLOPT_VERBOSE , true );
                    curl_setopt($soap_do, CURLOPT_URL,            $url );
                    curl_setopt($soap_do, CURLOPT_CONNECTTIMEOUT, 10);
                    curl_setopt($soap_do, CURLOPT_TIMEOUT,        5*60);
                    curl_setopt($soap_do, CURLOPT_RETURNTRANSFER, true );
                    curl_setopt($soap_do, CURLOPT_PORT,8080);
                    curl_setopt($soap_do, CURLOPT_POST, true);
                    curl_setopt($soap_do, CURLOPT_POSTFIELDS,$post_string);
                    curl_setopt($soap_do, CURLOPT_HTTPHEADER,$headers);
                $result = curl_exec($soap_do);

    if(!$result)
    {
        return "error";
    }
    else
    {
        $response  = $this->ReemplazaCaracteres($result);
        $response= preg_replace("/(<\/?)(\w+):([^>]*>)/", "$1$2$3", $response);
        $xml = new \SimpleXMLElement($response);
         $respuesta = $xml->xpath('//resultadoObjeto')[0];
        $respuesta = $xml->xpath('//parametrosSalida')[0]; 
        return $respuesta;
    }
}#end function   
/* FIN SPRINT ARELLANO 4.1 */

}#end Clase
