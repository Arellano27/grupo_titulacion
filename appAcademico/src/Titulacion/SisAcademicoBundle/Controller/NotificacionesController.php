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
        {   $idEstudiante  =2;
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
            try
                { $lcFacultad="";
                     $lcCarrera="";
                       $Carreras = array();
                        $UgServices = new UgServices;
                        $xml = $UgServices->Mensajes_Enviados($idEstudiante);
                        
                        if ( is_object($xml))
                        {
                                $cont = 0;
                                while($cont <= 5){
                                $materiaObject = array( 'Tipo' => 'Mensaje',
                                                             'Asunto'=>'Inicio Clases',
                                                             'Detalle'=>'Ciclo 1 2016',
                                                                'Fecha'=>'22/09/2015',
                                                            );                                
                                    array_push($Carreras, $materiaObject); 
                                  $cont = $cont +1;
                                 }
                                      
                                return $this->render('TitulacionSisAcademicoBundle:Notificaciones:index_notificaciones.html.twig',array(
                                                  'facultades' =>  $Carreras,
                                                  'idEstudiante'=>1,
                                                  'cuantos'=>4
                        )); 
                          }
                           else
                            {
                              throw new \Exception('Un error');
                            }
                        
                  }catch (\Exception $e)
                      {                       
                    return $this->render('TitulacionSisAcademicoBundle:Notificaciones:index_notificaciones.html.twig');
                      }
            }else{
                for($i=0;$i<2;$i++){
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
                        $xml = $UgServices->Mensajes_Enviados($idEstudiante);
                        
                        if ( is_object($xml))
                        {
                                $cont = 0;
                                while($cont <= 5){
                                $materiaObject = array( 'Tipo' => 'Mensaje',
                                                             'Asunto'=>'Inicio Clases',
                                                             'Detalle'=>'Ciclo 1 2016',
                                                                'Fecha'=>'22/09/2015',
                                                            );                                
                                    array_push($Carreras, $materiaObject); 
                                  $cont = $cont +1;
                                 }
                                      
                                return $this->render('TitulacionSisAcademicoBundle:Notificaciones:index_notificaciones.html.twig',array(
                                                  'facultades' =>  $Carreras,
                                                  'idEstudiante'=>1,
                                                  'cuantos'=>4
                                          )); 
                                      }
                           else
                            {
                              throw new \Exception('Un error');
                            }
                        
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
                    return $this->render('TitulacionSisAcademicoBundle:Notificaciones:mensajes_universidad.html.twig');
                }else{
                    return $this->render('TitulacionSisAcademicoBundle:Home:login.html.twig');
                }
                   
            }
        
         public function eventos_universidadAction(Request $request)
            {         $session=$request->getSession();   
                if($session->has("perfil")) {
                return $this->render('TitulacionSisAcademicoBundle:Notificaciones:eventos_universidad.html.twig');
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