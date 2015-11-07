<?php
   namespace Titulacion\SisAcademicoBundle\Controller;

    use Symfony\Bundle\FrameworkBundle\Controller\Controller;
    use Symfony\Component\HttpFoundation\Response;
    use Symfony\Component\HttpFoundation\Request;
    use Symfony\Component\HttpFoundation\JsonResponse;
    use Titulacion\SisAcademicoBundle\Helper\UgServices;
    use Titulacion\SisAcademicoBundle\fpdf\fpdf;
    use Symfony\Component\HttpFoundation\ResponseHeaderBag;
    use \PHPExcel_Style_Alignment;
    use \PHPExcel_Style_Border;

   class DocentesController extends Controller
   {
      var $v_error =false;
      var $v_html ="";
      var $v_msg  ="";

       var $pdf="";

      var $v_message="";
      var $idCarrera="";


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
         $inicioCiclo   = $request->request->get('inicioCiclo');
         $finCiclo      = $request->request->get('finCiclo');

         if( !isset($fechaInicio) || !isset($fechaFin) ){
            date_default_timezone_set ( "America/Guayaquil" );
            $day           = date('w');
            $fechaFin      = date('d-m-Y');
            if($day == 0){ $day = 7; }	//Esto es para el domingo
            $day--;
            $fechaInicio   = date('d-m-Y', strtotime('-'.($day).' days'));
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

         $datosAsistencia   = $this->procesarListadoAsistenciasEstudiantes($datosAsistenciasXML);

            
               
               
 
         return $this->render('TitulacionSisAcademicoBundle:Docentes:listadoAlumnosMateria.html.twig',
                         array(
                               'dataMateria' => array('fechasAsistencia' => $datosAsistencia["arregloFechas"],
                                                      'datosAsistencia' => $datosAsistencia["dataAsistencia"],
                                                      'fechaInicio'  => date("d/m/Y",strtotime($fechaInicio)),
                                                      'fechaFin'  => date("d/m/Y",strtotime($fechaFin)),
                                                      'idMateria' => $idMateria,
                                                      'inicioCiclo' => $inicioCiclo,
                                                      'finCiclo' => $finCiclo,
                                                      'datosConsultaActual' => $datosConsulta
                                                     )
                             )
                      );
      }



		public function notasAlumnosMateriaAction(Request $request)
      {
               $UgServices    = new UgServices;
               $session=$request->getSession();
               $idDocente="";
               $idCarrera="";
               $docente= $session->get('id_user');
               $idMateria  = $request->request->get('idMateria');
               $idCarrera  = $request->request->get('idCarrera');
               $ciclo  = $request->request->get('ciclo');
               
               $nom_materia  = $request->request->get('nom_materia');
               $paralelo  = $request->request->get('paralelo');
               $session->set("nom_materia",$nom_materia);
               $session->set("paralelo",$paralelo);
               
                $ciclo='18';
                $docente='5';
                $idMateria='251';
            
            $trama ="<PI_ID_CICLO_DETALLE>".$ciclo."</PI_ID_CICLO_DETALLE>
                         <PI_ID_USUARIO_PROFESOR>".$docente."</PI_ID_USUARIO_PROFESOR>
                         <PI_ID_MATERIA>".$idMateria."</PI_ID_MATERIA>
                         <PARCIAL>1</PARCIAL>
                         <PI_ESTUDIANTE>16</PI_ESTUDIANTE>";
            $idMateria  = $request->request->get('idMateria');
            
               $datosParciales  = $UgServices->Docentes_gettareaxparcial($trama);
               
              /* print_r($datosParciales);
               exit();*/
               
               for ($i=1; $i<=$datosParciales->registro[0]->cantParciales; $i++)
            {
                     $arr_parcial[$i]['parcial']='parcial #'.$i;
                          
            }
               $tareas= $datosParciales->registro[0]->periodos->periodo[0]->componentePeriodo;
               $i=0;
               foreach ($tareas->idNota as $idnota) {
               $registros[$i]['idNota']= (string)$idnota;
               $i++;
               }
               $i=0;
               foreach ($tareas->componente as $componente) {
               $registros[$i]['componente']= (string)$componente;
               $i++;
               }
//               print_r($registros);
//               exit();
               
               
              // print_r($datosParciales);
              // echo $datosParciales->registro[0]->cantParciales;
               // print_r($datosParciales[0]['periodos']);
               //echo $datosParciales[0]['cantparciales'];
               $session=$request->getSession();
               $session->set("idMateria",$idMateria);
               
               for ($i=1; $i<=$datosParciales->registro[0]->cantParciales; $i++)
            {
                     $arr_parcial[$i]['parcial']='parcial #'.$i;
                          
            }
       //Menu de Notas por Materia para Profesor
       return $this->render('TitulacionSisAcademicoBundle:Docentes:notasAlumnosMateria.html.twig',
                         array(
                               'condition' => '',
                               'arr_parcial' => $arr_parcial,
                               'idMateria' => $idMateria,
                             'idCarrera' => $idCarrera
                             )
                      );
      }


      public function listadoNotasAlumnosMateriaAction(Request $request)
      {
         $session=$request->getSession();
         $idDocente  = $session->get('id_user');
         $idMateria  = $request->request->get('idMateria');
         $idCarrera  = $request->request->get('idCarrera');
         if(NULL!==$request->request->get('idParcial')){
            $idParcial = $request->request->get('idParcial');
         
         
         
         }
         else{
            $idParcial = 'todos';
         }
         $UgServices       = new UgServices;
         
         /*Consulta de la información de los parciales - INICIO*/
         $datosConsultaParciales    = array( 'idCarrera' => $idCarrera);
         $datosParcialesArray       = $UgServices->Docentes_getParcialesCarrera($datosConsultaParciales);
         /*Consulta de la información de los parciales - INICIO*/
         
         
         /*Consulta de la información de las notas - INICIO*/
         //Consulta todos los parciales, es un WS diferente para consulta por parcial
         if($idParcial == 'todos'){
            $datosConsulta	= array( 'idMateria' => $idMateria,
                  
                                    'idDocente' => $idDocente);
          
            $datosNotasArray  = $UgServices->Docentes_getNotasMaterias($datosConsulta);
            
         }
         else {
            
            $datosConsulta	= array( 'idMateria' => $idMateria,
                                    'idDocente' => $idDocente,
                                    'idParcial' => $idParcial);
            $datosNotasArray  = $UgServices->Docentes_getNotasMateriasPorParcial($datosConsulta);
         }
         //print_r($datosNotasArray);
         
         $datosReturnArray = $this->procesarListadoNotasEstudiantes($datosNotasArray);
         /*Consulta de la información de las notas - FIN*/

            
         return $this->render('TitulacionSisAcademicoBundle:Docentes:listadoNotasMateria.html.twig',
                         array(
                              'identificaParcial' => $datosParcialesArray,
                              'datosGenerales' => $datosReturnArray["datosGenerales"],
                              'periodosMostrar' => $datosReturnArray["periodosMostrar"],
                              'datosEstudiantes' => $datosReturnArray["datosEstudiantes"],
                              'parcialActual' => $idParcial,
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
      
      public function exportarListadoNotasAlumnosMateriaAction(Request $request, $idMateria, $idParcial)
      {
         $session=$request->getSession();
         $idDocente  = $session->get('id_user');
         
         try {
//            $idDocente  = 31;
//            $idMateria  = 2271;

            $datosConsulta	= array( 'idMateria' => $idMateria,
                                    'idDocente' => $idDocente,
                                    'idParcial' => $idParcial);

            $UgServices       = new UgServices;
            /*Consulta de la información de las notas - INICIO*/
            //Consulta todos los parciales, es un WS diferente para consulta por parcial
            if($idParcial == 'todos'){
               $datosConsulta	= array( 'idMateria' => $idMateria,
                                       'idDocente' => $idDocente);
               $datosNotasArray  = $UgServices->Docentes_getNotasMaterias($datosConsulta);
            }
            else {
               $datosConsulta	= array( 'idMateria' => $idMateria,
                                       'idDocente' => $idDocente,
                                       'idParcial' => $idParcial);
               $datosNotasArray  = $UgServices->Docentes_getNotasMateriasPorParcial($datosConsulta);
            }
            $datosReturnArray = $this->procesarListadoNotasEstudiantes($datosNotasArray);
            /*Consulta de la información de las notas - FIN*/


           // $datosReturnArray = $this->procesarListadoNotasEstudiantes($datosNotasArray);

            $phpExcelObject = $this->get('phpexcel')->createPHPExcelObject();

            $phpExcelObject->getProperties()->setCreator("UG")
                ->setLastModifiedBy("UG")
                ->setTitle("Listado de Notas")
                ->setSubject("Listado de Notas")
                ->setDescription("Listado de Notas.")
                ->setKeywords("UG listado notas")
                ->setCategory("academico");
            //Definicion de Estilos - INICIO
            $estiloCelda["centradoHorizVert"] = array(
                                                   'alignment' => array(
                                                      'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                                                      'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
                                                   )
                                                );
            
            $estiloCelda["izquierdoHorizontal"] = array(
                                                   'alignment' => array(
                                                      'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT
                                                   )
                                                );
            
            $estiloCelda["allborders"] =  array(
                                             'borders' => array(
                                               'allborders' => array(
                                                 'style' => PHPExcel_Style_Border::BORDER_THIN
                                               )
                                             )
                                          );
            
            $estiloCelda["textoNegrita"] =   array(
                                                "font" => array( "bold" => true)
                                             );
            //Definicion de Estilos - FIN
            //UN ARRAY PARA HACER UNA RELACION DIRECTA ENTRE LAS LETRAS
            //DE LAS COLUMNAS DE EXCEL Y EL ARREGLO DE DATOS QUE VOY A MANDAR
            $columnasExcel       = array('','A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z');
            $primeraFilaDatos    = 2;
            $primeraColumnaDatos = 2;  //La letra 'B'
            
            $celdaBordesInicial["letra"]  = $primeraColumnaDatos;
            $celdaBordesInicial["numero"] = $primeraFilaDatos;
            $celdaBordesFinal["letra"]    = NULL;
            $celdaBordesFinal["numero"]   = NULL;
            
            
            $phpExcelObject->setActiveSheetIndex(0);
            
            $phpExcelObject->getDefaultStyle()->applyFromArray($estiloCelda["centradoHorizVert"]);   //Centrar el texto
            
            //Definicion de Cabeceras - INICIO
            $primeraColumnaCabecera = $primeraColumnaDatos;
            $phpExcelObject->getActiveSheet()->getColumnDimension($columnasExcel[$primeraColumnaCabecera])->setWidth(50);
            $phpExcelObject->setActiveSheetIndex(0)->setCellValue($columnasExcel[$primeraColumnaCabecera].$primeraFilaDatos, 'ALUMNOS');
            $iniciaFilaAlumnos = $primeraFilaDatos;
            $finFilaAlumnos    = $primeraFilaDatos+1;
            $phpExcelObject->setActiveSheetIndex(0)->mergeCells($columnasExcel[$primeraColumnaCabecera].$iniciaFilaAlumnos.":".$columnasExcel[$primeraColumnaCabecera].$finFilaAlumnos);
            
            //Para generar las columnas dinamicas - inicio
            $colspanParciales = $primeraColumnaCabecera+1;
            
            foreach(range($columnasExcel[$colspanParciales],'Z') as $columnID) { //Define el ancho de todas las columnas que vienen
               $phpExcelObject->getActiveSheet()->getColumnDimension($columnID)->setWidth(15);              //Ancho de las columnas
               $phpExcelObject->getActiveSheet()->getStyle($columnID)->getAlignment()->setWrapText(true);   //Ajuste de Texto
            }
            
            foreach($datosReturnArray["periodosMostrar"] as $nombreParcial => $dataParcial) {
               $nombreParcial       = ucfirst(str_replace("_"," ", $nombreParcial));
               $cantComponentes     = count($dataParcial["componente"]);
               $finColspanParciales = $colspanParciales+$cantComponentes-1;
               //Para presentar los nombres de los parciales
               $phpExcelObject->setActiveSheetIndex(0)->mergeCells($columnasExcel[$colspanParciales].$iniciaFilaAlumnos.":".$columnasExcel[$finColspanParciales].$iniciaFilaAlumnos);
               $phpExcelObject->setActiveSheetIndex(0)->setCellValue($columnasExcel[$colspanParciales].$primeraFilaDatos, $nombreParcial);
               
               //Para presentar los nombres de los componentes - inicio
               $filaComponentes  = $finFilaAlumnos;
               if(!is_array($dataParcial["componente"])){   //Este caso ocurre para SUSPENSO
                  $dataParcial["componente"]	= explode("*",$dataParcial["componente"]);
               }
               
               $phpExcelObject->getActiveSheet()
                  ->fromArray(
                      $dataParcial["componente"],   // Array, Los nombres de los componentes
                      NULL,                         // Array, si es que tengo los valores que van para cada componente
                      $columnasExcel[$colspanParciales].$finFilaAlumnos         // La coordenada (Ej. C3) donde quiero que comience a poner los valores
                  );
               //Para presentar los nombres de los componentes - fin
               
               $colspanParciales    = $finColspanParciales+1;

            }
            
            //Para presentar la cabeceras de la columna de resultados - inicio
            if($idParcial == 'todos'){
               $phpExcelObject->setActiveSheetIndex(0)->setCellValue($columnasExcel[$colspanParciales].$primeraFilaDatos, 'RESULTADO DEL SEMESTRE');
               $phpExcelObject->setActiveSheetIndex(0)->mergeCells($columnasExcel[$colspanParciales].$iniciaFilaAlumnos.":".$columnasExcel[$colspanParciales].$finFilaAlumnos);
            }
            //Para presentar la cabeceras de la columna de resultados - fin
            
            //Para generar las columnas dinamicas - fin
            
            //Definicion de Cabeceras - FIN
            
            //Para generar las celda con los datos de los estudiantes - INICIO
            $columnaActualCuerpo   = $primeraColumnaCabecera;
            $filaActualCuerpo      = $primeraFilaDatos+2;
            
            foreach($datosReturnArray["datosEstudiantes"] as $dataEstudiante) {
               //Presenta el nombre del estudiante - INICIO
               $phpExcelObject->setActiveSheetIndex(0)->setCellValue($columnasExcel[$columnaActualCuerpo].$filaActualCuerpo, $dataEstudiante["estudiante"]);
               $phpExcelObject->getActiveSheet()->getStyle($columnasExcel[$columnaActualCuerpo].$filaActualCuerpo)->applyFromArray($estiloCelda["izquierdoHorizontal"]);
               //Presenta el nombre del estudiante - FIN
               
               $columnaComponente   = $columnaActualCuerpo+1;
               
               //Presenta las notas del estudiante - INICIO
               foreach($dataEstudiante["parciales"] as $nombreParcial => $dataParcial) {
                  $cantComponentes     = count($dataParcial);
                  //Para presentar los nombres de los componentes - inicio
                  $phpExcelObject->getActiveSheet()
                     ->fromArray(
                         $dataParcial,   // Array, Los nombres de los componentes
                         NULL,                         // Array, si es que tengo los valores que van para cada componente
                         $columnasExcel[$columnaComponente].$filaActualCuerpo         // La coordenada (Ej. C3) donde quiero que comience a poner los valores
                     );
                  //Para presentar los nombres de los componentes - fin

                  $columnaComponente    = $columnaComponente+$cantComponentes;
               }
               //Presenta las notas del estudiante - FIN
               
               //Presenta el estado del semestre del estudiante - INICIO
               if($idParcial == 'todos'){
                  if($dataEstudiante["estadoCiclo"]=='A') {
                     $dataEstudiante["estadoCiclo"] = "APROBADO";
                  }
                  else if($dataEstudiante["estadoCiclo"]=='R') {
                     $dataEstudiante["estadoCiclo"] = "REPROBADO";
                  }
                  $phpExcelObject->setActiveSheetIndex(0)->setCellValue($columnasExcel[$columnaComponente].$filaActualCuerpo, $dataEstudiante["estadoCiclo"]);
               }
               //Presenta el estado del semestre del estudiante - FIN
               $filaActualCuerpo++;
            }
            //Para generar las celda con los datos de los estudiantes - FIN
            

            //PARA APLICAR LOS FORMATOS GENERALES A LA TABLA - INICIO
            if($idParcial == 'todos'){
               $celdaBordesFinalCab["letra"]    = $colspanParciales;
            }
            else{
               $celdaBordesFinalCab["letra"]    = $colspanParciales-1;
            }
            $celdaBordesFinalCab["numero"]   = $finFilaAlumnos;
            
            if($idParcial == 'todos'){
               $celdaBordesFinal["letra"]       = $colspanParciales;
            }
            else {
               $celdaBordesFinal["letra"]       = $colspanParciales-1;
            }
            $celdaBordesFinal["numero"]      = $filaActualCuerpo-1;

            $phpExcelObject->getActiveSheet()->getStyle($columnasExcel[$celdaBordesInicial["letra"]].$celdaBordesInicial["numero"].":".$columnasExcel[$celdaBordesFinal["letra"]].$celdaBordesFinal["numero"])->applyFromArray($estiloCelda["allborders"]);
            $phpExcelObject->getActiveSheet()->getStyle($columnasExcel[$celdaBordesInicial["letra"]].$celdaBordesInicial["numero"].":".$columnasExcel[$celdaBordesFinalCab["letra"]].$celdaBordesFinalCab["numero"])->applyFromArray($estiloCelda["textoNegrita"]);   //Este no cambia la celda de fin porque solo es para las cabeceras
            //PARA APLICAR LOS FORMATOS GENERALES A LA TABLA - INICIO
            
            
            // create the writer
            $writer = $this->get('phpexcel')->createWriter($phpExcelObject, 'Excel5');
            // create the response
            $response = $this->get('phpexcel')->createStreamedResponse($writer);
            // adding headers
            $dispositionHeader = $response->headers->makeDisposition(
                 ResponseHeaderBag::DISPOSITION_ATTACHMENT,
                 'Listado-Notas_'.date("Y-m-d").'.xls'
            );
            $response->headers->set('Content-Type', 'text/vnd.ms-excel; charset=utf-8');
            $response->headers->set('Pragma', 'public');
            $response->headers->set('Cache-Control', 'maxage=1');
            $response->headers->set('Content-Disposition', $dispositionHeader);

            return $response; 
            
         }
         catch(Exception $e) {
            echo 'Excepción capturada: ',  $e->getMessage(), "\n";
         }

      }
      
      public function exportarListadoAsistenciasAlumnosMateriaAction(Request $request, $idCarrera, $idParalelo, $idMateria, $fechaInicio, $fechaFin)
      {
         $session=$request->getSession();
         $idDocente  = $session->get('id_user');

         
//         $idDocente     = '31';
//         $idMateria     = '2271';
//         $idParalelo    = '0';
//         $idCarrera     = '4';
//         $fechaInicio   = '12/10/2015';
//         $fechaFin      = '18/10/2015';
         
         $anioConsulta  = date('o');
         
         

         try {
            $datosConsulta	= array( 
                                    'fechaInicio' => $fechaInicio,
                                    'fechaFin' => $fechaFin,
                                    'idDocente' => $idDocente,
                                    'idMateria' => $idMateria,
                                    'idParalelo' => $idParalelo,
                                    'anio' => $anioConsulta,
                                    'idCarrera' => $idCarrera
                                    );

            $UgServices       = new UgServices;
            /*Consulta de la información de las notas - INICIO*/
            $datosAsistenciasXML  = $UgServices->Docentes_getAsistenciasMaterias($datosConsulta);
            $datosAsistencia   = $this->procesarListadoAsistenciasEstudiantes($datosAsistenciasXML);
            /*Consulta de la información de las notas - FIN*/

            $dataEstudiantes  = $datosAsistencia["dataAsistencia"];
            $fechasCabecera   = $datosAsistencia["arregloFechas"];
            
            $dataFechas["fechaCompleta"]	= array();
            $dataFechas["nombreDia"]		= array();
            
            foreach($datosAsistencia["arregloFechas"] as $arregloFechas){
               array_push($dataFechas["fechaCompleta"], $arregloFechas["diaVal"]);
               array_push($dataFechas["nombreDia"], $arregloFechas["diaNom"]);
            }

            
            $phpExcelObject = $this->get('phpexcel')->createPHPExcelObject();

            $phpExcelObject->getProperties()->setCreator("UG")
                ->setLastModifiedBy("UG")
                ->setTitle("Listado de Asistencias")
                ->setSubject("Listado de Asistencias")
                ->setDescription("Listado de Asistencias.")
                ->setKeywords("UG listado asistencias")
                ->setCategory("academico");
            //Definicion de Estilos - INICIO
            $estiloCelda["centradoHorizVert"] = array(
                                                   'alignment' => array(
                                                      'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                                                      'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
                                                   )
                                                );
            
            $estiloCelda["izquierdoHorizontal"] = array(
                                                   'alignment' => array(
                                                      'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT
                                                   )
                                                );
            
            $estiloCelda["allborders"] =  array(
                                             'borders' => array(
                                               'allborders' => array(
                                                 'style' => PHPExcel_Style_Border::BORDER_THIN
                                               )
                                             )
                                          );
            
            $estiloCelda["textoNegrita"] =   array(
                                                "font" => array( "bold" => true)
                                             );
            //Definicion de Estilos - FIN
            //
            //UN ARRAY PARA HACER UNA RELACION DIRECTA ENTRE LAS LETRAS
            //DE LAS COLUMNAS DE EXCEL Y EL ARREGLO DE DATOS QUE VOY A MANDAR
            $columnasExcel       = array('','A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z');
            $primeraFilaDatos    = 2;
            $primeraColumnaDatos = 2;  //La letra 'B'
            
            $celdaBordesInicial["letra"]  = $primeraColumnaDatos;
            $celdaBordesInicial["numero"] = $primeraFilaDatos;
            $celdaBordesFinal["letra"]    = NULL;
            $celdaBordesFinal["numero"]   = NULL;
            
            
            $phpExcelObject->setActiveSheetIndex(0);
            
            $phpExcelObject->getDefaultStyle()->applyFromArray($estiloCelda["centradoHorizVert"]);   //Centrar el texto
            
            //Definicion de Cabeceras - INICIO
            $primeraColumnaCabecera = $primeraColumnaDatos;
            $phpExcelObject->getActiveSheet()->getColumnDimension($columnasExcel[$primeraColumnaCabecera])->setWidth(50);
            $phpExcelObject->setActiveSheetIndex(0)->setCellValue($columnasExcel[$primeraColumnaCabecera].$primeraFilaDatos, 'ALUMNOS');
            $iniciaFilaAlumnos = $primeraFilaDatos;
            $finFilaAlumnos    = $primeraFilaDatos+1;
            $phpExcelObject->setActiveSheetIndex(0)->mergeCells($columnasExcel[$primeraColumnaCabecera].$iniciaFilaAlumnos.":".$columnasExcel[$primeraColumnaCabecera].$finFilaAlumnos);
            
            //Para generar las columnas dinamicas - inicio
            $columnasAsistencia = $primeraColumnaCabecera+1;
            
            foreach(range($columnasExcel[$columnasAsistencia],'Z') as $columnID) { //Define el ancho de todas las columnas que vienen
               $phpExcelObject->getActiveSheet()->getColumnDimension($columnID)->setWidth(15);              //Ancho de las columnas
               $phpExcelObject->getActiveSheet()->getStyle($columnID)->getAlignment()->setWrapText(true);   //Ajuste de Texto
            }
            
            $phpExcelObject->getActiveSheet()
               ->fromArray(
                  $dataFechas["nombreDia"],   // Array, Los nombres de los componentes
                  NULL,                         // Array, si es que tengo los valores que van para cada componente
                  $columnasExcel[$columnasAsistencia].$finFilaAlumnos         // La coordenada (Ej. C3) donde quiero que comience a poner los valores
               );
            
            $phpExcelObject->getActiveSheet()
               ->fromArray(
                  $dataFechas["fechaCompleta"],   // Array, Los nombres de los componentes
                  NULL,                         // Array, si es que tengo los valores que van para cada componente
                  $columnasExcel[$columnasAsistencia].($finFilaAlumnos-1)         // La coordenada (Ej. C3) donde quiero que comience a poner los valores
               );
            
            //Para generar las columnas dinamicas - fin
         //Definicion de Cabeceras - FIN

         //Para generar las celda con los datos de los estudiantes - INICIO
            $columnaActualCuerpo   = $primeraColumnaCabecera;
            $filaActualCuerpo      = $primeraFilaDatos+2;
            
            
            foreach($dataEstudiantes as $dataEstudiante) {
               //Presenta el nombre del estudiante - INICIO
               $nombreEstudiante = $dataEstudiante["apellidos"] . " " . $dataEstudiante["nombres"];
               
               $phpExcelObject->setActiveSheetIndex(0)->setCellValue($columnasExcel[$columnaActualCuerpo].$filaActualCuerpo, $nombreEstudiante );
               $phpExcelObject->getActiveSheet()->getStyle($columnasExcel[$columnaActualCuerpo].$filaActualCuerpo)->applyFromArray($estiloCelda["izquierdoHorizontal"]);
               //Presenta el nombre del estudiante - FIN

               //Presenta las asistencias del estudiante - INICIO
               $columnaAsistencias   = $columnaActualCuerpo+1;
               $countDias  = count($dataEstudiante["fechas"]);
               
               //Convertir los textos para el XLS - INICIO
               for($iFecha=0; $iFecha<count($dataEstudiante["fechas"]); $iFecha++) {
                  $fechaEstudiante = $dataEstudiante["fechas"][$iFecha];
                  if($fechaEstudiante=="F") {
                     $fechaEstudiante = "No";
                  }elseif($fechaEstudiante=="V") {
                     $fechaEstudiante = "Si";
                  }/*elseif($fechaEstudiante=="S/R") {
                     $fechaEstudiante = "Si";
                  }*/
                  $dataEstudiante["fechas"][$iFecha] = $fechaEstudiante;
               }
               unset($fechaEstudiante);
               //Convertir los textos para el XLS - FIN
               
               $phpExcelObject->getActiveSheet()
                  ->fromArray(
                     $dataEstudiante["fechas"],    // Array, con los resultados de las asistencias
                     NULL,                         // Array, si es que tengo los valores que van para cada asistencia
                     $columnasExcel[$columnaAsistencias].$filaActualCuerpo         // La coordenada (Ej. C3) donde quiero que comience a poner los valores
               );
               //Presenta las asistencias del estudiante - FIN

               $filaActualCuerpo++;
            }
         //Para generar las celda con los datos de los estudiantes - FIN

            //PARA APLICAR LOS FORMATOS GENERALES A LA TABLA - INICIO
            
            $celdaBordesFinalCab["letra"]    = $columnaAsistencias+$countDias;
            $celdaBordesFinalCab["numero"]   = $finFilaAlumnos;

            $celdaBordesFinal["letra"]       = $columnaAsistencias+$countDias-1;
            $celdaBordesFinal["numero"]      = $filaActualCuerpo-1;

            $phpExcelObject->getActiveSheet()->getStyle($columnasExcel[$celdaBordesInicial["letra"]].$celdaBordesInicial["numero"].":".$columnasExcel[$celdaBordesFinal["letra"]].$celdaBordesFinal["numero"])->applyFromArray($estiloCelda["allborders"]);
            $phpExcelObject->getActiveSheet()->getStyle($columnasExcel[$celdaBordesInicial["letra"]].$celdaBordesInicial["numero"].":".$columnasExcel[$celdaBordesFinalCab["letra"]].$celdaBordesFinalCab["numero"])->applyFromArray($estiloCelda["textoNegrita"]);   //Este no cambia la celda de fin porque solo es para las cabeceras
            //PARA APLICAR LOS FORMATOS GENERALES A LA TABLA - INICIO
            
            
            // create the writer
            $writer = $this->get('phpexcel')->createWriter($phpExcelObject, 'Excel5');
            // create the response
            $response = $this->get('phpexcel')->createStreamedResponse($writer);
            // adding headers
            $dispositionHeader = $response->headers->makeDisposition(
                 ResponseHeaderBag::DISPOSITION_ATTACHMENT,
                 'Listado-Asistencias_'.date("Y-m-d").'.xls'
            );
            $response->headers->set('Content-Type', 'text/vnd.ms-excel; charset=utf-8');
            $response->headers->set('Pragma', 'public');
            $response->headers->set('Cache-Control', 'maxage=1');
            $response->headers->set('Content-Disposition', $dispositionHeader);

            return $response; 
            
         }
         catch(Exception $e) {
            echo 'Excepción capturada: ',  $e->getMessage(), "\n";
         }
      }      // getMateriasListado()


        public function mostraralumnosAction(Request $request)
        { 
          
            $notas='';
            $id_Materia =$request->request->get('materia');
            $desc_ciclo = $request->request->get('desc-ciclo');
            $ciclo = $request->request->get('ciclo');
            //echo $id_Materia."tt";
            //exit();
            $session=$request->getSession();
             //$parcial ='1';
            
            $response   		= new JsonResponse();
            $withoutModal       = true;
         
            $idDocente     = 1;
            $carrera  =1;
            $UgServices    = new UgServices;
            //$idDocente="";
               $idCarrera="";
             //$materia="2269";
           
               
               
               	$trama = "<materiaparalelo>".$id_Materia."</materiaparalelo>";
                
            $arr_datos  = $UgServices->Docentes_getAlumnos($trama);
            
           
          // print_r($arr_datos);
          // exit();
            
           // echo $arr_datos[0]['nombres'];
           /*$ar=$arr_datos->soapBody->ns2ejecucionConsultaResponse->return;
           echo $ar->idHistorico;*/
           // exit();
          // echo $arr_datos->estado;
           $docente= $session->get('id_user');
            $ciclo='18';
            $docente='5';
            $id_Materia='251';
            
            $trama ="<PI_ID_CICLO_DETALLE>".$ciclo."</PI_ID_CICLO_DETALLE>
                         <PI_ID_USUARIO_PROFESOR>".$docente."</PI_ID_USUARIO_PROFESOR>
                         <PI_ID_MATERIA>".$id_Materia."</PI_ID_MATERIA>
                         <PARCIAL>1</PARCIAL>
                         <PI_ESTUDIANTE>16</PI_ESTUDIANTE>";
            $id_Materia =$request->request->get('materia');
            
            
           $datosParciales  = $UgServices->Docentes_gettareaxparcial($trama);
            
          /* print_r($datosParciales);
           exit();*/
            $profesor=$datosParciales->registro[0]->profesor;
            $materia=$datosParciales->registro[0]->materia;
            $paralelo=$datosParciales->registro[0]->paralelo;
            $parcial=$datosParciales->registro[0]->periodos->periodo[0]->parcial;
            $profesor = $session->get('nom_usuario'); 
            $materia= $session->get('nom_materia');
            $paralelo= $session->get('paralelo');
            $parcial=str_replace('PARCIAL1','1',$parcial);
        
			$this->v_html = $this->renderView('TitulacionSisAcademicoBundle:Docentes:AlumnosIngresoNota.html.twig',
						  array(
							   'arr_datos'	=> $arr_datos,
                                                           'cantidad'   => '',
                                                           'profesor'   => $profesor,
                                                           'ciclo_des'   => $desc_ciclo,
                                                           'ciclo'   => $ciclo,
                                                           'materia'    => $materia,
                                                           'id_materia'    => $id_Materia,
                                                           'paralelo'   => $paralelo, 
                                                           'parcial'	=> $parcial,
                                                           'msg'   	=> $this->v_msg
						  ));
                        $this->v_html=utf8_encode($this->v_html);
                        
                        $response->setData(
                                array(
					'error' 		=> $this->v_error,
					'msg'			=> $this->v_msg,
                                        'html' 			=> utf8_decode($this->v_html),
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


   
            public function mostraralumnos2Action(Request $request)
        { 
          
            $notas='';
            $parcial =$request->request->get('alumno');
            $materia =$request->request->get('idMateria');
            //echo $materia."tt";
            //exit();
            $session=$request->getSession();
               $session->set("parcial",$parcial);
             $parcial ='2';
            
            $response   		= new JsonResponse();
            $withoutModal       = true;
         
            $idDocente     = 1;
            $carrera  =1;
            $UgServices    = new UgServices;
            //$idDocente="";
               $idCarrera="";
             $materia="2269";
           
               
               
               	$trama = "<materiaparalelo>".$materia."</materiaparalelo>";
                
            $arr_datos  = $UgServices->Docentes_getAlumnos($trama);
            
           
          // print_r($arr_datos);
          // exit();
            
           // echo $arr_datos[0]['nombres'];
           /*$ar=$arr_datos->soapBody->ns2ejecucionConsultaResponse->return;
           echo $ar->idHistorico;*/
           // exit();
          // echo $arr_datos->estado;
            $trama ="<PI_ID_CICLO_DETALLE>18</PI_ID_CICLO_DETALLE>
                         <PI_ID_USUARIO_PROFESOR>5</PI_ID_USUARIO_PROFESOR>
                         <PI_ID_MATERIA>251</PI_ID_MATERIA>
                         <PARCIAL>1</PARCIAL>
                         <PI_ESTUDIANTE>16</PI_ESTUDIANTE>";
            
           $datosParciales  = $UgServices->Docentes_gettareaxparcial($trama);
            
           /*print_r($datosParciales);
           exit();*/
            $profesor=$datosParciales->registro[0]->profesor;
            $materia=$datosParciales->registro[0]->materia;
            $paralelo=$datosParciales->registro[0]->paralelo;
            $profesor = $session->get('nom_usuario'); 
            $materia= $session->get('nom_materia');
            $paralelo= $session->get('paralelo');

        
			$this->v_html = $this->renderView('TitulacionSisAcademicoBundle:Docentes:AlumnosIngresoNota.html.twig',
						  array(
							   'arr_datos'	=> $arr_datos,
                                                           'cantidad'   => '',
                                                           'profesor'   => $profesor,
                                                           'materia'    => $materia,
                                                           'paralelo'   => $paralelo, 
                                                           'parcial'	=> $parcial,
                                                           'msg'   	=> $this->v_msg
						  ));
                        $this->v_html=utf8_encode($this->v_html);
                        
                        $response->setData(
                                array(
					'error' 		=> $this->v_error,
					'msg'			=> $this->v_msg,
                                        'html' 			=> utf8_decode($this->v_html),
                                        'withoutModal' 	=> $withoutModal,
                                        'recargar'      => '0'
                                     )
                              );
                        return $response;
        }
        
          
      public function ingresarNotaAction(Request $request)
        { 
             $response   		= new JsonResponse();
              $session=$request->getSession();
               $idCarrera=$session->get('idCarrera');
              // $idDocente=$session->get('idDocente')
               $parcial=$session->get('hdparcial');
              // $alumno=$session->get('codalumno');
               
               $idDocente     = $session->get('id_user');
          
                $alumno =$request->request->get('hdalumno');
                $parcial =$request->request->get('hdparcial');
            //echo $parcial."ppt";
            //exit();
                    
                 $total =$request->request->get('hdcount');
                 
       
                  $UgServices    = new UgServices;
                  
                    $doc = new \DOMDocument('1.0');
                    
                  $doc->formatOutput = true;
                  $xml = $doc->createElement('px_xml');
                  $xml = $doc->appendChild($xml);
                  $notas = $doc->createElement('Notas');
                  $notas = $xml->appendChild($notas);
                 for($i=1; $i<=$total; $i++)
            {     
                  $root = $doc->createElement('Nota');
                  $root = $notas->appendChild($root);
                  $carrera = $doc->createElement('id_estudiante_carrera_materia');
                  $carrera = $root->appendChild($carrera);
                  $text = $doc->createTextNode($alumno);
                  $text = $carrera->appendChild($text);
                  
                  /*$materia = $doc->createElement('idMateria');
                  $materia = $root->appendChild($materia);
                  $text = $doc->createTextNode($idMateria);
                  $text = $materia->appendChild($text);*/
                  $TipoNota = $doc->createElement('idTipoNota');
                  $TipoNota = $root->appendChild($TipoNota);
                  $text = $doc->createTextNode($request->request->get('hdtarea_'.$i));
                  $text = $TipoNota->appendChild($text);
                  $parciales = $doc->createElement('parcial');
                  $parciales = $root->appendChild($parciales);
                  $text = $doc->createTextNode($parcial);
                  $text = $parciales->appendChild($text);
                  $calificacion = $doc->createElement('calificacion');
                  $calificacion = $root->appendChild($calificacion);
                  $text = $doc->createTextNode($request->request->get('academicos_'.$i));
                  $text = $calificacion->appendChild($text);
                  $docente = $doc->createElement('id_sg_usuario');
                  $docente = $root->appendChild($docente);
                  $text = $doc->createTextNode($idDocente);
                  $text = $docente->appendChild($text);
            }
                  $opcion = $doc->createElement('pc_opcion');
                  $opcion = $doc->appendChild($opcion);
                  $text = $doc->createTextNode('A');
                  $text = $opcion->appendChild($text);
                  
                  $xmlfinal= $doc->saveXML() . "\n";
                 
                 $xmlfinal= str_replace ( '<?xml version="1.0"?>' , '' , $xmlfinal);
//                 print_r($xmlfinal);
//                   exit();
                  $respuesta  = $UgServices->Docentes_ingresoNotas($xmlfinal);
                //print ($notas);
               //   print_r($respuesta);
               //    exit();
            
                 $ar=$respuesta->soapBody->ns2ejecucionObjetoResponse->return;
                 
                 $result=$ar->resultadoObjeto->parametrosSalida->PV_MENSAJE;
                // echo $result;
                // exit();
                 
                      //print $result;
           $mensaje =(string)$result;
        
            $this->v_error	= true;

            $response->setData(
                                array(
                                        'error' => true,
                                        'msg' => $mensaje
                                     )
                              );
            
            return $response;
        }
        
     public function ingresonotasAction(Request $request)
        { 
             $response   		= new JsonResponse();
             
              $UgServices    = new UgServices;
               $idDocente="";
               $idCarrera="";
               $session=$request->getSession();
               
              $id_Materia =$request->request->get('materia');
              $parcial =$request->request->get('ciclo');
               //print_r($datosParciales);
               $docente= $session->get('id_user');
                $ciclo='18';
                $docente='5';
                $id_Materia='251';

                $trama ="<PI_ID_CICLO_DETALLE>".$ciclo."</PI_ID_CICLO_DETALLE>
                             <PI_ID_USUARIO_PROFESOR>".$docente."</PI_ID_USUARIO_PROFESOR>
                             <PI_ID_MATERIA>".$id_Materia."</PI_ID_MATERIA>
                             <PARCIAL>1</PARCIAL>
                             <PI_ESTUDIANTE>16</PI_ESTUDIANTE>";
               $datosParciales  = $UgServices->Docentes_gettareaxparcial($trama);
            
               $tareas1= $datosParciales->registro[0]->periodos->periodo[0]->componentePeriodo;
               $i=0;
               foreach ($tareas1->idNota as $idnota) {
               $tareas[$i]['idNota']= (string)$idnota;
               $i++;
               }
               $i=0;
               foreach ($tareas1->componente as $componente) {
               $tareas[$i]['componente']= (string)$componente;
               $i++;
               }
//               print_r($registros);
//               exit();
          //echo $i;
               // print_r($datosParciales->registro[0]->periodos[0]->periodo[0]->componentePeriodo->componente);
           // print_r($tareas);
                $alumno =$request->request->get('alumno');
                $idalumno =$request->request->get('idalumno');
                
                $codigo =$request->request->get('codigo');
                $parcial =$request->request->get('parcial');
                $session=$request->getSession();
                $session->set("codalumno",$codigo);
            /*
                    $tareas =  array(
                              array( 'tarealm' => 'leccion1'),
                              array( 'tarealm' => 'leccion2'),
                              array( 'tarealm' => 'taller1'),
                              array( 'tarealm' => 'taller2'),
                           );*/
           
			$this->v_html = $this->renderView('TitulacionSisAcademicoBundle:Docentes:ingresoNotas.html.twig',
						  array(
							   'codigo'	=> $codigo,
                                                           'alumno'	=> $alumno,
                                                           'idalumno'	=> $idalumno,
                                                           'arr_tareas'   => $tareas,
                                                           'parcial'   =>$parcial,
                                                           'msg'   	=> $this->v_msg
						  ));
                    
                        
                       /* $response->setData(
                                array('html' 			=> $this->v_html,
                                    'error'=>true,
                                    'msg'=>'por fin salio'
                                     )
                              );
                        return $response;*/
                           
           
            $title 		= 'Ingreso de Nota';
            $typeModalOverBody 	= 'advertises';
            $sizeModalOverBody 	= 'lg';
            $modalOverBody	= true;
                  $this->v_html=utf8_encode($this->v_html);
            $response->setData(
                                array(
                                        'anotherDivError' => $this->v_error,
                                        'msg' => trim($this->v_message),
                                        'modalOverBody' => $modalOverBody,
                                        'html' => utf8_decode($this->v_html),
                                        'title' => $title,
                                        'typeModalOverBody' => $typeModalOverBody,
                                        'sizeModalOverBody' => $sizeModalOverBody
                                     )
                              );
            
            return $response;
        }
        public function actualizaAsisAction(Request $request)
        { 
           $notas='';
            date_default_timezone_set('America/Buenos_Aires');
         $response   		= new JsonResponse();
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
            //$idDocente="";
               $idCarrera="";
            $id_Materia =$request->request->get('materia');
            $desc_ciclo = $request->request->get('desc-ciclo');
            $ciclo = $request->request->get('ciclo');
            // $materia="2269";
       //Menu de Notas por Materia para Profesor
         $Parcial='1';
               
                    $trama = "<materia>".$id_Materia."</materia>";
                
          $arr_fechas  = $UgServices->Docentes_getfechasparcial($trama);
//          print_r($arr_fechas);
//          exit();
           $muestrafecha="";
           $fecha_act=date('Y-m-d');
 
           foreach($arr_fechas as $fecha) {
              
                  $muestrafecha .= '<option value="'.$fecha['fecha'].'">'.$fecha['fecha'].'</option>';
               
            }
           
               $Parcial='1';
               
               	$trama = "<materiaparalelo>".$id_Materia."</materiaparalelo>";
                
            $arr_datos  = $UgServices->Docentes_getAlumnos($trama);
            $docente= $session->get('id_user');
            $ciclo='18';
            $docente='5';
            $id_Materia='251';
            
            $trama ="<PI_ID_CICLO_DETALLE>".$ciclo."</PI_ID_CICLO_DETALLE>
                         <PI_ID_USUARIO_PROFESOR>".$docente."</PI_ID_USUARIO_PROFESOR>
                         <PI_ID_MATERIA>".$id_Materia."</PI_ID_MATERIA>
                         <PARCIAL>1</PARCIAL>
                         <PI_ESTUDIANTE>16</PI_ESTUDIANTE>";
       $id_Materia =$request->request->get('materia');
           
           $datosParciales  = $UgServices->Docentes_gettareaxparcial($trama);
           /*print_r($datosParciales);
           exit();*/
            $profesor=$datosParciales->registro[0]->profesor;
            $materia=$datosParciales->registro[0]->materia;
            $paralelo=$datosParciales->registro[0]->paralelo;
            $profesor = $session->get('nom_usuario'); 
            $materia= $session->get('nom_materia');
            $paralelo= $session->get('paralelo');

        
			$this->v_html = $this->renderView('TitulacionSisAcademicoBundle:Docentes:AlumnosActualizaAsistencia.html.twig',
						  array(
							   'arr_datos'	=> $arr_datos,
                                                           'fecha'   => $muestrafecha ,
                                                           'profesor'   => $profesor,
                                                           'materia'    => $materia,
                                                           'id_materia'    => $id_Materia,
                                                           'ciclo'    => $desc_ciclo,
                                                           'paralelo'   => $paralelo,
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

        
          public function tabDocAsistenciasAction(Request $request)
      {
            date_default_timezone_set('America/Buenos_Aires');
         $idMateria     = $request->request->get('idMateria');
         $idCarrera     = $request->request->get('idCarrera');
         $inicioCiclo   = $request->request->get('inicioCiclo');
         $finCiclo      = $request->request->get('finCiclo');
         
         $UgServices    = new UgServices;
       //Menu de Notas por Materia para Profesor
         $Parcial='1';
               
               $trama = "<materia>".$idMateria."</materia>";
                
          $arr_fechas  = $UgServices->Docentes_getfechasparcial($trama);
//          print_r($arr_fechas);
//          exit();
           $muestrafecha="";
           $fecha_act=date('Y-m-d');
           foreach($arr_fechas as $fecha) {
               if ($fecha_act==$fecha['fecha'] && $fecha['ingreso'] == '1' ){
                   $muestrafecha=$fecha_act;
               }
            }
           // echo $fecha_act;
           // echo $arr_fechas[0]['fecha']."--".$fecha_act;
            // print_r($arr_fechas);
         //  exit();
       return $this->render('TitulacionSisAcademicoBundle:Docentes:tabsDocAsistencias.html.twig',
                         array(
                               'condition' => '',
                               'idCarrera' => $idCarrera,
                               'idMateria' => $idMateria,
                               'inicioCiclo' => $inicioCiclo,
                               'finCiclo' => $finCiclo,
                               'fecha'=> $muestrafecha,
                             )
                      );
      }
      
          
        public function IngresoAsistenciaAction(Request $request)
        { 
           $notas='';
            date_default_timezone_set('America/Buenos_Aires');
         $response   		= new JsonResponse();
         $withoutModal       = true;
                     $profesor='Apolinario';
            //$materia='Calculo';
            $paralelo='S2A';
          
            $notas='';
            $id_Materia =$request->request->get('materia');
            $desc_ciclo = $request->request->get('desc-ciclo');
            $ciclo = $request->request->get('ciclo');
            
            $parcial =$request->request->get('parcial');
            $session=$request->getSession();
               $session->set("parcial",$parcial);
            
            $response   		= new JsonResponse();
            $withoutModal       = true;
            
            $fecha=date('d/m/Y');
            $idDocente     = 1;
            $carrera  =1;
            $UgServices    = new UgServices;
            //$idDocente="";
               $idCarrera="";
            // $materia="2269";
           
               $Parcial='1';
               
               	$trama = "<materiaparalelo>".$id_Materia."</materiaparalelo>";
                
            $arr_datos  = $UgServices->Docentes_getAlumnos($trama);
            $docente= $session->get('id_user');
            $ciclo='18';
            $docente='5';
            $id_Materia='251';
            
            $trama ="<PI_ID_CICLO_DETALLE>".$ciclo."</PI_ID_CICLO_DETALLE>
                         <PI_ID_USUARIO_PROFESOR>".$docente."</PI_ID_USUARIO_PROFESOR>
                         <PI_ID_MATERIA>".$id_Materia."</PI_ID_MATERIA>
                         <PARCIAL>1</PARCIAL>
                         <PI_ESTUDIANTE>16</PI_ESTUDIANTE>";
          $id_Materia =$request->request->get('materia');
           
           $datosParciales  = $UgServices->Docentes_gettareaxparcial($trama);
           
            
           /*print_r($datosParciales);
           exit();*/
            $profesor=$datosParciales->registro[0]->profesor;
            $materia=$datosParciales->registro[0]->materia;
            $paralelo=$datosParciales->registro[0]->paralelo;
            $profesor = $session->get('nom_usuario'); 
            $materia= $session->get('nom_materia');
            $paralelo= $session->get('paralelo');

        
			$this->v_html = $this->renderView('TitulacionSisAcademicoBundle:Docentes:AlumnosIngresoAsistencia.html.twig',
						  array(
							   'arr_datos'	=> $arr_datos,
                                                           'fecha'   => $fecha,
                                                           'profesor'   => $profesor,
                                                           'ciclo'   => $desc_ciclo,
                                                           'materia'    => $materia,
                                                           'paralelo'   => $paralelo,
                                                           'id_materia'    => $id_Materia,
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
        
           
        public function ingresarAsistenciaAction(Request $request)
        { 
           $notas='';
           date_default_timezone_set('America/Buenos_Aires');
            $session=$request->getSession();
           $idDocente     = $session->get('id_user');
           $id_materia  = $request->request->get('materia');
           //$id_materia  ='2271';
            
           $arr_checked  = $request->request->get('arr_checked');
           $arr_unchecked  = $request->request->get('arr_unchecked');
           $alumnosa=json_decode($arr_checked);
           $alumnosi=json_decode($arr_unchecked);
           $UgServices    = new UgServices;
           //$idDocente='31';
           $estudiante='2';
           //$materia_paralelo='235';
           $fecha='01/06/2015';
           //$fecha=date('d/m/Y');
           $fecha=date('Y-m-d');
           $id_docente='2'; 
           
               $doc = new \DOMDocument('1.0');
                    
                  $doc->formatOutput = true;
                  $xml = $doc->createElement('PX_XML_CAB');
                  $xml = $doc->appendChild($xml);
                  $items = $doc->createElement('items');
                  $items = $xml->appendChild($items);
                  $item = $doc->createElement('item');
                  $item = $items->appendChild($item);
                  $id_profesor = $doc->createElement('id_profesor');
                  $id_profesor = $item->appendChild($id_profesor);
                  $text = $doc->createTextNode($idDocente);
                  $text = $id_profesor->appendChild($text);
                  $id_estudiante = $doc->createElement('fecha_asistencia');
                  $id_estudiante = $item->appendChild($id_estudiante);
                  $text = $doc->createTextNode($fecha);
                  $text = $id_estudiante->appendChild($text);
                  $id_materia_paralelo = $doc->createElement('id_materia_paralelo');
                  $id_materia_paralelo = $item->appendChild($id_materia_paralelo);
                  $text = $doc->createTextNode($id_materia);
                  $text = $id_materia_paralelo->appendChild($text);
                  $id_usuario = $doc->createElement('id_usuario');
                  $id_usuario = $item->appendChild($id_usuario);
                  $text = $doc->createTextNode($idDocente);
                  $text = $id_usuario->appendChild($text);
                  $xmldet = $doc->createElement('PX_XML_DET');
                  $xmldet = $doc->appendChild($xmldet);
                  $items = $doc->createElement('items');
                  $items = $xmldet->appendChild($items);
                  
           
             foreach($alumnosa as $alumno) {
                 // echo $alumno."-";
                  $item = $doc->createElement('item');
                  $item = $items->appendChild($item);
                  $estado_asistencia = $doc->createElement('estado_asistencia');
                  $estado_asistencia = $item->appendChild($estado_asistencia);
                  $text = $doc->createTextNode('1');
                  $text = $estado_asistencia->appendChild($text);
                  $id_estudiante = $doc->createElement('id_estudiante');
                  $id_estudiante = $item->appendChild($id_estudiante);
                  $text = $doc->createTextNode($alumno);
                  $text = $id_estudiante->appendChild($text);
            }
            foreach($alumnosi as $alumno) {
                 // echo $alumno."-";
                  $item = $doc->createElement('item');
                  $item = $items->appendChild($item);
                  $estado_asistencia = $doc->createElement('estado_asistencia');
                  $estado_asistencia = $item->appendChild($estado_asistencia);
                  $text = $doc->createTextNode('0');
                  $text = $estado_asistencia->appendChild($text);
                  $id_estudiante = $doc->createElement('id_estudiante');
                  $id_estudiante = $item->appendChild($id_estudiante);
                  $text = $doc->createTextNode($alumno);
                  $text = $id_estudiante->appendChild($text);
            }
                  $opcion = $doc->createElement('PC_OPCION');
                  $opcion = $doc->appendChild($opcion);
                  $text = $doc->createTextNode('I');
                  $text = $opcion->appendChild($text);
                  
                  $xmlfinal= $doc->saveXML() . "\n";
                 
                 $xmlfinal= str_replace ( '<?xml version="1.0"?>' , '' , $xmlfinal);
//                echo $xmlfinal;
//                exit();
         $response   		= new JsonResponse();
          $respuesta  = $UgServices->Docentes_ingresoAsistencia($xmlfinal);
              
//                 print_r($respuesta);
//                   exit();
            
                 $ar=$respuesta->soapBody->ns2ejecucionObjetoResponse->return;
                 
                 $result=$ar->resultadoObjeto->parametrosSalida->PV_MENSAJE;
                // echo $result;
                // exit();
                 
                      //print $result;
           $mensaje =(string)$result;
        
            $this->v_error	= true;

            $response->setData(
                                array(
                                        'error' => true,
                                        'msg' => $mensaje
                                     )
                              );
            
            return $response;
        }
        
             public function actualizarAsistenciaAction(Request $request)
        { 
           $notas='';
           date_default_timezone_set('America/Buenos_Aires');
            $session=$request->getSession();
           $idDocente     = $session->get('id_user');
           $id_materia  = $request->request->get('materia');
           $fecha=$session->get('combofecha');
           //$id_materia  ='235';
            
           $arr_checked  = $request->request->get('arr_checked');
           $arr_unchecked  = $request->request->get('arr_unchecked');
           
           $alumnosa=json_decode($arr_checked);
           $alumnosi=json_decode($arr_unchecked);
           $UgServices    = new UgServices;
           $idDocente='31';
           $estudiante='2';
           //$materia_paralelo='235';
          // $fecha='01/06/2015';
           //$fecha=date('d/m/Y');
           $fecha=date('Y-m-d');
           $id_docente='2'; 
           
               $doc = new \DOMDocument('1.0');
                    
                  $doc->formatOutput = true;
                  $xml = $doc->createElement('PX_XML_CAB');
                  $xml = $doc->appendChild($xml);
                  $items = $doc->createElement('items');
                  $items = $xml->appendChild($items);
                  $item = $doc->createElement('item');
                  $item = $items->appendChild($item);
                  $id_profesor = $doc->createElement('id_profesor');
                  $id_profesor = $item->appendChild($id_profesor);
                  $text = $doc->createTextNode($idDocente);
                  $text = $id_profesor->appendChild($text);
                  $id_estudiante = $doc->createElement('fecha_asistencia');
                  $id_estudiante = $item->appendChild($id_estudiante);
                  $text = $doc->createTextNode($fecha);
                  $text = $id_estudiante->appendChild($text);
                  $id_materia_paralelo = $doc->createElement('id_materia_paralelo');
                  $id_materia_paralelo = $item->appendChild($id_materia_paralelo);
                  $text = $doc->createTextNode($id_materia);
                  $text = $id_materia_paralelo->appendChild($text);
                  $id_usuario = $doc->createElement('id_usuario');
                  $id_usuario = $item->appendChild($id_usuario);
                  $text = $doc->createTextNode($idDocente);
                  $text = $id_usuario->appendChild($text);
                  $xmldet = $doc->createElement('PX_XML_DET');
                  $xmldet = $doc->appendChild($xmldet);
                  $items = $doc->createElement('items');
                  $items = $xmldet->appendChild($items);
                  
         
             foreach($alumnosa as $alumno) {
                 // echo $alumno."-";
                  $item = $doc->createElement('item');
                  $item = $items->appendChild($item);
                  $estado_asistencia = $doc->createElement('estado_asistencia');
                  $estado_asistencia = $item->appendChild($estado_asistencia);
                  $text = $doc->createTextNode('1');
                  $text = $estado_asistencia->appendChild($text);
                  $id_estudiante = $doc->createElement('id_estudiante');
                  $id_estudiante = $item->appendChild($id_estudiante);
                  $text = $doc->createTextNode($alumno);
                  $text = $id_estudiante->appendChild($text);
            }
            foreach($alumnosi as $alumno) {
                 // echo $alumno."-";
                  $item = $doc->createElement('item');
                  $item = $items->appendChild($item);
                  $estado_asistencia = $doc->createElement('estado_asistencia');
                  $estado_asistencia = $item->appendChild($estado_asistencia);
                  $text = $doc->createTextNode('0');
                  $text = $estado_asistencia->appendChild($text);
                  $id_estudiante = $doc->createElement('id_estudiante');
                  $id_estudiante = $item->appendChild($id_estudiante);
                  $text = $doc->createTextNode($alumno);
                  $text = $id_estudiante->appendChild($text);
            }
                  $opcion = $doc->createElement('PC_OPCION');
                  $opcion = $doc->appendChild($opcion);
                  $text = $doc->createTextNode('A');
                  $text = $opcion->appendChild($text);
                  
                  $xmlfinal= $doc->saveXML() . "\n";
                 
                 $xmlfinal= str_replace ( '<?xml version="1.0"?>' , '' , $xmlfinal);
//                 echo $xmlfinal;
//                 exit();
         $response   		= new JsonResponse();
          $respuesta  = $UgServices->Docentes_ingresoAsistencia($xmlfinal);
              
//                 print_r($respuesta);
//                   exit();
            
                 $ar=$respuesta->soapBody->ns2ejecucionObjetoResponse->return;
                 
                 $result=$ar->resultadoObjeto->parametrosSalida->PV_MENSAJE;
                // echo $result;
                // exit();
                 
                      //print $result;
           $mensaje =(string)$result;
        
            $this->v_error	= true;

            $response->setData(
                                array(
                                        'error' => true,
                                        'msg' => $mensaje
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
      
      function procesarListadoParcialesCarrera ($datosParcialesArray) {
         return $datosParcialesArray;
      }
      
      function procesarListadoNotasEstudiantes ($datosNotasArray) {
         try {
            $dataProcesar = $datosNotasArray["registro"];

            $datosGeneralesListado["notaMinima"]         = $dataProcesar["notaMinima"];
            $datosGeneralesListado["idProfesor"]         = $dataProcesar["idProfesor"];
            $datosGeneralesListado["profesor"]           = $dataProcesar["profesor"];
            $datosGeneralesListado["idMateria"]          = $dataProcesar["idMateria"];
            $datosGeneralesListado["idmateriaParalelo"]	= $dataProcesar["idmateriaParalelo"];
            $datosGeneralesListado["materia"]            = $dataProcesar["materia"];
            $datosGeneralesListado["idParalelo"]         = $dataProcesar["idParalelo"];
            $datosGeneralesListado["paralelo"]           = $dataProcesar["paralelo"];

            if(isset($dataProcesar["periodos"]["periodo"]["parcial"])) {  //Cuando llega un solo parcial
               $tempPeriodo = $dataProcesar["periodos"]["periodo"];
               $dataProcesar["periodos"]["periodo"]      = NULL;
               $dataProcesar["periodos"]["periodo"][0]   = $tempPeriodo;
               unset($tempPeriodo);
            }
            
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

            $datosEstudiantes	= array();
            if(isset($dataProcesar["estudiantes"]["estudiante"]["idEstudiante"])) {  //Cuando llega un solo estudiante
               $tempEstudiante = $dataProcesar["estudiantes"]["estudiante"];
               $dataProcesar["estudiantes"]["estudiante"]      = NULL;
               $dataProcesar["estudiantes"]["estudiante"][0]   = $tempEstudiante;
               unset($tempEstudiante);
            }
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
                      if(isset($estudiante["parciales"]["notas"]["nota"])) {
                     foreach($estudiante["parciales"]["notas"]["nota"] as $dataComponente){
                        
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


               }
               else {
                  //Si entra aqui quiere decir que tiene mas de un parcial
                  foreach($estudiante["parciales"] as $keyParcial => $dataParcial) {
                     if(is_numeric($dataParcial["Parcial"])) {
                        $keyParcial	= "Parcial_".strtolower(str_replace(" ","_",$dataParcial["Parcial"]));
                     }
                     else {
                        $keyParcial	= strtolower(str_replace(" ","_",$dataParcial["Parcial"]));
                     }
                   if(isset($dataParcial["notas"]))
                     {
                     if(isset($dataParcial["notas"]["nota"]["tipoNota"])) {  //Cuando llega solo una nota
                        $tempParcial = $dataParcial["notas"]["nota"];
                        $dataParcial["notas"]["nota"]      = NULL;
                        $dataParcial["notas"]["nota"][0]   = $tempParcial;
                        unset($tempParcial);
                     }
                     }
                            $i=0;
                    // print_r($dataParcial["notas"]);
                     if(isset($dataParcial["notas"]))
                     {
                     foreach($dataParcial["notas"] as $keyNotas => $dataNotas) {

                         
                         $i++;
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
                           foreach($dataNotas as $dataComponente){
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

               }

               array_push($datosEstudiantes, $tempArrayEst);
            }

            $datosReturnArray["datosGenerales"]    = $datosGeneralesListado;
            $datosReturnArray["periodosMostrar"]   = $periodosMostrar;
            $datosReturnArray["datosEstudiantes"]  = $datosEstudiantes;

            return $datosReturnArray;
         }
         catch(Exception $e) {
            echo 'Excepción capturada: ',  $e->getMessage(), "\n";
         }
      }
      
      function procesarListadoAsistenciasEstudiantes($datosAsistenciasXML) {
         $dataAsistencia   = array();
         $arregloFechas    = array();
         
         if($datosAsistenciasXML!=NULL) {
            //PARA OBTENER EL ARREGLO DE FECHAS
            if(isset($datosAsistenciasXML["alumno"]["nombres"])){
               $tempAlumno = $datosAsistenciasXML["alumno"];
               $datosAsistenciasXML["alumno"]      = NULL;
               $datosAsistenciasXML["alumno"][0]   = $tempAlumno;
            }
            
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
         else {
            $dataAsistencia = FALSE;
            $arregloFechas  = FALSE;
         }
         
         $dataReturn["dataAsistencia"] = $dataAsistencia;
         $dataReturn["arregloFechas"]  = $arregloFechas;
         
         return $dataReturn;
      }//function procesarListadoAsistenciasEstudiantes();
      

        
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
                   $this->v_html = $this->renderView('TitulacionSisAcademicoBundle:Docentes:tablaEstudiantes.html.twig',
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
            
            $section ='http://localhost/desarrollo/appAcademico/web/docentes/PDF/estudiantes/'.$idDocente.'/'.$idMateria.'/'.$Docente.'/'.$Materia.'/'.$paralelo;
            $response->setData(
                                array(
                                        'redirect' => true,
                                        'section' => $section
                                     )
                              );

            return $response;
        }
        
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
               if ($session->get('perfil') == $perfilDoc || $session->get('perfil') == $perfilEstDoc || $session->get('perfil') == $perfilDocAdm) 
               {
                    try
                    {
                          $lcFacultad="";
                          $lcCarrera="";
                          //$idEstudiante=3;
                          $idDocente=$session->get("id_user");
                          $idRol=$perfilDoc;
                          
                          //$idRol=$session->get("perfil");
                          $Carreras = array();
                          $UgServices = new UgServices;
                          $xml = $UgServices->getConsultaCarreras($idDocente,$idRol);
                             
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
                              return $this->render('TitulacionSisAcademicoBundle:Docentes:docentes_carrerashorarios.html.twig',array(
                                                      'facultades' =>  $Carreras,
                                                      'idDocente'=>$idDocente,
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
                            return $this->render('TitulacionSisAcademicoBundle:Docentes:docentes_carrerashorarios.html.twig',array(
                                                      'facultades' =>  $Carreras,
                                                      'idDocente'=>$idDocente,
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

      public function pdfHorarioExamenAction(Request $request,$idDocente,$idCarrera,$ciclo,$carrera)
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
               if($session->get('perfil') == $perfilDoc || $session->get('perfil') == $perfilEstDoc || $session->get('perfil') == $perfilDocAdm){


                $UgServices = new UgServices;
                $idDocente='10';
                $trama='<PV_Opcion>A</PV_Opcion>
                    <PI_Usuario>'.$idDocente.'</PI_Usuario>
                    <PI_Carrera>'.$idCarrera.'</PI_Carrera>';
                $xml1 = $UgServices->getConsultaHorario_examendoc($trama);
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
                
               
               if ( is_object($xml1))
                  {
//                            foreach($xml1->PX_SALIDA as $xml)
//                             {  echo "yyyyyyyyyyyyyyyyyy";
                              $xml=$xml1->PX_Salida;
                                  foreach($xml->cursos->curso as $lscabHorarios)
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
                                                        $presenta.="<td style='font-size:10px;'> <b>".$value['Materia']." :</b> ".$value['Profesor']."</td>";
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
                                     // }
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

           
          return $this->render('TitulacionSisAcademicoBundle:Docentes:consultahorarios.html.twig');
           
    }#end function
    
      public function pdfhorariosAction(Request $request)
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
            $UgServices    = new UgServices;
            $datosHorarios  = $UgServices->Docentes_Horarios($idUsuario);
          
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
                                              <b> $estudiante </b>
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

                                                    <tr><td align='center' ><b>$estudiante</b></td>
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