<?php

class HtmlTest extends PHPUnit_Framework_TestCase
{
	public function testStyleTag()
	{
		$html = $this->html();

		$actual = $html->style('path/file.css', ['attr1' => 'value1', 'attr2' => 'value2']);

		$expected = '<link href="path/file.css" rel="stylesheet" type="text/css" media="all" attr1="value1" attr2="value2" />' . PHP_EOL;

		$this->assertEquals($actual, $expected);
	}

	public function testScriptTag()
	{
		$html = $this->html();

		$actual = $html->script('path/file.css', ['attr1' => 'value1', 'attr2' => 'value2']);

		$expected = '<script src="path/file.css" type="text/javascript" attr1="value1" attr2="value2"></script>' . PHP_EOL;

		$this->assertEquals($actual, $expected);
	}

	public function testFaviconTag()
	{
		$html = $this->html();

		$actual = $html->favicon('path/file.ico');

		$expected = '<link href="path/file.ico" rel="shortcut icon" />' . PHP_EOL;

		$this->assertEquals($actual, $expected);
	}

	public function testMetaTag()
	{
		$html = $this->html();

		$actual = $html->meta(['charset' => 'utf-8']);

		$expected = '<meta charset="utf-8" />' . PHP_EOL;

		$this->assertEquals($actual, $expected);
	}

	public function testLabelTag()
	{
		$html = $this->html();

		$actual = $html->label('label text', ['attr1' => 'value1', 'attr2' => 'value2']);

		$expected = '<label attr1="value1" attr2="value2">label text</label>' . PHP_EOL;

		$this->assertEquals($actual, $expected);
	}

	public function html()
	{
		return new Qlake\Html\HtmlBuilder();
	}
}