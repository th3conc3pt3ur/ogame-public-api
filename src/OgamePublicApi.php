<?php
namespace OgamePublicApi;

use Symfony\Component\DomCrawler\Crawler;

class OgamePublicApi
{
    const CACHE_PATH = __DIR__."/cache/";
    
    private $univers;
    private $locale;
    private $activeCache;
    
    /**
     * @param int $univers
     * @param string $locale
     */
    public function __construct($univers, $locale, $activeCache = true)
    {
        $this->univers = $univers;
        $this->locale = $locale;
        $this->activeCache = $activeCache;

        $this->init();
    }

    private function init()
    {
        if ($this->activeCache) {
            // warm up cache
            if (!file_exists(self::CACHE_PATH.$this->univers)) {
                // create cache folder of univers
                mkdir(self::CACHE_PATH.$this->univers);
            }
            //init cache for alliances, players
            $this->getAlliances();
            $this->getPlayers();
        }
    }

    /**
     * @param string $type endpoint key
     * @param string $file name of the file
     * @param string $id = id of the player for playerData
     * @return xmlDom
     */
    private function getDataFromCache(string $type, string $file, string $id = null)
    {
        $path = self::CACHE_PATH.$this->univers.DIRECTORY_SEPARATOR.$file.".xml";
        if (file_exists($path) && $this->cacheValid($type, $path)) {
            $data = file_get_contents($path);
        } else {
            switch ($type) {
                case "players":
                    $data = file_get_contents("https://s".$this->univers."-".$this->locale.".ogame.gameforge.com/api/".$type.".xml");
                    break;
                case "player":
                    $data = file_get_contents("https://s".$this->univers."-".$this->locale.".ogame.gameforge.com/api/playerData.xml?id=".$id);
                    break;
                case "alliances":
                    $data = file_get_contents("https://s".$this->univers."-".$this->locale.".ogame.gameforge.com/api/".$type.".xml");
                    break;
            }
            if ($this->activeCache) {
                file_put_contents($path, $data);
            }
        }
        return $data;
    }
    /**
     * @param string $type Type of file
     * @param $path path of the file
     * @return bool if true cache is still ok, if false we need to regenerate
     * Return in second the time of cache expiration
     */
    private function cacheValid($type, $path)
    {
        $interval = 0;
        switch ($type) {
            case "players":
                //Updateinterval 1 day
                $interval = 24*3600;
                break;
            case "player":
                //Updateinterval 1 week
                $interval =  24*7*3600;
                break;
            case "alliances":
                //Updateinterval 1 day
                $interval = 24*3600;
                break;
        }
        $timestampLastUpdate = filemtime($path);
        $now = time();
        if (($now - $timestampLastUpdate) >= $interval) {
            return false;
        } else {
            return true;
        }
    }
    /**
     * Write player data in the json file
     */
    private function writeDataToCache($file, $data)
    {
        $path = self::CACHE_PATH.$this->univers.DIRECTORY_SEPARATOR.$file.".json";
        if (!file_exists($path)) {
            file_put_contents($path, $data);
        } else {
            // if file exist we check interval
            $timestampLastUpdate = filemtime($path);
            $now = time();
            // player data interval = 1 day , if > we rewrite the file
            $interval =  24*3600;
            if (($now - $timestampLastUpdate) >= $interval) {
                file_put_contents($path, $data);
            }
        }
    }

    private function updatePlayerWithData($id, $_data)
    {
        $path = self::CACHE_PATH.$this->univers.DIRECTORY_SEPARATOR."player_".$id.".json";
        $data = file_get_contents($path);
        $data = json_decode($data, true);
        $player = new Player($data);
        $player->setData($_data);
        file_put_contents($path, json_encode($player, JSON_PRETTY_PRINT));
    }

    /**
     * Only avaiable if cache is activated
     * @param $id id of player
     * Return player class convert from json with Planet and Moon
     */
    public function getPlayer($id)
    {
        if (!$this->activeCache) {
            throw new \Exception("This function is unavaiable is you have cache desactived");
        }
        $path = self::CACHE_PATH.$this->univers.DIRECTORY_SEPARATOR."player_".$id.".json";

        $data = json_decode(file_get_contents($path), true);
        $player = new Player($data);
        $tabData = [];
        if (empty($player->getData())) {
            // we need to call getPlayerData() for refresh cache
            $player->setData($this->getPlayerData($id));
            return $player;
        }
        foreach ($player->getData() as $planet) {
            $moon = null;
            if ($planet['moon'] != null) {
                $moon = new Moon($planet['moon']);
            }

            $myPlanet = new Planet($planet);
            $myPlanet->setMoon($moon);
            $tabData[] = $myPlanet;
        }
        $player->setData($tabData);
        return $player;
    }
    /**
     * url of the public api = https://s$univers-$locale.ogame.gameforge.com/api/players.xml => list of player
     * @return Player[]
     */
    public function getPlayers()
    {
        $data = $this->getDataFromCache('players', 's'.$this->univers."-".$this->locale);
        $crawler = new Crawler($data);
        $players = $crawler->filterXPath('players/player');
        $playerTab = [];
        foreach ($players as $node) {
            $tabData = [
                'id' => $node->getAttribute('id'),
                'name' => $node->getAttribute('name'),
                'status' => $node->getAttribute('status'),
                'alliance' => $node->getAttribute('alliance'),
                'data' => null
            ];
            $myPlayer = new Player($tabData);
            $playerTab[] = $myPlayer;
            if ($this->activeCache) {
                $this->writeDataToCache('player_'.$node->getAttribute('id'), json_encode($tabData));
            }
        };
        return $playerTab;
    }

    public function getPlayerData($id)
    {
        $data = $this->getDataFromCache('player', $id, $id);
        $crawler = new Crawler($data);
        $tabData = [];
        $planets = $crawler->filterXPath('playerData/planets/planet');
        foreach ($planets as $node) {
            $moons = $node->childNodes;
            $myMoon = null;
            foreach ($moons as $moon) {
                $myMoon = new Moon(['id' => $moon->getAttribute('id'),'name' => $moon->getAttribute('name'),'size' => $moon->getAttribute('size')]);
            }
            $tabData[] = new Planet(['id' => $node->getAttribute('id'),'name' => $node->getAttribute('name'),'coords' => $node->getAttribute('coords'),'moon' => $myMoon]);
        }
        if ($this->activeCache) {
            $this->updatePlayerWithData($id, $tabData);
        }
        
        return $tabData;
    }

    public function getAlliances()
    {
        $data = $this->getDataFromCache('alliances', 'alliance_'.$this->univers."-".$this->locale);
        $crawler = new Crawler($data);
        $alliances = $crawler->filterXPath('alliances/alliance');
        $alliancesTab = [];
        foreach ($alliances as $node) {
            $players = $node->childNodes;
            $tabPlayer = null;
            foreach ($players as $player) {
                $tabPlayer[] = $player->getAttribute('id');
            }
            $alliancesTab[] = new Alliance([
                'id' => $node->getAttribute('id'),
                'name' => $node->getAttribute('name'),
                'tag' => $node->getAttribute('tag'),
                'founder' => $node->getAttribute('founder'),
                'foundDate' => $node->getAttribute('foundDate'),
                'homePage' => $node->getAttribute('homePage'),
                'logo' => $node->getAttribute('logo'),
                'open' => $node->getAttribute('open'),
                'players' => $tabPlayer
            ]);
        };
        return $alliancesTab;
    }
}
