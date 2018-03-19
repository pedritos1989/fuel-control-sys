<?php

namespace CombBundle\Controller;

use APY\BreadcrumbTrailBundle\Annotation\Breadcrumb;
use CombBundle\Entity\AsignacionMensual;
use AppBundle\Exceptions\ErrorHandler;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\DateType;


/**
 * Asignacionmensual controller.
 *
 * @Route("asignacionmensual")
 * @Breadcrumb("home.title", route= {"name"= "homepage"}, attributes={"icon": "fa fa-home"})
 * @Breadcrumb("monthly.assign.list", route={"name"= "asignacionmensual_index"})
 */
class AsignacionMensualController extends Controller
{
    /**
     * Lists all asignacionMensual entities.
     *
     * @Route("/", name="asignacionmensual_index", options={"expose"=true})
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->get('doctrine.orm.entity_manager');

        $asignacionMensuals = $em->getRepository('CombBundle:AsignacionMensual')->filter();

        return $this->render('asignacionmensual/index.html.twig', array(
            'asignacionMensuals' => $asignacionMensuals,
        ));
    }

    /**
     * Creates a new asignacionMensual entity.
     *
     * @Route("/new", name="asignacionmensual_new")
     * @Method({"GET", "POST"})
     *
     * @Breadcrumb("monthly.assign.create", route={"name"= "planasignacion_new"})
     */
    public function newAction(Request $request)
    {
        $asignacionMensual = new Asignacionmensual();

        $form = $this->createForm('CombBundle\Form\Type\AsignacionMensualType', $asignacionMensual, array(
            'method' => 'POST',
            'action' => $this->generateUrl('asignacionmensual_new'),
        ));
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->get('doctrine.orm.entity_manager');
            try {
                $em->persist($asignacionMensual);
                $em->flush($asignacionMensual);

                $this->addFlash('success', $this->get('translator')->trans('created.success'));

                return $this->redirectToRoute('asignacionmensual_show', array('id' => $asignacionMensual->getId()));
            } catch (\Exception $e) {
                ErrorHandler::handleError($e, 'An error occur creating Asignacionmensual.', $this->get('logger'), $this->get('kernel')->isDebug());
                $this->addFlash('danger', $this->get('translator')->trans('created.error', array(), 'messages'));
            }
        }

        return $this->render('asignacionmensual/new.html.twig', array(
            'asignacionMensual' => $asignacionMensual,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a asignacionMensual entity.
     *
     * @Route("/{id}", name="asignacionmensual_show", requirements={
     *     "id": "\d+"
     * })
     * @Method("GET")
     *
     * @Breadcrumb("monthly.assign.show", route={"name"= "planasignacion_show", "parameters"={"id"}})
     */
    public function showAction(AsignacionMensual $asignacionMensual)
    {
        $deleteForm = $this->createDeleteForm($asignacionMensual);

        return $this->render('asignacionmensual/show.html.twig', array(
            'asignacionMensual' => $asignacionMensual,
            'deleteForm' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing asignacionMensual entity.
     *
     * @Route("/{id}/edit", name="asignacionmensual_edit", requirements={
     *     "id": "\d+"
     * })
     * @Method({"GET", "PUT"})
     *
     * @Breadcrumb("monthly.assign.edit", route={"name"= "planasignacion_edit", "parameters"={"id"}})
     */
    public function editAction(Request $request, AsignacionMensual $asignacionMensual)
    {
        $editForm = $this->createForm('CombBundle\Form\Type\AsignacionMensualType', $asignacionMensual, array(
            'method' => 'PUT',
            'action' => $this->generateUrl('asignacionmensual_edit', array('id' => $asignacionMensual->getId())),
        ));

        $editForm->handleRequest($request);
        if ($editForm->isSubmitted() && $editForm->isValid()) {
            try {
                $em = $this->get('doctrine.orm.entity_manager');
                $em->flush();

                $this->addFlash('success', $this->get('translator')->trans('updated.success'));

                return $this->redirectToRoute('asignacionmensual_show', array('id' => $asignacionMensual->getId()));
            } catch (\Exception $e) {
                ErrorHandler::handleError($e, 'An error occur editing Asignacionmensual.', $this->get('logger'), $this->get('kernel')->isDebug());
                $this->addFlash('danger', $this->get('translator')->trans('updated.error', array(), 'messages'));
            }
        }

        return $this->render('asignacionmensual/edit.html.twig', array(
            'asignacionMensual' => $asignacionMensual,
            'editForm' => $editForm->createView(),
        ));
    }

    /**
     * Deletes a asignacionMensual entity.
     *
     * @Route("/{id}", name="asignacionmensual_delete", options={"expose"=true}, requirements={
     *     "id": "\d+"
     * })
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, AsignacionMensual $asignacionMensual)
    {
        $form = $this->createDeleteForm($asignacionMensual);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $trans = $this->get('translator');
            try {
                $em = $this->get('doctrine.orm.entity_manager');
                $em->remove($asignacionMensual);
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
                $message = $trans->trans('delete.error.%key%', array('key' => $trans->trans('monthly.assign.label')));
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
            'entity' => $this->get('translator')->trans('monthly.assign.label'),
            'value' => $asignacionMensual->__toString(),
            'index_route' => 'asignacionmensual_index',
        ));
    }

    /**
     * Creates a form to delete a asignacionMensual entity.
     *
     * @param AsignacionMensual $asignacionMensual The asignacionMensual entity
     *
     * @return \Symfony\Component\Form\Form The delete form
     */
    private function createDeleteForm(AsignacionMensual $asignacionMensual)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('asignacionmensual_delete', array('id' => $asignacionMensual->getId())))
            ->setMethod('DELETE')
            ->getForm();
    }
}
