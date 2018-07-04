<?php

namespace NomencladorBundle\Controller;

use AppBundle\Exceptions\ErrorHandler;
use NomencladorBundle\Entity\Marca;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use APY\BreadcrumbTrailBundle\Annotation\Breadcrumb;
use NomencladorBundle\NomencladorDefaults;

/**
 * Marca controller.
 *
 * @Route("brand")
 * @Breadcrumb("home.title", route= {"name"= "homepage"}, attributes={"icon": "fa fa-home"})
 * @Breadcrumb("nomenclator.brand.list", route={"name"= "brand_index"})
 */
class MarcaController extends Controller
{
    /**
     * Lists all marca entities.
     *
     * @Route("/", name="brand_index", options={"expose"=true})
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $marcas = $em->getRepository('NomencladorBundle:Marca')->findAll();

        return $this->render('NomencladorBundle:Nomenclator:index.html.twig', array(
            'entities' => $marcas,
            'entityType' => NomencladorDefaults::CAR_BRAND_TYPE,
        ));
    }

    /**
     * Creates a new marca entity.
     *
     * @Route("/new", name="brand_new")
     * @Method({"GET", "POST"})
     *
     * @Breadcrumb("nomenclator.brand.create", route={"name"= "brand_new"})
     */
    public function newAction(Request $request)
    {
        $marca = new Marca();

        $form = $this->formGenerator('POST', $this->generateUrl('brand_new'), $marca);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->get('doctrine.orm.entity_manager');
            try {
                $em->persist($marca);
                $em->flush();

                $this->addFlash('success', $this->get('translator')->trans('created.success'));

                return $this->redirectToRoute('brand_show', array('id' => $marca->getId()));
            } catch (\Exception $e) {
                ErrorHandler::handleError($e, 'An error occur creating Marca.', $this->get('logger'), $this->get('kernel')->isDebug());
                $this->addFlash('danger', $this->get('translator')->trans('created.error', array(), 'messages'));
            }
        }

        return $this->render('NomencladorBundle:Nomenclator:new.html.twig', array(
            'entity' => $marca,
            'entityType' => NomencladorDefaults::CAR_BRAND_TYPE,
            'form' => $form->createView(),
        ));
    }

    /**
     * @param $methodType
     * @param $action
     * @param Marca $marca
     * @return \Symfony\Component\Form\Form|\Symfony\Component\Form\FormInterface
     */
    public function formGenerator($methodType, $action, Marca $marca)
    {
        $marca == null ? $marca = new Marca() : '';

        $form = $this->createFormBuilder($marca)
            ->add('valor', 'text', array(
                'label' => 'nomenclator.brand.alt',
            ))
            ->setAction($action)
            ->setMethod($methodType)
            ->getForm();

        return $form;
    }

    /**
     * Finds and displays a marca entity.
     *
     * @Route("/{id}", name="brand_show", requirements={
     *     "id": "\d+"
     * })
     * @Method("GET")
     *
     * @Breadcrumb("nomenclator.brand.show", route={"name"= "brand_show", "parameters"={"id"}})
     */
    public function showAction(Marca $marca)
    {
        $deleteForm = $this->createDeleteForm($marca);

        return $this->render('NomencladorBundle:Nomenclator:show.html.twig', array(
            'entity' => $marca,
            'delete_form' => $deleteForm->createView(),
            'entityType' => NomencladorDefaults::CAR_BRAND_TYPE,
        ));
    }

    /**
     * Displays a form to edit an existing marca entity.
     *
     * @Route("/{id}/edit", name="brand_edit", requirements={
     *     "id": "\d+"
     * })
     * @Method({"GET", "PUT"})
     *
     * @Breadcrumb("nomenclator.brand.edit", route={"name"= "brand_edit", "parameters"={"id"}})
     */
    public function editAction(Request $request, Marca $marca)
    {
        $deleteForm = $this->createDeleteForm($marca);
        $editForm = $this->formGenerator('PUT', $this->generateUrl('brand_edit', array(
            'id' => $marca->getId()
        )), $marca);

        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            try {
                $em = $this->get('doctrine.orm.entity_manager');
                $em->flush();

                $this->addFlash('success', $this->get('translator')->trans('updated.success'));

                return $this->redirectToRoute('brand_show', array('id' => $marca->getId()));
            } catch (\Exception $e) {
                ErrorHandler::handleError($e, 'An error occur editing Marca.', $this->get('logger'), $this->get('kernel')->isDebug());
                $this->addFlash('danger', $this->get('translator')->trans('updated.error', array(), 'messages'));
            }
        }

        return $this->render('NomencladorBundle:Nomenclator:edit.html.twig', array(
            'entity' => $marca,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
            'entityType' => NomencladorDefaults::CAR_BRAND_TYPE,
        ));
    }

    /**
     * Deletes a marca entity.
     *
     * @Route("/{id}", name="brand_delete", options={"expose"=true}, requirements={
     *     "id": "\d+"
     * })
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, Marca $marca)
    {
        $form = $this->createDeleteForm($marca);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $trans = $this->get('translator');
            try {
                $em = $this->get('doctrine.orm.entity_manager');
                $em->remove($marca);
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
                $message = $trans->trans('delete.error.%key%', array('key' => $trans->trans('nomenclator.brand.label')));
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
            'entity' => $this->get('translator')->trans('nomenclator.brand.alt'),
            'value' => $marca->getValor(),
            'index_route' => 'brand_index',
        ));
    }

    /**
     * Creates a form to delete a marca entity.
     *
     * @param Marca $marca The marca entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Marca $marca)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('brand_delete', array('id' => $marca->getId())))
            ->setMethod('DELETE')
            ->getForm();
    }

}