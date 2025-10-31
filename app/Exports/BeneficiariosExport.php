<?php

namespace App\Exports;

use App\Models\Beneficiario;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;

class BeneficiariosExport implements FromCollection, WithHeadings, WithMapping, WithStyles
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return Beneficiario::with(['estado', 'estadoViv', 'municipio']) 
            ->select([
                'curp',
                'primer_apellido', 
                'segundo_apellido',
                'nombres',
                'fecha_nac',
                'estado_id',
                'sexo',
                'discapacidad',
                'indigena',
                'estado_viv_id',
                'numero',
                'letra',
                'tipo_asentamiento',
                'colonia_fracc',
                'cp',
                'localidad',
                'municipio_id',
                'referencias_domicilio'
            ])->latest()->get();
    }

    public function headings(): array
    {
        $numeros = [];
        for ($i = 1; $i <= 48; $i++) {
            $numeros[] = $i;
        }

        $encabezados = [
            'CURP (18 dígitos)',
            'Primer Apellido',
            'Segundo Apellido', 
            'Nombres',
            'Fecha Nacimiento Año/Mes/Día',
            'Clave Entidad Federativa de Nacimiento',
            'Sexo',
            'Discapacidad',
            'Indígena',
            'Clave Estado Civil',
            'Cve Dependencia',
            'Cve Institucion',
            'Cve Programa',
            'Cve Intra-Programa',
            'Entidad Federativa',
            'Cve Municipio',
            'Cve Localidad',
            'Fecha Beneficio',
            'Cve Tipo Beneficiario',
            'Cve Tipo Beneficio',
            'Cantidad del apoyo',
            'CURP_D*',
            'RFC_D*',
            'Tipo Vial',
            'Nom Vial',
            'Carretera',
            'Camino',
            'Num Ext num1',
            'Num Ext num2',
            'Num Ext alf1',
            'Num Int num',
            'Num Int alf',
            'Tipo Asen',
            'Nom Asen',
            'CP',
            'Nom Loc',
            'Cve Loc',
            'Nom Mun',
            'Cve Mun',
            'Nom Ent',
            'Cve Ent',
            'Tipo Ref1',
            'Nom Ref1',
            'Tipo Ref2',
            'Nom Ref2',
            'Tipo Ref3',
            'Nom Ref3',
            'Descripcion ubicación'
        ];

        return [$numeros, $encabezados];
    }

    
    public function map($beneficiario): array
    {
        return [
            // Columnas 1-9: Datos existentes
            $beneficiario->curp, // 1. CURP
            $beneficiario->primer_apellido, // 2. Primer Apellido
            $beneficiario->segundo_apellido, // 3. Segundo Apellido
            $beneficiario->nombres, // 4. Nombres
            $beneficiario->fecha_nac ? $beneficiario->fecha_nac->format('Ymd') : '', // 5. Fecha Nacimiento
            $beneficiario->estadoViv ? $beneficiario->estadoViv->clave_estado : '', // 6. Clave Entidad Nacimiento
            $beneficiario->sexo, // 7. Sexo
            $beneficiario->discapacidad ? 'SÍ' : 'NO', // 8. Discapacidad
            $beneficiario->indigena ? 'SÍ' : 'NO', // 9. Indígena
            
            // Columnas 10-14: Vacías
            '', // 10. Clave Estado Civil
            '', // 11. Cve Dependencia
            '', // 12. Cve Institucion
            '', // 13. Cve Programa
            '', // 14. Cve Intra-Programa
            
            // Columna 15: Entidad Federativa (estado_viv_id)
            $beneficiario->estado_viv_id, // 15. Entidad Federativa
            
            // Columnas 16-23: Vacías
            '', // 16. Cve Municipio
            '', // 17. Cve Localidad
            '', // 18. Fecha Beneficio
            '', // 19. Cve Tipo Beneficiario
            '', // 20. Cve Tipo Beneficio
            '', // 21. Cantidad del apoyo
            '', // 22. CURP_D*
            '', // 23. RFC_D*
            
            // Columnas 24-27: Vacías
            '', // 24. Tipo Vial
            '', // 25. Nom Vial
            '', // 26. Carretera
            '', // 27. Camino
            
            // Columnas 28-30: Datos de número
            $beneficiario->numero, // 28. Num Ext num1
            '', // 29. Num Ext num2 (vacío)
            $beneficiario->letra, // 30. Num Ext alf1
            
            // Columnas 31-32: Vacías
            '', // 31. Num Int num
            '', // 32. Num Int alf
            
            // Columnas 33-36: Datos de ubicación
            $beneficiario->tipo_asentamiento, // 33. Tipo Asen
            $beneficiario->colonia_fracc, // 34. Nom Asen
            $beneficiario->cp, // 35. CP
            $beneficiario->localidad, // 36. Nom Loc
            
            // Columna 37: Vacía
            '', // 37. Cve Loc
            
            // Columnas 38-39: Datos de municipio
            $beneficiario->municipio ? $beneficiario->municipio->descripcion : '', // 38. Nom Mun
            '', // 39. Cve Mun (vacío)
            
            // Columnas 40-41: Datos de entidad
            $beneficiario->estado ? $beneficiario->estado->nombre : '', // 40. Nom Ent
            $beneficiario->estado_viv_id, // 41. Cve Ent
            
            // Columnas 42-47: Vacías (referencias)
            '', // 42. Tipo Ref1
            '', // 43. Nom Ref1
            '', // 44. Tipo Ref2
            '', // 45. Nom Ref2
            '', // 46. Tipo Ref3
            '', // 47. Nom Ref3
            
            // Columna 48: Descripción ubicación
            $beneficiario->referencias_domicilio // 48. Descripcion ubicación
        ];
    }

    /**
     * Aplicar estilos al Excel
     */
    public function styles(Worksheet $sheet)
    {
        // 🔥 ALTURA PARA AMBAS FILAS DE ENCABEZADO
        $sheet->getRowDimension(1)->setRowHeight(25);
        $sheet->getRowDimension(2)->setRowHeight(40);

        // 🔥 ESTILO PARA FILA 1 (NÚMEROS - FONDO GRIS)
        $sheet->getStyle('A1:AV1')->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['rgb' => '000000'],
                'size' => 12,
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => [
                    'rgb' => 'D3D3D3' // Color gris claro
                ]
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '000000'],
                ],
            ]
        ]);

        $sheet->getStyle('A2:AV2')->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF'],
                'size' => 10,
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => [
                    'rgb' => '722F37'
                ]
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
                'wrapText' => true,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '000000'],
                ],
            ]
        ]);

        $sheet->getStyle('E3:E' . $sheet->getHighestRow())->applyFromArray([
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_LEFT,
                'vertical' => Alignment::VERTICAL_CENTER,
            ]
        ]);

        foreach(['A', 'B', 'C', 'D', 'F', 'G', 'H', 'I', 'O', 'AC', 'AD', 'AE', 'AF', 'AH', 'AJ', 'AV'] as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }

        $sheet->getColumnDimension('E')->setWidth(12); 
        $sheet->getColumnDimension('J')->setWidth(15); 
        $sheet->getColumnDimension('K')->setWidth(15); 
        $sheet->getColumnDimension('L')->setWidth(15); 
        $sheet->getColumnDimension('M')->setWidth(12); 
        $sheet->getColumnDimension('N')->setWidth(15); 
        $sheet->getColumnDimension('P')->setWidth(12); 
        $sheet->getColumnDimension('Q')->setWidth(12);
        $sheet->getColumnDimension('R')->setWidth(12);
        $sheet->getColumnDimension('S')->setWidth(18); 
        $sheet->getColumnDimension('T')->setWidth(15); 
        $sheet->getColumnDimension('U')->setWidth(15); 
        $sheet->getColumnDimension('V')->setWidth(10); 
        $sheet->getColumnDimension('W')->setWidth(10); 
        $sheet->getColumnDimension('X')->setWidth(10); 
        $sheet->getColumnDimension('Y')->setWidth(12); 
        $sheet->getColumnDimension('Z')->setWidth(12);
        $sheet->getColumnDimension('AA')->setWidth(10);
        $sheet->getColumnDimension('AB')->setWidth(12); 
        $sheet->getColumnDimension('AC')->setWidth(12); 
        $sheet->getColumnDimension('AD')->setWidth(12); 
        $sheet->getColumnDimension('AE')->setWidth(12); 
        $sheet->getColumnDimension('AF')->setWidth(12); 
        $sheet->getColumnDimension('AG')->setWidth(12); 
        $sheet->getColumnDimension('AI')->setWidth(8);  
        $sheet->getColumnDimension('AK')->setWidth(8);  
        $sheet->getColumnDimension('AL')->setWidth(15); 
        $sheet->getColumnDimension('AM')->setWidth(10); 
        $sheet->getColumnDimension('AN')->setWidth(15); 
        $sheet->getColumnDimension('AO')->setWidth(10); 

        $sheet->getStyle('A3:AV' . ($sheet->getHighestRow()))
              ->getAlignment()
              ->setVertical(Alignment::VERTICAL_CENTER);

        return [
            1 => [
                'font' => ['bold' => true, 'color' => ['rgb' => '000000']],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'A0A0A0']]
            ],
            2 => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '722F37']]
            ],
        ];
    }
}