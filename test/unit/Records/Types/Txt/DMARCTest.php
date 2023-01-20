<?php

namespace Unit\Records\Types\Txt;

use BlueLibraries\Dns\Records\ExtendedTxtRecords;
use BlueLibraries\Dns\Records\Types\Txt\DMARC;
use BlueLibraries\Dns\Test\Unit\Records\AbstractRecordTestClass;

/**
 * @property DMARC $subject
 */
class DMARCTest extends AbstractRecordTestClass
{
    public function setUp(): void
    {
        $this->subject = new DMARC([]);
        parent::setUp();
    }

    public function testGetTxt()
    {
        $this->assertNull($this->subject->getTxt());
    }

    public function testGetIpValue()
    {
        $value = 'random text here';
        $this->subject->setData(['txt' => $value]);
        $this->assertSame($value, $this->subject->getTxt());
    }

    public function testToStringDefault()
    {
        $this->assertSame('0 IN TXT', $this->subject->toString());
    }

    public function testToStringComplete()
    {
        $this->subject->setData(
            [
                'ttl'  => 7200,
                'host' => 'test.com',
                'txt'  => 'text here'
            ]
        );
        $this->assertSame('test.com 7200 IN TXT "text here"', $this->subject->toString());
    }

    public function testToStringCompleteWithChaosClass()
    {
        $this->subject->setData(
            [
                'ttl'   => 7200,
                'class' => 'CH',
                'host'  => 'test.com',
                'txt'   => 'text here'
            ]
        );
        $this->assertSame('test.com 7200 CH TXT "text here"', $this->subject->toString());
    }

    public function testGetEmptyText()
    {
        $this->subject->setData(
            [
                'ttl'   => 7200,
                'class' => 'IN',
                'host'  => 'test.com',
                'txt'   => ''
            ]
        );
        $this->assertSame('test.com 7200 IN TXT ""', $this->subject->toString());
    }

    public function testGetExtendedTypeName()
    {
        $this->assertSame(ExtendedTxtRecords::DMARC, $this->subject->getTypeName());
    }

    public function parseValuesDataProvider(): array
    {
        return [
            ['', false],
            ['p', false],
            ['p=none', false],
            ['v=DMARC1; ', false],
            ['v=DMARC1; p=reject', true]
        ];
    }

    /**
     * @param $txt
     * @param $expected
     * @dataProvider parseValuesDataProvider
     * @return void
     */
    public function testParseValues($txt, $expected)
    {
        $this->subject->setData(
            [
                'ttl'   => 7200,
                'class' => 'IN',
                'host'  => '_dmarc.test.com',
                'txt'   => $txt
            ]
        );

        $this->assertSame($expected, $this->subject->parseValues());
    }

    public function valuesDataProvider(): array
    {
        return [
            ['', []],
            ['p=reject; ', ['p' => 'reject']],
            ['v=DMARC1; ', ['v' => 'DMARC1']],
            ['v=DMARC1; p=none', ['v' => 'DMARC1', 'p' => 'none']],
            [
                'v=DMARC1; p=quarantine;pct=75; rua=mailto:postmaster@test.com; ruf=mailto:ruf@test.com; sp=reject;fo=d; aspf=s;adkim=r; rf=afrf;ri=86400 ',
                [
                    'v'     => 'DMARC1',
                    'p'     => 'quarantine',
                    'pct'   => 75,
                    'rua'   => 'mailto:postmaster@test.com',
                    'ruf'   => 'mailto:ruf@test.com',
                    'sp'    => 'reject',
                    'fo'    => 'd',
                    'aspf'  => 's',
                    'adkim' => 'r',
                    'rf'    => 'afrf',
                    'ri'    => 86400
                ]],
        ];
    }

    private function getKeyValues(): array
    {
        return ['v', 'p', 'pct', 'rua', 'ruf', 'sp', 'fo', 'aspf', 'adkim', 'rf', 'ri'];
    }

    /**
     * @param string $txt
     * @param array $expected
     * @dataProvider valuesDataProvider
     * @return void
     */
    public function testValues(string $txt, array $expected)
    {

        $this->subject->setData(
            [
                'ttl'   => 7200,
                'class' => 'IN',
                'host'  => '_dmarc.test.com',
                'txt'   => $txt
            ]
        );

        $keyValues = $this->getKeyValues();

        foreach ($keyValues as $key) {
            $expectedValue = $expected[$key] ?? null;


            switch ($key) {

                case DMARC::VERSION:
                    $this->assertSame($expectedValue, $this->subject->getVersion());
                    break;

                case DMARC::POLICY:
                    $this->assertSame($expectedValue, $this->subject->getPolicy());
                    break;

                case DMARC::PERCENTAGE:
                    $this->assertSame($expectedValue, $this->subject->getPercentage());
                    break;

                case DMARC::RUA:
                    $this->assertSame($expectedValue, $this->subject->getRua());
                    break;

                case DMARC::RUF:
                    $this->assertSame($expectedValue, $this->subject->getRuf());
                    break;

                case DMARC::FO:
                    $this->assertSame($expectedValue, $this->subject->getFo());
                    break;

                case DMARC::ASPF:
                    $this->assertSame($expectedValue, $this->subject->getAspf());
                    break;

                case DMARC::ADKIM:
                    $this->assertSame($expectedValue, $this->subject->getAdkim());
                    break;

                case DMARC::REPORT_FORMAT:
                    $this->assertSame($expectedValue, $this->subject->getReportFormat());
                    break;

                case DMARC::REPORT_INTERVAL:
                    $this->assertSame($expectedValue, $this->subject->getReportInterval());
                    break;

                case DMARC::SUBDOMAIN_POLICY:
                    $this->assertSame($expectedValue, $this->subject->getSubdomainPolicy());
                    break;

            }
        }
    }

}
