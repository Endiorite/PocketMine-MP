<?php


namespace pocketmine\form\element;

use pocketmine\form\FormValidationException;

/**
 * Base class for UI elements which can be placed on custom forms.
 */
abstract class CustomFormElement implements \JsonSerializable{
	/** @var string */
	private $name;
	/** @var string */
	private $text;

	public function __construct(string $name, string $text){
		$this->name = $name;
		$this->text = $text;
	}

	/**
	 * Returns the type of element.
	 */
	abstract public function getType() : string;

	/**
	 * Returns the element's name. This is used to identify the element in code.
	 */
	public function getName() : string{
		return $this->name;
	}

	/**
	 * Returns the element's label. Usually this is used to explain to the user what a control does.
	 */
	public function getText() : string{
		return $this->text;
	}

	/**
	 * Validates that the given value is of the correct type and fits the constraints for the component. This function
	 * should do appropriate type checking and throw whatever errors necessary if the value is not valid.
	 *
	 * @param mixed $value
	 * @throws FormValidationException
	 */
	abstract public function validateValue($value) : void;

	/**
	 * Returns an array of properties which can be serialized to JSON for sending.
	 * @return mixed[]
	 */
	final public function jsonSerialize() : array{
		$ret = $this->serializeElementData();
		$ret["type"] = $this->getType();
		$ret["text"] = $this->getText();

		return $ret;
	}

	/**
	 * Returns an array of extra data needed to serialize this element to JSON for showing to a player on a form.
	 * @return mixed[]
	 */
	abstract protected function serializeElementData() : array;
}
