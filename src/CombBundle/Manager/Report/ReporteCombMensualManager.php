<?php

namespace CombBundle\Manager\Report;

use AppBundle\Manager\AbstractManager;
use Doctrine\ORM\EntityManager;
use AppBundle\Exceptions\ErrorHandler;
use Liuggio\ExcelBundle\Factory;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Translation\DataCollectorTranslator;

class ReporteCombMensualManager extends AbstractManager
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
        $objPHPExcel = $objReader->load(__DIR__ . '/../../Model/ExcelTemplates/plantilla_mensual_combustible.xlsx');

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
                'bold' => true
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
        $firstCell = 'A' . $row;
        $objPHPExcel->getActiveSheet()->mergeCells('A' . $row . ':' . 'J' . $row);

        $objPHPExcel->getActiveSheet()->setCellValue('A' . $row, 'CONTROL COMBUSTIBLE, KMS REC, ÍNDICE DE CONSUMO, VIAJES Y PASAJEROS MENSUAL');
        $objPHPExcel->getActiveSheet()->getStyle()->applyFromArray($style_bold);

        $row++;
        $objPHPExcel->getActiveSheet()->setCellValue('A' . $row, 'MES:');
        $objPHPExcel->getActiveSheet()->getStyle('A' . $row)->applyFromArray($style_bold);

        $now = new \DateTime();
        $objPHPExcel->getActiveSheet()->setCellValue('B' . $row, isset($mes) ? $mes->format('m/Y') : $now->format('m/Y'));

        $row += 2;

        $servicios = $this->em->getRepository('NomencladorBundle:Servicio')->findAll();
        foreach ($servicios as $servicio) {
            $totalesXColumnas = array('C' => 0, 'D' => 0, 'E' => 0, 'F' => 0, 'G' => 0, 'H' => 0, 'I' => 0, 'J' => 0);
            $tarjetas = $this->em->getRepository('CombBundle:Tarjeta')->report(isset($mes) ? $mes->format('Y-m') : $now->format('Y-m'), $servicio);
            if (count($tarjetas) > 0) {
                $strings = array('Vehículos', 'Tipo Veh.', 'Comb. Tanq.', 'Comb. Abas.', 'Comb. Con', 'Kms Rec.', 'Índice Con.', 'Cant Viajes', 'Pers. Transp.', 'Comb. Tanque');
                $inc = 0;
                for ($i = 'A'; $i < 'K'; $i++) {
                    $text = $strings[$inc];
                    if ($inc === 0) {
                        $text = $text . ' ' . $servicio->getValor();
                    }
                    $objPHPExcel->getActiveSheet()->setCellValue($i . $row, $text)->getStyle($i . $row)->applyFromArray($style_bold);

                    $this->setBorders($objPHPExcel, $i, $row, $borders);
                    $inc++;
                }
                $row++;

                foreach ($tarjetas as $tjt) {

                    $recargues = $this->em->getRepository('CombBundle:Recargue')->report(isset($mes) ? $mes->format('Y-m') : $now->format('Y-m'), $tjt);
                    if (count($recargues) > 0) {
                        $col = 'A';
                        $carro = $tjt->getCarros()->first();
                        $objPHPExcel->getActiveSheet()->setCellValue($col . $row, $carro ? $carro->getMatricula() : '');
                        $this->setBorders($objPHPExcel, $col, $row, $borders);
                        $col++;

                        $objPHPExcel->getActiveSheet()->setCellValue($col . $row, $carro ? $carro->getMarca() : '');
                        $this->setBorders($objPHPExcel, $col, $row, $borders);
                        $col++;

                        $rec = $recargues[count($recargues) - 1];
                        $objPHPExcel->getActiveSheet()->setCellValue($col . $row, $rec->getSaldoAlRecargar());
                        $totalesXColumnas[$col] += $rec->getSaldoAlRecargar();
                        $this->setBorders($objPHPExcel, $col, $row, $borders);
                        $col++;

                        $asignado = $rec->getDistTrjt()->getAsignacion();
                        $objPHPExcel->getActiveSheet()->setCellValue($col . $row, $asignado);
                        $totalesXColumnas[$col] += $asignado;
                        $this->setBorders($objPHPExcel, $col, $row, $borders);
                        $col++;

                        $actual = $tjt->getSaldoFinal();
                        $consumido = $rec->getSaldoAlRecargar() + $asignado - $actual;
                        $objPHPExcel->getActiveSheet()->setCellValue($col . $row, $consumido);
                        $totalesXColumnas[$col] += $consumido;
                        $this->setBorders($objPHPExcel, $col, $row, $borders);
                        $col++;

                        $objPHPExcel->getActiveSheet()->setCellValue($col . $row, $consumido * ($carro ? $carro->getIndcons() : 0));
                        $totalesXColumnas[$col] += ($consumido * ($carro ? $carro->getIndcons() : 0));
                        $this->setBorders($objPHPExcel, $col, $row, $borders);
                        $col++;

                        $objPHPExcel->getActiveSheet()->setCellValue($col . $row, ($carro ? $carro->getIndcons() : 0));
                        $totalesXColumnas[$col] += ($carro ? $carro->getIndcons() : 0);
                        $this->setBorders($objPHPExcel, $col, $row, $borders);
                        $col++;

                        $solicitudes = $rec->getDistTrjt()->getSolicitudes()->toArray();
                        $objPHPExcel->getActiveSheet()->setCellValue($col . $row, count($solicitudes));
                        $totalesXColumnas[$col] += count($solicitudes);
                        $this->setBorders($objPHPExcel, $col, $row, $borders);
                        $col++;

                        $viajeros = 0;
                        foreach ($solicitudes as $solicitud) {
                            $viajeros += $solicitud->getCantpersona();
                        }
                        $objPHPExcel->getActiveSheet()->setCellValue($col . $row, $viajeros);
                        $totalesXColumnas[$col] += $viajeros;
                        $this->setBorders($objPHPExcel, $col, $row, $borders);
                        $col++;

                        $objPHPExcel->getActiveSheet()->setCellValue($col . $row, $actual);
                        $totalesXColumnas[$col] += $actual;
                        $this->setBorders($objPHPExcel, $col, $row, $borders);

                    }
                    $row++;

                }

                $this->setBorders($objPHPExcel, 'A', $row, $borders);
                $this->setBorders($objPHPExcel, 'B', $row, $borders);
                $objPHPExcel->getActiveSheet()->mergeCells('A' . $row . ':B' . $row);
                $objPHPExcel->getActiveSheet()->setCellValue('A' . $row, 'TOTALES');
                $columna = 'C';
                foreach ($totalesXColumnas as $totalesXColumna) {
                    $objPHPExcel->getActiveSheet()->setCellValue($columna . $row, $totalesXColumna);
                    $this->setBorders($objPHPExcel, $columna, $row, $borders);
                    $columna++;
                }

                $row += 2;
            }
        }
        $lastCell = 'J' . $row;
        $objPHPExcel->getActiveSheet()->getStyle($firstCell . ':' . $lastCell)->applyFromArray($style);

        $objPHPExcel->getActiveSheet()->setTitle($this->translator->trans('fuel.report.label'));

        $writer = call_user_func(array('\PHPExcel_IOFactory', 'createWriter'), $objPHPExcel, 'Excel2007');

        $response = $this->phpexcel->createStreamedResponse($writer);

        $dispositionHeader = $response->headers->makeDisposition(
            ResponseHeaderBag::DISPOSITION_ATTACHMENT, sprintf('%s.%s', $this->translator->trans('fuel.report.monthly'), 'xlsx')
        );
        $response->headers->set('Content-Type', 'text/vnd.ms-excel; charset=utf-8');
        $response->headers->set('Pragma', 'public');
        $response->headers->set('Cache-Control', 'maxage=1');
        $response->headers->set('Content-Disposition', $dispositionHeader);
        return $response;
    }
}