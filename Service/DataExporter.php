<?php

namespace Fungio\DataExporterBundle\Service;

use Fungio\ExcelBundle\Factory;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

/**
 * Class DataExporter
 * @package Fungio\DataExporterBundle\Service
 *
 * @author  Pierrick AUBIN <pierrick.aubin@gmail.com>
 */
class DataExporter
{
    /**
     * @var array
     */
    protected $columns = [];

    /**
     * @var array
     */
    protected $data = [];

    /**
     * @var string
     */
    protected $format;

    /**
     * @var string
     */
    protected $separator = ';';

    /**
     * @var string
     */
    protected $fileName;

    /**
     * @var string
     */
    protected $title;

    /**
     * @var boolean
     */
    protected $skipHeader = false;

    /**
     * @var array
     */
    protected $supportedFormat = ['csv', 'xls', 'html', 'json'];

    /**
     * @var Factory
     */
    protected $phpExcel;

    /**
     * DataExporter constructor.
     *
     * @param Factory $phpExcel
     */
    public function __construct(Factory $phpExcel)
    {
        $this->phpExcel = $phpExcel;
    }

    /**
     * @param       $format
     * @param array $options
     *
     * @return $this
     * @throws \RuntimeException
     */
    public function setOptions($format, $options = [])
    {
        if (!in_array(strtolower($format), $this->supportedFormat)) {
            throw new \RuntimeException(sprintf('The format %s is not supported', $format));
        }

        $this->format = strtolower($format);

        //convert key and values to lowercase
        $options = array_change_key_case($options, CASE_LOWER);
        $options = array_map('strtolower', $options);

        //fileName
        if (array_key_exists('filename', $options)) {
            $this->fileName = $options['filename'] . '.' . $this->format;
        } else {
            $this->fileName = 'Data export' . '.' . $this->format;
        }

        //fileName
        if (array_key_exists('title', $options)) {
            $this->title = $options['title'];
        } else {
            $this->title = 'Data export';
        }

        if (array_key_exists('separator', $options)) {
            $this->separator = $options['separator'];
        }

        //skip header
        if (in_array('skip_header', $options) || (array_key_exists('skip_header', $options) && $options['skip_header'])) {
            $this->skipHeader = true;
        }

        return $this;
    }

    /**
     * @param $rows
     *
     * @return $this
     * @throws \RuntimeException
     */
    public function setData($rows)
    {
        foreach ($rows as $row) {
            $line = [];
            foreach ($row as $k => $v) {
                $line[] = $v;
            }
            $this->data[] = $line;
        }

        return $this;
    }

    /**
     * @param array $columns
     *
     * @return $this
     * @throws \RuntimeException
     */
    public function setColumns(Array $columns)
    {
        foreach ($columns as $key => $column) {
            $this->columns[] = $column;
        }

        return $this;
    }

    /**
     * @return BinaryFileResponse|JsonResponse
     * @throws \Exception
     */
    public function render()
    {
        if ($this->skipHeader) {
            $data = $this->data;
        } else {
            $data = array_merge([$this->columns], $this->data);
        }

        $phpExcelObject = $this->phpExcel->createPHPExcelObject();

        $phpExcelObject
            ->getProperties()
            ->setTitle($this->title)
            ->setSubject($this->title)
            ->setDescription($this->title);
        $phpExcelObject
            ->setActiveSheetIndex(0);
        $activeSheet = $phpExcelObject->getActiveSheet();

        $row = 1;
        foreach ($data as $line) {
            $col = 0;
            foreach ($line as $value) {
                $activeSheet->setCellValue(\PHPExcel_Cell::stringFromColumnIndex($col) . $row, $value);
                $col++;
            }
            $row++;
        }

        for ($i = 0; $i <= (count($this->columns) + 1); $i++) {
            $activeSheet->getColumnDimension(\PHPExcel_Cell::stringFromColumnIndex($i))
                ->setAutoSize(true);
        }

        $writer = null;
        switch ($this->format) {
            case 'csv':
                $writer = $this->phpExcel->createWriter($phpExcelObject, 'CSV');
                $writer->setDelimiter($this->separator);
                break;
            case 'json':
                $response = new JsonResponse($data);

                return $response;
                break;
            case 'xls':
                $writer = $this->phpExcel->createWriter($phpExcelObject, 'Excel5');
                break;
            case 'xlsx':
                $writer = $this->phpExcel->createWriter($phpExcelObject, 'Excel2007');
                break;
            case 'html':
                $writer = $this->phpExcel->createWriter($phpExcelObject, 'HTML');
                break;
        }

        if (!is_object($writer)) {
            throw new \Exception('Format ' . $this->format . ' not supported');
        }

        $writer->save($this->fileName);


        $response = new BinaryFileResponse($this->fileName);
        $response
            ->setContentDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT)
            ->deleteFileAfterSend(true);

        return $response;
    }
}
