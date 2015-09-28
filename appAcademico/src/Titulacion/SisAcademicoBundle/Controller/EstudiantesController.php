<?php
//consultaNotas
   namespace Titulacion\SisAcademicoBundle\Controller;

    use Symfony\Bundle\FrameworkBundle\Controller\Controller;
    use Symfony\Component\HttpFoundation\Response;
    use Symfony\Component\HttpFoundation\Request;
    use Symfony\Component\HttpFoundation\JsonResponse;
    use Titulacion\SisAcademicoBundle\Helper\UgServices;

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
        
           if (time ()  -  $session->getMetadataBag()->getLastUsed()  <  7500) 
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
                          //$idRol=1;
                          $idRol=$session->get("perfil");
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
                  $session->clear();
                  $this->get('session')->getFlashBag()->add(
                                'mensaje',
                                'Los datos ingresados no son válidos'
                            );
                    return $this->redirect($this->generateUrl('titulacion_sis_academico_homepage'));
               }
           }
           else
           {
                $session->clear();
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
                     $estudiante='Jeferson Bohorquez';
                      $carrerras = array(
                        array(
                            'Facultad' => '001',
                             'Carrera'=>'001',
                              'Nombre'=>'Matematicas' 
                        ),
                        array(
                            'Facultad' => '001',
                             'Carrera'=>'001',
                              'Nombre'=>'Networking'  
                        ),
                        array(
                            'Facultad' => '002',
                             'Carrera'=>'001',
                              'Nombre'=>'Sistemas'  
                        ),
                        array(
                            'Facultad' => '002',
                             'Carrera'=>'002',
                              'Nombre'=>'Economia'  
                        )
                        );



                    $xml = simplexml_load_file("pruebas.xml");
                    foreach($xml->materias as $materias) {
                        $lcMaterias=$materias->Nombre;
                        $lcregistro=$materias->registro;

                         $materiaObject = array(
                            'Nombre' => $lcMaterias,
                             'cursos'=>array(),
                             'registro' => $lcregistro,
                        );

                         $lscursos=array();
                        foreach($materias->cursos as $curso) {
                           $lscursos=array('cursos'=> $curso->curso);
                          // var_dump($lscursos);
                             //array_push($materiaObject, $lscursos);
                            array_push($materiaObject["cursos"], $lscursos);
                        }
                        
                        

                        array_push($matricula_dis, $materiaObject);
                    } 

                   
                   return $this->render('TitulacionSisAcademicoBundle:Estudiantes:estudiantes_matriculacion.html.twig',array(
                                                    'matricula_dis' =>  $matricula_dis,
                                                    'estudiante' => $estudiante,
                                                    'carreras'=> $carrerras 

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
                     $idFacultad  = 1222;
                     $listaMaterias=array();
                     $materiaObject=array();

                     if ($idIndica=='nh')
                     {   
                          
                          //$idEstudiante  = 17;
                          //$idCarrera  = 4;
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
                                           'DetalleParciales'=>array()
                                    ); 
                                        //ENCABEZADO-PARCIALES
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
                                       }
                               }
                                    /* No Borrar
                                    'CantidadParcial'=>array(),
                                           'CantidadDetalle'=>array() 
                                    $cuentadetalle=count(array_unique($materiaObject["DetalleParciales"]));
                                     $cuentaparcial=count(array_unique($materiaObject["Parciales"]));
                                     array_push($materiaObject["CantidadParcial"],$cuentaparcial);
                                     array_push($materiaObject["CantidadDetalle"],$cuentadetalle);
          */
                              
                                       
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
                                                                        'DetalleParciales'=>array()
                                                                      );
                                                //ENCABEZADO-PARCIALES

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
                                                       }
                                                       
                                               }




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
                                                                       
                   $bolCorrecto=1;
                   $cuantos=count($listaMaterias);
                   if($cuantos==0)
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
               
                     // return $this->render('TitulacionSisAcademicoBundle:Estudiantes:error_notas.html.twig');
                    // $bolCorrecto=0;                                                                                                                  
                    // return $this->render('TitulacionSisAcademicoBundle:Estudiantes:listarmaterias.html.twig',
                    //                       array('listaMaterias'=>$listaMaterias,
                    //                            'indica'=>$idIndica ,
                    //                            'bolcorrecto'=>$bolCorrecto) 
                    //                       );
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
                   $ciclo=1;
                   $anio=2015;
                   $asistencia = array();
                    try
                    {
                        $UgServices = new UgServices;
                        $xml = $UgServices->getConsultaAlumno_Asistencia($idEstudiante,$idCarrera,$ciclo,$anio);
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


        
    }