<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Casier;
use App\Models\Commune;
use App\Services\CommercialPortfolioService;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

/**
 * API mobile commercial : portefeuille (CRUD entreprises, boxes, listes).
 * Pas d'endpoints export PDF / rapports / graphiques agrégés.
 */
class CommercialApiController extends Controller
{
    public function __construct(
        private readonly CommercialPortfolioService $portfolio
    ) {}

    private function commercial(Request $request)
    {
        $user = $request->user();
        if (! $user || $user->role !== 'commercial') {
            return [null, response()->json(['success' => false, 'message' => 'Accès réservé aux commerciaux'], 403)];
        }

        return [$user, null];
    }

    public function dashboard(Request $request)
    {
        [$user, $err] = $this->commercial($request);
        if ($err) {
            return $err;
        }

        return response()->json([
            'success' => true,
            'data' => $this->portfolio->dashboardStats($user),
        ]);
    }

    public function communes(Request $request)
    {
        [$user, $err] = $this->commercial($request);
        if ($err) {
            return $err;
        }

        $list = Commune::with('warehouse')->orderBy('nom')->get(['id', 'nom', 'warehouse_id']);

        return response()->json(['success' => true, 'data' => $list]);
    }

    public function entreprisesIndex(Request $request)
    {
        [$user, $err] = $this->commercial($request);
        if ($err) {
            return $err;
        }

        $items = $this->portfolio->entreprisesQuery($user)->paginate(30);

        return response()->json([
            'success' => true,
            'data' => [
                'items' => collect($items->items())->map(fn ($e) => $this->portfolio->entrepriseToArray($e)),
                'pagination' => [
                    'current_page' => $items->currentPage(),
                    'last_page' => $items->lastPage(),
                    'per_page' => $items->perPage(),
                    'total' => $items->total(),
                ],
            ],
        ]);
    }

    public function entrepriseShow(Request $request, int $id)
    {
        [$user, $err] = $this->commercial($request);
        if ($err) {
            return $err;
        }

        $e = $this->portfolio->findEntrepriseForCommercial($id, $user);

        return response()->json([
            'success' => true,
            'data' => $this->portfolio->entrepriseToArray($e),
        ]);
    }

    public function entrepriseStore(Request $request)
    {
        [$user, $err] = $this->commercial($request);
        if ($err) {
            return $err;
        }

        $validated = $this->portfolio->validateEntreprisePayload($request);
        $e = $this->portfolio->createEntreprise($user, $validated, $request);

        return response()->json([
            'success' => true,
            'message' => 'Entreprise créée',
            'data' => $this->portfolio->entrepriseToArray($e),
        ], 201);
    }

    public function entrepriseUpdate(Request $request, int $id)
    {
        [$user, $err] = $this->commercial($request);
        if ($err) {
            return $err;
        }

        $e = $this->portfolio->findEntrepriseForCommercial($id, $user);
        $validated = $this->portfolio->validateEntreprisePayload($request, true);
        $e = $this->portfolio->updateEntreprise($e, $validated, $request, $user);

        return response()->json([
            'success' => true,
            'message' => 'Entreprise mise à jour',
            'data' => $this->portfolio->entrepriseToArray($e),
        ]);
    }

    public function boxesIndex(Request $request, int $entrepriseId)
    {
        [$user, $err] = $this->commercial($request);
        if ($err) {
            return $err;
        }

        $this->portfolio->findEntrepriseForCommercial($entrepriseId, $user);
        $boxes = \App\Models\Box::where('id_entreprise', $entrepriseId)
            ->withCount('casiers')
            ->orderByDesc('created_at')
            ->get();

        return response()->json(['success' => true, 'data' => $boxes]);
    }

    public function boxStore(Request $request)
    {
        [$user, $err] = $this->commercial($request);
        if ($err) {
            return $err;
        }

        $validated = $request->validate([
            'id_entreprise' => 'required|exists:entreprises,id',
            'nom' => 'required|string|max:255',
            'adresse' => 'nullable|string',
            'lat' => 'nullable|numeric',
            'long' => 'nullable|numeric',
            'capacite' => 'required|integer|min:1|max:200',
        ]);

        $box = $this->portfolio->createBox($user, $validated);

        return response()->json([
            'success' => true,
            'message' => 'Box créée',
            'data' => $box,
        ], 201);
    }

