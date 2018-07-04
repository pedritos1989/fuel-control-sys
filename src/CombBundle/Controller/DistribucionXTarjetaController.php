<?php

namespace CombBundle\Controller;

use CombBundle\Entity\Distribucion;
use CombBundle\Entity\DistribucionXTarjeta;
use AppBundle\Exceptions\ErrorHandler;
use CombBundle\Entity\Tarjeta;
use CombBundle\Form\Type\DistribucionXTarjetaType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class DistribucionXTarjetaController.
 *
 * @Route("/distribucion/{distribucion}/manage", requirements={
 *     "distribucion": "\d+"
 * })
 * @ParamConverter("distribucion", options={"mapping": {"distribucion": "id"}})
 */
class DistribucionXTarjetaController extends Controller
{
    /**
     * Lists all distribucionxtarjeta entities
     *
     * @param Request $request
     * @param Distribucion $distribucion
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @Route("/", name="dist_tarj_index", options={"expose": true})
     * @Method("GET")
     */
    public function indexAction(Request $request, Distribucion $distribucion)
    {
        $em = $this->get('doctrine.orm.entity_manager');
        $resources = $em
            ->getRepository('CombBundle:DistribucionXTarjeta')
            ->findBy(array('distribucion' => $distribucion));

        return $this->render('distxtarj/index.html.twig', array(
            'distribuciones' => $resources,
        ));
    }

    /**
     * Creates a new distribucionxtarjeta entity.
     *
     * @param Request $request
     * @param Distribucion $distribucion
     *
     * @return JsonResponse
     *
     * @Route("/new", name="dist_tarj_new", options={"expose": true})
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request, Distribucion $distribucion)
    {
        $resource = new DistribucionXTarjeta();
        $resource->setDistribucion($distribucion);
        $form = $this->createForm(DistribucionXTarjetaType::class, $resource, array(
            'method' => 'POST',
            'action' => $this->generateUrl('dist_tarj_new', array('distribucion' => $distribucion->getId())),
            'translator' => $this->get('translator'),
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
            'html' => $this->renderView('distxtarj/_form.html.twig', array(
                    'form' => $form->createView(),
                    'distribucion' => $distribucion,
                )
            ),
        ), 200);
    }

    /**
     * Gets Delete Modal distribucionxtarjeta entity
     *
     * @param Request $request
     * @param Distribucion $distribucion
     * @param DistribucionXTarjeta $resource
     *
     * @return JsonResponse
     *
     * @Route("/{id}/delete", name="dist_tarj_delete", options={"expose": true}, requirements={
     *     "id": "\d+"
     * })
     * @Method({"GET", "DELETE"})
     */
    public function deleteAction(Request $request, Distribucion $distribucion, DistribucionXTarjeta $resource)
    {
        $formDelete = $this->createDeleteForm($distribucion, $resource);
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
            'html' => $this->renderView('distxtarj/_delete.html.twig', array(
                'form' => $formDelete->createView(),
            )),
        ), 200);
    }

    /**
     * Creates a form to delete a distribucionxtarjeta entity
     *
     * @param Distribucion $distribucion
     * @param DistribucionXTarjeta $resource
     * @return mixed
     */
    private function createDeleteForm(Distribucion $distribucion, DistribucionXTarjeta $resource)
    {
        $form = $this->get('form.factory')
            ->createNamedBuilder('distxtarj_delete', FormType::class, null, array(
                'method' => 'DELETE',
                'action' => $this->generateUrl('dist_tarj_delete', array('distribucion' => $distribucion->getId(), 'id' => $resource->getId())),
            ))
            ->getForm();

        return $form;
    }

    /**
     * @Route("/{tarjeta}/selection", name="dist_tarj_card_chg", options={"expose":true}, requirements={
     *     "id": "\d+"
     * })
     * @ParamConverter("tarjeta", options={"mapping": {"tarjeta": "id"}})
     */
    public function cantidadXServicioAction(Request $request, Distribucion $distribucion, Tarjeta $tarjeta)
    {
        $servicio = $tarjeta->getServicio();
        $cantidades = $distribucion->getPlanAsignacion()->getCantidades();
        $depends = $distribucion->getDistTjts()->toArray();
        $resultado = array(
            'servicio' => $servicio->getValor(),
            'total' => 0,
            'consumido' => 0,
        );

        $consumido = 0;
        foreach ($depends as $elem) {
            if ($elem->getTarjeta()->getServicio() === $servicio) {
                $consumido += $elem->getAsignacion();
            }
        }
        $resultado['consumido'] = $consumido;

        foreach ($cantidades as $ctdad) {
            if ($ctdad->getServicio() === $servicio) {
                $resultado['total'] = $ctdad->getCantidad();
            }
        }
        return new JsonResponse($resultado, 200);
    }

}
