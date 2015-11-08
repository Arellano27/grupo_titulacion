<?php

namespace Titulacion\SisAcademicoBundle\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction(Request $request)
    {
        // echo "entre al index"; exit();
// echo '<pre>'; var_dump("this"); exit();
            $session=$request->getSession();

            $perfilEst    = $this->container->getParameter('perfilEst');
            $perfilDoc    = $this->container->getParameter('perfilDoc');
            $perfilAdmin  = $this->container->getParameter('perfilAdmin'); 
            $perfilEstDoc = $this->container->getParameter('perfilEstDoc'); 
            $perfilEstAdm = $this->container->getParameter('perfilEstAdm'); 
            $perfilDocAdm = $this->container->getParameter('perfilDocAdm'); 
            if ($session->has("perfil")) {
                if($session->get('perfil') == $perfilDoc || $session->get('perfil') == $perfilEstDoc || $session->get('perfil') == $perfilDocAdm){#docente
                     return $this->redirect($this->generateUrl('SisAcademico_Docentes_home'));
                  }elseif ($session->get('perfil') == $perfilEst || $session->get('perfil') == $perfilEstDoc || $session->get('perfil') == $perfilEstAdm) {
                      return $this->redirect($this->generateUrl('estudiantes_notas_actuales'));
                  }
            }else{
        		return $this->render('TitulacionSisAcademicoBundle:Home:login.html.twig');
    		}
    }
}
