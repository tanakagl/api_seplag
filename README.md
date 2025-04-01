Candidato: Matheo Rodrigues Bonucia
Engenheiro de Computação
Vaga: Desenvolvedor PHP - Pleno - Júnior

O projeto possui um Dockerfiler, um compose e um entrypoint para setar todas as configurações necessárias para o teste.
Basta clonar o projeto e rodar o conteiner com "docker compose up -d" dentro da pasta raiz.

Foram utilizados: 
Laravel 12
PostgreSQL 16
Vite - React
PHP 8

Foi criado um seeder para povoar a tabela de pessoas com alguns usuários:
```
public function run(): void
    {
        // Usuário Admin
        User::create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);

        // Usuário Manager
        User::create([
            'name' => 'Manager User',
            'email' => 'manager@example.com',
            'password' => Hash::make('password'),
            'role' => 'manager',
        ]);

        // Usuário Normal
        User::create([
            'name' => 'Regular User',
            'email' => 'user@example.com',
            'password' => Hash::make('password'),
            'role' => 'user',
        ]);
´´´
    }

Basicamente o que muda é o email e senha: password


O sistema possui interface de login própria do starterkit laravel, mas uma interface nova que agrupa em modo bloco os 4 CRUDS solicitados no edital.
