<?php

namespace Titulacion\SisAcademicoBundle\Controller;
//git
    use Symfony\Bundle\FrameworkBundle\Controller\Controller;
    use Symfony\Component\HttpFoundation\Response;
    use Symfony\Component\HttpFoundation\Request;
    use Symfony\Component\HttpFoundation\JsonResponse;
    use Titulacion\SisAcademicoBundle\Helper\UgServices;

class AdminController extends Controller
{
      var $v_error =false;
      var $v_html ="";
      var $v_msg  ="";

    public function calendario_carreraAction(Request $request){



        $UgServices   = new UgServices;
        $session=$request->getSession();
        $id_usuario = $session->get("id_user");
        // $id_usuario = 3;
        $id_rol     = $session->get("perfil");


        $rsEventos = $UgServices->getConsultaSoloEventos(1); #como parametros enviaremos siempre 1


        return $this->render('TitulacionSisAcademicoBundle:Admin:calendario_carrera.html.twig', array('data' => $rsEventos));
    }

    public function cambio_passwordAction(){
        return $this->render('TitulacionSisAcademicoBundle:Admin:cambio_password.html.twig', array());
    }

     public function cambio_password_intAction(Request $request){
      $session=$request->getSession();
       if ($session->has("id_user")) 
           {
        return $this->render('TitulacionSisAcademicoBundle:Admin:cambio_password_interno.html.twig', array());
                }else
           {
                $this->get('session')->getFlashBag()->add(
                                      'mensaje',
                                      'Los datos ingresados no son válidos'
                                  );
                    return $this->redirect($this->generateUrl('titulacion_sis_academico_homepage'));
            }
    }

    public function get_captcha_cambioAction(Request $request){

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
        $session->set("random_number_cambio",$string);

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

      }


    public function ingreso_nuevo_int_passAction(Request $request){
        #obtenemos los datos enviados por get

            $session=$request->getSession();
              if ($session->has("id_user")) 
           {
            $Email= $session->get('mail');
            $Nombre = $session->get('nom_usuario');



        #llamamos a la consulta del webservice
        $UgServices = new UgServices;


            $password     = $request->request->get('pass');
            $password1    = $request->request->get('pass1');
            $username       = $session->get('id_user');

            $UgServices   = new UgServices;
            $salt         = "µ≈α|⊥ε¢ʟ@δσ";
            $passwordEncr = password_hash($password, PASSWORD_BCRYPT, array("cost" => 14, "salt" => $salt));
            $passwordNuevoEncr = password_hash($password1, PASSWORD_BCRYPT, array("cost" => 14, "salt" => $salt));

            $dataMant = $UgServices->mantenimientoUsuario($username,$passwordEncr,'','',$passwordNuevoEncr,'B');

                if ( is_object($dataMant)) {
                    $estado = $dataMant ->PI_ESTADO;
                     $message = $dataMant ->PV_MENSAJE;
                }

                if ($estado == "1") {



                  $mailer    = $this->container->get('mailer');
                    $transport = \Swift_SmtpTransport::newInstance('smtp.gmail.com',465,'ssl')
                                ->setUsername('titulacion.php@gmail.com')
                                ->setPassword('sc123456');
                   //$mailer  = \Swift_Mailer($transport);
                    $message = \Swift_Message::newInstance('test')
                                ->setSubject("Contraseña Cambiada Correctamente")
                                ->setFrom('titulacion.php@gmail.com','Universidad de Guayaquil')
                                ->setTo($Email)
                                ->setBody("$Nombre usted ha Cambiado la Contraseña Exitosamente, Su nueva contraseña es $password1");
                    // ->setBody($this->renderView('TitulacionSisAcademicoBundle:Admin:Comtraseña.html.twig'),'text/html', 'utf8');
                    $this->get('mailer')->send($message);
                }

            

            $respuesta = array(
               "Codigo" => $estado ,
               "Mensaje" => $message,
              
            );

          return new Response(json_encode($respuesta));
        }else
           {
                $this->get('session')->getFlashBag()->add(
                                      'mensaje',
                                      'Los datos ingresados no son válidos'
                                  );
                    return $this->redirect($this->generateUrl('titulacion_sis_academico_homepage'));
            }

    }


    public function ingreso_nuevo_passAction(Request $request){
        #obtenemos los datos enviados por get

            $session=$request->getSession();
            $Email= $session->get('mail');
            $Nombre = $session->get('nom_usuario');
             $captchaEnv=$request->request->get('code');
            $captchaGene= $session->get('random_number_cambio') ;

            //var_dump($captchaGene, $captchaEnv);

            if( $captchaEnv != $captchaGene)
            {

               $respuesta = array(
                    "valError" => "1" 
                );

              return new Response(json_encode($respuesta));
            }


          #llamamos a la consulta del webservice
          $UgServices = new UgServices;


              $username     = $request->request->get('user');
              $password     = $request->request->get('pass');
              $password1    = $request->request->get('pass1');

              $UgServices   = new UgServices;
              $salt         = "µ≈α|⊥ε¢ʟ@δσ";
              $passwordEncr = password_hash($password, PASSWORD_BCRYPT, array("cost" => 14, "salt" => $salt));
              $passwordNuevoEncr = password_hash($password1, PASSWORD_BCRYPT, array("cost" => 14, "salt" => $salt));

              $dataMant = $UgServices->mantenimientoUsuario($username,$passwordEncr,'','',$passwordNuevoEncr,'A');

                  if ( is_object($dataMant)) {
                      $estado = $dataMant ->PI_ESTADO;
                       $message = $dataMant ->PV_MENSAJE;
                  }

                                    if ($estado == "1") {
                    $mailer    = $this->container->get('mailer');
                      $transport = \Swift_SmtpTransport::newInstance('smtp.gmail.com',465,'ssl')
                                  ->setUsername('titulacion.php@gmail.com')
                                  ->setPassword('sc123456');
                     //$mailer  = \Swift_Mailer($transport);
                      $message = \Swift_Message::newInstance('test')
                                  ->setSubject("Contraseña Cambiada Correctamente")
                                  ->setFrom('titulacion.php@gmail.com','Universidad de Guayaquil')
                                  ->setTo($Email)
                                  ->setBody("$Nombre usted ha Cambiado la Contraseña Exitosamente, Su nueva contraseña es $password1");
                      // ->setBody($this->renderView('TitulacionSisAcademicoBundle:Admin:Comtraseña.html.twig'),'text/html', 'utf8');
                      $this->get('mailer')->send($message);
                  }
            

            $respuesta = array(
               "Codigo" => $estado ,
               "Mensaje" => $message,
               "valError" => "0" 
            );

          return new Response(json_encode($respuesta));
    }

    public function cargar_eventosAction(Request $request)
    {
        $session=$request->getSession();


        $id_ciclo = $session->get("îdciclo_calendar");
        $id_usuario = $session->get("id_user");
        // echo '<pre>'; var_dump($id_ciclo); exit();


        #llamamos a la consulta del webservice
        $UgServices = new UgServices;
        $rsInsertEvent = $UgServices->cargarEventosCalendario($id_ciclo,$id_usuario);

        return new Response(json_encode($rsInsertEvent));
         // echo '<pre>'; var_dump($rsInsertEvent);

        // echo $rsInsertEvent;
    }

    public function cargar_eventos_carrera_userAction(Request $request)
    {

        #llamamos a la consulta del webservice
        $UgServices = new UgServices;

        return $this->render('TitulacionSisAcademicoBundle:Admin:calendario_academico_carrera_user.html.twig', array());
    }

     public function carrerasordenpagoAction(Request $request)
    {
        $session=$request->getSession();
        $perfilEst   = $this->container->getParameter('perfilEst');
        $perfilDoc   = $this->container->getParameter('perfilDoc');
        $perfilAdmin = $this->container->getParameter('perfilAdmin'); 
        $perfilEstDoc = $this->container->getParameter('perfilEstDoc'); 
        $perfilEstAdm = $this->container->getParameter('perfilEstAdm'); 
        $perfilDocAdm = $this->container->getParameter('perfilDocAdm');

        
           if ($session->has("perfil")) 
           {
                   if ($session->get('perfil') == $perfilEst || $session->get('perfil') == $perfilEstDoc || $session->get('perfil') == $perfilEstAdm || $session->get('perfil') ==$perfilAdmin || $session->get('perfil') ==$perfilDocAdm) 
                   {
                        #llamamos a la consulta del webservice
                        //$UgServices = new UgServices;
                    $Carreras=array();
                    $idUsuario="";

                        try
                    {
                          $lcFacultad="";
                          $lcCarrera="";
                          //$idEstudiante=3;
                          $idUsuario=$session->get("id_user");
                          //$idEstudiante=3;
                          $idRol=$perfilAdmin;
                          $idUsuario=9;
                          $idRol=1;
                          
                          //$idRol=$session->get("perfil");
                          $Carreras = array();
                          $UgServices = new UgServices;
                          $xml = $UgServices->getConsultaCarrerasorden($idUsuario,$idRol);
                            if ( is_object($xml))
                            {
                                  foreach($xml->registros->registro as $lcCarreras) 
                                  {
                                          $lcFacultad="";
                                          $lcCarrera=$lcCarreras->id_sa_carrera;
                                          $materiaObject = array( 'Nombre' => $lcCarreras->nombre,
                                                                     'Facultad'=>"",
                                                                     'Carrera'=>$lcCarreras->id_sa_carrera
                                                                    );
                                          array_push($Carreras, $materiaObject); 
                                  } 

                                  $bolCorrecto=1;
                                  $cuantos=count($Carreras);
                                  if ($cuantos==0)
                                  {
                                        $bolCorrecto=0;
                                  }
                                  
                            }
                            else
                            {

                              throw new \Exception('Un error');
                            }    
                     }
                     catch (\Exception $e)
                     {

                            $bolCorrecto=0;
                            $cuantos=0;

                     }    
                      return $this->render('TitulacionSisAcademicoBundle:Admin:carreras_ordenpago.html.twig',array(
                                                          'carreras' => $Carreras,
                                                          'bolcorrecto'=>$bolCorrecto
                                                       ));

            }
           else
               {
                  $this->get('session')->getFlashBag()->add(
                                'mensaje',
                                'Los datos ingresados no son válidos'
                            );
                    return $this->redirect($this->generateUrl('titulacion_sis_academico_homepage'));
               }
            }
           else
           {
                $this->get('session')->getFlashBag()->add(
                                      'mensaje',
                                      'Los datos ingresados no son válidos'
                                  );
                    return $this->redirect($this->generateUrl('titulacion_sis_academico_homepage'));
            }

    }


