<?php 
namespace Titulacion\SisAcademicoBundle\Controller;

    use Symfony\Bundle\FrameworkBundle\Controller\Controller;
    use Symfony\Component\HttpFoundation\Response;
    use Symfony\Component\HttpFoundation\Request;
    use Symfony\Component\HttpFoundation\JsonResponse;
    use Titulacion\SisAcademicoBundle\Helper\UgServices;
    
class NotificacionesController extends Controller
{
      var $v_html="";
      var $Emisor="";
      var $Universidad="";
      var $Estudiantes="";
      var $TipoMensaje="";
      var $Curso="";
      var $Asunto="";
      var $mensaje="";
      var $Correo_enivar = "";
        public function index_notificacionesAction(Request $request)
        {   
            $session=$request->getSession();
              $perfilEst   = $this->container->getParameter('perfilEst');
                $perfilDoc   = $this->container->getParameter('perfilDoc');
                $perfilAdmin = $this->container->getParameter('perfilAdmin');
                $perfilEstDoc = $this->container->getParameter('perfilEstDoc');
                $perfilEstAdm = $this->container->getParameter('perfilEstAdm');
                $perfilDocAdm = $this->container->getParameter('perfilDocAdm');
            
            $idUsuario  = $session->get('id_user');
           $idUsuario = Rtrim($idUsuario); 
            $Emisor   = $request->request->get('Emisor');
            $Universidad   = $request->request->get('Universidad');
            $Estudiantes = $request->request->get('Estudiantes'); 
            $TipoMensaje = $request->request->get('TipoMensaje'); 
            $Curso = $request->request->get('Curso'); 
            $Asunto = $request->request->get('Asunto');
            $mensaje = $request->request->get('mensaje');
             $Profesores = $request->request->get('Profesores');
             $Estudiantes = $request->request->get('Estudiantes');
             $Paralelo = $request->request->get('Paralelo');
//              echo var_dump($Profesores); 
//              echo var_dump($Estudiantes);
//              echo var_dump($Paralelo);exit();
         if($session->has("perfil")) {
            if($mensaje == null){
           $lcFacultad="";
                     $lcCarrera="";
                       $Carreras = array();
                        $UgServices = new UgServices;
                        $facultades = $UgServices->Mensajes_Enviados($idUsuario);
                        $Paralelos = $UgServices->Paralelos(4);
                        
                             return $this->render('TitulacionSisAcademicoBundle:Notificaciones:index_notificaciones.html.twig',
    									array(
    											'data' => array('facultades' => $facultades,
                                                                                                        'Paralelos' => $Paralelos )
    										 )
                              );
               
            }else{
                
                 $xmlFinal="
                      <Notificaciones>
                        <Notificaciones>
                            <Tipo_mensaje>$TipoMensaje</Tipo_mensaje>
                            <Emisor>$idUsuario</Emisor>
                            <Estado>1</Estado>
                             <Asunto>$Asunto</Asunto>
                            <Mensaje>$mensaje</Mensaje>	
                            <Opciones>'A,P,U'</Opciones> 
                        </Notificaciones>	
                    </Notificaciones>";
                  
                     $UgServices = new UgServices;
                    $xml = $UgServices->Guarda_Mensajes($xmlFinal);
                       
                    $Correos_Numeros = $UgServices->Datos(3);
                   // echo var_dump($Correos_Numeros); exit();
                  
                   $i=0;
                     
                   foreach($Correos_Numeros as $Correos_Numeros_) {
                              
                       if($Correos_Numeros_['correo_institucional']!=""){
                           $Correo_enivar = $Correos_Numeros_['correo_institucional'];                          
                       }else{
                           $Correo_enivar = $Correos_Numeros_['correoestudiante'];
                       }
                       
                       if($Correo_enivar!=""){
                         if($i < 10){  
                             
                              $mailer    = $this->container->get('mailer');
                            $transport = \Swift_SmtpTransport::newInstance('smtp.gmail.com',465,'ssl')
                                    ->setUsername('titulacion.php@gmail.com')
                                    ->setPassword('sc123456');
    //                   //$mailer  = \Swift_Mailer($transport);
                        $message = \Swift_Message::newInstance('test')
                                    ->setSubject($Asunto)
                                    ->setFrom('titulacion.php@gmail.com',$Emisor)
                                    ->setTo($Correo_enivar)
                                   ->setBody($mensaje);
                        $this->get('mailer')->send($message);
                        
                           //sms mensajes sms  descomentar cuando se configure                        
//                           $receptor =$Correos_Numeros_['celular'];
////                           
//                            $objGsmOut = new \COM ('ActiveXperts.GsmOut');
//
//                              $archivo = 'C:\log.txt';
//                              $dispositivo =  'SAMSUNG Mobile USB Modem';
//
//                             $velocidad = 0;
//
//                              $objGsmOut->LogFile          = $archivo; 
//                              $objGsmOut->Device           = $dispositivo;
//                              $objGsmOut->DeviceSpeed      = $velocidad; 
//
//                              $objGsmOut->MessageRecipient = $receptor;
//                             $objGsmOut->MessageData      = $mensaje;
//
//                              if($objGsmOut->LastError == 0){
//                                $objGsmOut->Send;                                                           
//                             }
                        
                        
                         }
                    $i = $i+1;
                       }
                    }
          
                     try
                { $lcFacultad="";
                      $lcCarrera="";
                       $Carreras = array();
                        $UgServices = new UgServices;
                        $facultades = $UgServices->Mensajes_Enviados($idUsuario);
                     $Paralelos = $UgServices->Paralelos(4);
                             return $this->render('TitulacionSisAcademicoBundle:Notificaciones:index_notificaciones.html.twig',
    									array(
    											'data' => array('facultades' => $facultades,
                                                                                                        'Paralelos' => $Paralelos )
    										 )
                              );
                        
                  }catch (\Exception $e)
                      {                       
                    return $this->render('TitulacionSisAcademicoBundle:Notificaciones:index_notificaciones.html.twig');
                      } 
            }
        }else{
            $this->get('session')->getFlashBag()->add(
                                'mensaje',
                                'Inicie Sesion'
                            );
            return $this->render('TitulacionSisAcademicoBundle:Home:login.html.twig');
        }
        }
            
