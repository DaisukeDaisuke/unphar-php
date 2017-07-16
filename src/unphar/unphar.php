<?php
namespace unphar;

use pocketmine\plugin\PluginBase;
use pocketmine\event\Listener;
use pocketmine\Player;
use pocketmine\Server;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\command\ConsoleCommandSender;
//use pocketmine\event\player\PlayerCommandPreprocessEvent;

use pocketmine\scheduler\CallbackTask;
use pocketmine\scheduler\PluginTask;

class unphar extends PluginBase implements Listener{

	public function onEnable(){
		$this->getServer()->getPluginManager()->registerEvents($this, $this);
		if(!file_exists($this->getDataFolder()))
		mkdir($this->getDataFolder(), 0744, true);
		
		if(!file_exists($this->getDataFolder()."target".DIRECTORY_SEPARATOR))
		mkdir($this->getDataFolder()."target".DIRECTORY_SEPARATOR, 0744, true);
		
		if(!file_exists($this->getDataFolder()."output".DIRECTORY_SEPARATOR))
		mkdir($this->getDataFolder()."output".DIRECTORY_SEPARATOR, 0744, true);
	}
	public function onCommand(CommandSender $sender, Command $command, $label, array $args){
		switch(strtolower($label)){
			case "unphar":
				if($sender instanceof ConsoleCommandSender){
					$this->unphar();
					return true;
				}else{
					if($sender->isOP())
					$sender->sendMessage("コンソールから使用しましょう。");
					return true;
				}
			break;
		}
	}

	public function unphar(){
		$this->getLogger()->info("unphar - start");
		$slash = DIRECTORY_SEPARATOR;
		foreach(glob($this->getDataFolder()."target".$slash."*.*") as $path){
			$cash = explode(".",$path);
			if($cash[count($cash)-1] === "phar"){
				$this->getLogger()->info("unphar - ".str_replace($this->getDataFolder()."target".$slash,'',$path));
				$pharPath = "phar://".$path.$slash;
				$this->extractphar($pharPath,$path,$slash);
			}
		}
		$this->getLogger()->info("unphar - exit.");
	}
		public function extractphar($targetfile,$path,$slash){
		if(is_dir($targetfile) && $handle = opendir($targetfile)){
			while(($file = readdir($handle)) !== false){
				if(filetype($target = $targetfile.$file) == "file"){
					$cash = str_replace(".phar",'',$path);
					$cash = explode($slash,$cash);
					$subpath = substr($target, strlen("phar://$path".$slash));
					$output = $this->getDataFolder()."output".$slash.$cash[count($cash)-1].$slash.$subpath;
					if(!file_exists(dirname($output))){
						mkdir(dirname($output), 0744, true);//
					}
					if(!copy($target,$output)){
						$this->getLogger()->info("error 展開が出来ませんでした... $target --> $output");
					}
				}else{
					$this->extractphar($target.$slash,$path,$slash);
				}
			}
		}
	}
}