 public function listarordenpagoAction(Request $request)
    {
        $session=$request->getSession();
        $perfilEst   = $this->container->getParameter('perfilEst');
        $perfilDoc   = $this->container->getParameter('perfilDoc');
        $perfilAdmin = $this->container->getParameter('perfilAdmin'); 
        $perfilEstDoc = $this->container->getParameter('perfilEstDoc'); 
        $perfilEstAdm = $this->container->getParameter('perfilEstAdm'); 
        $perfilDocAdm = $this->container->getParameter('perfilDocAdm');

        
           if ($session->has("perfil")) 
           {
                   if ($session->get('perfil') == $perfilEst || $session->get('perfil') == $perfilEstDoc || $session->get('perfil') == $perfilEstAdm || $session->get('perfil') ==$perfilAdmin || $session->get('perfil') ==$perfilDocAdm) 
                   {
                    $Carreras=array();
                    $idUsuario="";

                        try
                    {
                          $lcFacultad="";
                          $lcCarrera="";
                          //$idEstudiante=3;
                          $idUsuario=$session->get("id_user");
                          $idEstudiante  = $request->request->get('idEstudiante');
                          $idCarrera  = $request->request->get('idCarrera');
                          $modoConsulta  = $request->request->get('criterio');
                          $idEstado  = $request->request->get('estado');
                          $idRol=$perfilAdmin;
                          $idCiclo="";
                          
                          if (trim($modoConsulta)=='cedula')
                          {
                                 $modoConsulta='CED';
                          }
                          else
                          {
                            $modoConsulta='ID';
                          }
                          $banderalegalizar=0;
                          if ($idEstado=='79')
                          {
                            $banderalegalizar=1;
                          }
                          
                          $arrOrdenes = array();
                          $UgServices = new UgServices;
                          $lcNombre="";
                          $lcCedula="";
                          $idUsuarioEst="";
                          $bolCorrecto=0;
                          $xml1 = $UgServices->getConsulta_listado_OrdenPago($idEstudiante,$idCarrera,$idCiclo,$modoConsulta,$idEstado);
                           
                            if ( is_object($xml1))
                            {
                                foreach($xml1->PX_SALIDA as $xml)
                                         {  
                                                    $lcNombre=$xml->dato->Estudiante->nombres;
                                                    $lcCedula=$xml->dato->Estudiante->usuario;
                                                    $idUsuarioEst=$xml->dato->Estudiante->id_sg_usuario;
                                                    $idEstudiante=$idUsuarioEst;
                                                    
                                                      foreach($xml->dato->OrdenPagos as $lsciclo) 
                                                        {

                                                              foreach($lsciclo->OrdenPago as $lsOrdenes) 
                                                            {
                                                                    
                                                                      $lcFacultad="";
                                                                      $lcCarrera="";
                                                                      $materiaObject = array( 'Orden' => $lsOrdenes->numero_orden,
                                                                                                 'Fecha_pago'=> substr($lsOrdenes->fecha_limite_pago,0,10),
                                                                                                 'Valor'=>$lsOrdenes->valor_total,
                                                                                                 'ciclo'=>$lsOrdenes->id_sa_ciclo_detalle,
                                                                                                 'nomciclo'=>$lsOrdenes->anio." - ".$lsOrdenes->ciclo
                                                                                                );
                                                                      array_push($arrOrdenes, $materiaObject); 


                                                                      $bolCorrecto=1;
                                                                      $cuantos=count($Carreras);
                                                                      if ($cuantos==0)
                                                                      {
                                                                            $bolCorrecto=0;
                                                                      }
                                                              }

                                                        }
                                                
                                               
                                        }
                            }
                            else
                            {

                              throw new \Exception('Un error');
                            }    
                     }
                     catch (\Exception $e)
                     {

                            $bolCorrecto=0;
                            $cuantos=0;

                     }    

                      return $this->render('TitulacionSisAcademicoBundle:Admin:admin_legal_ordenpago.html.twig',array(
                                                          'pendientes' => $arrOrdenes,
                                                          'bolcorrecto'=>$bolCorrecto,
                                                          'estudiante'=>$lcNombre,
                                                          'identificacion'=>$lcCedula,
                                                          'idCarrera'=>$idCarrera,
                                                          'idEstudiante'=>$idEstudiante,
                                                          'banderalegalizar'=>$banderalegalizar
                                                       ));






                    }
                   else
                       {
                          $this->get('session')->getFlashBag()->add(
                                        'mensaje',
                                        'Los datos ingresados no son válidos'
                                    );
                            return $this->redirect($this->generateUrl('titulacion_sis_academico_homepage'));
                       }
            }
           else
           {
                $this->get('session')->getFlashBag()->add(
                                      'mensaje',
                                      'Los datos ingresados no son válidos'
                                  );
                    return $this->redirect($this->generateUrl('titulacion_sis_academico_homepage'));
            }



       // return $this->render('TitulacionSisAcademicoBundle:Admin:calendario_academico_carrera_user.html.twig', array());
    }

     public function registroordenpagoAction(Request $request)
    {
        $session=$request->getSession();
        $perfilEst   = $this->container->getParameter('perfilEst');
        $perfilDoc   = $this->container->getParameter('perfilDoc');
        $perfilAdmin = $this->container->getParameter('perfilAdmin'); 
        $perfilEstDoc = $this->container->getParameter('perfilEstDoc'); 
        $perfilEstAdm = $this->container->getParameter('perfilEstAdm'); 
        $perfilDocAdm = $this->container->getParameter('perfilDocAdm');
        if ($session->has("perfil")) 
           {
                   if ($session->get('perfil') == $perfilEst || $session->get('perfil') == $perfilEstDoc || $session->get('perfil') == $perfilEstAdm || $session->get('perfil') ==$perfilAdmin || $session->get('perfil') ==$perfilDocAdm) 
                   {

                        #llamamos a la consulta del webservice
                        $idEstudiante  = $request->request->get('idEstudiante');
                        $idCarrera  = $request->request->get('idCarrera');

                        return $this->render('TitulacionSisAcademicoBundle:Admin:admin_buscar_orden.html.twig', array('idCarrera'=>$idCarrera));

                     }
                   else
                       {
                          $this->get('session')->getFlashBag()->add(
                                        'mensaje',
                                        'Los datos ingresados no son válidos'
                                    );
                            return $this->redirect($this->generateUrl('titulacion_sis_academico_homepage'));
                       }
            }
           else
           {
                $this->get('session')->getFlashBag()->add(
                                      'mensaje',
                                      'Los datos ingresados no son válidos'
                                  );
                    return $this->redirect($this->generateUrl('titulacion_sis_academico_homepage'));
            }

    }

     public function actualizaordenpagoAction(Request $request)
    {
        $session=$request->getSession();
        $perfilEst   = $this->container->getParameter('perfilEst');
        $perfilDoc   = $this->container->getParameter('perfilDoc');
        $perfilAdmin = $this->container->getParameter('perfilAdmin'); 
        $perfilEstDoc = $this->container->getParameter('perfilEstDoc'); 
        $perfilEstAdm = $this->container->getParameter('perfilEstAdm'); 
        $perfilDocAdm = $this->container->getParameter('perfilDocAdm');
        if ($session->has("perfil")) 
           {
                   if ($session->get('perfil') == $perfilEst || $session->get('perfil') == $perfilEstDoc || $session->get('perfil') == $perfilEstAdm || $session->get('perfil') ==$perfilAdmin || $session->get('perfil') ==$perfilDocAdm) 
                   {  
                                $respuesta= new Response("",200);
                                $arrDetalle  = $request->request->get('arrDetalle');
                                $idUser=$session->get('id_user');
                                    
                                  $datosCuenta="<PX_XML><items> "; 
                                 foreach ($arrDetalle as $key => $value) {
                                      $datosCuenta.= "<item>
                                                            <id_sg_usuario>" . $value['idEstudiante'] . "</id_sg_usuario>
                                                            <id_sa_ciclo_detalle>" . $value['Ciclo'] . "</id_sa_ciclo_detalle>
                                                            <id_orden_pago>" . $value['Orden'] . "</id_orden_pago>
                                                            <id_sg_usuario_modifica>" .$idUser."</id_sg_usuario_modifica>
                                                     </item>"; 
                                  }
                                  $datosCuenta.="</items></PX_XML><pc_opcion>2</pc_opcion>"; 

                                   $UgServices = new UgServices;

                                    $xml = $UgServices->setActualizaOrden($datosCuenta);

                                    $Estado=0;
                                    $Mensaje="";
                                     if ( is_object($xml))
                                        {
                                            foreach($xml->parametrosSalida as $datos)
                                             {  
                                                $Estado=(int) $datos->PI_ESTADO;
                                                $Mensaje=(string) $datos->PV_MENSAJE;
                                                
                                             }
                                            
                                        }


                                    $arrayProceso=array();
                                    $arrayProceso['codigo_error']=$Estado;
                                    $arrayProceso['mensaje']=$Mensaje;
                                    $jarray=json_encode($arrayProceso);
                                    
                                    $respuesta->setContent($jarray);
                                    return $respuesta;
                    }
                   else
                       {
                          $this->get('session')->getFlashBag()->add(
                                        'mensaje',
                                        'Los datos ingresados no son válidos'
                                    );
                            return $this->redirect($this->generateUrl('titulacion_sis_academico_homepage'));
                       }
            }
           else
           {
                $this->get('session')->getFlashBag()->add(
                                      'mensaje',
                                      'Los datos ingresados no son válidos'
                                  );
                    return $this->redirect($this->generateUrl('titulacion_sis_academico_homepage'));
            }
        }




    /**
     * [Action que permite crear un vento del calendario academico]
     * @param  Request $request [description]
     */
    public function crear_eventos_academicosAction(Request $request){
        #llamamos a la consulta del webservice
        $UgServices = new UgServices;
        $evento    = $request->request->get('evento');
        $rsInsertEvent = $UgServices->crearEventos($evento);

        return new Response($rsInsertEvent);
        // echo '<pre>'; var_dump($rsInsertEvent); exit();
    }#end function

    public function insertar_eventos_calendarioAction(Request $request){
        $session=$request->getSession();

        $id_ciclo = $session->get("îdciclo_calendar");
        $UgServices   = new UgServices;
        $id_evento    = $request->request->get('id_evento');
        $fec_desde    = $request->request->get('start');
        $fec_hasta    = $request->request->get('end');
        // $date = date_format($fec_desde, 'Y-m-d H:i:s');
        $session=$request->getSession();
        $id_usuario = $session->get("id_user");
        $id_usuario = 11;
        $rsInsertEvent = $UgServices->insertarEventosCalendario($id_evento,$id_ciclo,$fec_desde,$fec_hasta,$id_usuario);
        // echo '<pre>'; var_dump($rsInsertEvent); exit();
        return new Response($rsInsertEvent);
    }#end function

//---------------------------------------------------------------------------------------------------------------------------------------//
//---------------------------------------------------------------------------------------------------------------------------------------//
//---------------------------------------------------------------------------------------------------------------------------------------//


  public function consulta_estudiantes_carreraAction(Request $request){

    $session=$request->getSession();
    $perfilEst   = $this->container->getParameter('perfilEst');
    $perfilDoc   = $this->container->getParameter('perfilDoc');
    $perfilAdmin = $this->container->getParameter('perfilAdmin'); 
    $perfilEstDoc = $this->container->getParameter('perfilEstDoc'); 
    $perfilEstAdm = $this->container->getParameter('perfilEstAdm'); 
    $perfilDocAdm = $this->container->getParameter('perfilDocAdm');

    $datos = array();

    if ($session->has("perfil")) {
      if ($session->get('perfil') == $perfilAdmin ||$session->get('perfil') == $perfilDoc || $session->get('perfil') == $perfilEstAdm || $session->get('perfil') == $perfilDocAdm){
        try{        
          $lcCarrera="";
          $idAdministrador=$session->get("id_user");
          $idRol=$perfilAdmin;
          $ciclo = "";
          $nombreCarrera = "";
          $idCarrera = "";
          $tienePermiso = "0";
          $lsEstudiantes = array();
          $lsOpciones = array();
          $Carreras = array();
          $UgServices = new UgServices;
          $xml = $UgServices->getConsultaCarreras($idAdministrador,$idRol);

          
          if ( is_object($xml)){

            foreach($xml->registros->registro as $lcCarreras){
              $idCarrera=$lcCarreras->id_sa_carrera;
              $nombreCarrera = $lcCarreras->nombre;
              $ciclo = $lcCarreras->id_sa_ciclo_detalle;
              $materiaObject = array( 
                'nombre' => $nombreCarrera,
                'idCarrera'=> $idCarrera,
                'idCiclo'=> $ciclo,
                );
              array_push($Carreras, $materiaObject);
            }
            $cuantos=count($Carreras);
            
            if ($cuantos!=0){
              $tienePermiso="1";
            }
            
            if ($tienePermiso == "1"){
              $lcValor = "";
              $lcDescripcion= "";
              $xmlEstadosMatricula = $UgServices->getEstadosMatricula();
              
              if ($xmlEstadosMatricula){
                foreach ($xmlEstadosMatricula as $lcOpciones) {
                  $lcValor=$lcOpciones['codigo'];
                  $lcDescripcion=$lcOpciones['nombre'];
                  $Opcion=array(
                    'nombre'=> $lcDescripcion,
                    'codigo'=> $lcValor,
                    );
                  array_push($lsOpciones, $Opcion);
                }
              }
              $datos =  array(
                'carreras' =>  $Carreras,
                'parametroCombos' => $lsOpciones,
                'mostrar' =>  'SI'
                );
              return $this->render('TitulacionSisAcademicoBundle:Admin:consultaEstudiantesXCarrera.html.twig', array('estudianteCarrera' => $datos));
            }
          }
        }
        catch (\Exception $e){}
      }
      else{
        $this->get('session')->getFlashBag()->add(
          'mensaje','Los datos ingresados no son válidos'
          );
        return $this->redirect($this->generateUrl('titulacion_sis_academico_homepage'));
      }
    }
    else{
      $this->get('session')->getFlashBag()->add(
        'mensaje','Los datos ingresados no son válidos'
        );
      return $this->redirect($this->generateUrl('titulacion_sis_academico_homepage'));
    }
    return $this->render('TitulacionSisAcademicoBundle:Admin:consultaEstudiantesXCarrera.html.twig', array('estudianteCarrera' => $datos  = array('mostrar' =>  'NO' )));
  }
    
    
    //INSCRIPCION 
    public function inscripcionAction(Request $request)
    {
        $session=$request->getSession();
        $perfilEst   = $this->container->getParameter('perfilEst');
        $perfilDoc   = $this->container->getParameter('perfilDoc');
        $perfilAdmin = $this->container->getParameter('perfilAdmin'); 
        $perfilEstDoc = $this->container->getParameter('perfilEstDoc'); 
        $perfilEstAdm = $this->container->getParameter('perfilEstAdm'); 
        $perfilDocAdm = $this->container->getParameter('perfilDocAdm');

         if ($session->has("perfil")) 
         {
                if ($session->get('perfil') == $perfilAdmin || $session->get('perfil') == $perfilDocAdm || $session->get('perfil') == $perfilEstAdm) 
                {
                    try
                    {
                          $lcFacultad="";
                          $lcCarrera="";
                          $idUsuario="";
                          $idRol="";
                          $idUsuario=$session->get("id_user");
                          $idRol=$session->get("perfil");
                          if(strlen($idRol)>1)
                          {
                            $idRol = mb_substr($idRol,0,1);
                          }
                          else
                          {
                          $idRol = $idRol;
                          }
                          //$idUsuario=9; //USUARIO DE SESION ADMIN CAMBIAR
                          //$idRol=1; //ROL DE SESION ADMIN CAMBIAR
                          
                          $Carreras = array();
                          $UgServices = new UgServices;
                          $xml = $UgServices->getConsultaCarrerasInscripcion($idUsuario,$idRol);
                            if ( is_object($xml))
                            {
                                  foreach($xml->registros->registro as $lcCarreras) 
                                  {
                                          $lcFacultad="";
                                          $lcCarrera=$lcCarreras->id_sa_carrera;
                                          $materiaObject = array( 'Nombre' => $lcCarreras->nombre,
                                                                     'Facultad'=>$lcCarreras->id_sa_facultad,
                                                                     'Carrera'=>$lcCarreras->id_sa_carrera,
                                                                     'Ciclo'=>$lcCarreras->id_sa_ciclo_detalle
                                                                    );
                                          array_push($Carreras, $materiaObject); 
                                  } 
    
                                  $bolCorrecto=1;
                                  $cuantos=count($Carreras);
                                  if ($cuantos==0)
                                  {
                                        $bolCorrecto=0;
                                  }    
                            }
                            else
                            {
    
                              throw new \Exception('Un error');
                            }    
                 }
                 catch (\Exception $e)
                 {
    
                        $bolCorrecto=0;
                        $cuantos=0;
    
                 }    
                 return $this->render('TitulacionSisAcademicoBundle:Admin:carreras_inscripcion.html.twig',array(
                                                  'carreras' => $Carreras,
                                                  'bolcorrecto'=>$bolCorrecto
                                               ));
            }
            else
            {
                   $this->get('session')->getFlashBag()->add(
                                 'mensaje',
                                 'Los datos ingresados no son válidos'
                             );
                     return $this->redirect($this->generateUrl('titulacion_sis_academico_homepage'));
            }
    }
    else
    {
         $this->get('session')->getFlashBag()->add(
                              'mensaje',
                               'Los datos ingresados no son válidos'
                           );
             return $this->redirect($this->generateUrl('titulacion_sis_academico_homepage'));
     }
    }#termina funcion
    
