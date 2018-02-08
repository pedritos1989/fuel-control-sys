<?php

namespace NomencladorBundle\Controller;

use AppBundle\Exceptions\ErrorHandler;
use NomencladorBundle\Entity\EstadoCarro;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use APY\BreadcrumbTrailBundle\Annotation\Breadcrumb;
use NomencladorBundle\NomencladorDefaults;

/**
 * EstadoCarro controller.
 *
 * @Route("car_status")
 * @Breadcrumb("home.title", route= {"name"= "homepage"}, attributes={"icon": "fa fa-home"})
 * @Breadcrumb("nomenclator.car_status.list", route={"name"= "car_status_index"})
 */
class EstadoCarroController extends Controller
{
    /**
     * Lists all estado carro entities.
     *
     * @Route("/", name="car_status_index", options={"expose"=true})
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $estado_carros = $em->getRepository('NomencladorBundle:EstadoCarro')->findAll();

        return $this->render('NomencladorBundle:Nomenclator:index.html.twig', array(
            'entities' => $estado_carros,
            'entityType' => NomencladorDefaults::CAR_STATUS_TYPE,
        ));
    }

    /**
     * Creates a new estado carro entity.
     *
     * @Route("/new", name="car_status_new")
     * @Method({"GET", "POST"})
     *
     * @Breadcrumb("nomenclator.car_status.create", route={"name"= "car_status_new"})
     */
    public function newAction(Request $request)
    {
        $estado_carro = new EstadoCarro();

        $form = $this->formGenerator('POST', $this->generateUrl('car_status_new'), $estado_carro);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->get('doctrine.orm.entity_manager');
            try {
                $em->persist($estado_carro);
                $em->flush();

                $this->addFlash('success', $this->get('translator')->trans('created.success'));

                return $this->redirectToRoute('car_status_show', array('id' => $estado_carro->getId()));
            } catch (\Exception $e) {
                ErrorHandler::handleError($e, 'An error occur creating EstadoCarro.', $this->get('logger'), $this->get('kernel')->isDebug());
                $this->addFlash('danger', $this->get('translator')->trans('created.error', array(), 'messages'));
            }
        }

        return $this->render('NomencladorBundle:Nomenclator:new.html.twig', array(
            'entity' => $estado_carro,
            'entityType' => NomencladorDefaults::CAR_STATUS_TYPE,
            'form' => $form->createView(),
        ));
    }

    /**
     * @param $methodType
     * @param $action
     * @param EstadoCarro $estado_carro
     * @return \Symfony\Component\Form\Form|\Symfony\Component\Form\FormInterface
     */
    public function formGenerator($methodType, $action, EstadoCarro $estado_carro)
    {
        $estado_carro == null ? $estado_carro = new EstadoCarro() : '';

        $form = $this->createFormBuilder($estado_carro)
            ->add('valor', 'text', array(
                'label' => 'nomenclator.car_status.alt',
            ))
            ->setAction($action)
            ->setMethod($methodType)
            ->getForm();

        return $form;
    }

    /**
     * Finds and displays a estado carro entity.
     *
     * @Route("/{id}", name="car_status_show", requirements={
     *     "id": "\d+"
     * })
     * @Method("GET")
     *
     * @Breadcrumb("nomenclator.car_status.show", route={"name"= "car_status_show", "parameters"={"id"}})
     */
    public function showAction(EstadoCarro $estado_carro)
    {
        $deleteForm = $this->createDeleteForm($estado_carro);

        return $this->render('NomencladorBundle:Nomenclator:show.html.twig', array(
            'entity' => $estado_carro,
            'delete_form' => $deleteForm->createView(),
            'entityType' => NomencladorDefaults::CAR_STATUS_TYPE,
        ));
    }

    /**
     * Displays a form to edit an existing estado carro entity.
     *
     * @Route("/{id}/edit", name="car_status_edit", requirements={
     *     "id": "\d+"
     * })
     * @Method({"GET", "PUT"})
     *
     * @Breadcrumb("nomenclator.car_status.edit", route={"name"= "car_status_edit", "parameters"={"id"}})
     */
    public function editAction(Request $request, EstadoCarro $estado_carro)
    {
        $deleteForm = $this->createDeleteForm($estado_carro);
        $editForm = $this->formGenerator('PUT', $this->generateUrl('car_status_edit', array(
            'id' => $estado_carro->getId()
        )), $estado_carro);

        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            try {
                $em = $this->get('doctrine.orm.entity_manager');
                $em->flush();

                $this->addFlash('success', $this->get('translator')->trans('updated.success'));

                return $this->redirectToRoute('car_status_show', array('id' => $estado_carro->getId()));
            } catch (\Exception $e) {
                ErrorHandler::handleError($e, 'An error occur editing EstadoCarro.', $this->get('logger'), $this->get('kernel')->isDebug());
                $this->addFlash('danger', $this->get('translator')->trans('updated.error', array(), 'messages'));
            }
        }

        return $this->render('NomencladorBundle:Nomenclator:edit.html.twig', array(
            'entity' => $estado_carro,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
            'entityType' => NomencladorDefaults::CAR_STATUS_TYPE,
        ));
    }

    /**
     * Deletes a estado carro entity.
     *
     * @Route("/{id}", name="car_status_delete", options={"expose"=true}, requirements={
     *     "id": "\d+"
     * })
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, EstadoCarro $estado_carro)
    {
        $form = $this->createDeleteForm($estado_carro);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $trans = $this->get('translator');
            try {
                $em = $this->get('doctrine.orm.entity_manager');
                $em->remove($estado_carro);
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
                $message = $trans->trans('delete.error.%key%', array('key' => $trans->trans('nomenclator.car_status.label')));
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
            'entity' => $this->get('translator')->trans('nomenclator.car_status.alt'),
            'value' => $estado_carro->getValor(),
            'index_route' => 'car_status_index',
        ));
    }

    /**
     * Creates a form to delete a estado carro entity.
     *
     * @param EstadoCarro $estado_carro The estado carro entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(EstadoCarro $estado_carro)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('car_status_delete', array('id' => $estado_carro->getId())))
            ->setMethod('DELETE')
            ->getForm();
    }

}