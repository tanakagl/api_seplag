<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Executa a seeder de cidades e estados
        $this->call(CidadeSeeder::class);
        $this->call(EstadoSeeder::class);
        // Executa a seeder que povoa a tabela de usuários
        $this->call(PovoarTabelaUsuarios::class);

        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);
    }
}
