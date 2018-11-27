<?php

namespace Juice\Tests;

use Hashids\Hashids;

class ServiceProviderTest extends TestCase
{
    public function test_service_provider_loaded()
    {
        $provider = \Juice\Attachments\AttachmentsServiceProvider::class;

        $loaded = $this->app->getLoadedProviders();

        $this->assertArrayHasKey($provider, $loaded);

        $this->assertTrue($loaded[$provider]);
    }

    public function test_hashids_registered_correctly()
    {
        $salt = str_random();

        $this->app['config']->set('juice-attachments.hashids-salt', $salt);

        $hashids = new Hashids($salt, 5);

        $this->assertSame(
            $hashids->encode(1),
            $this->app['ja-hashids']->encode(1)
        );
    }
}
