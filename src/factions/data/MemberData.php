<?php
/*
 *   FactionsPE: PocketMine-MP Plugin
 *   Copyright (C) 2016  Chris Prime
 *
 *   This program is free software: you can redistribute it and/or modify
 *   it under the terms of the GNU General Public License as published by
 *   the Free Software Foundation, either version 3 of the License, or
 *   (at your option) any later version.
 *
 *   This program is distributed in the hope that it will be useful,
 *   but WITHOUT ANY WARRANTY; without even the implied warranty of
 *   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *   GNU General Public License for more details.
 *
 *   You should have received a copy of the GNU General Public License
 *   along with this program. If not, see <http://www.gnu.org/licenses/>.
 */

namespace factions\data;

use factions\entity\Faction;
use factions\entity\Member;
use factions\FactionsPE;
use factions\relation\Relation;
use pocketmine\Player;

class MemberData extends Data
{

    /** @var int */
    protected $lastActivity;

    /** @var string */
    protected $factionId = Faction::NONE;

    /** @var string */
    protected $name;

    /**
     * When the player first time played in faction
     * @var int
     */
    protected $firstPlayed;

    /** @var int */
    protected $lastPlayed;

    // Add $onlineTime # TODO

    /** @var int */
    protected $power = 0;
    protected $powerBoost = 0;

    /** @var int */
    protected $role = Relation::NONE;

    /** @var string */
    protected $title;

    /**
     * @var Player|null
     */
    protected $player;

    /** @var boolean */
    protected $overriding = false;

    public function __construct(array $source)
    {
        $this->firstPlayed = $source["firstPlayed"] ?? time();
        $this->lastPlayed = $source["lastPlayed"] ?? time();
        $this->power = $source["power"] ?? $this->getDefaultPower(); # What if not extended by IMember ?
        $this->title = $source["title"] ?? null;
        if (isset($source["player"])) {
            if ($source["player"] instanceof Player) {
                $this->player = $source["player"];
            } else {
                $this->player = FactionsPE::get()->getServer()->getPlayer($source["player"]);
            }
        }
        if ($this->player) {
            $this->name = $this->player->getName();
        } else {
            $this->name = $source["name"];
        }
    }

    public function getPlayer()
    {
        return $this->player;
    }

    public function __toArray()
    {
        return [
            "firstPlayed" => $this->firstPlayed,
            "lastPlayed" => $this->lastPlayed,
            "power" => $this->power,
            "title" => $this->title,
            "name" => $this->name
        ];
    }

    /**
     * To save space on drive, we check if it's actually needed to save the player
     * @return bool
     */
    public function isDefault(): bool
    {
        /** @var $this Member */
        if ($this->hasFaction()) return false;
        if ($this->hasTitle()) return false;
        if ($this->getPower() !== $this->getDefaultPower()) return false;
        if ($this->getPowerBoost() !== 0) return false;
        return true;
    }

    public function hasTitle(): bool
    {
        return !empty($this->getTitle());
    }

    /*
     * ----------------------------------------------------------
     * NAME
     * ----------------------------------------------------------
     */

    public function getTitle(): string
    {
        return $this->title ?? "";
    }

    /*
     * ----------------------------------------------------------
     * TITLE
     * ----------------------------------------------------------
     */

    public function setTitle(string $title)
    {
        $this->title = $title;
    }

    public function save()
    {
        FactionsPE::get()->getDataProvider()->saveMember($this);
    }

    public function getName(): string
    {
        return $this->player ? $this->player->getName() : $this->name;
    }

    /*
     * ----------------------------------------------------------
     * TIMES
     * ----------------------------------------------------------
     */

    public function getLastPlayed(): int
    {
        return $this->lastPlayed;
    }

    public function setLastPlayed(int $time)
    {
        $this->lastPlayed = $time;
    }

    public function getFirstPlayed(): int
    {
        return $this->firstPlayed;
    }

    /*
     * ----------------------------------------------------------
     * LAST-ACTIVITY
     * ----------------------------------------------------------
     */

    public function getLastActivity(): int
    {
        return $this->lastActivity;
    }

    public function setLastActivity($lastActivity)
    {
        $this->lastActivity = $lastActivity;
    }

}