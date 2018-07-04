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

class ReporteCombAnualManager extends AbstractManager
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
     * @return \Symfony\Component\HttpFoundation\StreamedResponse
     * @throws \PHPExcel_Exception
     * @throws \PHPExcel_Reader_Exception
     */
    public function designReport()
    {
        $objReader = \PHPExcel_IOFactory::createReader('Excel2007');
        $objPHPExcel = $objReader->load(__DIR__ . '/../../Model/ExcelTemplates/plantilla_anual_combustible.xlsx');

        $style = array(
            'font' => array(
                'name' => 'Arial',
                'size' => 11,
                'bold' => false,
            ),
            'alignment' => array(
                'horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
            )
        );
        $style_bold = array(
            'font' => array(
                'name' => 'Arial',
                'size' => 12,
                'bold' => true,
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

        $row = 1;

        $meses = array("Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre");
        $servicios = $this->em->getRepository('NomencladorBundle:Servicio')->findAll();
        foreach ($servicios as $servicio) {

            $firstCell = 'A' . $row;
            $objPHPExcel->getActiveSheet()->mergeCells('A' . $row . ':B' . $row);
            $objPHPExcel->getActiveSheet()->setCellValue('A' . $row, strtoupper($servicio->getValor()));

            $col = 'B';
            for ($i = 0; $i < 25; $i++) {
                $col++;
                $this->setBorders($objPHPExcel, $col, $row, $borders);
            }
            for ($x = 'A'; $x < 'M'; $x++) {
                $this->setBorders($objPHPExcel, 'A' . $x, $row, $borders);
            }
            $start = 'C';
            $end = 'E';
            $alternativa = $meses;
            $alternativa[] = "Total";
            for ($y = 0; $y < 13; $y++) {
                $objPHPExcel->getActiveSheet()->mergeCells($start . $row . ':' . $end . $row);
                $objPHPExcel->getActiveSheet()->setCellValue($start . $row, strtoupper($alternativa[$y]));
                $start++;
                $start++;
                $start++;
                $end++;
                $end++;
                $end++;
            }
            $row++;

            $objPHPExcel->getActiveSheet()->setCellValue('A' . $row, 'Marca');
            $this->setBorders($objPHPExcel, 'A', $row, $borders);
            $objPHPExcel->getActiveSheet()->setCellValue('B' . $row, 'Carro');
            $this->setBorders($objPHPExcel, 'B', $row, $borders);
            $start = 'C';
            for ($n = 0; $n < 13; $n++) {
                $objPHPExcel->getActiveSheet()->setCellValue($start . $row, 'Litros');
                $this->setBorders($objPHPExcel, $start, $row, $borders);
                $start++;
                $objPHPExcel->getActiveSheet()->setCellValue($start . $row, 'Km/L');
                $this->setBorders($objPHPExcel, $start, $row, $borders);
                $start++;
                $objPHPExcel->getActiveSheet()->setCellValue($start . $row, 'Km');
                $this->setBorders($objPHPExcel, $start, $row, $borders);
                $start++;
            }
            $lastCell = 'AO' . $row;
            $objPHPExcel->getActiveSheet()->getStyle($firstCell . ':' . $lastCell)->applyFromArray($style_bold);

            $row++;

            $tarjetasXServicio = $this->em->getRepository('CombBundle:Tarjeta')->findBy(array('servicio' => $servicio));

            $firstCell = 'A' . $row;
            $firstRow = $row;
            if (count($tarjetasXServicio) > 0) {
                foreach ($tarjetasXServicio as $tjt) {
                    $col = 'A';
                    $carro = $tjt->getCarros()->first();
                    if ($carro) {
                        $sumtoriaAsignacines = 0;
                        $sumtoriaKm = 0;
                        $objPHPExcel->getActiveSheet()->setCellValue($col . $row, $carro ? $carro->getMarca() : '');
                        $this->setBorders($objPHPExcel, $col, $row, $borders);
                        $col++;
                        $objPHPExcel->getActiveSheet()->setCellValue($col . $row, $carro ? $carro->getMatricula() : '');
                        $this->setBorders($objPHPExcel, $col, $row, $borders);
                        $col++;

                        foreach ($meses as $key => $mes) {
                            $recargues = $this->em->getRepository('CombBundle:Recargue')->report(date('Y') . '-' . ($key + 1), $tjt);
                            if (count($recargues) > 0) {
                                $asignaciones = 0;
                                foreach ($recargues as $recargue) {
                                    $asignaciones += $recargue->getDistTrjt()->getAsignacion();
                                }
                                $sumtoriaAsignacines += $asignaciones;
                                $objPHPExcel->getActiveSheet()->setCellValue($col . $row, $asignaciones);
                                $this->setBorders($objPHPExcel, $col, $row, $borders);
                                $col++;
                                $indCons = $carro ? ($carro->getIndcons() !== null ? $carro->getIndcons() : 0) : '';
                                $objPHPExcel->getActiveSheet()->setCellValue($col . $row, $indCons);
                                $this->setBorders($objPHPExcel, $col, $row, $borders);
                                $col++;
                                $km = $indCons !== '' ? ($asignaciones * $indCons) : '';
                                $objPHPExcel->getActiveSheet()->setCellValue($col . $row, $km);
                                $sumtoriaKm += $km;
                                $this->setBorders($objPHPExcel, $col, $row, $borders);
                                $col++;
                            } else {
                                for ($i = 0; $i < 3; $i++) {
                                    $objPHPExcel->getActiveSheet()->setCellValue($col . $row, '');
                                    $this->setBorders($objPHPExcel, $col, $row, $borders);
                                    $col++;
                                }
                            }
                        }
                        $this->setBorders($objPHPExcel, 'AM', $row, $borders);
                        $this->setBorders($objPHPExcel, 'AN', $row, $borders);
                        $this->setBorders($objPHPExcel, 'AO', $row, $borders);

                        $objPHPExcel->getActiveSheet()->setCellValue('AM' . $row, $sumtoriaAsignacines);
                        $objPHPExcel->getActiveSheet()->setCellValue('AO' . $row, $sumtoriaKm);
                        $objPHPExcel->getActiveSheet()->setCellValue('AN' . $row, $sumtoriaKm !== 0 || $sumtoriaAsignacines !== 0 ? $sumtoriaKm / $sumtoriaAsignacines : 0);

                        $row++;

                    }
                }
            }

            $this->setBorders($objPHPExcel, 'A', $row, $borders);
            $this->setBorders($objPHPExcel, 'B', $row, $borders);
            $objPHPExcel->getActiveSheet()->mergeCells('A' . $row . ':B' . $row);
            $objPHPExcel->getActiveSheet()->setCellValue('A' . $row, 'Totales');

            $columna = 'C';
            for ($i = 0; $i < 25; $i++) {
                $objPHPExcel->getActiveSheet()->setCellValue($columna . $row, '=SUM(' . $columna . $firstRow . ':' . $columna . ($row - 1) . ')');
                $this->setBorders($objPHPExcel, $columna, $row, $borders);
                $columna++;
            }
            for ($x = 'A'; $x < 'P'; $x++) {
                $objPHPExcel->getActiveSheet()->setCellValue('A' . $x . $row, '=SUM(A' . $x . $firstRow . ':A' . $x . ($row - 1) . ')');
                $this->setBorders($objPHPExcel, 'A' . $x, $row, $borders);
            }

            $row++;

            $lastCell = 'AO' . $row;
            $objPHPExcel->getActiveSheet()->getStyle($firstCell . ':' . $lastCell)->applyFromArray($style);


            $row++;
            $row++;
        }

        $objPHPExcel->getActiveSheet()->setTitle($this->translator->trans('fuel.report.label'));

        $writer = call_user_func(array('\PHPExcel_IOFactory', 'createWriter'), $objPHPExcel, 'Excel2007');

        $response = $this->phpexcel->createStreamedResponse($writer);

        $dispositionHeader = $response->headers->makeDisposition(
            ResponseHeaderBag::DISPOSITION_ATTACHMENT, sprintf('%s.%s', $this->translator->trans('fuel.report.year'), 'xlsx')
        );
        $response->headers->set('Content-Type', 'text/vnd.ms-excel; charset=utf-8');
        $response->headers->set('Pragma', 'public');
        $response->headers->set('Cache-Control', 'maxage=1');
        $response->headers->set('Content-Disposition', $dispositionHeader);
        return $response;
    }
}