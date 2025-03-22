<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class DropAllTables extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'database:drop-all';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Supprime toutes les tables de la base de données';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->warn('Suppression de toutes les tables...');

        DB::statement('SET FOREIGN_KEY_CHECKS=0;'); // Désactiver les clés étrangères

        $tables = DB::select('SHOW TABLES'); // Récupérer toutes les tables

        foreach ($tables as $table) {
            $tableName = array_values((array) $table)[0]; // Récupérer le nom de la table
            
            Schema::dropIfExists($tableName); // Supprime proprement la table
            $this->info("Table supprimée : $tableName");
        }

        DB::statement('SET FOREIGN_KEY_CHECKS=1;'); // Réactiver les contraintes

        $this->info('Toutes les tables ont été supprimées avec succès !');
    }
}
