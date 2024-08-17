<?php


namespace pocketmine\block\utils;

use pocketmine\item\Item;

class AnvilResult{

	public function __construct(
		private int $repairCost,
		private ?Item $result,
	){}

	public function getRepairCost() : int{
		return $this->repairCost;
	}

	public function getResult() : ?Item{
		return $this->result;
	}

}