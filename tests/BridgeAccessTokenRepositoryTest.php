<?php

use Carbon\Carbon;

class BridgeAccessTokenRepositoryTest extends PHPUnit_Framework_TestCase
{
    public function tearDown()
    {
        Mockery::close();
    }

    public function test_access_tokens_can_be_persisted()
    {
        $expiration = Carbon::now();

        $tokenRepository = Mockery::mock('DrewHavard\Passport\TokenRepository');

        $events = Mockery::mock('Illuminate\Contracts\Events\Dispatcher');

        $tokenRepository->shouldReceive('create')->once()->andReturnUsing(function ($array) use ($expiration) {
            $this->assertEquals(1, $array['id']);
            $this->assertEquals(2, $array['user_id']);
            $this->assertEquals('client-id', $array['client_id']);
            $this->assertEquals(['scopes'], $array['scopes']);
            $this->assertEquals(false, $array['revoked']);
            $this->assertInstanceOf('DateTime', $array['created_at']);
            $this->assertInstanceOf('DateTime', $array['updated_at']);
            $this->assertEquals($expiration, $array['expires_at']);
        });

        $events->shouldReceive('dispatch')->once();

        $accessToken = new DrewHavard\Passport\Bridge\AccessToken(2, [new DrewHavard\Passport\Bridge\Scope('scopes')]);
        $accessToken->setIdentifier(1);
        $accessToken->setExpiryDateTime($expiration);
        $accessToken->setClient(new DrewHavard\Passport\Bridge\Client('client-id', 'name', 'redirect'));

        $repository = new DrewHavard\Passport\Bridge\AccessTokenRepository($tokenRepository, $events);

        $repository->persistNewAccessToken($accessToken);
    }
}
