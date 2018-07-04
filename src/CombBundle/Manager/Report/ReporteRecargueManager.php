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

class ReporteRecargueManager extends AbstractManager
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
        $objReader = \PHPExcel_IOFactory::createReader('Excel2007');
        $objPHPExcel = $objReader->load(__DIR__ . '/../../Model/ExcelTemplates/plantilla_solicitud_recargue.xlsx');

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

        $objPHPExcel->getActiveSheet()->setCellValue('B3', date('d'));
        $objPHPExcel->getActiveSheet()->setCellValue('B4', isset($mes) ? $mes->format('m/Y') : '');

        $operativos = $this->em->getRepository('CombBundle:AsignacionMensual')->report(isset($mes) ? $mes->format('Y-m') : '');

        $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setAutoSize(true);

        $fila = 7;
        $firstCell = 'A' . $fila;
        foreach ($operativos as $opr) {
            $ge = 0;
            $gr = 0;
            $diesel = 0;
            $ctdades = $opr->getCantidades()->toArray();
            foreach ($ctdades as $ctdad) {
                $ctdad->getServicio()->getId() === Servicio::SERVICE_GE
                    ? $ge += $ctdad->getCantidad()
                    : '';
                $ctdad->getServicio()->getId() === Servicio::SERVICE_GR
                    ? $gr += $ctdad->getCantidad()
                    : '';
                $ctdad->getServicio()->getId() === Servicio::SERVICE_DIESEL
                    ? $diesel += $ctdad->getCantidad()
                    : '';
            }
            $objPHPExcel->getActiveSheet()->setCellValue('A' . $fila, 'Diésel');
            $this->setBorders($objPHPExcel, 'A', $fila, $borders);
            $objPHPExcel->getActiveSheet()->setCellValue('B' . $fila, $diesel);
            $this->setBorders($objPHPExcel, 'B', $fila, $borders);
            $objPHPExcel->getActiveSheet()->setCellValue('C' . $fila, ($diesel * 0.77));
            $this->setBorders($objPHPExcel, 'C', $fila, $borders);
            $objPHPExcel->getActiveSheet()->setCellValue('D' . $fila, $opr);
            $this->setBorders($objPHPExcel, 'D', $fila, $borders);
            $fila++;
            $objPHPExcel->getActiveSheet()->setCellValue('A' . $fila, 'Gasolina regular');
            $this->setBorders($objPHPExcel, 'A', $fila, $borders);
            $objPHPExcel->getActiveSheet()->setCellValue('B' . $fila, $gr);
            $this->setBorders($objPHPExcel, 'B', $fila, $borders);
            $objPHPExcel->getActiveSheet()->setCellValue('C' . $fila, ($gr * 0.68));
            $this->setBorders($objPHPExcel, 'C', $fila, $borders);
            $objPHPExcel->getActiveSheet()->setCellValue('D' . $fila, $opr);
            $this->setBorders($objPHPExcel, 'D', $fila, $borders);
            $fila++;
            $objPHPExcel->getActiveSheet()->setCellValue('A' . $fila, 'Gasolina');
            $this->setBorders($objPHPExcel, 'A', $fila, $borders);
            $objPHPExcel->getActiveSheet()->setCellValue('B' . $fila, $ge);
            $this->setBorders($objPHPExcel, 'B', $fila, $borders);
            $objPHPExcel->getActiveSheet()->setCellValue('C' . $fila, ($ge * 0.70));
            $this->setBorders($objPHPExcel, 'C', $fila, $borders);
            $objPHPExcel->getActiveSheet()->setCellValue('D' . $fila, $opr);
            $this->setBorders($objPHPExcel, 'D', $fila, $borders);
            $fila++;
        }
        $lastCell = 'E' . $fila;
        $objPHPExcel->getActiveSheet()->getStyle($firstCell . ':' . $lastCell)->applyFromArray($style);

        $fila += 1;

        $headerColumns = array('TARJETA', 'RESPONSABLE', 'ÁREA', 'MATRÍCULA', 'SERVICIO', 'S.INC', 'ABAST', 'S.FIN', 'FIRMA');
        $x = 0;
        for ($i = 'A'; $i < 'J'; $i++) {
            $objPHPExcel->getActiveSheet()->setCellValue($i . $fila, $headerColumns[$x]);
            $this->setBorders($objPHPExcel, $i, $fila, $borders);
            $x++;
        }
        $fila++;

        $recargues = $this->em->getRepository('CombBundle:Recargue')->report(isset($mes) ? $mes->format('Y-m') : '');
        $firstCell = 'A' . $fila;
        foreach ($recargues as $rchrg) {
            $col = 'A';
            $objPHPExcel->getActiveSheet()->setCellValue($col . $fila, $rchrg->getTarjeta());
            $this->setBorders($objPHPExcel, $col, $fila, $borders);
            $col++;
            $objPHPExcel->getActiveSheet()->setCellValue($col . $fila, $rchrg->getResponsable());
            $this->setBorders($objPHPExcel, $col, $fila, $borders);
            $col++;
            $objPHPExcel->getActiveSheet()->setCellValue($col . $fila, $rchrg->getTarjeta()->getArea());
            $this->setBorders($objPHPExcel, $col, $fila, $borders);
            $col++;
            $carro = $rchrg->getTarjeta()->getCarros()->first();
            $objPHPExcel->getActiveSheet()->setCellValue($col . $fila, $carro ? $carro : '');
            $this->setBorders($objPHPExcel, $col, $fila, $borders);
            $col++;
            $objPHPExcel->getActiveSheet()->setCellValue($col . $fila, $rchrg->getTarjeta()->getServicio());
            $this->setBorders($objPHPExcel, $col, $fila, $borders);
            $col++;
            $objPHPExcel->getActiveSheet()->setCellValue($col . $fila, $rchrg->getSaldoAlRecargar());
            $this->setBorders($objPHPExcel, $col, $fila, $borders);
            $col++;
            $objPHPExcel->getActiveSheet()->setCellValue($col . $fila, $rchrg->getDistTrjt()->getAsignacion());
            $this->setBorders($objPHPExcel, $col, $fila, $borders);
            $col++;
            $objPHPExcel->getActiveSheet()->setCellValue($col . $fila, $rchrg->getSaldoDespRecarga());
            $this->setBorders($objPHPExcel, $col, $fila, $borders);
            $col++;
            $objPHPExcel->getActiveSheet()->setCellValue($col . $fila, '');
            $this->setBorders($objPHPExcel, $col, $fila, $borders);
            $fila++;
        }
        $lastCell = $col . $fila;
        $objPHPExcel->getActiveSheet()->getStyle($firstCell . ':' . $lastCell)->applyFromArray($style);

        $objPHPExcel->getActiveSheet()->setTitle($this->translator->trans('request.card.label'));

        $writer = call_user_func(array('\PHPExcel_IOFactory', 'createWriter'), $objPHPExcel, 'Excel2007');

        $response = $this->phpexcel->createStreamedResponse($writer);

        $dispositionHeader = $response->headers->makeDisposition(
            ResponseHeaderBag::DISPOSITION_ATTACHMENT, sprintf('%s.%s', $this->translator->trans('request.card.report.label'), 'xlsx')
        );
        $response->headers->set('Content-Type', 'text/vnd.ms-excel; charset=utf-8');
        $response->headers->set('Pragma', 'public');
        $response->headers->set('Cache-Control', 'maxage=1');
        $response->headers->set('Content-Disposition', $dispositionHeader);
        return $response;
    }
}