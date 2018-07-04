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

class ReporteDesagregacionManager extends AbstractManager
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
        $objPHPExcel = $objReader->load(__DIR__ . '/../../Model/ExcelTemplates/plantilla_desagregacion_combustible.xlsx');

        $style = array(
            'font' => array(
                'name' => 'Arial',
                'size' => 11,
            ),
            'alignment' => array(
                'horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
            )
        );
        $style_bold = array(
            'font' => array(
                'bold' => true,
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

        $row = 1;

        $firstRow = 3;
        $firstCell = 'A' . $row;

        $meses = array("Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre");
        $now = new \DateTime();

        $objPHPExcel->getActiveSheet()->setCellValue('E' . $row, isset($mes) ? $meses[$mes->format('n') - 1] . ' I' : $meses[$now->format('n') - 1] . ' I');
        $objPHPExcel->getActiveSheet()->setCellValue('H' . $row, isset($mes) ? $meses[$mes->format('n') - 1] . ' II' : $meses[$now->format('n') - 1] . ' II');
        $objPHPExcel->getActiveSheet()->setCellValue('K' . $row, 'Resumen ' . (isset($mes) ? $meses[$mes->format('n') - 1] : $meses[$now->format('n') - 1]));
        $row += 2;
        $carros = $this->em->getRepository('CombBundle:Carro')->findAll();

        $consecutive = 1;

        foreach ($carros as $carro) {
            $column = 'A';
            $objPHPExcel->getActiveSheet()->setCellValue($column . $row, $consecutive);
            $this->setBorders($objPHPExcel, $column, $row, $borders);
            $column++;
            $consecutive++;

            $objPHPExcel->getActiveSheet()->setCellValue($column . $row, $carro->getMarca());
            $this->setBorders($objPHPExcel, $column, $row, $borders);
            $column++;

            $objPHPExcel->getActiveSheet()->setCellValue($column . $row, $carro->getMatricula());
            $this->setBorders($objPHPExcel, $column, $row, $borders);
            $column++;

            $objPHPExcel->getActiveSheet()->setCellValue($column . $row, $carro->getArea());
            $this->setBorders($objPHPExcel, $column, $row, $borders);
            $column++;

            $tarjeta = $carro->getTarjeta();
            $tarjeta !== null ? $recargues = $this->em->getRepository('CombBundle:Recargue')->report(isset($mes) ? $mes->format('Y-m') : $now->format('Y-m'), $tarjeta) : $recargues = array();

            $totalAsig = 0;
            $totalCam = 0;
            if (count($recargues) > 0) {
                foreach ($recargues as $recargue) {

                    $objPHPExcel->getActiveSheet()->setCellValue($column . $row, $tarjeta);
                    $this->setBorders($objPHPExcel, $column, $row, $borders);
                    $column++;

                    $objPHPExcel->getActiveSheet()->setCellValue($column . $row, $recargue->getDistTrjt()->getAsignacion());
                    $totalAsig += $recargue->getDistTrjt()->getAsignacion();
                    $this->setBorders($objPHPExcel, $column, $row, $borders);
                    $column++;

                    $objPHPExcel->getActiveSheet()->setCellValue($column . $row, $recargue->getDistTrjt()->getAsignacion() * ($carro->getIndcons() !== null ? $carro->getIndcons() : 0));
                    $totalCam += $recargue->getDistTrjt()->getAsignacion() * ($carro->getIndcons() !== null ? $carro->getIndcons() : 0);
                    $this->setBorders($objPHPExcel, $column, $row, $borders);
                    $column++;
                }
                if (count($recargues) < 2) {
                    for ($i = 'H'; $i < 'K'; $i++) {
                        $this->setBorders($objPHPExcel, $i, $row, $borders);
                    }
                    $column++;
                    $column++;
                    $column++;
                }
                $objPHPExcel->getActiveSheet()->setCellValue($column . $row, $totalAsig);
                $this->setBorders($objPHPExcel, $column, $row, $borders);
                $column++;

                $objPHPExcel->getActiveSheet()->setCellValue($column . $row, $totalCam);
                $this->setBorders($objPHPExcel, $column, $row, $borders);
                $column++;

                $servicio = $tarjeta->getServicio();
                $multp = 0;
                if ($servicio->getId() === Servicio::SERVICE_GR)
                    $multp = 0.68;
                if ($servicio->getId() === Servicio::SERVICE_GE)
                    $multp = 0.70;
                if ($servicio->getId() === Servicio::SERVICE_DIESEL)
                    $multp = 0.77;


                $objPHPExcel->getActiveSheet()->setCellValue($column . $row, $totalAsig * $multp);
                $this->setBorders($objPHPExcel, $column, $row, $borders);

            } else {
                for ($i = 'E'; $i < 'N'; $i++) {
                    $this->setBorders($objPHPExcel, $i, $row, $borders);
                }
            }

            $row++;
        }

        for ($x = 'A'; $x < 'F'; $x++) {
            $this->setBorders($objPHPExcel, $x, $row, $borders);
        }
        $objPHPExcel->getActiveSheet()->getStyle('A' . $row . ':' . 'M' . $row)->applyFromArray($style_bold);

        $objPHPExcel->getActiveSheet()->mergeCells('A' . $row . ':E' . $row);
        $objPHPExcel->getActiveSheet()->setCellValue('A' . $row, 'Totales UNISS');

        $objPHPExcel->getActiveSheet()->setCellValue('F' . $row, '=SUM(F' . $firstRow . ':F' . ($row - 1) . ')');
        $this->setBorders($objPHPExcel, 'F', $row, $borders);
        $objPHPExcel->getActiveSheet()->setCellValue('G' . $row, '=SUM(G' . $firstRow . ':G' . ($row - 1) . ')');
        $this->setBorders($objPHPExcel, 'G', $row, $borders);
        $objPHPExcel->getActiveSheet()->setCellValue('I' . $row, '=SUM(I' . $firstRow . ':I' . ($row - 1) . ')');
        $this->setBorders($objPHPExcel, 'I', $row, $borders);
        $objPHPExcel->getActiveSheet()->setCellValue('J' . $row, '=SUM(J' . $firstRow . ':J' . ($row - 1) . ')');
        $this->setBorders($objPHPExcel, 'J', $row, $borders);
        $objPHPExcel->getActiveSheet()->setCellValue('K' . $row, '=SUM(K' . $firstRow . ':K' . ($row - 1) . ')');
        $this->setBorders($objPHPExcel, 'K', $row, $borders);
        $objPHPExcel->getActiveSheet()->setCellValue('L' . $row, '=SUM(L' . $firstRow . ':L' . ($row - 1) . ')');
        $this->setBorders($objPHPExcel, 'L', $row, $borders);
        $objPHPExcel->getActiveSheet()->setCellValue('M' . $row, '=SUM(M' . $firstRow . ':M' . ($row - 1) . ')');
        $this->setBorders($objPHPExcel, 'M', $row, $borders);

        $lastCell = 'M' . $row;
        $objPHPExcel->getActiveSheet()->getStyle($firstCell . ':' . $lastCell)->applyFromArray($style);

        $objPHPExcel->getActiveSheet()->setTitle($this->translator->trans('fuel.report.label'));

        $writer = call_user_func(array('\PHPExcel_IOFactory', 'createWriter'), $objPHPExcel, 'Excel2007');

        $response = $this->phpexcel->createStreamedResponse($writer);

        $dispositionHeader = $response->headers->makeDisposition(
            ResponseHeaderBag::DISPOSITION_ATTACHMENT, sprintf('%s.%s', $this->translator->trans('fuel.report.by.car'), 'xlsx')
        );
        $response->headers->set('Content-Type', 'text/vnd.ms-excel; charset=utf-8');
        $response->headers->set('Pragma', 'public');
        $response->headers->set('Cache-Control', 'maxage=1');
        $response->headers->set('Content-Disposition', $dispositionHeader);
        return $response;
    }
}