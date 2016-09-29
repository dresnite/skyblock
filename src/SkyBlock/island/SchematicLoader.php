<?php
/*
 * Schematic Loader plugin for PocketMine-MP & forks
 *
 * @Author: svile
 * @Kik: _svile_
 * @Telegram_Gruop: https://telegram.me/svile
 * @E-mail: thesville@gmail.com
 * @Github: https://github.com/svilex/Schematic_Loader
 *
 * Copyright (C) 2016 svile
 *
 * THANKS SO MUCH TO SVILE FOR HELPING ME WITH THIS!
 * My code just had 2 other things thats why it didn't work. He made it :)
 */
namespace SkyBlock\island;
use pocketmine\nbt\NBT;
use pocketmine\item\Item;
use pocketmine\block\Fence;
class SchematicLoader{
	/** @var SCHmain */
	private $pg;
	/** @var string */
	private $path;
	/** @var NBT */
	private $nbt;
	private $blocks;
	private $data;
	/** @var int */
	private $height = 0;
	/** @var int */
	private $length = 0;
	/** @var int */
	private $width = 0;
	/** @var array */
	private $blocks_array = [];
	/**
	 * Schematic constructor.
	 *
	 * @param Main $plugin 
	 * @param string $path 
	 */
		
		if(!touch($path)) return false;
		$this->nbt = new NBT(NBT::BIG_ENDIAN);
		$this->nbt->readCompressed(file_get_contents($path));
		$data = $this->nbt->getData();
		$this->blocks = $data->Blocks->getValue();
		$this->data = $data->Data->getValue();
		$this->height = (int) $data->Height->getValue();
		$this->length = (int) $data->Length->getValue();
		$this->width = (int) $data->Width->getValue();
		
		for($x = 0; $x < $this->width; $x++){
			for($y = 0; $y < $this->height; $y++){
				for($z = 0; $z < $this->length; $z++){
					$i = $y * $this->width * $this->length + $z * $this->width + $x;
					$id = $this->readByte($this->blocks, $i);
					$damage = $this->readByte($this->data, $i);
					switch($id){
						case 95:
							$id = Item::GLASS;
							$damage = 0;
							break;
						case 125:
							$id = Item::DOUBLE_WOODEN_SLAB;
							break;
						case 126:
							$id = Item::WOODEN_SLAB;
							break;
						case 157:
							$id = Item::ACTIVATOR_RAIL;
							break;
						case 160:
							$id = Item::GLASS_PANE;
							$damage = 0;
							break;
						case 188:
							$id = Item::FENCE;
							$damage = Fence::FENCE_SPRUCE;
							break;
						case 189:
							$id = Item::FENCE;
							$damage = Fence::FENCE_BIRCH;
							break;
						case 190:
							$id = Item::FENCE;
							$damage = Fence::FENCE_JUNGLE;
							break;
						case 191:
							$id = Item::FENCE;
							$damage = Fence::FENCE_ACACIA;
							break;
						case 192:
							$id = Item::FENCE;
							$damage = Fence::FENCE_DARKOAK;
							break;
						case Item::STONE_BUTTON:
						case Item::WOODEN_BUTTON:
							break;
						case Item::TRAPDOOR:
						case Item::IRON_TRAPDOOR:
							$damage ^= 0x03;
							break;
					}
					$this->blocks_array[] = [$x, $y, $z, $id, $damage];
				}
			}
		}
	}
	private static function readByte($c, $i = 0){
		return ord($c{$i});
	}
	/**
	 *
	 * @return string
	 */
	public function getPath(){
		return $this->path;
	}
	/**
	 *
	 * @return mixed
	 */
	public function getBlocks(){
		return $this->blocks;
	}
	/**
	 *
	 * @return array
	 */
	public function getBlocksArray(){
		return $this->blocks_array;
	}
	/**
	 *
	 * @return mixed
	 */
	public function getData(){
		return $this->data;
	}
	/**
	 *
	 * @return int
	 */
	public function getHeight(){
		return $this->height;
	}
	/**
	 *
	 * @return int
	 */
	public function getLength(){
		return $this->length;
	}
	/**
	 *
	 * @return int
	 */
	public function getWidth(){
		return $this->width;
	}
}
