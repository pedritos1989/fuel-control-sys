<?php

namespace NomencladorBundle\Controller;

use AppBundle\Exceptions\ErrorHandler;
use NomencladorBundle\Entity\TipoCarro;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use APY\BreadcrumbTrailBundle\Annotation\Breadcrumb;
use NomencladorBundle\NomencladorDefaults;

/**
 * TipoCarro controller.
 *
 * @Route("car_type")
 * @Breadcrumb("home.title", route= {"name"= "homepage"}, attributes={"icon": "fa fa-home"})
 * @Breadcrumb("nomenclator.car_type.list", route={"name"= "car_type_index"})
 */
class TipoCarroController extends Controller
{
    /**
     * Lists all tipo carro entities.
     *
     * @Route("/", name="car_type_index", options={"expose"=true})
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $tipo_carros = $em->getRepository('NomencladorBundle:TipoCarro')->findAll();

        return $this->render('NomencladorBundle:Nomenclator:index.html.twig', array(
            'entities' => $tipo_carros,
            'entityType' => NomencladorDefaults::CAR_TYPE_TYPE,
        ));
    }

    /**
     * Creates a new tipo carro entity.
     *
     * @Route("/new", name="car_type_new")
     * @Method({"GET", "POST"})
     *
     * @Breadcrumb("nomenclator.car_type.create", route={"name"= "car_type_new"})
     */
    public function newAction(Request $request)
    {
        $tipo_carro = new TipoCarro();

        $form = $this->formGenerator('POST', $this->generateUrl('car_type_new'), $tipo_carro);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->get('doctrine.orm.entity_manager');
            try {
                $em->persist($tipo_carro);
                $em->flush();

                $this->addFlash('success', $this->get('translator')->trans('created.success'));

                return $this->redirectToRoute('car_type_show', array('id' => $tipo_carro->getId()));
            } catch (\Exception $e) {
                ErrorHandler::handleError($e, 'An error occur creating TipoCarro.', $this->get('logger'), $this->get('kernel')->isDebug());
                $this->addFlash('danger', $this->get('translator')->trans('created.error', array(), 'messages'));
            }
        }

        return $this->render('NomencladorBundle:Nomenclator:new.html.twig', array(
            'entity' => $tipo_carro,
            'entityType' => NomencladorDefaults::CAR_TYPE_TYPE,
            'form' => $form->createView(),
        ));
    }

    /**
     * @param $methodType
     * @param $action
     * @param TipoCarro $tipo_carro
     * @return \Symfony\Component\Form\Form|\Symfony\Component\Form\FormInterface
     */
    public function formGenerator($methodType, $action, TipoCarro $tipo_carro)
    {
        $tipo_carro == null ? $tipo_carro = new TipoCarro() : '';

        $form = $this->createFormBuilder($tipo_carro)
            ->add('valor', 'text', array(
                'label' => 'nomenclator.car_type.alt',
            ))
            ->setAction($action)
            ->setMethod($methodType)
            ->getForm();

        return $form;
    }

    /**
     * Finds and displays a tipo carro entity.
     *
     * @Route("/{id}", name="car_type_show", requirements={
     *     "id": "\d+"
     * })
     * @Method("GET")
     *
     * @Breadcrumb("nomenclator.car_type.show", route={"name"= "car_type_show", "parameters"={"id"}})
     */
    public function showAction(TipoCarro $tipo_carro)
    {
        $deleteForm = $this->createDeleteForm($tipo_carro);

        return $this->render('NomencladorBundle:Nomenclator:show.html.twig', array(
            'entity' => $tipo_carro,
            'delete_form' => $deleteForm->createView(),
            'entityType' => NomencladorDefaults::CAR_TYPE_TYPE,
        ));
    }

    /**
     * Displays a form to edit an existing tipo carro entity.
     *
     * @Route("/{id}/edit", name="car_type_edit", requirements={
     *     "id": "\d+"
     * })
     * @Method({"GET", "PUT"})
     *
     * @Breadcrumb("nomenclator.car_type.edit", route={"name"= "car_type_edit", "parameters"={"id"}})
     */
    public function editAction(Request $request, TipoCarro $tipo_carro)
    {
        $deleteForm = $this->createDeleteForm($tipo_carro);
        $editForm = $this->formGenerator('PUT', $this->generateUrl('car_type_edit', array(
            'id' => $tipo_carro->getId()
        )), $tipo_carro);

        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            try {
                $em = $this->get('doctrine.orm.entity_manager');
                $em->flush();

                $this->addFlash('success', $this->get('translator')->trans('updated.success'));

                return $this->redirectToRoute('car_type_show', array('id' => $tipo_carro->getId()));
            } catch (\Exception $e) {
                ErrorHandler::handleError($e, 'An error occur editing TipoCarro.', $this->get('logger'), $this->get('kernel')->isDebug());
                $this->addFlash('danger', $this->get('translator')->trans('updated.error', array(), 'messages'));
            }
        }

        return $this->render('NomencladorBundle:Nomenclator:edit.html.twig', array(
            'entity' => $tipo_carro,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
            'entityType' => NomencladorDefaults::CAR_TYPE_TYPE,
        ));
    }

    /**
     * Deletes a tipo carro entity.
     *
     * @Route("/{id}", name="car_type_delete", options={"expose"=true}, requirements={
     *     "id": "\d+"
     * })
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, TipoCarro $tipo_carro)
    {
        $form = $this->createDeleteForm($tipo_carro);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $trans = $this->get('translator');
            try {
                $em = $this->get('doctrine.orm.entity_manager');
                $em->remove($tipo_carro);
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
                $message = $trans->trans('delete.error.%key%', array('key' => $trans->trans('nomenclator.car_type.label')));
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
            'entity' => $this->get('translator')->trans('nomenclator.car_type.alt'),
            'value' => $tipo_carro->getValor(),
            'index_route' => 'car_type_index',
        ));
    }

    /**
     * Creates a form to delete a tipo carro entity.
     *
     * @param TipoCarro $tipo_carro The tipo carro entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(TipoCarro $tipo_carro)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('car_type_delete', array('id' => $tipo_carro->getId())))
            ->setMethod('DELETE')
            ->getForm();
    }

}