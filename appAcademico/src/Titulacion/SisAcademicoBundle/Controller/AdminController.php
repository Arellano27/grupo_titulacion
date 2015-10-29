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


    public function calendario_carreraAction(Request $request){



        $UgServices   = new UgServices;
        $session=$request->getSession();
        // $id_usuario = $session->get("id_user");
        $id_usuario = 3;
        $id_rol     = $session->get("perfil");

        if ($id_rol > 3) {
           if ($id_rol == 4) {
               $id_rol =  1;
           }elseif ($id_rol == 5) {
               $id_rol =  1;
           }elseif ($id_rol == 6) {
               $id_rol =  2;
           }
        }

        $rsCarrera = $UgServices->getConsultaCarreras($id_usuario,$id_rol);
        // echo '<pre>'; var_dump($rsCarrera); exit();
        $resultadoObjeto = json_encode($rsCarrera);
        $xml_array = json_decode($resultadoObjeto,TRUE);

        $session->set("îdcarrera_calendar",$xml_array["registros"]["registro"]["id_sa_carrera"]);
        $session->set("îdciclo_calendar",$xml_array["registros"]["registro"]["id_sa_ciclo_detalle"]);

        $rsEventos = $UgServices->getConsultaSoloEventos(1); #como parametros enviaremos siempre 1


        return $this->render('TitulacionSisAcademicoBundle:Admin:calendario_carrera.html.twig', array('data' => $rsEventos));
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

                    //echo "<pre>";
                    //var_dump($estado.'-----'.$message);
                    //echo "</pre>";
                    //exit();
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

}