<?php


namespace pocketmine\form;

class MenuOption implements \JsonSerializable{

	/** @var string */
	private $text;
	/** @var FormIcon|null */
	private $image;

	public function __construct(string $text, ?FormIcon $image = null){
		$this->text = $text;
		$this->image = $image;
	}

	public function getText() : string{
		return $this->text;
	}

	public function hasImage() : bool{
		return $this->image !== null;
	}

	public function getImage() : ?FormIcon{
		return $this->image;
	}

	#[\ReturnTypeWillChange]
	public function jsonSerialize(){
		$json = [
			"text" => $this->text
		];

		if($this->hasImage()){
			$json["image"] = $this->image;
		}

		return $json;
	}
}
