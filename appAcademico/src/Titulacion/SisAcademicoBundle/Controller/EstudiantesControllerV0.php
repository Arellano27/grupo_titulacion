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
            $idEstudiante  =1234;

           if ($session->has("perfil")) {
               if ($session->get('perfil') == $perfilEst || $session->get('perfil') == $perfilEstDoc || $session->get('perfil') == $perfilEstAdm) {
                //set_error_handler('errorHandler');
               // set_error_handler();
                try
                {
                      $lcFacultad="";
                      $lcCarrera="";
                       $Carreras = array();
                        $UgServices = new UgServices;
                        $xml = $UgServices->getConsultaCarreras($idEstudiante);
                        
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

                                $cuantos=count($Carreras);


                                return $this->render('TitulacionSisAcademicoBundle:Estudiantes:estudiantes_home.html.twig',array(
                                                  'facultades' =>  $Carreras,
                                                  'idEstudiante'=>$idEstudiante,
                                                  'idFacultad'=>$lcFacultad,
                                                  'idCarrera'=>$lcCarrera,
                                                  'cuantos'=>$cuantos

                                               ));
 
                        }
                        else
                        {
                          throw new \Exception('Un error');
                        }
                        
                  }catch (\Exception $e)
                      {
                       
                        return $this->render('TitulacionSisAcademicoBundle:Estudiantes:error.html.twig');
                      }

              
                          //throw new \Exception('Un error');
                

              
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
               if($session->get('perfil') == $perfilDoc || $session->get('perfil') == $perfilEstDoc || $session->get('perfil') == $perfilDocAdm){
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
        
               if($session->get('perfil') == $perfilDoc || $session->get('perfil') == $perfilEstDoc || $session->get('perfil') == $perfilDocAdm){

                     $idEstudiante  = $request->request->get('idEstudiante');
                     $idCarrera  = $request->request->get('idCarrera');
                     $idIndica  = $request->request->get('idIndica');
                     $idFacultad  = 1222;
                     $listaMaterias=array();
                     $materiaObject=array();

                     if ($idIndica=='nh')
                     {      

                            $xml = $UgServices->getConsultaNotas_nh($idFacultad,$idCarrera,$idEstudiante);

                             foreach($xml->p_xmlSalida->materia as $Periodo) {
                                //$Periodo->materias->ciclo;
                                  $lcMaterias="xxxx";
                                   $materiaObject = array(
                                      'Semestre' => $lcMaterias,
                                       'Materias'=>array(),
                                  );
                                   

                                   $lscursos=array();
                                  foreach($Periodo->materias as $inscripcion) {
                                    
                                      $Nombre=$inscripcion->materia;
                                      $Veces=$inscripcion->veces;
                                      $Nota1=$inscripcion->nota1;
                                      $Nota1A="";
                                      $Nota1E="";
                                      $Nota2=$inscripcion->nota2;
                                      $Nota2A="";
                                      $Nota2E="";
                                      $Supenso="";
                                      $Promedio=$inscripcion->promedio;
                                      $Estado=$inscripcion->estadoMateria;
                                      $lscursos=array('Materia'=> $Nombre,
                                                      'Veces'=> $Veces,
                                                      'Nota1'=>$Nota1,
                                                      'Nota1A'=>$Nota1A,
                                                      'Nota1E'=>$Nota1E,
                                                      'Nota2'=>$Nota2,
                                                      'Nota2A'=>$Nota2A,
                                                      'Nota2E'=>$Nota2E,
                                                      'Suspenso'=>$Supenso,
                                                      'Promedio'=>$Promedio,
                                                      'Estado'=>$Estado);
                                      
                                      array_push($materiaObject["Materias"], $lscursos);
                                  }
                                  
                                  array_push($listaMaterias, $materiaObject);
                              }    



                         }
                            if ($idIndica=='na')
                            {
                               $xml = $UgServices->getConsultaNotas_act($idFacultad,$idCarrera,$idEstudiante);
                               
                              $lscursos=array();
                              //$lcMaterias='Notas Actuales';
                             foreach($xml->p_xmlSalida->materia as $actual) {
                                 $lcMaterias="Actual";
                                 $materiaObject = array(
                                          'Semestre' => $lcMaterias,
                                           'Materias'=>array(),
                                      );

                                      
                                      foreach($actual->materias as $materias) {
                                              $Nombre=$materias->materia;
                                              $Veces=$materias->veces;
                                              $Nota1=$materias->nota1;
                                              $Nota2=$materias->nota2;
                                              $Supenso="";
                                              $Promedio=$materias->promedio;
                                              $Estado=$materias->estadoMateria;
                                              $lscursos=array('Materia'=> $Nombre,
                                                              'Veces'=> $Veces,
                                                              'Nota1'=>$Nota1,
                                                              'Nota2'=>$Nota2,
                                                              'Suspenso'=>$Supenso,
                                                              'Promedio'=>$Promedio,
                                                              'Estado'=>$Estado);
                                              array_push($materiaObject["Materias"], $lscursos);
                                            }

                                    array_push($listaMaterias, $materiaObject);
                                  }
                            }

                                                                      
                    return $this->render('TitulacionSisAcademicoBundle:Estudiantes:listarmaterias.html.twig',
                                          array('listaMaterias'=>$listaMaterias,
                                               'indica'=>$idIndica ) 
                                          );


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
        
        public function menuderechoAction(Request $request)
        {

          $session=$request->getSession();

           
        $perfilEst   = $this->container->getParameter('perfilEst');
        $perfilDoc   = $this->container->getParameter('perfilDoc');
        $perfilAdmin = $this->container->getParameter('perfilAdmin'); 
        $perfilEstDoc = $this->container->getParameter('perfilEstDoc'); 
        $perfilEstAdm = $this->container->getParameter('perfilEstAdm'); 
        $perfilDocAdm = $this->container->getParameter('perfilDocAdm');


           if ($session->has("perfil")) {
                if($session->get('perfil') == $perfilDoc || $session->get('perfil') == $perfilEstDoc || $session->get('perfil') == $perfilDocAdm){
                   $idEstudiante  = $request->request->get('idEstudiante');
                   $idCarrera  = $request->request->get('idCarrera');
                   $idIndica  = $request->request->get('idIndica');
                   return $this->render('TitulacionSisAcademicoBundle:Estudiantes:menuderecho.html.twig',
                                        array('idCarrera'=>$idCarrera) );


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


        
    }