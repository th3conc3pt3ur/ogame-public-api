# Ogame Public Api Class
PHP class for retreive info on the GameForge's OGame public api

## Installation
`composer require theconcepteur/ogame-public-api:dev-master`

## Usage
the class contruct take 3 arguments : number of the server,locale, and boolean for activated the cache or not (strongly recommanded !)

```php
use OgamePublicApi\OgamePublicApi;
$og = new OgamePublicApi(179,'fr',true);
```
## API
Get list of player (call at init)
```php
$og->getPlayers()
```
Get list of Alliances (call at init)
```php
$og->getAlliances
```

Retrieve info about Planet and Moon of the Player with id = $id
```php
$og->getPlayerData($id)
```

With cache system on, you can retrieves player data along with his planets and moon in one call :
```php
$og->getPlayer($id)
```

## Cache System
This class provide a built-in cache system. 2 cache file type exist : `xml` and `json`

`xml` cache the ogame api file

`json` cache the internal system file

At class init we build the alliances and players list, and we pre-create the `player_X.json` file.

When we call the `$og->getPlayerData($id)` we write the `xml` file of this call AND we update the `player_X.json` with this data.

This way we have a complete file with all players data.

Each api call have a related files and this files have different cache expiration time. This time is configure in the `cacheValid` function accordingly to the info we have on the official interval.

