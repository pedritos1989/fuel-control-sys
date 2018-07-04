<?php

namespace CombBundle\Controller;

use CombBundle\Entity\PlanAsignacion;
use CombBundle\Entity\CantidadXPlan;
use AppBundle\Exceptions\ErrorHandler;
use CombBundle\Form\Type\CantidadXPlanType;
use NomencladorBundle\Entity\Servicio;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class CantidadXPlanController.
 *
 * @Route("/plan_asignacion/{planAsignacion}/manage", requirements={
 *     "planAsignacion": "\d+"
 * })
 * @ParamConverter("planAsignacion", options={"mapping": {"planAsignacion": "id"}})
 */
class CantidadXPlanController extends Controller
{
    /**
     * Lists all cantidadxplan entities
     *
     * @param Request $request
     * @param PlanAsignacion $planAsignacion
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @Route("/", name="cant_plan_index", options={"expose": true})
     * @Method("GET")
     */
    public function indexAction(Request $request, PlanAsignacion $planAsignacion)
    {
        $em = $this->get('doctrine.orm.entity_manager');
        $resources = $em
            ->getRepository('CombBundle:CantidadXPlan')
            ->findBy(array('plan' => $planAsignacion));

        return $this->render('cantxplan/index.html.twig', array(
            'asignaciones' => $resources,
        ));
    }

    /**
     * Creates a new cantidadxplan entity.
     *
     * @param Request $request
     * @param PlanAsignacion $planAsignacion
     *
     * @return JsonResponse
     *
     * @Route("/new", name="cant_plan_new", options={"expose": true})
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request, PlanAsignacion $planAsignacion)
    {
        $resource = new CantidadXPlan();
        $resource->setPlan($planAsignacion);
        $form = $this->createForm(CantidadXPlanType::class, $resource, array(
            'method' => 'POST',
            'action' => $this->generateUrl('cant_plan_new', array('planAsignacion' => $planAsignacion->getId())),
        ));

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $em = $this->get('doctrine.orm.entity_manager');

                $em->persist($resource);
                $em->flush();

                return new JsonResponse(array(
                    'type' => 'success',
                    'message' => $this->get('translator')->trans('created.success'),
                ), 201);
            } catch (\Exception $e) {
                ErrorHandler::handleError($e, null, $this->get('logger'), $this->get('kernel')->isDebug());

                return new JsonResponse(array(
                    'type' => 'danger',
                    'message' => $this->get('translator')->trans('created.error'),
                ), 500);
            }
        }

        return new JsonResponse(array(
            'html' => $this->renderView('cantxplan/_form.html.twig', array(
                    'form' => $form->createView(),
                    'planAsignacion' => $planAsignacion,
                )
            ),
        ), 200);
    }

    /**
     * Gets Delete Modal cantidadxplan entity
     *
     * @param Request $request
     * @param PlanAsignacion $planAsignacion
     * @param CantidadXPlan $resource
     *
     * @return JsonResponse
     *
     * @Route("/{id}/delete", name="cant_plan_delete", options={"expose": true}, requirements={
     *     "id": "\d+"
     * })
     * @Method({"GET", "DELETE"})
     */
    public function deleteAction(Request $request, PlanAsignacion $planAsignacion, CantidadXPlan $resource)
    {
        $formDelete = $this->createDeleteForm($planAsignacion, $resource);
        $formDelete->handleRequest($request);
        if ($formDelete->isSubmitted() && $formDelete->isValid()) {
            try {
                $em = $this->get('doctrine.orm.entity_manager');

                $em->remove($resource);
                $em->flush();

                return new JsonResponse(array(
                    'type' => 'success',
                    'message' => $this->get('translator')->trans('deleted.success'),
                ), 202);
            } catch (\Exception $e) {
                ErrorHandler::handleError($e, null, $this->get('logger'), $this->get('kernel')->isDebug());

                return new JsonResponse(array(
                    'type' => 'danger',
                    'message' => $this->get('translator')->trans('deleted.error'),
                ), 500);
            }
        }

        return new JsonResponse(array(
            'html' => $this->renderView('cantxplan/_delete.html.twig', array(
                'form' => $formDelete->createView(),
            )),
        ), 200);
    }

    /**
     * Creates a form to delete a cantidadxplan entity
     *
     * @param PlanAsignacion $planAsignacion
     * @param CantidadXPlan $resource
     * @return mixed
     */
    private function createDeleteForm(PlanAsignacion $planAsignacion, CantidadXPlan $resource)
    {
        $form = $this->get('form.factory')
            ->createNamedBuilder('cantxplan_delete', FormType::class, null, array(
                'method' => 'DELETE',
                'action' => $this->generateUrl('cant_plan_delete', array('planAsignacion' => $planAsignacion->getId(), 'id' => $resource->getId())),
            ))
            ->getForm();

        return $form;
    }

    /**
     * @param Request $request
     * @param PlanAsignacion $planAsignacion
     * @param Servicio $servicio
     * @return JsonResponse
     *
     * @Route("/{servicio}/selection", name="cant_plan_service_changed", options={"expose":true}, requirements={
     *     "id": "\d+"
     * })
     * @ParamConverter("servicio", options={"mapping": {"servicio": "id"}})
     */
    public function cantidadXServicioAction(Request $request, PlanAsignacion $planAsignacion, Servicio $servicio)
    {
        $asignacionMensual = $planAsignacion->getAsignacionMensual();
        $planes = $asignacionMensual->getPlan()->toArray();
        $asignaciones = $asignacionMensual->getCantidades()->toArray();

        $consumido = 0;
        foreach ($planes as $plan) {
            $cantidades = $plan->getCantidades()->toArray();
            foreach ($cantidades as $ctdad) {
                if ($ctdad->getServicio() == $servicio) {
                    $consumido += $ctdad->getCantidad();
                }
            }
        }
        $resultado = array(
            'cantidad' => 0,
            'consumido' => $consumido,
        );
        foreach ($asignaciones as $asignacion) {
            if ($asignacion->getServicio() === $servicio) {
                $resultado['cantidad'] = $asignacion->getCantidad();
            }
        }
        return new JsonResponse($resultado, 200);
    }
}