    public function boxShow(Request $request, int $boxId)
    {
        [$user, $err] = $this->commercial($request);
        if ($err) {
            return $err;
        }

        $box = $this->portfolio->findBoxForCommercial($boxId, $user);

        return response()->json(['success' => true, 'data' => $box]);
    }

    public function casiersIndex(Request $request, int $boxId)
    {
        [$user, $err] = $this->commercial($request);
        if ($err) {
            return $err;
        }

        $box = $this->portfolio->findBoxForCommercial($boxId, $user);
        $casiers = $box->casiers()->with('employe')->orderBy('numero_casier')->get();

        return response()->json(['success' => true, 'data' => $casiers]);
    }

    public function assignCasier(Request $request, int $boxId, int $casierId)
    {
        [$user, $err] = $this->commercial($request);
        if ($err) {
            return $err;
        }

        $validated = $request->validate(['employe_id' => 'required|exists:users,id']);
        $box = $this->portfolio->findBoxForCommercial($boxId, $user);
        $casier = Casier::where('id_box', $box->id)->findOrFail($casierId);

        try {
            $casier = $this->portfolio->assignCasier($user, $box, $casier, (int) $validated['employe_id']);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Attribution impossible',
                'errors' => $e->errors(),
            ], 422);
        }

        return response()->json([
            'success' => true,
            'message' => 'Casier attribué',
            'data' => $casier,
        ]);
    }

    public function unassignCasier(Request $request, int $boxId, int $casierId)
    {
        [$user, $err] = $this->commercial($request);
        if ($err) {
            return $err;
        }

        $box = $this->portfolio->findBoxForCommercial($boxId, $user);
        $casier = Casier::where('id_box', $box->id)->findOrFail($casierId);
        $casier = $this->portfolio->unassignCasier($casier);

        return response()->json([
            'success' => true,
            'message' => 'Casier libéré',
            'data' => $casier,
        ]);
    }

    public function employesIndex(Request $request, int $entrepriseId)
    {
        [$user, $err] = $this->commercial($request);
        if ($err) {
            return $err;
        }

        $e = $this->portfolio->findEntrepriseForCommercial($entrepriseId, $user);

        return response()->json([
            'success' => true,
            'data' => $this->portfolio->employesForEntreprise($e),
        ]);
    }

    public function employeStore(Request $request, int $entrepriseId)
    {
        [$user, $err] = $this->commercial($request);
        if ($err) {
            return $err;
        }

        $e = $this->portfolio->findEntrepriseForCommercial($entrepriseId, $user);
        $validated = $this->portfolio->validateEmployePayload($request);
        $employe = $this->portfolio->createEmploye($user, $e, $validated, $request);

        return response()->json([
            'success' => true,
            'message' => 'Employé créé',
            'data' => $this->portfolio->employeToArray($employe),
        ], 201);
    }

    public function employeUpdate(Request $request, int $employeId)
    {
        [$user, $err] = $this->commercial($request);
        if ($err) {
            return $err;
        }

        $employe = $this->portfolio->findEmployeForCommercial($employeId, $user);
        $validated = $this->portfolio->validateEmployePayload($request, $employe);
        $employe = $this->portfolio->updateEmploye($employe, $validated, $request);

        return response()->json([
            'success' => true,
            'message' => 'Employé mis à jour',
            'data' => $this->portfolio->employeToArray($employe),
        ]);
    }

    public function abonnementsIndex(Request $request)
    {
        [$user, $err] = $this->commercial($request);
        if ($err) {
            return $err;
        }

        $entrepriseId = $request->integer('entreprise_id') ?: null;
        $items = $this->portfolio->abonnementsQuery($user, $entrepriseId)->paginate(30);

        return response()->json([
            'success' => true,
            'data' => $items,
        ]);
    }

    public function paiementsIndex(Request $request)
    {
        [$user, $err] = $this->commercial($request);
        if ($err) {
            return $err;
        }

        $entrepriseId = $request->integer('entreprise_id') ?: null;
        $items = $this->portfolio->paiementsQuery($user, $entrepriseId)->paginate(30);

        return response()->json([
            'success' => true,
            'data' => $items,
        ]);
    }

    public function impayesIndex(Request $request)
    {
        [$user, $err] = $this->commercial($request);
        if ($err) {
            return $err;
        }

        $entrepriseId = $request->integer('entreprise_id') ?: null;
        $items = $this->portfolio->impayesQuery($user, $entrepriseId)->paginate(30);

        return response()->json([
            'success' => true,
            'data' => $items,
        ]);
    }
}
