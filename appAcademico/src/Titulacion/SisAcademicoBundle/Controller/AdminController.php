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
                
            $respuesta = array(
               "Codigo" => $estado ,
               "Mensaje" => $message,
            );
            
          return new Response(json_encode($respuesta));
            
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

         /*if ($session->has("perfil")) 
         {
                if ($session->get('perfil') == $perfilEst || $session->get('perfil') == $perfilEstDoc || $session->get('perfil') == $perfilEstAdm) 
                {*/ 
                    try
                    {
                          $lcFacultad="";
                          $lcCarrera="";
                          $idUsuario="";
                          $idRol="";
                          $idUsuario=$session->get("id_user");
                          $idRol=$perfilAdmin;
                          $idUsuario=9; //USUARIO DE SESION ADMIN CAMBIAR
                          $idRol=1; //ROL DE SESION ADMIN CAMBIAR
                          
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
            /*}
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
     }*/
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

          /*if ($session->has("perfil")) 
         {
                if ($session->get('perfil') == $perfilEst || $session->get('perfil') == $perfilEstDoc || $session->get('perfil') == $perfilEstAdm) 
                {*/ 
                        try
                        {
                              $lcFacultad="";
                              $lcCarrera="";
                              $idUsuario="";
                              $idRol="";
                              $idUsuario=$session->get("id_user");
                              $idRol=$perfilAdmin;
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
           /*}
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
             }*/
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



}