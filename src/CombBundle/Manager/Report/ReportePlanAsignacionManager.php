<?php

namespace CombBundle\Manager\Report;

use AppBundle\Manager\AbstractManager;
use Doctrine\ORM\EntityManager;
use AppBundle\Exceptions\ErrorHandler;
use Liuggio\ExcelBundle\Factory;
use NomencladorBundle\Entity\Servicio;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Translation\DataCollectorTranslator;

class ReportePlanAsignacionManager extends AbstractManager
{
    /**
     * @var DataCollectorTranslator
     */
    private $translator;

    /**
     * @var Factory
     */
    private $phpexcel;

    /**
     * ReporteSolicitudManager constructor.
     *
     * @param EntityManager $em
     * @param ErrorHandler $errorHandler
     * @param EventDispatcherInterface $dispatcher
     * @param DataCollectorTranslator $translator
     * @param Factory $phpexcel
     */
    public function __construct(
        EntityManager $em,
        ErrorHandler $errorHandler,
        EventDispatcherInterface $dispatcher,
        DataCollectorTranslator $translator,
        Factory $phpexcel
    )
    {
        parent::__construct($em, $errorHandler, $dispatcher);
        $this->translator = $translator;
        $this->phpexcel = $phpexcel;
    }

    /**
     * @param $parameters
     * @return \Symfony\Component\HttpFoundation\StreamedResponse
     * @throws \PHPExcel_Exception
     * @throws \PHPExcel_Reader_Exception
     */
    public function designReport($parameters)
    {
        $mes = $parameters['mes'];
        $elaborado = $parameters['elaborado'];
        $cargoElab = $parameters['cargoElab'];
        $aprobado = $parameters['aprobado'];
        $cargoAprob = $parameters['cargoAprob'];

        $objReader = \PHPExcel_IOFactory::createReader('Excel5');
        $objPHPExcel = $objReader->load(__DIR__ . '/../../Model/ExcelTemplates/plantilla_plan_asignacion.xls');

        $style = array(
            'font' => array(
                'name' => 'Arial',
                'size' => 11,
            ),
            'alignment' => array(
                'horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
            )
        );

        $borders = array(
            'borders' => array(
                'allborders' => array('style' => \PHPExcel_Style_Border::BORDER_THIN),
            ),
        );

//        \PHPExcel_Shared_Font::setAutoSizeMethod(\PHPExcel_Shared_Font::AUTOSIZE_METHOD_EXACT);

        $objPHPExcel->getProperties()
            ->setCreator($this->translator->trans('system.title'))
            ->setLastModifiedBy($this->translator->trans('system.title'));

        $objPHPExcel->getActiveSheet()->setCellValue('E3', isset($mes) ? $mes->format('m/Y') : '');

        $plans = $this->em->getRepository('CombBundle:PlanAsignacion')->report(isset($mes) ? $mes->format('Y-m') : '');

        if (count($plans) > 0) {
            $totalDiesel = 0;
            $totalGE = 0;
            $totalGR = 0;
            $row = 6;
            foreach ($plans as $key => $plan) {
                $column = 'a';
                $objPHPExcel->getActiveSheet()->setCellValue(strtoupper($column) . $row, $plan->getArea());
                $this->setBorders($objPHPExcel, $column, $row, $borders);
                $column++;
                $cantidades = $plan->getCantidades()->toArray();

                if (count($cantidades) > 0) {
                    foreach ($cantidades as $ctdad) {
                        if ($ctdad->getServicio()->getId() == Servicio::SERVICE_GR) {
                            $valor = $ctdad->getCantidad();
                            $totalGR += $valor;
                            break;
                        } else {
                            $valor = '';
                        }
                    }
                    $objPHPExcel->getActiveSheet()->setCellValue(strtoupper($column) . $row, $valor);
                    $this->setBorders($objPHPExcel, $column, $row, $borders);
                    $column++;
                    foreach ($cantidades as $ctdad) {
                        if ($ctdad->getServicio()->getId() == Servicio::SERVICE_GE) {
                            $valor = $ctdad->getCantidad();
                            $totalGE += $valor;
                            break;
                        } else {
                            $valor = '';
                        }
                    }
                    $objPHPExcel->getActiveSheet()->setCellValue(strtoupper($column) . $row, $valor);
                    $this->setBorders($objPHPExcel, $column, $row, $borders);
                    $column++;
                    foreach ($cantidades as $ctdad) {
                        if ($ctdad->getServicio()->getId() == Servicio::SERVICE_DIESEL) {
                            $valor = $ctdad->getCantidad();
                            $totalDiesel += $valor;
                            break;
                        } else {
                            $valor = '';
                        }
                    }
                    $objPHPExcel->getActiveSheet()->setCellValue(strtoupper($column) . $row, $valor);
                    $this->setBorders($objPHPExcel, $column, $row, $borders);
                    $column++;
                    $objPHPExcel->getActiveSheet()->setCellValue(strtoupper($column) . $row, $this->translator->trans('assign.plan.monthly.asign') . ': ' . $plan->getAsignacionMensual());
                    $this->setBorders($objPHPExcel, $column, $row, $borders);
                    $objPHPExcel->getActiveSheet()->getColumnDimension(strtoupper($column))->setAutoSize(true);
                }

                $key + 1 < count($plans) ? $row++ : '';
            }
            $row++;
            $row++;
            $totalColumn = 'a';
            $objPHPExcel->getActiveSheet()->setCellValue(strtoupper($totalColumn) . $row, $this->translator->trans('assign.plan.report.total.asign'));
            $this->setBorders($objPHPExcel, $totalColumn, $row, $borders);
            $totalColumn++;
            $objPHPExcel->getActiveSheet()->setCellValue(strtoupper($totalColumn) . $row, $totalGR);
            $this->setBorders($objPHPExcel, $totalColumn, $row, $borders);
            $totalColumn++;
            $objPHPExcel->getActiveSheet()->setCellValue(strtoupper($totalColumn) . $row, $totalGE);
            $this->setBorders($objPHPExcel, $totalColumn, $row, $borders);
            $totalColumn++;
            $objPHPExcel->getActiveSheet()->setCellValue(strtoupper($totalColumn) . $row, $totalDiesel);
            $this->setBorders($objPHPExcel, $totalColumn, $row, $borders);
            $totalColumn = 'a';
            $row++;
            $objPHPExcel->getActiveSheet()->setCellValue(strtoupper($totalColumn) . $row, $this->translator->trans('assign.plan.report.total.free'));
            $this->setBorders($objPHPExcel, $totalColumn, $row, $borders);
            $totalColumn++;
            $objPHPExcel->getActiveSheet()->setCellValue(strtoupper($totalColumn) . $row, $totalGR);
            $this->setBorders($objPHPExcel, $totalColumn, $row, $borders);
            $totalColumn++;
            $objPHPExcel->getActiveSheet()->setCellValue(strtoupper($totalColumn) . $row, $totalGE);
            $this->setBorders($objPHPExcel, $totalColumn, $row, $borders);
            $totalColumn++;
            $objPHPExcel->getActiveSheet()->setCellValue(strtoupper($totalColumn) . $row, $totalDiesel);
            $this->setBorders($objPHPExcel, $totalColumn, $row, $borders);

            $lastCell = strtoupper($column) . $row;
            $objPHPExcel->getActiveSheet()->getStyle('A6:' . $lastCell)->applyFromArray($style);

            $row++;
            $row++;
            $objPHPExcel->getActiveSheet()->setCellValue('A' . $row, $this->translator->trans('assign.plan.report.write.name') . ' ' . $elaborado . ' ' . $cargoElab . ' __________________');
            $row++;
            $objPHPExcel->getActiveSheet()->setCellValue('A' . $row, $this->translator->trans('assign.plan.report.aprove.name') . ' ' . $aprobado . ' ' . $cargoAprob . ' __________________');
        }

        $objPHPExcel->getActiveSheet()->setTitle($this->translator->trans('assign.plan.label'));

        $writer = call_user_func(array('\PHPExcel_IOFactory', 'createWriter'), $objPHPExcel, 'Excel2007');

        $response = $this->phpexcel->createStreamedResponse($writer);

        $dispositionHeader = $response->headers->makeDisposition(
            ResponseHeaderBag::DISPOSITION_ATTACHMENT, sprintf('%s.%s', $this->translator->trans('assign.plan.report.label'), 'xlsx')
        );
        $response->headers->set('Content-Type', 'text/vnd.ms-excel; charset=utf-8');
        $response->headers->set('Pragma', 'public');
        $response->headers->set('Cache-Control', 'maxage=1');
        $response->headers->set('Content-Disposition', $dispositionHeader);
        return $response;
    }
}