<?php

namespace Fungio\DataExporterBundle\Test\Service;

use Fungio\DataExporterBundle\Service\DataExporter;
use Fungio\DataExporterBundle\Tests\TestObject;

/**
 * Class DataExporterTest
 * @package Fungio\DataExporterBundle\Test\Service
 *
 * @author Pierrick AUBIN <pierrick.aubin@gmail.com>
 */
class DataExporterTest extends \PHPUnit_Framework_TestCase
{
    public function testCSVExport()
    {
        $exporter = new DataExporter();
        $exporter->setOptions('csv', ['fileName' => 'file', 'separator' => ';']);
        $exporter->setColumns(['[col1]', '[col2]', '[col3]']);
        $exporter->setData(
            [
                ['col1' => '1a', 'col2' => '1b', 'col3' => '1c'],
                ['col1' => '2a', 'col2' => '2b'],
            ]
        );

        $result = "[col1];[col2];[col3]\n1a;1b;1c\n2a;2b;";

        $this->assertEquals($result, $exporter->render()->getContent());
    }

    public function testXLSExport()
    {
        $exporter = new DataExporter();
        $exporter->setOptions('xls', ['fileName' => 'file']);
        $exporter->setColumns(['[col1]', '[col2]', '[col3]']);
        $exporter->setData(
            [
                ['col1' => '1a', 'col2' => '1b', 'col3' => '1c'],
                ['col1' => '2a', 'col2' => '2b'],
            ]
        );

        $result = '<!DOCTYPE ><html><head><meta http-equiv="Content-Type" content="text/html; charset=utf-8" /><meta name="ProgId" content="Excel.Sheet"><meta name="Generator" content="https://github.com/fungio/DataExporter"></head><body><table><tr><td>[col1]</td><td>[col2]</td><td>[col3]</td></tr><tr><td>1a</td><td>1b</td><td>1c</td></tr><tr><td>2a</td><td>2b</td><td></td></tr></table></body></html>';

        $this->assertEquals($result, $exporter->render()->getContent());
    }

    public function testCSVExportFromObject()
    {
        $exporter = new DataExporter();
        $testObject = new TestObject();

        $exporter->setOptions('csv', ['fileName' => 'file', 'separator' => ';']);
        $exporter->setColumns(['col1' => 'Label1', 'col2' => 'Label2']);
        $exporter->setData([$testObject]);

        $result = "Label1;Label2\n1a;1b";

        $this->assertEquals($result, $exporter->render()->getContent());
    }

    public function testXLSExportFromObject()
    {
        $exporter = new DataExporter();
        $testObject = new TestObject();

        $exporter->setOptions('xls', ['fileName' => 'file']);
        $exporter->setColumns(['col1' => 'Label1', 'col2' => 'Label2', 'col3.col1' => 'From object two']);
        $exporter->setData([$testObject]);

        $result = '<!DOCTYPE ><html><head><meta http-equiv="Content-Type" content="text/html; charset=utf-8" /><meta name="ProgId" content="Excel.Sheet"><meta name="Generator" content="https://github.com/fungio/DataExporter"></head><body><table><tr><td>Label1</td><td>Label2</td><td>From object two</td></tr><tr><td>1a</td><td>1b</td><td>Object two</td></tr></table></body></html>';

        $this->assertEquals($result, $exporter->render()->getContent());
    }

    public function testHTMLExport()
    {
        $exporter = new DataExporter();
        $exporter->setOptions('html', ['fileName' => 'file']);
        $exporter->setColumns(['[col1]' => 'Column 1', '[col2]' => 'Column 2', '[col3]' => 'Column 3']);
        $exporter->setData(
            [
                ['col1' => '1a', 'col2' => '1b', 'col3' => '1c'],
                ['col1' => '2a', 'col2' => '2b'],
            ]
        );

        $result = '<!DOCTYPE ><html><head><meta http-equiv="Content-Type" content="text/html; charset=utf-8" /><meta name="Generator" content="https://github.com/fungio/DataExporter"></head><body><table><tr><td>Column 1</td><td>Column 2</td><td>Column 3</td></tr><tr><td>1a</td><td>1b</td><td>1c</td></tr><tr><td>2a</td><td>2b</td><td></td></tr></table></body></html>';

        $this->assertEquals($result, $exporter->render()->getContent());
    }

    public function testXMLExport()
    {
        $exporter = new DataExporter();
        $exporter->setOptions('xml', ['fileName' => 'file', 'charset' => 'ISO-8859-2']);
        $exporter->setColumns(['[col1]', '[col2]', '[col3]']);
        $exporter->setData(
            [
                ['col1' => '<test>1a</test>', 'col2' => '"1b"', 'col3' => '< 1c'],
                ['col1' => '\'2a\'', 'col2' => '> & "2b"'],
            ]
        );

        $result_5_4 = '<?xml version="1.0" encoding="ISO-8859-2"?><table><row><column name="[col1]">&lt;test&gt;1a&lt;/test&gt;</column><column name="[col2]">"1b"</column><column name="[col3]">&lt; 1c</column></row><row><column name="[col1]">\'2a\'</column><column name="[col2]">&gt; &amp; "2b"</column><column name="[col3]"></column></row></table>';
        $result = '<?xml version="1.0" encoding="ISO-8859-2"?><table><row><column name="[col1]">&lt;test&gt;1a&lt;/test&gt;</column><column name="[col2]">&quot;1b&quot;</column><column name="[col3]">&lt; 1c</column></row><row><column name="[col1]">\'2a\'</column><column name="[col2]">&gt; &amp; &quot;2b&quot;</column><column name="[col3]"></column></row></table>';

        if (version_compare(phpversion(), '5.4.0', '>=')) {
            $this->assertEquals($result_5_4, $exporter->render()->getContent());
        } else {
            $this->assertEquals($result, $exporter->render()->getContent());
        }
    }

