<?php

namespace PhpOffice\PhpSpreadsheetTests\Helper\NumberFormat;

use PhpOffice\PhpSpreadsheet\Helper\NumberFormat\Number;
use PHPUnit\Framework\TestCase;

class NumberWithIntlTest extends TestCase
{
    public static function setUpBeforeClass(): void
    {
        if (!extension_loaded('intl')) {
            self::markTestSkipped('The Intl extension is not available');
        }
    }

    /**
     * @group intl
     * @dataProvider numberMaskWithIntlData
     */
    public function testNumberMaskWithIntl(string $expectedResult, ...$args): void
    {
        $numberFormatter = new Number(...$args);
        $numberFormatMask = $numberFormatter->format();
        self::assertSame($expectedResult, $numberFormatMask);
    }

    public function numberMaskWithIntlData()
    {
        return [
            'Default' => ['#,##0.00'],
            'US Default' => ['#,##0.00', 'en_US'],
            'US 4 Decimals' => ['#,##0.0000', 'en_US', 4],
            'US No Decimals' => ['#,##0', 'en_US', 0],
            'US 2 Decimals, No Thousands' => ['0.00', 'en_US', 2, false],
            'US No Decimals, No Thousands' => ['0', 'en_US', 0, false],
            'India lakh, 2-2-3 Grouping' => ['#,##,##0.00', 'gu_IN'],
            'India lakh, No thousands' => ['0.00', 'gu_IN', 2, false],
        ];
    }

    /**
     * @group intl
     * @dataProvider numberMaskWithIntlDataManualOverrides
     */
    public function testNumberMaskWithIntlManualOverrides(string $expectedResult, string $locale, array $args): void
    {
        $numberFormatter = new Number($locale);

        foreach ($args as $methodName => $methodArgs) {
            $numberFormatter->{$methodName}(...$methodArgs);
        }

        $numberFormatMask = $numberFormatter->format();
        self::assertSame($expectedResult, $numberFormatMask);
    }

    public function numberMaskWithIntlDataManualOverrides()
    {
        return [
            'Override to integer implicit zero' => [
                '#,##0',
                'en_US',
                [
                    'setDecimals' => [],
                ],
            ],
            'Override to integer explicit zero' => [
                '#,##0',
                'en_US',
                [
                    'setDecimals' => [0],
                ],
            ],
            'No thousands separator' => [
                '0.00',
                'en_US',
                [
                    'useThousandsSeparator' => [false],
                ],
            ],
            'Trailing negative' => [
                '#,##0.00;#,##0.00-',
                'en_US',
                [
                    'trailingSign' => [true],
                ],
            ],
            'Trailing negative with separator' => [
                '#,##0.00;#,##0.00 -',
                'en_US',
                [
                    'trailingSign' => [true, Number::NON_BREAKING_SPACE],
                ],
            ],
            'Leading negative with separator' => [
                '#,##0.00;- #,##0.00',
                'en_US',
                [
                    'trailingSign' => [false, Number::NON_BREAKING_SPACE],
                ],
            ],
            'Leading sign without separator' => [
                '+#,##0.00;-#,##0.00;0.00',
                'en_US',
                [
                    'displayPositiveSign' => [true],
                ],
            ],
            'Leading sign with separator' => [
                '+ #,##0.00;- #,##0.00;0.00',
                'en_US',
                [
                    'trailingSign' => [false, Number::NON_BREAKING_SPACE],
                    'displayPositiveSign' => [true],
                ],
            ],
            'Trailing sign with separator' => [
                '#,##0.00 +;#,##0.00 -;0.00',
                'en_US',
                [
                    'trailingSign' => [true, Number::NON_BREAKING_SPACE],
                    'displayPositiveSign' => [true],
                ],
            ],
            'Override to 3 decimal places without thousands' => [
                '0.000',
                'en_US',
                [
                    'setDecimals' => [3],
                    'useThousandsSeparator' => [false],
                ],
            ],
        ];
    }

    /**
     * @group intl
     * @dataProvider numberMaskWithIntlColors
     */
    public function testNumberMaskWithIntlColors(string $expectedResult, string $locale, array $args): void
    {
        $numberFormatter = new Number($locale);

        foreach ($args as $methodName => $methodArgs) {
            $numberFormatter->{$methodName}(...$methodArgs);
        }

        $numberFormatMask = $numberFormatter->format();
        self::assertSame($expectedResult, $numberFormatMask);
    }

    public function numberMaskWithIntlColors()
    {
        return [
            '/Red/' => [
                '#,##0.00;[Red]-#,##0.00',
                'nl_NL',
                [
                    'setColors' => [null, 'Red'],
                ],
            ],
            'Green/Red/Orange' => [
                '[Green]#,##0.00;[Red]-#,##0.00;[Orange]0.00',
                'nl_NL',
                [
                    'setColors' => ['Green', 'Red', 'Orange'],
                ],
            ],
        ];
    }
}
