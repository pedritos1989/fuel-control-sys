<?php

namespace CombBundle\Manager\Report;

use AppBundle\Manager\AbstractManager;
use Doctrine\ORM\EntityManager;
use AppBundle\Exceptions\ErrorHandler;
use Liuggio\ExcelBundle\Factory;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Translation\DataCollectorTranslator;

class ReporteSolicitudManager extends AbstractManager
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
        $mes = $parameters['mes'];
        $objReader = \PHPExcel_IOFactory::createReader('Excel5');
        $objPHPExcel = $objReader->load(__DIR__ . '/../../Model/ExcelTemplates/plantilla_solicitud_transporte.xls');

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

        $objPHPExcel->getActiveSheet()->setCellValue('D4', $area);

        $objPHPExcel->getActiveSheet()->setCellValue('D5', isset($mes) ? $mes->format('m/Y') : '');

        $solicitudes = $this->em->getRepository('CombBundle:Solicitud')->report(isset($area) ? $area->getId() : '', isset($mes) ? $mes->format('Y-m') : '');

        if (count($solicitudes) > 0) {
            $row = 9;
            foreach ($solicitudes as $key => $solicitud) {
                $column = 'c';
                $objPHPExcel->getActiveSheet()->setCellValue(strtoupper($column) . $row, $solicitud->getFechaHoraS()->format('d/m/Y H:i'));
                $this->setBorders($objPHPExcel, $column, $row, $borders);
                $column++;
                $objPHPExcel->getActiveSheet()->setCellValue(strtoupper($column) . $row, $solicitud->getLugar());
                $this->setBorders($objPHPExcel, $column, $row, $borders);
                $column++;
                $objPHPExcel->getActiveSheet()->setCellValue(strtoupper($column) . $row, $solicitud->getMotivo());
                $this->setBorders($objPHPExcel, $column, $row, $borders);
                $column++;
                $objPHPExcel->getActiveSheet()->setCellValue(strtoupper($column) . $row, $solicitud->getCantpersona());
                $this->setBorders($objPHPExcel, $column, $row, $borders);
                $column++;
                $objPHPExcel->getActiveSheet()->setCellValue(strtoupper($column) . $row, $solicitud->getFechaHoraR()->format('d/m/Y H:i'));
                $this->setBorders($objPHPExcel, $column, $row, $borders);

                $key + 1 < count($solicitudes) ? $row++ : '';
            }
            $lastCell = strtoupper($column) . $row;

            $objPHPExcel->getActiveSheet()->getStyle('C9:' . $lastCell)->applyFromArray($style);
        }

        $objPHPExcel->getActiveSheet()->setTitle($this->translator->trans('request.label'));

        $writer = call_user_func(array('\PHPExcel_IOFactory', 'createWriter'), $objPHPExcel, 'Excel2007');

        $response = $this->phpexcel->createStreamedResponse($writer);

        $dispositionHeader = $response->headers->makeDisposition(
            ResponseHeaderBag::DISPOSITION_ATTACHMENT, sprintf('%s.%s', $this->translator->trans('request.report'), 'xlsx')
        );
        $response->headers->set('Content-Type', 'text/vnd.ms-excel; charset=utf-8');
        $response->headers->set('Pragma', 'public');
        $response->headers->set('Cache-Control', 'maxage=1');
        $response->headers->set('Content-Disposition', $dispositionHeader);
        return $response;
    }

}