    public function testJSONExport()
    {
        $exporter = new DataExporter();
        $exporter->setOptions('json', ['fileName' => 'file']);
        $exporter->setColumns(['[col1]', '[col2]', '[col3]']);
        $exporter->setData(
            [
                ['col1' => '1a', 'col2' => '1b', 'col3' => '1c'],
                ['col1' => '2a', 'col2' => '2b'],
            ]
        );

        $result = '{"1":{"[col1]":"1a","[col2]":"1b","[col3]":"1c"},"2":{"[col1]":"2a","[col2]":"2b","[col3]":""}}';

        $this->assertEquals($result, $exporter->render()->getContent());
    }

    public function testJSONMemoryEscapeExport()
    {
        $exporter = new DataExporter();
        $exporter->setOptions('json', ['memory']);
        $exporter->setColumns(['[col1]', '[col2]', '[col3]']);
        $exporter->setData(
            [
                ['col1' => '1a', 'col2' => '1b', 'col3' => '1c'],
                ['col1' => '2a', 'col2' => '2b'],
            ]
        );

        $result = '{"1":{"[col1]":"1a","[col2]":"1b","[col3]":"1c"},"2":{"[col1]":"2a","[col2]":"2b","[col3]":""}}';

        $this->assertEquals($result, $exporter->render());
    }


    public function testHookExport()
    {
        $exporter = new DataExporter();
        $exporter->setOptions('json', ['fileName' => 'file']);
        $exporter->setColumns(['[col1]', '[col2]', '[col3]']);
        $exporter->addHook(['Fungio\DataExporterBundle\Test\Service\DataExporterTest', 'hookTest'], '[col1]');
        $exporter->addHook([&$this, 'hookTest2'], '[col3]');
        $exporter->setData(
            [
                ['col1' => '1a', 'col2' => '1b', 'col3' => '1c'],
                ['col1' => '2a', 'col2' => '2b'],
            ]
        );

        $result = '{"1":{"[col1]":"1aHooked","[col2]":"1b","[col3]":"1cHooked2"},"2":{"[col1]":"2aHooked","[col2]":"2b","[col3]":"Hooked2"}}';

        $this->assertEquals($result, $exporter->render()->getContent());
    }

    public function testHookClosureExport()
    {
        $exporter = new DataExporter();
        $exporter->setOptions('json', ['fileName' => 'file']);
        $exporter->setColumns(['[col1]', '[col2]', '[col3]']);

        $f = function ($parm) {
            return $parm . 'Hooked2';
        };

        $exporter->addHook([&$this, 'hookTest'], '[col1]');
        $exporter->addHook($f, '[col3]');

        $exporter->setData(
            [
                ['col1' => '1a', 'col2' => '1b', 'col3' => '1c'],
                ['col1' => '2a', 'col2' => '2b'],
            ]
        );

        $result = '{"1":{"[col1]":"1aHooked","[col2]":"1b","[col3]":"1cHooked2"},"2":{"[col1]":"2aHooked","[col2]":"2b","[col3]":"Hooked2"}}';

        $this->assertEquals($result, $exporter->render()->getContent());
    }

    public function testCSVExportSkipHeader()
    {
        $exporter = new DataExporter();
        $exporter->setOptions('csv', ['fileName' => 'file', 'separator' => ';', 'skip_header']);
        $exporter->setColumns(['[col1]', '[col2]', '[col3]']);
        $exporter->setData(
            [
                ['col1' => '1a', 'col2' => '1b', 'col3' => '1c'],
                ['col1' => '2a', 'col2' => '2b'],
            ]
        );

        $result = "1a;1b;1c\n2a;2b;";

        $this->assertEquals($result, $exporter->render()->getContent());
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testExportSkipHeaderException()
    {
        $exporter = new DataExporter();
        $exporter->setOptions('html', ['fileName' => 'file', 'separator' => ';', 'skip_header']);
    }

    public function hookTest($data)
    {
        return $data . 'Hooked';
    }

    public function hookTest2($data)
    {
        return $data . 'Hooked2';
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testBadFormatException()
    {
        $exporter = new DataExporter();
        $exporter->setOptions('none');
    }

    /**
     * @expectedException \LengthException
     */
    public function testHookNonParameter()
    {
        $exporter = new DataExporter();
        $exporter->setOptions('json', ['fileName' => 'file']);
        $exporter->setColumns(['[col1]', '[col2]', '[col3]']);
        $exporter->addHook(['Fungio\DataExporterBundle\Test\Service\DataExporterTest'], '[col1]');
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testHookNonExistColumn()
    {
        $exporter = new DataExporter();
        $exporter->setOptions('json', ['fileName' => 'file']);
        $exporter->setColumns(['[col1]', '[col2]', '[col3]']);
        $exporter->addHook(['Fungio\DataExporterBundle\Test\Service\DataExporterTest', 'hookTest'], '[colNonExist]');
    }

    /**
     * @expectedException \BadFunctionCallException
     */
    public function testHookNonFunctionExist()
    {
        $exporter = new DataExporter();
        $exporter->setOptions('json', ['fileName' => 'file']);
        $exporter->setColumns(['[col1]', '[col2]', '[col3]']);
        $exporter->addHook(['Fungio\DataExporterBundle\Test\Service\DataExporterTest', 'hookTestNon'], '[col1]');
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testSetDataException()
    {
        $exporter = new DataExporter();
        $exporter->setData([]);
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testSetData2Exception()
    {
        $exporter = new DataExporter();
        $exporter->setOptions('csv');
        $exporter->setData([]);
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testAddColumnsException()
    {
        $exporter = new DataExporter();
        $exporter->setColumns([]);
    }
}
