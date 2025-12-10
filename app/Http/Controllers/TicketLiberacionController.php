<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class TicketLiberacionController extends Controller
{
    public function show($id)
    {
        try {
            Log::info('Buscando ticket ID:', ['id' => $id]);
            
            $ticket = Ticket::with(['building', 'department', 'employee'])->find($id);
            
            if (!$ticket) {
                Log::warning('Ticket no encontrado:', ['id' => $id]);
                return response()->json([
                    'success' => false,
                    'message' => 'Ticket no encontrado.'
                ], 404);
            }
            
            Log::info('Ticket encontrado:', ['ticket' => $ticket->toArray()]);

            if ($ticket->service_status_id != 2) {
                return response()->json([
                    'success' => false,
                    'message' => 'Este ticket no puede ser liberado en este momento. Debe estar en estado "Atendiendo".'
                ], 400);
            }

            return response()->json([
                'success' => true,
                'ticket' => $ticket
            ]);

        } catch (\Exception $e) {
            Log::error('Error en show method:', [
                'ticket_id' => $id, 
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Error interno del servidor: ' . $e->getMessage()
            ], 500);
        }
    }

    public function liberar(Request $request, $id)
    {
        $request->validate([
            'retroalimentation' => 'nullable|string|max:1000',
            'stars' => 'required|integer|min:1|max:5'
        ]);

        try {
            $ticket = Ticket::findOrFail($id);

            if ($ticket->service_status_id != 2) {
                return response()->json([
                    'success' => false,
                    'message' => 'Este ticket no puede ser liberado.'
                ], 400);
            }

            $ticket->update([
                'service_status_id' => 3,
                'closed_by_user' => 1,
                'retroalimentation' => $request->retroalimentation,
                'stars' => $request->stars,
                'support_closing' => now()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Ticket liberado exitosamente.'
            ]);

        } catch (\Exception $e) {
            Log::error('Error al liberar ticket:', [
                'ticket_id' => $id, 
                'error' => $e->getMessage()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Error al liberar el ticket: ' . $e->getMessage()
            ], 500);
        }
    }
}