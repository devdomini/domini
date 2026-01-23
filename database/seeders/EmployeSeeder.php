<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class EmployeSeeder extends Seeder
{
    public function run(): void
    {
        $employes = [
            // Orange CI
            ['name' => 'Kouassi Yao', 'email' => 'k.yao@orange.ci', 'telephone' => '+225 07 12 34 56 78', 'id_entreprise' => 1, 'num_box' => 'A-101'],
            ['name' => 'Aminata Diallo', 'email' => 'a.diallo@orange.ci', 'telephone' => '+225 05 23 45 67 89', 'id_entreprise' => 1, 'num_box' => 'A-102'],
            ['name' => 'Jean-Marc Bamba', 'email' => 'jm.bamba@orange.ci', 'telephone' => '+225 07 34 56 78 90', 'id_entreprise' => 1, 'num_box' => 'A-103'],
            
            // MTN CI
            ['name' => 'Fatou Traoré', 'email' => 'f.traore@mtn.ci', 'telephone' => '+225 05 45 67 89 01', 'id_entreprise' => 2, 'num_box' => 'B-201'],
            ['name' => 'Moussa Koné', 'email' => 'm.kone@mtn.ci', 'telephone' => '+225 07 56 78 90 12', 'id_entreprise' => 2, 'num_box' => 'B-202'],
            ['name' => 'Awa Touré', 'email' => 'a.toure@mtn.ci', 'telephone' => '+225 05 67 89 01 23', 'id_entreprise' => 2, 'num_box' => 'B-203'],
            
            // Société Générale
            ['name' => 'Eric Kouadio', 'email' => 'e.kouadio@sgci.ci', 'telephone' => '+225 07 78 90 12 34', 'id_entreprise' => 3, 'num_box' => 'C-301'],
            ['name' => 'Marie N\'Guessan', 'email' => 'm.nguessan@sgci.ci', 'telephone' => '+225 05 89 01 23 45', 'id_entreprise' => 3, 'num_box' => 'C-302'],
            
            // Nestlé
            ['name' => 'Abdoul Karim Cissé', 'email' => 'ak.cisse@nestle.ci', 'telephone' => '+225 07 90 12 34 56', 'id_entreprise' => 4, 'num_box' => 'D-401'],
            ['name' => 'Adjoua Koffi', 'email' => 'a.koffi@nestle.ci', 'telephone' => '+225 05 01 23 45 67', 'id_entreprise' => 4, 'num_box' => 'D-402'],
            ['name' => 'Ibrahim Sanogo', 'email' => 'i.sanogo@nestle.ci', 'telephone' => '+225 07 12 34 56 78', 'id_entreprise' => 4, 'num_box' => 'D-403'],
            
            // Bloomfield
            ['name' => 'Désirée Konan', 'email' => 'd.konan@bloomfield.ci', 'telephone' => '+225 05 23 45 67 89', 'id_entreprise' => 5, 'num_box' => 'E-501'],
            ['name' => 'Youssouf Ouattara', 'email' => 'y.ouattara@bloomfield.ci', 'telephone' => '+225 07 34 56 78 90', 'id_entreprise' => 5, 'num_box' => 'E-502'],
            
            // NSIA
            ['name' => 'Clarisse Assi', 'email' => 'c.assi@nsia.ci', 'telephone' => '+225 05 45 67 89 01', 'id_entreprise' => 6, 'num_box' => 'F-601'],
            ['name' => 'Patrick Gnapa', 'email' => 'p.gnapa@nsia.ci', 'telephone' => '+225 07 56 78 90 12', 'id_entreprise' => 6, 'num_box' => 'F-602'],
        ];

        foreach ($employes as $employe) {
            User::create(array_merge($employe, [
                'role' => 'employe',
                'password' => Hash::make('password123'),
                'is_active' => true,
            ]));
        }
    }
}
