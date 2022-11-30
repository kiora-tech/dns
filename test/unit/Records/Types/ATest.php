<?php

namespace MamaOmida\Dns\Test\Unit\Records\Types;

use MamaOmida\Dns\Records\Types\A;
use MamaOmida\Dns\Test\Unit\Records\AbstractRecordTestClass;

/**
 * @property A $subject
 */
class ATest extends AbstractRecordTestClass
{
    public function setUp(): void
    {
        $this->subject = new A([]);
        parent::setUp();
    }

    public function testGetTarget()
    {
        $this->assertNull($this->subject->getIp());
    }

    public function testGetIpValue()
    {
        $value = '192.168.0.1';
        $this->subject->setData(['ip' => $value]);
        $this->assertSame($value, $this->subject->getIp());
    }

}