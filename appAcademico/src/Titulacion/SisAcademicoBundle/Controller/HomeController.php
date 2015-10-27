<?php
    namespace Titulacion\SisAcademicoBundle\Controller;

    use Symfony\Bundle\FrameworkBundle\Controller\Controller;
    use Symfony\Component\HttpFoundation\Response;
    use Symfony\Component\HttpFoundation\Request;
    use Symfony\Component\HttpFoundation\JsonResponse; 
    use Titulacion\SisAcademicoBundle\Helper\UgServices;

/**
*
*/
class HomeController extends Controller
{
    public function enviarmailAction(Request $request){
        $user = $request->request->get('user');
        #recepto desde la base el correo
        $UgServices = new UgServices;
        $data = $UgServices->getConsultaCorreo($user);

        $email = '';


         if ($data) {

              $count  = count($data);
               if($count == 1){
                   $email   = $data[0]['correo'];
              }
         }




       // $email = "arellano.torres27gmail.com"; #quemado por el momento
           
          if($email != '')
          {
                $source = 'abcdefghijklmnopqrstuvwxyz';
                $source .= 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
                $source .= '1234567890';
                $source .= '$%*&';

                $rstr = "";
                $source = str_split($source,1);
                for($i=1; $i<=8; $i++){
                    mt_srand((double)microtime() * 1000000);
                    $num = mt_rand(1,count($source));
                    $rstr .= $source[$num-1];
                }

               $message = \Swift_Message::newInstance()
                ->setSubject('Activación Password')
                ->setFrom('titulacion.php@gmail.com')
                ->setTo($email)
                ->setBody($this->renderView('TitulacionSisAcademicoBundle:Admin:link_cambio_clave.html.twig',  array('clave' => $rstr)),'text/html', 'utf8');
                $resp = $this->get('mailer')->send($message);
         
                   // echo "<respuesta envio>";
                   //     var_dump($resp);
                   //    echo "</respuesta envio>"; 
                   //   exit();
                
                if($resp == 1){
  
                    $salt    = "µ≈α|⊥ε¢ʟ@δσ";
                    $password = password_hash($rstr, PASSWORD_BCRYPT, array("cost" => 14, "salt" => $salt));
                    $dataMant = $UgServices->mantenimientoUsuario($user,'','','',$password,'O');
                    
                    if ( is_object($dataMant))
                    {
                     $estado = $dataMant ->PI_ESTADO;
                     $message = $dataMant ->PV_MENSAJE;
                    }

                    // echo "<pre>";
                    // var_dump($estado.'-----'.$message);
                    // echo "</pre>";
                    // exit();
                   
                    
                }

              echo $estado; exit();
            }else{
                echo '2' ; exit();

            }


          // $respuesta = array(
          //      "Codigo" => $estado ,
          //      "Mensaje" => $message,
          //   );
            
          // return new Response(json_encode($respuesta));

    }


	public function ingresarAction(Request $request)
         {

        $perfil = 1;

        if($request->getMethod()=="POST")
        {
            #obtenemos los datos guardados en la variable global
            $perfilEst   = $this->container->getParameter("perfilEst");
            $perfilDoc   = $this->container->getParameter("perfilDoc");
            $perfilAdmin = $this->container->getParameter("perfilAdmin");
            #obtenemos los datos enviados por get
            $username    = $request->request->get('user');
            //$password    = $request->request->get('pass');
            $contrasenia = $request->request->get('pass');
            

            $salt    = "µ≈α|⊥ε¢ʟ@δσ";
            $password = password_hash($contrasenia, PASSWORD_BCRYPT, array("cost" => 14, "salt" => $salt));
            


            #llamamos a la consulta del webservice
            $UgServices = new UgServices;
            $data = $UgServices->getLogin($username,$password);

            if ($data) {
                $login_act     =array();
                $perfilUsuario = null;
                $count         = count($data);

                if($count == 1){
                    $perfil        = $data[0]['idrol'];
                    $idUsuario     = $data[0]['usuario'];
                    $nombreUsuario = $data[0]['nombreusuario'];
                    $cedula        = $data[0]['cedula'];

                    $mail          = $data[0]['mail'];
                     $descRol       = $data[0]['descrol'];

                }else{


                    foreach ($data as $login) {
                        $idUsuario     = $login['usuario'];
                        $nombreUsuario = $login['nombreusuario'];
                        $cedula        = $login['cedula'];


                        $mail          = $login['mail'];


                        $descRol       = $login['descrol'];

                        if ($login['idrol'] == $perfilAdmin) {
                            $perfil = (int)$perfil + (int)$perfilAdmin;
                        }elseif ($login['idrol'] == $perfilEst) {
                            $perfil = (int)$perfil + (int)$perfilEst;
                        }elseif ($login['idrol'] == $perfilDoc) {
                            $perfil = (int)$perfil + (int)$perfilDoc;
                        }
                    }
                }
                $session=$request->getSession();
                $session->set("id_user",$idUsuario);
                $session->set("perfil",$perfil); //idrol
                $session->set("nom_usuario",$nombreUsuario);
                $session->set("cedula",$cedula);
                $session->set("mail",$mail);
                $session->set("descRol",$descRol);//nombre rol

                $respuesta = array(
                  "Perfil" => $perfil ,
                  "NombreUsuario" => $nombreUsuario,
                );
                return new Response(json_encode($respuesta));

                //return new Response($perfil);
            }else{
                $perfil = 5;# error usuario y contraseña no

                 $respuesta = array(
                  "Perfil" => '05' ,
                  "NombreUsuario" => '',
                );

                return new Response(json_encode($respuesta));
                //return new Response('05');
            }




        }else{

        return $this->render('TitulacionSisAcademicoBundle:Home:login.html.twig');
        }

    }#end function