     public function inscripcion_datosAction(Request $request)
    {
        $idCarrera  = $request->request->get('idCarrera');
        $idCiclo  = $request->request->get('idCiclo');

        return $this->render('TitulacionSisAcademicoBundle:Admin:admin_buscar_inscripcion.html.twig', array('idCarrera'=>$idCarrera,'idCiclo'=>$idCiclo));
    }
    
    public function inscripcion_listarAction(Request $request)
    {
        $session=$request->getSession();
        $perfilEst   = $this->container->getParameter('perfilEst');
        $perfilDoc   = $this->container->getParameter('perfilDoc');
        $perfilAdmin = $this->container->getParameter('perfilAdmin'); 
        $perfilEstDoc = $this->container->getParameter('perfilEstDoc'); 
        $perfilEstAdm = $this->container->getParameter('perfilEstAdm'); 
        $perfilDocAdm = $this->container->getParameter('perfilDocAdm');

          if ($session->has("perfil")) 
         {
                if ($session->get('perfil') == $perfilAdmin || $session->get('perfil') == $perfilDocAdm || $session->get('perfil') == $perfilEstAdm) 
                {
                        try
                        {
                              $lcFacultad="";
                              $lcCarrera="";
                              $idUsuario="";
                              $idRol="";
                              $idUsuario=$session->get("id_user");
                              $idRol=$session->get("perfil");
                              if(strlen($idRol)>1)
                              {
                                $idRol = mb_substr($idRol,0,1);
                              }
                              else
                              {
                              $idRol = $idRol;
                              }
                              $idEstudiante  = $request->request->get('idEstudiante');
                              $idCarrera  = $request->request->get('idCarrera');
                              $idCiclo=$request->request->get('idCiclo');
                              //$modoConsulta  = $request->request->get('criterio');
                              
                              $arrInscripcion = array();
                              $UgServices = new UgServices;
                              $lcNombre="";
                              $lcApellidos="";
                              $lcCedula="";
                              $idUsuarioEst="";
                              $bolCorrecto=0;
                              $cuantos=0;
                              $xml1 = $UgServices->getConsulta_listado_inscripcion($idEstudiante,$idCarrera,$idCiclo);
                              
                                if ( is_object($xml1))
                                {
                                    foreach($xml1->PX_SALIDA as $xml)
                                    {  
                                                        $lcNombre=$xml->registros->registro->estudiante->nombres;
                                                        $lcApellidos=$xml->registros->registro->estudiante->apellidos;
                                                        $lcCedula=$xml->registros->registro->estudiante->cedula;
                                                        $lcEstadoAlumno=$xml->registros->registro->estudiante->estado_alumno;
                                                        $idUsuarioEst=$xml->registros->registro->estudiante->id_sg_usuario;
                                                        
                                                          foreach($xml->registros->registro->materias as $lsmaterias) 
                                                          {
    
                                                                foreach($lsmaterias->materia as $lsmateria) 
                                                                {
                                                                          $lcFacultad="";
                                                                          $lcCarrera="";
                                                                          $materiaObject = array( 'id_sa_materia' => $lsmateria->id_sa_materia,
                                                                                                     'nombre'=> $lsmateria->nombre,
                                                                                                     'nivel'=>$lsmateria->nivel,
                                                                                                     'curso'=>$lsmateria->curso,
                                                                                                     'veces'=>$lsmateria->veces
                                                                                                    );
                                                                          array_push($arrInscripcion, $materiaObject);
                                                                  }
                                                            }  
                                    }
                                }
                                else
                                {
                                  throw new \Exception('Un error');
                                }    
                     }
                     catch (\Exception $e)
                     {

                            $bolCorrecto=0;
                            $cuantos=0;

                     }    
                      return $this->render('TitulacionSisAcademicoBundle:Admin:admin_listado_inscripcion.html.twig',array(
                                                          'inscripcion' => $arrInscripcion,
                                                          'idUsuarioEst' => $idUsuarioEst,
                                                          'estudiante'=>$lcNombre,
                                                          'estudiante_ap'=>$lcApellidos,
                                                          'identificacion'=>$lcCedula,
                                                          'EstadoAlumno'=>$lcEstadoAlumno,
                                                          'idCarrera'=>$idCarrera,
                                                          'idCiclo'=>$idCiclo
                                                       ));
           }
                    else
                    {
                           $this->get('session')->getFlashBag()->add(
                                         'mensaje',
                                         'Los datos ingresados no son válidos'
                                     );
                             return $this->redirect($this->generateUrl('titulacion_sis_academico_homepage'));
                    }
            }
            else
            {
                 $this->get('session')->getFlashBag()->add(
                                      'mensaje',
                                       'Los datos ingresados no son válidos'
                                   );
                     return $this->redirect($this->generateUrl('titulacion_sis_academico_homepage'));
             }
        }
        
        public function inscripcion_registrarAction(Request $request)
        {
            $session=$request->getSession();    
            $respuesta= new Response("",200);
            $idEstudiante  = $request->request->get('idUsuarioEst');
            $idCiclo  = $request->request->get('idCiclo');
            //$idUser=$session->get('id_user'); USUARIO LOGONEADO  <id_sg_usuario_modifica>" .$idUser."</id_sg_usuario_modifica>
                
            $datosCuenta="<PX_XML><items> "; 
            $datosCuenta.= "<item>
                                        <id_sg_usuario>".$idEstudiante."</id_sg_usuario>
                                        <id_sa_ciclo_detalle>".$idCiclo."</id_sa_ciclo_detalle>
                            </item>"; 
            $datosCuenta.="</items></PX_XML><pc_opcion>1</pc_opcion>"; 
              
            $UgServices = new UgServices;
            $xml = $UgServices->setActualizaInscripcion($datosCuenta);

            
            $Estado=0;
            $Mensaje="";
            if ( is_object($xml))
             {
                    foreach($xml->parametrosSalida as $datos)
                     {  
                        $Estado=(int) $datos->PI_ESTADO;
                        $Mensaje=(string) $datos->PV_MENSAJE;
                     }
             }
    
    
                $arrayProceso=array();
                $arrayProceso['codigo_error']=$Estado;
                $arrayProceso['mensaje']=$Mensaje;
                $jarray=json_encode($arrayProceso);
                
                $respuesta->setContent($jarray);
                return $respuesta;
        }
        
