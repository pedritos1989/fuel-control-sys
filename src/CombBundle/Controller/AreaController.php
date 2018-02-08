<?php

namespace CombBundle\Controller;

use AppBundle\Exceptions\ErrorHandler;
use CombBundle\Entity\Area;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use APY\BreadcrumbTrailBundle\Annotation\Breadcrumb;

/**
 * Area controller.
 *
 * @Route("area")
 * @Breadcrumb("home.title", route= {"name"= "homepage"}, attributes={"icon": "fa fa-home"})
 * @Breadcrumb("section.list", route={"name"= "area_index"})
 */
class AreaController extends Controller
{
    /**
     * Lists all area entities.
     *
     * @Route("/", name="area_index", options={"expose"=true})
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $areas = $em->getRepository('CombBundle:Area')->findAll();

        return $this->render('area/index.html.twig', array(
            'areas' => $areas,
        ));
    }

    /**
     * Creates a new area entity.
     *
     * @Route("/new", name="area_new")
     * @Method({"GET", "POST"})
     *
     * @Breadcrumb("section.create", route={"name"= "area_new"})
     */
    public function newAction(Request $request)
    {
        $area = new Area();

        $form = $this->createForm('CombBundle\Form\AreaType', $area, array(
            'method' => 'POST',
            'action' => $this->generateUrl('area_new'),
        ));
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->get('doctrine.orm.entity_manager');
            try {
                $em->persist($area);
                $em->flush($area);

                $this->addFlash('success', $this->get('translator')->trans('created.success'));

                return $this->redirectToRoute('area_show', array('id' => $area->getId()));
            } catch (\Exception $e) {
                ErrorHandler::handleError($e, 'An error occur creating Area.', $this->get('logger'), $this->get('kernel')->isDebug());
                $this->addFlash('danger', $this->get('translator')->trans('created.error', array(), 'messages'));
            }
        }

        return $this->render('area/new.html.twig', array(
            'area' => $area,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a area entity.
     *
     * @Route("/{id}", name="area_show", requirements={
     *     "id": "\d+"
     * })
     * @Method("GET")
     *
     * @Breadcrumb("section.show", route={"name"= "area_show", "parameters"={"id"}})
     */
    public function showAction(Area $area)
    {
        $deleteForm = $this->createDeleteForm($area);

        return $this->render('area/show.html.twig', array(
            'area' => $area,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing area entity.
     *
     * @Route("/{id}/edit", name="area_edit", requirements={
     *     "id": "\d+"
     * })
     * @Method({"GET", "PUT"})
     *
     * @Breadcrumb("section.edit", route={"name"= "area_edit", "parameters"={"id"}})
     */
    public function editAction(Request $request, Area $area)
    {
        $editForm = $this->createForm('CombBundle\Form\AreaType', $area, array(
            'method' => 'PUT',
            'action' => $this->generateUrl('area_edit', array('id' => $area->getId())),
        ));
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            try {
                $em = $this->get('doctrine.orm.entity_manager');
                $em->flush();

                $this->addFlash('success', $this->get('translator')->trans('updated.success'));

                return $this->redirectToRoute('area_show', array('id' => $area->getId()));
            } catch (\Exception $e) {
                ErrorHandler::handleError($e, 'An error occur editing Area.', $this->get('logger'), $this->get('kernel')->isDebug());
                $this->addFlash('danger', $this->get('translator')->trans('updated.error', array(), 'messages'));
            }
        }

        return $this->render('area/edit.html.twig', array(
            'area' => $area,
            'edit_form' => $editForm->createView(),
        ));
    }

    /**
     * Deletes a area entity.
     *
     * @Route("/{id}", name="area_delete", options={"expose"=true}, requirements={
     *     "id": "\d+"
     * })
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, Area $area)
    {
        $form = $this->createDeleteForm($area);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $trans = $this->get('translator');
            try {
                $em = $this->get('doctrine.orm.entity_manager');
                $em->remove($area);
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
                $message = $trans->trans('delete.error.%key%', array('key' => $trans->trans('section.label')));
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
            'entity' => $this->get('translator')->trans('section.label'),
            'value' => $area->getNombre(),
            'index_route' => 'area_index',
        ));
    }

    /**
     * Creates a form to delete a area entity.
     *
     * @param Area $area The area entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Area $area)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('area_delete', array('id' => $area->getId())))
            ->setMethod('DELETE')
            ->getForm();
    }

    private function reporteExcel()
    {
        $objPhpExcel = $this->get('phpexcel')->createPHPExcelObject();
    }

}
