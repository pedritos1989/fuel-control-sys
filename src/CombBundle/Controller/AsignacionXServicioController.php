<?php

namespace CombBundle\Controller;

use CombBundle\Entity\AsignacionMensual;
use CombBundle\Entity\AsignacionXServicio;
use AppBundle\Exceptions\ErrorHandler;
use CombBundle\Form\Type\AsignacionXServicioType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class AsignacionXServicioController.
 *
 * @Route("/asignacion_mensual/{asignacionMensual}/manage", requirements={
 *     "asignacionMensual": "\d+"
 * })
 * @ParamConverter("asignacionMensual", options={"mapping": {"asignacionMensual": "id"}})
 */
class AsignacionXServicioController extends Controller
{
    /**
     * Lists all asignacionxservicio entities
     *
     * @param Request $request
     * @param AsignacionMensual $asignacionMensual
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @Route("/", name="asign_serv_index", options={"expose": true})
     * @Method("GET")
     */
    public function indexAction(Request $request, AsignacionMensual $asignacionMensual)
    {
        $em = $this->get('doctrine.orm.entity_manager');
        $resources = $em
            ->getRepository('CombBundle:AsignacionXServicio')
            ->findBy(array('asignacionMensual' => $asignacionMensual));

        return $this->render('asignxservicio/index.html.twig', array(
            'asignaciones' => $resources,
        ));
    }

    /**
     * Creates a new asignacionxservicio entity.
     *
     * @param Request $request
     * @param AsignacionMensual $asignacionMensual
     *
     * @return JsonResponse
     *
     * @Route("/new", name="asign_serv_new", options={"expose": true})
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request, AsignacionMensual $asignacionMensual)
    {
        $resource = new AsignacionXServicio();
        $resource->setAsignacionMensual($asignacionMensual);
        $form = $this->createForm(AsignacionXServicioType::class, $resource, array(
            'method' => 'POST',
            'action' => $this->generateUrl('asign_serv_new', array('asignacionMensual' => $asignacionMensual->getId())),
        ));

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $em = $this->get('doctrine.orm.entity_manager');

                $em->persist($resource);
                $em->flush();

                return new JsonResponse(array(
                    'type' => 'success',
                    'message' => $this->get('translator')->trans('created.success'),
                ), 201);
            } catch (\Exception $e) {
                ErrorHandler::handleError($e, null, $this->get('logger'), $this->get('kernel')->isDebug());

                return new JsonResponse(array(
                    'type' => 'danger',
                    'message' => $this->get('translator')->trans('created.error'),
                ), 500);
            }
        }

        return new JsonResponse(array(
            'html' => $this->renderView('asignxservicio/_form.html.twig', array('form' => $form->createView())),
        ), 200);
    }

    /**
     * Gets Delete Modal asignacionxservicio entity
     *
     * @param Request $request
     * @param AsignacionMensual $asignacionMensual
     * @param AsignacionXServicio $resource
     *
     * @return JsonResponse
     *
     * @Route("/{id}/delete", name="asign_serv_delete", options={"expose": true}, requirements={
     *     "id": "\d+"
     * })
     * @Method({"GET", "DELETE"})
     */
    public function deleteAction(Request $request, AsignacionMensual $asignacionMensual, AsignacionXServicio $resource)
    {
        $formDelete = $this->createDeleteForm($asignacionMensual, $resource);
        $formDelete->handleRequest($request);
        if ($formDelete->isSubmitted() && $formDelete->isValid()) {
            try {
                $em = $this->get('doctrine.orm.entity_manager');

                $em->remove($resource);
                $em->flush();

                return new JsonResponse(array(
                    'type' => 'success',
                    'message' => $this->get('translator')->trans('deleted.success'),
                ), 202);
            } catch (\Exception $e) {
                ErrorHandler::handleError($e, null, $this->get('logger'), $this->get('kernel')->isDebug());

                return new JsonResponse(array(
                    'type' => 'danger',
                    'message' => $this->get('translator')->trans('deleted.error'),
                ), 500);
            }
        }

        return new JsonResponse(array(
            'html' => $this->renderView('asignxservicio/_delete.html.twig', array(
                'form' => $formDelete->createView(),
            )),
        ), 200);
    }

    /**
     * Creates a form to delete a asignacionxservicio entity
     *
     * @param AsignacionMensual $asignacionMensual
     * @param AsignacionXServicio $resource
     * @return mixed
     */
    private function createDeleteForm(AsignacionMensual $asignacionMensual, AsignacionXServicio $resource)
    {
        $form = $this->get('form.factory')
            ->createNamedBuilder('asignxserv_delete', FormType::class, null, array(
                'method' => 'DELETE',
                'action' => $this->generateUrl('asign_serv_delete', array('asignacionMensual' => $asignacionMensual->getId(), 'id' => $resource->getId())),
            ))
            ->getForm();

        return $form;
    }
}