        //ANULACION
          public function anulacionAction(Request $request)
    {
        $session=$request->getSession();
        $perfilEst   = $this->container->getParameter('perfilEst');
        $perfilDoc   = $this->container->getParameter('perfilDoc');
        $perfilAdmin = $this->container->getParameter('perfilAdmin'); 
        $perfilEstDoc = $this->container->getParameter('perfilEstDoc'); 
        $perfilEstAdm = $this->container->getParameter('perfilEstAdm'); 
        $perfilDocAdm = $this->container->getParameter('perfilDocAdm');

         if ($session->has("perfil")) 
         {
                if ($session->get('perfil') == $perfilAdmin || $session->get('perfil') == $perfilDocAdm || $session->get('perfil') == $perfilEstAdm) 
                {
                    try
                    {
                          $lcFacultad="";
                          $lcCarrera="";
                          $idUsuario="";
                          $idRol="";
                          $idUsuario=$session->get("id_user");
                          $idRol=$session->get("perfil");
                          if(strlen($idRol)>1)
                          {
                            $idRol = mb_substr($idRol,0,1);
                          }
                          else
                          {
                          $idRol = $idRol;
                          }
                          //$idUsuario=9; //USUARIO DE SESION ADMIN CAMBIAR
                          //$idRol=1; //ROL DE SESION ADMIN CAMBIAR
                          
                          $Carreras = array();
                          $UgServices = new UgServices;
                          $xml = $UgServices->getConsultaCarrerasAnulacion($idUsuario,$idRol);
                            if ( is_object($xml))
                            {
                                  foreach($xml->registros->registro as $lcCarreras) 
                                  {
                                          $lcFacultad="";
                                          $lcCarrera=$lcCarreras->id_sa_carrera;
                                          $materiaObject = array( 'Nombre' => $lcCarreras->nombre,
                                                                     'Facultad'=>$lcCarreras->id_sa_facultad,
                                                                     'Carrera'=>$lcCarreras->id_sa_carrera,
                                                                     'Ciclo'=>$lcCarreras->id_sa_ciclo_detalle
                                                                    );
                                          array_push($Carreras, $materiaObject); 
                                  } 
    
                                  $bolCorrecto=1;
                                  $cuantos=count($Carreras);
                                  if ($cuantos==0)
                                  {
                                        $bolCorrecto=0;
                                  }    
                            }
                            else
                            {
    
                              throw new \Exception('Un error');
                            }    
                 }
                 catch (\Exception $e)
                 {
    
                        $bolCorrecto=0;
                        $cuantos=0;
    
                 }    
                 return $this->render('TitulacionSisAcademicoBundle:Admin:carreras_anulacion.html.twig',array(
                                                  'carreras' => $Carreras,
                                                  'bolcorrecto'=>$bolCorrecto
                                               ));
            }
            else
            {
                   $this->get('session')->getFlashBag()->add(
                                 'mensaje',
                                 'Los datos ingresados no son válidos'
                             );
                     return $this->redirect($this->generateUrl('titulacion_sis_academico_homepage'));
            }
    }
    else
    {
         $this->get('session')->getFlashBag()->add(
                              'mensaje',
                               'Los datos ingresados no son válidos'
                           );
             return $this->redirect($this->generateUrl('titulacion_sis_academico_homepage'));
     }
    }#termina funcion
    
    
    public function estudiantesxDocentesAction(Request $request)
    {
        $session=$request->getSession();
        $perfilEst   = $this->container->getParameter('perfilEst');
        $perfilDoc   = $this->container->getParameter('perfilDoc');
        $perfilAdmin = $this->container->getParameter('perfilAdmin'); 
        $perfilEstDoc = $this->container->getParameter('perfilEstDoc'); 
        $perfilEstAdm = $this->container->getParameter('perfilEstAdm'); 
        $perfilDocAdm = $this->container->getParameter('perfilDocAdm');

         if ($session->has("perfil")) 
         {
                if ($session->get('perfil') == $perfilAdmin || $session->get('perfil') == $perfilDocAdm || $session->get('perfil') == $perfilEstAdm) 
                {
                    try
                    {
                          $lcFacultad="";
                          $lcCarrera="";
                          $idUsuario="";
                          $idRol="";
                          $idUsuario=$session->get("id_user");
                          $idRol=$session->get("perfil");
                          if(strlen($idRol)>1)
                          {
                            $idRol = mb_substr($idRol,0,1);
                          }
                          else
                          {
                          $idRol = $idRol;
                          }
                          //$idUsuario=9; //USUARIO DE SESION ADMIN CAMBIAR
                          //$idRol=1; //ROL DE SESION ADMIN CAMBIAR
                          
                          $Carreras = array();
                          $UgServices = new UgServices;
                          $xml = $UgServices->getConsultaCarrerasAnulacion($idUsuario,$idRol);
                            if ( is_object($xml))
                            {
                                  foreach($xml->registros->registro as $lcCarreras) 
                                  {
                                          $lcFacultad="";
                                          $lcCarrera=$lcCarreras->id_sa_carrera;
                                          $materiaObject = array( 'Nombre' => $lcCarreras->nombre,
                                                                     'Facultad'=>$lcCarreras->id_sa_facultad,
                                                                     'Carrera'=>$lcCarreras->id_sa_carrera,
                                                                     'Ciclo'=>$lcCarreras->id_sa_ciclo_detalle
                                                                    );
                                          array_push($Carreras, $materiaObject); 
                                  } 
    
                                  $bolCorrecto=1;
                                  $cuantos=count($Carreras);
                                  if ($cuantos==0)
                                  {
                                        $bolCorrecto=0;
                                  }    
                            }
                            else
                            {
    
                              throw new \Exception('Un error');
                            }    
                 }
                 catch (\Exception $e)
                 {
    
                        $bolCorrecto=0;
                        $cuantos=0;
    
                 }    
                 return $this->render('TitulacionSisAcademicoBundle:Admin:EstudiantesxDocentes.html.twig',array(
                                                  'carreras' => $Carreras,
                                                  'bolcorrecto'=>$bolCorrecto
                                               ));
            }
            else
            {
                   $this->get('session')->getFlashBag()->add(
                                 'mensaje',
                                 'Los datos ingresados no son válidos'
                             );
                     return $this->redirect($this->generateUrl('titulacion_sis_academico_homepage'));
            }
    }
    else
    {
         $this->get('session')->getFlashBag()->add(
                              'mensaje',
                               'Los datos ingresados no son válidos'
                           );
             return $this->redirect($this->generateUrl('titulacion_sis_academico_homepage'));
     }
    }#termina funcion
    
    
      public function anulacion_datosAction(Request $request)
    {
        $idCarrera  = $request->request->get('idCarrera');
        $idCiclo  = $request->request->get('idCiclo');

        return $this->render('TitulacionSisAcademicoBundle:Admin:admin_buscar_anulacion.html.twig', array('idCarrera'=>$idCarrera,'idCiclo'=>$idCiclo));
    }
     public function anulacion_listarAction(Request $request)
    {
        $session=$request->getSession();
        $perfilEst   = $this->container->getParameter('perfilEst');
        $perfilDoc   = $this->container->getParameter('perfilDoc');
        $perfilAdmin = $this->container->getParameter('perfilAdmin'); 
        $perfilEstDoc = $this->container->getParameter('perfilEstDoc'); 
        $perfilEstAdm = $this->container->getParameter('perfilEstAdm'); 
        $perfilDocAdm = $this->container->getParameter('perfilDocAdm');

          if ($session->has("perfil")) 
         {
                if ($session->get('perfil') == $perfilAdmin || $session->get('perfil') == $perfilDocAdm || $session->get('perfil') == $perfilEstAdm) 
                { 
                        try
                        {
                              $lcFacultad="";
                              $lcCarrera="";
                              $idUsuario="";
                              $idRol="";
                              $idUsuario=$session->get("id_user");
                              $idRol=$session->get("perfil");
                              if(strlen($idRol)>1)
                              {
                                $idRol = mb_substr($idRol,0,1);
                              }
                              else
                              {
                              $idRol = $idRol;
                              }
                              //$idUsuario=9; //USUARIO DE SESION ADMIN CAMBIAR
                              //$idRol=1; //ROL DE SESION ADMIN CAMBIAR
                              $idEstudiante  = $request->request->get('idEstudiante');
                              $idCarrera  = $request->request->get('idCarrera');
                              $idCiclo=$request->request->get('idCiclo');
                              $mes  = $request->request->get('criterio_mes');
                              $anio  = $request->request->get('criterio_anio');
                              $mes_h  = $request->request->get('criterio_mes_h');
                              $anio_h  = $request->request->get('criterio_anio_h');
                              $fechaInicio="01-".$mes."-".$anio;
                              $fechaFin="31-".$mes_h."-".$anio_h;
                              $tipo_solicitud=82; //solocitudes de anulaciones
                              
                              $arrInscripcion = array();
                              $UgServices = new UgServices;
                              $bolCorrecto=0;
                              $cuantos=0;
                              $xml1 = $UgServices->getConsulta_listado_anulacion($fechaInicio,$fechaFin,$idCarrera,$tipo_solicitud);
                              
                                if ( is_object($xml1))
                                {
                                        foreach($xml1->registros as $xml)
                                        {  
                                                              foreach($xml->registro as $lsregistros) 
                                                              {
                                                                              
                          
                                                                              $materiaObject = array( 'id_sa_solicitud' => $lsregistros->id_sa_solicitud,
                                                                                                         'id_tipo_solicitud'=> $lsregistros->id_tipo_solicitud,
                                                                                                         'tipo'=>$lsregistros->tipo,
                                                                                                         'solicitante'=>$lsregistros->solicitante,
                                                                                                         'estado'=>$lsregistros->estado,
                                                                                                         'id_estado'=>$lsregistros->id_estado
                                                                                                        );
                                                                              array_push($arrInscripcion, $materiaObject); 
                                                                }  
                                    }
                                    
                                }
                                else
                                {
                                  throw new \Exception('Un error');
                                }    
                     }
                     catch (\Exception $e)
                     {

                            $bolCorrecto=0;
                            $cuantos=0;

                     }    
                      return $this->render('TitulacionSisAcademicoBundle:Admin:admin_listado_anulacion.html.twig',array(
                                                          'inscripcion' => $arrInscripcion,
                                                          'idCarrera'=>$idCarrera,
                                                          'idCiclo'=>$idCiclo
                                                       ));
           }
                    else
                    {
                           $this->get('session')->getFlashBag()->add(
                                         'mensaje',
                                         'Los datos ingresados no son válidos'
                                     );
                             return $this->redirect($this->generateUrl('titulacion_sis_academico_homepage'));
                    }
            }
            else
            {
                 $this->get('session')->getFlashBag()->add(
                                      'mensaje',
                                       'Los datos ingresados no son válidos'
                                   );
                     return $this->redirect($this->generateUrl('titulacion_sis_academico_homepage'));
             }
        }
         public function anulacion_detalleAction(Request $request)
    {
        $session=$request->getSession();
        $perfilEst   = $this->container->getParameter('perfilEst');
        $perfilDoc   = $this->container->getParameter('perfilDoc');
        $perfilAdmin = $this->container->getParameter('perfilAdmin'); 
        $perfilEstDoc = $this->container->getParameter('perfilEstDoc'); 
        $perfilEstAdm = $this->container->getParameter('perfilEstAdm'); 
        $perfilDocAdm = $this->container->getParameter('perfilDocAdm');

          if ($session->has("perfil")) 
         {
                if ($session->get('perfil') == $perfilAdmin || $session->get('perfil') == $perfilDocAdm || $session->get('perfil') == $perfilEstAdm) 
                {
                        try
                        {
                              $lcFacultad="";
                              $lcCarrera="";
                              $idUsuario="";
                              $idRol="";
                              $idUsuario=$session->get("id_user");
                              $idRol=$session->get("perfil");
                              if(strlen($idRol)>1)
                              {
                                $idRol = mb_substr($idRol,0,1);
                              }
                              else
                              {
                              $idRol = $idRol;
                              }
                              //$idUsuario=9; //USUARIO DE SESION ADMIN CAMBIAR
                              //$idRol=1; //ROL DE SESION ADMIN CAMBIAR
                              //$idEstudiante  = $request->request->get('idEstudiante');
                              $NombreEstudiante  = $request->request->get('nombreEst');
                              $idCarrera  = $request->request->get('idCarrera');
                              $idCiclo=$request->request->get('idCiclo');
                              $id_sa_solicitud=$request->request->get('id_sa_solicitud');
                              $id_tipo_solicitud=$request->request->get('id_tipo_solicitud');
                              
                              $arrInscripcion = array();
                              $UgServices = new UgServices;
                              $bolCorrecto=0;
                              $cuantos=0;
                              $xml1 = $UgServices->getConsulta_listado_anulacion_detalle($id_sa_solicitud,$id_tipo_solicitud);
                              
                                if ( is_object($xml1))
                                {
                                        foreach($xml1->px_Salida as $xml)
                                        {  
                                                              
                                                              foreach($xml->detalle as $lsregistros) 
                                                              {
                              
                          
                                                                              $materiaObject = array( 'id_solicitud_detalle' => $lsregistros->id_solicitud_detalle,
                                                                                                         'identificador'=> $lsregistros->identificador,
                                                                                                         'aprobada'=>$lsregistros->aprobada,
                                                                                                         'descripcion'=>$lsregistros->descripcion
                                                                                                        );
                                                                              array_push($arrInscripcion, $materiaObject); 
                                                                                                                       echo "<pre>";
                                 
                                                                }  
                                                                
                                    }
                                    
                                }
                                else
                                {
                                  throw new \Exception('Un error');
                                }    
                     }
                     catch (\Exception $e)
                     {

                            $bolCorrecto=0;
                            $cuantos=0;

                     }    
                      return $this->render('TitulacionSisAcademicoBundle:Admin:admin_listado_anulacion_detalle.html.twig',array(
                                                          'inscripcion' => $arrInscripcion,
                                                          'idCarrera'=>$idCarrera,
                                                          'idCiclo'=>$idCiclo,
                                                           'NombreEstudiante'=>$NombreEstudiante,
                                                            'IdSolicitudCab'=>$id_sa_solicitud
                                                       ));
           }
                    else
                    {
                           $this->get('session')->getFlashBag()->add(
                                         'mensaje',
                                         'Los datos ingresados no son válidos'
                                     );
                             return $this->redirect($this->generateUrl('titulacion_sis_academico_homepage'));
                    }
            }
            else
            {
                 $this->get('session')->getFlashBag()->add(
                                      'mensaje',
                                       'Los datos ingresados no son válidos'
                                   );
                     return $this->redirect($this->generateUrl('titulacion_sis_academico_homepage'));
             }
        }
        
