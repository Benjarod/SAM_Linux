<?php

namespace App\Livewire;

use App\Models\Person;
use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Builder;
use PowerComponents\LivewirePowerGrid\Button;
use PowerComponents\LivewirePowerGrid\Column;
use PowerComponents\LivewirePowerGrid\Components\SetUp\Exportable;
use PowerComponents\LivewirePowerGrid\Facades\Filter;
use PowerComponents\LivewirePowerGrid\Facades\PowerGrid;
use PowerComponents\LivewirePowerGrid\PowerGridFields;
use PowerComponents\LivewirePowerGrid\PowerGridComponent;
use PowerComponents\LivewirePowerGrid\Traits\WithExport;

// Esta es la tabla que se muestra en la vista gestionBd, esta tabla es una tabla PowerGrid
final class PeopleTable extends PowerGridComponent
{
    public string $tableName = 'people-table';

    use WithExport;
    
    public function setUp(): array
    {
        $this->showCheckBox();

        return [
            PowerGrid::exportable('export')
                ->striped()
                ->type(Exportable::TYPE_XLS, Exportable::TYPE_CSV),
            PowerGrid::header()
                ->showToggleColumns(),
            PowerGrid::footer()
                ->showPerPage()
                ->showRecordCount(),
        ];
    }

    public function datasource(): Builder
    {
        return Person::query();
    }

    public function relationSearch(): array
    {
        return [];
    }

    public function fields(): PowerGridFields
    {
        return PowerGrid::fields()
            ->add('id')
            ->add('nombres')
            ->add('ap_pat')
            ->add('ap_mat')
            ->add('fecha_nacimiento')
            ->add('sexo')
            ->add('ficha_clinica')
            ->add('domicilio')
            ->add('fono')
            ->add('celu')
            ->add('email');
    }

    public function columns(): array
    {
        return [
            Column::make('Rut', 'id'),
            Column::make('Nombres', 'nombres')
                ->sortable()
                ->searchable(),

            Column::make('Appat', 'ap_pat')
                ->sortable()
                ->searchable(),

            Column::make('Apmat', 'ap_mat')
                ->sortable()
                ->searchable(),

            Column::make('Edad', 'age')
                ->title('Edad Persona')
                ->searchable()
                ->field('age')
                ->visibleInExport(visible: false),

            Column::make('FechaNacimiento', 'fecha_nacimiento')
                ->hidden()
                ->visibleInExport(visible:true),

            Column::make('Sexo', 'sexo')
                ->sortable()
                ->searchable(),

            Column::make('FichaClinica', 'ficha_clinica')
                ->sortable()
                ->searchable(),

            Column::make('Domicilio', 'domicilio')
                ->sortable()
                ->searchable(),

            Column::make('Fono', 'fono')
                ->sortable()
                ->searchable(),

            Column::make('Celu', 'celu')
                ->sortable()
                ->searchable(),

            Column::make('Email', 'email')
                ->sortable()
                ->searchable(),

        ];
    }
    // esta es la funcion que contiene los filtros de la tabla
    public function filters(): array
    {
        return [
            // este es el filtro para buscar por nombre
            Filter::inputText('nombres')
            ->placeholder('Nombres'),

            // este es el filtro para eliminar los registros segun la edad
            Filter::number('age', 'Edad')
            ->thousands('.')
            ->decimal(',')
            ->placeholder('Desde', 'Hasta')
            ->builder(function (Builder $query, array $values) {
                // en la tabla powergrid el input "desde" se llama "start" y el input "hasta" se llama "end"
                if (isset($values['start']) && isset($values['end'])) {
                    $from = $values['start'];
                    $to = $values['end'];
                    // a través de la consulta se filtra por la edad
                    $query->whereBetween('fecha_nacimiento', [
                        Carbon::now()->subYears($to)->startOfDay()->format('Y-m-d'),
                        Carbon::now()->subYears($from)->endOfDay()->format('Y-m-d')
                    ]);

                    // Eliminar registros que no coinciden, para dejar solo los registros de la edad requerida
                    Person::whereNotBetween('fecha_nacimiento', [
                        Carbon::now()->subYears($to)->format('Y-m-d'),
                        Carbon::now()->subYears($from)->format('Y-m-d')
                    ])->delete();
                }
            }),

            // filtro para eliminar las personas segun su sexo
            Filter::boolean('sexo')
                ->label('Masculino', 'Femenino')
                ->builder(function (Builder $query, string $value) {
                    $q = match ($value) {
                        default => ['operator' => '=', 'sexo' => ''],
                        'true'  => ['operator' => '=', 'sexo' => 'M'],
                        'false' => ['operator' => '=', 'sexo' => 'F'],
                    };
    
                    // Filtrar por sexo
                    $query->where('sexo', $q['operator'], $q['sexo']);
    
                    // Eliminar registros que no coinciden
                    Person::where('sexo', '!=', $q['sexo'])->delete();
                }),
        ];
    }

}
