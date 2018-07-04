<?php

namespace CombBundle\Manager\Report;

use AppBundle\Manager\AbstractManager;
use Doctrine\ORM\EntityManager;
use AppBundle\Exceptions\ErrorHandler;
use Liuggio\ExcelBundle\Factory;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Translation\DataCollectorTranslator;

class ReporteVehiculoManager extends AbstractManager
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
        $area = $parameters['area'];

        $objReader = \PHPExcel_IOFactory::createReader('Excel2007');
        $objPHPExcel = $objReader->load(__DIR__ . '/../../Model/ExcelTemplates/plantilla_vehiculo.xlsx');

        $style = array(
            'font' => array(
                'name' => 'Arial',
                'size' => 11,
            ),
            'alignment' => array(
                'horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
            )
        );
        $style_color = array(
            'font' => array(
                'color' => array(
                    'rgb' => 'fb0505'
                ),
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

        isset($area)
            ? $vehiculos = $this->em->getRepository('CombBundle:Carro')->findBy(
            array(
                'area' => $area
            ))
            : $vehiculos = $this->em->getRepository('CombBundle:Carro')->findAll();

        if (count($vehiculos) > 0) {
            $consecutive = 1;
            $row = 3;
            foreach ($vehiculos as $key => $vehiculo) {
                $column = 'A';
                $objPHPExcel->getActiveSheet()->setCellValue($column . $row, $consecutive);
                $this->setBorders($objPHPExcel, $column, $row, $borders);
                $consecutive++;
                $column++;
                $objPHPExcel->getActiveSheet()->setCellValue($column . $row, $vehiculo->getMarca());
                $this->setBorders($objPHPExcel, $column, $row, $borders);
                $column++;
                $objPHPExcel->getActiveSheet()->setCellValue($column . $row, $vehiculo->getMatricula());
                $this->setBorders($objPHPExcel, $column, $row, $borders);
                $column++;
                $objPHPExcel->getActiveSheet()->setCellValue($column . $row, $vehiculo->getArea());
                $this->setBorders($objPHPExcel, $column, $row, $borders);
                $column++;
                $objPHPExcel->getActiveSheet()->setCellValue($column . $row, $vehiculo->getChofer());
                $this->setBorders($objPHPExcel, $column, $row, $borders);
                $column++;
                $objPHPExcel->getActiveSheet()->setCellValue($column . $row, $vehiculo->getIndcons());
                $this->setBorders($objPHPExcel, $column, $row, $borders);
                $column++;
                $now = new \DateTime();
                $color = false;
                $vehiculo->getInsptecn() !== null
                    ? ($vehiculo->getInsptecn()->format('dmY') > $now->format('dmY')
                    ? ''
                    : $color = true)
                    : $color = true;
                if ($color) {
                    $objPHPExcel->getActiveSheet()->setCellValue($column . $row, strtoupper($this->translator->trans('car.report.deprecated')));
                    $objPHPExcel->getActiveSheet()->getStyle($column . $row)->applyFromArray($style_color);
                } else {
                    $objPHPExcel->getActiveSheet()->setCellValue($column . $row, $vehiculo->getInsptecn()->format('d/m/Y'));
                }
                $this->setBorders($objPHPExcel, $column, $row, $borders);

                $key + 1 < count($vehiculos) ? $row++ : '';
            }
            $lastCell = strtoupper($column) . $row;

            $objPHPExcel->getActiveSheet()->getStyle('A3:' . $lastCell)->applyFromArray($style);
        }

        $objPHPExcel->getActiveSheet()->setTitle($this->translator->trans('car.label'));

        $writer = call_user_func(array('\PHPExcel_IOFactory', 'createWriter'), $objPHPExcel, 'Excel2007');

        $response = $this->phpexcel->createStreamedResponse($writer);

        $dispositionHeader = $response->headers->makeDisposition(
            ResponseHeaderBag::DISPOSITION_ATTACHMENT, sprintf('%s.%s', $this->translator->transChoice('car.report.label', 1), 'xlsx')
        );
        $response->headers->set('Content-Type', 'text/vnd.ms-excel; charset=utf-8');
        $response->headers->set('Pragma', 'public');
        $response->headers->set('Cache-Control', 'maxage=1');
        $response->headers->set('Content-Disposition', $dispositionHeader);
        return $response;
    }

}