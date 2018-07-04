<?php

namespace NomencladorBundle\Controller;

use AppBundle\Exceptions\ErrorHandler;
use NomencladorBundle\Entity\Persona;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use APY\BreadcrumbTrailBundle\Annotation\Breadcrumb;
use NomencladorBundle\NomencladorDefaults;

/**
 * Persona controller.
 *
 * @Route("people")
 * @Breadcrumb("home.title", route= {"name"= "homepage"}, attributes={"icon": "fa fa-home"})
 * @Breadcrumb("nomenclator.people.list", route={"name"= "people_index"})
 */
class PersonaController extends Controller
{
    /**
     * Lists all persona entities.
     *
     * @Route("/", name="people_index", options={"expose"=true})
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $personas = $em->getRepository('NomencladorBundle:Persona')->findAll();

        return $this->render('NomencladorBundle:Nomenclator:index.html.twig', array(
            'entities' => $personas,
            'entityType' => NomencladorDefaults::PEOPLE_TYPE,
        ));
    }

    /**
     * Creates a new persona entity.
     *
     * @Route("/new", name="people_new")
     * @Method({"GET", "POST"})
     *
     * @Breadcrumb("nomenclator.people.create", route={"name"= "people_new"})
     */
    public function newAction(Request $request)
    {
        $persona = new Persona();

        $form = $this->formGenerator('POST', $this->generateUrl('people_new'), $persona);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->get('doctrine.orm.entity_manager');
            try {
                $em->persist($persona);
                $em->flush();

                $this->addFlash('success', $this->get('translator')->trans('created.success'));

                return $this->redirectToRoute('people_show', array('id' => $persona->getId()));
            } catch (\Exception $e) {
                ErrorHandler::handleError($e, 'An error occur creating Persona.', $this->get('logger'), $this->get('kernel')->isDebug());
                $this->addFlash('danger', $this->get('translator')->trans('created.error', array(), 'messages'));
            }
        }

        return $this->render('NomencladorBundle:Nomenclator:new.html.twig', array(
            'entity' => $persona,
            'entityType' => NomencladorDefaults::PEOPLE_TYPE,
            'form' => $form->createView(),
        ));
    }

    /**
     * @param $methodType
     * @param $action
     * @param Persona $persona
     * @return \Symfony\Component\Form\Form|\Symfony\Component\Form\FormInterface
     */
    public function formGenerator($methodType, $action, Persona $persona)
    {
        $persona == null ? $persona = new Persona() : '';

        $form = $this->createFormBuilder($persona)
            ->add('valor', 'text', array(
                'label' => 'nomenclator.people.alt',
            ))
            ->setAction($action)
            ->setMethod($methodType)
            ->getForm();

        return $form;
    }

    /**
     * Finds and displays a persona entity.
     *
     * @Route("/{id}", name="people_show", requirements={
     *     "id": "\d+"
     * })
     * @Method("GET")
     *
     * @Breadcrumb("nomenclator.people.show", route={"name"= "people_show", "parameters"={"id"}})
     */
    public function showAction(Persona $persona)
    {
        $deleteForm = $this->createDeleteForm($persona);

        return $this->render('NomencladorBundle:Nomenclator:show.html.twig', array(
            'entity' => $persona,
            'delete_form' => $deleteForm->createView(),
            'entityType' => NomencladorDefaults::PEOPLE_TYPE,
        ));
    }

    /**
     * Displays a form to edit an existing persona entity.
     *
     * @Route("/{id}/edit", name="people_edit", requirements={
     *     "id": "\d+"
     * })
     * @Method({"GET", "PUT"})
     *
     * @Breadcrumb("nomenclator.people.edit", route={"name"= "people_edit", "parameters"={"id"}})
     */
    public function editAction(Request $request, Persona $persona)
    {
        $deleteForm = $this->createDeleteForm($persona);
        $editForm = $this->formGenerator('PUT', $this->generateUrl('people_edit', array(
            'id' => $persona->getId()
        )), $persona);

        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            try {
                $em = $this->get('doctrine.orm.entity_manager');
                $em->flush();

                $this->addFlash('success', $this->get('translator')->trans('updated.success'));

                return $this->redirectToRoute('people_show', array('id' => $persona->getId()));
            } catch (\Exception $e) {
                ErrorHandler::handleError($e, 'An error occur editing Persona.', $this->get('logger'), $this->get('kernel')->isDebug());
                $this->addFlash('danger', $this->get('translator')->trans('updated.error', array(), 'messages'));
            }
        }

        return $this->render('NomencladorBundle:Nomenclator:edit.html.twig', array(
            'entity' => $persona,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
            'entityType' => NomencladorDefaults::PEOPLE_TYPE,
        ));
    }

    /**
     * Deletes a persona entity.
     *
     * @Route("/{id}", name="people_delete", options={"expose"=true}, requirements={
     *     "id": "\d+"
     * })
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, Persona $persona)
    {
        $form = $this->createDeleteForm($persona);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $trans = $this->get('translator');
            try {
                $em = $this->get('doctrine.orm.entity_manager');
                $em->remove($persona);
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
                $message = $trans->trans('delete.error.%key%', array('key' => $trans->trans('nomenclator.people.label')));
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
            'entity' => $this->get('translator')->trans('nomenclator.people.alt'),
            'value' => $persona->getValor(),
            'index_route' => 'people_index',
        ));
    }

    /**
     * Creates a form to delete a persona entity.
     *
     * @param Persona $persona The persona entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Persona $persona)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('people_delete', array('id' => $persona->getId())))
            ->setMethod('DELETE')
            ->getForm();
    }

}