   public function anulacion_detalle_2Action(Request $request)
        {
             
            $session=$request->getSession();
            $perfilEst   = $this->container->getParameter('perfilEst');
            $perfilDoc   = $this->container->getParameter('perfilDoc');
            $perfilAdmin = $this->container->getParameter('perfilAdmin'); 
            $perfilEstDoc = $this->container->getParameter('perfilEstDoc'); 
            $perfilEstAdm = $this->container->getParameter('perfilEstAdm'); 
            $perfilDocAdm = $this->container->getParameter('perfilDocAdm');
            $respuesta= new Response("",200);
            $materias  = $request->request->get('arrMaterias');
            $idEstudiante  = $request->request->get('idEstudiante');
            $idCarrera  = $request->request->get('idCarrera');
            $idCiclo  = $request->request->get('idCiclo');
            $idSolicitudCab  = $request->request->get('idSolicitudCab');
            $idRol=$session->get("perfil");
              if(strlen($idRol)>1)
              {
                $idRol = mb_substr($idRol,0,1);
              }
              else
              {
              $idRol = $idRol;
                  }


           if ($session->has("perfil")) {
               if($session->get('perfil') == $perfilAdmin || $session->get('perfil') == $perfilDocAdm || $session->get('perfil') == $perfilEstAdm){
                     $matricula_dis=array();
                     
                 try
                {
                    

                    $datosCuenta=""; 
                     foreach ($materias as $key => $value) {
                          $datosCuenta.= "<item>
                                          <id_solicitud_detalle>".$value['idSolicitud']."</id_solicitud_detalle>
                                            <identificador>".$value['idMateria']."</identificador>
                                            <aprobada>1</aprobada>
                                            <opcion>A</opcion>
                                          </item> 
                                          "; 
                      }
                      $xmlFinal="
                                <cabecera>
                                  <id_solicitud>".$idSolicitudCab."</id_solicitud> 
                                  <estado>75</estado>
                                  <usuario_estudiante></usuario_estudiante>
                                  <carrera>".$idCarrera."</carrera>
                                  <tipo_solicitud>82</tipo_solicitud> 
                                  <usuario_registro>".$idRol."</usuario_registro>
                                </cabecera>
                                <detalle> 
                                    ".$datosCuenta." 
                                </detalle>";



                     //$estudiante  = $session->get('nom_usuario');
                      $Mensaje="";
                      $Estado=0;
                      $UgServices = new UgServices;
                      $xml2 = $UgServices->setSolicitudAnula_Detalle($xmlFinal);

                       
                       if ( is_object($xml2))
                          {
                              foreach($xml2->parametrosSalida as $datos)
                               {  
                                  $Mensaje=(string) $datos->PV_MENSAJE;
                                  $Estado=(int) $datos->PI_ESTADO;
                               }
                              
                          }


                        $arrayProceso=array();
                        $arrayProceso['codigo_error']=$Estado;
                        $arrayProceso['mensaje']=$Mensaje;
                        $jarray=json_encode($arrayProceso);
                        
                        $respuesta->setContent($jarray);
                        return $respuesta;
                          
                    }catch (\Exception $e)
                        {
                         $Estado=0;
                         $arrayProceso=array();
                          $arrayProceso['codigo_error']=0;
                          $arrayProceso['mensaje']="Problemas al ejecutar la solicitud";
                          $jarray=json_encode($arrayProceso);
                          $respuesta->setContent($jarray);
                          return $respuesta;
                          //return $this->render('TitulacionSisAcademicoBundle:Estudiantes:error.html.twig');
                        }
                     
                   

             }else{
                  $this->get('session')->getFlashBag()->add(
                                'mensaje',
                                'Los datos ingresados no son válidos'
                            );
                    return $this->redirect($this->generateUrl('titulacion_sis_academico_homepage'));
               }
           }else{
                $this->get('session')->getFlashBag()->add(
                                      'mensaje',
                                      'Los datos ingresados no son válidos'
                                  );
                    return $this->redirect($this->generateUrl('titulacion_sis_academico_homepage'));
           }
                                
        }  




  public function estudiante_carrera_selectedAction(Request $request)
  {            
    $session=$request->getSession();
    $UgServices = new UgServices;
    $idCarrera = $request->request->get('carrera');
    $ciclo = $request->request->get('ciclo');
    $opcion = $request->request->get('opcion');
    $identificacion = $request->request->get('identificacion');
    $datos = array('estado' => 'NO');
    $lsPorcentaje = array();
    $idEstadoEstudiante = $request->request->get('estadoMatricula');
    if($opcion == "1"){
      $session->set("id_EstadoMatriculaEstudiante_"+$idCarrera,"0");
      $xml = $UgServices->getConsultaPorcentajeEstudianteCarrera($ciclo,$idCarrera);
      if ($xml){
        $colors = array('#F56954','#00A65A','#F39C12','#00C0EF','#3C8DBC','#D2D6DE','#2ecc40','#01ff70','#ffdc00','#ff851b','#ff4136','#85144b', '#f012be','#b10dc9','#111111','#aaaaaa','#dddddd');
        $iColor = 0;
        foreach($xml as $lcEstudiantes){
          $Estudiantes=array(
            "value" => $lcEstudiantes['cantidad'],
            "color" =>$colors[$iColor],
            "highlight" =>$colors[$iColor],
            "label"=> $lcEstudiantes['nombre'],
            );
          $iColor = $iColor +1;
          array_push($lsPorcentaje, $Estudiantes);
        }
      }
    }
    else{
      $session->set("id_EstadoMatriculaEstudiante_"+$idCarrera,$idEstadoEstudiante);
    }
    $xmlEstudiante = $UgServices->getConsultaEstudiantes_InscritosMatriculados($ciclo,$idCarrera,$idEstadoEstudiante,$identificacion);
    $lcNombre = "";
    $lcEstadoMatricula= "";
    $lsEstudiantes = array();

    if ($xmlEstudiante){
      foreach($xmlEstudiante as $lcEstudiantes){ 
        $lcNombre=$lcEstudiantes['nombrecompleto'];
        $lcEstadoMatricula=$lcEstudiantes['estadoestudiante'];
        $Estudiantes=array(
          "cedulaestudiante" => $lcEstudiantes['cedulaestudiante'],
          "nombre"=> $lcNombre,
          "estadoMateria"=> $lcEstadoMatricula,
          );
        array_push($lsEstudiantes, $Estudiantes);
      }
    }
    if($opcion == "1"){
      $datos =  array(
        "listadoEstudiante" =>  $lsEstudiantes,
        "listadoPorcentaje" =>  $lsPorcentaje,
        'estado' => 'SI'
        );
    }
    else{
      $datos =  array(
        "listadoEstudiante" =>  $lsEstudiantes,
        'estado' => 'SI'
        );
    }
    return new Response(json_encode($datos));
  }


  public function pdfEstudiantes_InscritosMatriculadosAction(Request $request, $idCarrera, $ciclo,$carrera ){     
    $session      = $request->getSession();
    $idEstadoEstudiante =  $session->get('id_EstadoMatriculaEstudiante'+$idCarrera);
    $UgServices = new UgServices;
    $xmlEstudiante = $UgServices->getConsultaEstudiantes_InscritosMatriculados($ciclo,$idCarrera,$idEstadoEstudiante,"");

    if ($xmlEstudiante){
      $pdf = "<html>
                <body>
                <img width='5%' src='images/menu/ug_logo.png'/>
                  <table align='center'>
                    <tr>
                      <td align='center'>
                        <b>Registro de Estudiantes</b>
                      </td>
                    </tr>
                    <tr>
                      <td>
                        <b>".$carrera."</b>
                      </td>
                    </tr>
                  </table>
                  <div class='col-lg-12'>
                  <br><br><br><br>
                  <table class='table table-striped table-bordered' border='1' width='100%' >
                    <thead>
                      <tr>
                        <th colspan='3'   style='text-align: center !important;background-color: #337AB7 !important;color: white!important;'>REPORTE DEL CLICLO</th>
                      </tr>
                      <tr>
                        <th style='text-align: center !important;'>Identificación</th> 
                        <th style='text-align: center !important;'>Nombre Alumno</th>
                        <th style='text-align: center !important;'>Estado Matricula</th>
                      </tr>
                    </thead>";

      foreach($xmlEstudiante as $lcEstudiantes){
        $lcNombre = $lcEstudiantes['nombrecompleto'];
        $lcEstadoMatricula = $lcEstudiantes['estadoestudiante'];
        $cedula = $lcEstudiantes['cedulaestudiante'];
        $pdf.="<tr>
                <td>".$cedula."</td>
                <td>".$lcNombre."</td>
                <td>".$lcEstadoMatricula."</td>
              </tr>";
            }
        $pdf.="</table>";
        $pdf.="</div></body></html>";
      }

      else{
        $pdf ="<p>No existen datos</p>";
      }
      $mpdfService = $this->get('tfox.mpdfport');
      $mPDF = $mpdfService->getMpdf();
      $mPDF->AddPage('','','1','i','on');
      $mPDF->WriteHTML($pdf);

    return new response($mPDF->Output());
  }#end function

//------------------------------------------------------------------------------------//
//------------------------------------------------------------------------------------//
//------------------------------------------------------------------------------------//

  
    public function editar_eventos_calendarioAction(Request $request){
        $UgServices = new UgServices;

        $evento     = $request->request->get('evento');
        $id_evento  = $request->request->get('id_evento');
        $est_evento  = $request->request->get('est_evento');

        $rsInsertEvent = $UgServices->modificarEventos($evento,$id_evento,$est_evento);

        return new Response($rsInsertEvent);
    }#end function

    public function modificar_eventos_calendarioAction(Request $request){
        $session=$request->getSession();

        $id_ciclo = $session->get("îdciclo_calendar");
        $UgServices   = new UgServices;
        $id_evento    = $request->request->get('id_evento');
        $fec_desde    = $request->request->get('start');
        $fec_hasta    = $request->request->get('end');
        $id_calendario  = $request->request->get('id_calendario');
        $estado  = $request->request->get('estado');
        // $date = date_format($fec_desde, 'Y-m-d H:i:s');
        $session=$request->getSession();
        $id_usuario = $session->get("id_user");
        $id_usuario = 11;
        $rsInsertEvent = $UgServices->modificarEventosCalendario($id_evento,$id_ciclo,$fec_desde,$fec_hasta,$id_usuario,$id_calendario,$estado);
        // echo '<pre>'; var_dump($rsInsertEvent); exit();
        return new Response($rsInsertEvent);
    }#end function


  /* INICIO SPRINT ARELLANO 4.1 */
  /* ------------------------------------------------------------------------------*/
  /* ------------------------------------------------------------------------------*/
  public function consulta_materias_aprobadas_estudianteAction(Request $request){
    $session=$request->getSession();
    $perfilEst   = $this->container->getParameter('perfilEst');
    $perfilDoc   = $this->container->getParameter('perfilDoc');
    $perfilAdmin = $this->container->getParameter('perfilAdmin'); 
    $perfilEstDoc = $this->container->getParameter('perfilEstDoc'); 
    $perfilEstAdm = $this->container->getParameter('perfilEstAdm'); 
    $perfilDocAdm = $this->container->getParameter('perfilDocAdm');
    //$datos = array();        
    if ($session->has("perfil")){
      if ($session->get('perfil') == $perfilAdmin || $session->get('perfil') == $perfilEstAdm || $session->get('perfil') == $perfilDocAdm){
        $Carreras = array();
        try{
          $lcCarrera="";
          // $idAdministrador=$session->get("id_user");
          $idAdministrador= "24";
          // $idRol=$perfilAdmin;
          $idRol= "3";
          $ciclo = "";
          $nombreCarrera = "";
          $idCarrera = "";
          $tienePermiso = "0";
          $lsEstudiantes = array();
          $lsOpciones = array();
          $UgServices = new UgServices;
          $xml = $UgServices->getConsultaCarreras($idAdministrador,$idRol);
          
          if ( is_object($xml)){
            
            foreach($xml->registros->registro as $lcCarreras){
              $idCarrera=$lcCarreras->id_sa_carrera;
              $nombreCarrera = $lcCarreras->nombre;
              $ciclo = $lcCarreras->id_sa_ciclo_detalle;
              $carreraObject = array(
                'nombre' => $nombreCarrera,
                'idCarrera'=> $idCarrera,
                'idCiclo'=> $ciclo,
                );
              array_push($Carreras, $carreraObject);
            }
            $cuantos=count($Carreras);
            
            if ($cuantos!=0){
              $datos =  array(
                'carreras' =>  $Carreras,
                'mostrar' =>  'SI'
                );
                
                // echo "<pre>";
                // var_dump(array('estudianteCarrera' => $datos));
                // echo "</pre>";
                // exit();
              return $this->render('TitulacionSisAcademicoBundle:Admin:consulta_materia_aprobadas_estudiantes.html.twig', array('estudianteCarrera' => $datos =array('mostrar' =>  'SI', 'carreras' =>  $Carreras)));
            }
          }
        }
        catch (\Exception $e){
          return $this->render('TitulacionSisAcademicoBundle:Admin:consulta_materia_aprobadas_estudiantes.html.twig', array('estudianteCarrera' => $datos   = array('mostrar' =>  'SI', 'carreras' =>  $Carreras)));
        }
      }
      else{
        $this->get('session')->getFlashBag()->add(
          'mensaje', 'Los datos ingresados no son válidos'
          );
        return $this->redirect($this->generateUrl('titulacion_sis_academico_homepage'));
      }
    }
    else{
      $this->get('session')->getFlashBag()->add(
        'mensaje', 'Los datos ingresados no son válidos'
        );
      return $this->redirect($this->generateUrl('titulacion_sis_academico_homepage'));
    }
    return $this->render('TitulacionSisAcademicoBundle:Admin:consulta_materia_aprobadas_estudiantes.html.twig', array('estudianteCarrera' => $datos   = array('mostrar' =>  'SI', 'carreras' =>  $Carreras)));
  }

