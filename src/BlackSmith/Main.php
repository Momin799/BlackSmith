<?php

namespace BlackSmith;

use pocketmine\plugin\PluginBase;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\command\ConsoleCommandSender;
use pocketmine\event\Listener;
use pocketmine\item\Item;
use pocketmine\item\Tool;
use pocketmine\item\Armor;
use pocketmine\Player;
use pocketmine\Server;
use pocketmine\utils\TextFormat as T;

use jojoe77777\FormAPI;
use onebone\economyapi\EconomyAPI;

class Main extends PluginBase implements Listener {
   
    public function onEnable(){
    $this->getServer()->getPluginManager()->registerEvents($this, $this);
    $this->getLogger()->info(T::AQUA . "Plugin Enabled");
    }

    public function onCommand(CommandSender $sender, Command $command, string $label, array $args) : bool{
    	if($sender instanceof Player){
        switch($command->getName()){
            case "blacksmith":
                $this->rruiform($sender);
        }
        return true;
    }
    return false;
 }
public function rruiform(Player $sender){
    $api = $this->getServer()->getPluginManager()->getPlugin("FormAPI");
    $form = $api->createSimpleForm(function(Player $sender, ?int $data){
             if(!isset($data)) return;
      switch($data){
    
                        case 0:
                            $this->repair($sender);
                            break;
                        case 1:
                            $this->rename($sender);
                            break;
                        case 2:
                            $this->setLore($sender);
                            break;
                        case 3:
                            break;
      }
    });
    $form->setTitle("§l§eBlacksmith");
    $form->addButton("§l§6REPAIR\n§l§9»» §r§oTap to Repair", 0, "textures/ui/smithing_icon");
    $form->addButton("§l§6RENAME\n§l§9»» §r§oTap to Rename", 0, "textures/ui/smithing_icon");
    $form->addButton("§l§6LORE\n§l§9»» §r§oTap to Change", 0, "textures/ui/smithing_icon");
    $form->addButton("§l§cEXIT\n§l§9»» §r§oTap to Exit");
    $form->sendToPlayer($sender);
 }
public function repair(Player $sender){
      $api = $this->getServer()->getPluginManager()->getPlugin("FormAPI");
      $f = $api->createCustomForm(function(Player $sender, ?array $data){
       if(!isset($data)) return;
      $economy = EconomyAPI::getInstance();
          $mymoney = $economy->myMoney($sender);
          $cash = $this->getConfig()->get("price-repair");
          $dg = $sender->getInventory()->getItemInHand()->getDamage();
          if($mymoney >= $cash * $dg){
        $economy->reduceMoney($sender, $cash * $dg);
          $index = $sender->getPlayer()->getInventory()->getHeldItemIndex();
    $item = $sender->getInventory()->getItem($index);
    $id = $item->getId();
     if($item instanceof Armor or $item instanceof Tool){
       if($item->getDamage() > 0){
            $sender->getInventory()->setItem($index, $item->setDamage(0));
					  $sender->sendMessage("§aYour item have been repaired");
					return true;
							}else{
								$sender->sendMessage("§cItem doesn't have any damage.");
								return false;
							}
							return true;
							}else{
								$sender->sendMessage("§cThis item can't repaired");
								return false;
						}
						return true;
						}else{
									$sender->sendMessage("§cYou don't have enough xp!");
									return true;
					}
					});
	   $mny = $this->getConfig()->get("price-repair");
          $dg = $sender->getInventory()->getItemInHand()->getDamage();
          $pc = $mny * $dg;
          $economy = EconomyAPI::getInstance();
          $mne = $economy->myMoney($sender);
          $f->setTitle("§e§lRepair Menu");
	  $f->addLabel("§aYour money: §7$mne \n§aPrice perDamage: §7$mny\n§aItem damage: §7$dg \n§aTotal money needed : §7$pc");
          $f->sendToPlayer($sender);
   }

public function rename(Player $sender){
            $api = $this->getServer()->getPluginManager()->getPlugin("FormAPI");
	    $f = $api->createCustomForm(function(Player $sender, ?array $data){
             if(!isset($data)) return;
		 $item = $sender->getInventory()->getItemInHand();
		  if($item->getId() == 0) {
                    $sender->sendMessage("§aHold item in hand!");
                    return;
                }
          $economy = EconomyAPI::getInstance();
          $mymoney = $economy->myMoney($sender);
          $rename = $this->getConfig()->get("price-rename");
          if($mymoney >= $rename){
	      $economy->reduceMoney($sender, $rename);
                $item->setCustomName(T::colorize($data[1]));
                $sender->getInventory()->setItemInHand($item);
                $sender->sendMessage("§asuccessfully changed item name to §r$data[1]");
                }else{
             $sender->sendMessage("§cYou don't have enough money!");
             }
	    });
	   
          $economy = EconomyAPI::getInstance();
          $mymoney = $economy->myMoney($sender);
          $rename = $this->getConfig()->get("price-rename");
	  $f->setTitle("§l§eRename Menu");
	  $f->addLabel("§aRename cost: §7$rename\n§aYour money: §7$mymoney");
          $f->addInput("§cRename Item:", "§eName");
	  $f->sendToPlayer($sender);
   }
public function setLore(Player $sender){
            $api = $this->getServer()->getPluginManager()->getPlugin("FormAPI");
	    $f = $api->createCustomForm(function(Player $sender, ?array $data){
             if(!isset($data)) return;
		 $item = $sender->getInventory()->getItemInHand();
		  if($item->getId() == 0) {
                    $sender->sendMessage("§cHold item in hand!");
                    return;
                }
          $economy = EconomyAPI::getInstance();
          $mymoney = $economy->myMoney($sender);
          $lore = $this->getConfig()->get("price-lore");
          if($mymoney >= $lore){
	      $economy->reduceMoney($sender, $lore);
                $item->setLore([$data[1]]);
                $sender->getInventory()->setItemInHand($item);
                $sender->sendMessage("§asuccessfully changed item lore to §r$data[1]");
                }else{
             $sender->sendMessage("§cYou don't have enough money!");
             }
	    });
	   
          $economy = EconomyAPI::getInstance();
          $mymoney = $economy->myMoney($sender);
          $lore = $this->getConfig()->get("price-lore");
	  $f->setTitle("§l§eCustom Lore");
	  $f->addLabel("§aSet lore cost: §e$lore\n§bYour money: $mymoney");
          $f->addInput("§cSetLore:", "§bYour Lore");
	  $f->sendToPlayer($sender);
   }
}
