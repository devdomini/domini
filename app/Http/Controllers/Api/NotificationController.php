<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    /**
     * Liste des notifications de l'utilisateur connecté.
     */
    public function index(Request $request)
    {
        $notifications = $request->user()->notifications()->paginate(20);
        
        return response()->json([
            'success' => true,
            'data' => $notifications
        ]);
    }

    /**
     * Récupérer les notifications non lues.
     */
    public function unread(Request $request)
    {
        $notifications = $request->user()->unreadNotifications;
        
        return response()->json([
            'success' => true,
            'data' => $notifications
        ]);
    }

    /**
     * Marquer une notification comme lue.
     */
    public function markAsRead(Request $request, $id)
    {
        $notification = $request->user()->notifications()->where('id', $id)->first();

        if ($notification) {
            $notification->markAsRead();
            return response()->json(['success' => true, 'message' => 'Notification marquée comme lue']);
        }

        return response()->json(['success' => false, 'message' => 'Notification introuvable'], 404);
    }

    /**
     * Marquer toutes les notifications comme lues.
     */
    public function markAllAsRead(Request $request)
    {
        $request->user()->unreadNotifications->markAsRead();
        return response()->json(['success' => true, 'message' => 'Toutes les notifications ont été marquées comme lues']);
    }

    /**
     * Supprimer une notification.
     */
    public function destroy(Request $request, $id)
    {
        $notification = $request->user()->notifications()->where('id', $id)->first();

        if ($notification) {
            $notification->delete();
            return response()->json(['success' => true, 'message' => 'Notification supprimée']);
        }

        return response()->json(['success' => false, 'message' => 'Notification introuvable'], 404);
    }
}
