<?php
//consultaNotas
   namespace Titulacion\SisAcademicoBundle\Controller;

    use Symfony\Bundle\FrameworkBundle\Controller\Controller;
    use Symfony\Component\HttpFoundation\Response;
    use Symfony\Component\HttpFoundation\Request;
    use Symfony\Component\HttpFoundation\JsonResponse;
    use Titulacion\SisAcademicoBundle\Helper\UgServices;
    use Titulacion\SisAcademicoBundle\fpdf\fpdf;
    use Titulacion\SisAcademicoBundle\Helper\procesarArchivos;

    class EstudiantesController extends Controller
    {
	
    public function indexAction(Request $request)
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
               if ($session->get('perfil') == $perfilEst || $session->get('perfil') == $perfilEstDoc || $session->get('perfil') == $perfilEstAdm) 
               {
                    try
                    {
                          $lcFacultad="";
                          $lcCarrera="";
                          //$idEstudiante=3;
                          $idEstudiante=$session->get("id_user");
                          //$idEstudiante=3;
                          $idRol=$perfilEst;
                          
                          //$idRol=$session->get("perfil");
                          $Carreras = array();
                          $UgServices = new UgServices;
                          $xml = $UgServices->getConsultaCarreras($idEstudiante,$idRol);
                             
                            if ( is_object($xml))
                            {
                              foreach($xml->registros->registro as $lcCarreras) 
                              {
                                      $lcFacultad=$lcCarreras->id_sa_facultad;
                                      $lcCarrera=$lcCarreras->id_sa_carrera;
                                      $materiaObject = array( 'Nombre' => $lcCarreras->nombre,
                                                                 'Facultad'=>$lcCarreras->id_sa_facultad,
                                                                 'Carrera'=>$lcCarreras->id_sa_carrera,
                                                                 'idCiclo'=>$lcCarreras->id_sa_ciclo_detalle
                                                                );
                                      array_push($Carreras, $materiaObject); 
                              } 

                              $bolCorrecto=1;
                              $cuantos=count($Carreras);
                              if ($cuantos==0)
                              {
                                $bolCorrecto=0;
                              }
                               
                //                $NotifiNol = $UgServices->Notificaciones_No_leidos($idEstudiante);
                //                $MensajesNoL = $UgServices->Eventos_No_Leidos($idEstudiante);
                                
                              return $this->render('TitulacionSisAcademicoBundle:Estudiantes:estudiantes_home.html.twig',array(
                                                      'facultades' =>  $Carreras,
                                                      'idEstudiante'=>$idEstudiante,
                                                      'idFacultad'=>$lcFacultad,
                                                      'idCarrera'=>$lcCarrera,
                                                      'cuantos'=>$cuantos,
                                                      'bolcorrecto'=>$bolCorrecto
                                                   ));
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
                            return $this->render('TitulacionSisAcademicoBundle:Estudiantes:estudiantes_home.html.twig',array(
                                                      'facultades' =>  $Carreras,
                                                      'idEstudiante'=>$idEstudiante,
                                                      'idFacultad'=>$lcFacultad,
                                                      'idCarrera'=>$lcCarrera,
                                                      'cuantos'=>$cuantos,
                                                      'bolcorrecto'=>$bolCorrecto
                                                   ));

                            //return $this->render('TitulacionSisAcademicoBundle:Estudiantes:error.html.twig');
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
           else
           {
                $this->get('session')->getFlashBag()->add(
                                      'mensaje',
                                      'Los datos ingresados no son válidos'
                                  );
                    return $this->redirect($this->generateUrl('titulacion_sis_academico_homepage'));
            }
        }
        
        public function MatriculacionAction(Request $request)
        {

         

            $session=$request->getSession();
            $perfilEst   = $this->container->getParameter('perfilEst');
            $perfilDoc   = $this->container->getParameter('perfilDoc');
            $perfilAdmin = $this->container->getParameter('perfilAdmin'); 
            $perfilEstDoc = $this->container->getParameter('perfilEstDoc'); 
            $perfilEstAdm = $this->container->getParameter('perfilEstAdm'); 
            $perfilDocAdm = $this->container->getParameter('perfilDocAdm');

           if ($session->has("perfil")) {
               if($session->get('perfil') == $perfilEst || $session->get('perfil') == $perfilEstDoc || $session->get('perfil') == $perfilEstAdm){
                     $matricula_dis=array();
                     
                   
                 try
                {
                     $estudiante  = $session->get('nom_usuario');
                     $idEstudiante=$session->get('id_user');
                      $lcFacultad="";
                      $lcCarrera="";
                       $Carreras_inscribir = array();
                       $idRol=$perfilEst;
                       $lnCuantos=0;
                       //$idEstudiante=17;
                       //$estudiante='Jeferson Bohorquez';
                        $UgServices = new UgServices;
                        $xml = $UgServices->getConsultaCarreras_Matricula($idEstudiante);
                        
                        if ( is_object($xml))
                        {
                          foreach($xml->registros->registro as $lcCarreras) 
                            {
                                  //$lcFacultad=$lcCarreras->id_sa_facultad;
                                  $lcCarrera=$lcCarreras->idCarrera;
                                  $nombreCarrera=$lcCarreras->nombreCarrera;
                                  //$nombreCarrera=$lcCarreras->nombre; // cambiar 
                                  

                                 $materiaObject = array( 'Nombre' => $nombreCarrera,
                                                             'Facultad'=>"",
                                                             'Carrera'=>$lcCarrera
                                                            );

                                  array_push($Carreras_inscribir, $materiaObject); 
                              }
                            }
                          else
                          {
                            throw new \Exception('Un error');
                          }
                          
                    }catch (\Exception $e)
                        {
                         
                          //return $this->render('TitulacionSisAcademicoBundle:Estudiantes:error.html.twig');
                        }

                        $lnCuantos=count($Carreras_inscribir);
                   
                   return $this->render('TitulacionSisAcademicoBundle:Estudiantes:estudiantes_carrerasmatricula.html.twig',array(
                                                    'nomEstudiante' =>  $estudiante,
                                                    'idEstudiante' => $idEstudiante,
                                                    'carreras_inscribir'=>$Carreras_inscribir,
                                                    'lnCuantos'=>$lnCuantos

                                                 ));

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
        
        public function DeudasAction()
        {
            return $this->render('TitulacionSisAcademicoBundle:Estudiantes:estudiantes_deudas.html.twig',compact("notas_act"));
        }
	
     public function listarmateriasAction(Request $request)
        {
        $session=$request->getSession();
        $perfilEst   = $this->container->getParameter('perfilEst');
        $perfilDoc   = $this->container->getParameter('perfilDoc');
        $perfilAdmin = $this->container->getParameter('perfilAdmin'); 
        $perfilEstDoc = $this->container->getParameter('perfilEstDoc'); 
        $perfilEstAdm = $this->container->getParameter('perfilEstAdm'); 
        $perfilDocAdm = $this->container->getParameter('perfilDocAdm');


           if ($session->has("perfil")) {
                      $UgServices = new UgServices;
        
               if($session->get('perfil') == $perfilEst || $session->get('perfil') == $perfilEstDoc || $session->get('perfil') == $perfilEstAdm){
                     
                try
                {
                     $idEstudiante  = $request->request->get('idEstudiante');
                     $idCarrera  = $request->request->get('idCarrera');
                     $idIndica  = $request->request->get('idIndica');
                     $idFacultad  = "";
                     $listaMaterias=array();
                     $materiaObject=array();

                     if ($idIndica=='nh')
                     {   
                          
                         // $idEstudiante  = 17;
                         // $idCarrera  = 4;
                           $xml1 = $UgServices->getConsultaNotas_nh($idFacultad,$idCarrera,$idEstudiante);
                            
                          if ( is_object($xml1))
                          {
                              foreach($xml1 as $xml)
                              {   
                                 foreach($xml->ciclo as $Periodo) {
                                    //$Periodo->materias->ciclo;
                                      $PeriodoCiclo=$Periodo->anio."-".$Periodo->nombre;
                                       $materiaObject = array(
                                          'PeriodoCiclo' => $PeriodoCiclo,
                                           'Materias'=>array(),
                                           'Parciales'=>array(),
                                           'DetalleParciales'=>array(),
                                           'CantidadParcial'=>array(),
                                           'CantidadDetalle'=>array() 
                                    ); 
                                        //ENCABEZADO-PARCIALES
                                       $lnCuantos=array();
                               foreach($Periodo->materias->materia->parciales->parcial as $parciales) 
                               {
                                      $NombreParcial=$parciales->parcial;  
                                      $lsparciales=array('Parcial'=> $NombreParcial);
                                      array_push($materiaObject["Parciales"],$lsparciales);
                                     
                                       foreach($parciales->detalles->detalle as $detalleparciales) 
                                       {
                                         
                                          $NombreDetalle=$detalleparciales->nombre;  
                                          $lsparcialesdetalle=array(
                                                                    'NombreDetalle'=> $NombreDetalle
                                                                   );
                                          array_push($materiaObject["DetalleParciales"],$lsparcialesdetalle);
                                          if (in_array($NombreDetalle, $lnCuantos)) {
                                                }
                                          else
                                          {
                                            array_push($lnCuantos,(string) $NombreDetalle);
                                          }
                                       }
                               }

                                  $lnCuantos=count($lnCuantos); // Cantidad de Deatlle
                                  

                                  $lnParciales=count($materiaObject["Parciales"]); // Cantidad de Parciales 


                                  $lnParciales=$lnParciales*$lnCuantos;
                                  $lnParciales=$lnParciales+6;

                                  
                                     array_push($materiaObject["CantidadParcial"],$lnParciales);
                                     array_push($materiaObject["CantidadDetalle"],$lnCuantos);

                                     
                            
                                       
                                       $lscursos=array();
                                      foreach($Periodo->materias->materia as $inscripcion) {
                                          $Nivel=$inscripcion->nivel;  
                                          $Nombre=$inscripcion->nombre;
                                          $Veces=$inscripcion->veces;
                                          $Nota1=0;
                                          $Nota1A="";
                                          $Nota1E="";
                                          $Nota2=0;
                                          $Nota2A="";
                                          $Nota2E="";
                                          $Supenso=$inscripcion->suspenso;
                                          $Promedio=$inscripcion->promedio;
                                          $Estado=$inscripcion->estadoMateria;
                                          $lscursos=array('Nivel'=> $Nivel,
                                                          'Materia'=> $Nombre,
                                                          'Veces'=> $Veces,
                                                          'Nota1'=>$Nota1,
                                                          'Nota1A'=>$Nota1A,
                                                          'Nota1E'=>$Nota1E,
                                                          'Nota2'=>$Nota2,
                                                          'Nota2A'=>$Nota2A,
                                                          'Nota2E'=>$Nota2E,
                                                          'Suspenso'=>$Supenso,
                                                          'Promedio'=>$Promedio,
                                                          'Estado'=>$Estado,
                                                          'Parcial'=>array());
                                            $listadetalle=array();

                                            foreach($inscripcion->parciales->parcial as $detalleparciales) 
                                               {
                                                 
                                                  $Suma=$detalleparciales->suma; 
                                                  $listadetalle=array('Suma'=>$Suma,
                                                                        'calificacion'=>array()) ;
                                                  foreach($detalleparciales->detalles->detalle as $notas) 
                                                    {
                                                      array_push($listadetalle['calificacion'],$notas->calificacion);
                                                    }
                                                    array_push($lscursos["Parcial"], $listadetalle);
                                                  
                                                  //array_push($detallenotas,$listadetalle);

                                               }
                                               
                                                
                                          
                                                  array_push($materiaObject["Materias"], $lscursos);

                                              }

                                      array_push($listaMaterias, $materiaObject);
                                        
                                   }   
                              } 
                             }
                             else
                             {
                                throw new \Exception('Un error');
                             }
                         }

                        
                         
                            if ($idIndica=='na') //NOTAS ACTUALES -JOSELINE
                            {
                                  //$idEstudiante  = 17;
                                  //$idCarrera  = 4;
                                  $xml1 = $UgServices->getConsultaNotas_act($idFacultad,$idCarrera,$idEstudiante);

                                  if ( is_object($xml1))
                                  {
                                      foreach($xml1->PX_Salida as $xml)
                                       {  
                                          
                                            $lscursos=array();
                                            foreach($xml->materias as $actual) 
                                            {
                                              
                                               $cicloAnio=$actual->materia->cicloAnio;
                                               $ciclo=$actual->materia->ciclo;
                                               $materiaObject = array(  'CicloAnio' => $cicloAnio,  
                                                                        'Ciclo' => $ciclo,
                                                                        'Materias'=>array(),
                                                                        'Parciales'=>array(),
                                                                        'DetalleParciales'=>array(),
                                                                        'CantidadParcial'=>array(),
                                                                        'CantidadDetalle'=>array() 
                                                                      );
                                                //ENCABEZADO-PARCIALES
                                                 $lnCuantos=array();
                                               foreach($actual->materia->parciales->parcial as $parciales) 
                                               {
                                                      $NombreParcial=$parciales->parcial;  
                                                      $lsparciales=array('Parcial'=> $NombreParcial);
                                                      array_push($materiaObject["Parciales"],$lsparciales);
                                                       foreach($actual->materia->parciales->parcial->detalles->detalle as $detalleparciales) 
                                                       {
                                                                  $NombreDetalle=$detalleparciales->nombre; 
                                                                  $lsparcialesdetalle=array('NombreDetalle'=> $NombreDetalle);
                                                                  array_push($materiaObject["DetalleParciales"],$lsparcialesdetalle);
                                                                  if (in_array($NombreDetalle, $lnCuantos)) {
                                                                        }
                                                                  else
                                                                  {
                                                                    array_push($lnCuantos,(string) $NombreDetalle);
                                                                  }
                                                       }
                                                       
                                               }

                                                $lnCuantos=count($lnCuantos)+6;
                                                $lnParciales=count($materiaObject["Parciales"])+6;

                                           
                                                 array_push($materiaObject["CantidadParcial"],$lnParciales);
                                                 array_push($materiaObject["CantidadDetalle"],$lnCuantos);



                                                  /*'CantidadParcial'=>array(),
                                                  'CantidadDetalle'=>array()
                                                 $cuentadetalle=count(array_unique($materiaObject["DetalleParciales"]));
                                                 $cuentaparcial=count(array_unique($materiaObject["Parciales"]));
                                                 array_push($materiaObject["CantidadParcial"],$cuentaparcial);
                                                 array_push($materiaObject["CantidadDetalle"],$cuentadetalle);*/

                                               //DETALLE-MATERIA(OJO EL NOMBRE DE LAS MATERIAS EN REALIDAD VIENE DE ENCABEZADO)
                                               foreach($actual->materia as $materias) 
                                               {
                                                           $Nivel=$materias->nivel; 
                                                            $Nombre=$materias->nombre;
                                                            $Veces=$materias->veces;
                                                            $Supenso=$materias->suspenso;
                                                            $Promedio=$materias->promedio;
                                                            $Estado=$materias->estadoMateria;
                                                            $lscursos=array('Nivel'=> $Nivel,
                                                                            'Materia'=> $Nombre,
                                                                            'Veces'=> $Veces,
                                                                            'Suspenso'=>$Supenso,
                                                                            'Promedio'=>$Promedio,
                                                                            'Estado'=>$Estado,
                                                                            'Parcial'=>array());
                                                            $listadetalle=array();
                                                            foreach($materias->parciales->parcial as $detalleparciales) 
                                                            {
                                                                $Suma=$detalleparciales->suma; 
                                                                $listadetalle=array('Suma'=>$Suma,
                                                                                    'Calificacion'=>array()
                                                                                    ) ;
                                                                foreach($detalleparciales->detalles->detalle as $notas) 
                                                                  {
                                                                    array_push($listadetalle['Calificacion'],$notas->calificacion);
                                                                  }
                                                                  array_push($lscursos["Parcial"], $listadetalle);
                                                            }
                                                            array_push($materiaObject["Materias"],$lscursos);        
                                               }
                                               array_push($listaMaterias, $materiaObject);
                                             }
                                        }
                                    }
                                    else
                                    {
                                        throw new \Exception('Un error');
                                    }
                            }
                            
                          if ($idIndica=='re') //Resumen de Asistencias 
                            {
                                $bolCorrecto=1;
                            } 
                                                                       
                   $bolCorrecto=1;
                   $cuantos=count($listaMaterias);
                   if($cuantos==0 || $idIndica=='re'  )
                   {
                    $bolCorrecto=0;
                   }

                    
                                                                                                                                           
                    return $this->render('TitulacionSisAcademicoBundle:Estudiantes:listarmaterias.html.twig',
                                          array('listaMaterias'=>$listaMaterias,
                                               'indica'=>$idIndica ,
                                               'bolcorrecto'=>$bolCorrecto) 
                                          );
                        
            }
            catch (\Exception $e)
            {
               
            } 
               
             }
            
             else{
                      $this->get('session')->getFlashBag()->add(
                                    'mensaje',
                                    'Los datos ingresados no son válidos'
                                );
                        return $this->redirect($this->generateUrl('dayscript_mi_claro_homepage'));
                   }
           }else{
                $this->get('session')->getFlashBag()->add(
                                      'mensaje',
                                      'Los datos ingresados no son válidos'
                                  );
                    return $this->redirect($this->generateUrl('titulacion_sis_academico_homepage'));
        }
        }
        
        public function menuderechoAction(Request $request)
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
                if($session->get('perfil') == $perfilEst || $session->get('perfil') == $perfilEstDoc || $session->get('perfil') == $perfilEstAdm)
                {
                   $idEstudiante  = $request->request->get('idEstudiante');
                   $idCarrera  = $request->request->get('idCarrera');
                   $idIndica  = $request->request->get('idIndica');
                   //$idEstudiante  = 17;
                   //$idCarrera  = 4;
                   //$ciclo=1;
                   $idCiclo  = $request->request->get('idCiclo');
                   $anio=date("Y");
                   $asistencia = array();
                    try
                    {
                        $UgServices = new UgServices;
                        $xml = $UgServices->getConsultaAlumno_Asistencia($idEstudiante,$idCarrera,$idCiclo,$anio);

                        if ( is_object($xml))
                        {
                          foreach($xml->PX_SALIDA->PorcentjeAsistencias->materia as $lcAsistencia) 
                              {
                                  $valAsistencia= (int) $lcAsistencia->PorcentajeAsistencia;
                                  $materiaObject = array( 'Materia' => (string) $lcAsistencia->materia,
                                                          'Asistencia'=>$valAsistencia
                                                         );
                                      array_push($asistencia, $materiaObject); 
                               } 
                                $bolCorrecto=1;  

                                 return $this->render('TitulacionSisAcademicoBundle:Estudiantes:menuderecho.html.twig',
                                        array('idCarrera'=>$idCarrera,
                                              'asistencia'=>$asistencia,
                                              'indica'=>$idIndica,
                                              'bolCorrecto'=>$bolCorrecto));
                        }
                        else
                        {
                          throw new \Exception('Un error');
                        }
                        
                  }
                  catch (\Exception $e)
                  {
                        $bolCorrecto=0;  
                        //return $this->render('TitulacionSisAcademicoBundle:Estudiantes:error.html.twig');
                        return $this->render('TitulacionSisAcademicoBundle:Estudiantes:menuderecho.html.twig',
                                        array('idCarrera'=>$idCarrera,
                                              'asistencia'=>$asistencia,
                                              'indica'=>$idIndica,
                                              'bolCorrecto'=>$bolCorrecto));
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
         else
         {
                $this->get('session')->getFlashBag()->add(
                                      'mensaje',
                                      'Los datos ingresados no son válidos'
                                  );
                    return $this->redirect($this->generateUrl('titulacion_sis_academico_homepage'));
         }
       }
       
        //  ANULACIONES
    public function anulacion_materiasAction(Request $request)
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
               if ($session->get('perfil') == $perfilEst || $session->get('perfil') == $perfilEstDoc || $session->get('perfil') == $perfilEstAdm) 
               {
                    try
                    {
                          $lcFacultad="";
                          $lcCarrera="";
                          //$idEstudiante=3;
                          $idEstudiante=$session->get("id_user");
                          //$idEstudiante=3;
                          $idRol=$perfilEst;
                          
                          //$idRol=$session->get("perfil");
                          $Carreras = array();
                          $UgServices = new UgServices;
                          $xml = $UgServices->getConsultaCarreras($idEstudiante,$idRol);
                              
                            if ( is_object($xml))
                            {
                              foreach($xml->registros->registro as $lcCarreras) 
                              {
                                      $lcFacultad=$lcCarreras->id_sa_facultad;
                                      $lcCarrera=$lcCarreras->id_sa_carrera;
                                      $materiaObject = array( 'Nombre' => $lcCarreras->nombre,
                                                                 'Facultad'=>$lcCarreras->id_sa_facultad,
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
                              return $this->render('TitulacionSisAcademicoBundle:Estudiantes:estudiantes_anulacion_materias.html.twig',array(
                                                      'facultades' =>  $Carreras,
                                                      'idEstudiante'=>$idEstudiante,
                                                      'idFacultad'=>$lcFacultad,
                                                      'idCarrera'=>$lcCarrera,
                                                      'cuantos'=>$cuantos,
                                                      'bolcorrecto'=>$bolCorrecto
                                                   ));
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
                            return $this->render('TitulacionSisAcademicoBundle:Estudiantes:estudiantes_anulacion_materias.html.twig',array(
                                                      'facultades' =>  $Carreras,
                                                      'idEstudiante'=>$idEstudiante,
                                                      'idFacultad'=>$lcFacultad,
                                                      'idCarrera'=>$lcCarrera,
                                                      'cuantos'=>$cuantos,
                                                      'bolcorrecto'=>$bolCorrecto
                                                   ));

                            //return $this->render('TitulacionSisAcademicoBundle:Estudiantes:error.html.twig');
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
           else
           {
                $this->get('session')->getFlashBag()->add(
                                      'mensaje',
                                      'Los datos ingresados no son válidos'
                                  );
                    return $this->redirect($this->generateUrl('titulacion_sis_academico_homepage'));
            }
        }
        
        public function anulacion_materias_2Action(Request $request)
        {
             
            $session=$request->getSession();
            $perfilEst   = $this->container->getParameter('perfilEst');
            $perfilDoc   = $this->container->getParameter('perfilDoc');
            $perfilAdmin = $this->container->getParameter('perfilAdmin'); 
            $perfilEstDoc = $this->container->getParameter('perfilEstDoc'); 
            $perfilEstAdm = $this->container->getParameter('perfilEstAdm'); 
            $perfilDocAdm = $this->container->getParameter('perfilDocAdm');
            $idEstudiante  = $request->request->get('idEstudiante');
            $idCarrera  = $request->request->get('idCarrera');
            $carrera  = $request->request->get('carrera');
             $solicitudenProceso=0;

            
           if ($session->has("perfil")) {
               if($session->get('perfil') == $perfilEst || $session->get('perfil') == $perfilEstDoc || $session->get('perfil') == $perfilEstAdm){
                     $matricula_dis=array();

                  $estudiante  = $session->get('nom_usuario');
                   $banderaMatricula=0;
                    $UgServices = new UgServices;
                    $Mensaje="";
                    $Idciclo="";
                    $CicloMatricula="";
                    $anio="";  
                    $ciclo="";
                    $parveces='2';
                    $Materias_inscribir = array();                                           
                 try
                {
                      $xml2 = $UgServices->getConsultaDatos_Turno($idEstudiante,$idCarrera);

                       if ( is_object($xml2))
                          {
                              foreach($xml2->registros->registro as $datos)
                               {  
                                  //$banderaMatricula=(int) $datos->valor;
                                  //$banderaMatricula=5;
                                  $Mensaje=(string) $datos->mensaje;
                                  $Idciclo=(string) $datos->id_ciclo;
                                  $ciclo=(string) $datos->ciclo_descripcion;
                                  $anio=(string) $datos->anio;
                               }
                              
                          }



                      $lcFacultad="";
                      $lcCarrera="";
                      
                      $idRol=$perfilEst;

                      $CicloMatricula=$anio." - Ciclo ".$ciclo; 

                      $xml3 = $UgServices->getConsultaRequisitos_Anula($idEstudiante,$idCarrera,$Idciclo);
                      
                      if ( is_object($xml3))
                          {
                              foreach($xml3->parametrosSalida as $datosValida)
                               {  
                                  $banderaMatricula=(int) $datosValida->PI_ESTADO;
                                  if ($banderaMatricula==1)
                                  {
                                    $banderaMatricula=4;
                                  }
                                  else
                                  {
                                    $banderaMatricula=0;
                                  }
                               }
                              
                          }


                        if ($banderaMatricula==4)
                        {

                            

                             $xml1 = $UgServices->getConsultaRegistro_Matricula($idEstudiante,$idCarrera,$Idciclo);
                             

                          //obtenet el ciclo de matriculacion del XML
                           if ( is_object($xml1))
                              {
                                        foreach($xml1->PX_SALIDA as $xml)
                                         {  

                                              foreach($xml->registros as $lsciclo) 
                                                {
                                                      $solicitudenProceso=$lsciclo->anulacion;
                                                      foreach($lsciclo->registro as $lsdetallematerias) 
                                                      {
                                                              $Nombre=$lsdetallematerias->nombre;
                                                              $Veces=$lsdetallematerias->veces;
                                                              $IdMateria=$lsdetallematerias->id_sa_materia;
                                                              $Nivel=$lsdetallematerias->nivel;
                                                              $Curso=$lsdetallematerias->curso;
                                                              $Mensaje=$lsdetallematerias->estado_materia;
                                                              $materiaObject=array('Nombre'=>$Nombre,
                                                                                      'Veces'=>$Veces,
                                                                                      'IdMateria'=>$IdMateria,
                                                                                      'Nivel'=>$Nivel,
                                                                                      'Curso'=>$Curso,
                                                                                      'mensaje'=>$Mensaje);
                                                              
                                                                array_push($Materias_inscribir, $materiaObject); 
                                                        }
                                                }
                                          }
                              }


                        }
                        else
                        {
                           $banderaMatricula=0;
                        }
                        
                          
                    }catch (\Exception $e)
                        {
                         $banderaMatricula=0;
                          //return $this->render('TitulacionSisAcademicoBundle:Estudiantes:error.html.twig');
                        }
                     
                   
                   return $this->render('TitulacionSisAcademicoBundle:Estudiantes:estudiantes_anulacion_materias2.html.twig',array(
                                                    'matricula_dis' =>  $matricula_dis,
                                                    'estudiante' => $estudiante,
                                                    'idEstudiante' => $idEstudiante,
                                                    'idCarrera' => $idCarrera,
                                                    'banderaMatricula'=> $banderaMatricula ,
                                                    'Mensaje'=>$Mensaje,
                                                    'Materias_inscribir'=>$Materias_inscribir,
                                                    'cicloencurso'=>$CicloMatricula,
                                                    'idciclo'=>$Idciclo,
                                                    'parveces'=>$parveces,
                                                    'solicitudenProceso'=>$solicitudenProceso,
                                                    'carrera'=>$carrera 

                                                 ));

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
        
         public function anulacion_materias_3Action(Request $request)
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
            $idCiclo  = $request->request->get('idciclo');


           if ($session->has("perfil")) {
               if($session->get('perfil') == $perfilEst || $session->get('perfil') == $perfilEstDoc || $session->get('perfil') == $perfilEstAdm){
                     $matricula_dis=array();
                     
                 try
                {
                    

                    $datosCuenta=""; 
                     foreach ($materias as $key => $value) {
                          $datosCuenta.= "<item>
                                          <id_solicitud_detalle></id_solicitud_detalle>
                                            <identificador>" . $value['idMateria'] . "</identificador>
                                            <aprobada></aprobada>
                                            <opcion>A</opcion>
                                          </item> 
                                          "; 
                      }
                      $xmlFinal="
                                <cabecera>
                                  <id_solicitud></id_solicitud> 
                                  <estado></estado>
                                  <usuario_estudiante>".$idEstudiante."</usuario_estudiante>
                                  <carrera>".$idCarrera."</carrera>
                                  <tipo_solicitud>82</tipo_solicitud> 
                                  <usuario_registro>".$idEstudiante."</usuario_registro>
                                </cabecera>
                                <detalle> 
                                    ".$datosCuenta." 
                                </detalle>";



                     //$estudiante  = $session->get('nom_usuario');
                      $Mensaje="";
                      $Estado=0;
                      $UgServices = new UgServices;
                      $xml2 = $UgServices->setSolicitudAnula($xmlFinal);

                       
                       if (is_object($xml2))
                          {
                              foreach($xml2->parametrosSalida as $datos)
                               {  
                                  $Mensaje=(string) $datos->PV_MENSAJE;
                                  $Estado=(int) $datos->PI_ESTADO;
                               }
                                $session=$request->getSession();
                              $Email= $session->get('mail');
                              $Nombre = $session->get('nom_usuario');
                                                 $mailer    = $this->container->get('mailer');
                                      $transport = \Swift_SmtpTransport::newInstance('smtp.gmail.com',465,'ssl')
                                                  ->setUsername('titulacion.php@gmail.com')
                                                  ->setPassword('sc123456');
                                     //$mailer  = \Swift_Mailer($transport);
                                      $message = \Swift_Message::newInstance('test')
                                                  ->setSubject("Anulación de Materia Exitosa")
                                                  ->setFrom('titulacion.php@gmail.com','Universidad de Guayaquil')
                                                  ->setTo($Email)
                                                  ->setBody("$Nombre usted ha anulado con exito las Materias");
                                      // ->setBody($this->renderView('TitulacionSisAcademicoBundle:Admin:Comtraseña.html.twig'),'text/html', 'utf8');
                                      $this->get('mailer')->send($message);   
                               
                              
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

 //Proceso de Matriculacion 
       public function procesomatriculaAction(Request $request)
        {
             
            $session=$request->getSession();
            $perfilEst   = $this->container->getParameter('perfilEst');
            $perfilDoc   = $this->container->getParameter('perfilDoc');
            $perfilAdmin = $this->container->getParameter('perfilAdmin'); 
            $perfilEstDoc = $this->container->getParameter('perfilEstDoc'); 
            $perfilEstAdm = $this->container->getParameter('perfilEstAdm'); 
            $perfilDocAdm = $this->container->getParameter('perfilDocAdm');
            $idEstudiante  = $request->request->get('idEstudiante');
            $idCarrera  = $request->request->get('idCarrera');
            $carrera  = $request->request->get('carrera');
            $maxmaterias=7;


           if ($session->has("perfil")) {
               if($session->get('perfil') == $perfilEst || $session->get('perfil') == $perfilEstDoc || $session->get('perfil') == $perfilEstAdm){
                     $matricula_dis=array();


                 $Materias_inscribir = array();
                 try
                {
                     $estudiante  = $session->get('nom_usuario');
                     $banderaMatricula=0;
                      $UgServices = new UgServices;
                      $Mensaje="";
                      $Idciclo="";
                      $CicloMatricula="";
                      //$idEstudiante=12;
                      //$idCarrera=4;
                      $anio="";
                      $xml2 = $UgServices->getConsultaDatos_Turno($idEstudiante,$idCarrera);
                      $ciclo="";
                      $Idciclo="";

                      //$idEstudiante=12;
                       
                      

                       if ( is_object($xml2))
                          {
                              foreach($xml2->registros->registro as $datos)
                               {  
                                  $banderaMatricula=(int) $datos->valor;
                                  $Mensaje=(string) $datos->mensaje;
                                  $Idciclo=(string) $datos->id_ciclo;
                                  $ciclo=(string) $datos->ciclo_descripcion;
                                  $anio=(string) $datos->anio;
                                  //$Idciclo="18";
                                  //$banderaMatricula=5; 
                               }
                              
                          }  

                      $lcFacultad="";
                      $lcCarrera="";
                      $idRol=$perfilEst;
                      $CicloMatricula=$anio." - Ciclo ".$ciclo; //'2015 Ciclo 2';
                      $pdf= " ";
                      $banderaarchivos=0;
                     // $banderaMatricula=7;
                      
                      if ($banderaMatricula==1)
                      {
                         $UgServices = new UgServices;

                          $xml1 = $UgServices->getConsultaDatos_Matricula($idEstudiante,$idCarrera,$Idciclo);

                          //obtenet el ciclo de matriculacion del XML
                           if ( is_object($xml1))
                              {
                                        foreach($xml1->PX_SALIDA as $xml)
                                         {  

                                              foreach($xml->registros as $lsciclo) 
                                                {

                                                      foreach($lsciclo->registro as $lsdetallematerias) 
                                                      {
                                                              $Nombre=$lsdetallematerias->nombre;
                                                              $Veces=$lsdetallematerias->veces;
                                                              $IdMateria=$lsdetallematerias->id_sa_materia;
                                                              $Nivel=$lsdetallematerias->nivel;
                                                              $materiaObject=array('Nombre'=>$Nombre,
                                                                                      'Veces'=>$Veces,
                                                                                      'IdMateria'=>$IdMateria,
                                                                                      'Nivel'=>$Nivel,
                                                                                      'Cursos'=>array());
                                                              foreach($lsdetallematerias->Paralelos->Paralelo as $cursos) 
                                                                {

                                                                    $Nombrecurso=$cursos->curso;
                                                                    $Idcurso=$cursos->curso;
                                                                    $Registrados=$cursos->cuposRegistrados;
                                                                    $Maximo=$cursos->cupoMaximo;
                                                                    $Idmatpar=$cursos->idMateriaParalelo;
                                                                    $arrcurso=array('Curso'=>$Nombrecurso,
                                                                                    'Registrados'=>$Registrados,
                                                                                    'Maximo'=>$Maximo,
                                                                                    'Idcurso'=>$Idcurso,
                                                                                    'Idmatpar'=>$Idmatpar,
                                                                                    );
                                                                    
                                                                     array_push($materiaObject['Cursos'], $arrcurso);
                                                                }
                                                                array_push($Materias_inscribir, $materiaObject); 
                                                        }
                                                }
                                          }
                              }
                          else
                          {
                            throw new \Exception('Un error');
                          }
                        }
                        if ($banderaMatricula==4)
                        {


                            $UgServices = new UgServices;
                            
                            $xml1 = $UgServices->getConsultaRegistro_Matricula($idEstudiante,$idCarrera,$Idciclo);
                       
                          //obtenet el ciclo de matriculacion del XML
                           if ( is_object($xml1))
                              {
                                        foreach($xml1->PX_SALIDA as $xml)
                                         {  


                                              foreach($xml->registros as $lsciclo) 
                                                {


                                                      foreach($lsciclo->registro as $lsdetallematerias) 
                                                      {
                                                             
                                                            


                                                              $Nombre=$lsdetallematerias->nombre;
                                                              $Veces=$lsdetallematerias->veces;
                                                              $IdMateria=$lsdetallematerias->id_sa_materia;
                                                              $Nivel=$lsdetallematerias->nivel;
                                                              $Curso=$lsdetallematerias->curso;
                                                              $materiaObject=array('Nombre'=>$Nombre,
                                                                                      'Veces'=>$Veces,
                                                                                      'IdMateria'=>$IdMateria,
                                                                                      'Nivel'=>$Nivel,
                                                                                      'Curso'=>$Curso);
                                                            
                                                          
                                                              
                                                                array_push($Materias_inscribir, $materiaObject); 
                                                        }

                                                }
                                          }
                              }
                              else
                              {
                                throw new \Exception('Un error');
                              }


                        }
                        
                          
                    }catch (\Exception $e)
                        {
                         $banderaMatricula=0;
                          //return $this->render('TitulacionSisAcademicoBundle:Estudiantes:error.html.twig');
                        }


                     
                   return $this->render('TitulacionSisAcademicoBundle:Estudiantes:estudiantes_matriculacion2.html.twig',array(
                                                    'matricula_dis' =>  $matricula_dis,
                                                    'estudiante' => $estudiante,
                                                    'idEstudiante' => $idEstudiante,
                                                    'idCarrera' => $idCarrera,
                                                    'banderaMatricula'=> $banderaMatricula ,
                                                    'Mensaje'=>$Mensaje,
                                                    'Materias_inscribir'=>$Materias_inscribir,
                                                    'cicloencurso'=>$CicloMatricula,
                                                    'idciclo'=>$Idciclo,
                                                    'carrera'=>$carrera,
                                                    'banderaarchivos'=>$banderaarchivos,
                                                    'maxmaterias'=>$maxmaterias
                                                 ));

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


  public function estudiantes_grabar_matriculaAction(Request $request)
        {
           
           $respuesta= new Response("",200);
           $materias  = $request->request->get('arrMaterias');

           $idEstudiante  = $request->request->get('idEstudiante');
           $idCarrera  = $request->request->get('idCarrera');
           $idCiclo  = $request->request->get('idciclo');
           $noPrimerasBase=0;
           $noPrimerasPantalla=0;
           $lnCuantassel=0;
           $maxmaterias=7;
          $UgServices = new UgServices;
           $xmlPendientes = $UgServices->getConsultaDatos_Matricula($idEstudiante,$idCarrera,$idCiclo);
                          
            //obtenet el ciclo de matriculacion del XML
             if ( is_object($xmlPendientes))
                {
                          foreach($xmlPendientes->PX_SALIDA as $xml)
                           {  

                                foreach($xml->registros as $lsciclo) 
                                  {

                                        foreach($lsciclo->registro as $lsdetallematerias) 
                                        {
                                                $Veces=(int) $lsdetallematerias->veces;
                                                if ($Veces>1)
                                                {
                                                  $noPrimerasBase=$noPrimerasBase+1;
                                                }
                                         }
                                  }
                            }
                  }

          foreach ($materias as $key => $value) {
              $Veces=(int) $value['Veces'];
              if ($Veces>1)
              {
                $noPrimerasPantalla=$noPrimerasPantalla+1;
              }
              $lnCuantassel=$lnCuantassel+1;
          }
          $BanderaGrabar=1;
          if ($noPrimerasPantalla!=$noPrimerasBase)
          {
            $BanderaGrabar=2;
          }
          if ($lnCuantassel>$maxmaterias)
          {
            $BanderaGrabar=3;
          }
          if ($BanderaGrabar==1)
          {
                $datosCuenta=""; 

               foreach ($materias as $key => $value) {
                    $datosCuenta.= "<id_materia_paralelo>" . $value['Idcurso'] . "</id_materia_paralelo>"; 
                }
                  $xmlFinal="
                            <matricula>
                               <matriculacion>
                                   <idEstudiante>".$idEstudiante."</idEstudiante>
                                   <idCarrera>".$idCarrera."</idCarrera>
                                   <idCiclo>".$idCiclo."</idCiclo>
                                   <item>
                                         ".$datosCuenta." 
                                   </item>
                                </matriculacion>
                             </matricula>";

                 
                  $xml = $UgServices->setMatricula_Estudiante($xmlFinal);

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
            }
            if($BanderaGrabar==2)
            {
              $Estado=12;
              $Mensaje="Las materias que fueron vistas y no aprobadas en ciclos anteriores <br/> deben ser seleccionadas en el ciclo actual";
            }

            if($BanderaGrabar==3)
            {
              $Estado=13;
              $Mensaje="El máximo de materias a matricularse es de : ".$maxmaterias;
            }


            $arrayProceso=array();
            $arrayProceso['codigo_error']=$Estado;
            $arrayProceso['mensaje']=$Mensaje;
            $jarray=json_encode($arrayProceso);         
            $respuesta->setContent($jarray);
              if($Estado==1)
                {  
                            $session=$request->getSession();
                            $Email= $session->get('mail');
                            $Nombre = $session->get('nom_usuario');
                            $mailer    = $this->container->get('mailer');
                            $transport = \Swift_SmtpTransport::newInstance('smtp.gmail.com',465,'ssl')
                                     ->setUsername('titulacion.php@gmail.com')
                                     ->setPassword('sc123456');
                            $message = \Swift_Message::newInstance('test')
                                     ->setSubject("Registro de Materias Exitosa")
                                     ->setFrom('titulacion.php@gmail.com','Universidad de Guayaquil')
                                     ->setTo($Email)
                                     ->setBody("$Nombre usted a completado su registro de materias con exito");                  
                           $this->get('mailer')->send($message);
                  }   
            return $respuesta;
        }

  public function presenta_matriculaAction(Request $request)
    {     
            $session=$request->getSession();
            $perfilEst   = $this->container->getParameter('perfilEst');
            $perfilDoc   = $this->container->getParameter('perfilDoc');
            $perfilAdmin = $this->container->getParameter('perfilAdmin'); 
            $perfilEstDoc = $this->container->getParameter('perfilEstDoc'); 
            $perfilEstAdm = $this->container->getParameter('perfilEstAdm'); 
            $perfilDocAdm = $this->container->getParameter('perfilDocAdm');

           if ($session->has("perfil")) {
               if($session->get('perfil') == $perfilEst || $session->get('perfil') == $perfilEstDoc || $session->get('perfil') == $perfilEstAdm){





                  $estudiante  = $session->get('nom_usuario');  
                                  

                   $inscripcion=array();
                   $respuesta= new Response("",200);
                   $materias  = $request->request->get('arrMaterias');
                   $idEstudiante  = $request->request->get('idEstudiante');
                   $idCarrera  = $request->request->get('idCarrera');
                   $idCiclo  = $request->request->get('idCiclo');
                   $carrera  = $request->request->get('carrera');
                   $UgServices = new UgServices;
                   $xml1 = $UgServices->getConsultaRegistro_Matricula($idEstudiante,$idCarrera,$idCiclo);
                //obtenet el ciclo de matriculacion del XML
                   if ( is_object($xml1))
                      {
                                foreach($xml1->PX_SALIDA as $xml)
                                 {  

                                      foreach($xml->registros as $lsciclo) 
                                        {
                                             foreach($lsciclo->registro as $lsdetallematerias) 
                                              {
                                                 $Nombre=$lsdetallematerias->nombre;
                                                  $Veces=$lsdetallematerias->veces;
                                                  $IdMateria=$lsdetallematerias->id_sa_materia;
                                                  $Nivel=$lsdetallematerias->nivel;
                                                  $Curso=$lsdetallematerias->curso;
                                                  $arrayProceso=array();
                                                  $arrayProceso['Curso']=$Curso; 
                                                  $arrayProceso['Materia']=$Nombre; 
                                                  $arrayProceso['Idmateria']=$IdMateria; 
                                                  $arrayProceso['Veces']=$Veces; 
                                                  $arrayProceso['Nivel']=$Nivel; 
                                                  array_push($inscripcion, $arrayProceso); 
                                              }
                                        }
                                  }
                      }



                  //  foreach ($materias as $key => $value) {
                  //    $arrayProceso=array();
                  //    $arrayProceso['Curso']=$value['Curso']; 
                  //    $arrayProceso['Idcurso']=$value['Idcurso']; 
                  //    $arrayProceso['Materia']=$value['Materia']; 
                  //    $arrayProceso['Idmateria']=$value['idMateria']; 
                  //    $arrayProceso['Veces']=$value['Veces']; 
                  //    $arrayProceso['Nivel']=$value['Nivel']; 
                     
                  //    array_push($inscripcion, $arrayProceso); 

                  // }
                    

                  return $this->render('TitulacionSisAcademicoBundle:Estudiantes:listarmatricula.html.twig',
                                                  array('listaMaterias'=>$inscripcion,
                                                        'estudiante'=>$estudiante,
                                                        'idEstudiante'=>$idEstudiante,
                                                        'idCarrera'=>$idCarrera,
                                                        'idciclo'=>$idCiclo,
                                                        'carrera'=>$carrera 
                                                        ));

           }

         else{
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
    }#end function

      
    public function pdfmatriculaAction(Request $request,$idEstudiante,$idCarrera,$ciclo,$carrera)
    {     
            $session=$request->getSession();
            $perfilEst   = $this->container->getParameter('perfilEst');
            $perfilDoc   = $this->container->getParameter('perfilDoc');
            $perfilAdmin = $this->container->getParameter('perfilAdmin'); 
            $perfilEstDoc = $this->container->getParameter('perfilEstDoc'); 
            $perfilEstAdm = $this->container->getParameter('perfilEstAdm'); 
            $perfilDocAdm = $this->container->getParameter('perfilDocAdm');
            $estudiante  = $session->get('nom_usuario'); 

           if ($session->has("perfil")) {
               if($session->get('perfil') == $perfilEst || $session->get('perfil') == $perfilEstDoc || $session->get('perfil') == $perfilEstAdm){


                  $UgServices = new UgServices;
                $xml1 = $UgServices->getConsultaRegistro_Matricula($idEstudiante,$idCarrera,$ciclo);
              //obtenet el ciclo de matriculacion del XML
               if ( is_object($xml1))
                  {
                            foreach($xml1->PX_SALIDA as $xml)
                             {  

                                  foreach($xml->registros as $lsciclo) 
                                    {
                                      $pdf= " <html> 
                                            <body>
                                            <img width='5%' src='images/menu/ug_logo.png'/>
                                            <table align='center'>
                                            <tr>
                                              <td align='center'>
                                                <b> Hoja de Registro</b>
                                              </td>
                                            <tr>
                                            <tr>
                                            <td>
                                              <b> $carrera </b>
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
                                                            <th style='text-align: center !important;'>Nivel</th>
                                                            <th style='text-align: center !important;'>Id Materias</th>
                                                            <th style='text-align: center !important;'>Nombre Materias</th>
                                                            <th style='text-align: center !important;'>Veces</th>
                                                            <th style='text-align: center !important;'>Curso</th> 
                                                        </tr>";
                                          foreach($lsciclo->registro as $lsdetallematerias) 
                                          {
                                                 
                                                


                                                  $Nombre=$lsdetallematerias->nombre;
                                                  $Veces=$lsdetallematerias->veces;
                                                  $IdMateria=$lsdetallematerias->id_sa_materia;
                                                  $Nivel=$lsdetallematerias->nivel;
                                                  $Curso=$lsdetallematerias->curso;
                                                  

                                                  
                                                 $pdf.="<tr>
                                                            <td align='center'>$Nivel</td>
                                                            <td align='center'>$IdMateria</td>
                                                            <td align='center'>$Nombre</td>
                                                            <td align='center'>$Veces</td>
                                                            <td align='center'>$Curso</td>
                                                        </tr>";
                                            }

                                            $pdf.="</table><br><br><br><br><br><br>  <table align='center' class='table table-striped'> 

                                                    <tr><td width='40%'><img width='80%' src='images/menu/firma.png'/></td> 
                                                      <td width='20%'>&nbsp;</td>
                                                      <td width='40%'><img width='80%' src='images/menu/firma.png'/></td>
                                                    </tr>

                                                    <tr><td align='center' ><b>$estudiante</b></td>
                                                    <td >&nbsp;</td>
                                                   <td align='center'><b>SECRETARÍA</b></td></tr>
                                                    </table>";

                                             $pdf.="</div></body></html>";
 
                                            
                                    }
                              }
                  }
                  $mpdfService = $this->get('tfox.mpdfport');
                  $mPDF = $mpdfService->getMpdf();
                 // $mPDF = $mpdfService->add();
                  $mPDF->AddPage('','','1','i','on');
                  $mPDF->WriteHTML($pdf);
                  
                  //$mPDF->AddPage('','','1','i','on');
                  //$mPDF->WriteHTML($pdf);
                  //$mPDF->Output();
                  return new response($mPDF->Output());
                   // $html =  $pdf;

                    //$mpdfService->SetTitle("Acme Trading Co. - Invoice");
                    //$mpdfService->Output("Pruebas.pdf")


                    //$response = $mpdfService->generatePdfResponse($html);
                    //return $response;



        } else{
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
    }#end function
        
    public function generaturnoAction(Request $request)
    {     
            $session=$request->getSession();
            $perfilEst   = $this->container->getParameter('perfilEst');
            $perfilDoc   = $this->container->getParameter('perfilDoc');
            $perfilAdmin = $this->container->getParameter('perfilAdmin'); 
            $perfilEstDoc = $this->container->getParameter('perfilEstDoc'); 
            $perfilEstAdm = $this->container->getParameter('perfilEstAdm'); 
            $perfilDocAdm = $this->container->getParameter('perfilDocAdm');
            $estudiante  = $session->get('nom_usuario'); 

           if ($session->has("perfil")) {
               if($session->get('perfil') == $perfilEst || $session->get('perfil') == $perfilEstDoc || $session->get('perfil') == $perfilEstAdm){

                $respuesta= new Response("",200);
                $idEstudiante  = $request->request->get('idEstudiante');
                $idCarrera  = $request->request->get('idCarrera');
                $idCiclo  = $request->request->get('idCiclo');
                $UgServices = new UgServices;
                $xml = $UgServices->getgeneraTurno($idEstudiante,$idCarrera,$idCiclo);
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
                $arrayProceso=array();
                $arrayProceso['codigo_error']=$Estado;
                $arrayProceso['mensaje']=$Mensaje;
                $jarray=json_encode($arrayProceso);

                 $respuesta->setContent($jarray);
                return $respuesta;




        } else{
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
    }#end function

     public function pdfordenpagoAction(Request $request,$idEstudiante,$idCarrera,$ciclo,$carrera)
    {     
            $session=$request->getSession();
            $perfilEst   = $this->container->getParameter('perfilEst');
            $perfilDoc   = $this->container->getParameter('perfilDoc');
            $perfilAdmin = $this->container->getParameter('perfilAdmin'); 
            $perfilEstDoc = $this->container->getParameter('perfilEstDoc'); 
            $perfilEstAdm = $this->container->getParameter('perfilEstAdm'); 
            $perfilDocAdm = $this->container->getParameter('perfilDocAdm');
            $estudiante  = $session->get('nom_usuario'); 

           if ($session->has("perfil")) {
               if($session->get('perfil') == $perfilEst || $session->get('perfil') == $perfilEstDoc || $session->get('perfil') == $perfilEstAdm){


                $UgServices = new UgServices;
                $xml1 = $UgServices->getConsultaRegistro_OrdenPago($idEstudiante,$idCarrera,$ciclo);
              //obtenet el ciclo de matriculacion del XML
                $pdfGen="";
               $mpdfService = $this->get('tfox.mpdfport');
                $mPDF = $mpdfService->getMpdf();
                $mPDF->AddPage('','','1','i','on');
                $lnPage=1;
                $lnCuenta=0;
                $lnhasta=0;
               //var_dump($xml1);
               if ( is_object($xml1))
                  {
                            foreach($xml1->PX_SALIDA as $xml)
                             {  
                                  
                                  foreach($xml->OrdenPagos as $lscaborden)
                                  {
                                        $lnhasta=count ($lscaborden->OrdenPago);
                                        foreach($lscaborden->OrdenPago as $lsOrden) 
                                          {
                                              $lnCuenta=$lnCuenta+1;
                                              $NumOrden= (string ) $lsOrden->numero_orden;
                                              $FecOrden= (string ) $lsOrden->fecha_limite_pago;
                                              $ValorOrden=(string ) $lsOrden->valor_total;
                                              $pdf= " <html> 
                                                  <body>
                                                  <table class='table table-striped table-bordered' border='1' width='100%'  >
                                                  <tr>
                                                  <td width='100%'>
                                                    <img width='5%' src='images/menu/ug_logo.png'/>
                                                    <b> $carrera </b>
                                                  </td>
                                                  </tr>
                                                  <tr>
                                                  <td width='100%'>
                                                    <table align='center'>
                                                    <tr>
                                                      <td align='left'>
                                                        <b> Orden de Pago  N° </b>
                                                      </td>
                                                      <td>
                                                        $NumOrden
                                                      </td>
                                                    </tr>
                                                    <tr>
                                                      <td align='left'>
                                                        <b> Fecha Máxima de Pago </b>
                                                      </td>
                                                      <td>
                                                        $FecOrden
                                                      </td>
                                                    </tr>
                                                    <tr>
                                                      <td align='left'>
                                                        <b> Valor a Pagar </b>
                                                      </td>
                                                      <td>
                                                        $ValorOrden
                                                      </td>
                                                    </tr>
                                                    </table>
                                                  </td>
                                                </tr>
                                                <tr>
                                                <td width='105%'>    
                                                    <table  border='1' width='100%' align='center'>
                                                                <tr>
                                                                        <th colspan='2' > Detalle de Orden de Pago  </th>
                                                                </tr>
                                                                <tr>
                                                                    <th  align='center'>Detalle</th>
                                                                    <th  align='center'>Valor</th>
                                                                </tr>"; 


                                             foreach($lsOrden->Detalles->Detalle as $lsDetOrden) 
                                                {
                                                  $Rubro=$lsDetOrden->Rubro;
                                                  $Valor=$lsDetOrden->valor;
                                                  $pdf.="<tr>
                                                            <td  width='100%' align='center'>$Rubro</td>
                                                            <td  width='100%' align='center'>$Valor</td>
                                                        </tr>"; 
                                                } 
                                                 $pdf.="</table>";

                                               $pdf.="</td>
                                                        </tr>
                                                      </table> <br><br><br><br>
                                               </body></html>";
                                               //$pdfGen.=$pdf;
                                               
                                               if ($lnPage==3)
                                               {
                                                  $lnPage=1;
                                                  $mPDF->AddPage('','','1','i','on');
                                                  
                                               }
                                               else
                                               {
                                                $lnPage=$lnPage+1;
                                                if ($lnhasta==$lnCuenta)
                                                  {
                                                    $mPDF->AddPage('','','1','i','on'); 
                                                    
                                                  }
                                               }
                                               

                                               $mPDF->WriteHTML($pdf);
                                               
                                          }
                                      }
                                  }
                                      
                  }
                 
                  //$mPDF->WriteHTML($pdfGen);
                  
                  //$mPDF->AddPage('','','1','i','on');
                  //$mPDF->WriteHTML($pdf);
                  //$mPDF->Output();
                  if ($lnhasta<=0)
                  {
                    $mPDF->WriteHTML("No existen Datos para Generar");
                  }
                  return new response($mPDF->Output());
                   // $html =  $pdf;

                    //$mpdfService->SetTitle("Acme Trading Co. - Invoice");
                    //$mpdfService->Output("Pruebas.pdf")


                    //$response = $mpdfService->generatePdfResponse($html);
                    //return $response;



        } else{
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
    }#end function

     public function subirarchivosAction(Request $request,$idEstudiante)
    {     
            $session=$request->getSession();
            $perfilEst   = $this->container->getParameter('perfilEst');
            $perfilDoc   = $this->container->getParameter('perfilDoc');
            $perfilAdmin = $this->container->getParameter('perfilAdmin'); 
            $perfilEstDoc = $this->container->getParameter('perfilEstDoc'); 
            $perfilEstAdm = $this->container->getParameter('perfilEstAdm'); 
            $perfilDocAdm = $this->container->getParameter('perfilDocAdm');
            $estudiante  = $session->get('nom_usuario'); 

           if ($session->has("perfil")) {
               if($session->get('perfil') == $perfilEst || $session->get('perfil') == $perfilEstDoc || $session->get('perfil') == $perfilEstAdm){

                $ruta="documentos/estudiantes";
                $Documentos = new procesarArchivos;
                if (!empty($_FILES)) {
                    $existingFile = false;
                    //Comprobamos que por lo menos haya un archivo
                    foreach ($_FILES as $uploadedFile => $dataFile) {
                        for ($i = 0; $i < count($dataFile["name"]); $i++) {
                            if ($dataFile["name"][$i] != "") {
                                $existingFile = true;
                            };
                        }
                    }
                    if ($existingFile) {
                        //llamamos a la funcion que mueve y comprueba los archivos
                        $filesUploaded = $Documentos->moveFiles($_FILES,$idEstudiante,$ruta);
                    } else {
                        die("No hay un archivo para procesar");
                    }
                } else {
                    die("No se enviaron archivos");
                }

                
                return new response("Proceso Exitoso");

                  



        } else{
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
    }#end function

    public function pdfAnulacionMateriasAction(Request $request,$idEstudiante,$idCarrera,$ciclo,$carrera)
    {     
            $session=$request->getSession();
            $perfilEst   = $this->container->getParameter('perfilEst');
            $perfilDoc   = $this->container->getParameter('perfilDoc');
            $perfilAdmin = $this->container->getParameter('perfilAdmin'); 
            $perfilEstDoc = $this->container->getParameter('perfilEstDoc'); 
            $perfilEstAdm = $this->container->getParameter('perfilEstAdm'); 
            $perfilDocAdm = $this->container->getParameter('perfilDocAdm');
            $estudiante  = $session->get('nom_usuario'); 

           if ($session->has("perfil")) {
               if($session->get('perfil') == $perfilEst || $session->get('perfil') == $perfilEstDoc || $session->get('perfil') == $perfilEstAdm){


                  $UgServices = new UgServices;
                $xml1 = $UgServices->getConsultaRegistro_Matricula($idEstudiante,$idCarrera,$ciclo);
              //obtenet el ciclo de matriculacion del XML
               if ( is_object($xml1))
                  {
                            foreach($xml1->PX_SALIDA as $xml)
                             {  

                                  foreach($xml->registros as $lsciclo) 
                                    {
                                      $pdf= " <html> 
                                            <body>
                                            <img width='5%' src='images/menu/ug_logo.png'/>
                                            <table align='center'>
                                            <tr>
                                              <td align='center'>
                                                <b> Solicitud de Anulación</b>
                                              </td>
                                            <tr>
                                            <tr>
                                            <td>
                                              <b> $carrera </b>
                                            </td>
                                            </tr>
                                            </table>
                                            <div class='col-lg-12'>
                                            <br><br><br><br>
                                            <table class='table table-striped table-bordered' border='1' width='100%' >
                                                     <thead>
                                                        <tr>
                                                                <th colspan='5'   style='text-align: center !important;background-color: #337AB7 !important;color: white!important;'>Detalle de Materias  </th>
                                                        </tr>
                                                        <tr>
                                                            <th style='text-align: center !important;'>Nivel</th>
                                                            <th style='text-align: center !important;'>Nombre Materias</th>
                                                            <th style='text-align: center !important;'>Veces</th>
                                                            <th style='text-align: center !important;'>Curso</th>
                                                            <th style='text-align: center !important;'>Estado</th> 
                                                        </tr>";
                                          foreach($lsciclo->registro as $lsdetallematerias) 
                                          {
                                                 
                                                


                                                  $Nombre=$lsdetallematerias->nombre;
                                                  $Veces=$lsdetallematerias->veces;
                                                  $IdMateria=$lsdetallematerias->id_sa_materia;
                                                  $Nivel=$lsdetallematerias->nivel;
                                                  $Curso=$lsdetallematerias->curso;
                                                  $Estado=$lsdetallematerias->estado_materia;
                                                  $idEstado=$lsdetallematerias->id_estado_materia;
                                                  

                                                  if ($idEstado=='119')
                                                  {
                                                       $pdf.="<tr>
                                                                  <td align='center'>$Nivel</td>
                                                                  <td align='center'>$Nombre</td>
                                                                  <td align='center'>$Veces</td>
                                                                  <td align='center'>$Curso</td>
                                                                  <td align='center'>$Estado</td>
                                                              </tr>";
                                                  }
                                            }

                                            $pdf.="</table><br><br><br><br><br><br>  <table align='center' class='table table-striped'> 

                                                    <tr><td width='40%'><img width='80%' src='images/menu/firma.png'/></td> 
                                                      <td width='20%'>&nbsp;</td>
                                                      <td width='40%'><img width='80%' src='images/menu/firma.png'/></td>
                                                    </tr>

                                                    <tr><td align='center' ><b>$estudiante</b></td>
                                                    <td >&nbsp;</td>
                                                   <td align='center'><b>SECRETARÍA</b></td></tr>
                                                    </table>";

                                             $pdf.="</div></body></html>";
 
                                            
                                    }
                              }
                  }
                  $mpdfService = $this->get('tfox.mpdfport');
                  $mPDF = $mpdfService->getMpdf();
                 // $mPDF = $mpdfService->add();
                  $mPDF->AddPage('','','1','i','on');
                  $mPDF->WriteHTML($pdf);
                  
                  //$mPDF->AddPage('','','1','i','on');
                  //$mPDF->WriteHTML($pdf);
                  //$mPDF->Output();
                  return new response($mPDF->Output());
                   // $html =  $pdf;

                    //$mpdfService->SetTitle("Acme Trading Co. - Invoice");
                    //$mpdfService->Output("Pruebas.pdf")


                    //$response = $mpdfService->generatePdfResponse($html);
                    //return $response;



        } else{
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
    }#end function


    
    
      public function listarsolicitudesAction(Request $request)
    {      $session=$request->getSession();
           if ($session->has("perfil")) {
           $UgServices = new UgServices;    
          $xmlsolicitudes = "<parametros>
			
				</parametros>";
           
       $solicitudes = $UgServices->Solicitudes_disponible($xmlsolicitudes);
       //echo var_dump($solicitudes); exit();
          return $this->render('TitulacionSisAcademicoBundle:Estudiantes:listarsolicitudes.html.twig',
						  array(
							
                                                           'solicitud'   => $solicitudes 
						  ));
           } else{
                  $this->get('session')->getFlashBag()->add(
                                'mensaje',
                                'Los datos ingresados no son válidos'
                            );
                    return $this->redirect($this->generateUrl('titulacion_sis_academico_homepage'));
               }
           
    }#end function
    

          
 public function pdfHorarioExamenAction(Request $request,$idEstudiante,$idCarrera,$ciclo,$carrera)
    {     
            $session=$request->getSession();
            $perfilEst   = $this->container->getParameter('perfilEst');
            $perfilDoc   = $this->container->getParameter('perfilDoc');
            $perfilAdmin = $this->container->getParameter('perfilAdmin'); 
            $perfilEstDoc = $this->container->getParameter('perfilEstDoc'); 
            $perfilEstAdm = $this->container->getParameter('perfilEstAdm'); 
            $perfilDocAdm = $this->container->getParameter('perfilDocAdm');
            $estudiante  = $session->get('nom_usuario'); 
        
           if ($session->has("perfil")) {
               if($session->get('perfil') == $perfilEst || $session->get('perfil') == $perfilEstDoc || $session->get('perfil') == $perfilEstAdm){


                $UgServices = new UgServices;
                $xml1 = $UgServices->getConsultaHorario_examen($idEstudiante,$idCarrera,$ciclo);
              //obtenet el ciclo de matriculacion del XML
                $pdfGen="";
                $mpdfService = $this->get('tfox.mpdfport');
                $mPDF = $mpdfService->getMpdf();
                $mPDF->AddPage('','','1','i','on');
                $lnPage=1;
                $lnCuenta=0;
                $lnhasta=0;
                $arrDias=array();
                $arrHoras=array();
                $arrMaterias=array();
                $arrPresentar=array();
                //var_dump($xml1);
                // var_dump($xmlCursos);
                //   exit();
               //var_dump($xml1);
               if ( is_object($xml1))
                  {
                            foreach($xml1->PX_Salida as $xml)
                             {  
                                  foreach($xml->cursos as $xmlCursos)
                                    { 

                                            foreach($xmlCursos->curso as $lscabHorarios)
                                            {
                                              $nombreCurso=$lscabHorarios->descripcion;
                                              $arrDias=array();
                                              $arrHoras=array();
                                              $arrMaterias=array();
                                              $arrPresentar=array();
                                              $arrProfesores=array();

                                                  $lnhasta=count ($lscabHorarios);
                                                  foreach($lscabHorarios->dias->dia as $Horarios) 
                                                    {
                                                          $arrDatos=array('Dia'=>(string)$Horarios->nombre,
                                                                  'idDia'=>(string)$Horarios->id_dia);
                                                          array_push($arrDias, $arrDatos);
                                                      }

                                                      foreach($lscabHorarios->horas->hora as $Horariosh) 
                                                      {
                                                        $arrDatos=array('Hora'=>(string)$Horariosh->nombre,
                                                                    'idHora'=>(string)$Horariosh->id_hora);
                                                          array_push($arrHoras, $arrDatos);
                                                      } 
                                                      
                                                      
                                                      $c=count($arrDias);
                                                      $f=count($arrHoras);

                                                      for($i = 0; $i < $f; $i++)
                                                      {
                                                        for($j = 0; $j < $c; $j++)
                                                        {
                                                          $arrPresentar[$i][$j]="";
                                                        }
                                                      }
                                                      foreach($lscabHorarios->materias->materia as $Horariosm) 
                                                      {
                                                        $arrDatos=array('Materia'=>(string) $Horariosm->descripcion_materia,
                                                                    'idMateria'=> (string)$Horariosm->id_materia,
                                                                    'idHora'=> (string)$Horariosm->id_hora,
                                                                    'idDia'=> (string)$Horariosm->id_dia);
                                                          array_push($arrMaterias, $arrDatos);
                                                      } 
                                                      foreach($lscabHorarios->profesores->profesor as $Horariosp) 
                                                      {
                                                        $arrDatos=array('Materia'=>(string) $Horariosp->nombre_materia,
                                                                    'Profesor'=> (string)$Horariosp->nombre);
                                                          array_push($arrProfesores, $arrDatos);
                                                      } 
                                                      
                                                      $lncuantosp=count($arrProfesores);
                                                      $lncuantosp=ceil($lncuantosp/2);
                                                       foreach ($arrMaterias as $key => $Detalle) 
                                                       {
                                                            $idDia=$Detalle['idDia'];
                                                            $idHora=$Detalle['idHora'];
                                                            $Materia=$Detalle['Materia'];

                                                            //$poscol=array_search($idDia, $arrCol);
                                                            //echo $idDia;
                                                            foreach ($arrHoras as $keyf => $Filas) 
                                                            {
                                                                if ((string) $Filas['idHora']==$idHora)
                                                                {
                                                                  $posFil=$keyf;
                                                                   break;
                                                                }
                                                             }
                                                            foreach ($arrDias as $keyc => $Filas) 
                                                            {
                                                                if ((string) $Filas['idDia']==$idDia)
                                                                {
                                                                  $posCol=$keyc;
                                                                   break;
                                                                }
                                                            }
                                                            
                                                            $arrPresentar[$posFil][$posCol]=$Materia;
                                                          }

                                                          $presenta="<html> 
                                                      <body>
                                                      <br/>
                                                      <img width='5%' src='images/menu/ug_logo.png'/>
                                                      <table align='center'>
                                                      <tr>
                                                        <td align='center'>
                                                          <b> Horario de Examen Curso : $nombreCurso </b>
                                                        </td>
                                                      <tr>
                                                      <tr>
                                                      <td>
                                                        <b> $carrera </b>
                                                      </td>
                                                      </tr>
                                                      </table><table class='table table-striped table-bordered' border='1' width='100%'>";
                                                          $presenta.="<thead><tr>";
                                                          $presenta.="<th>Horario</th>";
                                                          //var_dump($arrCol);
                                                            foreach ($arrDias as $key => $value) {
                                                              $presenta.="<th>".$value['Dia']."</th>";
                                                            }
                                                          $presenta.="</tr></thead>";
                                                          
                                                          foreach ($arrPresentar as $key => $value) {
                                                            $presenta.="<tr>";
                                                            $presenta.="<td>".$arrHoras[$key]['Hora']."</td>";
                                                            //var_dump($value);
                                                            foreach ($value as $key2 => $value2) {
                                                              $presenta.="<td>".$value2."</td>";
                                                              //echo $key2;
                                                            }
                                                            $presenta.="</tr>";
                                                            //$presenta.="<tr><td>".$value[$key]."</td></tr>";
                                                            //var_dump($value);
                                                          }
                                                          $presenta.="</table>";
                                                          $presenta.="<table class='table table-striped table-bordered' border='0' width='100%'>";
                                                          $i=1;
                                                           $presenta.="<thead>"; 
                                                           $presenta.="<tr><th colspan=1>Detalle de Profesores por Materia</th></tr>";
                                                           $presenta.="</thead>";
                                                          foreach ($arrProfesores as $key => $value) {
                                                                  if ($i%2!=0)
                                                                  {
                                                                    $presenta.="<tr>";
                                                                  }
                                                                  $presenta.="<td style='font-size:10px; line-height:1;'> <b>".$value['Materia']."</b> - ".$value['Profesor']."</td>";
                                                                  if ($i%2==0)
                                                                  {
                                                                    $presenta.="<tr>";
                                                                  }
                                                                  $i=$i+1;
                                                                }

                                                            $presenta.="</table>";

                                                          $presenta.="</body></html>";
                                                          $mPDF->WriteHTML($presenta);
                                                          //echo $presenta;
                                                      //var_dump($arrPresentar);
                                                      //exit();
                                                    }
                                            }
                                      }
                  }
                 
                  //$mPDF->WriteHTML($pdfGen);
                  
                  //$mPDF->AddPage('','','1','i','on');
                  //$mPDF->WriteHTML($pdf);
                  //$mPDF->Output();
                  if ($lnhasta<=0)
                  {
                    $mPDF->WriteHTML("No existen Datos para Generar");
                  }
                  return new response($mPDF->Output());
 

        } else{
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
    }#end function

    public function carrerasHorariosAction(Request $request)
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
               if ($session->get('perfil') == $perfilEst || $session->get('perfil') == $perfilEstDoc || $session->get('perfil') == $perfilEstAdm) 
               {
                    try
                    {
                          $lcFacultad="";
                          $lcCarrera="";
                          //$idEstudiante=3;
                          $idEstudiante=$session->get("id_user");
                          //$idEstudiante=3;
                          $idRol=$perfilEst;
                          
                          //$idRol=$session->get("perfil");
                          $Carreras = array();
                          $UgServices = new UgServices;
                          $xml = $UgServices->getConsultaCarreras($idEstudiante,$idRol);

                             
                            if ( is_object($xml))
                            {
                              foreach($xml->registros->registro as $lcCarreras) 
                              {
                                      $lcFacultad=$lcCarreras->id_sa_facultad;
                                      $lcCarrera=$lcCarreras->id_sa_carrera;
                                      $materiaObject = array( 'Nombre' => $lcCarreras->nombre,
                                                                 'Facultad'=>$lcCarreras->id_sa_facultad,
                                                                 'Carrera'=>$lcCarreras->id_sa_carrera,
                                                                 'idCiclo'=>$lcCarreras->id_sa_ciclo_detalle
                                                                );
                                      array_push($Carreras, $materiaObject); 
                              } 


                              $bolCorrecto=1;
                              $cuantos=count($Carreras);

                              if ($cuantos==0)
                              {
                                $bolCorrecto=0;
                              }
                              
                              return $this->render('TitulacionSisAcademicoBundle:Estudiantes:estudiantes_carrerashorarios.html.twig',array(
                                                      'facultades' =>  $Carreras,
                                                      'idEstudiante'=>$idEstudiante,
                                                      'bolcorrecto'=>$bolCorrecto
                                                   ));
                              var_dump(".....".$cuantos);
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
                            return $this->render('TitulacionSisAcademicoBundle:Estudiantes:estudiantes_carrerashorarios.html.twig',array(
                                                      'facultades' =>  $Carreras,
                                                      'idEstudiante'=>$idEstudiante,
                                                      'bolcorrecto'=>$bolCorrecto
                                                   ));

                            //return $this->render('TitulacionSisAcademicoBundle:Estudiantes:error.html.twig');
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
           else
           {
                $this->get('session')->getFlashBag()->add(
                                      'mensaje',
                                      'Los datos ingresados no son válidos'
                                  );
                    return $this->redirect($this->generateUrl('titulacion_sis_academico_homepage'));
            }
        }

public function pdfHorarioGeneralAction(Request $request,$idEstudiante,$idCarrera,$ciclo,$carrera)
    {     
            $session=$request->getSession();
            $perfilEst   = $this->container->getParameter('perfilEst');
            $perfilDoc   = $this->container->getParameter('perfilDoc');
            $perfilAdmin = $this->container->getParameter('perfilAdmin'); 
            $perfilEstDoc = $this->container->getParameter('perfilEstDoc'); 
            $perfilEstAdm = $this->container->getParameter('perfilEstAdm'); 
            $perfilDocAdm = $this->container->getParameter('perfilDocAdm');
            $estudiante  = $session->get('nom_usuario'); 
          

           if ($session->has("perfil")) {
               if($session->get('perfil') == $perfilEst || $session->get('perfil') == $perfilEstDoc || $session->get('perfil') == $perfilEstAdm){


                $UgServices = new UgServices;
                $xml1 = $UgServices->getConsultaHorario_General($idEstudiante,$idCarrera,$ciclo);

              //obtenet el ciclo de matriculacion del XML
                $pdfGen="";
                $mpdfService = $this->get('tfox.mpdfport');
                $mPDF = $mpdfService->getMpdf();
                $mPDF->AddPage('','','1','i','on');
                $lnPage=1;
                $lnCuenta=0;
                $lnhasta=0;
                $arrDias=array();
                $arrHoras=array();
                $arrMaterias=array();
                $arrPresentar=array();

                
               //var_dump($xml1);
               if ( is_object($xml1))
                  {
                    
                            foreach($xml1->PX_Salida as $xml)
                             {  
                              foreach($xml->cursos as $xmlCursos)
                                    { 

                                        $presenta="<html> 
                                                  <body>
                                                  <br/>
                                                  <img width='5%' src='images/menu/ug_logo.png'/>
                                                  <table align='center'>
                                                      <tr>
                                                        <td>
                                                          <b> $carrera </b>
                                                        </td>
                                                        </tr>
                                                    </table>
                                                  ";
                                                foreach($xmlCursos->curso as $lscabHorarios)
                                                {

                                                  $nombreCurso=$lscabHorarios->descripcion;
                                                  $arrDias=array();
                                                  $arrHoras=array();
                                                  $arrMaterias=array();
                                                  $arrPresentar=array();
                                                  $arrProfesores=array();

                                                      $lnhasta=count ($lscabHorarios);
                                                      foreach($lscabHorarios->dias->dia as $Horarios) 
                                                        {
                                                              $arrDatos=array('Dia'=>(string)$Horarios->nombre,
                                                                      'idDia'=>(string)$Horarios->id_dia);
                                                              array_push($arrDias, $arrDatos);
                                                          }

                                                          foreach($lscabHorarios->horas->hora as $Horariosh) 
                                                          {
                                                            $arrDatos=array('Hora'=>(string)$Horariosh->nombre,
                                                                        'idHora'=>(string)$Horariosh->id_hora);
                                                              array_push($arrHoras, $arrDatos);
                                                          } 
                                                          
                                                          
                                                          $c=count($arrDias);
                                                          $f=count($arrHoras);

                                                          for($i = 0; $i < $f; $i++)
                                                          {
                                                            for($j = 0; $j < $c; $j++)
                                                            {
                                                              $arrPresentar[$i][$j]="";
                                                            }
                                                          }
                                                          foreach($lscabHorarios->materias->materia as $Horariosm) 
                                                          {
                                                            $arrDatos=array('Materia'=>(string) $Horariosm->descripcion_materia,
                                                                        'idMateria'=> (string)$Horariosm->id_materia,
                                                                        'idHora'=> (string)$Horariosm->id_hora,
                                                                        'idDia'=> (string)$Horariosm->id_dia);
                                                              array_push($arrMaterias, $arrDatos);
                                                          } 
                                                          foreach($lscabHorarios->profesores->profesor as $Horariosp) 
                                                          {
                                                            $arrDatos=array('Materia'=>(string) $Horariosp->nombre_materia,
                                                                        'Profesor'=> (string)$Horariosp->nombre);
                                                              array_push($arrProfesores, $arrDatos);
                                                          } 
                                                          
                                                          $lncuantosp=count($arrProfesores);
                                                         // $lncuantosp=ceil($lncuantosp/2);
                                                           foreach ($arrMaterias as $key => $Detalle) 
                                                           {
                                                                $idDia=$Detalle['idDia'];
                                                                $idHora=$Detalle['idHora'];
                                                                $Materia=$Detalle['Materia'];

                                                                //$poscol=array_search($idDia, $arrCol);
                                                                //echo $idDia;
                                                                foreach ($arrHoras as $keyf => $Filas) 
                                                                {
                                                                    if ((string) $Filas['idHora']==$idHora)
                                                                    {
                                                                      $posFil=$keyf;
                                                                       break;
                                                                    }
                                                                 }
                                                                foreach ($arrDias as $keyc => $Filas) 
                                                                {
                                                                    if ((string) $Filas['idDia']==$idDia)
                                                                    {
                                                                      $posCol=$keyc;
                                                                       break;
                                                                    }
                                                                }
                                                                
                                                                $arrPresentar[$posFil][$posCol]=$Materia;
                                                              }
                                                              if (trim($presenta)=="")
                                                                {
                                                                   $presenta="<html> 
                                                                        <body>
                                                                        <br/>";
                                                                } 
                                                              
                                                              $presenta.="<table align='center'>
                                                                          <tr>
                                                                            <td align='center'>
                                                                              <b> Horario de Clases Curso : $nombreCurso </b>
                                                                            </td>
                                                                          </tr>
                                                                          </table>
                                                                        <table class='table table-striped table-bordered' border='1' width='100%'>";
                                                              $presenta.="<thead><tr>";
                                                              $presenta.="<th>Horario</th>";
                                                              //var_dump($arrCol);
                                                                foreach ($arrDias as $key => $value) {
                                                                  $presenta.="<th>".$value['Dia']."</th>";
                                                                }
                                                              $presenta.="</tr></thead>";
                                                              
                                                              foreach ($arrPresentar as $key => $value) {
                                                                $presenta.="<tr>";
                                                                $presenta.="<td>".$arrHoras[$key]['Hora']."</td>";
                                                                //var_dump($value);
                                                                foreach ($value as $key2 => $value2) {
                                                                  $presenta.="<td>".$value2."</td>";
                                                                  //echo $key2;
                                                                }
                                                                $presenta.="</tr>";
                                                                //$presenta.="<tr><td>".$value[$key]."</td></tr>";
                                                                //var_dump($value);
                                                              }
                                                              $presenta.="</table>";
                                                              $presenta.="<table class='table table-striped table-bordered' border='0' width='100%'>";
                                                              $i=1;
                                                               $presenta.="<thead>"; 
                                                               $presenta.="<tr><th colspan=1>Detalle de Profesores por Materia</th></tr>";
                                                               $presenta.="</thead>";
                                                              foreach ($arrProfesores as $key => $value) {
                                                                      if ($i%2!=0)
                                                                      {
                                                                        $presenta.="<tr>";
                                                                      }
                                                                      $presenta.="<td style='font-size:10px; line-height:1;'> <b>".$value['Materia']."</b> - ".$value['Profesor']."</td>";
                                                                      if ($i%2==0)
                                                                      {
                                                                        $presenta.="<tr>";
                                                                      }
                                                                      $i=$i+1;
                                                                    }

                                                                $presenta.="</table>";

                                                              $presenta.="</body></html>";
                                                              $mPDF->WriteHTML($presenta);
                                                              $presenta="";
                                                              //echo $presenta;
                                                          //var_dump($arrPresentar);
                                                          //exit();
                                                }
                                          }
                                      }
                  }
                 
                  //$mPDF->WriteHTML($pdfGen);
                  
                  //$mPDF->AddPage('','','1','i','on');
                  //$mPDF->WriteHTML($pdf);
                  //$mPDF->Output();
                  if ($lnhasta<=0)
                  {
                    $mPDF->WriteHTML("No existen Datos para Generar");
                  }
                  return new response($mPDF->Output());
 

        } else{
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
    }#end function        
     public function pdfDatosGeneralesAction(Request $request,$carrera)
    {     
            $session=$request->getSession();
            $perfilEst   = $this->container->getParameter('perfilEst');
            $perfilDoc   = $this->container->getParameter('perfilDoc');
            $perfilAdmin = $this->container->getParameter('perfilAdmin'); 
            $perfilEstDoc = $this->container->getParameter('perfilEstDoc'); 
            $perfilEstAdm = $this->container->getParameter('perfilEstAdm'); 
            $perfilDocAdm = $this->container->getParameter('perfilDocAdm');
            $estudiante  = $session->get('nom_usuario'); 
            $idEstudiante=$session->get("id_user");
             //$idEstudiante=32;

           if ($session->has("perfil")) {
               if($session->get('perfil') == $perfilEst || $session->get('perfil') == $perfilEstDoc || $session->get('perfil') == $perfilEstAdm){


                  $UgServices = new UgServices;
                $xml1 = $UgServices->getConsultaDatos_Generales($idEstudiante);
                // var_dump($xml1);
                // exit();
              //obtenet el ciclo de matriculacion del XML
               if ( is_object($xml1))
                  {
                            foreach($xml1->registros as $xml)
                             {  

                                  foreach($xml->registro as $Datos) 
                                    {

                                       $rutaFoto=(string) $Datos->DirectorioFoto;
                                       if (trim($rutaFoto)=="")
                                       {
                                            $rutaFoto="images/menu/ug_logo.png";
                                       }
                                       
                                      $pdf= " <html> 
                                            <body>
                                            <img width='5%' src='images/menu/ug_logo.png'/>
                                            
                                            <table align='right'>
                                            <tr>
                                              <td align='center'>
                                                <b> Ficha de Datos</b>
                                              </td>
                                            </tr>
                                            <tr>
                                            <td>
                                                <b> $carrera </b>
                                            </td>
                                            <td >&nbsp;&nbsp;</td>
                                            <td>
                                                <table align='right' border=1  >
                                                <tr>
                                                    <td>
                                                        <img  width='15%' src='$rutaFoto'/>
                                                    </td>
                                                </tr>
                                            </table>
                                              </td>
                                            </tr>
                                            </table>

                                            <div class='col-lg-12'>
                                            <br><br><br><br>
                                            <table class='table table-striped table-bordered'  width='100%' >
                                            ";
                                            $nombres=$Datos->nombres;
                                            $apellidos=$Datos->apellidos;
                                            $identificacion=$Datos->usuario;
                                            $direccion=$Datos->direccion;
                                            $telefono=$Datos->telefono;
                                            $correoinst=$Datos->correo_institucional;
                                            $correoper=$Datos->correo_personal;
                                            $estadoCivil=$Datos->descEstadoCivil;
                                            $nombrePadre=$Datos->nombre_padre;
                                            $nombreMadre=$Datos->nombre_madre;
                                            $sexo=$Datos->descSexo;
                                            $tipoSangre=$Datos->descTipoSangre;
                                            $fechaNac=$Datos->fecha_nacimiento;


                                             $pdf.="<tr>
                                                                  <td align='left' ><b>Nombres :</b> $nombres</td>
                                                                  <td align='left' ><b>Apellidos :</b> $apellidos</td>
                                                      </tr><tr>
                                                                  <td align='left' ><b>Numero de Identificación :</b> $identificacion</td>
                                                                  <td align='left' ><b>Fecha de Nacimiento :</b> $fechaNac</td>
                                                      </tr>
                                                      <tr rowspan=2>
                                                              <td align='left' colspan=2 ><b>Dirección :</b> $direccion</td>
                                                      </tr>
                                                      <tr>
                                                                  <td align='left' ><b>Telefono :</b> $telefono</td>
                                                                  <td align='left' ><b>Correo Institucional :</b> $correoinst</td>
                                                      </tr>
                                                      <tr>
                                                                  <td align='left' ><b>Correo Personal :</b> $correoper</td>
                                                                  <td align='left' ><b>Estado Civil :</b> $estadoCivil</td>
                                                      </tr>
                                                      <tr>
                                                                  <td align='left' ><b>Nombre del Padre:</b> $nombrePadre</td>
                                                                  <td align='left' ><b>Nombre de la Madre :</b> $nombreMadre</td>
                                                      </tr>
                                                      <tr>
                                                                  <td align='left' ><b>Sexo:</b> $sexo</td>
                                                                  <td align='left' ><b>Tipo de Sangre :</b> $tipoSangre</td>
                                                      </tr>

                                                      ";
                                          

                                            $pdf.="</table><br><br><br><br><br><br>  <table align='center' class='table table-striped'> 

                                                    <tr><td width='40%'><img width='80%' src='images/menu/firma.png'/></td> 
                                                      <td width='20%'>&nbsp;</td>
                                                      <td width='40%'><img width='80%' src='images/menu/firma.png'/></td>
                                                    </tr>

                                                    <tr><td align='center' ><b>$estudiante</b></td>
                                                    <td >&nbsp;</td>
                                                   <td align='center'><b>SECRETARÍA</b></td></tr>
                                                    </table>";

                                             $pdf.="</div></body></html>";
 
                                            
                                    }
                              }
                  }
                  $mpdfService = $this->get('tfox.mpdfport');
                  $mPDF = $mpdfService->getMpdf();
                 // $mPDF = $mpdfService->add();
                  $mPDF->AddPage('','','1','i','on');
                  $mPDF->WriteHTML($pdf);
                  
                  //$mPDF->AddPage('','','1','i','on');
                  //$mPDF->WriteHTML($pdf);
                  //$mPDF->Output();
                  return new response($mPDF->Output());
                   // $html =  $pdf;

                    //$mpdfService->SetTitle("Acme Trading Co. - Invoice");
                    //$mpdfService->Output("Pruebas.pdf")


                    //$response = $mpdfService->generatePdfResponse($html);
                    //return $response;



        } else{
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
    }#end function

          public function PdfSolicitudesAction(Request $request)
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
            $cedula  = $session->get('cedula');            
            $UgServices    = new UgServices;
            $datosHorarios  = $UgServices->Docentes_Horarios($idUsuario);
          
                // $archivo = basename($_GET['archivo']);
                $destino = 'upload_files/formato_solicitudes28682Huayamabe_Gabriel_Documento';
                $ruta = $destino;

                
                   header('Content-Type: application/force-download');
                   header('Content-Disposition: attachment; filename='."HOLA");
                   header('Content-Transfer-Encoding: binary');
                   header('Content-Length: '.filesize($ruta));

                   readfile($ruta);
                    return $this->render('TitulacionSisAcademicoBundle:Estudiantes:listarsolicitudes.html.twig');
             
    }#end function
    
    public function consultar_solicitudAction(Request $request){
    
    $response   = new JsonResponse();       
    $session=$request->getSession();
    $idUsuario  = $session->get('id_user');
    $desde  = $request->request->get('desde');
    $hasta  = $request->request->get('hasta');
    
    $UgServices = new UgServices;    
    $xmlsolicitudes = "<parametros>
			
				</parametros>";
           
    $Solicitudes = $UgServices->Solicitudes_disponible($xmlsolicitudes);
    //echo var_dump($Solicitudes); exit();
    $Estado="";
                $Mensaje="";
             if (count($Solicitudes)> 0)
                {               
                   $Mensaje="con";
                }else{
                    $Mensaje="";
                }
               //echo var_dump($Mensaje); exit();
                if($Mensaje == ""){
                 
                    
                          return $this->render('TitulacionSisAcademicoBundle:Estudiantes:listarsolicitudes.html.twig',
						  array(
							 //  'arr_datos' => $arr_datos,
                                                           'solicitudes'   => $Solicitudes 
						  ));              
                }else{
           
    return $this->render('TitulacionSisAcademicoBundle:Estudiantes:listarsolicitudes.html.twig',
						  array(
							 //  'arr_datos' => $arr_datos,
                                                           'solicitud'   => $Solicitudes 
						  ));
                }
            
   }

    }