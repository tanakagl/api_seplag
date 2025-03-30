<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CheckAndSeedDatabase extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:check-and-seed';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Verifica se o banco já tem dados e só executa seeders se necessário';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $count = DB::table('cidade')->count();
        
        if ($count == 0) {
            $this->info('Banco de dados vazio. Executando seeders...');
            $this->call('db:seed', ['--force' => true]);
        } else {
            $this->info("Banco de dados já possui {$count} registros. Pulando seeders.");
        }
        
        return Command::SUCCESS;
    }
}
