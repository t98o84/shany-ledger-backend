<?php

namespace Tests\Unit\Models\Shared;

use App\Models\Shared\Signature;
use Carbon\Carbon;
use PHPUnit\Framework\TestCase;
use Tests\CreatesApplication;

class SignatureTest extends TestCase
{
    use CreatesApplication;

    public function setUp(): void
    {
        parent::setUp();

        $this->createApplication();
    }

    public function testMake_StringValues_SignatureInstanceReturned(): void
    {
        $this->assertInstanceOf(Signature::class, Signature::make(['one'], Carbon::now()));
    }

    public function testMake_NumericalValues_SignatureInstanceReturned(): void
    {
        $this->assertInstanceOf(Signature::class, Signature::make([1], Carbon::now()));
    }

    public function testMake_MultidimensionalArray_SignatureInstanceReturned(): void
    {
        $this->assertInstanceOf(Signature::class, Signature::make(['one' => ['two'],], Carbon::now()));
    }

    public function testTampered_ValidData_FalseReturned(): void
    {
        $signature = Signature::make([1], Carbon::now());
        $this->assertFalse($signature->tampered());
    }

    public function testTampered_InvalidData_TrueReturned(): void
    {
        $now = Carbon::now();
        $signature = Signature::make([1], $now);
        $tamperedSignature = new Signature($signature->signature, [2], $now);

        $this->assertTrue($tamperedSignature->tampered());
    }

    public function testExpired_ValidData_FalseReturned(): void
    {
        $signature = Signature::make([1], Carbon::now()->addMinute());
        $this->assertFalse($signature->expired());
    }

    public function testExpired_InvalidData_TrueReturned(): void
    {
        $signature = Signature::make([1], Carbon::now()->subMinute());
        $this->assertTrue($signature->expired());
    }

    public function testValid_ValidData_TrueReturned(): void
    {
        $now = Carbon::now()->addMinute();
        $signature = Signature::make([1], $now);
        $tamperedSignature = new Signature($signature->signature, [1], $now);

        $this->assertTrue($tamperedSignature->valid());
    }

    public function testValid_ExpiredData_FalseReturned(): void
    {
        $now = Carbon::now()->subMinute();
        $signature = Signature::make([1], $now);
        $tamperedSignature = new Signature($signature->signature, [1], $now);

        $this->assertFalse($tamperedSignature->valid());
    }

    public function testValid_TamperedData_FalseReturned(): void
    {
        $now = Carbon::now()->addMinute();
        $signature = Signature::make([1], $now);
        $tamperedSignature = new Signature($signature->signature, [2], $now);

        $this->assertFalse($tamperedSignature->valid());
    }

    public function testValid_InvalidData_FalseReturned(): void
    {
        $now = Carbon::now()->subMinute();
        $signature = Signature::make([1], $now);
        $tamperedSignature = new Signature($signature->signature, [2], $now);

        $this->assertFalse($tamperedSignature->valid());
    }
}
