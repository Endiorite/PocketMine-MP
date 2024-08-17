<?php


namespace pocketmine\form;

class ServerSettingsForm extends CustomForm{
	/** @var FormIcon|null */
	private $icon;

	public function __construct(string $title, array $elements, ?FormIcon $icon, \Closure $onSubmit, ?\Closure $onClose = null){
		parent::__construct($title, $elements, $onSubmit, $onClose);
		$this->icon = $icon;
	}

	public function hasIcon() : bool{
		return $this->icon !== null;
	}

	public function getIcon() : ?FormIcon{
		return $this->icon;
	}

	protected function serializeFormData() : array{
		$data = parent::serializeFormData();

		if($this->hasIcon()){
			$data["icon"] = $this->icon;
		}

		return $data;
	}
}