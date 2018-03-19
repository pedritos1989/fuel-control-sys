<?php

namespace CombBundle\Controller;

use AppBundle\Exceptions\ErrorHandler;
use CombBundle\Entity\Area;
use CombBundle\Entity\Solicitud;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use APY\BreadcrumbTrailBundle\Annotation\Breadcrumb;


/**
 * Solicitud controller.
 *
 * @Route("solicitud")
 * @Breadcrumb("home.title", route= {"name"= "homepage"}, attributes={"icon": "fa fa-home"})
 * @Breadcrumb("request.list", route={"name"= "solicitud_index"})
 */
class SolicitudController extends Controller
{
    /**
     * Lists all solicitud entities.
     *
     * @Route("/", name="solicitud_index", options={"expose"=true})
     * @Method("GET")
     */
    public function indexAction(Request $request)
    {
        $em = $this->get('doctrine.orm.entity_manager');

        $reportForm = $this->createReportForm();

        $reportForm->handleRequest($request);
        if ($reportForm->isSubmitted() && $reportForm->isValid()) {
            $submitted = $reportForm->getData();
            $area = $submitted['area'];
            $report = array();
            $report['area'] = isset($area) ? $area->getId() : '';
            $report['mes'] = $submitted['mes'] ?? '';

            return $this->redirectToRoute('rep_sol_trnsp_index', array(
                'parameters' => $report,
            ));
        }

        $solicituds = $em->getRepository('CombBundle:Solicitud')->findAll();

        return $this->render('solicitud/index.html.twig', array(
            'solicituds' => $solicituds,
            'reportForm' => $reportForm->createView(),
        ));
    }

    public function createReportForm()
    {
        $form = $this->get('form.factory')
            ->createNamedBuilder('request_report')
            ->setAction($this->generateUrl('solicitud_index'))
            ->setMethod('GET');

        $form
            ->add('area', EntityType::class, array(
                'label' => 'request.section',
                'required' => false,
                'class' => Area::class,
            ))
            ->add('mes', DateType::class, array(
                'label' => 'request.month',
                'widget' => 'single_text',
                'format' => 'M/y',
                'required' => false,
            ))
            ->add('report', SubmitType::class, array(
                'label' => 'actions.report',
            ));

        return $form->getForm();
    }

    /**
     * Creates a new solicitud entity.
     *
     * @Route("/new", name="solicitud_new")
     * @Method({"GET", "POST"})
     *
     * @Breadcrumb("request.create", route={"name"= "solicitud_new"})
     */
    public function newAction(Request $request)
    {
        $solicitud = new Solicitud();

        $form = $this->createForm('CombBundle\Form\Type\SolicitudType', $solicitud, array(
            'method' => 'POST',
            'action' => $this->generateUrl('solicitud_new'),
        ));
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->get('doctrine.orm.entity_manager');
            try {
                $em->persist($solicitud);
                $em->flush($solicitud);

                $this->addFlash('success', $this->get('translator')->trans('created.success'));

                return $this->redirectToRoute('solicitud_show', array('id' => $solicitud->getId()));
            } catch (\Exception $e) {
                ErrorHandler::handleError($e, 'An error occur creating Solicitud.', $this->get('logger'), $this->get('kernel')->isDebug());
                $this->addFlash('danger', $this->get('translator')->trans('created.error', array(), 'messages'));
            }
        }

        return $this->render('solicitud/new.html.twig', array(
            'solicitud' => $solicitud,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a solicitud entity.
     *
     * @Route("/{id}", name="solicitud_show", requirements={
     *     "id": "\d+"
     * })
     * @Method("GET")
     *
     * @Breadcrumb("request.show", route={"name"= "solicitud_show", "parameters"={"id"}})
     */
    public function showAction(Solicitud $solicitud)
    {
        $deleteForm = $this->createDeleteForm($solicitud);

        return $this->render('solicitud/show.html.twig', array(
            'solicitud' => $solicitud,
            'deleteForm' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing solicitud entity.
     *
     * @Route("/{id}/edit", name="solicitud_edit", requirements={
     *     "id": "\d+"
     * })
     * @Method({"GET", "PUT"})
     *
     * @Breadcrumb("request.edit", route={"name"= "solicitud_edit", "parameters"={"id"}})
     */
    public function editAction(Request $request, Solicitud $solicitud)
    {
        $editForm = $this->createForm('CombBundle\Form\Type\SolicitudType', $solicitud, array(
            'method' => 'PUT',
            'action' => $this->generateUrl('solicitud_edit', array('id' => $solicitud->getId())),
        ));

        $editForm->handleRequest($request);
        if ($editForm->isSubmitted() && $editForm->isValid()) {
            try {
                $em = $this->get('doctrine.orm.entity_manager');
                $em->flush();

                $this->addFlash('success', $this->get('translator')->trans('updated.success'));

                return $this->redirectToRoute('solicitud_show', array('id' => $solicitud->getId()));
            } catch (\Exception $e) {
                ErrorHandler::handleError($e, 'An error occur editing Solicitud.', $this->get('logger'), $this->get('kernel')->isDebug());
                $this->addFlash('danger', $this->get('translator')->trans('updated.error', array(), 'messages'));
            }
        }

        return $this->render('solicitud/edit.html.twig', array(
            'solicitud' => $solicitud,
            'editForm' => $editForm->createView(),
        ));
    }

    /**
     * Deletes a solicitud entity.
     *
     * @Route("/{id}", name="solicitud_delete", options={"expose"=true}, requirements={
     *     "id": "\d+"
     * })
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, Solicitud $solicitud)
    {
        $form = $this->createDeleteForm($solicitud);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $trans = $this->get('translator');
            try {
                $em = $this->get('doctrine.orm.entity_manager');
                $em->remove($solicitud);
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
                $message = $trans->trans('delete.error.%key%', array('key' => $trans->trans('request.label')));
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
            'entity' => $this->get('translator')->trans('request.label'),
            'value' => $solicitud->__toString(),
            'index_route' => 'solicitud_index',
        ));
    }

    /**
     * Creates a form to delete a solicitud entity.
     *
     * @param Solicitud $solicitud The solicitud entity
     *
     * @return \Symfony\Component\Form\Form The delete form
     */
    private function createDeleteForm(Solicitud $solicitud)
    {
        return $this->get('form.factory')
            ->createNamedBuilder('solicitud_delete')
            ->setAction($this->generateUrl('solicitud_delete', array('id' => $solicitud->getId())))
            ->setMethod('DELETE')
            ->getForm();
    }
}
