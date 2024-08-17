<?php

/*
 *
 *  ____            _        _   __  __ _                  __  __ ____
 * |  _ \ ___   ___| | _____| |_|  \/  (_)_ __   ___      |  \/  |  _ \
 * | |_) / _ \ / __| |/ / _ \ __| |\/| | | '_ \ / _ \_____| |\/| | |_) |
 * |  __/ (_) | (__|   <  __/ |_| |  | | | | | |  __/_____| |  | |  __/
 * |_|   \___/ \___|_|\_\___|\__|_|  |_|_|_| |_|\___|     |_|  |_|_|
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * @author PocketMine Team
 * @link http://www.pocketmine.net/
 *
 *
 */

declare(strict_types=1);

namespace pocketmine\item\enchantment;

use pocketmine\block\BlockTypeIds;
use pocketmine\item\Armor;
use pocketmine\item\Axe;
use pocketmine\item\Book;
use pocketmine\item\Bow;
use pocketmine\item\enchantment\AvailableEnchantmentRegistry as EnchantmentRegistry;
use pocketmine\item\Item;
use pocketmine\item\ItemTypeIds;
use pocketmine\item\Pickaxe;
use pocketmine\item\Shovel;
use pocketmine\item\Sword;
use pocketmine\item\VanillaItems as Items;
use pocketmine\utils\Limits;
use pocketmine\utils\Random;
use pocketmine\world\Position;
use function abs;
use function array_filter;
use function chr;
use function count;
use function floor;
use function max;
use function min;
use function mt_rand;
use function ord;
use function round;

/**
 * Helper methods used for enchanting using the enchanting table.
 */
final class EnchantingHelper{
	private const MAX_BOOKSHELF_COUNT = 15;

	private function __construct(){
		//NOOP
	}

	/**
	 * Generates a new random seed for enchant option randomization.
	 */
	public static function generateSeed() : int{
		return mt_rand(Limits::INT32_MIN, Limits::INT32_MAX);
	}

	/**
	 * @param EnchantmentInstance[] $enchantments
	 */
	public static function enchantItem(Item $item, array $enchantments) : Item{
		$resultItem = $item->getTypeId() === ItemTypeIds::BOOK ? Items::ENCHANTED_BOOK() : clone $item;

		foreach($enchantments as $enchantment){
			$resultItem->addEnchantment($enchantment);
		}

		return $resultItem;
	}

