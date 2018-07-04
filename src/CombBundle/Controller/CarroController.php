<?php

namespace CombBundle\Controller;

use AppBundle\Exceptions\ErrorHandler;
use CombBundle\Entity\Area;
use CombBundle\Entity\Carro;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use APY\BreadcrumbTrailBundle\Annotation\Breadcrumb;

/**
 * Carro controller.
 *
 * @Route("carro")
 * @Breadcrumb("home.title", route= {"name"= "homepage"}, attributes={"icon": "fa fa-home"})
 * @Breadcrumb("car.list", route={"name"= "carro_index"})
 */
class CarroController extends Controller
{
    /**
     * Lists all carro entities.
     *
     * @Route("/", name="carro_index", options={"expose"=true})
     * @Method("GET")
     */
    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $reportVehicleForm = $this->createReportVehicleForm();
        $reportStatusForm = $this->createStatusVehicleForm();

        $reportVehicleForm->handleRequest($request);
        if ($reportVehicleForm->isSubmitted() && $reportVehicleForm->isValid()) {
            $submitted = $reportVehicleForm->getData();
            return $this->get('vehiculos.report.manager')->designReport($submitted);
        }

        $reportStatusForm->handleRequest($request);
        if ($reportStatusForm->isSubmitted() && $reportStatusForm->isValid()) {
            return $this->get('automotor.report.manager')->designReport();
        }
        
        $carros = $em->getRepository('CombBundle:Carro')->findAll();

        return $this->render('carro/index.html.twig', array(
            'carros' => $carros,
            'reportVehicleForm' => $reportVehicleForm->createView(),
            'reportStatusVehiclesForm' => $reportStatusForm->createView(),
        ));
    }

    /**
     * @return \Symfony\Component\Form\FormInterface
     * @throws \Symfony\Component\OptionsResolver\Exception\InvalidOptionsException
     */
    public function createStatusVehicleForm()
    {
        $form = $this->get('form.factory')
            ->createNamedBuilder('status_vehicles_report')
            ->setAction($this->generateUrl('carro_index'))
            ->setMethod('GET');

        $form
            ->add('report', SubmitType::class, array(
                'label' => 'actions.report',
            ));

        return $form->getForm();
    }

    /**
     * @return \Symfony\Component\Form\FormInterface
     * @throws \Symfony\Component\OptionsResolver\Exception\InvalidOptionsException
     */
    public function createReportVehicleForm()
    {
        $form = $this->get('form.factory')
            ->createNamedBuilder('vehicle_report')
            ->setAction($this->generateUrl('carro_index'))
            ->setMethod('GET');

        $form
            ->add('area', EntityType::class, array(
                'label' => 'car.section',
                'required' => false,
                'class' => Area::class,
            ))
            ->add('report', SubmitType::class, array(
                'label' => 'actions.report',
            ));

        return $form->getForm();
    }

    /**
     * Creates a new carro entity.
     *
     * @Route("/new", name="carro_new")
     * @Method({"GET", "POST"})
     *
     * @Breadcrumb("car.create", route={"name"= "carro_new"})
     */
    public function newAction(Request $request)
    {
        $carro = new Carro();

        $form = $this->createForm('CombBundle\Form\CarroType', $carro, array(
            'method' => 'POST',
            'action' => $this->generateUrl('carro_new'),
        ));
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->get('doctrine.orm.entity_manager');
            try {
                $em->persist($carro);
                $em->flush($carro);

                $this->addFlash('success', $this->get('translator')->trans('created.success'));

                return $this->redirectToRoute('carro_show', array('id' => $carro->getId()));
            } catch (\Exception $e) {
                ErrorHandler::handleError($e, 'An error occur creating Carro.', $this->get('logger'), $this->get('kernel')->isDebug());
                $this->addFlash('danger', $this->get('translator')->trans('created.error', array(), 'messages'));
            }
        }

        return $this->render('carro/new.html.twig', array(
            'carro' => $carro,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a carro entity.
     *
     * @Route("/{id}", name="carro_show", options={"expose"=true}, requirements={
     *     "id": "\d+"
     * })
     * @Method("GET")
     *
     * @Breadcrumb("car.show", route={"name"= "carro_show", "parameters"={"id"}})
     */
    public function showAction(Carro $carro)
    {
        $deleteForm = $this->createDeleteForm($carro);

        return $this->render('carro/show.html.twig', array(
            'carro' => $carro,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing carro entity.
     *
     * @Route("/{id}/edit", name="carro_edit", requirements={
     *     "id": "\d+"
     * })
     * @Method({"GET", "PUT"})
     *
     * @Breadcrumb("car.edit", route={"name"= "carro_edit", "parameters"={"id"}})
     */
    public function editAction(Request $request, Carro $carro)
    {

        $editForm = $this->createForm('CombBundle\Form\CarroType', $carro, array(
            'method' => 'PUT',
            'action' => $this->generateUrl('carro_edit', array('id' => $carro->getId())),
        ));
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            try {
                $em = $this->get('doctrine.orm.entity_manager');
                $em->flush();

                $this->addFlash('success', $this->get('translator')->trans('updated.success'));

                return $this->redirectToRoute('carro_show', array('id' => $carro->getId()));
            } catch (\Exception $e) {
                ErrorHandler::handleError($e, 'An error occur editing Carro.', $this->get('logger'), $this->get('kernel')->isDebug());
                $this->addFlash('danger', $this->get('translator')->trans('updated.error', array(), 'messages'));
            }
        }

        return $this->render('carro/edit.html.twig', array(
            'carro' => $carro,
            'edit_form' => $editForm->createView(),
        ));
    }

    /**
     * Deletes a carro entity.
     *
     * @Route("/{id}", name="carro_delete", options={"expose"=true}, requirements={
     *     "id": "\d+"
     * })
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, Carro $carro)
    {
        $form = $this->createDeleteForm($carro);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $trans = $this->get('translator');
            try {
                $em = $this->get('doctrine.orm.entity_manager');
                $em->remove($carro);
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
                $message = $trans->trans('delete.error.%key%', array('key' => $trans->trans('car.label')));
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
            'entity' => $this->get('translator')->trans('car.label'),
            'value' => $carro->getMatricula(),
            'index_route' => 'carro_index',
        ));
    }

    /**
     * Creates a form to delete a carro entity.
     *
     * @param Carro $carro The carro entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Carro $carro)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('carro_delete', array('id' => $carro->getId())))
            ->setMethod('DELETE')
            ->getForm();
    }
}
