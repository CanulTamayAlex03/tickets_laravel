<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use App\Models\SupportPersonal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{

    public function getAssignedTicketsCount(Request $request)
    {
        if (!auth()->user()->can('notificaciones tickets asignados')) {
            return response()->json([
                'success' => true,
                'count' => 0
            ]);
        }
        
        $user = Auth::user();
        
        $supportPersonal = SupportPersonal::where('email', $user->email)->first();
        
        if (!$supportPersonal) {
            return response()->json([
                'success' => true,
                'count' => 0,
                'message' => 'No se encontró información de soporte para este usuario'
            ]);
        }
        
        $count = Ticket::where('support_personal_id', $supportPersonal->id)
            ->where('service_status_id', 2)
            ->count();
        
        return response()->json([
            'success' => true,
            'count' => $count,
            'support_personal_id' => $supportPersonal->id
        ]);
    }
}