	/**
	 * @return EnchantingOption[]
	 */
	public static function generateOptions(Position $tablePos, Item $input, int $seed) : array{
		if($input->isNull() || $input->hasEnchantments()){
			return [];
		}

		$random = new Random($seed);

		/*
		$bookshelfCount = self::countBookshelves($tablePos);
		$baseRequiredLevel = $random->nextRange(1, 8) + ($bookshelfCount >> 1) + $random->nextRange(0, $bookshelfCount);
		$topRequiredLevel = (int) floor(max($baseRequiredLevel / 3, 1));
		$middleRequiredLevel = (int) floor($baseRequiredLevel * 2 / 3 + 1);
		$bottomRequiredLevel = max($baseRequiredLevel, $bookshelfCount * 2);
		return [
			self::createOption($random, $input, $topRequiredLevel),
			self::createOption($random, $input, $middleRequiredLevel),
			self::createOption($random, $input, $bottomRequiredLevel),
		];*/


		$bookshelfCount = self::countBookshelves($tablePos);
		$baseRequiredLevel = $random->nextRange(1, 8) + ($bookshelfCount >> 1) + $random->nextRange(0, $bookshelfCount);
		$topRequiredLevel = (int) floor(max($baseRequiredLevel / 1, 1));
		$middleRequiredLevel = (int) floor($baseRequiredLevel * 1 / 3 + 1);
		$bottomRequiredLevel = max($baseRequiredLevel, $bookshelfCount * 2);

		$sharpnessLevel = ($bookshelfCount >= 15) ? 5 : (($bookshelfCount >= 12) ? 4 : (($bookshelfCount >= 8) ? 3 : (($bookshelfCount >= 4) ? 2 : 1)));
		$efficiencyLevel = ($bookshelfCount >= 15) ? 5 : (($bookshelfCount >= 12) ? 4 : (($bookshelfCount >= 8) ? 3 : (($bookshelfCount >= 4) ? 2 : 1)));
		$unbreakingLevel = ($bookshelfCount >= 15) ? 3 : (($bookshelfCount >= 12) ? 2 : 1);
		$fireAspectLevel = ($bookshelfCount >= 15) ? 2 : (($bookshelfCount >= 8) ? 2 : 1);
		$protectionLevel = ($bookshelfCount >= 15) ? 4 : (($bookshelfCount >= 12) ? 3 : (($bookshelfCount >= 8) ? 2 : 1));
		$fireProtectionLevel = ($bookshelfCount >= 15) ? 4 : (($bookshelfCount >= 12) ? 3 : (($bookshelfCount >= 8) ? 2 : 1));
		$flameLevel = 1;
		$infinityLevel = 1;
		if($input instanceof Sword) {
			return [
				new EnchantingOption($topRequiredLevel, "Sharpness", [ new EnchantmentInstance(VanillaEnchantments::SHARPNESS(), $sharpnessLevel)]),
				new EnchantingOption($middleRequiredLevel, "Solidité", [ new EnchantmentInstance(VanillaEnchantments::UNBREAKING(), $unbreakingLevel)]),
				new EnchantingOption($middleRequiredLevel, "Aura de feu", [ new EnchantmentInstance(VanillaEnchantments::FIRE_ASPECT(), $fireAspectLevel)])
			];
		} else if($input instanceof Armor) {
			return [
				new EnchantingOption($topRequiredLevel, "Protection", [ new EnchantmentInstance(VanillaEnchantments::PROTECTION(), $protectionLevel)]),
				new EnchantingOption($middleRequiredLevel, "Solidité", [ new EnchantmentInstance(VanillaEnchantments::UNBREAKING(), $unbreakingLevel)]),
				new EnchantingOption($topRequiredLevel, "Protection contre le feu", [ new EnchantmentInstance(VanillaEnchantments::FIRE_PROTECTION(), $fireProtectionLevel)]),
			];
		} else if($input instanceof Axe || $input instanceof Pickaxe || $input instanceof Shovel) {
			return [
				new EnchantingOption($topRequiredLevel, "Efficacité", [ new EnchantmentInstance(VanillaEnchantments::EFFICIENCY(), $efficiencyLevel)]),
				new EnchantingOption($middleRequiredLevel, "Solidité", [ new EnchantmentInstance(VanillaEnchantments::UNBREAKING(), $unbreakingLevel)])
			];
		} else if($input instanceof Bow) {
			return [
				new EnchantingOption($bottomRequiredLevel, "Flame", [ new EnchantmentInstance(VanillaEnchantments::FLAME(), $flameLevel)]),
				new EnchantingOption($bottomRequiredLevel, "Infinité", [ new EnchantmentInstance(VanillaEnchantments::INFINITY(), $infinityLevel)])
			];
		} else if($input instanceof Book) {
			$enchantOption = [
				new EnchantingOption($middleRequiredLevel, "Flame", [ new EnchantmentInstance(VanillaEnchantments::FLAME(), $flameLevel)]),
				new EnchantingOption($middleRequiredLevel, "Infinité", [ new EnchantmentInstance(VanillaEnchantments::INFINITY(), $infinityLevel)]),
				new EnchantingOption($topRequiredLevel, "Efficacité", [ new EnchantmentInstance(VanillaEnchantments::EFFICIENCY(), $efficiencyLevel)]),
				new EnchantingOption($middleRequiredLevel, "Solidité", [ new EnchantmentInstance(VanillaEnchantments::UNBREAKING(), $unbreakingLevel)]),
				new EnchantingOption($middleRequiredLevel, "Aura de feu", [ new EnchantmentInstance(VanillaEnchantments::FIRE_ASPECT(), $fireAspectLevel)]),
				new EnchantingOption($topRequiredLevel, "Sharpness", [ new EnchantmentInstance(VanillaEnchantments::SHARPNESS(), $sharpnessLevel)]),
				new EnchantingOption($topRequiredLevel, "Protection", [ new EnchantmentInstance(VanillaEnchantments::PROTECTION(), $protectionLevel)]),
				new EnchantingOption($topRequiredLevel, "Protection contre le feu", [ new EnchantmentInstance(VanillaEnchantments::FIRE_PROTECTION(), $fireProtectionLevel)])
			];
			$randomEnchant = array_rand($enchantOption, 3);
			return [
				$enchantOption[$randomEnchant[0]],
				$enchantOption[$randomEnchant[1]],
				$enchantOption[$randomEnchant[2]]
			];
		} else {
			return [];
		}
	}

