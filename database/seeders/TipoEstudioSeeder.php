<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TipoEstudioSeeder extends Seeder
{
    public function run()
    {
        // Primero, insertar los requisitos
        $requisitos = [
            ['descripRequisito' => 'Vejiga llena'],
            ['descripRequisito' => '20 a 24 semanas de gestación'],
            ['descripRequisito' => 'Vejiga vacía'],
            ['descripRequisito' => '4 horas de ayuno'],
            ['descripRequisito' => '6 horas de ayuno'],
            ['descripRequisito' => '1 hora antes'],
            ['descripRequisito' => 'Clampear sonda 1 hora antes'],
            ['descripRequisito' => 'Sin vendajes'],
            ['descripRequisito' => 'Sin vendajes o parches'],
        ];

        foreach ($requisitos as $requisito) {
            DB::table('Requisito')->insert($requisito);
        }

        // Luego, insertar los tipos de estudio
        $tiposEstudio = [
            [
                'descripcion' => 'Estudio Obstétrico 1',
                'requisitos' => [
                    [
                        'codRequisito' => 1, // Vejiga llena
                        'observacion' => 'Estimado paciente debe acudir para el estudio OBSTÉTRICO (Gestante hasta 10 semanas venir con vejiga llena beber 8 vasos de agua 1 hora antes del estudio). En caso de ser transvaginal vejiga vacía entrar al sanitario 5 minutos antes de ingresar. TRAER CARTON PRENATAL Y SUS ECOGRAFÍAS PREVIAS. GRACIAS.'
                    ],
                ]
            ],
            [
                'descripcion' => 'Estudio Obstétrico 2/3',
                'requisitos' => [
                    [
                        'codRequisito' => 2,
                        'observacion' => 'Estimado paciente acudir para el estudio OBSTÉTRICO (Vejiga vacía para mejor visualización). Vejiga vacía entrar al sanitario antes de ingresar. TRAER CARTON PRENATAL Y SUS ECOGRAFÍAS PREVIAS. GRACIAS.'
                    ],
                ]
            ],
            [
                'descripcion' => 'Estudio Morfológico',
                'requisitos' => [
                    [
                        'codRequisito' => 3, // 20 a 24 semanas
                        'observacion' => 'Estimado paciente debe acudir para el estudio morfológico (estructuras fetales a detalle, etc) es de mayor tiempo 1 hora aprox semanas 20 a 24 de gestación posterior a eso corresponde estudio estándar. TRAER CARTON PRENATAL Y SUS ECOGRAFÍAS PREVIAS. GRACIAS.'
                    ],
                ]
            ],
            [
                'descripcion' => 'Estudio Abdominal Pediátrico',
                'requisitos' => [
                    [
                        'codRequisito' => 4, // 4 horas de ayuno
                        'observacion' => 'Estimado paciente debe acudir para el estudio abdominal (hígado vesícula, etc) por las mañanas o por la tarde con 4 horas de ayuno. GRACIAS'
                    ],
                ]
            ],
            [
                'descripcion' => 'Estudio Abdominal',
                'requisitos' => [
                    [
                        'codRequisito' => 5, // 6 horas de ayuno
                        'observacion' => 'Estimado paciente debe acudir para el estudio abdominal (hígado, vesícula, etc) por las mañanas o por la tarde con 6 horas de ayuno. GRACIAS.'
                    ],
                ]
            ],
            [
                'descripcion' => 'Estudio Urología',
                'requisitos' => [
                    [
                        'codRequisito' => 1, // Vejiga llena
                        'observacion' => 'Estimado paciente debe acudir para el estudio UROLOGICO (próstata, vejiga, riñón, etc) por la tarde vejiga llena beber 8 vasos de agua 1 hora antes del estudio. GRACIAS.'
                    ],
                    ['codRequisito' => 6, 'observacion' => null], // 1 hora antes
                ]
            ],
            [
                'descripcion' => 'Estudio Ginecológico Transvaginal',
                'requisitos' => [
                    [
                        'codRequisito' => 3, // Vejiga vacía
                        'observacion' => 'Estimado paciente debe acudir para el estudio con vejiga vacía debe ingresar al baño 5 minutos antes del estudio. E INFORMA LO SIGUIENTE: NO debe de cursar con infección urinaria. NO debe estar en sus días de menstruación o presentar sangrado abundante. Este tipo de estudio es apto para mujeres que iniciaron su vida sexual. GRACIAS.'
                    ],
                ]
            ],
            [
                'descripcion' => 'Estudio Ginecológico Abdominal',
                'requisitos' => [
                    [
                        'codRequisito' => 1, // Vejiga llena
                        'observacion' => 'Estimada paciente debe acudir al estudio con la vejiga llena debe de tomar 1 litro de agua es decir 8 vasos de agua 1 HORA ANTES DEL ESTUDIO. SE INFORMA LO SIGUIENTE: No debe de cursar con infección urinaria alguna patología vesical. GRACIAS.'
                    ],
                    ['codRequisito' => 6, 'observacion' => null], // 1 hora antes
                ]
            ],
            [
                'descripcion' => 'Estudio Urología con Sonda',
                'requisitos' => [
                    [
                        'codRequisito' => 1, // Vejiga llena
                        'observacion' => 'Estimado paciente debe acudir para el estudio UROLOGICO (Próstata, vejiga, riñón, etc) Previamente Clampear sonda 1 hora antes y tener vejiga llena beber 4 vasos de agua 1 hora antes del estudio. GRACIAS.'
                    ],
                    ['codRequisito' => 7, 'observacion' => null], // Clampear sonda
                ]
            ],
            [
                'descripcion' => 'Ecografía de Partes Blandas',
                'requisitos' => [
                    [
                        'codRequisito' => 8, // Sin vendajes
                        'observacion' => 'Estimado paciente debe acudir para el estudio con la región a observar sin vendaje (aflojarlos previamente) el estudio visualiza: piel, tejido celular subcutáneo, musculo ej.- tendinitis, esguinces, desgarros musculares, hernias o alteraciones de la pared abdominal, existencia de cuerpos extraños (como astillas o vidrio), masas de tejido blando. GRACIAS.'
                    ]
                ]
            ],
            [
                'descripcion' => 'Ecografía de Mama-Tiroides-Escroto-Testículo',
                'requisitos' => [
                    [
                        'codRequisito' => 9, // Sin vendajes o parches
                        'observacion' => 'Estimado paciente debe acudir para el estudio con la región a observar sin vendajes o parches (aflojarlos previamente) PATOLOGIAS BASICAS PARA PRIMER NIVEL. GRACIAS.'
                    ]
                ]
            ],
        ];

        foreach ($tiposEstudio as $tipoData) {
            // Insertar el tipo de estudio
            $codTest = DB::table('TipoEstudio')->insertGetId([
                'descripcion' => $tipoData['descripcion']
            ]);

            // Insertar las relaciones con requisitos
            foreach ($tipoData['requisitos'] as $requisito) {
                DB::table('TipoEstudio_Requisito')->insert([
                    'codTest' => $codTest,
                    'codRequisito' => $requisito['codRequisito'],
                    'observacion' => $requisito['observacion']
                ]);
            }
        }
    }
}
