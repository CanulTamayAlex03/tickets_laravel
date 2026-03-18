<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Extension;
use Maatwebsite\Excel\Facades\Excel;

class ExtensionController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');

        $extensiones = Extension::withTrashed()
            ->when($search, function ($query, $search) {
                return $query->where(function ($q) use ($search) {
                    $q->where('nombre_extension', 'like', '%' . $search . '%')
                        ->orWhere('extension', 'like', '%' . $search . '%');
                });
            })
            ->orderByRaw('deleted_at IS NULL DESC, extension ASC')
            ->paginate(50);

        return view('administrador.admin.extensiones', compact('extensiones', 'search'));
    }

    public function toggleActive($id)
    {
        try {
            $extension = Extension::withTrashed()->findOrFail($id);

            if ($extension->trashed()) {
                $extension->restore();
                $message = 'Extensión activada exitosamente';
            } else {
                $extension->delete();
                $message = 'Extensión inactivada exitosamente';
            }

            return response()->json([
                'success' => true,
                'message' => $message
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al cambiar el estado: ' . $e->getMessage()
            ], 500);
        }
    }

    public function importar(Request $request)
    {
        $request->validate([
            'archivo' => 'required|mimes:xlsx,xls,csv'
        ]);

        $data = Excel::toArray([], $request->file('archivo'));
        $filas = $data[0];

        $nuevos = 0;
        $actualizados = 0;

        foreach ($filas as $index => $fila) {
            if ($index == 0) continue;

            $nombre = trim($fila[0] ?? '');
            $extension = trim($fila[1] ?? '');

            if ($nombre == '' && $extension == '') continue;
            if (!is_numeric($extension)) {
                return back()->with('error', 'La extensión debe ser numérica en la fila ' . ($index + 1));
            }

            // Buscar también en inactivos
            $extExistente = Extension::withTrashed()
                ->where('extension', $extension)
                ->first();

            if ($extExistente) {
                $extExistente->update([
                    'nombre_extension' => $nombre
                ]);

                // Si estaba inactiva, la reactivamos automáticamente
                if ($extExistente->trashed()) {
                    $extExistente->restore();
                }

                $actualizados++;
            } else {
                Extension::create([
                    'nombre_extension' => $nombre,
                    'extension' => $extension
                ]);
                $nuevos++;
            }
        }

        return back()->with(
            'success',
            "Importación completada: $nuevos extensiones nuevas, $actualizados extensiones actualizadas."
        );
    }

    public function getActive()
    {
        try {
            $extensiones = Extension::whereNull('deleted_at')
                ->orderBy('extension', 'asc')
                ->get(['id', 'nombre_extension', 'extension']);

            return response()->json($extensiones);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error al cargar las extensiones'], 500);
        }
    }
}
