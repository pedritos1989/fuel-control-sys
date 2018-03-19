<?php

namespace ReporteBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use APY\BreadcrumbTrailBundle\Annotation\Breadcrumb;

/**
 * Class SolicitudTransporteController
 *
 * @package ReporteBundle\Controller
 * @Route("rep_sol_trnsp")
 * @Breadcrumb("home.title", route= {"name"= "homepage"}, attributes={"icon": "fa fa-home"})
 * @Breadcrumb("report.card.request.list", route={"name"= "rep_sol_trnsp_index"})
 */
class SolicitudTransporteController extends Controller
{
    /**
     * @Route("/", name="rep_sol_trnsp_index", options={"expose"=true})
     */
    public function indexAction(Request $request)
    {
        $parameters = $request->query->get('parameters', array());

        dump($parameters);die;
        return $this->render('@Reporte/solicitudtransporte/index.html.twig');
    }
}