<?php

namespace UsuarioBundle\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use UsuarioBundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use APY\BreadcrumbTrailBundle\Annotation\Breadcrumb;

/**
 * User controller.
 *
 * @Route("user")
 * @Breadcrumb("home.title", route= {"name"= "homepage"}, attributes={"icon": "fa fa-home"})
 * @Breadcrumb("user.list", route={"name"= "user_index"})
 */
class UserController extends Controller
{
    /**
     * Lists all user entities.
     *
     * @Route("/", name="user_index", options={"expose"=true})
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $users = $em->getRepository('UsuarioBundle:User')->findAll();

        return $this->render('security/user/index.html.twig', array(
            'users' => $users,
        ));
    }

    /**
     * Creates a new user entity.
     *
     * @Route("/new", name="user_new")
     * @Method({"GET", "POST"})
     *
     * @Breadcrumb("user.create", route={"name"= "user_new"})
     */
    public function newAction(Request $request)
    {
        $user = new User();
        $form = $this->createForm('UsuarioBundle\Form\Type\UserRegistrationType', $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();

            return $this->redirectToRoute('user_show', array('id' => $user->getId()));
        }

        return $this->render('FOSUserBundle:Registration:register.html.twig', array(
            'form' => $form->createView(),
            'user' => $user,
        ));
    }

    /**
     * Finds and displays a user entity.
     *
     * @Route("/{id}", name="user_show", requirements={
     *     "id": "\d+"
     * })
     * @Method("GET")
     *
     * @Breadcrumb("user.show", route={"name"= "user_show", "parameters"={"id"}})
     */
    public function showAction(User $user)
    {
        $deleteForm = $this->createDeleteForm($user);

        return $this->render('security/user/show.html.twig', array(
            'user' => $user,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing user entity.
     *
     * @Route("/{id}/edit", name="user_edit", requirements={
     *     "id": "\d+"
     * })
     * @Method({"GET", "PUT"})
     *
     * @Breadcrumb("user.edit", route={"name"= "user_edit", "parameters"={"id"}})
     */
    public function editAction(Request $request, User $user)
    {
        $editForm = $this->createForm('UsuarioBundle\Form\Type\UserRegistrationType', $user, array(
            'method' => 'PUT',
        ));
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('user_index');
        }

        return $this->render('FOSUserBundle:Profile:edit.html.twig', array(
            'form' => $editForm->createView(),
            'user' => $user,
        ));
    }

    /**
     * Deletes a user entity.
     *
     * @Route("/{id}", name="user_delete", options={"expose"=true}, requirements={
     *     "id": "\d+"
     * })
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, User $user)
    {
        $form = $this->createDeleteForm($user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $trans = $this->get('translator');
            try {
                $em = $this->get('doctrine.orm.entity_manager');
                $em->remove($user);
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
                $message = $trans->trans('delete.error.%key%', array('key' => $trans->trans('user.label')));
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
            'entity' => $this->get('translator')->trans('user.label'),
            'value' => $user->getUsername(),
            'index_route' => 'user_index',
        ));
    }

    /**
     * Creates a form to delete a user entity.
     *
     * @param User $user The user entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(User $user)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('user_delete', array('id' => $user->getId())))
            ->setMethod('DELETE')
            ->getForm();
    }
}
