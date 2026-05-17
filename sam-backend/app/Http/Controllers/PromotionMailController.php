<?php

namespace App\Http\Controllers;

use App\Imports\ImportExcel;
use App\Mail\PromotionMail;
use App\Models\Person;
use App\Models\Promotion;
use App\Models\User;
use ErrorException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Mail;
use Maatwebsite\Excel\Facades\Excel;

class PromotionMailController extends Controller
{
    public function promocion()
    {   
        try{
            // se obtiene una persona aleatoria de la base de datos para mostrarla en la vista como ejemplo
            $randomPerson = Person::inRandomOrder()->first();
            // si no logra mostrar alguna persona, muestra el error que no hay personas cargadas en la base de datos
            if (!$randomPerson){
                throw new \ErrorException('No hay personas disponibles en la base de datos');
            }
            // se obtiene el usuario auth
            $user = Auth::user();
            // se muestran las promociones de cada usuario, ya que cada promocion tiene el id del usuario que las creo
            $promotions = Promotion::where('user_id', $user->id)->get();
            // si no logra mostrar alguna promocion, muestra el error que no hay promociones cargadas en la base de datos
            if ($promotions->isEmpty()) {
                throw new \ErrorException('No hay promociones disponibles en la base de datos.');
            }

            return view('MailPromotion.index', compact('promotions','randomPerson'));
        } catch (ErrorException){
            return redirect()->back()->with('error', 'Para enviar correos se necesitan personas y promociones en la base de datos');
        } catch (\Exception){
            return redirect()->back()->with('error', 'Algo anda mal...intente nuevamente');
        }
    }

    public function sendMail(Request $request){
        // se obtienen todas las personas de la base de datos
        $People = Person::all();
        // se obtiene la promocion seleccionada en el input de promociones de la vista MailPromotion/index
        $promotion = Promotion::find($request->input('promotion_id'));

        // se crea un ciclo para recorrer cada persona de la base de datos
        foreach ($People as $person) {
            // si la persona tiene un email, se envia un correo con la promocion seleccionada
            if (!empty($person->email)){
                $data = [
                    'nombre' => $person->nombres,
                    'asunto' => $request->input('asunto'),
                    'desde' => $request->input('desde'),
                    'contenido' => $promotion->description,
                ];
                // se envia el correo a las personas mediante una cola de trabajo, con el contenido del array $data
                Mail::to($person->email)->send(new PromotionMail($data));
            }
        }   
        
        return redirect()->back();
    }

    public function promocion_selected($id)
    // esta funcion es para que muestre la promocion seleccionada en la vista MailPromotion/index
    {   
        $promotion = Promotion::find($id);
        return view('MailPromotion.index',compact('promotion'));
    }
}