	private static function countBookshelves(Position $tablePos) : int{
		$bookshelfCount = 0;
		$world = $tablePos->getWorld();

		for($x = -2; $x <= 2; $x++){
			for($z = -2; $z <= 2; $z++){
				// We only check blocks at a distance of 2 blocks from the enchanting table
				if(abs($x) !== 2 && abs($z) !== 2){
					continue;
				}

				// Ensure the space between the bookshelf stack at this X/Z and the enchanting table is empty
				for($y = 0; $y <= 1; $y++){
					// Calculate the coordinates of the space between the bookshelf and the enchanting table
					$spaceX = max(min($x, 1), -1);
					$spaceZ = max(min($z, 1), -1);
					$spaceBlock = $world->getBlock($tablePos->add($spaceX, $y, $spaceZ));
					if($spaceBlock->getTypeId() !== BlockTypeIds::AIR){
						continue 2;
					}
				}

				// Finally, check the number of bookshelves at the current position
				for($y = 0; $y <= 1; $y++){
					$block = $world->getBlock($tablePos->add($x, $y, $z));
					if($block->getTypeId() === BlockTypeIds::BOOKSHELF){
						$bookshelfCount++;
						if($bookshelfCount === self::MAX_BOOKSHELF_COUNT){
							return $bookshelfCount;
						}
					}
				}
			}
		}

		return $bookshelfCount;
	}

	private static function createOption(Random $random, Item $inputItem, int $requiredXpLevel) : EnchantingOption{
		$enchantingPower = $requiredXpLevel;

		$enchantability = $inputItem->getEnchantability();
		$enchantingPower = $enchantingPower + $random->nextRange(0, $enchantability >> 2) + $random->nextRange(0, $enchantability >> 2) + 1;
		// Random bonus for enchanting power between 0.85 and 1.15
		$bonus = 1 + ($random->nextFloat() + $random->nextFloat() - 1) * 0.15;
		$enchantingPower = (int) round($enchantingPower * $bonus);

		$resultEnchantments = [];
		$availableEnchantments = self::getAvailableEnchantments($enchantingPower, $inputItem);

		$lastEnchantment = self::getRandomWeightedEnchantment($random, $availableEnchantments);
		if($lastEnchantment !== null){
			$resultEnchantments[] = $lastEnchantment;

			// With probability (power + 1) / 50, continue adding enchantments
			while($random->nextFloat() <= ($enchantingPower + 1) / 50){
				// Remove from the list of available enchantments anything that conflicts
				// with previously-chosen enchantments
				$availableEnchantments = array_filter(
					$availableEnchantments,
					function(EnchantmentInstance $e) use ($lastEnchantment){
						return $e->getType() !== $lastEnchantment->getType() &&
							$e->getType()->isCompatibleWith($lastEnchantment->getType());
					}
				);

				$lastEnchantment = self::getRandomWeightedEnchantment($random, $availableEnchantments);
				if($lastEnchantment === null){
					break;
				}

				$resultEnchantments[] = $lastEnchantment;
				$enchantingPower >>= 1;
			}
		}

		return new EnchantingOption($requiredXpLevel, self::getRandomOptionName($random), $resultEnchantments);
	}

	/**
	 * @return EnchantmentInstance[]
	 */
	private static function getAvailableEnchantments(int $enchantingPower, Item $item) : array{
		$list = [];

		foreach(EnchantmentRegistry::getInstance()->getPrimaryEnchantmentsForItem($item) as $enchantment){
			for($lvl = $enchantment->getMaxLevel(); $lvl > 0; $lvl--){
				if($enchantingPower >= $enchantment->getMinEnchantingPower($lvl) &&
					$enchantingPower <= $enchantment->getMaxEnchantingPower($lvl)
				){
					$list[] = new EnchantmentInstance($enchantment, $lvl);
					break;
				}
			}
		}

		return $list;
	}

	/**
	 * @param EnchantmentInstance[] $enchantments
	 */
	private static function getRandomWeightedEnchantment(Random $random, array $enchantments) : ?EnchantmentInstance{
		if(count($enchantments) === 0){
			return null;
		}

		$totalWeight = 0;
		foreach($enchantments as $enchantment){
			$totalWeight += $enchantment->getType()->getRarity();
		}

		$result = null;
		$randomWeight = $random->nextRange(1, $totalWeight);

		foreach($enchantments as $enchantment){
			$randomWeight -= $enchantment->getType()->getRarity();

			if($randomWeight <= 0){
				$result = $enchantment;
				break;
			}
		}

		return $result;
	}

	private static function getRandomOptionName(Random $random) : string{
		$name = "";
		for($i = $random->nextRange(5, 15); $i > 0; $i--){
			$name .= chr($random->nextRange(ord("a"), ord("z")));
		}

		return $name;
	}
}
