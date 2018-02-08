<?php

namespace CombBundle\Controller;

use AppBundle\Exceptions\ErrorHandler;
use CombBundle\Entity\Chofer;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use APY\BreadcrumbTrailBundle\Annotation\Breadcrumb;

/**
 * Chofer controller.
 *
 * @Route("chofer")
 * @Breadcrumb("home.title", route= {"name"= "homepage"}, attributes={"icon": "fa fa-home"})
 * @Breadcrumb("driver.list", route={"name"= "chofer_index"})
 */
class ChoferController extends Controller
{
    /**
     * Lists all chofer entities.
     *
     * @Route("/", name="chofer_index", options={"expose"=true})
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $chofers = $em->getRepository('CombBundle:Chofer')->findAll();

        return $this->render('chofer/index.html.twig', array(
            'chofers' => $chofers,
        ));
    }

    /**
     * Creates a new chofer entity.
     *
     * @Route("/new", name="chofer_new")
     * @Method({"GET", "POST"})
     *
     * @Breadcrumb("driver.create", route={"name"= "chofer_new"})
     */
    public function newAction(Request $request)
    {
        $chofer = new Chofer();
        $form = $this->createForm('CombBundle\Form\ChoferType', $chofer, array(
            'method' => 'POST',
            'action' => $this->generateUrl('chofer_new'),
        ));

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->get('doctrine.orm.entity_manager');
            try {
                $em->persist($chofer);
                $em->flush($chofer);

                $this->addFlash('success', $this->get('translator')->trans('created.success'));

                return $this->redirectToRoute('chofer_show', array('id' => $chofer->getId()));
            } catch (\Exception $e) {
                ErrorHandler::handleError($e, 'An error occur creating Chofer.', $this->get('logger'), $this->get('kernel')->isDebug());
                $this->addFlash('danger', $this->get('translator')->trans('created.error', array(), 'messages'));
            }
        }

        return $this->render('chofer/new.html.twig', array(
            'chofer' => $chofer,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a chofer entity.
     *
     * @Route("/{id}", name="chofer_show", requirements={
     *     "id": "\d+"
     * })
     * @Method("GET")
     *
     * @Breadcrumb("driver.show", route={"name"= "chofer_show", "parameters"={"id"}})
     */
    public function showAction(Chofer $chofer)
    {
        $deleteForm = $this->createDeleteForm($chofer);

        return $this->render('chofer/show.html.twig', array(
            'chofer' => $chofer,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing chofer entity.
     *
     * @Route("/{id}/edit", name="chofer_edit", requirements={
     *     "id": "\d+"
     * })
     * @Method({"GET", "PUT"})
     *
     * @Breadcrumb("driver.edit", route={"name"= "chofer_edit", "parameters"={"id"}})
     */
    public function editAction(Request $request, Chofer $chofer)
    {
        $editForm = $this->createForm('CombBundle\Form\ChoferType', $chofer, array(
            'method' => 'PUT',
            'action' => $this->generateUrl('chofer_edit', array('id' => $chofer->getId())),
        ));

        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            try {
                $em = $this->get('doctrine.orm.entity_manager');
                $em->flush();

                $this->addFlash('success', $this->get('translator')->trans('updated.success'));

                return $this->redirectToRoute('chofer_show', array('id' => $chofer->getId()));
            } catch (\Exception $e) {
                ErrorHandler::handleError($e, 'An error occur editing Chofer.', $this->get('logger'), $this->get('kernel')->isDebug());
                $this->addFlash('danger', $this->get('translator')->trans('updated.error', array(), 'messages'));
            }
        }

        return $this->render('chofer/edit.html.twig', array(
            'chofer' => $chofer,
            'edit_form' => $editForm->createView(),
        ));
    }

    /**
     * Deletes a chofer entity.
     *
     * @Route("/{id}", name="chofer_delete", options={"expose"=true}, requirements={
     *     "id": "\d+"
     * })
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, Chofer $chofer)
    {
        $form = $this->createDeleteForm($chofer);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $trans = $this->get('translator');
            try {
                $em = $this->get('doctrine.orm.entity_manager');
                $em->remove($chofer);
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
                $message = $trans->trans('delete.error.%key%', array('key' => $trans->trans('driver.label')));
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
            'entity' => $this->get('translator')->trans('driver.label'),
            'value' => $chofer->__toString(),
            'index_route' => 'chofer_index',
        ));
    }

    /**
     * Creates a form to delete a chofer entity.
     *
     * @param Chofer $chofer The chofer entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Chofer $chofer)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('chofer_delete', array('id' => $chofer->getId())))
            ->setMethod('DELETE')
            ->getForm();
    }
}
