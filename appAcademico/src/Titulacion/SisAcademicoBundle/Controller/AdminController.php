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
            $username    = $request->request->get('user');
            $username    = $request->request->get('pass1');
            $password    = $request->request->get('pass2');
        #llamamos a la consulta del webservice
        $UgServices = new UgServices;
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



}