<?php

namespace App\Imports;

use App\Models\Person;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class PeopleImport implements ToModel, WithHeadingRow
{
    private $userId;

    public function __construct(int $userId)
    {
        $this->userId = $userId;
    }

    public function model(array $row)
    {
        return new Person([
            'rut'              => $row['rut'] ?? null,
            'nombres'          => $row['nombres'] ?? null,
            'ap_pat'           => $row['ap_pat'] ?? null,
            'ap_mat'           => $row['ap_mat'] ?? null,
            'fecha_nacimiento' => $this->convertirFecha($row['fecha_nacimiento'] ?? null),
            'sexo'             => $row['sexo'] ?? null,
            'ficha_clinica'    => $row['ficha_clinica'] ?? 'SIN FICHA',
            'domicilio'        => $row['domicilio'] ?? null,
            'fono'             => $row['fono'] ?? null,
            'celu'             => $row['celu'] ?? null,
            'email'            => $row['email'] ?? null,
            'user_id'          => $this->userId,
        ]);
    }

    private function convertirFecha($valor)
    {
        try {
            if (is_numeric($valor)) {
                return Date::excelToDateTimeObject($valor)->format('Y-m-d');
            } else {
                return date('Y-m-d', strtotime($valor));
            }
        } catch (\Exception $e) {
            return null;
        }
    }
}
