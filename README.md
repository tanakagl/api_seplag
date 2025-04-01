Candidato: Matheo Rodrigues Bonucia
Engenheiro de Computação
Vaga: Desenvolvedor PHP - Pleno - Júnior

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


⚠️ ATENÇÃO: O sistema possui interface de login própria do starterkit laravel, mas uma interface nova que agrupa em modo bloco os 4 CRUDS solicitados no edital e também registro de endereços
além da rota API ele possui esta rota WEB caso seja de interesse.



API SEPLAG - Sistema de Gestão de Servidores Públicos
📋 Visão Geral
API SEPLAG é um sistema completo para gerenciamento de servidores públicos, incluindo servidores efetivos e temporários, suas lotações, endereços e fotografias. Desenvolvido com Laravel e integrado com MinIO para armazenamento de imagens.

🚀 Funcionalidades
✅ Cadastro e gestão de servidores efetivos e temporários
✅ Gerenciamento de unidades e lotações
✅ Controle de endereços pessoais e funcionais
✅ Upload e gerenciamento de fotografias com armazenamento em MinIO
✅ Autenticação segura com tokens e controle de permissões
✅ API RESTful completa para integração com outros sistemas
🛠️ Tecnologias
Backend: PHP 8.2, Laravel 12
Banco de Dados: PostgreSQL 16
Armazenamento: MinIO (compatível com S3)
Containerização: Docker e Docker Compose
Frontend: Inertia.js, React, TypeScript
📦 Requisitos
Docker e Docker Compose
Git
🔧 Instalação Rápida
1. Clone o repositório
git clone https://github.com/tanakagl/api_seplag.git
cd api_seplag

2. Configure o ambiente
cp .env.example .env

3. Inicie os contêineres
docker-compose up -d

# Crie o bucket no MinIO
php artisan minio:create-bucket seplag

5. Acesse a aplicação
API: http://localhost:8000/api
Interface Web: http://localhost:8000
MinIO Console: http://localhost:9001 (Login: matheorb / Senha: apiseplag)

## 🚀 Inicialização Automatizada

O projeto inclui um script de entrypoint que automatiza várias tarefas de configuração:

- ✅ Verifica a conexão com o banco de dados
- ✅ Gera a chave da aplicação
- ✅ Executa migrações
- ✅ Popula o banco de dados com dados iniciais
- ✅ Limpa os caches
- ✅ Inicia o servidor de desenvolvimento Vite
- ✅ Inicia o servidor PHP

Isso significa que após executar `docker-compose up -d`, a aplicação estará pronta para uso

📚 Estrutura do Projeto
api_seplag/
├── app/                  # Código da aplicação
│   ├── Http/             # Controllers, Middleware, Requests
│   ├── Models/           # Modelos Eloquent
│   └── ...
├── database/             # Migrações e seeders
├── resources/            # Frontend (React/TypeScript)
├── routes/               # Definições de rotas
├── storage/              # Armazenamento local
├── docker-compose.yml    # Configuração Docker
└── ...

🔑 Autenticação e Autorização
A API utiliza Laravel Sanctum para autenticação baseada em tokens. Os tokens têm uma validade de 5 minutos e possuem habilidades (abilities) baseadas no papel do usuário:

Papel	Habilidades	Descrição
admin	* (todas)	Acesso completo a todas as funcionalidades
manager	read, create, update	Pode ler, criar e atualizar registros
user	read	Acesso somente leitura
📡 Endpoints Principais
Autenticação
POST /api/login                # Obter token de acesso
POST /api/refresh-token        # Renovar token
POST /api/logout               # Encerrar sessão
POST /api/me                   # Obter dados do usuário autenticado

Servidores
GET    /api/servidores/efetivos            # Listar servidores efetivos
GET    /api/servidores/efetivos/{id}       # Detalhes de um servidor efetivo
POST   /api/servidores/efetivos            # Criar servidor efetivo
PUT    /api/servidores/efetivos/{id}       # Atualizar servidor efetivo
DELETE /api/servidores/efetivos/{id}       # Excluir servidor efetivo

GET    /api/servidores/temporarios         # Listar servidores temporários
GET    /api/servidores/temporarios/{id}    # Detalhes de um servidor temporário
POST   /api/servidores/temporarios         # Criar servidor temporário
PUT    /api/servidores/temporarios/{id}    # Atualizar servidor temporário
DELETE /api/servidores/temporarios/{id}    # Excluir servidor temporário

Unidades e Lotações
GET    /api/unidades                       # Listar unidades
GET    /api/unidades/{id}                  # Detalhes de uma unidade
POST   /api/unidades                       # Criar unidade
PUT    /api/unidades/{id}                  # Atualizar unidade
DELETE /api/unidades/{id}                  # Excluir unidade

GET    /api/lotacoes                       # Listar lotações
GET    /api/lotacoes/{id}                  # Detalhes de uma lotação
POST   /api/lotacoes                       # Criar lotação
PUT    /api/lotacoes/{id}                  # Atualizar lotação
DELETE /api/lotacoes/{id}                  # Excluir lotação

Endereços
GET    /api/enderecos                      # Listar endereços
GET    /api/enderecos/{id}                 # Detalhes de um endereço
POST   /api/enderecos                      # Criar endereço
PUT    /api/enderecos/{id}                 # Atualizar endereço
DELETE /api/enderecos/{id}                 # Excluir endereço

Fotografias
GET    /api/fotografias/url-temporaria     # Obter URL temporária para uma fotografia
GET    /api/fotografias/pessoa             # Listar fotografias de uma pessoa
POST   /api/fotografias/upload             # Upload de fotografias
DELETE /api/fotografias/{id}               # Excluir uma fotografia

Endpoints Especiais
GET    /api/servidores-efetivos/unidade            # Consultar servidores por unidade
GET    /api/servidores-efetivos/endereco-funcional # Consultar endereço funcional por nome

📝 Exemplos de Uso
Autenticação e obtenção de token
curl -X POST http://localhost:8000/api/login \
  -H "Content-Type: application/json" \
  -d '{"email":"admin@example.com","password":"password"}'

Upload de fotografias
curl -X POST \
  'http://localhost:8000/api/fotografias/upload' \
  -H 'Authorization: Bearer seu_token_aqui' \
  -F 'pes_id=1' \
  -F 'fotografias[]=@/caminho/para/foto.jpg'

Consultar servidores por unidade
curl -X GET \
  'http://localhost:8000/api/servidores-efetivos/unidade?unid_id=1' \
  -H 'Authorization: Bearer seu_token_aqui'

📄 Licença
Este projeto está licenciado sob a Licença MIT.
