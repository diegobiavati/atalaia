# tests/Feature/HttpsProtocolTest.php
<?php

namespace Tests\Feature;

use Tests\TestCase;

class HttpsProtocolTest extends TestCase
{
    /** @test */
    public function o_sistema_deve_forcar_esquema_https()
    {
        $this->assertEquals('https', url('/')->getScheme());
    }

    /** @test */
    public function links_de_ativos_devem_ser_gerados_com_https()
    {
        $assetUrl = asset('css/app.css');
        $this->assertStringStartsWith('https://', $assetUrl);
    }
}