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

  public function get_captchaAction(Request $request){

    $string = '';

    for ($i = 0; $i < 5; $i++) {
       $numC = rand(1,3);

       switch ($numC) {
        case '1':
             $string .= chr(rand(97, 122));
           break;
        case '2':
              $string .= chr(rand(48, 57));
           break;
        case '3':
              $string .= chr(rand(65, 90));
           break;
         default:
           $string .= chr(rand(97, 122));
           break;
       }
    }

    $session=$request->getSession();
    $session->set("random_number",$string);
    $dir = 'fonts/';
    $image = imagecreatetruecolor(165, 50);
    // random number 1 or 2
    $num = rand(1,2);

    if($num==1)
    {
      $font = "HandVetica.ttf"; // font style
    }
    else
    {
      $font = "Sketch_Block.ttf";// font style
    }
 
    // random number 1 or 2
    $num2 = rand(1,2);
    if($num2==1){
      $color = imagecolorallocate($image, 113, 193, 217);// color
    }
    else{
      $color = imagecolorallocate($image, 163, 197, 82);// color
    }

    $white = imagecolorallocate($image, 255, 255, 255); // background color white
    imagefilledrectangle($image,0,0,399,99,$white);
    imagettftext ($image, 25, 0, 10, 40, $color, $dir.$font, $string);
    header("Content-type: image/png");
    imagepng($image);

    // return base64_encode($image);

  }


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


    }


	public function ingresarAction(Request $request)
         {

        $perfil = '';

        if($request->getMethod()=="POST")
        {

          $session=$request->getSession();
          $captchaEnv=$request->request->get('code');
          $captchaGene= $session->get('random_number') ;

          if( $captchaEnv != $captchaGene)
          {
            $respuesta = array(
                "valerror" => "1"
            );
            return new Response(json_encode($respuesta));
          }

            #obtenemos los datos guardados en la variable global
            $perfilEst   = $this->container->getParameter("perfilEst");
            $perfilDoc   = $this->container->getParameter("perfilDoc");
            $perfilAdmin = $this->container->getParameter("perfilAdmin");
            $perfilCordi = $this->container->getParameter("perfilCordi");
            #obtenemos los datos enviados por get
            $username    = $request->request->get('user');
            //$password    = $request->request->get('pass');
            $contrasenia = $request->request->get('pass');



            $rol_secretario = '';
            $rol_coordinador = "";
            $data_secretario = "";
            $data_coordinador = "";

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

                    if ($perfilAdmin == $perfil) {
                      $rol_secretario = $perfil;
                    }elseif ($perfilCordi == $perfil) {
                      $rol_coordinador = $perfil;
                    }


                }else{



                    foreach ($data as $login) {
                        $idUsuario     = $login['usuario'];
                        $nombreUsuario = $login['nombreusuario'];
                        $cedula        = $login['cedula'];
                        $mail          = $login['mail'];
                        $descRol       = $login['descrol'];
                        $perfil       .=  $login['idrol'];

                        if ($perfilAdmin == $login['idrol']) {
                          $rol_secretario = $login['idrol'];
                        }elseif ($perfilCordi == $login['idrol']) {
                          $rol_coordinador = $login['idrol'];
                        }

                    }#end foreach
                }

               $datosConsulta       = array( 'idUsuario' => $idUsuario);
               $datosUsuarioArray   = $UgServices->Titulacion_getConsultaPerfilUsuario($datosConsulta);
               $datosUsuario        = $datosUsuarioArray[0];
               

                $id_rol = $perfil;
                $session=$request->getSession();
                $session->set("id_user",$idUsuario);
                $session->set("perfil",$perfil); //idrol
                $session->set("img_perfil",$datosUsuario["directoriofoto"]);
                $session->set("nom_usuario",$nombreUsuario);
                $session->set("cedula",$cedula);
                $session->set("mail",$mail);
                $session->set("descRol",$descRol);//nombre rol


                if ($rol_secretario  != "") {
                  $data_secretario = $UgServices->getRolesAdmin($idUsuario,$rol_secretario);
                  // echo '<pre>'; var_dump($data_secretario); exit();
                  $session->set("data_secretario",$data_secretario);
                }elseif ($rol_coordinador != "") {
                  $data_coordinador = $UgServices->getRolesAdmin($idUsuario,$rol_coordinador);
                  $session->set("data_coordinador",$data_coordinador);
                }

                if(strlen($id_rol)>1){
                    $id_rol = mb_substr($id_rol,0,1);
                }else{
                  $id_rol = $id_rol;
                }
                // $id_rol = 3;

                $rsCarrera = $UgServices->getConsultaCarreras($idUsuario,$id_rol);

                // echo '<pre>'; var_dump($rsCarrera); exit();
                $resultadoObjeto = json_encode($rsCarrera);
                $xml_array = json_decode($resultadoObjeto,TRUE);
                if(!isset($xml_array["registros"]["registro"]["id_sa_carrera"])) {
                  $tempRegistro  = $xml_array["registros"]["registro"][0];
                  $xml_array["registros"]["registro"] = array();
                  $xml_array["registros"]["registro"] = $tempRegistro;
                  unset($tempRegistro);
                }

                $session->set("îdcarrera_calendar",$xml_array["registros"]["registro"]["id_sa_carrera"]);
                $session->set("îdciclo_calendar",$xml_array["registros"]["registro"]["id_sa_ciclo_detalle"]);

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
   
    public function editarPerfilAction(Request $request) {
         $session    =$request->getSession();
         $idUsuario  = $session->get('id_user');
         
         $UgServices          = new UgServices;
         $datosConsulta       = array( 'idUsuario' => $idUsuario);
         $datosUsuarioArray   = $UgServices->Titulacion_getConsultaPerfilUsuario($datosConsulta);

         $datosUsuario        = $datosUsuarioArray[0];
         if($datosUsuario['directoriofoto']!=NULL) {
            $datosUsuario['existefotoperfil'] = 1;
         }
         else {
            $datosUsuario['existefotoperfil'] = 0;
         }
         return $this->render('TitulacionSisAcademicoBundle:Home:visualizarPerfil.html.twig',
                           array(
                              'dataUsuario' => $datosUsuario
                           )
                        );
      } // editarPerfilAction()
      
      public function editarPerfilActualizarAction(Request $request)
      {
         $session=$request->getSession();
         $idUsuario  = $session->get('id_user');
         
         $UgServices          = new UgServices;
         //Consulta los datos del usuario
         $datosConsulta       = array( 'idUsuario' => $idUsuario);
         $datosUsuario        = $UgServices->Titulacion_getConsultaPerfilUsuario($datosConsulta);
         
         //Consulta de parametro - Tipo de sangre
         $datosConsulta       = array( 'parametro' => 6);
         $datosTiposSangre    = $UgServices->Titulacion_getParametroPerfilUsuario($datosConsulta);
         //Consulta de parametro - sexo
         $datosConsulta       = array( 'parametro' => 7);
         $datosGeneros        = $UgServices->Titulacion_getParametroPerfilUsuario($datosConsulta);
         //Consulta de parametro - estado civil
         $datosConsulta       = array( 'parametro' => 5);
         $datosEstadosCiviles = $UgServices->Titulacion_getParametroPerfilUsuario($datosConsulta);
         //Consulta de parametro - nacionalidad
         $datosConsulta       = array( 'parametro' => 23);
         $datosNacionalidades = $UgServices->Titulacion_getParametroPerfilUsuario($datosConsulta);
         //Consulta de parametro - pais
         $datosConsulta       = array( 'parametro' => 4);
         $datosPaises         = $UgServices->Titulacion_getParametroPerfilUsuario($datosConsulta);
//         
//         $UgServices          = new UgServices;
//         $datosConsulta       = array( 'idUsuario' => $idUsuario);
//         $datosUsuarioArray   = $UgServices->Titulacion_getConsultaPerfilUsuarioEditar($datosConsulta);

//         $datosUsuario        = $datosUsuarioArray[0];
         if(!isset($datosUsuario['nombres'])){
            if(isset($datosUsuario[0])) {
               $tempDataUsuario  = $datosUsuario[0];
               $datosUsuario     = NULL;
               $datosUsuario     = $tempDataUsuario;
               unset($tempDataUsuario);
               
               if($datosUsuario['directoriofoto']!=NULL) {
                  $datosUsuario['existefotoperfil'] = 1;
               }
               else {
                  $datosUsuario['existefotoperfil'] = 0;
               }
            }
            else {
               $datosUsuario = NULL;
            }
         }
         
         return $this->render('TitulacionSisAcademicoBundle:Home:editarPerfil.html.twig',
                           array(
                              'dataUsuario' => $datosUsuario,
                              'datosTiposSangre' => $datosTiposSangre,
                              'datosGeneros' => $datosGeneros,
                              'datosEstadosCiviles' => $datosEstadosCiviles,
                              'datosNacionalidades' => $datosNacionalidades,
                              'datosPaises' => $datosPaises,
                           )
                        );
      } // editarPerfilAction()
      
      public function grabarEditarPerfilActualizarAction(Request $request)
      {
         $respuesta  = new Response("",200);
         $session    = $request->getSession();
         $idUsuario  = $session->get('id_user');
         $dataPerfil = $request->request->get('dataPerfil');
         $imagenPerfil = $request->request->get('imagenPerfil');
         $datosPerfilGrabar   = array();
         $datosPerfilXML   = ""; 
         
         foreach($dataPerfil as $registroPerfil) {
            $datosPerfilGrabar[$registroPerfil['name']] = $registroPerfil['value'];
         }
         
         $datosPerfilGrabar["imagenPerfil"] = $imagenPerfil;
         
         $rutaImagenPerfil    = NULL;
         $datosPerfilXML_img  = NULL;
         $imagenGrabada       = NULL;
         
         $UgServices          = new UgServices;
         
         //Actualizacion de la foto del perfil - INICIO
         if($datosPerfilGrabar["imagenPerfil"]!=NULL) {
            $dataImgTemp         = substr($datosPerfilGrabar["imagenPerfil"], strpos($datosPerfilGrabar["imagenPerfil"], ",") + 1); //Quitar la cabecera
            $decodedDataImgTemp  = base64_decode($dataImgTemp);

            $rutaTempImagenPerfil   = "images/img_perfil/";
            $nombreTempImagen       = "imgPerfil_".$idUsuario.".png";
            
            if (file_exists($rutaTempImagenPerfil)) { //Compruebo que la ruta para subidas existe
               $fp = fopen($rutaTempImagenPerfil.$nombreTempImagen, 'wb');
               fwrite($fp, $decodedDataImgTemp);
               fclose($fp);
               
               $rutaImagenPerfil = $rutaTempImagenPerfil.$nombreTempImagen;
               $session->set("img_perfil",$rutaImagenPerfil);
               if (file_exists($rutaImagenPerfil)) {
                  $imagenGrabada = 1;
               } else {
                  $imagenGrabada = 0;
               }
               
               //$datosPerfilXML_img  = "<directorio_foto>".$rutaImagenPerfil."</directorio_foto>";
               if($imagenGrabada==1) {
                  //Proceso para grabar la foto del perfil
                  $datosPerfilXML_img  = "<item>";
                  $datosPerfilXML_img .= "<id_sg_usuario>".$idUsuario."</id_sg_usuario>";
                  $datosPerfilXML_img .= "<directorio_foto>".$rutaImagenPerfil."</directorio_foto>";
                  $datosPerfilXML_img .= "<id_sg_usuario_registro>".$idUsuario."</id_sg_usuario_registro>";
                  $datosPerfilXML_img .= "</item>";

                  $datosConsultaImg       = array('DatosImgPerfil' => $datosPerfilXML_img);
                  $datosImgArray   = $UgServices->Docentes_setDataPerfilUsuarioImgPerfilEditar($datosConsultaImg);
                  
                  if(isset($datosImgArray["pi_estado"])) {
                     $datosUsuarioRespuesta["imagenGrabada"]  = $datosImgArray["pi_estado"];
                     $datosUsuarioRespuesta["imagenRuta"]     = $rutaImagenPerfil;
                  }
                  else {
                     $datosUsuarioRespuesta["imagenGrabada"]  = -1;
                     $datosUsuarioRespuesta["imagenRuta"]     = NULL;
                  }
               }
            }
            else {
               $imagenGrabada = -1;
            }
         }
         //Actualizacion de la foto del perfil - FIN 
         
         //Actualizacion de los datos del perfil - INICIO
         if($datosPerfilGrabar["fecha_nacimiento"]!=NULL) {
            $datosPerfilGrabar["fecha_nacimiento"] = date('Y-m-d', strtotime(str_replace('/', '-', $datosPerfilGrabar["fecha_nacimiento"])));
         }
         
         $datosPerfilXML   .= "<item>";
         $datosPerfilXML   .= "<id_sg_usuario>".$idUsuario."</id_sg_usuario>";
         $datosPerfilXML   .= "<fecha_nacimiento>".$datosPerfilGrabar["fecha_nacimiento"]."</fecha_nacimiento>";
         $datosPerfilXML   .= "<id_sa_parametro_tipo_sangre>".$datosPerfilGrabar["tipo_sangre"]."</id_sa_parametro_tipo_sangre>";
         $datosPerfilXML   .= "<id_sa_parametro_sexo>".$datosPerfilGrabar["sexo"]."</id_sa_parametro_sexo>";
         $datosPerfilXML   .= "<id_sa_parametro_estado_civil>".$datosPerfilGrabar["estado_civil"]."</id_sa_parametro_estado_civil>";
         $datosPerfilXML   .= "<id_sa_parametro_nacionalidad>".$datosPerfilGrabar["nacionalidad"]."</id_sa_parametro_nacionalidad>";
         $datosPerfilXML   .= "<id_sa_parametro_pais>".$datosPerfilGrabar["pais"]."</id_sa_parametro_pais>";
         $datosPerfilXML   .= "<direccion>".$datosPerfilGrabar["direccion"]."</direccion>";
         $datosPerfilXML   .= "<telefono>".$datosPerfilGrabar["telefono"]."</telefono>";
         $datosPerfilXML   .= "<correo_personal>".$datosPerfilGrabar["correo_personal"]."</correo_personal>";
         $datosPerfilXML   .= "<correo_institucional>".$datosPerfilGrabar["correo_institucional"]."</correo_institucional>";
         $datosPerfilXML   .= "<estado>A</estado>";
         $datosPerfilXML   .= "<id_sg_usuario_registro>".$idUsuario."</id_sg_usuario_registro>";
         $datosPerfilXML   .= "</item>";
         
         
         $datosConsulta       = array( 'idUsuario' => $idUsuario,
                                       'DatosPerfil' => $datosPerfilXML,
                                       'rutaImgPerfil' => $rutaImagenPerfil);
         $datosUsuarioArray   = $UgServices->Docentes_setDataPerfilUsuarioEditar($datosConsulta);
         
         $datosUsuarioRespuesta["idMensaje"]      = $datosUsuarioArray["pi_estado"];
         $datosUsuarioRespuesta["infoMensaje"]    = $datosUsuarioArray["pv_mensaje"];
         //Actualizacion de los datos del perfil - FIN
         
         $jsonResponse  = json_encode($datosUsuarioRespuesta);
         
         $response = new Response($jsonResponse);
         $response->headers->set('Content-Type', 'application/json');
         
         return $response;
      } // grabarEditarPerfilActualizarAction()

      public function informacionAction(Request $request) {
         // $session    =$request->getSession();
         // $idUsuario  = $session->get('id_user');
         
         // $UgServices          = new UgServices;
         // $datosConsulta       = array( 'idUsuario' => $idUsuario);
         // $datosUsuarioArray   = $UgServices->Titulacion_getConsultaPerfilUsuario($datosConsulta);

         // $datosUsuario        = $datosUsuarioArray[0];
         // if($datosUsuario['directoriofoto']!=NULL) {
         //    $datosUsuario['existefotoperfil'] = 1;
         // }
         // else {
         //    $datosUsuario['existefotoperfil'] = 0;
         // }
        $session=$request->getSession();
         return $this->render('TitulacionSisAcademicoBundle:Home:informacion.html.twig');
      } // editarPerfilAction()
}