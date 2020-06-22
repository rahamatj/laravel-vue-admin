<?php

namespace App\Sms;

use App\Sms\Exceptions\SmsApiNotSetException;
use App\Sms\Exceptions\SmsApiStringFormatException;
use App\Sms\Exceptions\SmsMessageNotSetException;
use App\Sms\Exceptions\SmsToNotSetException;
use App\Sms\Jobs\SendQueuedSms;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class Sms
{
    protected $api;
    protected $to;
    protected $message;

    public function __construct($api)
    {
        $this->api = $api;
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
        return Http::get($this->build())->throw();
    }

    public function queue()
    {
        SendQueuedSms::dispatch($this);
    }

    protected function build()
    {
        if (!$this->to)
            throw new SmsToNotSetException('SMS to is not set.');

        if (!$this->message)
            throw new SmsMessageNotSetException('SMS message is not set');

        if (!$this->api)
            throw new SmsApiNotSetException('SMS API is not set.');

        if (!Str::containsAll($this->api, ['{to}', '{message}']))
            throw new SmsApiStringFormatException('SMS API string does not contain {to} and {message} placeholders.');

        return Str::of($this->api)
            ->replaceFirst('{to}', $this->to)
            ->replaceFirst('{message}', $this->message);
    }
}