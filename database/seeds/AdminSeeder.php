<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Cria a loja padrão e o usuário administrador do sistema.
     */
    public function run()
    {
        // ============================================================
        // 1. Loja padrão
        // ============================================================
        $lojaId = DB::table('loja_lojas')->insertGetId([
            'nome' => 'Minha Loja',
            'status' => 1,
            'razao' => 'Minha Loja Ltda',
            'cnpj' => '00.000.000/0001-00',
            'endereco' => 'Rua Principal, 100',
            'local' => 'Centro',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->command->info("Loja criada com ID: {$lojaId}");

        // ============================================================
        // 2. Usuário na tabela users (para autenticação Auth)
        // ============================================================
        $userId = DB::table('users')->insertGetId([
            'name' => 'Administrador',
            'email' => 'admin@loja.com',
            'login' => 'admin',
            'password' => Hash::make('admin123'),
            'is_admin' => 1,
            'email_verified_at' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->command->info("Usuário auth criado com ID: {$userId}");

        // ============================================================
        // 3. Usuário admin em loja_usuarios (vinculado via user_id)
        // ============================================================
        DB::table('loja_usuarios')->insert([
            'user_id' => $userId,
            'nome' => 'Administrador',
            'login' => 'admin',
            'password' => Hash::make('admin123'),
            'status' => 1,
            'admin' => 1,
            'sexo' => 'M',
            'loja_id' => $lojaId,
            'database' => 'api_loja',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->command->info('Usuário loja_usuarios vinculado criado: login=admin / senha=admin123');
    }
}
