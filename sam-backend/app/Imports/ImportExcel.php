<?php

namespace App\Imports;

use App\Models\Person;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class ImportExcel implements ToModel, WithHeadingRow, WithBatchInserts, WithChunkReading
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {   
        // se valida si la fecha de nacimiento es un numero, si es asi se convierte a fecha
        if (is_numeric($row['fechanacimiento'])){
            $fechaNacimiento = Date::excelToDateTimeObject($row['fechanacimiento'])
            ->format('Y-m-d');
        } else{
            $fechaNacimiento = $row['fechanacimiento'];
        }
        // se limpia el rut de caracteres especiales
        $id = preg_replace('/[^0-9]/', '', $row['rut']);

        // se retorna un nuevo objeto Person con los datos del excel
        return new Person([
            'id' =>  $id,
            'nombres' => $row['nombres'],
            'ap_pat' =>  $row['appat'],
            'ap_mat' =>  $row['apmat'], 
            'fecha_nacimiento' => $fechaNacimiento,
            'sexo' =>    $row['sexo'],
            'ficha_clinica'  => $row['fichaclinica'],
            'domicilio'   => $row['domicilio'],
            'fono' =>  $row['fono'],
            'celu'  =>  $row['celu'],
            'email'   =>  $row['email'],
        ]);
    }
    public function batchSize(): int
    {
        return 500;
    }
    public function chunkSize(): int
    {
        return 500;
    }
}
