<?php

namespace CombBundle\Controller;

use APY\BreadcrumbTrailBundle\Annotation\Breadcrumb;
use CombBundle\Entity\ClientUploadedResource;
use AppBundle\Exceptions\ErrorHandler;
use CombBundle\Form\Type\ClientUploadedResourceType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use UsuarioBundle\Entity\User;

/**
 * Class ClientUploadResourceController.
 *
 * @Route("/upload")
 * @Breadcrumb("home.title", route= {"name"= "homepage"}, attributes={"icon": "fa fa-home"})
 * @Breadcrumb("client.uploadresource.list", route={"name"= "client_uploadresource"})
 */
class ClientUploadResourceController extends Controller
{
    /**
     * Lists all clientuploadedresource entities
     *
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @Route("/", name="client_uploadresource", options={"expose": true})
     * @Method("GET")
     */
    public function indexAction(Request $request)
    {
        $em = $this->get('doctrine.orm.entity_manager');
        $resources = $em
            ->getRepository('CombBundle:ClientUploadedResource')->findAll();


        return $this->render('client_uploadresource/index.html.twig', array(
            'resources' => $resources,
        ));
    }

    /**
     * Creates a new clientuploadedresource entity.
     *
     * @param Request $request
     *
     * @return JsonResponse
     *
     * @Route("/new", name="client_uploadresource_new", options={"expose": true})
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $resource = new ClientUploadedResource();
        $this->getUser() instanceof User ? $resource->setClient($this->getUser()) : '';
        $form = $this->createForm(ClientUploadedResourceType::class, $resource, array(
            'method' => 'POST',
            'action' => $this->generateUrl('client_uploadresource_new'),
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
            'html' => $this->renderView('client_uploadresource/_form.html.twig', array('form' => $form->createView())),
        ), 200);
    }

    /**
     * Gets Delete Modal clientuploadedresource entity
     *
     * @param Request $request
     *
     * @param ClientUploadedResource $resource
     *
     * @return JsonResponse
     *
     * @Route("/{id}/delete", name="client_uploadresource_delete", options={"expose": true}, requirements={
     *     "id": "\d+"
     * })
     * @Method({"GET", "DELETE"})
     */
    public function deleteAction(Request $request, ClientUploadedResource $resource)
    {
        $formDelete = $this->createDeleteForm($resource);
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
            'html' => $this->renderView('client_uploadresource/_delete.html.twig', array(
                'form' => $formDelete->createView(),
            )),
        ), 200);
    }

    /**
     * Creates a form to delete a clientuploadedresource entity
     *
     * @param ClientUploadedResource $resource
     * @return mixed
     */
    private function createDeleteForm(ClientUploadedResource $resource)
    {
        $form = $this->get('form.factory')
            ->createNamedBuilder('uploadresource_delete', FormType::class, null, array(
                'method' => 'DELETE',
                'action' => $this->generateUrl('client_uploadresource_delete', array('id' => $resource->getId())),
            ))
            ->getForm();

        return $form;
    }
}
