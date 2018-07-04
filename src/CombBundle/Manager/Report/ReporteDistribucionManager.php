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

class ReporteDistribucionManager extends AbstractManager
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
        $revisado = $parameters['revisado'];
        $cargoRev = $parameters['cargoRev'];

        $objReader = \PHPExcel_IOFactory::createReader('Excel2007');
        $objPHPExcel = $objReader->load(__DIR__ . '/../../Model/ExcelTemplates/plantilla_distribucion.xlsx');

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

        $objPHPExcel->getProperties()
            ->setCreator($this->translator->trans('system.title'))
            ->setLastModifiedBy($this->translator->trans('system.title'));

        $objPHPExcel->getActiveSheet()->getColumnDimension()->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);

        $fila = 1;
        $planOperativo = 1;
        $operativos = $this->em->getRepository('CombBundle:AsignacionMensual')->report(isset($mes) ? $mes->format('Y-m') : '');

        foreach ($operativos as $operativo) {

            $objPHPExcel->getActiveSheet()->setCellValue('A' . $fila, 'ASIGNACIÓN Y DISTRIBUCIÓN DE COMBUSTIBLE ' . $planOperativo . ' DEL MES ' . $mes->format('m/Y'));

            $fila++;
            $fila++;
            $objPHPExcel->getActiveSheet()->setCellValue('A' . $fila, 'ASIGNACIÓN ' . $planOperativo . ' DEL MES: ' . $mes->format('m'));

            $valoresOperativo = $operativo->getCantidades()->toArray();
            $diesel = 0;
            $gr = 0;
            $ge = 0;
            foreach ($valoresOperativo as $valor) {
                if ($valor->getServicio()->getId() == Servicio::SERVICE_GE)
                    $ge = $valor->getCantidad();

                if ($valor->getServicio()->getId() == Servicio::SERVICE_GR)
                    $gr = $valor->getCantidad();

                if ($valor->getServicio()->getId() == Servicio::SERVICE_DIESEL)
                    $diesel = $valor->getCantidad();
            }
            $objPHPExcel->getActiveSheet()->setCellValue('B' . $fila, strtoupper(
                    $this->em->getRepository('NomencladorBundle:Servicio')->find(Servicio::SERVICE_GE)->getValor()
                ) . ':');
            $objPHPExcel->getActiveSheet()->setCellValue('C' . $fila, $ge . ' LITROS');
            $fila++;
            $objPHPExcel->getActiveSheet()->setCellValue('B' . $fila, strtoupper(
                    $this->em->getRepository('NomencladorBundle:Servicio')->find(Servicio::SERVICE_GR)->getValor()
                ) . ':');
            $objPHPExcel->getActiveSheet()->setCellValue('C' . $fila, $gr . ' LITROS');
            $fila++;
            $objPHPExcel->getActiveSheet()->setCellValue('B' . $fila, strtoupper(
                    $this->em->getRepository('NomencladorBundle:Servicio')->find(Servicio::SERVICE_DIESEL)->getValor()
                ) . ':');
            $objPHPExcel->getActiveSheet()->setCellValue('C' . $fila, $diesel . ' LITROS');
            $planOperativo++;

            $fila++;
            $fila++;

            $col = 'A';

            $firstCell = 'A' . $fila;

            $objPHPExcel->getActiveSheet()->setCellValue($col . $fila, 'ÁREA');
            $this->setBorders($objPHPExcel, $col, $fila, $borders);
            $col++;
            $objPHPExcel->getActiveSheet()->setCellValue($col . $fila, 'MATRÍCULA');
            $this->setBorders($objPHPExcel, $col, $fila, $borders);
            $col++;
            $objPHPExcel->getActiveSheet()->setCellValue($col . $fila, 'TARJETA ASIGNADA');
            $this->setBorders($objPHPExcel, $col, $fila, $borders);
            $col++;
            $objPHPExcel->getActiveSheet()->setCellValue($col . $fila, strtoupper(
                $this->em->getRepository('NomencladorBundle:Servicio')->find(Servicio::SERVICE_GE)->getValor()
            ));
            $this->setBorders($objPHPExcel, $col, $fila, $borders);
            $col++;
            $objPHPExcel->getActiveSheet()->setCellValue($col . $fila, strtoupper(
                $this->em->getRepository('NomencladorBundle:Servicio')->find(Servicio::SERVICE_GR)->getValor()
            ));
            $this->setBorders($objPHPExcel, $col, $fila, $borders);
            $col++;
            $objPHPExcel->getActiveSheet()->setCellValue($col . $fila, strtoupper(
                $this->em->getRepository('NomencladorBundle:Servicio')->find(Servicio::SERVICE_DIESEL)->getValor()
            ));
            $this->setBorders($objPHPExcel, $col, $fila, $borders);
            $col++;
            $objPHPExcel->getActiveSheet()->setCellValue($col . $fila, 'FIRMA');
            $this->setBorders($objPHPExcel, $col, $fila, $borders);
            $col++;
            $objPHPExcel->getActiveSheet()->setCellValue($col . $fila, 'RESPONSABLE');
            $this->setBorders($objPHPExcel, $col, $fila, $borders);

            $fila++;

            $planes = $operativo->getPlan()->toArray();
            $col = 'A';

            $diesel = 0;
            $gr = 0;
            $ge = 0;

            foreach ($planes as $plan) {

                $objPHPExcel->getActiveSheet()->setCellValue($col . $fila, $plan->getArea());

                $distribuciones = $plan->getDistribuciones()->toArray();

                foreach ($distribuciones as $distribucion) {
                    $distTarjetas = $distribucion->getDistTjts()->toArray();
                    $merge = count($distTarjetas);

                    if ($merge > 1) {
                        $objPHPExcel->getActiveSheet()->mergeCells($col . $fila . ':' . $col . ($fila + ($merge - 1)));
                        $this->setBorders($objPHPExcel, $col, $fila, $borders);
                    }

                    $aux = $fila;
                    foreach ($distTarjetas as $diTjt) {
                        $tarjeta = $diTjt->getTarjeta();
                        $asignacion = $diTjt->getAsignacion();
                        $objPHPExcel->getActiveSheet()->setCellValue('C' . $aux, $tarjeta);
                        $this->setBorders($objPHPExcel, 'C', $aux, $borders);

                        $carro = $tarjeta->getCarros()->first();

                        if ($tarjeta->getServicio()->getId() === Servicio::SERVICE_GE) {
                            $objPHPExcel->getActiveSheet()->setCellValue('D' . $aux, $asignacion);
                            $ge += $asignacion;
                        }

                        $this->setBorders($objPHPExcel, 'D', $aux, $borders);

                        if ($tarjeta->getServicio()->getId() === Servicio::SERVICE_GR) {
                            $objPHPExcel->getActiveSheet()->setCellValue('E' . $aux, $asignacion);
                            $gr += $asignacion;
                        }

                        $this->setBorders($objPHPExcel, 'E', $aux, $borders);

                        if ($tarjeta->getServicio()->getId() === Servicio::SERVICE_DIESEL) {
                            $objPHPExcel->getActiveSheet()->setCellValue('F' . $aux, $asignacion);
                            $diesel += $asignacion;
                        }

                        $this->setBorders($objPHPExcel, 'F', $aux, $borders);

                        if ($carro)
                            $objPHPExcel->getActiveSheet()->setCellValue('B' . $aux, $carro->getMatricula());

                        $this->setBorders($objPHPExcel, 'B', $aux, $borders);

                        $this->setBorders($objPHPExcel, 'G', $aux, $borders);

                        $this->setBorders($objPHPExcel, 'H', $aux, $borders);

                        $aux++;
                    }
                }
                $fila = $aux;
            }

            $this->setBorders($objPHPExcel, 'A', $fila, $borders);
            $this->setBorders($objPHPExcel, 'B', $fila, $borders);
            $this->setBorders($objPHPExcel, 'C', $fila, $borders);
            $objPHPExcel->getActiveSheet()->mergeCells('A' . $fila . ':C' . $fila);

            $objPHPExcel->getActiveSheet()->setCellValue('A' . $fila, 'TOTAL');
            $this->setBorders($objPHPExcel, 'A', $fila, $borders);

            $objPHPExcel->getActiveSheet()->setCellValue('D' . $fila, $ge);
            $this->setBorders($objPHPExcel, 'D', $fila, $borders);

            $objPHPExcel->getActiveSheet()->setCellValue('E' . $fila, $gr);
            $this->setBorders($objPHPExcel, 'E', $fila, $borders);

            $objPHPExcel->getActiveSheet()->setCellValue('F' . $fila, $diesel);
            $this->setBorders($objPHPExcel, 'F', $fila, $borders);

            $fila++;
            $fila++;

            $objPHPExcel->getActiveSheet()->setCellValue('A' . $fila, sprintf('%s %s', $this->translator->trans('distribution.report.write.name'), $elaborado));
            $objPHPExcel->getActiveSheet()->setCellValue('A' . ($fila + 1), $cargoElab);

            $objPHPExcel->getActiveSheet()->setCellValue('B' . ($fila + 3), sprintf('%s %s', $this->translator->trans('distribution.report.aprove.name'), $aprobado));
            $objPHPExcel->getActiveSheet()->setCellValue('B' . ($fila + 4), $cargoAprob);

            $objPHPExcel->getActiveSheet()->setCellValue('C' . $fila, sprintf('%s %s', $this->translator->trans('distribution.report.review.name'), $revisado));
            $objPHPExcel->getActiveSheet()->setCellValue('C' . ($fila + 1), $cargoRev);

            $fila += 4;

            $lastCell = strtoupper('H') . $fila;
            $objPHPExcel->getActiveSheet()->getStyle($firstCell . ':' . $lastCell)->applyFromArray($style);

            $fila++;
            $fila++;

        }


        $objPHPExcel->getActiveSheet()->setTitle($this->translator->trans('distribution.label'));

        $writer = call_user_func(array('\PHPExcel_IOFactory', 'createWriter'), $objPHPExcel, 'Excel2007');

        $response = $this->phpexcel->createStreamedResponse($writer);

        $dispositionHeader = $response->headers->makeDisposition(
            ResponseHeaderBag::DISPOSITION_ATTACHMENT, sprintf('%s.%s', $this->translator->trans('distribution.report.label'), 'xlsx')
        );
        $response->headers->set('Content-Type', 'text/vnd.ms-excel; charset=utf-8');
        $response->headers->set('Pragma', 'public');
        $response->headers->set('Cache-Control', 'maxage=1');
        $response->headers->set('Content-Disposition', $dispositionHeader);
        return $response;
    }

}