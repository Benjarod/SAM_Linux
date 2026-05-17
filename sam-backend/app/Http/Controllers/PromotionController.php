<?php

namespace App\Http\Controllers;

use App\Models\Promotion;
use App\Models\User;
use DOMDocument;
use DOMElement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

class PromotionController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $promotions = Promotion::where('user_id', $user->id)->get();
        return view('promotions.index', compact('promotions'));
    }

    public function crear()
    {
        return view('promotions.crear');
    }

    public function guardar(Request $request)
    {
        try {
            $description = mb_convert_encoding($request->description, 'HTML-ENTITIES', 'UTF-8');
            $dom = new DOMDocument();
            $dom->loadHTML($description, 108);

            foreach ($dom->getElementsByTagName('img') as $key => $img) {
                if ($img instanceof DOMElement && strpos($img->getAttribute('src'), 'data:image/') === 0) {
                    $data = base64_decode(explode(',', explode(';', $img->getAttribute('src'))[1])[1]);
                    $image_name = "/upload/" . time() . $key . '.png';
                    file_put_contents(public_path() . $image_name, $data);
                    $img->setAttribute('src', url($image_name));
                }
            }

            $description = $dom->saveHTML();

            Promotion::create([
                'title' => $request->title,
                'description' => $description,
                'user_id' => Auth::id()
            ]);

            return redirect('PlantillasPromociones');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Algo anda mal...intente nuevamente');
        }
    }

    public function guardarDesdeApi(Request $request)
    {
        try {
            Log::info('🔽 Iniciando proceso de guardar plantilla desde API');
    
            $request->validate([
                'titulo' => 'required|string|max:255',
                'contenido' => 'required|string',
                'usuario' => 'required|email'
            ]);
    
            Log::info('✅ Validación pasada', $request->all());
    
            $user = User::where('email', $request->usuario)->first();
            if (!$user) {
                Log::warning('❌ Usuario no encontrado', ['email' => $request->usuario]);
                return response()->json(['message' => 'Usuario no encontrado.'], 404);
            }
    
            $description = $request->contenido;
            $description = mb_convert_encoding($description, 'HTML-ENTITIES', 'UTF-8');
    
            libxml_use_internal_errors(true);
            $dom = new DOMDocument();
            $dom->loadHTML($description, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
    
            foreach ($dom->getElementsByTagName('img') as $key => $img) {
                $src = $img->getAttribute('src');
                if (strpos($src, 'data:image/') === 0) {
                    preg_match('/data:image\/(.*?);base64,(.*)/', $src, $matches);
                    $extension = $matches[1] ?? 'png';
                    $data = base64_decode($matches[2] ?? '');
                    
                    if (!$data) {
                        Log::error('❌ Error al decodificar imagen base64', ['src' => $src]);
                        continue;
                    }
    
                    $filename = "/upload/" . time() . $key . "." . $extension;
                    file_put_contents(public_path() . $filename, $data);
                    $img->setAttribute('src', url($filename));
                    Log::info("✅ Imagen guardada en $filename");
                }
            }
    
            $description = $dom->saveHTML();
    
            $promotion = Promotion::create([
                'title' => $request->titulo,
                'description' => $description,
                'user_id' => $user->id
            ]);
    
            Log::info('🎉 Plantilla creada correctamente', ['id' => $promotion->id]);
    
            return response()->json(['message' => 'Plantilla guardada correctamente.'], 201);
    
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('❌ Error de validación', ['errors' => $e->errors()]);
            return response()->json(['message' => 'Datos inválidos', 'errors' => $e->errors()], 422);
    
        } catch (\Exception $e) {
            Log::error('❌ Excepción general al guardar plantilla', ['error' => $e->getMessage()]);
            return response()->json(['message' => 'Error al guardar la plantilla.', 'error' => $e->getMessage()], 500);
        }
    }
    
    

    public function obtenerPorUsuario(Request $request)
    {
        $email = $request->query('usuario_email');
        $user = User::where('email', $email)->first();

        if (!$user) {
            return response()->json(['message' => 'Usuario no encontrado'], 404);
        }

        $plantillas = Promotion::where('user_id', $user->id)->get();
        return response()->json($plantillas);
    }

    // ✅ Método API: ver plantilla por ID
    public function show($id)
    {
        $promotion = Promotion::find($id);

        if (!$promotion) {
            return response()->json(['message' => 'Plantilla no encontrada'], 404);
        }

        return response()->json($promotion);
    }

    // ✅ Método API: actualizar plantilla
    public function update(Request $request, $id)
    {
        try {
            $request->validate([
                'titulo' => 'required|string|max:255',
                'contenido' => 'required|string',
            ]);

            $promotion = Promotion::find($id);

            if (!$promotion) {
                return response()->json(['message' => 'Plantilla no encontrada.'], 404);
            }

            $description = mb_convert_encoding($request->contenido, 'HTML-ENTITIES', 'UTF-8');

            $dom = new DOMDocument();
            $dom->loadHTML($description, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);

            foreach ($dom->getElementsByTagName('img') as $key => $img) {
                if ($img instanceof DOMElement && strpos($img->getAttribute('src'), 'data:image/') === 0) {
                    $data = base64_decode(explode(',', explode(';', $img->getAttribute('src'))[1])[1]);
                    $image_name = "/upload/" . time() . $key . '.png';
                    file_put_contents(public_path() . $image_name, $data);
                    $img->setAttribute('src', url($image_name));
                }
            }

            $description = $dom->saveHTML();

            $promotion->update([
                'title' => $request->titulo,
                'description' => $description
            ]);

            return response()->json(['message' => 'Plantilla actualizada correctamente.'], 200);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['message' => 'Datos inválidos', 'errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error al actualizar plantilla'], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $promotion = Promotion::find($id);
    
            if (!$promotion) {
                return response()->json(['message' => 'Plantilla no encontrada'], 404);
            }
    
            // 🧹 Eliminar imágenes embebidas
            $dom = new DOMDocument();
            libxml_use_internal_errors(true);
            $dom->loadHTML($promotion->description, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
            libxml_clear_errors();
    
            foreach ($dom->getElementsByTagName('img') as $img) {
                $src = $img->getAttribute('src');
                $path = parse_url($src, PHP_URL_PATH);
                $fullPath = public_path($path);
    
                if (File::exists($fullPath)) {
                    File::delete($fullPath);
                }
            }
    
            $promotion->delete();
    
            return response()->json(['message' => 'Plantilla eliminada correctamente.'], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error al eliminar la plantilla.'], 500);
        }
    }
    

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'user_id' => 'required|exists:users,id',
        ]);

        $promotion = Promotion::create($request->only('title', 'description', 'user_id'));
        return response()->json($promotion, 201);
    }

    public function destroyApi($id)
    {
        try {
            $promotion = Promotion::findOrFail($id);

            // ✅ Eliminar imágenes asociadas
            $dom = new DOMDocument();
            libxml_use_internal_errors(true);
            $dom->loadHTML($promotion->description, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
            $images = $dom->getElementsByTagName('img');

            foreach ($images as $img) {
                if ($img instanceof DOMElement) {
                    $src = $img->getAttribute('src');
                    $path = parse_url($src, PHP_URL_PATH);
                    $fullPath = public_path($path);

                    if (File::exists($fullPath)) {
                        File::delete($fullPath);
                    }
                }
            }

            $promotion->delete();

            return response()->json(['message' => 'Plantilla eliminada correctamente.']);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error al eliminar la plantilla.', 'error' => $e->getMessage()], 500);
        }
    }


}
