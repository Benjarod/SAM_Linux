<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\PeopleImport;

class ImportController extends Controller
{
    public function import(Request $request)
    {
        $request->validate([
            'excel' => 'required|mimes:xlsx,xls,csv|max:2048'
        ]);

        $userId = $request->user()->id;
        Excel::import(new PeopleImport($userId), $request->file('excel'));

        return response()->json(['message' => 'Archivo importado correctamente']);
    }
}
