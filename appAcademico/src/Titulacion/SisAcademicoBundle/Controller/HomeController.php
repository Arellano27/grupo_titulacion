<?php 
namespace Titulacion\SisAcademicoBundle\Controller;

    use Symfony\Bundle\FrameworkBundle\Controller\Controller;
    use Symfony\Component\HttpFoundation\Response;
    use Symfony\Component\HttpFoundation\Request;
    use Symfony\Component\HttpFoundation\JsonResponse;
    use Titulacion\SisAcademicoBundle\Helper\UgServices;

/**
* 
*/
class HomeController extends Controller
{
	public function ingresarAction(Request $request)
    {

        $perfil = 1;

        if($request->getMethod()=="POST")
        {
            #obtenemos los datos guardados en la variable global
            $perfilEst   = $this->container->getParameter("perfilEst");
            $perfilDoc   = $this->container->getParameter("perfilDoc");
            $perfilAdmin = $this->container->getParameter("perfilAdmin");
            #obtenemos los datos enviados por get
            $username    = $request->request->get('user');
            $password    = $request->request->get('pass');
            #llamamos a la consulta del webservice
            $UgServices = new UgServices;
            $data = $UgServices->getLogin($username,$password);

            if ($data) {
                $login_act     =array();
                $perfilUsuario = null;
                $count         = count($data);
    
                if($count == 1){
                    $perfil        = $data[0]['idrol'];
                    $idUsuario     = $data[0]['usuario'];
                    $nombreUsuario = $data[0]['nombreusuario'];
                    $cedula        = $data[0]['cedula'];
                    $mail          = $data[0]['mail'];
                    $descRol       = $data[0]['descrol'];
                }else{


                    foreach ($data as $login) {
                        $idUsuario     = $login['usuario'];
                        $nombreUsuario = $login['nombreusuario'];
                        $cedula        = $login['cedula'];
                        $mail          = $login['mail'];
                        $descRol       = $login['descrol'];

                        if ($login['idrol'] == $perfilAdmin) {
                            $perfil = (int)$perfil + (int)$perfilAdmin;
                        }elseif ($login['idrol'] == $perfilEst) {
                            $perfil = (int)$perfil + (int)$perfilEst;
                        }elseif ($login['idrol'] == $perfilDoc) {
                            $perfil = (int)$perfil + (int)$perfilDoc;
                        }
                    }
                }
                $session=$request->getSession();
                $session->set("id_user",$idUsuario);
                $session->set("perfil",$perfil); //idrol
                $session->set("nom_usuario",$nombreUsuario);
                $session->set("cedula",$cedula);
                $session->set("mail",$mail);
                $session->set("descRol",$descRol);//nombre rol

                return new Response($perfil);
            }else{
                $perfil = 5;# error usuario y contraseña no
                return new Response('05');
            }

               


        }else{

        return $this->render('TitulacionSisAcademicoBundle:Home:login.html.twig');
        }
        
    }#end function


    /**
     * [indexAction description]
     */
    public function indexAction(Request $request)
        {

            // echo "entre al index"; exit();
            $session=$request->getSession();

            $perfilEst    = $this->container->getParameter('perfilEst');
            $perfilDoc    = $this->container->getParameter('perfilDoc');
            $perfilAdmin  = $this->container->getParameter('perfilAdmin'); 
            $perfilEstDoc = $this->container->getParameter('perfilEstDoc'); 
            $perfilEstAdm = $this->container->getParameter('perfilEstAdm'); 
            $perfilDocAdm = $this->container->getParameter('perfilDocAdm'); 

            if ($session->has("perfil")) {
                if($session->get('perfil') == $perfilDoc || $session->get('perfil') == $perfilEstDoc || $session->get('perfil') == $perfilDocAdm){#docente
                     $idDocente     = 1;
                     $datosCarreras =  array(
                                          array( 'idCarrera' => '135', 'nombreCarrera'=>'Ingeniería en Sistemas Computaciones', 'order'=>'One' ),
                                          array( 'idCarrera' => '246', 'nombreCarrera'=>'Ingeniería Química', 'order'=>'Two' ),
                                          array( 'idCarrera' => '789', 'nombreCarrera'=>'Ingeniería Civil', 'order'=>'Three' )
                                       );         
                     $datosDocente  = array( 'idDocente' => $idDocente );
                     
                     


                     return $this->render('SisAcademicoBundle:Docentes:listadoCarreras.html.twig',
                                                array(
                                                        'data' => array('datosDocente' => $datosDocente,  'datosCarreras' => $datosCarreras)
                                                     )
                                            );
                  }elseif ($session->get('perfil') == $perfilEst || $session->get('perfil') == $perfilEstDoc || $session->get('perfil') == $perfilEstAdm) {
                      return $this->redirect($this->generateUrl('estudiantes_notas_actuales'));
                  }
            }else{
                $pagina = 1;
                $services = '';
                $error = false;
                $user_name = '';
                $redirect = false;
                $datos_menu_izquierda = array();
                $datos_menu_izquierda = array( 'error' => $error,
                        'services' => $services );

                return $this->render('TitulacionSisAcademicoBundle:Home:index.html.twig',
                                        array(
                                                'data' => array('service_selected' => 'DatosMenu',
                                                        'services_menu_izq' => $datos_menu_izquierda

                                                               )
                                             )
                );
            }
        }



        public function logoutAction(Request $request){


        $session=$request->getSession();
        $session->clear();


        $this->get('session')->getFlashBag()->add(
                                'mensaje',
                                'Se ha cerrado sessión exitosamente, gracias por visitarnos'
                            );
         $pagina = 1;
                $services = '';
                $error = false;
                $user_name = '';
                $redirect = false;
                $datos_menu_izquierda = array();
                $datos_menu_izquierda = array( 'error' => $error,
                        'services' => $services );

                return $this->render('TitulacionSisAcademicoBundle:Home:login.html.twig',
                                        array(
                                                'data' => array('service_selected' => 'DatosMenu',
                                                        'services_menu_izq' => $datos_menu_izquierda

                                                               )
                                             )
                );
    }

}