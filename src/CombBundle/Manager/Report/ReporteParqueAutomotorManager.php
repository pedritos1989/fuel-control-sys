<?php

namespace CombBundle\Manager\Report;

use AppBundle\Manager\AbstractManager;
use Doctrine\ORM\EntityManager;
use AppBundle\Exceptions\ErrorHandler;
use Liuggio\ExcelBundle\Factory;
use NomencladorBundle\Entity\EstadoCarro;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Translation\DataCollectorTranslator;

class ReporteParqueAutomotorManager extends AbstractManager
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
     * @return array
     */
    public function getEstados()
    {
        $estados = array(EstadoCarro::STATUS_OK, EstadoCarro::STATUS_BREAK, EstadoCarro::STATUS_OUT);
        return $estados;
    }

    /**
     * @return \Symfony\Component\HttpFoundation\StreamedResponse
     * @throws \InvalidArgumentException
     * @throws \PHPExcel_Exception
     * @throws \PHPExcel_Reader_Exception
     */
    public function designReport()
    {
        $objReader = \PHPExcel_IOFactory::createReader('Excel2007');
        $objPHPExcel = $objReader->load(__DIR__ . '/../../Model/ExcelTemplates/plantilla_parque_automotor.xlsx');

        $style = array(
            'font' => array(
                'name' => 'Arial',
                'size' => 11,
            ),
            'alignment' => array(
                'horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
            )
        );
        $style_2 = array(
            'font' => array(
                'bold' => true,
            ),
            'fill' => array(
                'type' => \PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array(
                    'rgb' => 'ffc800'
                ),
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

        $autoIncremental = 1;
        $fila = 2;
        $firstCell = 'A' . $fila;
        $tipos = $this->em->getRepository('NomencladorBundle:TipoCarro')->findAll();
        foreach ($tipos as $tipo) {
            $columna = 'A';
            if ($tipo->getCarros()->count() > 0) {
                $objPHPExcel->getActiveSheet()->setCellValue($columna . $fila, $autoIncremental);
                $this->setBorders($objPHPExcel, $columna, $fila, $borders);
                $columna++;
                $objPHPExcel->getActiveSheet()->setCellValue($columna . $fila, $tipo->getValor());
                $this->setBorders($objPHPExcel, $columna, $fila, $borders);
                $columna++;
                $objPHPExcel->getActiveSheet()->setCellValue($columna . $fila, $tipo->getCarros()->count());
                $this->setBorders($objPHPExcel, $columna, $fila, $borders);
                $columna++;

                $totalesXEstado = array();
                foreach ($this->getEstados() as $estado) {
                    $totalesXEstado[$estado] = $this->em->getRepository('CombBundle:Carro')->findBy(array(
                        'tipo' => $tipo,
                        'estado' => $this->em->getRepository('NomencladorBundle:EstadoCarro')->find($estado)
                    ));
                }
                $objPHPExcel->getActiveSheet()->setCellValue($columna . $fila, isset($totalesXEstado[EstadoCarro::STATUS_OK][0]) ? count($totalesXEstado[EstadoCarro::STATUS_OK]) : 0);
                $this->setBorders($objPHPExcel, $columna, $fila, $borders);
                $columna++;
                $objPHPExcel->getActiveSheet()->setCellValue($columna . $fila, isset($totalesXEstado[EstadoCarro::STATUS_BREAK][0]) ? count($totalesXEstado[EstadoCarro::STATUS_BREAK]) : 0);
                $this->setBorders($objPHPExcel, $columna, $fila, $borders);
                $columna++;
                $objPHPExcel->getActiveSheet()->setCellValue($columna . $fila, isset($totalesXEstado[EstadoCarro::STATUS_OUT][0]) ? count($totalesXEstado[EstadoCarro::STATUS_OUT]) : 0);
                $this->setBorders($objPHPExcel, $columna, $fila, $borders);
                $columna++;

                $wizard = new \PHPExcel_Helper_HTML();

                $bajas = null;
                count($totalesXEstado[EstadoCarro::STATUS_OUT]) > 0 ? $bajas = $totalesXEstado[EstadoCarro::STATUS_OUT] : '';
                if ($bajas !== null) {
                    $string = "<b>Son baja(s): </b>";
                    $stringBaja = $wizard->toRichTextObject($string);
                    foreach ($bajas as $baja) {
                        $stringBaja .= $baja->getMatricula() . ', ';
                    }
                }

                $rotos = null;
                count($totalesXEstado[EstadoCarro::STATUS_BREAK]) > 0 ? $rotos = $totalesXEstado[EstadoCarro::STATUS_BREAK] : '';
                if ($rotos !== null) {
                    $string = "<b>Roto(s): </b>";
                    $stringRoto = $wizard->toRichTextObject($string);
                    foreach ($rotos as $roto) {
                        $stringRoto .= $roto->getMatricula() . ', ';
                    }

                }
                $objPHPExcel->getActiveSheet()->setCellValue($columna . $fila, ($bajas !== null ? $stringBaja : '') . ($rotos !== null ? $stringRoto : ''));

                $this->setBorders($objPHPExcel, $columna, $fila, $borders);

            }
            $fila++;
            $autoIncremental++;
        }

        $objPHPExcel->getActiveSheet()->setCellValue('A' . $fila);
        $this->setBorders($objPHPExcel, 'A', $fila, $borders);

        $objPHPExcel->getActiveSheet()->setCellValue('B' . $fila, 'TOTAL');
        $this->setBorders($objPHPExcel, 'B', $fila, $borders);

        for ($i = 'C'; $i < 'G'; $i++) {
            $objPHPExcel->getActiveSheet()->setCellValue($i . $fila, '=SUM(' . $i . '2:' . $i . ($fila - 1) . ')');
            $this->setBorders($objPHPExcel, $i, $fila, $borders);
        }
        $objPHPExcel->getActiveSheet()->setCellValue('G' . $fila);
        $this->setBorders($objPHPExcel, 'G', $fila, $borders);

        $objPHPExcel->getActiveSheet()->getStyle('A' . $fila . ':G' . $fila)->applyFromArray($style_2);

        $fila++;

        $lastCell = strtoupper($columna) . $fila;

        $objPHPExcel->getActiveSheet()->getStyle($firstCell . ':' . $lastCell)->applyFromArray($style);

        $writer = call_user_func(array('\PHPExcel_IOFactory', 'createWriter'), $objPHPExcel, 'Excel2007');

        $response = $this->phpexcel->createStreamedResponse($writer);

        $dispositionHeader = $response->headers->makeDisposition(
            ResponseHeaderBag::DISPOSITION_ATTACHMENT, sprintf('%s.%s', $this->translator->trans('car.report.status.label'), 'xlsx')
        );
        $response->headers->set('Content-Type', 'text/vnd.ms-excel; charset=utf-8');
        $response->headers->set('Pragma', 'public');
        $response->headers->set('Cache-Control', 'maxage=1');
        $response->headers->set('Content-Disposition', $dispositionHeader);
        return $response;
    }

}