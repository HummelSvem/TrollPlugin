<?php

namespace SoartexHD;

use pocketmine\plugin\PluginBase;
use pocketmine\event\Listener;
use pocketmine\command\{Command, CommandSender};
use pocketmine\scheduler\PluginTask;
use pocketmine\Player;
use pocketmine\entity\Entity;
use pocketmine\event\player\PlayerMoveEvent;

use pocketmine\utils\TextFormat as C;

class troll extends PluginBase implements Listener {
	
	public $prefix = "§7[§4§lTROLL§r§7]§r";
	public $vanish = [];
	public $freeze = [];
	
	public function onEnable (){
		$this->getServer()->getPluginManager()->registerEvents ($this, $this);
		$this->getServer()->getScheduler()->scheduleRepeatingTask(new vanish($this), 1);
		$this->getLogger()->info("\n §» §7[§4§lTROLL§r§7]§r\n");
	}
	
	public function onMove(PlayerMoveEvent $event){
		
		if(in_array($event->getPlayer()->getName(), $this->freeze)){
			$event->setCancelled(true);
			}
	}
	
	public function onCommand (CommandSender $sender, Command $cmd, string $label, array $args) : bool {
		
		if($cmd->getName() == "troll"){
			if($sender instanceof Player){
			    if($sender->hasPermission("troll.use")){
				    if(empty($args[0])){
					$sender->sendMessage ("§7-----{$this->prefix}§7-----\n\n§c» §f/troll vanish <an | aus>\n§c» §f/troll freeze <Spieler>\n§c» §f/troll unfreeze <Spieler>\n§c» §f/troll gm <0 | 1 | 2 | 3>\n§c» §f/troll tp <Spieler>");
					}else{
					
						if($args[0] == "vanish"){	
                            if(empty($args[1])){
                            	 $sender->sendMessage ($this->prefix . " use: /troll vanish <an | aus>");
                            	}else{
						if($args[1] == "aus"){
						     unset($this->vanish[array_search($sender, $this->vanish)]);
						     foreach($this->getServer()->getOnlinePlayers() as $p3){
						     $p3->showplayer($sender);
						     }
					     	$sender->sendMessage ( $this->prefix . " Du bist jetzt §csichtbar");			
						}
						if($args[1] == "an"){
							$sender->sendMessage ( $this->prefix . " Du bist jetzt §aunsichtbar");
							$sender->sendMessage("\n§f-> Du kannst dich mit §6/troll vanish < aus >§f  wieder sichtbar machen");
							$this->vanish[] = $sender;
							}
						}	
					}
													
						if($args[0] == "gm"){
					     	if(empty($args[1])){
					     		$sender->sendMessage($this->prefix . " use: /troll gm < 0 | 1 | 2 | 3 >");
					     		}else{
					    	if($args[1] == "1"){
					    	$sender->setGamemode(1);
						    $sender->sendMessage($this->prefix . " Du bist nun im Gamemode §a1");
					        }
					        if($args[1] == "0"){
						    $sender->setGamemode(0);
						    $sender->sendMessage($this->prefix . " Du bist nun im Gamemode §a0");
						     }
						     if($args[1] == "2"){
							$sender->setGamemode(2);
						    $sender->sendMessage($this->prefix . " Du bist nun im Gamemode §a2");
							 }
							if($args[1] == "3"){
							$sender->setGamemode(3);
						    $sender->sendMessage($this->prefix . " Du bist nun im Gamemode §a3");
							 }
						 }
					 }
					
						if($args[0] == "freeze"){
						    if(empty($args[1])){
						    $sender->sendMessage($this->prefix . " use: /troll freeze <name>");
						}else{
					    $sender->sendMessage($this->prefix . " Du hast §a{$args[1]}§f eingefrohren"); 
						if(file_exists($this->getServer()->getDataPath()."players/".strtolower($args[1]).".dat")){
						$this->freeze[] = $args[1];
						          }else{
							      $sender->sendMessage ($this->prefix . " Der Spieler §a{$args[1]}§f ist nicht online oder existiert nicht!");
						          }
						     }
						}
						
						if($args[0] == "unfreeze"){
						    if(empty($args[1])){
							$sender->sendMessage ($this->prefix . " use: /troll unfreeze <Spieler>");
						}else{
						$sender->sendMessage($this->prefix . " Du hast §a{$args[1]}§f aufgetaut");
						if(in_array($args[1], $this->freeze)){
						unset($this->freeze[array_search($args[1], $this->freeze)]);
						          }
						     }
						}
						
						//Noch nicht fertig bzw geht nicht 
						if($args[0] == "knock"){
						    if(empty($args[1])){
							$sender->sendMessage ($this->prefix . " use: /troll knock <Spieler>");
						}else{
						$sender->sendMessage($this->prefix . " Du hast §a{$args[1]}§f einen Rückstoß geben!");
						if(file_exists($this->getServer()->getDataPath()."players/".$args[1].".dat")){
						$sender->knockBack($args[1], 0, 1, 0, 1);
                        $sender->knockBack($args[1], 0, 0, 1, 1);
                        $sender->knockBack($args[1], 0, -1, 0, 1);
                        $sender->knockBack($args[1], 0, 0, -1, 1);
                        $sender->knockBack($args[1], 0, 0, 0, 0);
                        $sender->knockBack($args[1], 0, 0, 1, 1);
                        $sender->knockBack($args[1], 0, 0, 0, 0);
                                  }
						     }
						}
						
						if($args[0] == "tp"){
							if(empty($args[1])){
								$sender->sendMessage($this->prefix . " use: /troll tp <Spieler>");
							}else{
								if(file_exists($this->getServer()->getDataPath()."players/".$args[1].".dat")){
									if($this->getServer()->getPlayerExact($args[1]) == null){
										$sender->sendMessage($this->prefix . " Spieler §a{$args[1]} §fist nicht Online");
									}else{
										$sender->sendMessage($this->prefix . " Du wurdest zu §a{$args[1]}§f Teleportiert.");
										$sender->setGamemode(1);
										$sender->teleport($this->getServer()->getPlayerExact($args[1]));
									}
							}
					}
			}
	}
						
				}else{
					$sender->sendMessage ($this->prefix . " Kein Recht");
					}
			}else{
			    $this->getLogger()->info("Die Konsole kann nicht Trollen.");
			}
			return true;
		}
		
	}

}

class vanish extends PluginTask{
public function __construct(troll $plugin) {
        parent::__construct($plugin);
        $this->plugin = $plugin;
    }

    public function onRun(int $currintTick) {

    foreach($this->plugin->vanish as $p){
    foreach($this->plugin->getServer()->getOnlinePlayers() as $p2){
    $p2->hideplayer($p);
    }    
    }    
    }
    }