  public function consulta_materias_aprobadas_estudiante_busquedaAction(Request $request){

  $session=$request->getSession();
  $perfilEst   = $this->container->getParameter('perfilEst');
  $perfilDoc   = $this->container->getParameter('perfilDoc');
  $perfilAdmin = $this->container->getParameter('perfilAdmin'); 
  $perfilEstDoc = $this->container->getParameter('perfilEstDoc'); 
  $perfilEstAdm = $this->container->getParameter('perfilEstAdm'); 
  $perfilDocAdm = $this->container->getParameter('perfilDocAdm');
  $datos  = array('mostrar' =>  'NO' );
  $idCarrera = $request->request->get('carrera');
  $opcion = $request->request->get('opcion');
  $identificacion = $request->request->get('identificacion');
  $session->set("identificacion_materias".$idCarrera,$identificacion);
  $session->set("nivel_materias".$idCarrera,"0");
  $session->set("cicloDetalle_materias".$idCarrera,"0");

  if ($session->has("perfil")){

    if ($session->get('perfil') == $perfilAdmin || $session->get('perfil') == $perfilEstAdm || $session->get('perfil') == $perfilDocAdm){
      try{
        $lcCarrera="";
        $tienePermiso = "0";
        $lsEstudiantes = array();
        $lsOpciones = array();
        $lsMaterias = array();
        $lsNivel = array();
        $lsCiclo = array();
        $lsPorcentaje = array();
        $UgServices = new UgServices;
        $xml = $UgServices->getConsultaMateriasAprobadasEstudianteAdmin($opcion,$identificacion, $idCarrera, "", "");
        
        if ( is_object($xml)) {
          $estado = $xml ->PI_ESTADO;
          $message = $xml ->PV_MENSAJE;
          /* foreach( $xml->PX_Salida->datosAlumno as $lcMaterias){
            $materia=$lcMaterias->materia;
            $nivel = $lcMaterias->nivel;
            $ciclo = $lcMaterias->ciclo;
            $promedio = $lcMaterias->porcentaje;

            echo "<pre>";
            var_dump( $materia);
            echo "</pre>";
            exit();
          } */ 
          $xmlAlumno = $xml->PX_Salida->datosAlumno->materias;

          if(is_object($xmlAlumno)){
            foreach($xmlAlumno->materia as $lcMaterias){
              $materia=$lcMaterias->materia;
              $nivel = $lcMaterias->nivel;
              $ciclo = $lcMaterias->ciclo;
              $promedio = $lcMaterias->promedio;
              $materiasObject = array(
                'materia' => $materia,
                'nivel'=> $nivel,
                'ciclo'=> $ciclo,
                'promedio'=> $promedio
                );
              array_push($lsMaterias, $materiasObject);
            }
          }
          $xmlNivel = $xml->PX_Salida->datosAlumno->niveles;

          if(is_object($xmlNivel)){
            foreach($xmlNivel->nivel as $lcNivel){
              $codigo=$lcNivel->nivel;
              $descripcion = $lcNivel->nombreNivel;
              $nivelObject = array(
                'valor' => $codigo,
                'descripcion'=> $descripcion
                );
              array_push($lsNivel, $nivelObject); 
            }
          }
          $xmlCiclo = $xml->PX_Salida->datosAlumno->ciclos;
          
          if(is_object($xmlCiclo)){
            foreach($xmlCiclo->ciclo as $lcCiclo){
              $codigo=$lcCiclo->idCiclo;
              $descripcion = $lcCiclo->descripcion;
              $cicloObject = array(
                'valor' => $codigo,
                'descripcion'=> $descripcion
                );
              array_push($lsCiclo, $cicloObject);
            }
          }
          $xmlPorcentaje = $xml->PX_Salida->datosAlumno->porcentajes;

          if(is_object($xmlPorcentaje)){
            foreach($xmlPorcentaje->porcentaje as $lcPorcentaje){
              $codigo=$lcPorcentaje->totalPorcentaje;
              $descripcion = $lcPorcentaje->nivel;
              $porcentajeObject = array(
                'valor' => $codigo,
                'descripcion'=> $descripcion
                );
              array_push($lsPorcentaje, $porcentajeObject);
            }
          }
        }
        $cuantos=count($lsMaterias);

        if ($cuantos!=0){
          $datos =  array(
            'nombre' =>   $xml->PX_Salida->datosAlumno->nombreEstudiante,
            'listadoCiclos' =>  $lsCiclo,
            'listadoNiveles' =>  $lsNivel,
            'listadoEstudiante' =>  $lsMaterias,
            'porcentajes' =>  $lsPorcentaje,
            'mostrar' =>  'SI'
            );
        }
      }
      catch (\Exception $e){
        $datos  = array('mostrar' =>  'NO' );
      }
      return new Response(json_encode($datos));
    }
    else{
      $this->get('session')->getFlashBag()->add(
        'mensaje', 'Los datos ingresados no son válidos'
        );
      return $this->redirect($this->generateUrl('titulacion_sis_academico_homepage'));
    }
  }
  else{
    $this->get('session')->getFlashBag()->add(
      'mensaje', 'Los datos ingresados no son válidos'
      );
    return $this->redirect($this->generateUrl('titulacion_sis_academico_homepage'));
  }
  }


  public function consulta_materias_aprobadas_estudiante_filtroAction(Request $request){

  $session=$request->getSession();
  $perfilEst   = $this->container->getParameter('perfilEst');
  $perfilDoc   = $this->container->getParameter('perfilDoc');
  $perfilAdmin = $this->container->getParameter('perfilAdmin'); 
  $perfilEstDoc = $this->container->getParameter('perfilEstDoc'); 
  $perfilEstAdm = $this->container->getParameter('perfilEstAdm'); 
  $perfilDocAdm = $this->container->getParameter('perfilDocAdm');
  $datos  = array('mostrar' =>  'NO' );
  $idCarrera = $request->request->get('carrera');
  $opcion = $request->request->get('opcion');
  $identificacion = $request->request->get('identificacion');
  $nivel = $request->request->get('nivel');
  $ciclo = $request->request->get('cicloDetalle');
  $session->set("identificacion_materias".$idCarrera,$identificacion);
  $session->set("nivel_materias".$idCarrera,$nivel);
  $session->set("cicloDetalle_materias".$idCarrera,$ciclo);
  /*  echo "<pre>";
  var_dump($session->set("identificacion"+$idCarrera,$identificacion),
  $session->set("nivel"+$idCarrera,$nivel),
  $session->set("cicloDetalle"+$idCarrera,$ciclo));
  echo "</pre>";
  exit();*/
  if ($session->has("perfil")){
    if ($session->get('perfil') == $perfilAdmin || $session->get('perfil') == $perfilEstAdm || $session->get('perfil') == $perfilDocAdm){
      try{
        $lcCarrera=""; 
        $tienePermiso = "0";
        $lsEstudiantes = array();
        $lsOpciones = array();
        $lsMaterias = array();
        $lsNivel = array();
        $lsCiclo = array();
        $lsPorcentaje = array();
        $UgServices = new UgServices;
        $xml = $UgServices->getConsultaMateriasAprobadasEstudianteAdmin($opcion,$identificacion, $idCarrera, $ciclo, $nivel);
        if ( is_object($xml)) {
          $estado = $xml ->PI_ESTADO;
          $message = $xml ->PV_MENSAJE;
          /* foreach( $xml->PX_Salida->datosAlumno as $lcMaterias){
            $materia=$lcMaterias->materia;
            $nivel = $lcMaterias->nivel;
            $ciclo = $lcMaterias->ciclo;
            $promedio = $lcMaterias->porcentaje;
            echo "<pre>";
            var_dump( $materia);
            echo "</pre>";
            exit();
          } */
          $xmlAlumno = $xml->PX_Salida->alumno;
          if(is_object($xmlAlumno)){
            foreach($xmlAlumno->materia as $lcMaterias){
              $materia=$lcMaterias->materia;
              $nivel = $lcMaterias->nivel;
              $ciclo = $lcMaterias->ciclo;
              $promedio = $lcMaterias->promedio;
              $materiasObject = array(
                'materia' => $materia,
                'nivel'=> $nivel,
                'ciclo'=> $ciclo,
                'promedio'=> $promedio
                );
              array_push($lsMaterias, $materiasObject);
            }
          }
        }
        $cuantos=count($lsMaterias);
        if ($cuantos!=0){
          $datos =  array(
            'listadoEstudiante' =>  $lsMaterias,
            'mostrar' =>  'SI');
            /* echo "<pre>";
            var_dump( $datos);
            echo "</pre>";
            exit(); */
        }
      }
      catch (\Exception $e){
        $datos  = array('mostrar' =>  'NO' );
      }
      return new Response(json_encode($datos));
    }
    else{
      $this->get('session')->getFlashBag()->add(
        'mensaje', 'Los datos ingresados no son válidos'
        );
      return $this->redirect($this->generateUrl('titulacion_sis_academico_homepage'));
    }
  }
  else{
    $this->get('session')->getFlashBag()->add(
      'mensaje', 'Los datos ingresados no son válidos'
    );
    return $this->redirect($this->generateUrl('titulacion_sis_academico_homepage'));
  }
}


