<?php

namespace CombBundle\Controller;

use AppBundle\Exceptions\ErrorHandler;
use CombBundle\Entity\Recargue;
use CombBundle\Entity\Tarjeta;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use APY\BreadcrumbTrailBundle\Annotation\Breadcrumb;
use Symfony\Component\HttpFoundation\Response;


/**
 * Recargue controller.
 *
 * @Route("recargue")
 * @Breadcrumb("home.title", route= {"name"= "homepage"}, attributes={"icon": "fa fa-home"})
 * @Breadcrumb("request.card.list", route={"name"= "recargue_index"})
 */
class RecargueController extends Controller
{
    /**
     * Lists all recargue entities.
     *
     * @Route("/", name="recargue_index", options={"expose"=true})
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->get('doctrine.orm.entity_manager');

        $recargues = $em->getRepository('CombBundle:Recargue')->filter();

        return $this->render('recargue/index.html.twig', array(
            'recargues' => $recargues,
        ));
    }

    /**
     * Creates a new recargue entity.
     *
     * @Route("/new", name="recargue_new")
     * @Method({"GET", "POST"})
     *
     * @Breadcrumb("request.card.create", route={"name"= "recargue_new"})
     */
    public function newAction(Request $request)
    {
        $recargue = new Recargue();

        $form = $this->createForm('CombBundle\Form\Type\RecargueType', $recargue, array(
            'method' => 'POST',
            'action' => $this->generateUrl('recargue_new'),
            'translator' => $this->get('translator'),
        ));
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->get('doctrine.orm.entity_manager');
            try {
                $em->persist($recargue);
                $em->flush($recargue);

                $this->addFlash('success', $this->get('translator')->trans('created.success'));

                return $this->redirectToRoute('recargue_show', array('id' => $recargue->getId()));
            } catch (\Exception $e) {
                ErrorHandler::handleError($e, 'An error occur creating Recargue.', $this->get('logger'), $this->get('kernel')->isDebug());
                $this->addFlash('danger', $this->get('translator')->trans('created.error', array(), 'messages'));
            }
        }

