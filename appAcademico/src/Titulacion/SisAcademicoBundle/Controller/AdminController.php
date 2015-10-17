<?php

namespace Titulacion\SisAcademicoBundle\Controller;
//prueba git<
    use Symfony\Bundle\FrameworkBundle\Controller\Controller;
    use Symfony\Component\HttpFoundation\Response;
    use Symfony\Component\HttpFoundation\Request;
    use Symfony\Component\HttpFoundation\JsonResponse;
    use Titulacion\SisAcademicoBundle\Helper\UgServices;

class AdminController extends Controller
{


    public function calendario_carreraAction(){



        // $tareas =  array(array( 'tarealm' => 'leccion1'),
        //                 array( 'tarealm' => 'leccion2'),
        //                 array( 'tarealm' => 'taller1'),
        //                 array( 'tarealm' => 'taller2'), );

        // return = $this->render('TitulacionSisAcademicoBundle:Admin:calendario_carrera.html.twig',
        //                   array(    'data' => array(
        //                                      'datosDocente' => $datosDocente,
        //                                      'datosCarrera' => $datosCarrera2,
        //                                      'datosMaterias' => $datosMaterias
        //                                 )
        //                   ));
        $arreglo = array(
                            array('evento' => 'evento1' ),
                            array('evento' => 'evento2' ),
                            array('evento' => 'evento3' ),
                            array('evento' => 'evento4' )
                        );


        return $this->render('TitulacionSisAcademicoBundle:Admin:calendario_carrera.html.twig', array('data' => $arreglo));
        // $response->setData(
        //                         array(
        //             'error'         => $this->v_error,
        //             'msg'           => $this->v_msg,
        //                                 'html'          => $this->v_html,
        //                                 'withoutModal'  => $withoutModal,
        //                                 'recargar'      => '0'
        //                              )
        //                       );
        // return $response;
    }

    public function cambio_passwordAction(){
    	return $this->render('TitulacionSisAcademicoBundle:Admin:cambio_password.html.twig', array());
    }

    public function ingreso_nuevo_passAction(Request $request){
        #obtenemos los datos enviados por get
            $session=$request->getSession();
            $Email= $session->get('mail');
            $Nombre = $session->get('nom_usuario');
            $username    = $request->request->get('user');
            $username    = $request->request->get('pass1');
            $password    = $request->request->get('pass2');
           
              
        
        #llamamos a la consulta del webservice
        $UgServices = new UgServices;
        
               $mailer    = $this->container->get('mailer');
                    $transport = \Swift_SmtpTransport::newInstance('smtp.gmail.com',465,'ssl')
                                ->setUsername('titulacion.php@gmail.com')
                                ->setPassword('sc123456');
                   //$mailer  = \Swift_Mailer($transport);
                    $message = \Swift_Message::newInstance('test')
                                ->setSubject("Contraseña Cambiada Correctamente")
                                ->setFrom('titulacion.php@gmail.com','Universidad de Guayaquil')
                                ->setTo($Email)
                                ->setBody("$Nombre usted ha Cambiado la Contraseña Exitosamente, Su nueva contraseña es 123456");
                    // ->setBody($this->renderView('TitulacionSisAcademicoBundle:Admin:Comtraseña.html.twig'),'text/html', 'utf8');
                    $this->get('mailer')->send($message);   
    }

    public function cargar_eventosAction(Request $request)
    {
        #llamamos a la consulta del webservice
        $UgServices = new UgServices;
        // $data = $UgServices->getEventos($start,$end);
//         $data = array(
//   "id"=>
//   "1",
//   "title"=>
//    "hola",
//   "content"=>
//    "mundo",
//   "start_date"=>
//   "2015-09-28 22:00:00",
//   "end_date"=>
//    "2015-09-29 22:00:00",
//   "access_url_id"=>
//   "1",
//   "all_day"=>
//    "0"
// );

        echo '<pre>'; var_dump($data); exit();


    }

    public function cargar_eventos_carrera_userAction(Request $request)
    {

        #llamamos a la consulta del webservice
        $UgServices = new UgServices;



        return $this->render('TitulacionSisAcademicoBundle:Admin:calendario_academico_carrera_user.html.twig', array());
    }


}