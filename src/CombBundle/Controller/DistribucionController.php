<?php

namespace CombBundle\Controller;

use AppBundle\Exceptions\ErrorHandler;
use CombBundle\Entity\Distribucion;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use APY\BreadcrumbTrailBundle\Annotation\Breadcrumb;

/**
 * Distribucion controller.
 *
 * @Route("distribucion")
 * @Breadcrumb("home.title", route= {"name"= "homepage"}, attributes={"icon": "fa fa-home"})
 * @Breadcrumb("distribution.list", route={"name"= "distribucion_index"})
 */
class DistribucionController extends Controller
{
    /**
     * Lists all distribucion entities.
     *
     * @Route("/", name="distribucion_index", options={"expose"=true})
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $distribucions = $em->getRepository('CombBundle:Distribucion')->findAll();

        return $this->render('distribucion/index.html.twig', array(
            'distribucions' => $distribucions,
        ));
    }

    /**
     * Creates a new distribucion entity.
     *
     * @Route("/new", name="distribucion_new")
     * @Method({"GET", "POST"})
     *
     * @Breadcrumb("distribution.create", route={"name"= "distribucion_new"})
     */
    public function newAction(Request $request)
    {
        $distribucion = new Distribucion();

        $form = $this->createForm('CombBundle\Form\DistribucionType', $distribucion, array(
            'method' => 'POST',
            'action' => $this->generateUrl('distribucion_new'),
        ));
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->get('doctrine.orm.entity_manager');
            try {
                $em->persist($distribucion);
                $em->flush($distribucion);

                $this->addFlash('success', $this->get('translator')->trans('created.success'));

                return $this->redirectToRoute('distribucion_show', array('id' => $distribucion->getId()));
            } catch (\Exception $e) {
                ErrorHandler::handleError($e, 'An error occur creating Distribucion.', $this->get('logger'), $this->get('kernel')->isDebug());
                $this->addFlash('danger', $this->get('translator')->trans('created.error', array(), 'messages'));
            }
        }

        return $this->render('distribucion/new.html.twig', array(
            'distribucion' => $distribucion,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a distribucion entity.
     *
     * @Route("/{id}", name="distribucion_show", requirements={
     *     "id": "\d+"
     * })
     * @Method("GET")
     *
     * @Breadcrumb("distribution.show", route={"name"= "distribucion_show", "parameters"={"id"}})
     */
    public function showAction(Distribucion $distribucion)
    {
        $deleteForm = $this->createDeleteForm($distribucion);

        return $this->render('distribucion/show.html.twig', array(
            'distribucion' => $distribucion,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing distribucion entity.
     *
     * @Route("/{id}/edit", name="distribucion_edit", requirements={
     *     "id": "\d+"
     * })
     * @Method({"GET", "PUT"})
     *
     * @Breadcrumb("distribution.edit", route={"name"= "distribucion_edit", "parameters"={"id"}})
     */
    public function editAction(Request $request, Distribucion $distribucion)
    {
        $editForm = $this->createForm('CombBundle\Form\DistribucionType', $distribucion, array(
            'method' => 'PUT',
            'action' => $this->generateUrl('distribucion_edit', array('id' => $distribucion->getId())),
        ));

        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            try {
                $em = $this->get('doctrine.orm.entity_manager');
                $em->flush();

                $this->addFlash('success', $this->get('translator')->trans('updated.success'));

                return $this->redirectToRoute('distribucion_show', array('id' => $distribucion->getId()));
            } catch (\Exception $e) {
                ErrorHandler::handleError($e, 'An error occur editing Distribucion.', $this->get('logger'), $this->get('kernel')->isDebug());
                $this->addFlash('danger', $this->get('translator')->trans('updated.error', array(), 'messages'));
            }
        }

        return $this->render('distribucion/edit.html.twig', array(
            'distribucion' => $distribucion,
            'edit_form' => $editForm->createView(),
        ));
    }

    /**
     * Deletes a distribucion entity.
     *
     * @Route("/{id}", name="distribucion_delete", options={"expose"=true}, requirements={
     *     "id": "\d+"
     * })
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, Distribucion $distribucion)
    {
        $form = $this->createDeleteForm($distribucion);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $trans = $this->get('translator');
            try {
                $em = $this->get('doctrine.orm.entity_manager');
                $em->remove($distribucion);
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
                $message = $trans->trans('delete.error.%key%', array('key' => $trans->trans('distribution.label')));
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
            'entity' => $this->get('translator')->trans('distribution.label'),
            'value' => $distribucion->__toString(),
            'index_route' => 'distribucion_index',
        ));
    }

    /**
     * Creates a form to delete a distribucion entity.
     *
     * @param Distribucion $distribucion The distribucion entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Distribucion $distribucion)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('distribucion_delete', array('id' => $distribucion->getId())))
            ->setMethod('DELETE')
            ->getForm();
    }
}
