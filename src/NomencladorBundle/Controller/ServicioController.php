<?php

namespace NomencladorBundle\Controller;

use AppBundle\Exceptions\ErrorHandler;
use NomencladorBundle\Entity\Servicio;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use APY\BreadcrumbTrailBundle\Annotation\Breadcrumb;
use NomencladorBundle\NomencladorDefaults;

/**
 * Servicio controller.
 *
 * @Route("service")
 * @Breadcrumb("home.title", route= {"name"= "homepage"}, attributes={"icon": "fa fa-home"})
 * @Breadcrumb("nomenclator.service.list", route={"name"= "service_index"})
 */
class ServicioController extends Controller
{
    /**
     * Lists all servicio entities.
     *
     * @Route("/", name="service_index", options={"expose"=true})
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $servicios = $em->getRepository('NomencladorBundle:Servicio')->findAll();

        return $this->render('NomencladorBundle:Nomenclator:index.html.twig', array(
            'entities' => $servicios,
            'entityType' => NomencladorDefaults::SERVICE_TYPE,
        ));
    }

    /**
     * Creates a new servicio entity.
     *
     * @Route("/new", name="service_new")
     * @Method({"GET", "POST"})
     *
     * @Breadcrumb("nomenclator.service.create", route={"name"= "service_new"})
     */
    public function newAction(Request $request)
    {
        $servicio = new Servicio();

        $form = $this->formGenerator('POST', $this->generateUrl('service_new'), $servicio);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->get('doctrine.orm.entity_manager');
            try {
                $em->persist($servicio);
                $em->flush();

                $this->addFlash('success', $this->get('translator')->trans('created.success'));

                return $this->redirectToRoute('service_show', array('id' => $servicio->getId()));
            } catch (\Exception $e) {
                ErrorHandler::handleError($e, 'An error occur creating Servicio.', $this->get('logger'), $this->get('kernel')->isDebug());
                $this->addFlash('danger', $this->get('translator')->trans('created.error', array(), 'messages'));
            }
        }

        return $this->render('NomencladorBundle:Nomenclator:new.html.twig', array(
            'entity' => $servicio,
            'entityType' => NomencladorDefaults::SERVICE_TYPE,
            'form' => $form->createView(),
        ));
    }

    /**
     * @param $methodType
     * @param $action
     * @param Servicio $servicio
     * @return \Symfony\Component\Form\Form|\Symfony\Component\Form\FormInterface
     */
    public function formGenerator($methodType, $action, Servicio $servicio)
    {
        $servicio == null ? $servicio = new Servicio() : '';

        $form = $this->createFormBuilder($servicio)
            ->add('valor', 'text', array(
                'label' => 'nomenclator.service.alt',
            ))
            ->setAction($action)
            ->setMethod($methodType)
            ->getForm();

        return $form;
    }

    /**
     * Finds and displays a servicio entity.
     *
     * @Route("/{id}", name="service_show", requirements={
     *     "id": "\d+"
     * })
     * @Method("GET")
     *
     * @Breadcrumb("nomenclator.service.show", route={"name"= "service_show", "parameters"={"id"}})
     */
    public function showAction(Servicio $servicio)
    {
        $deleteForm = $this->createDeleteForm($servicio);

        return $this->render('NomencladorBundle:Nomenclator:show.html.twig', array(
            'entity' => $servicio,
            'delete_form' => $deleteForm->createView(),
            'entityType' => NomencladorDefaults::SERVICE_TYPE,
        ));
    }

    /**
     * Displays a form to edit an existing servicio entity.
     *
     * @Route("/{id}/edit", name="service_edit", requirements={
     *     "id": "\d+"
     * })
     * @Method({"GET", "PUT"})
     *
     * @Breadcrumb("nomenclator.service.edit", route={"name"= "service_edit", "parameters"={"id"}})
     */
    public function editAction(Request $request, Servicio $servicio)
    {
        $deleteForm = $this->createDeleteForm($servicio);
        $editForm = $this->formGenerator('PUT', $this->generateUrl('service_edit', array(
            'id' => $servicio->getId()
        )), $servicio);

        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            try {
                $em = $this->get('doctrine.orm.entity_manager');
                $em->flush();

                $this->addFlash('success', $this->get('translator')->trans('updated.success'));

                return $this->redirectToRoute('service_show', array('id' => $servicio->getId()));
            } catch (\Exception $e) {
                ErrorHandler::handleError($e, 'An error occur editing Servicio.', $this->get('logger'), $this->get('kernel')->isDebug());
                $this->addFlash('danger', $this->get('translator')->trans('updated.error', array(), 'messages'));
            }
        }

        return $this->render('NomencladorBundle:Nomenclator:edit.html.twig', array(
            'entity' => $servicio,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
            'entityType' => NomencladorDefaults::SERVICE_TYPE,
        ));
    }

    /**
     * Deletes a servicio entity.
     *
     * @Route("/{id}", name="service_delete", options={"expose"=true}, requirements={
     *     "id": "\d+"
     * })
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, Servicio $servicio)
    {
        $form = $this->createDeleteForm($servicio);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $trans = $this->get('translator');
            try {
                $em = $this->get('doctrine.orm.entity_manager');
                $em->remove($servicio);
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
                $message = $trans->trans('delete.error.%key%', array('key' => $trans->trans('nomenclator.service.label')));
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
            'entity' => $this->get('translator')->trans('nomenclator.service.alt'),
            'value' => $servicio->getValor(),
            'index_route' => 'service_index',
        ));
    }

    /**
     * Creates a form to delete a servicio entity.
     *
     * @param Servicio $servicio The servicio entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Servicio $servicio)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('service_delete', array('id' => $servicio->getId())))
            ->setMethod('DELETE')
            ->getForm();
    }

}