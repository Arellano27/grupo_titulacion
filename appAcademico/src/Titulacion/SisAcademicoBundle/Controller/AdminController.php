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

        return $this->render('TitulacionSisAcademicoBundle:Admin:calendario_carrera.html.twig', array());

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


    }

    public function cargar_eventos_carrera_userAction(Request $request)
    {
        return $this->render('TitulacionSisAcademicoBundle:Admin:calendario_academico_carrera_user.html.twig', array());
    }
}