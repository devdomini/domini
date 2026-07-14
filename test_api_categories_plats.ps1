# Test des API Catégories et Plats
$ErrorActionPreference = "Continue"

$baseUrl = "http://localhost:8000/api"

Write-Host "=========================================" -ForegroundColor Cyan
Write-Host "  TEST API CATEGORIES ET PLATS" -ForegroundColor Cyan
Write-Host "=========================================" -ForegroundColor Cyan
Write-Host ""

# Test 1 : API Catégories
Write-Host "TEST 1 : API Catégories" -ForegroundColor Green
Write-Host "--------------------------------------" -ForegroundColor Gray
Write-Host ""

try {
    $categoriesResponse = Invoke-RestMethod -Uri "$baseUrl/menu/categories" `
        -Method GET `
        -ContentType "application/json" `
        -ErrorAction Stop
    
    Write-Host "SUCCES !" -ForegroundColor Green
    Write-Host "Nombre de catégories: $($categoriesResponse.data.total)" -ForegroundColor Cyan
    Write-Host ""
    Write-Host "Liste des catégories:" -ForegroundColor Yellow
    
    if ($categoriesResponse.data.categories) {
        foreach ($cat in $categoriesResponse.data.categories) {
            Write-Host "  - ID: $($cat.id) | Nom: $($cat.nom) | Disponible: $($cat.est_disponible) | Plats: $($cat.plats_count)" -ForegroundColor White
        }
    }
    Write-Host ""
    
    Write-Host "JSON complet:" -ForegroundColor Gray
    Write-Host ($categoriesResponse | ConvertTo-Json -Depth 5) -ForegroundColor DarkGray
    
} catch {
    Write-Host "ERREUR lors de la requête catégories" -ForegroundColor Red
    Write-Host "Message: $($_.Exception.Message)" -ForegroundColor Yellow
    
    if ($_.Exception.Response) {
        $reader = New-Object System.IO.StreamReader($_.Exception.Response.GetResponseStream())
        $errorBody = $reader.ReadToEnd()
        Write-Host "Réponse: $errorBody" -ForegroundColor Gray
    }
}

Write-Host ""
Start-Sleep -Seconds 2

# Test 2 : API Plats
Write-Host "TEST 2 : API Plats" -ForegroundColor Green
Write-Host "--------------------------------------" -ForegroundColor Gray
Write-Host ""

try {
    $platsResponse = Invoke-RestMethod -Uri "$baseUrl/menu/plats" `
        -Method GET `
        -ContentType "application/json" `
        -ErrorAction Stop
    
    Write-Host "SUCCES !" -ForegroundColor Green
    Write-Host "Nombre de plats: $($platsResponse.data.total)" -ForegroundColor Cyan
    Write-Host ""
    Write-Host "Liste des plats:" -ForegroundColor Yellow
    
    if ($platsResponse.data.plats) {
        $count = 0
        foreach ($plat in $platsResponse.data.plats) {
            $count++
            Write-Host "  - ID: $($plat.id) | Nom: $($plat.nom) | Prix: $($plat.prix) CFA | Dispo: $($plat.est_disponible)" -ForegroundColor White
            if ($count -ge 10) {
                Write-Host "  ... et $($platsResponse.data.total - 10) autres plats" -ForegroundColor Gray
                break
            }
        }
    }
    Write-Host ""
    
    Write-Host "Premier plat (détail):" -ForegroundColor Gray
    if ($platsResponse.data.plats -and $platsResponse.data.plats.Count -gt 0) {
        Write-Host ($platsResponse.data.plats[0] | ConvertTo-Json -Depth 3) -ForegroundColor DarkGray
    }
    
} catch {
    Write-Host "ERREUR lors de la requête plats" -ForegroundColor Red
    Write-Host "Message: $($_.Exception.Message)" -ForegroundColor Yellow
    
    if ($_.Exception.Response) {
        $reader = New-Object System.IO.StreamReader($_.Exception.Response.GetResponseStream())
        $errorBody = $reader.ReadToEnd()
        Write-Host "Réponse: $errorBody" -ForegroundColor Gray
    }
}

Write-Host ""
Write-Host "=========================================" -ForegroundColor Cyan
Write-Host "  VERIFICATION BASE DE DONNEES" -ForegroundColor Cyan
Write-Host "=========================================" -ForegroundColor Cyan
Write-Host ""

# Vérifier directement dans la base de données
Write-Host "Vérification directe dans MySQL..." -ForegroundColor Yellow
Write-Host ""

$sqlCategories = @"
SELECT COUNT(*) as total FROM categories WHERE est_disponible = 1;
SELECT id, nom, est_disponible FROM categories LIMIT 5;
"@

$sqlPlats = @"
SELECT COUNT(*) as total FROM plats WHERE est_disponible = 1;
SELECT id, nom, prix, est_disponible, categorie_id FROM plats LIMIT 5;
"@

Write-Host "Catégories dans la BD:" -ForegroundColor Cyan
php artisan tinker --execute="echo 'Catégories: ' . \App\Models\Categorie::count(); echo PHP_EOL; \App\Models\Categorie::where('est_disponible', true)->get(['id', 'nom'])->each(function(\$c) { echo 'ID: ' . \$c->id . ' - ' . \$c->nom . PHP_EOL; });"

Write-Host ""
Write-Host "Plats dans la BD:" -ForegroundColor Cyan
php artisan tinker --execute="echo 'Plats: ' . \App\Models\Plat::count(); echo PHP_EOL; \App\Models\Plat::where('est_disponible', true)->limit(5)->get(['id', 'nom', 'prix'])->each(function(\$p) { echo 'ID: ' . \$p->id . ' - ' . \$p->nom . ' - ' . \$p->prix . ' CFA' . PHP_EOL; });"

Write-Host ""
Write-Host "=========================================" -ForegroundColor Cyan
Write-Host ""
