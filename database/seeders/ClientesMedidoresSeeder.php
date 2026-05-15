<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ClientesMedidoresSeeder extends Seeder
{
    public function run(): void
    {
        $ccostoPorCliente = [
            '10122596' => '1400010201', // CAPJ Coyhaique
            '10123101' => '1400010201', // CAPJ Coyhaique
            '10127837' => '1400020301', // Corte de Apelaciones de Coyhaique
            '10127838' => '1400020301', // Corte de Apelaciones de Coyhaique
            '10101388' => '1400020401', // Primer Juzgado de Letras de Coyhaique
            '10101389' => '1400020401', // Primer Juzgado de Letras de Coyhaique
            '10126102' => '1400020401', // Primer Juzgado de Letras de Coyhaique
            '10129956' => '1400020401', // Primer Juzgado de Letras de Coyhaique
            '10138053' => '1400020601', // Juzgado de Letras, Garantia y Familia de Chile Chico
            '10115714' => '1400020601', // Juzgado de Letras, Garantia y Familia de Chile Chico
            '10117895' => '1400020602', // Juzgado de Letras, Garantia y Familia Pto. Cisnes
            '10105641' => '1400020603', // Juzgado de Letras, Garantia y Familia de Cochrane
            '10117896' => '1400020603', // Juzgado de Letras, Garantia y Familia de Cochrane
            '10115704' => '1471031301', // Letras, Garantia y Familia Aysen
            '10135754' => '1471031301', // Letras, Garantia y Familia Aysen
            '10139038' => '1400010201', // CAPJ Coyhaique
        ];

        $clientesMedidores = [
[
        'numerocliente' => '10122596',
        'medidor' => 'ELECTRICIDAD',
        'rutproveedor' => '88272600-2',
        'ccosto' => 1400010201,
        'tarifa' => 'BLANCA',
        'tipo' => 'CALEFACCION',
        'vigente' => 1
    ],
[
        'numerocliente' => '10123101',
        'medidor' => 'ELECTRICIDAD',
        'rutproveedor' => '88272600-2',
        'ccosto' => 1400010201,
        'tarifa' => 'BT4-3',
        'tipo' => 'NORMAL',
        'vigente' => 1
    ],
[
        'numerocliente' => '10127837',
        'medidor' => 'ELECTRICIDAD',
        'rutproveedor' => '88272600-2',
        'ccosto' => 1400010201,
        'tarifa' => 'AT4-3',
        'tipo' => 'NORMAL',
        'vigente' => 1
    ],
[
        'numerocliente' => '10127838',
        'medidor' => 'ELECTRICIDAD',
        'rutproveedor' => '88272600-2',
        'ccosto' => 1400010201,
        'tarifa' => 'AT4-1',
        'tipo' => 'NORMAL',
        'vigente' => 1
    ],
[
        'numerocliente' => '10138053',
        'medidor' => 'ELECTRICIDAD',
        'rutproveedor' => '88272600-2',
        'ccosto' => 1400010201,
        'tarifa' => 'BT4-3',
        'tipo' => 'NORMAL',
        'vigente' => 1
    ],
[
        'numerocliente' => '10115714',
        'medidor' => 'ELECTRICIDAD',
        'rutproveedor' => '88272600-2',
        'ccosto' => 1400010201,
        'tarifa' => 'BLANCA',
        'tipo' => 'CALEFACCION',
        'vigente' => 1
    ],
[
        'numerocliente' => '10117895',
        'medidor' => 'ELECTRICIDAD',
        'rutproveedor' => '88272600-2',
        'ccosto' => 1400010201,
        'tarifa' => 'BT3-B',
        'tipo' => 'NORMAL',
        'vigente' => 1
    ],
[
        'numerocliente' => '10105641',
        'medidor' => 'ELECTRICIDAD',
        'rutproveedor' => '88272600-2',
        'ccosto' => 1400010201,
        'tarifa' => 'BT3-B',
        'tipo' => 'NORMAL',
        'vigente' => 1
    ],
[
        'numerocliente' => '10117896',
        'medidor' => 'ELECTRICIDAD',
        'rutproveedor' => '88272600-2',
        'ccosto' => 1400010201,
        'tarifa' => 'BLANCA',
        'tipo' => 'CALEFACCION',
        'vigente' => 1
    ],
[
        'numerocliente' => '10101389',
        'medidor' => 'ELECTRICIDAD',
        'rutproveedor' => '88272600-2',
        'ccosto' => 1400010201,
        'tarifa' => 'BT1',
        'tipo' => 'NORMAL',
        'vigente' => 1
    ],
[
        'numerocliente' => '10101388',
        'medidor' => 'ELECTRICIDAD',
        'rutproveedor' => '88272600-2',
        'ccosto' => 1400010201,
        'tarifa' => 'BT1',
        'tipo' => 'NORMAL',
        'vigente' => 1
    ],
[
        'numerocliente' => '10129956',
        'medidor' => 'ELECTRICIDAD',
        'rutproveedor' => '88272600-2',
        'ccosto' => 1400010201,
        'tarifa' => 'BT4-3',
        'tipo' => 'NORMAL',
        'vigente' => 1
    ],
[
        'numerocliente' => '10126102',
        'medidor' => 'ELECTRICIDAD',
        'rutproveedor' => '88272600-2',
        'ccosto' => 1400010201,
        'tarifa' => 'BLANCA',
        'tipo' => 'CALEFACCION',
        'vigente' => 1
    ],
[
        'numerocliente' => '10094414',
        'medidor' => 'ELECTRICIDAD',
        'rutproveedor' => '88272600-2',
        'ccosto' => 1400010201,
        'tarifa' => 'BT1',
        'tipo' => 'NORMAL',
        'vigente' => 1
    ],
[
        'numerocliente' => '10098536',
        'medidor' => 'ELECTRICIDAD',
        'rutproveedor' => '88272600-2',
        'ccosto' => 1400010201,
        'tarifa' => 'BT3-PP',
        'tipo' => 'NORMAL',
        'vigente' => 1
    ],
[
        'numerocliente' => '10115703',
        'medidor' => 'ELECTRICIDAD',
        'rutproveedor' => '88272600-2',
        'ccosto' => 1400010201,
        'tarifa' => 'BLANCA',
        'tipo' => 'CALEFACCION',
        'vigente' => 1
    ],
[
        'numerocliente' => '10135754',
        'medidor' => 'ELECTRICIDAD',
        'rutproveedor' => '88272600-2',
        'ccosto' => 1400010201,
        'tarifa' => 'BT-43',
        'tipo' => 'NORMAL',
        'vigente' => 1
    ],
[
        'numerocliente' => '10115704',
        'medidor' => 'ELECTRICIDAD',
        'rutproveedor' => '88272600-2',
        'ccosto' => 1400010201,
        'tarifa' => 'BLANCA',
        'tipo' => 'CALEFACCION',
        'vigente' => 1
    ],
[
        'numerocliente' => '10118753',
        'medidor' => 'ELECTRICIDAD',
        'rutproveedor' => '88272600-2',
        'ccosto' => 1400010201,
        'tarifa' => 'Ciro Arredondo S/N',
        'tipo' => 'NORMAL',
        'vigente' => 0
    ],
[
        'numerocliente' => '10113130',
        'medidor' => 'ELECTRICIDAD',
        'rutproveedor' => '88272600-2',
        'ccosto' => 1400010201,
        'tarifa' => 'Ciro Arredondo S/N',
        'tipo' => 'NORMAL',
        'vigente' => 0
    ],
[
        'numerocliente' => '10103242',
        'medidor' => 'ELECTRICIDAD',
        'rutproveedor' => '88272600-2',
        'ccosto' => 1400010201,
        'tarifa' => 'Ciro Arredondo S/N',
        'tipo' => 'NORMAL',
        'vigente' => 0
    ],
[
        'numerocliente' => '10116551',
        'medidor' => 'ELECTRICIDAD',
        'rutproveedor' => '88272600-2',
        'ccosto' => 1400010201,
        'tarifa' => 'Ramon Freire 293',
        'tipo' => 'NORMAL',
        'vigente' => 0
    ],
[
        'numerocliente' => '10122085',
        'medidor' => 'ELECTRICIDAD',
        'rutproveedor' => '88272600-2',
        'ccosto' => 1400010201,
        'tarifa' => 'Ramon Freire 293',
        'tipo' => 'NORMAL',
        'vigente' => 0
    ],
[
        'numerocliente' => '10124561',
        'medidor' => 'ELECTRICIDAD',
        'rutproveedor' => '88272600-2',
        'ccosto' => 1400010201,
        'tarifa' => 'Moraleda 448',
        'tipo' => 'NORMAL',
        'vigente' => 0
],


// NUEVOS CLIENTES
    [
        'numerocliente' => '1011401',
        'medidor' => 'ELECTRICIDAD',
        'rutproveedor' => '88272600-2',
        'ccosto' => 1400010201,
        'tarifa' => 'Sin direccion',
        'tipo' => 'NORMAL',
        'vigente' => 1
    ], 
    [
        'numerocliente' => '10091997',
        'medidor' => 'ELECTRICIDAD',
        'rutproveedor' => '88272600-2',
        'ccosto' => 1400010201,
        'tarifa' => 'Sin direccion',
        'tipo' => 'NORMAL',
        'vigente' => 1
    ], 
    [
        'numerocliente' => '10099037',
        'medidor' => 'ELECTRICIDAD',
        'rutproveedor' => '88272600-2',
        'ccosto' => 1400010201,
        'tarifa' => 'Sin direccion',
        'tipo' => 'NORMAL',
        'vigente' => 1
    ], 
    [
        'numerocliente' => '10099168',
        'medidor' => 'ELECTRICIDAD',
        'rutproveedor' => '88272600-2',
        'ccosto' => 1400010201,
        'tarifa' => 'Sin direccion',
        'tipo' => 'NORMAL',
        'vigente' => 1
    ], 
    [
        'numerocliente' => '10100106',
        'medidor' => 'ELECTRICIDAD',
        'rutproveedor' => '88272600-2',
        'ccosto' => 1400010201,
        'tarifa' => 'Sin direccion',
        'tipo' => 'NORMAL',
        'vigente' => 1
    ], 
    [
        'numerocliente' => '10100511',
        'medidor' => 'ELECTRICIDAD',
        'rutproveedor' => '88272600-2',
        'ccosto' => 1400010201,
        'tarifa' => 'Sin direccion',
        'tipo' => 'NORMAL',
        'vigente' => 1
    ], 
    [
        'numerocliente' => '10117916',
        'medidor' => 'ELECTRICIDAD',
        'rutproveedor' => '88272600-2',
        'ccosto' => 1400010201,
        'tarifa' => 'Sin direccion',
        'tipo' => 'NORMAL',
        'vigente' => 1
    ], 
    [
        'numerocliente' => '10119499',
        'medidor' => 'ELECTRICIDAD',
        'rutproveedor' => '88272600-2',
        'ccosto' => 1400010201,
        'tarifa' => 'Sin direccion',
        'tipo' => 'NORMAL',
        'vigente' => 1
    ], 
    [
        'numerocliente' => '10119500',
        'medidor' => 'ELECTRICIDAD',
        'rutproveedor' => '88272600-2',
        'ccosto' => 1400010201,
        'tarifa' => 'Sin direccion',
        'tipo' => 'NORMAL',
        'vigente' => 1
    ], 
    [
        'numerocliente' => '10119501',
        'medidor' => 'ELECTRICIDAD',
        'rutproveedor' => '88272600-2',
        'ccosto' => 1400010201,
        'tarifa' => 'Sin direccion',
        'tipo' => 'NORMAL',
        'vigente' => 1
    ], 
    [
        'numerocliente' => '10119502',
        'medidor' => 'ELECTRICIDAD',
        'rutproveedor' => '88272600-2',
        'ccosto' => 1400010201,
        'tarifa' => 'Sin direccion',
        'tipo' => 'NORMAL',
        'vigente' => 1
    ], 
    [
        'numerocliente' => '10119503',
        'medidor' => 'ELECTRICIDAD',
        'rutproveedor' => '88272600-2',
        'ccosto' => 1400010201,
        'tarifa' => 'Sin direccion',
        'tipo' => 'NORMAL',
        'vigente' => 1
    ], 
    [
        'numerocliente' => '10119504',
        'medidor' => 'ELECTRICIDAD',
        'rutproveedor' => '88272600-2',
        'ccosto' => 1400010201,
        'tarifa' => 'Sin direccion',
        'tipo' => 'NORMAL',
        'vigente' => 1
    ], 
    [
        'numerocliente' => '10122862',
        'medidor' => 'ELECTRICIDAD',
        'rutproveedor' => '88272600-2',
        'ccosto' => 1400010201,
        'tarifa' => 'Sin direccion',
        'tipo' => 'NORMAL',
        'vigente' => 1
    ], 
    [
        'numerocliente' => '10122863',
        'medidor' => 'ELECTRICIDAD',
        'rutproveedor' => '88272600-2',
        'ccosto' => 1400010201,
        'tarifa' => 'Sin direccion',
        'tipo' => 'NORMAL',
        'vigente' => 1
    ],
    [
        'numerocliente' => '10139038',
        'medidor' => 'ELECTRICIDAD',
        'rutproveedor' => '88272600-2',
        'ccosto' => 1400010201,
        'tarifa' => 'BT4-3',
        'tipo' => 'NORMAL',
        'vigente' => 1
    ],


        ];

        $clientesMedidores = array_map(function (array $cliente) use ($ccostoPorCliente) {
            $cliente['ccosto'] = $ccostoPorCliente[$cliente['numerocliente']] ?? (string) $cliente['ccosto'];
            return $cliente;
        }, $clientesMedidores);

        foreach ($clientesMedidores as $cliente) {
            DB::table('clientesmedidores')->updateOrInsert(
                ['numerocliente' => $cliente['numerocliente']],
                [
                    'medidor' => $cliente['medidor'],
                    'rutproveedor' => $cliente['rutproveedor'],
                    'ccosto' => $cliente['ccosto'],
                    'tarifa' => $cliente['tarifa'],
                    'tipo' => $cliente['tipo'],
                    'vigente' => $cliente['vigente'],
                ]
            );
        }
    }
}
