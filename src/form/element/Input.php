<?php


namespace pocketmine\form\element;

use pocketmine\form\FormValidationException;
use function gettype;
use function is_string;

/**
 * Element which accepts text input. The text-box can have a default value, and may also have a text hint when there is
 * no text in the box.
 */
class Input extends CustomFormElement{

	/** @var string */
	private $hint;
	/** @var string */
	private $default;

	public function __construct(string $name, string $text, string $hintText = "", string $defaultText = ""){
		parent::__construct($name, $text);
		$this->hint = $hintText;
		$this->default = $defaultText;
	}

	public function getType() : string{
		return "input";
	}

	public function validateValue($value) : void{
		if(!is_string($value)){
			throw new FormValidationException("Expected string, got " . gettype($value));
		}
	}

	/**
	 * Returns the text shown in the text-box when the box is not focused and there is no text in it.
	 */
	public function getHintText() : string{
		return $this->hint;
	}

	/**
	 * Returns the text which will be in the text-box by default.
	 */
	public function getDefaultText() : string{
		return $this->default;
	}

	protected function serializeElementData() : array{
		return [
			"placeholder" => $this->hint,
			"default" => $this->default
		];
	}
}