  public function consulta_materias_aprobadas_estudiante_pdfAction(Request $request,$idCarrera, $ciclo,$carrera){

  $session=$request->getSession();
  $perfilEst   = $this->container->getParameter('perfilEst');
  $perfilDoc   = $this->container->getParameter('perfilDoc');
  $perfilAdmin = $this->container->getParameter('perfilAdmin'); 
  $perfilEstDoc = $this->container->getParameter('perfilEstDoc'); 
  $perfilEstAdm = $this->container->getParameter('perfilEstAdm'); 
  $perfilDocAdm = $this->container->getParameter('perfilDocAdm');

  $datos  = array('mostrar' =>  'NO' );

  $identificacion =$session->get('identificacion_materias'.$idCarrera);
  $nivel =   $session->get('nivel_materias'.$idCarrera);
  $ciclo =  $session->get('cicloDetalle_materias'.$idCarrera);

  // echo "<pre>";
  // var_dump( $ciclo);
  // echo "</pre>";
  // exit();


  if ($session->has("perfil")){
    if ($session->get('perfil') == $perfilAdmin || $session->get('perfil') == $perfilEstAdm || $session->get('perfil') == $perfilDocAdm){
      try{
        $lcCarrera=""; 
        $tienePermiso = "0";
        $lsEstudiantes = array();
        $lsOpciones = array();
        $lsMaterias = array();
        $lsNivel = array();
        $lsCiclo = array();
        $lsPorcentaje = array();
        $pdf="";
        $UgServices = new UgServices;
        // echo "<pre>";
        // var_dump($identificacion, $idCarrera, $ciclo, $nivel);
        // echo "</pre>";
        // exit();
        $xml = $UgServices->getConsultaMateriasAprobadasEstudianteAdmin("F",$identificacion, $idCarrera, $ciclo, $nivel);
        // echo "<pre>";
        // var_dump($xml);
        // echo "</pre>";
        // exit();
        if ( is_object($xml)) {
          $estado = $xml ->PI_ESTADO;
          $message = $xml ->PV_MENSAJE;
          if($estado == "1"){ 
            $xmlAlumno = $xml->PX_Salida->alumno;
            if(is_object($xmlAlumno)){
              $pdf =
              " <html> 
                  <body>
                    <img width='5%' src='images/menu/ug_logo.png'/>
                    <table align='center'>
                      <tr>
                        <td align='center'>
                          <b>Materias del Estudiante</b>
                        </td>
                      </tr>
                      <tr>
                        <td>
                          <b>".$carrera."</b>
                        </td>
                      </tr>
                    </table>
                    <div class='col-lg-12'>
                      <br><br><br><br>
                    <table class='table table-striped table-bordered' border='1' width='100%' >
                      <thead>
                        <tr>
                          <th colspan='4'   style='text-align: center !important;background-color: #337AB7 !important;color: white!important;'>REPORTE DEL CLICLO</th>
                        </tr>
                        <tr>
                          <th style='text-align: center !important;'>Ciclo</th>
                          <th style='text-align: center !important;'>Nivel</th>
                          <th style='text-align: center !important;'>Materia</th>
                          <th style='text-align: center !important;'>Promedio</th>
                        </tr>
                      </thead>";
                      
                foreach($xmlAlumno->materia as $lcMaterias){
                  $materia=$lcMaterias->materia;
                  $nivel = $lcMaterias->nivel;
                  $ciclo = $lcMaterias->ciclo;
                  $promedio = $lcMaterias->promedio;
                  $pdf.="<tr>
                          <td>".$ciclo."</td>
                          <td>".$nivel."</td>
                          <td>".$materia."</td>
                          <td>".$promedio."</td>
                        </tr>";
                }
                $pdf.="</table>";
                $pdf.="</div></body></html>";
              }
            }
            else{
              $pdf.="<p>No existen datos</p>";
            }
          }
          $mpdfService = $this->get('tfox.mpdfport');
          $mPDF = $mpdfService->getMpdf();
          $mPDF->AddPage('','','1','i','on');
          $mPDF->WriteHTML($pdf);
          return new response($mPDF->Output());
        }
        catch (\Exception $e){}
      }
      else{
        $this->get('session')->getFlashBag()->add(
          'mensaje', 'Los datos ingresados no son válidos'
          );
        return $this->redirect($this->generateUrl('titulacion_sis_academico_homepage'));
      }
    }
    else{
      $this->get('session')->getFlashBag()->add(
      'mensaje', 'Los datos ingresados no son válidos'
      );
      return $this->redirect($this->generateUrl('titulacion_sis_academico_homepage'));
    }
    // return $this->render('TitulacionSisAcademicoBundle:Admin:consulta_materia_aprobadas_estudiantes.html.twig', array('estudianteCarrera' => $datos  = array('mostrar' =>  'NO' )));
  }
  /* FIN SPRINT ARELLANO 4.1 */
  /* ------------------------------------------------------------------------------*/
  /* ------------------------------------------------------------------------------*/
     public function listaEstudiantesAction(Request $request)
        { 
           $notas='';
            date_default_timezone_set('America/Buenos_Aires');
         $withoutModal       = true;
                     $profesor='Apolinario';
            //$materia='Calculo';
            $paralelo='S2A';
          
            $notas='';
            $parcial =$request->request->get('parcial');
            $session=$request->getSession();
               $session->set("parcial",$parcial);
            
            
            $response   		= new JsonResponse();
            $withoutModal       = true;
         
            $idDocente     = 1;
            $carrera  =1;
            $UgServices    = new UgServices;
            
            ////////////////////////////////
            
            $idDocente  = $session->get('id_user');
            //$idDocente  = $request->request->get('idDocente');
            $idCarrera  = $request->request->get('carrera');

            $datosMaterias	= array();
            $datosDocentes	= array();
            //$idDocente = "1";
            //$idCarrera = "2";

            $UgServices    = new UgServices;
            
            
            $datosDocentes  = $UgServices->Docentes_getDocentes($idCarrera);
           // print_r($datosDocentes);
            
            
            $datosMaterias  = $UgServices->Docentes_getMaterias($idDocente, $idCarrera);
            $muestraDocente="<option value=''>Seleccione Docente</option>";
            $muestraMateria="<option value=''>Seleccione Materia</option>";
           /*print_r($datosMaterias);
           exit();*/
             foreach($datosMaterias as $materia) {
              ##echo $materia['materia'];
                  $muestraMateria .= '<option value="'.$materia['id_sa_materia_paralelo'].'">'.$materia['materia']."--".$materia['paralelo'].'</option>';
               
            }
           
             foreach($datosDocentes as $docente) {
              
                  $muestraDocente .= '<option value="'.$docente['id_sg_usuario'].'">'.$docente['profesor'].'</option>';
               
            }
            
            
            
            ///////////////////////////////////////
            //$idDocente="";
               //$idCarrera="";
            // $materia="2269";
       //Menu de Notas por Materia para Profesor
         $Parcial='1';
             
        
                 $this->v_html = $this->renderView('TitulacionSisAcademicoBundle:Admin:listaEstudiantes.html.twig',
						  array(
							 //  'arr_datos'	=> $arr_datos,
                                                           'docente'   => $muestraDocente ,
                                                           'materia'   => $muestraMateria,
                                                           'idCarrera'   => $idCarrera,
                                                           'cantidad'   => '',
                                                           'msg'   	=> $this->v_msg
						  ));
                        
                        $response->setData(
                                array(
					'error' 		=> $this->v_error,
					'msg'			=> $this->v_msg,
                                        'html' 			=> $this->v_html,
                                        'withoutModal' 	=> $withoutModal,
                                        'recargar'      => '0'
                                     )
                              );
                        return $response;
        }
        
        public function cmbMateriasAction(Request $request)
        { 
             $response   		= new JsonResponse();
            $idCarrera =$request->request->get('carrera');
            
            $idDocente =$request->request->get('docente');
             $UgServices    = new UgServices;
            $response   		= new JsonResponse();
            
            $datosMaterias  = $UgServices->Docentes_getMaterias($idDocente, $idCarrera);
            $muestraMateria="<option value=''>Seleccione Materia</option>";
           /*print_r($datosMaterias);
           exit();*/
             foreach($datosMaterias as $materia) {
              ##echo $materia['materia'];
                  $muestraMateria .= '<option value="'.$materia['id_sa_materia_paralelo'].'">'.$materia['materia']."-".$materia['paralelo'].'</option>';
               
            }
           
            
        
			$this->v_html = $this->renderView('TitulacionSisAcademicoBundle:Admin:cmbMaterias.html.twig',
						  array(
                                                           'materia'   => $muestraMateria
                                                           
						  ));
                        
                        $response->setData(
                                array(
                                        'html' 			=> $this->v_html
                                     )
                              );
                        return $response;
        }
        
       public function muestraEstudiantesAction(Request $request)
        { 
             $response   		= new JsonResponse();
            
            
            $idDocente =$request->request->get('docente');
            
            $idMateria =$request->request->get('materia');
            
            $Docente =$request->request->get('docente_text');
            
            $Materia =$request->request->get('materia_text');
            $paralelo =$request->request->get('paralelo');
            
            $Fecha=date('d/m/Y');
            
            //echo $idDocente."--".$idMateria."--";
            
            
            $response   		= new JsonResponse();
            if ($idDocente==="")
            {
                    $this->v_error	= true; 
                    $this->v_msg ='Debe seleccionar un Docente';
                 }else {   
                    
                    if($idMateria==="")
                   { 
                        $this->v_error	= true; 
                          $this->v_msg ='Debe seleccionar una Materia'; 
                        }else { 
                           // $idMateria="235";
                   $trama = "<materiaparalelo>".$idMateria."</materiaparalelo>";
                   $UgServices    = new UgServices; 
                    $arr_datos  = $UgServices->Docentes_getAlumnos($trama);
                   $this->v_html = $this->renderView('TitulacionSisAcademicoBundle:Admin:tablaEstudiantes.html.twig',
						  array(
                                                           'docente_text'   => $Docente,
                                                           'materia_text'   => $Materia,
                                                           'paralelo'   => $paralelo,
                                                           'fecha'   => $Fecha,
                                                           'arr_datos'   => $arr_datos
                                                           
						  ));
                     }
                     
             
                 }
                       
                        
                        $response->setData(
                                array(
                                        'msg'                => $this->v_msg,
                                        'error'              => $this->v_error,
                                        'html' 		     => $this->v_html
                                     )
                              );
                        return $response;
        }
        
     public function ExportarPDFEstudiantesAction(Request $request,$docente,$materia,$docente_text,$materia_text,$paralelo)
        { 
             $response   		= new JsonResponse();
          //  echo "ttttttttttttttttttt";
            
            $Fecha=date('d/m/Y');
            //echo $idDocente."--".$idMateria."--";
            
            
            $response   		= new JsonResponse();
           
                           // $idMateria="2269";
                   $trama = "<materiaparalelo>".$materia."</materiaparalelo>";
                   $UgServices    = new UgServices; 
                    $arr_datos  = $UgServices->Docentes_getAlumnos($trama);
                   $this->v_html = $this->renderView('TitulacionSisAcademicoBundle:Admin:tablaEstudiantes.html.twig',
						  array(
                                                           'docente_text'   => $docente_text,
                                                           'materia_text'   => $materia_text,
                                                           'paralelo'   => $paralelo,
                                                           'fecha'   => $Fecha,
                                                           'arr_datos'   => $arr_datos
                                                           
						  ));
                   
                       
                        
                       /* $response->setData(
                                array(
                                        'msg'                => $this->v_msg,
                                        'error'              => $this->v_error,
                                        'html' 		     => $this->v_html
                                     )
                              );
                        return $response;*/
                  $mpdfService = $this->get('tfox.mpdfport');
                  $mPDF = $mpdfService->getMpdf();
                 // $mPDF = $mpdfService->add();
                  $mPDF->AddPage('','','1','i','on');
                  $mPDF->WriteHTML($this->v_html);
                  
                  //$mPDF->AddPage('','','1','i','on');
                  //$mPDF->WriteHTML($pdf);
                  //$mPDF->Output();
                  return new response($mPDF->Output());
        }
        
      public function cargaPaginaAction(Request $request)
        {
			//print_r($_SESSION);
            $response = new JsonResponse();
            
             $idDocente =$request->request->get('docente');
            
            $idMateria =$request->request->get('materia');
            $Docente =$request->request->get('docente_text');
            
            $Materia =$request->request->get('materia_text');
           list($Materia,$paralelo) = split('[-]', $Materia);
            
           $Docente= trim($Docente);
            // $prueba2=$_SERVER['HTTP_HOST'];
             //$prueba=app.request.getSchemeAndHttpHost();
//             $servidor=$_SERVER['HTTP_HOST'];
//             $section ='http://'.$servidor.'/desarrollo/appAcademico/web/admin/PDF/estudiantes/'.$idDocente.'/'.$idMateria.'/'.$Docente.'/'.$Materia.'/'.$paralelo;
             $baseurl = $request->getScheme() . '://' . $request->getHttpHost() . $request->getBasePath();

           $section =$baseurl.'/admin/PDF/estudiantes/'.$idDocente.'/'.$idMateria.'/'.$Docente.'/'.$Materia.'/'.$paralelo;
            $response->setData(
                                array(
                                        'redirect' => true,
                                        'section' => $section,
                                     )
                              );

            return $response;
        }
        
public function generacion_horariosAction(Request $request){
    
    $session=$request->getSession();
    $idUsuario  = $session->get('id_user');
    $UgServices = new UgServices;
    $Paralelos = $UgServices->Paralelos(4);
    $Materia = $UgServices->Materia(4,44);
        //echo var_dump($Materia); exit();
    return $this->render('TitulacionSisAcademicoBundle:Admin:generacion_horario_admin.html.twig',
    									array(
    				'data' => array('Paralelo' => $Paralelos,
                                                'Materia'   => $Materia)
    										 )
                              );
   }
   
   public function generacion_horarios_examenAction(Request $request){
    
    $session=$request->getSession();
    $idUsuario  = $session->get('id_user');
    $UgServices = new UgServices;
    $Paralelos = $UgServices->Paralelos(4);
    $Materia = $UgServices->Materia(4,44);
        //echo var_dump($Materia); exit();
    return $this->render('TitulacionSisAcademicoBundle:Admin:generacion_horario_examen.html.twig',
    									array(
    				'data' => array('Paralelo' => $Paralelos,
                                                'Materia'   => $Materia)
    										 )
                              );
   }
   
    public function generacion_horarios_grabarAction(Request $request){
    $respuesta= new Response("",200);
    $session=$request->getSession();
    $idUsuario  = $session->get('id_user');
    $materias  = $request->request->get('arrMaterias');
    $contador  = $request->request->get('contador');
             $UgServices = new UgServices;
                 foreach ($materias as $key => $value) {
                     $valore = explode(";",$value['Horario']);  
                        for($i=0;$i<$contador;$i++){
                            $datos = explode("_",$valore[$i]); 
                            $hora_inicio = $datos[4].":00";
                            $hora_fin = $datos[5].":00";
                           $xmlfinal=" <pi_id_sg_usuario_profesor>$datos[2]</pi_id_sg_usuario_profesor>
                        <pi_id_sa_materia>$datos[0]</pi_id_sa_materia>
                        <pi_id_sa_paralelo>$datos[1]</pi_id_sa_paralelo>
                        <pi_cupo_estudiantes>$datos[3]</pi_cupo_estudiantes>
                        <pi_dia_semana>1</pi_dia_semana>
                        <pt_hora_inicio>$hora_inicio</pt_hora_inicio>
                        <pt_hora_fin>$hora_fin</pt_hora_fin>
                        <pi_id_sg_usuario_registro>1</pi_id_sg_usuario_registro>
                        <pc_opcion>A</pc_opcion>
                        <pi_id_sa_materia_paralelo>2330</pi_id_sa_materia_paralelo>
                        <pi_id_sa_horario>1091</pi_id_sa_horario>
                        <pi_id_sa_profesor_materia_carrera>2115</pi_id_sa_profesor_materia_carrera>";
                                                
                         $xml = $UgServices->Guarda_Horarios_docente($xmlfinal);
                        }                                         
                }
             $Estado="";
                $Mensaje="";
             if ( is_object($xml))
                {
                    foreach($xml->parametrosSalida as $datos)
                     {  
                        $Estado=(int) $datos->PI_ESTADO;
                        $Mensaje=(string) $datos->PV_MENSAJE;
                     }
                    
                }
            $arrayProceso = array();
            $arrayProceso['codigo_error']=$Estado;
            $arrayProceso['mensaje']=$Mensaje;
            $jarray=json_encode($arrayProceso);          
            $respuesta->setContent($jarray);
            return $respuesta;

   }
   
