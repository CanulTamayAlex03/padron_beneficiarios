<?php
namespace App\Imports;

use App\Models\Beneficiario;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\SkipsFailures;

class BeneficiariosImport implements ToModel, WithHeadingRow, SkipsEmptyRows, WithValidation, SkipsOnFailure
{
    use SkipsFailures;

    public $errores = [];
    public $importados = 0;

    public function model(array $row)
    {
        $normalizedRow = [
            'nombres'   => $row['nombres']   ?? $row['Nombres']   ?? $row['NOMBRES']   ?? $row['nombre']   ?? null,
            'apellidos' => $row['apellidos'] ?? $row['Apellidos'] ?? $row['APELLIDOS'] ?? $row['apellido'] ?? null,
            'curp'      => $row['curp']      ?? $row['CURP']      ?? $row['Curp']      ?? null,
        ];

        if (!empty($normalizedRow['nombres']) && !empty($normalizedRow['apellidos']) && !empty($normalizedRow['curp'])) {

            if (\App\Models\Beneficiario::where('curp', $normalizedRow['curp'])->exists()) {
                $this->errores[] = "El beneficiario con CURP {$normalizedRow['curp']} ya existe y no se importó.";
                return null;
            }

            $this->importados++;
            return new \App\Models\Beneficiario([
                'nombres'   => $normalizedRow['nombres'],
                'apellidos' => $normalizedRow['apellidos'],
                'curp'      => $normalizedRow['curp'],
            ]);
        }

        return null;
    }

    public function rules(): array
    {
        return [
            'nombres'   => 'required|string',
            'apellidos' => 'required|string',
            'curp'      => 'required|string|size:18',
        ];
    }

    public function headingRow(): int
    {
        return 1;
    }
}