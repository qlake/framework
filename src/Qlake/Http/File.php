<?php

namespace Qlake\Http;

use SplFileInfo;

class File
{
	protected $file;


	protected $name;


	protected $type;


	protected $tempName;


	protected $size;


	protected $error;



	public function __construct($file)
	{
		$this->name = $file['name'];

		$this->type = $file['type'];

		$this->tempName = $file['tmp_name'];

		$this->size = $file['size'];

		$this->error = $file['error'];

		$this->file = new SplFileInfo($file['tmp_name']);
	}



	public function getSize()
	{
		return $this->size;
	}



	public function getName()
	{
		return $this->name;
	}



	public function getTempName()
	{
		return $this->tempName;
	}



	public function getType()
	{
		return $this->type;
	}



	public function getRealType()
	{
		
	}



	public function getError()
	{
		return $this->error;
	}



	/*public function getKey()
	{
		
	}*/




	public function isValid()
	{
		return $this->error === UPLOAD_ERR_OK && is_uploaded_file($this->tempName);
	}




	public function moveTo($destination)
	{
		return move_uploaded_file($this->tempName, $destination);
	}



	/*public function static __set_state ($paras)
	{
		
	}*/



	public function getExtension()
	{
		
	}



	public function hasError()
	{
		return $this->error !== UPLOAD_ERR_OK;
	}



	/**/
	/*public function getPath()
	{
		
	}



	public function getFilename()
	{
		
	}



	public function getBasename($suffi)
	{
		
	}



	public function getPathname()
	{
		
	}



	public function getPerms()
	{
		
	}



	public function getInode()
	{
		
	}



	public function getOwner()
	{
		
	}



	public function getGroup()
	{
		
	}



	public function getATime()
	{
		
	}



	public function getMTime()
	{
		
	}



	public function getCTime()
	{
		
	}



	public function isWritable()
	{
		
	}



	public function isReadable()
	{
		
	}



	public function isExecutable()
	{
		
	}



	public function isFile()
	{
		
	}



	public function isDir()
	{
		
	}



	public function isLink()
	{
		
	}



	public function getLinkTarget()
	{
		
	}



	public function getRealPath()
	{
		
	}



	public function getFileInfo($class_nam)
	{
		
	}



	public function getPathInfo($class_nam)
	{
		
	}



	public function openFile($open_mode, $use_include_path, $contex)
	{
		
	}



	public function setFileClass($class_nam)
	{
		
	}



	public function setInfoClass($class_nam)
	{
		
	}



	public function __toString()
	{

	}*/
}