    public function generacion_horario_examene2Action(Request $request){
    $respuesta= new Response("",200);
    $session=$request->getSession();
    $idUsuario  = $session->get('id_user');
    $cedula = $request->request->get('cedula');

             $UgServices = new UgServices;

                           $xmlfinal=" <PI_ID_CICLO_DET>19</PI_ID_CICLO_DET>
				<PI_ID_USUARIO_REG>$idUsuario</PI_ID_USUARIO_REG>";
//                                                
                         $xml = $UgServices->Guarda_Horarios_examen($xmlfinal);
                         
                      $Estado="";
                $Mensaje="";
             if ( is_object($xml))
                {
                    foreach($xml->parametrosSalida as $datos)
                     {  
                        $Estado=(int) $datos->PI_ESTADO;
                        $Mensaje=(string) $datos->PV_MENSAJE;
                     }
                    
                }
            $arrayProceso = array();
            $arrayProceso['codigo_error']=$Estado;
            $arrayProceso['mensaje']=$Mensaje;
            $jarray=json_encode($arrayProceso);          
            $respuesta->setContent($jarray);
            return $respuesta;
           
   }
   
     public function docente_horarioAction(Request $request){
    $respuesta= new Response("",200);
    $session=$request->getSession();
    $idUsuario  = $session->get('id_user');
    $idMateria = $request->request->get('idMateria');
    $dia = $request->request->get('dia');
    $idParalelo = $request->request->get('idParalelo');
    $horaInicio  = $request->request->get('horaInicio');
    $horaFin  = $request->request->get('horaFin');
           $horaInicio = $horaInicio.":00";
                            $horaFin = $horaFin.":00";
                            $horaFin = ltrim($horaFin);
             $UgServices = new UgServices;
                 
                           $xmlfinal="		<horarios>
                                    <idMateria>$idMateria</idMateria>
                                    <dia>$dia</dia>
                               <idParalelo>$idParalelo</idParalelo>
                                <horaInicio>$horaInicio</horaInicio>
                                <horaFin>$horaFin</horaFin>
                                </horarios>";
                                                
                         $xml = $UgServices->docente_horario_c($xmlfinal);
                        
                  
             $Estado="";
                $Mensaje="";
             if ( is_object($xml))
                {
                    foreach($xml->parametrosSalida as $datos)
                     {  
                        $Estado=(int) $datos->PI_ESTADO;
                        $Mensaje=(string) $datos->PV_MENSAJE;
                     }
                    
                }
            $arrayProceso = array();
            $arrayProceso['codigo_error']=$Estado;
            $arrayProceso['mensaje']="Gabriel Huayamabe";
            $jarray=json_encode($arrayProceso);          
            $respuesta->setContent($jarray);
            return $respuesta;

   }
   
     public function carga_solicitudAction(Request $request)
            {    $session=$request->getSession();   
            $idUsuario  = $session->get('id_user');
                if($session->has("perfil")) {
                   
                    return $this->render('TitulacionSisAcademicoBundle:Admin:Subir_Solicitud.html.twig');
                }else{
                    return $this->render('TitulacionSisAcademicoBundle:Home:login.html.twig');
                }
                   
            }
   
    public function subir_solicitudAction(Request $request){
    $respuesta= new Response("",200);
    $session=$request->getSession();
    $idUsuario  = $session->get('id_user');
    $cedula = $request->request->get('cedula');
    $Solicitud  = $request->request->get('Solicitud');
    $fileSize  = $request->request->get('fileSize');
             $UgServices = new UgServices;
               
                           $xmlfinal="<PX_XML>
					<items>
						<item>
						    <id_sa_formato_solicitud></id_sa_formato_solicitud>
						    <descripcion>$cedula</descripcion>
							<ruta_archivo>$fileSize</ruta_archivo>
							<id_usuario>$idUsuario</id_usuario>
							<estado>A</estado>
						</item>
					</items>
				</PX_XML>
				<PC_OPCION>A</PC_OPCION>";
                                            //echo  var_dump($xmlfinal); exit();     
                        $xml = $UgServices->subir_solicitud($xmlfinal);
                     
             $Estado="";
                $Mensaje="";
             if ( is_object($xml))
                {
                    foreach($xml->parametrosSalida as $datos)
                     {  
                        $Estado=(int) $datos->PI_ESTADO;
                        $Mensaje=(string) $datos->PV_MENSAJE;
                     }
                    
                }
            $arrayProceso = array();
            $arrayProceso['codigo_error']=$Estado;
            $arrayProceso['mensaje']=$Mensaje;
            $jarray=json_encode($arrayProceso);          
            $respuesta->setContent($jarray);
            return $respuesta;

   }
   
    public function generacion_horarios_examenesAction(Request $request){
    
    $session=$request->getSession();
    $idUsuario  = $session->get('id_user');
            if($session->has("perfil")) {
              return $this->render('TitulacionSisAcademicoBundle:Admin:generacion_horarios_examen.html.twig');
            }else{
                    return $this->render('TitulacionSisAcademicoBundle:Home:login.html.twig');
                }
   }
   
    public function consultahorariosAction(Request $request)
    {     
            $session=$request->getSession();
            $perfilEst   = $this->container->getParameter('perfilEst');
            $perfilDoc   = $this->container->getParameter('perfilDoc');
            $perfilAdmin = $this->container->getParameter('perfilAdmin'); 
            $perfilEstDoc = $this->container->getParameter('perfilEstDoc'); 
            $perfilEstAdm = $this->container->getParameter('perfilEstAdm'); 
            $perfilDocAdm = $this->container->getParameter('perfilDocAdm');
            $estudiante  = $session->get('nom_usuario'); 
             $idUsuario  = $session->get('id_user');
             
               $UgServices = new UgServices;
               $docentes = $UgServices->cargar_docente_por_carrera(4);
           // echo var_dump($docentes); exit();
           
          return $this->render('TitulacionSisAcademicoBundle:Admin:consultahorariosgenerales.html.twig',
    									array(
    				'data' => array('docentes' => $docentes)
    										 )
                              );
           
    }#end function
    
      public function pdfhorariosAction(Request $request,$id,$nombre)
    {     
        
            $session=$request->getSession();          
                       
            $UgServices    = new UgServices;
          
            $datosHorarios  = $UgServices->Docentes_Horarios($id);
            
                   $pdf= " <html> 
                                            <body>
                                            <img width='5%' src='images/menu/ug_logo.png'/>
                                            <table align='center'>
                                            <tr>
                                              <td align='center'>
                                                <b> Horario de clases</b>
                                              </td>
                                            <tr>
                                            <tr>
                                            <td>
                                              <b>Docente: $nombre</b>
                                            </td>
                                            </tr>
                                            </table>
                                            <div class='col-lg-12'>
                                            <br><br><br><br>
                                            <table class='table table-striped table-bordered' border='1' width='100%' >
                                                     <thead>
                                                        <tr>
                                                                <th colspan='5'   style='text-align: center !important;background-color: #337AB7 !important;color: white!important;'>Periodo  </th>
                                                        </tr>
                                                        <tr>
                                                            <th style='text-align: center !important;'>Dia</th>
                                                            <th style='text-align: center !important;'>Materia</th>
                                                            <th style='text-align: center !important;'>Desde</th>
                                                            <th style='text-align: center !important;'>Hasta</th>
                                                            <th style='text-align: center !important;'>Curso</th> 
                                                        </tr>";

                                                   foreach($datosHorarios as $Horario) {
                                                 $pdf.="<tr>
                                                            <td align='center'>".$Horario['dia']."</td>
                                                            <td align='center'>".$Horario['materia']."</td>
                                                            <td align='center'>".$Horario['curso']."</td>
                                                            <td align='center'>".$Horario['hora_desde']."</td>
                                                            <td align='center'>".$Horario['hora_hasta']."</td>
                                                        </tr>";
                                                   }
                                            

                                            $pdf.="</table><br><br><br><br><br><br>  <table align='center' class='table table-striped'> 

                                                    <tr><td width='40%'><img width='80%' src='images/menu/firma.png'/></td> 
                                                      <td width='20%'>&nbsp;</td>
                                                      <td width='40%'><img width='80%' src='images/menu/firma.png'/></td>
                                                    </tr>

                                                    <tr><td align='center' ><b>$nombre</b></td>
                                                    <td >&nbsp;</td>
                                                   <td align='center'><b>SECRETARÍA</b></td></tr>
                                                    </table>";

                                             $pdf.="</div></body></html>";
 
                                        
                            
                  $mpdfService = $this->get('TFox.mpdfport');
                  $mPDF = $mpdfService->getMpdf();               
                  $mPDF->AddPage('','','1','i','on');
                  $mPDF->WriteHTML($pdf);
                  return new response($mPDF->Output());
                 
                  
                  
    }#end function
    
    
     public function horarios_examen_docenteAction(Request $request)
    {     
            $session=$request->getSession();
            $perfilEst   = $this->container->getParameter('perfilEst');
            $perfilDoc   = $this->container->getParameter('perfilDoc');
            $perfilAdmin = $this->container->getParameter('perfilAdmin'); 
            $perfilEstDoc = $this->container->getParameter('perfilEstDoc'); 
            $perfilEstAdm = $this->container->getParameter('perfilEstAdm'); 
            $perfilDocAdm = $this->container->getParameter('perfilDocAdm');
            $estudiante  = $session->get('nom_usuario'); 

           $UgServices = new UgServices;
               $docentes = $UgServices->cargar_docente_por_carrera(4);
           // echo var_dump($docentes); exit();
           
          return $this->render('TitulacionSisAcademicoBundle:Admin:horarios_examen_docente.html.twig',
    									array(
    				'data' => array('docentes' => $docentes)
    										 )
                              );
        
           
    }#end function
    
    
      public function pdfhorariosexamedocenteAction(Request $request,$id,$nombre)
    {     
        
            $session=$request->getSession();          
                      
            $UgServices    = new UgServices;
          
            $datosHorarios  = $UgServices->Docentes_Horarios_Examen($id);
       
                   $pdf= " <html> 
                                            <body>
                                            <img width='5%' src='images/menu/ug_logo.png'/>
                                            <table align='center'>
                                            <tr>
                                              <td align='center'>
                                                <b> Horario de Examenes</b>
                                              </td>
                                            <tr>
                                            <tr>
                                            <td>
                                              <b>Docente: $nombre</b>
                                            </td>
                                            </tr>
                                            </table>
                                            <div class='col-lg-12'>
                                            <br><br><br><br>
                                            <table class='table table-striped table-bordered' border='1' width='100%' >
                                                     <thead>
                                                        <tr>
                                                                <th colspan='5'   style='text-align: center !important;background-color: #337AB7 !important;color: white!important;'>Periodo  </th>
                                                        </tr>
                                                        <tr>
                                                            <th align='center'>Curso</th>
                                                            <th align='center'>Materia</th>
                                                            <th align='center'>Dia</th>
                                                            <th align='center'>Hora</th>                                                   
                                                        </tr>";

                                                   foreach($datosHorarios as $Horario) {
                                                 $pdf.="<tr>
                                                            <td align='center'>".$Horario['curso.descripcion']."</td>
                                                            <td align='center'>".$Horario['curso.materias.materia.descripcion_materia']."</td>
                                                            <td align='center'>".$Horario['curso.dias.dia.nombre']."</td>
                                                            <td align='center'>".$Horario['curso.horas.hora.nombre']."</td>                                                          
                                                        </tr>";
                                                   }
                                            

                                            $pdf.="</table><br><br><br><br><br><br>  <table align='center' class='table table-striped'> 

                                                    <tr><td width='40%'><img width='80%' src='images/menu/firma.png'/></td> 
                                                      <td width='20%'>&nbsp;</td>
                                                      <td width='40%'><img width='80%' src='images/menu/firma.png'/></td>
                                                    </tr>

                                                    <tr><td align='center' ><b>$nombre</b></td>
                                                    <td >&nbsp;</td>
                                                   <td align='center'><b>SECRETARÍA</b></td></tr>
                                                    </table>";

                                             $pdf.="</div></body></html>";
 
                                        
                            
                  $mpdfService = $this->get('TFox.mpdfport');
                  $mPDF = $mpdfService->getMpdf();               
                  $mPDF->AddPage('','','1','i','on');
                  $mPDF->WriteHTML($pdf);
                  return new response($mPDF->Output());
                 
                  
                  
    }#end function
}