<?php

namespace App\Http\Controllers;

use App\Imports\ImportExcel;
use App\Models\Person;
use ErrorException;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Exceptions\NoTypeDetectedException;
use Maatwebsite\Excel\Facades\Excel;

class GestionBDController extends Controller
{
    public function index()
    {   
        return view('gestionBd');
    }

    public function import(Request $request){
        // esta funcion se encarga de importar los datos de un archivo excel a la base de datos

        // se crea una variable "file" que almacena el archivo excel que se ha subido en el formulario de la vista "gestionBd.blade.php"
        $file = $request->file('excel_file');
        try{
            // se utiliza la clase "Excel" para importar los datos del archivo excel a la base de datos
            Excel::import(new  ImportExcel, $file);
            // luego se redirige a la vista "gestionBd.blade.php" con un mensaje de éxito
            return view('gestionBd');
            // Si este proceso no funciona correctamente, se redirige a la vista "gestionBd.blade.php" con un mensaje de error
        } catch (NoTypeDetectedException $e){
            return redirect()->back()->with('error', 'Se necesita importar un archivo Excel');
        } catch (ErrorException){
            return redirect()->back()->with('error', 'El archivo excel no contiene las columnas necesarias');
        } catch (\Exception){
            return redirect()->back()->with('error', 'Algo anda mal...intente nuevamente');
        }
        
        
    }

    public function destroy(){
        // esta funcion se encarga de eliminar todos los datos de la base de datos,
        // en caso de que la persona subio el excel equivocado pueda borrar los datos importados erroneamente
        Person::truncate();
        return redirect()->back();
    }

    public function save(){
        
        return view('promotions.index');
    }
}