    /**
     * [indexAction description]
     */
    public function indexAction(Request $request)
        {

            // echo "entre al index"; exit();
            $session=$request->getSession();

            $perfilEst    = $this->container->getParameter('perfilEst');
            $perfilDoc    = $this->container->getParameter('perfilDoc');
            $perfilAdmin  = $this->container->getParameter('perfilAdmin');
            $perfilEstDoc = $this->container->getParameter('perfilEstDoc');
            $perfilEstAdm = $this->container->getParameter('perfilEstAdm');
            $perfilDocAdm = $this->container->getParameter('perfilDocAdm');

            if ($session->has("perfil")) {
                if($session->get('perfil') == $perfilDoc || $session->get('perfil') == $perfilEstDoc || $session->get('perfil') == $perfilDocAdm){#docente
                     $idDocente     = 1;
                     $datosCarreras =  array(
                                          array( 'idCarrera' => '135', 'nombreCarrera'=>'Ingeniería en Sistemas Computaciones', 'order'=>'One' ),
                                          array( 'idCarrera' => '246', 'nombreCarrera'=>'Ingeniería Química', 'order'=>'Two' ),
                                          array( 'idCarrera' => '789', 'nombreCarrera'=>'Ingeniería Civil', 'order'=>'Three' )
                                       );
                     $datosDocente  = array( 'idDocente' => $idDocente );




                     return $this->render('SisAcademicoBundle:Docentes:listadoCarreras.html.twig',
                                                array(
                                                        'data' => array('datosDocente' => $datosDocente,  'datosCarreras' => $datosCarreras)
                                                     )
                                            );
                  }elseif ($session->get('perfil') == $perfilEst || $session->get('perfil') == $perfilEstDoc || $session->get('perfil') == $perfilEstAdm) {
                      return $this->redirect($this->generateUrl('estudiantes_notas_actuales'));
                  }
            }else{
                $pagina = 1;
                $services = '';
                $error = false;
                $user_name = '';
                $redirect = false;
                $datos_menu_izquierda = array();
                $datos_menu_izquierda = array( 'error' => $error,
                        'services' => $services );

                return $this->render('TitulacionSisAcademicoBundle:Home:index.html.twig',
                                        array(
                                                'data' => array('service_selected' => 'DatosMenu',
                                                        'services_menu_izq' => $datos_menu_izquierda

                                                               )
                                             )
                );
            }
        }



        public function logoutAction(Request $request){


        $session=$request->getSession();
        $session->clear();


        $this->get('session')->getFlashBag()->add(
                                'mensaje',
                                'Se ha cerrado sessión exitosamente, gracias por visitarnos'
                            );
         $pagina = 1;
                $services = '';
                $error = false;
                $user_name = '';
                $redirect = false;
                $datos_menu_izquierda = array();
                $datos_menu_izquierda = array( 'error' => $error,
                        'services' => $services );

                return $this->render('TitulacionSisAcademicoBundle:Home:login.html.twig',
                                        array(
                                                'data' => array('service_selected' => 'DatosMenu',
                                                        'services_menu_izq' => $datos_menu_izquierda

                                                               )
                                             )
                );
    }

}