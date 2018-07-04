<?php

namespace CombBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class DefaultController extends Controller
{
    /**
     * @Route("/")
     */
    public function indexAction()
    {
        return $this->render('CombBundle:Default:index.html.twig');
    }

    /**
     * @Route("/area/setter", name="area_setter", options={"expose"=true})
     */
    public function areaSetterAction()
    {
        $em = $this->get('doctrine.orm.default_entity_manager');
        $areas = $em->getRepository('CombBundle:Area')->findAll();
        $trans = $this->get('translator');
        $select = '<label for="area-select-dist" class="control-label">'
            . $trans->trans('distribution.area.label')
            . '</label><select id="area-select-dist">';
        foreach ($areas as $area) {
            $select .= '<option value="' . $area->getId() . '">' . $area->getNombre() . '</option>';
        }
        $select .= '</select>';
        return new JsonResponse(array('select' => $select), Response::HTTP_OK);
    }
}
