<?php

class ConfigTest extends PHPUnit_Framework_TestCase
{
	public function testSetNameKeyConfig()
	{
		$config = $this->config();

		$config->set('name.key','value');

		$value = $config->get('name.key');

		$this->assertEquals('value', $value);
	}

	public function testGetNameKeyConfig()
	{
		$config = $this->config();

		$value = $config->get('database.default');

		$this->assertEquals('mysql', $value);
	}

	public function testSetAllConfig()
	{
		$config = $this->config();

		$config->set('name', ['key' => 'value']);

		$value = $config->get('name');

		$this->assertEquals('value', $value['key']);
	}

	public function testGetAllConfig()
	{
		$config = $this->config();

		$value = $config->get('database');

		$this->assertEquals('mysql', $value['default']);
	}

	public function testSetAndGetAliases()
	{
		$config = $this->config();

		$value = $config->aliases(['alias' => 'c:/']);

		$aliases = $config->aliases();

		$this->assertEquals('c:/', $aliases['alias']);
	}

	public function testAddAlias()
	{
		$config = $this->config();

		$value = $config->alias('alias', 'c:/');

		$alias = $config->alias('alias');

		$this->assertEquals('c:/', $alias);
	}

	public function testSetAliasNameKeyConfig()
	{
		$config = $this->config();

		$config->set('alias::name.key', 'value');

		$value = $config->get('alias::name.key');

		$this->assertEquals('value', $value);
	}

	public function testGetAliasNameKeyConfig()
	{
		$config = $this->config();

		$config->alias('alias', $config->getDefaultPath());

		$value = $config->get('alias::database.default');

		$this->assertEquals('mysql', $value);
	}

	public function config()
	{
		$defaultPath = __DIR__ . '';

		$config = new Qlake\Config\Config();

		$config->setDefaultPath($defaultPath);

		return $config;
	}
}