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
        $rsEventos = $UgServices->getConsultaSoloEventos(1); #como parametros enviaremos siempre 1


        return $this->render('TitulacionSisAcademicoBundle:Admin:calendario_carrera.html.twig', array('data' => $rsEventos));
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

        echo '<pre>'; var_dump("hi"); exit();


    }

    public function cargar_eventos_carrera_userAction(Request $request)
    {

        #llamamos a la consulta del webservice
        $UgServices = new UgServices;

        return $this->render('TitulacionSisAcademicoBundle:Admin:calendario_academico_carrera_user.html.twig', array());
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

        $id_ciclo = 19;
        $UgServices = new UgServices;
        $id_evento    = $request->request->get('id_evento');
        $fec_desde    = $request->request->get('start');
        $fec_hasta    = $request->request->get('end');
        // $date = date_format($fec_desde, 'Y-m-d H:i:s');
// echo '<pre>'; var_dump($date); exit();
        $session=$request->getSession();
        $id_usuario = $session->get("id_user");
        $id_usuario = 11;
        $rsInsertEvent = $UgServices->insertarEventosCalendario($id_evento,$id_ciclo,$fec_desde,$fec_hasta,$id_usuario);
        // echo '<pre>'; var_dump($rsInsertEvent); exit();
        return new Response($rsInsertEvent);
    }#end function


}