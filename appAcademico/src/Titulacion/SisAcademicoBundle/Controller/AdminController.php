<?php

namespace Titulacion\SisAcademicoBundle\Controller;

    use Symfony\Bundle\FrameworkBundle\Controller\Controller;
    use Symfony\Component\HttpFoundation\Response;
    use Symfony\Component\HttpFoundation\Request;
    use Symfony\Component\HttpFoundation\JsonResponse;
    use Titulacion\SisAcademicoBundle\Helper\UgServices;

class AdminController extends Controller
{
    public function indexAction()
    {
        return $this->render('.html.twig');
    }

    public function calendario_carrera(){
    	return $this->render('TitulacionSisAcademicoBundle:Admin:calendario_carrera.html.twig', array());
    }
}