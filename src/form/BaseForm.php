<?php


namespace pocketmine\form;

abstract class BaseForm implements Form{

	/** @var string */
	protected $title;

	public function __construct(string $title){
		$this->title = $title;
	}

	/**
	 * Returns the text shown on the form title-bar.
	 */
	public function getTitle() : string{
		return $this->title;
	}

	/**
	 * Serializes the form to JSON for sending to clients.
	 * @return mixed[]
	 */
	final public function jsonSerialize() : array{
		$ret = $this->serializeFormData();
		$ret["type"] = $this->getType();
		$ret["title"] = $this->getTitle();

		return $ret;
	}

	/**
	 * Returns the type used to show this form to clients
	 */
	abstract protected function getType() : string;

	/**
	 * Serializes additional data needed to show this form to clients.
	 * @return mixed[]
	 */
	abstract protected function serializeFormData() : array;

}