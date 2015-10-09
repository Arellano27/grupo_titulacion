<?php
//consultaNotas
   namespace Titulacion\SisAcademicoBundle\Controller;

    use Symfony\Bundle\FrameworkBundle\Controller\Controller;
    use Symfony\Component\HttpFoundation\Response;
    use Symfony\Component\HttpFoundation\Request;
    use Symfony\Component\HttpFoundation\JsonResponse;
    use Titulacion\SisAcademicoBundle\Helper\UgServices;
    use Titulacion\SisAcademicoBundle\fpdf\fpdf;

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


                   
                   return $this->render('TitulacionSisAcademicoBundle:Estudiantes:estudiantes_carrerasmatricula.html.twig',array(
                                                    'nomEstudiante' =>  $estudiante,
                                                    'idEstudiante' => $idEstudiante,
                                                    'carreras_inscribir'=>$Carreras_inscribir

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


           if ($session->has("perfil")) {
               if($session->get('perfil') == $perfilEst || $session->get('perfil') == $perfilEstDoc || $session->get('perfil') == $perfilEstAdm){
                     $matricula_dis=array();


                   
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

                      //$idEstudiante=12;
                      var_dump($idEstudiante);
                      $Materias_inscribir = array();

                       if ( is_object($xml2))
                          {
                              foreach($xml2->registros->registro as $datos)
                               {  
                                  $banderaMatricula=(int) $datos->valor;
                                  $Mensaje=(string) $datos->mensaje;
                                  $Idciclo=(string) $datos->id_ciclo;
                                  $ciclo=(string) $datos->ciclo_descripcion;
                                  $anio=(string) $datos->anio;
                               }
                              
                          }



                          
                      $lcFacultad="";
                      

                      $lcCarrera="";
                      
                     // $materiaObject = array(); 
                      $idRol=$perfilEst;

                       //$idEstudiante=1;
                       //$estudiante='Jeferson Bohorquez';
                      $CicloMatricula=$anio." - Ciclo ".$ciclo; //'2015 Ciclo 2';
                      //$Mensaje.=$CicloMatricula;


                     
                      if ($banderaMatricula==1)
                      {
                         $UgServices = new UgServices;

                          $xml1 = $UgServices->getConsultaDatos_Matricula($idEstudiante,$idCarrera,$ciclo);

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
                        if ($banderaMatricula==5)
                        {


                            $UgServices = new UgServices;

                            $xml1 = $UgServices->getConsultaRegistro_Matricula($idEstudiante,$idCarrera,$ciclo);
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
                                                    'idciclo'=>$Idciclo

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

           $UgServices = new UgServices;
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


            $arrayProceso=array();
            $arrayProceso['codigo_error']=$Estado;
            $arrayProceso['mensaje']=$Mensaje;
            $jarray=json_encode($arrayProceso);
            
            //exit();

          // $serializer = $this->container->get('jms_serializer');
           //$response = $serializer->serialize($data["title"], 'json');
            //$idCarrera  = $request->request->get('idCarrera');
            

           // $respuesta="SI";

            //return $this->render('TitulacionSisAcademicoBundle:Estudiantes:estudiantes_deudas.html.twig',compact("notas_act"));
           //return $jarray;
            $respuesta->setContent($jarray);
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
                   foreach ($materias as $key => $value) {
                     $arrayProceso=array();
                     $arrayProceso['Curso']=$value['Curso']; 
                     $arrayProceso['Idcurso']=$value['Idcurso']; 
                     $arrayProceso['Materia']=$value['Materia']; 
                     $arrayProceso['Idmateria']=$value['idMateria']; 
                     $arrayProceso['Veces']=$value['Veces']; 
                     $arrayProceso['Nivel']=$value['Nivel']; 
                     array_push($inscripcion, $arrayProceso); 

                  }


                  return $this->render('TitulacionSisAcademicoBundle:Estudiantes:listarmatricula.html.twig',
                                                  array('listaMaterias'=>$inscripcion,
                                                        'estudiante'=>$estudiante));

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

      
       public function pdfmatriculaAction(Request $request)
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
                   foreach ($materias as $key => $value) {
                     $arrayProceso=array();
                     $arrayProceso['Curso']=$value['Curso']; 
                     $arrayProceso['Materia']=$value['Materia']; 
                     $arrayProceso['Veces']=$value['Veces']; 
                     $arrayProceso['Nivel']=$value['Nivel']; 
                     array_push($inscripcion, $arrayProceso); 

                  }

                  //define('FPDF_FONTPATH','/fpdf/font/');
                  //require('/fpdf.php');
                  //var_dump(__DIR__);
                  $pdf=new FPDF();
                  $pdf->AddPage();
                  $pdf->SetFont('Arial','B',16);
                  $pdf->Cell(40,10,'¡Hola, Mundo!');
                  $pdf->Output();

               $jarray=json_encode($inscripcion);
                 $respuesta->setContent($jarray);
                 return $respuesta;

                /*  return $this->render('TitulacionSisAcademicoBundle:Estudiantes:listarmatricula.html.twig',
                                                  array('listaMaterias'=>$inscripcion,
                                                        'estudiante'=>$estudiante));*/

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
        
    }