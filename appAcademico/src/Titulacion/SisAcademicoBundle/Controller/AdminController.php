<?php

namespace Titulacion\SisAcademicoBundle\Controller;

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
            $username     = $request->request->get('user');
            $password    = $request->request->get('pass');
            $password1    = $request->request->get('pass1');
            
            $UgServices = new UgServices;
             $salt    = "µ≈α|⊥ε¢ʟ@δσ";
             $passwordEncr = password_hash($password, PASSWORD_BCRYPT, array("cost" => 14, "salt" => $salt));
            
             $passwordNuevoEncr = password_hash($password1, PASSWORD_BCRYPT, array("cost" => 14, "salt" => $salt));

              $dataMant = $UgServices->mantenimientoUsuario("A",$username,$password,$password1);

                   if ($dataMant) {

                        $countMant  = count($dataMant);
                        if($count == 1){
                         $estado   = $dataMant[0]['pi_estado'];
                         $message   = $dataMant[0]['pv_mensaje'];
                        }
                    }

            $respuesta = array(
               "Codigo" => $estado ,
               "Mensaje" => $message,
            );
            
          return new Response(json_encode($respuesta));
            
    }
}