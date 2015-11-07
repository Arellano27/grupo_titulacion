<?php
   namespace Titulacion\SisAcademicoBundle\Controller;

    use Symfony\Bundle\FrameworkBundle\Controller\Controller;
    use Symfony\Component\HttpFoundation\Response;
    use Symfony\Component\HttpFoundation\Request;
    use Symfony\Component\HttpFoundation\JsonResponse;
    use Titulacion\SisAcademicoBundle\Helper\UgServices;
    use Symfony\Component\HttpFoundation\ResponseHeaderBag;
    use \PHPExcel_Style_Alignment;
    use \PHPExcel_Style_Border;

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
         $idMateria  = $request->request->get('idMateria');
         $idCarrera  = $request->request->get('idCarrera');
         //Menu de Notas por Materia para Profesor
         return $this->render('TitulacionSisAcademicoBundle:Docentes:notasAlumnosMateria.html.twig',
                         array(
                              'condition' => '',
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
         $idCiclo    = $request->request->get('idCiclo');
         $idCarrera  = $request->request->get('idCarrera');
         $nombreMateriaTitulo  = $request->request->get('tituloPanelMateria');

         $datosDocente	= array( 'idDocente' => $idDocente );
         $datosMateria	= array( 'idMateria' => $idMateria, 'nombreMateriaTitulo' => $nombreMateriaTitulo);
         
         $UgServices       = new UgServices;
         
         /*Consulta de la información de los parciales - INICIO*/
         $datosConsultaParciales = array( 'idCarrera' => $idCarrera);
         $datosParciales         = $UgServices->Docentes_getParcialesCarrera($datosConsultaParciales);
         /*Consulta de la información de los parciales - INICIO*/
         
         //$datosAsistencias["ID PARCIAL / TODOS"]
         
         ////->Obtener los datos para la grafica de asistencias - INICIO
         $datosAsistencias = array();
         
            //Todos los parciales
         $datosConsulta	= array( 'idMateria' => $idMateria,
                                 'idCiclo' => $idCiclo,
                                 'idParcial' => 'todos');
         $tempDataAsistencia        = $UgServices->Docentes_Graph_getAsistencias($datosConsulta);
         if(isset($tempDataAsistencia["MateriaParalelo"])) {
            $tempDataAsistencia  = $tempDataAsistencia["MateriaParalelo"];
         }
         $datosAsistencias["todos"] = $tempDataAsistencia;
         
            //Por parcial
         foreach($datosParciales as $dataParcial){
            $datosConsulta	= array( 'idMateria' => $idMateria,
                                    'idCiclo' => $idCiclo,
                                    'idParcial' => $dataParcial["numero_parcial"]);
            $tempDataAsistencia        = $UgServices->Docentes_Graph_getAsistencias($datosConsulta);
            if(isset($tempDataAsistencia["MateriaParalelo"])) {
               $tempDataAsistencia  = $tempDataAsistencia["MateriaParalelo"];
            }
            $datosAsistencias[$dataParcial["nombre"]] = $tempDataAsistencia;
         }
         ////->Obtener los datos para la grafica de asistencias - FIN
         
         ////->Obtener los datos para la grafica detalle de aprobados - INICIO
         $datosNotasDetalle = array();
            //Todos los parciales
         $datosConsulta	= array( 'idMateria' => $idMateria,
                                 'idCiclo' => $idCiclo,
                                 'idParcial' => 'todos');
         $tempDataNotasDetalle        = $UgServices->Docentes_Graph_getAprobadosDetalle($datosConsulta);
         
         if(isset($tempDataNotasDetalle["MateriaParaleloCiclo"])) {
            $tempDataNotasDetalle  = $tempDataNotasDetalle["MateriaParaleloCiclo"];
         }
         $datosNotasDetalle["todos"] = $tempDataNotasDetalle;
         
            //Por parcial
         foreach($datosParciales as $dataParcial){
            $datosConsulta	= array( 'idMateria' => $idMateria,
                                    'idCiclo' => $idCiclo,
                                    'idParcial' => $dataParcial["numero_parcial"]);
            $tempDataNotasDetalle        = $UgServices->Docentes_Graph_getAprobadosDetalle($datosConsulta);
            
            if(isset($tempDataNotasDetalle["MateriaParaleloCicloParcial"])) {
               $tempDataNotasDetalle  = $tempDataNotasDetalle["MateriaParaleloCicloParcial"];
            }
            $datosNotasDetalle[$dataParcial["nombre"]] = $tempDataNotasDetalle;
            //var_dump($datosNotasDetalle[$dataParcial["nombre"]]);
         }
         ////->Obtener los datos para la grafica detalle de aprobados - FIN
         
         ////->Obtener los datos para la grafica de resumen de aprobados - INICIO
         $datosNotasResumen = array();
            //Todos los parciales
         $datosConsulta	= array( 'idMateria' => $idMateria,
                                 'idCiclo' => $idCiclo,
                                 'idParcial' => 'todos');
         $tempDataNotasResumen        = $UgServices->Docentes_Graph_getAprobadosResumen($datosConsulta);
         var_dump('hola', $tempDataNotasResumen);
         if(isset($tempDataNotasDetalle["MateriaParaleloCiclo"])) {
            $tempDataNotasDetalle  = $tempDataNotasDetalle["MateriaParaleloCiclo"];
         }
         $datosNotasDetalle["todos"] = $tempDataNotasDetalle;
         
            //Por parcial
         foreach($datosParciales as $dataParcial){
            $datosConsulta	= array( 'idMateria' => $idMateria,
                                    'idCiclo' => $idCiclo,
                                    'idParcial' => $dataParcial["numero_parcial"]);
            $tempDataNotasDetalle        = $UgServices->Docentes_Graph_getAprobadosResumen($datosConsulta);
            
            if(isset($tempDataNotasDetalle["MateriaParaleloCicloParcial"])) {
               $tempDataNotasDetalle  = $tempDataNotasDetalle["MateriaParaleloCicloParcial"];
            }
            $datosNotasDetalle[$dataParcial["nombre"]] = $tempDataNotasDetalle;
            //var_dump($datosNotasDetalle[$dataParcial["nombre"]]);
         }
         ////->Obtener los datos para la grafica de resumen de aprobados - FIN
         
         return $this->render('TitulacionSisAcademicoBundle:Docentes:visionGeneralMateria.html.twig',
                           array(
                              'datosDocente' => $datosDocente,
                              'datosMateria' => $datosMateria,
                              'datosParciales' => $datosParciales,
                              'datosAsistencias' => $datosAsistencias,
                              'datosNotasDetalle' => $datosNotasDetalle,
                              'datosNotasResumen' => $datosNotasResumen
                               
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


            $datosReturnArray = $this->procesarListadoNotasEstudiantes($datosNotasArray);

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
      }      // exportarListadoAsistenciasAlumnosMateriaAction()

      

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
               else {
                  //Si entra aqui quiere decir que tiene mas de un parcial
                  foreach($estudiante["parciales"] as $keyParcial => $dataParcial) {
                     if(is_numeric($dataParcial["Parcial"])) {
                        $keyParcial	= "Parcial_".strtolower(str_replace(" ","_",$dataParcial["Parcial"]));
                     }
                     else {
                        $keyParcial	= strtolower(str_replace(" ","_",$dataParcial["Parcial"]));
                     }
                     
                     if(isset($dataParcial["notas"]["nota"]["tipoNota"])) {  //Cuando llega solo una nota
                        $tempParcial = $dataParcial["notas"]["nota"];
                        $dataParcial["notas"]["nota"]      = NULL;
                        $dataParcial["notas"]["nota"][0]   = $tempParcial;
                        unset($tempParcial);
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

   }