        return $this->render('recargue/new.html.twig', array(
            'recargue' => $recargue,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a recargue entity.
     *
     * @Route("/{id}", name="recargue_show", requirements={
     *     "id": "\d+"
     * })
     * @Method("GET")
     *
     * @Breadcrumb("request.card.show", route={"name"= "recargue_show", "parameters"={"id"}})
     */
    public function showAction(Recargue $recargue)
    {
        $deleteForm = $this->createDeleteForm($recargue);

        return $this->render('recargue/show.html.twig', array(
            'recargue' => $recargue,
            'deleteForm' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing recargue entity.
     *
     * @Route("/{id}/edit", name="recargue_edit", requirements={
     *     "id": "\d+"
     * })
     * @Method({"GET", "PUT"})
     *
     * @Breadcrumb("request.card.edit", route={"name"= "recargue_edit", "parameters"={"id"}})
     */
    public function editAction(Request $request, Recargue $recargue)
    {
        $editForm = $this->createForm('CombBundle\Form\Type\RecargueType', $recargue, array(
            'method' => 'PUT',
            'action' => $this->generateUrl('recargue_edit', array('id' => $recargue->getId())),
            'translator' => $this->get('translator'),
        ));

        $editForm->handleRequest($request);
        if ($editForm->isSubmitted() && $editForm->isValid()) {
            try {
                $em = $this->get('doctrine.orm.entity_manager');
                $em->flush();

                $this->addFlash('success', $this->get('translator')->trans('updated.success'));

                return $this->redirectToRoute('recargue_show', array('id' => $recargue->getId()));
            } catch (\Exception $e) {
                ErrorHandler::handleError($e, 'An error occur editing Recargue.', $this->get('logger'), $this->get('kernel')->isDebug());
                $this->addFlash('danger', $this->get('translator')->trans('updated.error', array(), 'messages'));
            }
        }

        return $this->render('recargue/edit.html.twig', array(
            'recargue' => $recargue,
            'editForm' => $editForm->createView(),
        ));
    }

    /**
     * Deletes a recargue entity.
     *
     * @Route("/{id}", name="recargue_delete", options={"expose"=true}, requirements={
     *     "id": "\d+"
     * })
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, Recargue $recargue)
    {
        $form = $this->createDeleteForm($recargue);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $trans = $this->get('translator');
            try {
                $em = $this->get('doctrine.orm.entity_manager');
                $em->remove($recargue);
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
                $message = $trans->trans('delete.error.%key%', array('key' => $trans->trans('request.card.label')));
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
            'entity' => $this->get('translator')->trans('request.card.label'),
            'value' => $recargue->__toString(),
            'index_route' => 'recargue_index',
        ));
    }

    /**
     * Creates a form to delete a recargue entity.
     *
     * @param Recargue $recargue The recargue entity
     *
     * @return \Symfony\Component\Form\Form The delete form
     */
    private function createDeleteForm(Recargue $recargue)
    {
        return $this->get('form.factory')
            ->createNamedBuilder('recargue_delete')
            ->setAction($this->generateUrl('recargue_delete', array('id' => $recargue->getId())))
            ->setMethod('DELETE')
            ->getForm();
    }

    /**
     * @param Tarjeta $tarjeta
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     *
     * @Route("/{tarjeta}/flag", name="recargue_dist", options={"expose": true}, requirements={
     *     "tarjeta":"\d+"
     * })
     * @ParamConverter("tarjeta", class="CombBundle\Entity\Tarjeta", options={"mappgin":{"tarjeta":"id"}})
     * @Method("GET")
     */
    public function changeAction(Tarjeta $tarjeta)
    {
        $em = $this->get('doctrine.orm.entity_manager');
        $dists = $em->getRepository('CombBundle:DistribucionXTarjeta')
            ->findBy(array('tarjeta' => $tarjeta));

        $exportData = array();
        foreach ($dists as $dist) {
            $exportData[] = array(
                'id' => $dist->getId(),
                'name' => $dist->__toString(),
            );
        }

        return new JsonResponse($exportData, Response::HTTP_OK);
    }

    /**
     * @param Request $request
     * @param Recargue $recargue
     * @return JsonResponse
     *
     * @Route("/{id}/changer", name="status_changer", options={"expose"=true},
     *     requirements={"id": "\d+"})
     * @Method({"GET", "POST"})
     */
    public function statusSetterAction(Request $request, Recargue $recargue)
    {
        $editForm = $this->formGenerator('POST', $this->generateUrl('status_changer', array(
            'id' => $recargue->getId()
        )), $recargue);

        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            try {
                $em = $this->get('doctrine.orm.entity_manager');

                $tarjeta = $recargue->getTarjeta();
                if ($recargue->getConfirmacion()) {
                    $dist = $recargue->getDistTrjt();
                    $tarjeta->setSaldoInicial($dist->getAsignacion());
                    $tarjeta->setSaldoFinal($dist->getAsignacion());
                } else {
                    $tarjeta->setSaldoInicial(0);
                    $tarjeta->setSaldoFinal(0);
                }

                $em->flush();

                return new JsonResponse(array(
                    'type' => 'success',
                    'message' => $this->get('translator')->trans('updated.success'),
                ), 201);
            } catch (\Exception $e) {
                ErrorHandler::handleError($e, null, $this->get('logger'), $this->get('kernel')->isDebug());

                return new JsonResponse(array(
                    'type' => 'danger',
                    'message' => $this->get('translator')->trans('updated.error'),
                ), 500);
            }
        }

        return new JsonResponse(array(
            'html' => $this->renderView('recargue/_form_status.html.twig', array(
                    'form' => $editForm->createView(),
                    'recargue' => $recargue,
                )
            ),
        ), 200);
    }

    /**
     * @param $methodType
     * @param $action
     * @param Recargue $recargue
     * @return \Symfony\Component\Form\Form|\Symfony\Component\Form\FormInterface
     */
    public function formGenerator($methodType, $action, Recargue $recargue)
    {
        $recargue == null ? $recargue = new Recargue() : '';

        $form = $this->createFormBuilder($recargue)
            ->add('confirmacion', CheckboxType::class, array(
                'label' => false,
                'required' => false,
            ))
            ->setAction($action)
            ->setMethod($methodType)
            ->getForm();

        return $form;
    }
}
