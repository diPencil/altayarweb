<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Gateway;
use App\Models\GatewayCurrency;

class FawaterkSeeder extends Seeder
{
    public function run()
    {
        $gatewayCurrencies = [
            'USD' => [
                'symbol' => 'USD',
                'rate' => 1,
                'min_amount' => 10,
                'max_amount' => 100000,
            ],
            'SAR' => [
                'symbol' => 'SAR',
                'rate' => 3.75,
                'min_amount' => 10,
                'max_amount' => 100000,
            ],
            'EGP' => [
                'symbol' => 'EGP',
                'rate' => 48.5,
                'min_amount' => 10,
                'max_amount' => 100000,
            ],
            'EUR' => [
                'symbol' => 'EUR',
                'rate' => 0.92,
                'min_amount' => 10,
                'max_amount' => 100000,
            ],
        ];

        $gateway = Gateway::updateOrCreate(
            ['code' => 115],
            [
                'name' => 'Fawaterk',
                'alias' => 'Fawaterk',
                'status' => 1,
                'gateway_parameters' => json_encode([
                    'api_key' => [
                        'title' => 'API Key',
                        'global' => true,
                        'value' => 'a9bd550ecbd78778ce88dc8f0928e7673e117e89c0acf5cf23',
                    ],
                    'provider_key' => [
                        'title' => 'Provider Key',
                        'global' => true,
                        'value' => 'FAWATERAK.19700',
                    ],
                ]),
                'supported_currencies' => json_encode(array_combine(array_keys($gatewayCurrencies), array_keys($gatewayCurrencies))),
                'crypto' => 0,
            ]
        );

        foreach ($gatewayCurrencies as $currencyCode => $currencyData) {
            GatewayCurrency::updateOrCreate(
                ['method_code' => 115, 'currency' => $currencyCode],
                [
                    'name' => 'Fawaterk',
                    'method_code' => 115,
                    'currency' => $currencyCode,
                    'symbol' => $currencyData['symbol'],
                    'gateway_alias' => 'Fawaterk',
                    'min_amount' => $currencyData['min_amount'],
                    'max_amount' => $currencyData['max_amount'],
                    'percent_charge' => 0,
                    'fixed_charge' => 0,
                    'rate' => $currencyData['rate'],
                    'gateway_parameter' => json_encode([
                        'api_key' => [
                            'title' => 'API Key',
                            'global' => true,
                            'value' => 'a9bd550ecbd78778ce88dc8f0928e7673e117e89c0acf5cf23',
                        ],
                        'provider_key' => [
                            'title' => 'Provider Key',
                            'global' => true,
                            'value' => 'FAWATERAK.19700',
                        ],
                    ]),
                ]
            );
        }
    }
}
