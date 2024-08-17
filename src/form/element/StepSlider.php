<?php


namespace pocketmine\form\element;

class StepSlider extends BaseSelector{

	public function getType() : string{
		return "step_slider";
	}

	protected function serializeElementData() : array{
		return [
			"steps" => $this->options,
			"default" => $this->defaultOptionIndex
		];
	}
}