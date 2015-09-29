<?php
   namespace Titulacion\SisAcademicoBundle\Controller;

    use Symfony\Bundle\FrameworkBundle\Controller\Controller;
    use Symfony\Component\HttpFoundation\Response;
    use Symfony\Component\HttpFoundation\Request;
    use Symfony\Component\HttpFoundation\JsonResponse;
    use Titulacion\SisAcademicoBundle\Helper\UgServices;

   class DocentesController extends Controller
   {
      var $v_error =false;
      var $v_html ="";
      var $v_msg  ="";

      public function indexAction(Request $request) //(Request $request)
      {
         $session=$request->getSession();

         $perfilEst   = $this->container->getParameter('perfilEst');
         $perfilDoc   = $this->container->getParameter('perfilDoc');
         $perfilAdmin = $this->container->getParameter('perfilAdmin'); 
         $perfilEstDoc = $this->container->getParameter('perfilEstDoc'); 
         $perfilEstAdm = $this->container->getParameter('perfilEstAdm'); 
         $perfilDocAdm = $this->container->getParameter('perfilDocAdm');

         if($session->has("perfil")) {
            if($session->get('perfil') == $perfilDoc || $session->get('perfil') == $perfilEstDoc || $session->get('perfil') == $perfilDocAdm){
               $idDocente     = $session->get('id_user');
               //$idDocente     = 1;
             
               $UgServices    = new UgServices;
               $datosCarrerasXML  = $UgServices->Docentes_getCarreras($idDocente);
               
               if($datosCarrerasXML!="") {
                  $datosCarreras = $datosCarrerasXML;
               }
               else {
               # Docente sin Carreras
               }
            
               $datosDocente	= array( 'idDocente' => $idDocente );
             
               return $this->render('TitulacionSisAcademicoBundle:Docentes:listadoCarreras.html.twig',
    									array(
    											'data' => array('datosDocente' => $datosDocente,  'datosCarreras' => $datosCarreras)
    										 )
                              );
            }else{
               $this->get('session')->getFlashBag()->add(
                                'mensaje',
                                'Los datos ingresados no son válidos'
                            );
               return $this->render('TitulacionSisAcademicoBundle:Home:login.html.twig');
            }
         }else{
            $this->get('session')->getFlashBag()->add(
                                'mensaje',
                                'Los datos ingresados no son válidos'
                            );
            return $this->render('TitulacionSisAcademicoBundle:Home:login.html.twig');
        }

      }
		
		
    public function listadoMateriasAction(Request $request)
      {	 
	 $session=$request->getSession();
         $idDocente  = $session->get('id_user');
	 //$idDocente  = $request->request->get('idDocente');
         $idCarrera  = $request->request->get('idCarrera');
         
         $datosDocente	= array( 'idDocente' => $idDocente );
         $datosCarrera2	= array( 'idCarrera' => $idCarrera );
         $datosMaterias	= array();
         //$idDocente = "1";
         //$idCarrera = "2";
		 
         $UgServices    = new UgServices;
         $datosMaterias  = $UgServices->Docentes_getMaterias($idDocente, $idCarrera);
/*
         if($datosMateriasXML!="") {
               foreach($datosMateriasXML->registros->registro as $datosCarrera) {
                  array_push($datosMaterias, (array)$datosCarrera);
               }
         }*/
         //para el render realmente deberia estar mandando la informacion de las materias
       
         return $this->render('TitulacionSisAcademicoBundle:Docentes:listadoMaterias.html.twig',
                        array(
                              'data' => array(
                                             'datosDocente' => $datosDocente,
                                             'datosCarrera' => $datosCarrera2,
                                             'datosMaterias' => $datosMaterias
                                        )
                        )
                     );
      }
		
      
		
      public function listadoAlumnosMateriaAction(Request $request)
      {
         $session=$request->getSession();
         $idDocente  = $session->get('id_user');
         //$idDocente= 7;
         $idMateria     = $request->request->get('idMateria');
         $idParalelo    = $request->request->get('idParalelo');
         $idCarrera     = $request->request->get('idCarrera');
         $fechaInicio   = $request->request->get('fechaInicio');
         $fechaFin      = $request->request->get('fechaFin');
         
         if( !isset($fechaInicio) || !isset($fechaFin) ){
            date_default_timezone_set ( "America/Guayaquil" );
            $day           = date('w');
            $fechaFin      = date('d-m-Y', strtotime('-'.$day.' days'));
            $fechaInicio   = date('d-m-Y', strtotime('-'.(6-$day).' days'));
         }
         else {
            $fechaFin      = str_replace("/","-",$fechaFin);
            $fechaInicio   = str_replace("/","-",$fechaInicio);
         }
         $anioConsulta  = date('o');
         
         $datosConsulta	= array( 
                                 'fechaInicio' => $fechaInicio,
                                 'fechaFin' => $fechaFin,
                                 'idDocente' => $idDocente,
                                 'idMateria' => $idMateria,
                                 'idParalelo' => $idParalelo,
                                 'anio' => $anioConsulta,
                                 'idCarrera' => $idCarrera
                                 );
         $UgServices    = new UgServices;
         $datosAsistenciasXML  = $UgServices->Docentes_getAsistenciasMaterias($datosConsulta);
         
         $dataAsistencia   = array();
         $arregloFechas    = array();
         
         if($datosAsistenciasXML!=NULL) {
            //PARA OBTENER EL ARREGLO DE FECHAS
            foreach($datosAsistenciasXML["alumno"][0] as $keyFecha => $valueFecha){
               //var_dump($keyFecha);
               //$regExp = "/(f)([0-9]{2}\\-[0-9]{2}\\-[0-9]{4})/";
               $regExp = "/(f)([0-9]{4}\\-[0-9]{2}\\-[0-9]{2})/";
               $tempFecha['diaVal'] = '';
               $tempFecha['diaNom'] = '';
               if(preg_match($regExp, $keyFecha, $matchesFecha)){
                  $tempFecha['diaVal'] = substr($keyFecha, 1);
                  //$tempFecha['diaVal'] =  date("d/m/Y",strtotime($tempFecha['diaVal']));     //Cambio de formato
                  $tempFecha['diaNom'] = $this->nombresDias( date('l', strtotime($tempFecha['diaVal'])) );
                  $tempFecha['diaVal'] =  date("d/m/Y",strtotime($tempFecha['diaVal']));     //Cambio de formato
                  array_push($arregloFechas, $tempFecha);
               }
            }
            //PARA GRABAR LOS ESTADOS DE LAS ASISTENCIAS POR ALUMNO
            foreach($datosAsistenciasXML["alumno"] as $dataAlumno){
               $dataAsistenciaReg   = array();
               $dataAsistenciaReg['nombres']   = $dataAlumno['nombres'];
               $dataAsistenciaReg['apellidos'] = $dataAlumno['apellidos'];
               $dataAsistenciaReg['fechas']    = array();
               //Para procesar las fechas que me han llegado, son dinamicas
               foreach($dataAlumno as $keyFecha => $valueFecha){
                  //$regExp = "/(f)([0-9]{2}\\-[0-9]{2}\\-[0-9]{4})/";
                  $regExp = "/(f)([0-9]{4}\\-[0-9]{2}\\-[0-9]{2})/";
                  if(preg_match($regExp, $keyFecha, $matchesFecha)){
                     array_push($dataAsistenciaReg['fechas'], $valueFecha);
                  }
               }
               
               array_push($dataAsistencia, $dataAsistenciaReg);
            }
         }

         return $this->render('TitulacionSisAcademicoBundle:Docentes:listadoAlumnosMateria.html.twig',
                         array(
                               'dataMateria' => array('fechasAsistencia' => $arregloFechas,
                                                      'datosAsistencia' => $dataAsistencia,
                                                      'fechaInicio'  => date("d/m/Y",strtotime($fechaInicio)),
                                                      'fechaFin'  => date("d/m/Y",strtotime($fechaFin)),
                                                      'idMateria' => $idMateria
                                                     )
                             )
                      );
      }
		
		public function notasAlumnosMateriaAction(Request $request)
      {
       //Menu de Notas por Materia para Profesor
       return $this->render('TitulacionSisAcademicoBundle:Docentes:notasAlumnosMateria.html.twig',
                         array(
                               'condition' => ''
                             )
                      );
      }
      
      
      
      public function listadoNotasAlumnosMateriaAction(Request $request)
      {
         $idDocente  = $request->request->get('idDocente');
         $idMateria  = $request->request->get('idMateria');
         //$idParalelo  = $request->request->get('idParalelo');
         $idParalelo  = 1;

         $datosConsulta	= array( 'idMateria' => $idMateria,
                                 'idParalelo' => $idParalelo,
                                 'idDocente' => $idDocente);
         
         $UgServices       = new UgServices;
         $datosNotasArray  = $UgServices->Docentes_getNotasMaterias($datosConsulta);
         
         $dataProcesar = $datosNotasArray["registro"];
         
         $datosGeneralesListado["notaMinima"]	= $dataProcesar["notaMinima"];
         $datosGeneralesListado["idProfesor"]	= $dataProcesar["idProfesor"];
         $datosGeneralesListado["profesor"]		= $dataProcesar["profesor"];
         $datosGeneralesListado["idMateria"]		= $dataProcesar["idMateria"];
         $datosGeneralesListado["materia"]		= $dataProcesar["materia"];
         $datosGeneralesListado["idParalelo"]	= $dataProcesar["idParalelo"];
         $datosGeneralesListado["paralelo"]		= $dataProcesar["paralelo"];


         foreach($dataProcesar["periodos"]["periodo"] as $periodoCheck) {
            if(is_numeric($periodoCheck["parcial"])) {
               $nombreKey	= "Parcial_".strtolower(str_replace(" ","_",$periodoCheck["parcial"]));
            }
            else {
               $nombreKey	= strtolower(str_replace(" ","_",$periodoCheck["parcial"]));
            }

            $periodosMostrar[$nombreKey]           		= array();
            $periodosMostrar[$nombreKey]["componente"]		= array();

            $iComponente = 0;
            foreach($periodoCheck["componentePeriodo"] as $keyComp => $componente) {
               if($keyComp=="idNota") {
                  $periodosMostrar[$nombreKey]["idComponente"]	= $componente;
               }
               if($keyComp=="componente") {
                  $periodosMostrar[$nombreKey]["componente"]		= $componente;
               }
            }
            $periodosMostrar[$nombreKey]["cantComponentes"] = count($periodosMostrar[$nombreKey]["componente"]);
            $periodosMostrar[$nombreKey]["totalizar"]		= $periodoCheck["totalizar"];
            if($periodosMostrar[$nombreKey]["totalizar"]=="SI") {
               $periodosMostrar[$nombreKey]["cantComponentes"]++;
               array_push($periodosMostrar[$nombreKey]["idComponente"], "99999999");
               array_push($periodosMostrar[$nombreKey]["componente"], "total");
            }
         }

         //var_dump($periodosMostrar);
         //var_dump($dataProcesar["estudiantes"]["estudiante"]);
         //echo count($dataProcesar["estudiantes"]["estudiante"]);

         $datosEstudiantes	= array();
         foreach($dataProcesar["estudiantes"]["estudiante"] as $estudiante) {
            $tempArrayEst = NULL;
            $tempArrayEst["idEstudiante"]	= $estudiante["idEstudiante"];
            $tempArrayEst["estudiante"]		= $estudiante["estudiante"];
            $tempArrayEst["ciclo"]			= $estudiante["ciclo"];
            $tempArrayEst["estadoCiclo"]	= $estudiante["estadoCiclo"];
            $tempArrayEst["parciales"]		= array();
            //Creo el array para grabar las notas
            foreach($periodosMostrar as $keyPeriodo => $valuePeriodo) {
               $tempArrayEst["parciales"][$keyPeriodo]		= array();
               $tempComponente	= NULL;
               if(is_array($valuePeriodo["componente"])){
                  foreach($valuePeriodo["componente"] as $componente) {
                     $tempComponente	= strtolower($componente);
                     $tempComponente	= str_replace("á","a",$tempComponente);
                     $tempComponente	= str_replace("é","e",$tempComponente);
                     $tempComponente	= str_replace("í","i",$tempComponente);
                     $tempComponente	= str_replace("ó","o",$tempComponente);
                     $tempComponente	= str_replace("ú","u",$tempComponente);
                     $tempComponente	= str_replace("ñ","n",$tempComponente);

                     $tempArrayEst["parciales"][$keyPeriodo][$tempComponente] = "-";
                  }
               }
               elseif($valuePeriodo["componente"]!=NULL) {
                  $tempComponente	= strtolower($valuePeriodo["componente"]);
                  $tempComponente	= str_replace("á","a",$tempComponente);
                  $tempComponente	= str_replace("é","e",$tempComponente);
                  $tempComponente	= str_replace("í","i",$tempComponente);
                  $tempComponente	= str_replace("ó","o",$tempComponente);
                  $tempComponente	= str_replace("ú","u",$tempComponente);
                  $tempComponente	= str_replace("ñ","n",$tempComponente);

                  $tempArrayEst["parciales"][$keyPeriodo][$tempComponente] = "-";
               }
            }


            //Para grabar las notas
            if(isset($estudiante["parciales"]["Parcial"])) {
               //Si entra aqui quiere decir que tiene SOLO UN parcial
               $tempComponente = NULL;
               if(is_numeric($estudiante["parciales"]["Parcial"])) {
                  $keyParcial	= "Parcial_".strtolower(str_replace(" ","_",$estudiante["parciales"]["Parcial"]));
               }
               else {
                  $keyParcial	= strtolower(str_replace(" ","_",$estudiante["parciales"]["Parcial"]));
               }

               if(isset($estudiante["parciales"]["notas"]["nota"]["Nota"])) {
                  //Si entra aqui es porque solo trae una nota (ej.Mejoramiento)
                  $keyComponente	= strtolower($estudiante["parciales"]["notas"]["nota"]["tipoNota"]);
                  $notaComponente	= $estudiante["parciales"]["notas"]["nota"]["Nota"];

                  $tempComponente	= strtolower($keyComponente);
                  $tempComponente	= str_replace("á","a",$tempComponente);
                  $tempComponente	= str_replace("é","e",$tempComponente);
                  $tempComponente	= str_replace("í","i",$tempComponente);
                  $tempComponente	= str_replace("ó","o",$tempComponente);
                  $tempComponente	= str_replace("ú","u",$tempComponente);
                  $keyComponente	= str_replace("ñ","n",$tempComponente);
                  $tempArrayEst["parciales"][$keyParcial][$keyComponente] = $notaComponente;
               }
               else {
                  foreach($estudiante["parciales"]["notas"]["nota"] as $dataComponente){
                     //var_dump($dataComponente);
                     $keyComponente	= strtolower($dataComponente["tipoNota"]);
                     $notaComponente	= $dataComponente["Nota"];

                     $tempComponente	= $keyComponente;
                     $tempComponente	= str_replace("á","a",$tempComponente);
                     $tempComponente	= str_replace("é","e",$tempComponente);
                     $tempComponente	= str_replace("í","i",$tempComponente);
                     $tempComponente	= str_replace("ó","o",$tempComponente);
                     $tempComponente	= str_replace("ú","u",$tempComponente);
                     $keyComponente	= str_replace("ñ","n",$tempComponente);
                     $tempArrayEst["parciales"][$keyParcial][$keyComponente] = $notaComponente;
                  }
                  
               }


            }
            else {
               //Si entra aqui quiere decir que tiene mas de un parcial
               //var_dump($estudiante["parciales"]);
               foreach($estudiante["parciales"] as $keyParcial => $dataParcial) {
                  //var_dump($dataParcial);
                  if(is_numeric($dataParcial["Parcial"])) {
                     $keyParcial	= "Parcial_".strtolower(str_replace(" ","_",$dataParcial["Parcial"]));
                  }
                  else {
                     $keyParcial	= strtolower(str_replace(" ","_",$dataParcial["Parcial"]));
                  }

                  foreach($dataParcial["notas"] as $keyNotas => $dataNotas) {

                     if(isset($dataNotas["tipoNota"])){
                        //Si entra aqui es porque llega solo una nota
                        $keyComponente	= strtolower($dataNotas["tipoNota"]);
                        $notaComponente	= $dataNotas["Nota"];

                        $tempComponente	= $keyComponente;
                        $tempComponente	= str_replace("á","a",$tempComponente);
                        $tempComponente	= str_replace("é","e",$tempComponente);
                        $tempComponente	= str_replace("í","i",$tempComponente);
                        $tempComponente	= str_replace("ó","o",$tempComponente);
                        $tempComponente	= str_replace("ú","u",$tempComponente);
                        $keyComponente	= str_replace("ñ","n",$tempComponente);
                     }
                     else {
                        //var_dump($dataNotas);
                        foreach($dataNotas as $dataComponente){
                           //var_dump($dataComponente);
                           $keyComponente	= strtolower($dataComponente["tipoNota"]);
                           $notaComponente	= $dataComponente["Nota"];

                           $tempComponente	= $keyComponente;
                           $tempComponente	= str_replace("á","a",$tempComponente);
                           $tempComponente	= str_replace("é","e",$tempComponente);
                           $tempComponente	= str_replace("í","i",$tempComponente);
                           $tempComponente	= str_replace("ó","o",$tempComponente);
                           $tempComponente	= str_replace("ú","u",$tempComponente);
                           $keyComponente	= str_replace("ñ","n",$tempComponente);
                           $tempArrayEst["parciales"][$keyParcial][$keyComponente] = $notaComponente;
                        }
                     }

                     $tempArrayEst["parciales"][$keyParcial][$keyComponente] = $notaComponente;
                     if($periodosMostrar[$keyParcial]["totalizar"]=="SI"){
                        $tempArrayEst["parciales"][$keyParcial]["total"]		= $dataParcial["total"];
                     }
                  }				
               }

            }

            array_push($datosEstudiantes, $tempArrayEst);
         }
         
       //listadoMaterias
         return $this->render('TitulacionSisAcademicoBundle:Docentes:listadoNotasMateria.html.twig',
                         array(
                              'datosGenerales' => $datosGeneralesListado,
                              'periodosMostrar' => $periodosMostrar,
                              'datosEstudiantes' => $datosEstudiantes
                             )
                      );
      }
      
      public function visionGeneralMateriaAction(Request $request)
      {
         $idDocente  = $request->request->get('idDocente');
         $idMateria  = $request->request->get('idMateria');
         
         $datosDocente	= array( 'idDocente' => $idDocente );

         $datosMateria	= array( 'idMateria' => $idMateria );

       //listadoMaterias
       return $this->render('TitulacionSisAcademicoBundle:Docentes:visionGeneralMateria.html.twig',
                         array(
                               'dataDocente' => array('datosDocente' => $datosDocente ),
                               'dataMateria' => array('datosMateria' => $datosMateria )
                             )
                      );
      }
      
             public function mostraralumnosAction(Request $request)
        { 
          
            $notas='';
            
            $parametro1 =$request->request->get('parametro1');
            
         $response   		= new JsonResponse();
         $withoutModal       = true;
         
            $idDocente     = 1;
            $carrera  =1;
            $UgServices    = new UgServices;
            $datosAlumnosXML  = $UgServices->Docentes_getAlumnos($idDocente,$carrera);
            
           /* if($datosAlumnosXML!="") {
               $nombresalumnos = array();
               foreach($datosAlumnosXML->registros->registro as $datosAlumnos) {
                  array_push($nombresalumnos, (array)$datosAlumnos);
               }
            }*/
          
        
        $tareas =  array(
                              array( 'tarealm' => 'leccion1'),
                              array( 'tarealm' => 'leccion2'),
                              array( 'tarealm' => 'taller1'),
                              array( 'tarealm' => 'taller2'),
                           );
        
			$this->v_html = $this->renderView('TitulacionSisAcademicoBundle:Docentes:ingresonotas.html.twig',
						  array(
							   'arr_datos'	=> $datosAlumnosXML,
                                                           'arr_tareas'	=> $tareas,
                                                           'cantidad'   => '',
                                                          'pruebaexam'	=> $parametro1,
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
        
              public function mostraralumnos2Action(Request $request)
        { 
            
            $notas='';
            
         $response   		= new JsonResponse();
         $withoutModal       = true;
          
	$nombresalumnos =  array(
                              array( 'Nombrealm' => 'Carlos Quiñonez'),
                              array( 'Nombrealm' => 'Juan Romero'),
                              array( 'Nombrealm' => 'Daniel Verdesoto'),
                              array( 'Nombrealm' => 'Fernando Lopez'),
                              array( 'Nombrealm' => 'Alexandra Gutierrez'),
                              array( 'Nombrealm' => 'Roberto Carlos'),
                              array( 'Nombrealm' => 'Orlando Macias'),
                              array( 'Nombrealm' => 'Fernanda Montero'),
                              array( 'Nombrealm' => 'Ana Kam'),
                              array( 'Nombrealm' => 'Angel Fuentes'),
                           );   
        
        $tareas =  array(
                              array( 'tarealm' => 'leccion1'),
                              array( 'tarealm' => 'leccion2'),
                              array( 'tarealm' => 'taller1'),
                              array( 'tarealm' => 'taller2'),
                           );
        
			$this->v_html = $this->renderView('TitulacionSisAcademicoBundle:Docentes:ingresonotas2.html.twig',
						  array(
							   'arr_datos'	=> $nombresalumnos,
                                                      'arr_tareas'	=> $tareas,
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
        
               public function mostraralumnos3Action(Request $request)
        { 
            
            $notas='';
            
         $response   		= new JsonResponse();
         $withoutModal       = true;
          
	$nombresalumnos =  array(
                              array( 'Nombrealm' => 'Carlos Quiñonez'),
                              array( 'Nombrealm' => 'Juan Romero'),
                              array( 'Nombrealm' => 'Daniel Verdesoto'),
                              array( 'Nombrealm' => 'Fernando Lopez'),
                              array( 'Nombrealm' => 'Alexandra Gutierrez'),
                              array( 'Nombrealm' => 'Roberto Carlos'),
                              array( 'Nombrealm' => 'Orlando Macias'),
                              array( 'Nombrealm' => 'Fernanda Montero'),
                              array( 'Nombrealm' => 'Ana Kam'),
                              array( 'Nombrealm' => 'Angel Fuentes'),
                           );   
        
			$this->v_html = $this->renderView('TitulacionSisAcademicoBundle:Docentes:ingresoexamen.html.twig',
						  array(
							   'arr_datos'	=> $nombresalumnos,
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
        
       public function ingresonotasAction(Request $request)
        { 
            
            $notas='';
            
            $totalalm =$request->request->get('hdcountalm');
            $totaltar =$request->request->get('hdcounttar');
            
            ;
            for($i=1; $i<=$totalalm; $i++)
            {
                $notas['alumno'][] =$request->request->get('hdalumno_'.$i);
                for($x=1; $x<=$totaltar; $x++)
               {//echo $x."_".$i."---";
                $notas['titulo1'][] =$request->request->get('hdtarea_'.$x);
                $notas['academico'.$i][] =$request->request->get('academicos_'.$i.'_'.$x);
               }
              // echo "otro";
                $notas['titulo2'][] ='Examen';
                $notas['examen'][] =$request->request->get('examen_'.$i);
            }
            print_r($notas) ;
			$pagina = 1;
        $nombresalumnos  =[];
			return $this->render('TitulacionSisAcademicoBundle:Docentes:notasAlumnosMateria.html.twig',
						  array(
							   'condition'	=> 'ingresonotas',
                                                           'cantidad'   => '',
                                                           'msg'   	=> $this->v_msg
						  ));
        }

       public function ingresonotas2Action(Request $request)
        { 
            
            $notas='';
            
            $total =$request->request->get('hdcount');
            for($i=1; $i<$total; $i++)
            {
                $notas['academico1'][] =$request->request->get('academicos1_'.$i);
                $notas['examen1'][] =$request->request->get('examen1_'.$i);
                $notas['academico2'][] =$request->request->get('academicos2_'.$i);
                $notas['examen2'][] =$request->request->get('examen2_'.$i);
                $notas['examen3'][] =$request->request->get('examen3_'.$i);
            }
            print_r($notas) ;
			$pagina = 1;
          
	$nombresalumnos =  array(
                              array( 'Nombrealm' => 'Carlos Quiñonez'),
                              array( 'Nombrealm' => 'Juan Romero'),
                              array( 'Nombrealm' => 'Daniel Verdesoto'),
                              array( 'Nombrealm' => 'Fernando Lopez'),
                              array( 'Nombrealm' => 'Alexandra Gutierrez'),
                              array( 'Nombrealm' => 'Roberto Carlos'),
                              array( 'Nombrealm' => 'Orlando Macias'),
                              array( 'Nombrealm' => 'Fernanda Montero'),
                              array( 'Nombrealm' => 'Ana Kam'),
                              array( 'Nombrealm' => 'Angel Fuentes'),
                           );   
			return $this->render('TitulacionSisAcademicoBundle:Docentes:ingresonotas.html.twig',
						  array(
							   'arr_datos'	=> $nombresalumnos,
                                                           'cantidad'   => '',
                                                           'msg'   	=> $this->v_msg
						  ));
        }
     
        public function ingresoexamenAction(Request $request)
        { 
            
            $notas='';
            
            $total =$request->request->get('hdcount');
            for($i=1; $i<$total; $i++)
            {
                $notas['academico1'][] =$request->request->get('academicos1_'.$i);
                $notas['examen1'][] =$request->request->get('examen1_'.$i);
                $notas['academico2'][] =$request->request->get('academicos2_'.$i);
                $notas['examen2'][] =$request->request->get('examen2_'.$i);
                $notas['examen3'][] =$request->request->get('examen3_'.$i);
            }
            print_r($notas) ;
			$pagina = 1;
          
	$nombresalumnos =  array(
                              array( 'Nombrealm' => 'Carlos Quiñonez'),
                              array( 'Nombrealm' => 'Juan Romero'),
                              array( 'Nombrealm' => 'Daniel Verdesoto'),
                              array( 'Nombrealm' => 'Fernando Lopez'),
                              array( 'Nombrealm' => 'Alexandra Gutierrez'),
                              array( 'Nombrealm' => 'Roberto Carlos'),
                              array( 'Nombrealm' => 'Orlando Macias'),
                              array( 'Nombrealm' => 'Fernanda Montero'),
                              array( 'Nombrealm' => 'Ana Kam'),
                              array( 'Nombrealm' => 'Angel Fuentes'),
                           );   
			return $this->render('TitulacionSisAcademicoBundle:Docentes:ingresonotas.html.twig',
						  array(
							   'arr_datos'	=> $nombresalumnos,
                                                           'cantidad'   => '',
                                                           'msg'   	=> $this->v_msg
						  ));
        }
        
        public function consultaNotasAction(Request $request)
        { 
           $notas='';
            
         $response   		= new JsonResponse();
         $withoutModal       = true;
          
	$nombresalumnos =  array(
                              array( 'Nombrealm' => 'Carlos Quiñonez'),
                              array( 'Nombrealm' => 'Juan Romero'),
                              array( 'Nombrealm' => 'Daniel Verdesoto'),
                              array( 'Nombrealm' => 'Fernando Lopez'),
                              array( 'Nombrealm' => 'Alexandra Gutierrez'),
                              array( 'Nombrealm' => 'Roberto Carlos'),
                              array( 'Nombrealm' => 'Orlando Macias'),
                              array( 'Nombrealm' => 'Fernanda Montero'),
                              array( 'Nombrealm' => 'Ana Kam'),
                              array( 'Nombrealm' => 'Angel Fuentes'),
                           );   
        
			$this->v_html = $this->renderView('TitulacionSisAcademicoBundle:Docentes:consultanotas.html.twig',
						  array(
							   'arr_datos'	=> $nombresalumnos,
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
        
      function nombresDias($nombreIngles) {
         $diasEspaniol  = array("lunes", "martes", "miércoles", "jueves", "viernes", "sábado", "domingo");
         $diasIngles    = array("monday", "tuesday", "wednesday", "thursday", "friday", "saturday", "sunday");
         
         $nombreIngles  = str_replace($diasIngles, $diasEspaniol, strtolower($nombreIngles));
         
         return $nombreIngles;
      }
		
   }