<?php

namespace CombBundle\Controller;

use AppBundle\Exceptions\ErrorHandler;
use CombBundle\Entity\Chip;
use CombBundle\Entity\Tarjeta;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use APY\BreadcrumbTrailBundle\Annotation\Breadcrumb;

/**
 * Chip controller.
 *
 * @Route("chip")
 * @Breadcrumb("home.title", route= {"name"= "homepage"}, attributes={"icon": "fa fa-home"})
 * @Breadcrumb("chip.list", route={"name"= "chip_index"})
 */
class ChipController extends Controller
{
    /**
     * Lists all chip entities.
     *
     * @Route("/", name="chip_index", options={"expose"=true})
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $chips = $em->getRepository('CombBundle:Chip')->filter();

        return $this->render('chip/index.html.twig', array(
            'chips' => $chips,
        ));
    }

    /**
     * Creates a new chip entity.
     *
     * @Route("/new", name="chip_new")
     * @Method({"GET", "POST"})
     *
     * @Breadcrumb("chip.create", route={"name"= "chip_new"})
     */
    public function newAction(Request $request)
    {
        $chip = new Chip();

        $form = $this->createForm('CombBundle\Form\ChipType', $chip, array(
            'method' => 'POST',
            'action' => $this->generateUrl('chip_new'),
            'translator' => $this->get('translator'),
        ));
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->get('doctrine.orm.entity_manager');
            try {
                if ($chip->getSaldoFinal() > 0) {
                    $tarjeta = $chip->getTarjeta();
                    $tarjeta->setSaldoFinal($chip->getSaldoFinal());
                }
                $em->persist($chip);
                $em->flush();

                $this->addFlash('success', $this->get('translator')->trans('created.success'));

                return $this->redirectToRoute('chip_show', array('id' => $chip->getId()));
            } catch (\Exception $e) {
                ErrorHandler::handleError($e, 'An error occur creating Chip.', $this->get('logger'), $this->get('kernel')->isDebug());
                $this->addFlash('danger', $this->get('translator')->trans('created.error', array(), 'messages'));
            }
        }

        return $this->render('chip/new.html.twig', array(
            'chip' => $chip,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a chip entity.
     *
     * @Route("/{id}", name="chip_show", requirements={
     *     "id": "\d+"
     * })
     * @Method("GET")
     *
     * @Breadcrumb("chip.show", route={"name"= "chip_show", "parameters"={"id"}})
     */
    public function showAction(Chip $chip)
    {
        $deleteForm = $this->createDeleteForm($chip);

        return $this->render('chip/show.html.twig', array(
            'chip' => $chip,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing chip entity.
     *
     * @Route("/{id}/edit", name="chip_edit", requirements={
     *     "id": "\d+"
     * })
     * @Method({"GET", "PUT"})
     *
     * @Breadcrumb("chip.edit", route={"name"= "chip_edit", "parameters"={"id"}})
     */
    public function editAction(Request $request, Chip $chip)
    {
        $editForm = $this->createForm('CombBundle\Form\ChipType', $chip, array(
            'method' => 'PUT',
            'action' => $this->generateUrl('chip_edit', array('id' => $chip->getId())),
            'translator' => $this->get('translator'),
        ));

        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            try {
                $em = $this->get('doctrine.orm.entity_manager');
                $em->flush();

                $this->addFlash('success', $this->get('translator')->trans('updated.success'));

                return $this->redirectToRoute('chip_show', array('id' => $chip->getId()));
            } catch (\Exception $e) {
                ErrorHandler::handleError($e, 'An error occur editing Chip.', $this->get('logger'), $this->get('kernel')->isDebug());
                $this->addFlash('danger', $this->get('translator')->trans('updated.error', array(), 'messages'));
            }
        }

        return $this->render('chip/edit.html.twig', array(
            'chip' => $chip,
            'edit_form' => $editForm->createView(),
        ));
    }

    /**
     * Deletes a chip entity.
     *
     * @Route("/{id}", name="chip_delete", options={"expose"=true}, requirements={
     *     "id": "\d+"
     * })
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, Chip $chip)
    {
        $form = $this->createDeleteForm($chip);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $trans = $this->get('translator');
            try {
                $em = $this->get('doctrine.orm.entity_manager');
                $em->remove($chip);
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
                $message = $trans->trans('delete.error.%key%', array('key' => $trans->trans('chip.label')));
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
            'entity' => $this->get('translator')->trans('chip.label'),
            'value' => $chip->__toString(),
            'index_route' => 'chip_index',
        ));
    }

    /**
     * Creates a form to delete a chip entity.
     *
     * @param Chip $chip The chip entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Chip $chip)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('chip_delete', array('id' => $chip->getId())))
            ->setMethod('DELETE')
            ->getForm();
    }
}
