<?php

class Logger
{
	private $_file;
	private $_strs = array();

  public function __construct($file = null)
  {
    $this->_file = $file;
  }

  public function add($str)
  {
    $this->_strs[] = $str;

    return $this;
  }

	public function log($str)
	{
    $this->add('['.date('Y-m-d H:i:s').'] '.$str.'<br/>');
    $this->write();
	}
	public function errorLog($str)
	{
    $this->add('<div style="color: red; font-weight: bold;">['.date('Y-m-d H:i:s').'] '.$str.'</div> <br/>');
    $this->write();
	}
	public function successLog($str)
	{
    $this->add('<div style="color: green; font-weight: bold;">['.date('Y-m-d H:i:s').'] '.$str.'</div> <br/>');
    $this->write();
	}

  public function get($glue = '')
  {
    return implode($glue, $this->_strs);
  }


  public function write()
  {
    file_put_contents(
      $this->_file,
      $this->get(PHP_EOL).PHP_EOL,
      FILE_APPEND
    );

    $this->_strs = [];
  }
}
