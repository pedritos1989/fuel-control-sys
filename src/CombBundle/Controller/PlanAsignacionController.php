<?php

namespace CombBundle\Controller;

use AppBundle\Exceptions\ErrorHandler;
use CombBundle\Entity\PlanAsignacion;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use APY\BreadcrumbTrailBundle\Annotation\Breadcrumb;

/**
 * Planasignacion controller.
 *
 * @Route("planasignacion")
 * @Breadcrumb("home.title", route= {"name"= "homepage"}, attributes={"icon": "fa fa-home"})
 * @Breadcrumb("monthly.assign.list", route={"name"= "planasignacion_index"})
 */
class PlanAsignacionController extends Controller
{
    /**
     * Lists all planAsignacion entities.
     *
     * @Route("/", name="planasignacion_index", options={"expose"=true})
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->get('doctrine.orm.entity_manager');

        $planAsignacions = $em->getRepository('CombBundle:PlanAsignacion')->filter();

        return $this->render('planasignacion/index.html.twig', array(
            'planAsignacions' => $planAsignacions,
        ));
    }

    /**
     * Creates a new planAsignacion entity.
     *
     * @Route("/new", name="planasignacion_new")
     * @Method({"GET", "POST"})
     *
     * @Breadcrumb("assign.plan.create", route={"name"= "planasignacion_new"})
     */
    public function newAction(Request $request)
    {
        $planAsignacion = new PlanAsignacion();

        $form = $this->createForm('CombBundle\Form\Type\PlanAsignacionType', $planAsignacion, array(
            'method' => 'POST',
            'action' => $this->generateUrl('planasignacion_new'),
        ));
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->get('doctrine.orm.entity_manager');
            try {
                $em->persist($planAsignacion);
                $em->flush($planAsignacion);

                $this->addFlash('success', $this->get('translator')->trans('created.success'));

                return $this->redirectToRoute('planasignacion_show', array('id' => $planAsignacion->getId()));
            } catch (\Exception $e) {
                ErrorHandler::handleError($e, 'An error occur creating Planasignacion.', $this->get('logger'), $this->get('kernel')->isDebug());
                $this->addFlash('danger', $this->get('translator')->trans('created.error', array(), 'messages'));
            }
        }

        return $this->render('planasignacion/new.html.twig', array(
            'planAsignacion' => $planAsignacion,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a planAsignacion entity.
     *
     * @Route("/{id}", name="planasignacion_show", requirements={
     *     "id": "\d+"
     * })
     * @Method("GET")
     *
     * @Breadcrumb("assign.plan.show", route={"name"= "planasignacion_show", "parameters"={"id"}})
     */
    public function showAction(PlanAsignacion $planAsignacion)
    {
        $deleteForm = $this->createDeleteForm($planAsignacion);

        return $this->render('planasignacion/show.html.twig', array(
            'planAsignacion' => $planAsignacion,
            'deleteForm' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing planAsignacion entity.
     *
     * @Route("/{id}/edit", name="planasignacion_edit", requirements={
     *     "id": "\d+"
     * })
     * @Method({"GET", "PUT"})
     *
     * @Breadcrumb("assign.plan.edit", route={"name"= "planasignacion_edit", "parameters"={"id"}})
     */
    public function editAction(Request $request, PlanAsignacion $planAsignacion)
    {
        $editForm = $this->createForm('CombBundle\Form\Type\PlanAsignacionType', $planAsignacion, array(
            'method' => 'PUT',
            'action' => $this->generateUrl('planasignacion_edit', array('id' => $planAsignacion->getId())),
        ));

        $editForm->handleRequest($request);
        if ($editForm->isSubmitted() && $editForm->isValid()) {
            try {
                $em = $this->get('doctrine.orm.entity_manager');
                $em->flush();

                $this->addFlash('success', $this->get('translator')->trans('updated.success'));

                return $this->redirectToRoute('planasignacion_show', array('id' => $planAsignacion->getId()));
            } catch (\Exception $e) {
                ErrorHandler::handleError($e, 'An error occur editing Planasignacion.', $this->get('logger'), $this->get('kernel')->isDebug());
                $this->addFlash('danger', $this->get('translator')->trans('updated.error', array(), 'messages'));
            }
        }

        return $this->render('planasignacion/edit.html.twig', array(
            'planAsignacion' => $planAsignacion,
            'editForm' => $editForm->createView(),
        ));
    }

    /**
     * Deletes a planAsignacion entity.
     *
     * @Route("/{id}", name="planasignacion_delete", options={"expose"=true}, requirements={
     *     "id": "\d+"
     * })
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, PlanAsignacion $planAsignacion)
    {
        $form = $this->createDeleteForm($planAsignacion);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $trans = $this->get('translator');
            try {
                $em = $this->get('doctrine.orm.entity_manager');
                $em->remove($planAsignacion);
                $em->flush();

                $message = $trans->trans('deleted.success');

                if ($request->isXmlHttpRequest()) {
                    return new JsonResponse(array(
                        'message' => $message,
                        'type' => 'success',
                        'time' => 4000,
                        'layout' => 'topRight',
                    ), 202);
                } else {
                    $this->get('session')->getFlashBag()->add('success', $message);
                }
            } catch (\Exception $e) {
                $message = $trans->trans('delete.error.%key%', array('key' => $trans->trans('assign.plan.label')));
                $this->get('logger')->addCritical(sprintf($message . ' ' . $trans->trans('actions.details') . ': %s', $e->getMessage()));

                if ($request->isXmlHttpRequest()) {
                    return new JsonResponse(array(
                        'message' => $message,
                        'type' => 'danger',
                        'time' => 4000,
                        'layout' => 'topRight',
                    ), 500);
                } else {
                    $this->get('session')->getFlashBag()->add('danger', $message);
                }
            }
        }

        return $this->render('default/modal_template_delete.html.twig', array(
            'delete_form' => $form->createView(),
            'entity' => $this->get('translator')->trans('assign.plan.label'),
            'value' => $planAsignacion->__toString(),
            'index_route' => 'planasignacion_index',
        ));
    }

    /**
     * Creates a form to delete a planAsignacion entity.
     *
     * @param PlanAsignacion $planAsignacion The planAsignacion entity
     *
     * @return \Symfony\Component\Form\Form The delete form
     */
    private function createDeleteForm(PlanAsignacion $planAsignacion)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('planasignacion_delete', array('id' => $planAsignacion->getId())))
            ->setMethod('DELETE')
            ->getForm();
    }
}
