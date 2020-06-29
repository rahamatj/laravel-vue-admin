<?php

namespace Tests\Feature\Sms;

use App\Sms\Exceptions\SmsApiNotSetException;
use App\Sms\Exceptions\SmsMessageNotSetException;
use App\Sms\Exceptions\SmsToNotSetException;
use App\Sms\Facades\Sms;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class SmsTest extends TestCase
{
    /** @test */
    public function builds_sms()
    {
        $api = 'http://smsapi.com?to={to}&message={message}';

        $sms = new \App\Sms\Sms($api);

        $reflection = new \ReflectionClass($sms);

        $toReflection = $reflection->getProperty('to');
        $toReflection->setAccessible(true);
        $toReflection->setValue($sms, 'test');

        $messageReflection = $reflection->getProperty('message');
        $messageReflection->setAccessible(true);
        $messageReflection->setValue($sms, 'test');

        $buildReflection = $reflection->getMethod('build');
        $buildReflection->setAccessible(true);

        $this->assertEquals(
            'http://smsapi.com?to=test&message=test',
            $buildReflection->invoke($sms)
        );
    }

    /** @test */
    public function throws_exception_building_sms_if_api_is_not_set()
    {
        $api = null;

        $sms = new \App\Sms\Sms($api);

        $reflection = new \ReflectionClass($sms);

        $toReflection = $reflection->getProperty('to');
        $toReflection->setAccessible(true);
        $toReflection->setValue($sms, 'test');

        $messageReflection = $reflection->getProperty('message');
        $messageReflection->setAccessible(true);
        $messageReflection->setValue($sms, 'test');

        $buildReflection = $reflection->getMethod('build');
        $buildReflection->setAccessible(true);

        $this->expectException(SmsApiNotSetException::class);

        $buildReflection->invoke($sms);
    }

    /** @test */
    public function throws_exception_building_sms_if_to_is_not_set()
    {
        $api = 'http://smsapi.com?to={to}&message={message}';

        $sms = new \App\Sms\Sms($api);

        $reflection = new \ReflectionClass($sms);

        $messageReflection = $reflection->getProperty('message');
        $messageReflection->setAccessible(true);
        $messageReflection->setValue($sms, 'test');

        $buildReflection = $reflection->getMethod('build');
        $buildReflection->setAccessible(true);

        $this->expectException(SmsToNotSetException::class);

        $buildReflection->invoke($sms);
    }

    /** @test */
    public function throws_exception_building_sms_if_message_is_not_set()
    {
        $api = 'http://smsapi.com?to={to}&message={message}';

        $sms = new \App\Sms\Sms($api);

        $reflection = new \ReflectionClass($sms);

        $toReflection = $reflection->getProperty('to');
        $toReflection->setAccessible(true);
        $toReflection->setValue($sms, 'test');

        $buildReflection = $reflection->getMethod('build');
        $buildReflection->setAccessible(true);

        $this->expectException(SmsMessageNotSetException::class);

        $buildReflection->invoke($sms);
    }

    /**
     * @test
     */
    public function sends_sms()
    {
        Sms::fake();
        Sms::to('test')
            ->message('test')
            ->send();

        Sms::assertSent(function ($sms) {
            return $sms->hasTo('test') &&
                $sms->hasMessage('test');
        });
    }

    /**
     * @test
     */
    public function queues_sms()
    {
        Sms::fake();
        Sms::to('test')
            ->message('test')
            ->queue();

        Sms::assertqueued(function ($sms) {
            return $sms->hasTo('test') &&
                $sms->hasMessage('test');
        });
    }
}
