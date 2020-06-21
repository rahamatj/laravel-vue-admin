<?php

namespace App\Sms;

use PHPUnit\Framework\Assert as PHPUnit;

class SmsFake
{
    protected $to;
    protected $message;
    protected $sent = [];
    protected $queued = [];

    public function assertSent($callable, $times = null)
    {
        if (is_numeric($times))
            return $this->assertSentTimes($callable, $times);

        PHPUnit::assertTrue($this->sent($callable)->count() > 0);
    }

    public function assertQueued($callable, $times = null)
    {
        if (is_numeric($times))
            return $this->assertQueuedTimes($callable, $times);

        PHPUnit::assertTrue($this->queued($callable)->count() > 0);
    }

    public function assertNotSent($callable)
    {
        PHPUnit::assertCount(0, $this->sent($callable));
    }

    public function assertNotQueued($callable)
    {
        PHPUnit::assertCount(0, $this->queued($callable));
    }

    protected function assertSentTimes($callable, $times)
    {
        $count = $this->sent($callable)->count();

        PHPUnit::assertSame($times, $count);
    }

    protected function assertQueuedTimes($callable, $times)
    {
        $count = $this->queued($callable)->count();

        PHPUnit::assertSame($times, $count);
    }

    protected function sent($callable)
    {
        return collect($this->sent)->filter(function ($sms) use ($callable) {
            return $callable($sms);
        });
    }

    protected function queued($callable)
    {
        return collect($this->queued)->filter(function ($sms) use ($callable) {
            return $callable($sms);
        });
    }

    public function to($to)
    {
        $this->to = $to;

        return $this;
    }

    public function message($message)
    {
        $this->message = $message;

        return $this;
    }

    public function send()
    {
        $this->sent[] = $this;
    }

    public function queue()
    {
        $this->queued[] = $this;
    }

    public function hasTo($to)
    {
        return $this->to == $to;
    }

    public function hasMessage($message)
    {
        return $this->message == $message;
    }
}