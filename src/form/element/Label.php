<?php


namespace pocketmine\form\element;

use function assert;

/**
 * Element which displays some text on a form.
 */
class Label extends CustomFormElement{

	public function getType() : string{
		return "label";
	}

	public function validateValue($value) : void{
		assert($value === null);
	}

	protected function serializeElementData() : array{
		return [];
	}
}