<?php

namespace Taoran\Laravel\Jwt;

use SessionHandlerInterface;
use Carbon\Carbon;

class JwtAuthSession implements SessionHandlerInterface
{
    public function open($savePath, $sessionName)
    {
        return true;
    }

    public function close()
    {
        return true;
    }

    public function read($sessionId)
    {
        return serialize(\Cache::get(config('session.cookie') . ':' . $sessionId));
    }

    public function write($sessionId, $data)
    {
        $get_data = \Cache::get(config('session.cookie') . ':' . $sessionId);
        if ($get_data) {
            $data = array_merge(unserialize($data), $get_data);
        } else {
            $data = unserialize($data);
        }
        \Cache::put(config('session.cookie') . ':' . $sessionId, $data, Carbon::now()->addMinutes(config('session.lifetime')));
        return true;
    }

    public function destroy($sessionId)
    {
        return \Cache::forget(config('session.cookie') . ':' . $sessionId);
    }

    public function gc($lifetime)
    {
        return true;
    }
}