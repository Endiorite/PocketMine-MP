<?php


namespace pocketmine\form;

class FormIcon implements \JsonSerializable{
	public const IMAGE_TYPE_URL = "url";
	public const IMAGE_TYPE_PATH = "path";

	/** @var string */
	private $type;
	/** @var string */
	private $data;

	/**
	 * @param string $data URL or path depending on the type chosen.
	 * @param string $type Can be one of the constants at the top of the file, but only "url" is known to work.
	 */
	public function __construct(string $data, string $type = self::IMAGE_TYPE_URL){
		$this->type = $type;
		$this->data = $data;
	}

	public function getType() : string{
		return $this->type;
	}

	public function getData() : string{
		return $this->data;
	}

	#[\ReturnTypeWillChange]
	public function jsonSerialize(){
		return [
			"type" => $this->type,
			"data" => $this->data
		];
	}
}