        public function mensajes_universidadAction(Request $request)
            {    $session=$request->getSession();   
            $idUsuario  = $session->get('id_user');
                if($session->has("perfil")) {
                    $UgServices = new UgServices;
                    $Mensajes_Recividos = $UgServices->Mensajes_No_Leidos($idUsuario);
                
                    return $this->render('TitulacionSisAcademicoBundle:Notificaciones:mensajes_universidad.html.twig',
    									array(
    											'data' => array('Mensajes' => $Mensajes_Recividos)
    										 )
                              );
                }else{
                    return $this->render('TitulacionSisAcademicoBundle:Home:login.html.twig');
                }
                   
            }
        
         public function eventos_universidadAction(Request $request)
            {  $session=$request->getSession();   
             $idUsuario  = $session->get('id_user');
                if($session->has("perfil")) {
                     $UgServices = new UgServices;
                     $Eventos_Recividos = $UgServices->Eventos_Recibidos($idUsuario);
                    
                return $this->render('TitulacionSisAcademicoBundle:Notificaciones:eventos_universidad.html.twig',
    									array(
    											'data' => array('Mensajes' => $Eventos_Recividos)
    										 )
                              );
                }else{
                    return $this->render('TitulacionSisAcademicoBundle:Home:login.html.twig');
                }
            }
        
         public function notificaciones_universidadAction(Request $request)
            {    
             
                $session=$request->getSession();   
                 $idUsuario  = $session->get('id_user');
                if($session->has("perfil")) {
                     $UgServices = new UgServices;
                     $Notificaciones_Recividos = $UgServices->Notificaciones_Recibidas($idUsuario);
                     
                return $this->render('TitulacionSisAcademicoBundle:Notificaciones:notificaciones_universidad.html.twig',	array(
    											'data' => array('Mensajes' => $Notificaciones_Recividos)
    										 )
                              );
                }else{
                    return $this->render('TitulacionSisAcademicoBundle:Home:login.html.twig');
                }
            }
        
         public function perfilAction(Request $request)
            {        
                $Nombres =$request->request->get('Nombres');
                $session=$request->getSession();   
                 $idUsuario  = $session->get('id_user');
                if($session->has("perfil")) {
                if($Nombres == null){
                return $this->render('TitulacionSisAcademicoBundle:Notificaciones:perfil.html.twig', array('name' => $Nombres));
                }else{                
                return $this->render('TitulacionSisAcademicoBundle:Notificaciones:perfil.html.twig', array('name' => $Nombres));
                }
                }else{
                    return $this->render('TitulacionSisAcademicoBundle:Home:login.html.twig');
                }
            }
        
         public function passwordAction(Request $request)
            {         $session=$request->getSession(); 
             $idUsuario  = $session->get('id_user');
                if($session->has("perfil")) {
                return $this->render('TitulacionSisAcademicoBundle:Notificaciones:password.html.twig');
                }else{
                    return $this->render('TitulacionSisAcademicoBundle:Home:login.html.twig');
                }
            }
        
         public function sugerenciaAction(Request $request)
            {       $sugerencia = $request->request->get('sugerencia');
        $session=$request->getSession();   
                if($session->has("perfil")) {
            if($sugerencia == null){  
                return $this->render('TitulacionSisAcademicoBundle:Notificaciones:sugerencia.html.twig');
            }else{
              
                
                $mailer    = $this->container->get('mailer');
                    $transport = \Swift_SmtpTransport::newInstance('smtp.gmail.com')
                                ->setUsername('titulacio.php@gmail.com')
                                ->setPassword('sc123456');
                  // $mailer  = \Swift_Mailer($transport);
                    $message = \Swift_Message::newInstance('test')
                                ->setSubject("sugerencia")
                                ->setFrom('titulacio.php@gmail.com')
                                ->setTo('gabrielhuayamabe@hotmail.com')
                                ->setBody($sugerencia);
                    $this->get('mailer')->send($message);
                     return $this->render('TitulacionSisAcademicoBundle:Notificaciones:sugerencia.html.twig');
                                 }
            }else{
                    return $this->render('TitulacionSisAcademicoBundle:Home:login.html.twig');
                }
         }
        
        public function LecturaAction(Request $request)
            {      
            
            $session=$request->getSession();   
                if($session->has("perfil")) {   
                return $this->render('TitulacionSisAcademicoBundle:Notificaciones:Lectura.html.twig');
                }else{
                    return $this->render('TitulacionSisAcademicoBundle:Home:login.html.twig');
                }
            }
        
        

}