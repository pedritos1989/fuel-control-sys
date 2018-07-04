<?php

namespace CombBundle\Controller;

use AppBundle\Exceptions\ErrorHandler;
use CombBundle\Entity\Area;
use CombBundle\Entity\Tarjeta;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use APY\BreadcrumbTrailBundle\Annotation\Breadcrumb;
use Symfony\Component\HttpFoundation\Response;


/**
 * Tarjeta controller.
 *
 * @Route("tarjeta")
 * @Breadcrumb("home.title", route= {"name"= "homepage"}, attributes={"icon": "fa fa-home"})
 * @Breadcrumb("card.list", route={"name"= "tarjeta_index"})
 */
class TarjetaController extends Controller
{
    /**
     * Lists all tarjeta entities.
     *
     * @Route("/", name="tarjeta_index", options={"expose"=true})
     * @Method("GET")
     */
    public function indexAction(Request $request)
    {
        $em = $this->get('doctrine.orm.entity_manager');

        $tarjetas = $em->getRepository('CombBundle:Tarjeta')->findAll();

        return $this->render('tarjeta/index.html.twig', array(
            'tarjetas' => $tarjetas,
        ));
    }

    /**
     * Creates a new tarjeta entity.
     *
     * @Route("/new", name="tarjeta_new")
     * @Method({"GET", "POST"})
     *
     * @Breadcrumb("card.create", route={"name"= "tarjeta_new"})
     */
    public function newAction(Request $request)
    {
        $tarjeta = new Tarjeta();

        $form = $this->createForm('CombBundle\Form\Type\TarjetaType', $tarjeta, array(
            'method' => 'POST',
            'action' => $this->generateUrl('tarjeta_new'),
        ));
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->get('doctrine.orm.entity_manager');
            try {
                $em->persist($tarjeta);
                $em->flush($tarjeta);

                $this->addFlash('success', $this->get('translator')->trans('created.success'));

                return $this->redirectToRoute('tarjeta_show', array('id' => $tarjeta->getId()));
            } catch (\Exception $e) {
                ErrorHandler::handleError($e, 'An error occur creating Tarjeta.', $this->get('logger'), $this->get('kernel')->isDebug());
                $this->addFlash('danger', $this->get('translator')->trans('created.error', array(), 'messages'));
            }
        }

        return $this->render('tarjeta/new.html.twig', array(
            'tarjeta' => $tarjeta,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a tarjeta entity.
     *
     * @Route("/{id}", name="tarjeta_show", options={"expose"=true}, requirements={
     *     "id": "\d+"
     * })
     * @Method("GET")
     *
     * @Breadcrumb("card.show", route={"name"= "tarjeta_show", "parameters"={"id"}})
     */
    public function showAction(Tarjeta $tarjeta)
    {
        $deleteForm = $this->createDeleteForm($tarjeta);

        return $this->render('tarjeta/show.html.twig', array(
            'tarjeta' => $tarjeta,
            'deleteForm' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing tarjeta entity.
     *
     * @Route("/{id}/edit", name="tarjeta_edit", requirements={
     *     "id": "\d+"
     * })
     * @Method({"GET", "PUT"})
     *
     * @Breadcrumb("card.edit", route={"name"= "tarjeta_edit", "parameters"={"id"}})
     */
    public function editAction(Request $request, Tarjeta $tarjeta)
    {
        $editForm = $this->createForm('CombBundle\Form\Type\TarjetaType', $tarjeta, array(
            'method' => 'PUT',
            'action' => $this->generateUrl('tarjeta_edit', array('id' => $tarjeta->getId())),
        ));

        $editForm->handleRequest($request);
        if ($editForm->isSubmitted() && $editForm->isValid()) {
            try {
                $em = $this->get('doctrine.orm.entity_manager');
                $em->flush();

                $this->addFlash('success', $this->get('translator')->trans('updated.success'));

                return $this->redirectToRoute('tarjeta_show', array('id' => $tarjeta->getId()));
            } catch (\Exception $e) {
                ErrorHandler::handleError($e, 'An error occur editing Tarjeta.', $this->get('logger'), $this->get('kernel')->isDebug());
                $this->addFlash('danger', $this->get('translator')->trans('updated.error', array(), 'messages'));
            }
        }

        return $this->render('tarjeta/edit.html.twig', array(
            'tarjeta' => $tarjeta,
            'editForm' => $editForm->createView(),
        ));
    }

    /**
     * Deletes a tarjeta entity.
     *
     * @Route("/{id}", name="tarjeta_delete", options={"expose"=true}, requirements={
     *     "id": "\d+"
     * })
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, Tarjeta $tarjeta)
    {
        $form = $this->createDeleteForm($tarjeta);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $trans = $this->get('translator');
            try {
                $em = $this->get('doctrine.orm.entity_manager');
                $em->remove($tarjeta);
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
                $message = $trans->trans('delete.error.%key%', array('key' => $trans->trans('card.label')));
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
            'entity' => $this->get('translator')->trans('card.label'),
            'value' => $tarjeta->__toString(),
            'index_route' => 'tarjeta_index',
        ));
    }

    /**
     * Creates a form to delete a tarjeta entity.
     *
     * @param Tarjeta $tarjeta The tarjeta entity
     *
     * @return \Symfony\Component\Form\Form The delete form
     */
    private function createDeleteForm(Tarjeta $tarjeta)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('tarjeta_delete', array('id' => $tarjeta->getId())))
            ->setMethod('DELETE')
            ->getForm();
    }

    /**
     * @param Area $section
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     *
     * @Route("/{section}/option", name="selection_card", options={"expose": true}, requirements={
     *     "section":"\d+"
     * })
     * @ParamConverter("section", class="CombBundle\Entity\Area", options={"mappgin":{"section":"id"}})
     * @Method("GET")
     */
    public function changeAction(Area $section)
    {
        $em = $this->get('doctrine.orm.entity_manager');
        $tarjetas = $em->getRepository('CombBundle:Tarjeta')
            ->findBy(array('area' => $section), array('servicio' => 'ASC'));
        $trans = $this->get('translator');
        $exportData = array();
        foreach ($tarjetas as $tarjeta) {
            $exportData[] = array(
                'id' => $tarjeta->getId(),
                'name' => $tarjeta->getNumero(),
                'service' => $trans->trans('card.service') . ': ' . $tarjeta->getServicio(),
            );
        }

        return new JsonResponse($exportData, Response::HTTP_OK);
    }

}
