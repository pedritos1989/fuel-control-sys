<?php

namespace AppBundle\Controller;

use APY\BreadcrumbTrailBundle\Annotation\Breadcrumb;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class DefaultController.
 *
 * @Breadcrumb(title="home.title", attributes={"icon": "fa fa-home"})
 */
class DefaultController extends Controller
{
    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response|\Symfony\Component\HttpFoundation\StreamedResponse
     * @throws \PHPExcel_Exception
     * @throws \PHPExcel_Reader_Exception
     *
     * @Route("/", name="homepage", options={"expose"=true})
     * @Method("GET")
     * @Security("has_role('ROLE_USER')")
     */
    public function indexAction(Request $request)
    {
        $em = $this->get('doctrine.orm.entity_manager');

        $reportForm = $this->createReportForm();

        $reportForm->handleRequest($request);
        if ($reportForm->isSubmitted() && $reportForm->isValid()) {
            $submitted = $reportForm->getData();
            if ($reportForm->get('report_monthly')->isClicked()) {
                return $this->get('combustible.mensual.report.manager')->designReport($submitted);
            }
            if ($reportForm->get('report_vehicle')->isClicked()) {
                return $this->get('desagregacion.report.manager')->designReport($submitted);
            }
            if ($reportForm->get('report_year')->isClicked()) {
                return $this->get('anual.combustible.report.manager')->designReport();
            }
        }

        $asignacionMensuals = $em->getRepository('CombBundle:AsignacionMensual')->filter();

        return $this->render('default/index.html.twig', array(
            'asignacionMensuals' => $asignacionMensuals,
            'reportForm' => $reportForm->createView(),
        ));
    }

    /**
     * @return \Symfony\Component\Form\FormInterface
     */
    public function createReportForm()
    {
        $form = $this->get('form.factory')
            ->createNamedBuilder('fuel_report')
            ->setAction($this->generateUrl('homepage'))
            ->setMethod('GET');
        $now = new \DateTime();

        $form
            ->add('mes', DateType::class, array(
                'label' => false,
                'widget' => 'single_text',
                'format' => 'M/y',
                'required' => false,
                'attr' => array(
                    'value' => $now->format('m/Y')
                )
            ))
            ->add('report_monthly', SubmitType::class)
            ->add('report_vehicle', SubmitType::class)
            ->add('report_year', SubmitType::class);

        return $form->getForm();
    }


    /**
     * @return JsonResponse
     *
     * @Route("/inspection", name="carro_vencimiento", options={"expose"=true})
     */
    public function notificacionExpiracionCarroAction()
    {
        $em = $this->get('doctrine.orm.entity_manager');
        $carros = $em->getRepository('CombBundle:Carro')->findAll();
        $expire = array();
        $now = new \DateTime();
        $trans = $this->get('translator');
        $expire['header'] = $trans->trans('notification.car.label');
        foreach ($carros as $carro) {
            $diff = $now->diff($carro->getInsptecn());
            if ($diff->format('%R%a') <= 7) {
                $expire['body'][$carro->getId()]['message'] = $trans->trans('notification.car.expire', array(
                    'car' => $carro->getMatricula(),
                    'section' => $carro->getArea()->getNombre(),
                ));
                $expire['body'][$carro->getId()]['type'] = $diff->format('%R%a') > 0 ? 'warning' : 'danger';
                $expire['body'][$carro->getId()]['date'] = $carro->getInsptecn()->format('d/m/Y');
            }
        }
        return new JsonResponse($expire, Response::HTTP_OK);
    }

    /**
     * @return JsonResponse
     *
     * @Route("/expiration", name="tarjeta_vencimiento", options={"expose"=true})
     */
    public function notificacionExpiracionTarjetaAction()
    {
        $em = $this->get('doctrine.orm.entity_manager');
        $tarjetas = $em->getRepository('CombBundle:Tarjeta')->findAll();
        $expire = array();
        $now = new \DateTime();
        $trans = $this->get('translator');
        $expire['header'] = $trans->trans('notification.card.label');
        foreach ($tarjetas as $tarjeta) {
            $diff = $now->diff($tarjeta->getFechaVenc());
            if ($diff->format('%R%a') <= 7) {
                $expire['body'][$tarjeta->getId()]['message'] = $trans->trans('notification.card.expire', array(
                    'card' => $tarjeta->getNumero(),
                    'section' => $tarjeta->getArea()->getNombre(),
                ));
                $expire['body'][$tarjeta->getId()]['type'] = $diff->format('%R%a') > 0 ? 'warning' : 'danger';
                $expire['body'][$tarjeta->getId()]['date'] = $tarjeta->getFechaVenc()->format('d/m/Y');
            }
        }
        return new JsonResponse($expire, Response::HTTP_OK);
    }
}
