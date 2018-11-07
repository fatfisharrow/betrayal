<?php

include_once(dirname(__FILE__) . "/player.php");
include_once(dirname(__FILE__) . "/game.php");
include_once(dirname(__FILE__) . "/hero.php");

class Server {
    public static $nullptr = null;
    private $ws = null;
    private $mClients = array();
    private $mGames = array();
    private $mGameIndex = 0;

    public function Start($port) {
        $this->initGame();
        $this->ws = new swoole_websocket_server("0.0.0.0", $port);
        $this->ws->set(array(
            "daemonize" => 0,
            "reactor_num" => 1,
            "worker_num" => 1,
        ));
        $this->ws->on("open", array($this, "onOpen"));
        $this->ws->on("message", array($this, "onMessage"));
        $this->ws->on("close", array($this, "onClose"));
        logging::d("Server", "start socket.");
        $this->ws->start();
    }

    public function onOpen(swoole_server $server, $request) {
        logging::d("Server", "client connected.");
        logging::d("Server", $request);
        $this->createClient($request->fd);
    }

    public function onMessage(swoole_server $server, $frame) {
        logging::d("Server", "onMessage.");
        logging::d("Server", $frame);
        $player = $this->findPlayer($frame->fd);
        if ($player == null) {
            $this->ws->close($frame->fd, true);
            return;
        }

        if (!$player->onCommand($frame->data)) {
            $this->ws->close($frame->fd, true);
            return;
        }
    }

    public function onClose(swoole_server $server, $fd) {
        logging::d("Server", "webserver close: $fd.");
        $this->removeClient($fd);
    }


    public function loginSuccess(&$player) {
        $this->sendToPlayer($player, array("op" => "login", "data" => "success"));
    }

    public function sendGameList(&$player) {
        $games = array();
        foreach ($this->mGames as &$g) {
            $games []= $g->getSummary();
        }
        $data = array("op" => "games", "data" => $games);
        logging::d("Server", $data);
        $this->sendToPlayer($player, $data);
    }

    public function sendGameInfo(&$game, &$player) {
        $info = $game->getInfo();
        $this->sendToPlayer($player, array("op" => "gameinfo", "data" => $info));
    }

    public function &createGame(&$player, $title) {
        foreach ($this->mGames as &$g) {
            if ($g->getTitle() == $title) {
                $this->sendToPlayer($player, array("op" => "creategame", "data" => "fail"));
                return self::$nullptr;
            }
        }
        $game = new Game($this, $title, $this->mGameIndex);
        $game->attachPlayer($player);
        $this->mGames []= $game;
        $this->sendToPlayer($player, array("op" => "creategame", "data" => "success"));
        $this->mGameIndex++;
        return $game;
    }

    public function &joinGame(&$player, $gameid) {
        $game = null;
        foreach ($this->mGames as &$g) {
            if ($g->getId() == $gameid) {
                $game = $g;
                break;
            }
        }
        if ($game == null) {
            $this->sendToPlayer($player, array("op" => "join", "data" => "fail"));
            return null;
        } else {
            $game->attachPlayer($player);
            $game->broadcastInfo();
            $this->sendToPlayer($player, array("op" => "join", "data" => "success"));
            return $game;
        }
    }

    public function leaveGame(&$player) {
    }

    public function startGame(&$game) {
        $game->broadcast(array("op" => "start", "data" => "success"));
    }

    public function startFail(&$player) {
        $this->sendToPlayer($player, array("op" => "start", "data" => "fail"));
    }
    ///////////////////////////////////////////////////////

    private function send($fd, $data) {
        if (!is_string($data)) {
            $data = json_encode($data);
        }
        logging::d("Server", "send: $data");
        $ret = $this->ws->push($fd, $data);
        if (!$ret) {
            logging::e("Server", $this->ws->getLastError());
            removeClient($fd);
        }
    }

    public function sendToPlayer(&$player, $data) {
        $fd = $this->findSocket($player);
        logging::d("Server", "fd = $fd");
        if ($fd == null) {
            logging::fatal("No such player.");
            return;
        }
        $this->send($fd, $data);
    }

    private function removeClient($fd) {
        if (isset($this->mClients[$fd])) {
            unset($this->mClients[$fd]);
        }
    }

    private function createClient($fd) {
        $this->removeClient($fd);
        $this->mClients[$fd] = new Player($this);
        return $this->mClients[$fd];
    }

    private function findPlayer($fd) {
        if (isset($this->mClients[$fd])) {
            return $this->mClients[$fd];
        }
        return null;
    }

    private function findSocket(&$player) {
        foreach ($this->mClients as $fd => &$client) {
            if ($client == $player) {
                return $fd;
            }
        }
        return null;
    }

    private function initGame() {
        $this->mGameIndex = 10000;
    }
};




