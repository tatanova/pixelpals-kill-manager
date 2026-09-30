<?php
namespace ppls_kills;
use pocketmine\plugin\PluginBase;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerDeathEvent;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\Player;
class Pixel extends PluginBase implements Listener {
   public function onEnable(){
      $this->getServer()->getPluginManager()->registerEvents($this,$this);
      @mkdir($this->getDataFolder());
   }
   public function onDeath(PlayerDeathEvent $e){
      $v = $e->getEntity();
      if($v instanceof Player){
         $this->addKd($v, "d");
      }
      $cause = $e->getEntity()->getLastDamageCause()->getCause();
      if($cause == 1){
         $k = $e->getEntity()->getLastDamageCause()->getDamager();
         if($k instanceof Player){
            $this->addKd($k, "k");
         }
      }
   }
   public function onJoin(PlayerJoinEvent $e){
      $p = $e->getPlayer();
      if(!file_exists($this->getDataFolder().strtolower($p->getName()).".kd")){
         file_put_contents($this->getDataFolder().strtolower($p->getName()).".kd",gzdeflate(json_encode(array("k"=>0,"d"=>0))));
      }
   }
   public function getKd(Player $p){
      $dat = @json_decode(gzinflate(file_get_contents($this->getDataFolder().strtolower($p->getName()).".kd")), true);
      return $dat;
   }
   private function addKd(Player $p, $m = "k"){
      switch($m){
         case "k":
         $dat = $this->getKd($p);
         file_put_contents($this->getDataFolder().strtolower($p->getName()).".kd",gzdeflate(json_encode(array("k"=>$dat["k"]+1, "d"=>$dat["d"]))));
         return true;
         break;
         case "d":
         $dat = $this->getKd($p);
         file_put_contents($this->getDataFolder().strtolower($p->getName()).".kd",gzdeflate(json_encode(array("k"=>$dat["k"], "d"=>$dat["d"]+1))));
         return true;
         break;
         case "kd":
         $dat = $this->getKd($p);
         file_put_contents($this->getDataFolder().strtolower($p->getName()).".kd",gzdeflate(json_encode(array("k"=>$dat["k"]+1, "d"=>$dat["d"]+1))));
         return true;
         break;
         default:
         return false;
         break;
      }
   }
}