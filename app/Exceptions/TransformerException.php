<?php namespace App\Exceptions;

class TransformerException extends \Exception
{
	public function __construct ($message = "Transformer's transformCollection requires Collection or Paginator instance.") {
		parent::__construct($message, 500);
	}
}