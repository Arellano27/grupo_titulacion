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
        public function index_notificacionesAction(Request $request)
        {   $idEstudiante  = 1;
         $session=$request->getSession();
            $Emisor   = $request->request->get('Emisor');
            $Universidad   = $request->request->get('Universidad');
            $Estudiantes = $request->request->get('Estudiantes'); 
            $TipoMensaje = $request->request->get('TipoMensaje'); 
            $Curso = $request->request->get('Curso'); 
            $Asunto = $request->request->get('Asunto');
            $mensaje = $request->request->get('mensaje');
         if($session->has("perfil")) {
            if($mensaje == null){
           $lcFacultad="";
                     $lcCarrera="";
                       $Carreras = array();
                        $UgServices = new UgServices;
                        $facultades = $UgServices->Mensajes_Enviados($idEstudiante);
                        
                             return $this->render('TitulacionSisAcademicoBundle:Notificaciones:index_notificaciones.html.twig',
    									array(
    											'data' => array('facultades' => $facultades)
    										 )
                              );
               
            }else{
                for($i=0;$i<10;$i++){
                   // if($i==0){
                    $mailer    = $this->container->get('mailer');
                    $transport = \Swift_SmtpTransport::newInstance('smtp.gmail.com',465,'ssl')
                                ->setUsername('titulacion.php@gmail.com')
                                ->setPassword('sc123456');
                   //$mailer  = \Swift_Mailer($transport);
                    $message = \Swift_Message::newInstance('test')
                                ->setSubject($Asunto)
                                ->setFrom('titulacion.php@gmail.com',$Emisor)
                                ->setTo('gabrielhuayamabe@hotmail.com')
                                ->setBody($mensaje);
                    $this->get('mailer')->send($message);
//                    }else{
//                         $mailer    = $this->container->get('mailer');
//                    $transport = \Swift_SmtpTransport::newInstance('smtp.gmail.com',465,'ssl')
//                                ->setUsername('ugacademico@gmail.com')
//                                ->setPassword('SisAcademico');
//                   //$mailer  = \Swift_Mailer($transport);
//                    $message = \Swift_Message::newInstance('test')
//                                ->setSubject($Asunto)
//                                ->setFrom('ugacademico@gmail.com')
//                                ->setTo('gabrielhuayamabe@hotmail.com')
//                                ->setBody($mensaje);
//                    $this->get('mailer')->send($message);
//                    }
                }
                     try
                { $lcFacultad="";
                      $lcCarrera="";
                       $Carreras = array();
                        $UgServices = new UgServices;
                        $facultades = $UgServices->Mensajes_Enviados($idEstudiante);
                        
                             return $this->render('TitulacionSisAcademicoBundle:Notificaciones:index_notificaciones.html.twig',
    									array(
    											'data' => array('facultades' => $facultades)
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
                if($session->has("perfil")) {
                    $UgServices = new UgServices;
                    $Mensajes_Recividos = $UgServices->Mensajes_No_Leidos(2);
                    var_dump($Mensajes_Recividos);
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
                if($session->has("perfil")) {
                     $UgServices = new UgServices;
                     $Eventos_Recividos = $UgServices->Eventos_Recividos(2);
                    
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
                if($session->has("perfil")) {
                return $this->render('TitulacionSisAcademicoBundle:Notificaciones:notificaciones_universidad.html.twig');
                }else{
                    return $this->render('TitulacionSisAcademicoBundle:Home:login.html.twig');
                }
            }
        
         public function perfilAction(Request $request)
            {        
                $Nombres =$request->request->get('Nombres');
                $session=$request->getSession();   
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
            {      $session=$request->getSession();   
                if($session->has("perfil")) {   
                return $this->render('TitulacionSisAcademicoBundle:Notificaciones:Lectura.html.twig');
                }else{
                    return $this->render('TitulacionSisAcademicoBundle:Home:login.html.twig');
                }
            }
        
        

}