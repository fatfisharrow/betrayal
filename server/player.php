<?php

class Player {

    const STATUS_WAIT = 0;
    const STATUS_IDLE = 1;
    const STATUS_ROOM = 2;
    const STATUS_GAME = 3;
    const STATUS_BLACKLISTED = 4;

    private $mStatus;
    private $mServer;
    private $mNick;
    private $mHero;

    public function __construct(&$server) {
        $this->mStatus = self::STATUS_WAIT;
        $this->mServer = $server;
    }

    public function onCommand($command) {
        $cmd = json_decode($command, true);
        if (!isset($cmd["op"])) {
            return false;
        }

        if ($this->mStatus == self::STATUS_WAIT) {
            return $this->onWaitCommand($cmd);
        } else if ($this->mStatus == self::STATUS_IDLE) {
            return $this->onIdleCommand($cmd);
        } else if ($this->mStatus == self::STATUS_ROOM) {
            return $this->onRoomCommand($cmd);
        } else if ($this->mStatus == self::STATUS_GAME) {
            return $this->onGameCommand($cmd);
        } else {
        }
        return false;
    }

    private function onWaitCommand($command) {
        logging::d("Player.onWaitCommand", $command);
        if ($command["op"] == "login") {
            // TODO: do login
            // $this->mNick = "";
            $this->mStatus = self::STATUS_IDLE;
        } else {
            return false;
        }
        return true;
    }

    private function onIdleCommand($command) {
        logging::d("Player.onIdleCommand", $command);
        if ($command["op"] == "create") {
            $ret = $this->mServer->createGame($this, $command["title"]);
            if ($ret) {
                $this->mStatus = self::STATUS_ROOM;
            }
        } else if ($command["op"] == "refresh") {
            $this->mServer->sendGameList($this);
        } else if ($command["op"] == "join") {
            $gameid = $command["game"];
        } else {
            return false;
        }
        return true;
    }

    private function onRoomCommand($command) {
        $op = $command["op"];
        if ($op == "select") {
        } else if ($op == "leave") {
        } else {
            return false;
        }
        return true;
    }
    private function onGameCommand($command) {
        return true;
    }
};




