# Open Cookbook

Um gerenciador de receitas, com backend em Laravel e frontend em React. O projeto utiliza autenticação JWT para proteger endpoints e permite que usuários criem, editem, busquem e excluam receitas.

## Tecnologias Utilizadas

-  **Backend**: Laravel 12.0, PHP 8.2, MySQL 8.0, `tymon/jwt-auth` para autenticação
-  **Frontend**: React 18, Vite, Tailwind CSS, `react-router-dom`, `axios`
-  **Containerização**: Docker, Docker Compose
-  **Testes**: PHPUnit 11.5 com cobertura de código
-  **CI/CD**: GitHub Actions para testes automatizados

## Estrutura do Projeto

```
open-cookbook/
├── backend/ # Código do backend (Laravel)
│ ├── app/ # Lógica do aplicativo (Controllers, Models)
│ ├── tests/ # Testes PHPUnit (Feature e Unit)
│ ├── Dockerfile # Configuração do container PHP
│ └── .env.example # Exemplo de variáveis de ambiente
├── frontend/ # Código do frontend (React)
│ ├── src/ # Componentes React, contexto de autenticação
│ ├── Dockerfile # Configuração do container Node
├── docker-compose.yml # Definição dos serviços (backend, frontend, db)
└── README.md # Documentação do projeto
```  

## Pré-requisitos

- **Docker**: Versão 24.0 ou superior
- **Docker Compose**: Versão 2.20 ou superior
- **Git**: Versão 2.30 ou superior

## Configuração do Ambiente

1.  **Clonar o Repositório**:

```bash
git clone git@github.com:aurelioluiz/open-cookbook.git
cd open-cookbook
```

2.  **Copiar Arquivo de Variáveis de Ambiente**:

- Copie o `.env.example` do backend para `.env`:

```bash
cp backend/.env.example backend/.env
```

- Edite `backend/.env` para configurar o banco de dados:

```env
APP_ENV=local
APP_KEY=base64:gerar-com-artisan-key:generate
DB_CONNECTION=mysql
DB_HOST=db
DB_PORT=3306
DB_DATABASE=cookbook
DB_USERNAME=root
DB_PASSWORD=root
JWT_SECRET=gerar-com-artisan-jwt:secret
```

3.  **Construir e Iniciar os Containers**:

```bash
docker-compose build
docker-compose up -d
```

4.  **Instalar Dependências do Backend**:

```bash
docker-compose exec backend composer install
```

5.  **Gerar Chaves do Laravel e JWT**:

```bash
docker-compose exec backend php artisan key:generate
docker-compose exec backend php artisan jwt:secret
```

6.  **Executar Migrações do Banco de Dados**:

```bash
docker-compose exec backend php artisan migrate
```

7.  **Instalar Dependências do Frontend**:

```bash
docker-compose exec frontend npm install
```

**Observação**: É recomendável reinicializar os serviços após instalar as dependências do backend e frontend para garantir que as mudanças sejam aplicadas:

## Executando o Projeto

-  **Backend**: Acesse `http://localhost:8000/api` (endpoints `/api/login`, `/api/register`, `/api/recipes`).
-  **Frontend**: Acesse `http://localhost` (rotas `/login`, `/register`, `/recipes`, `/recipes/:id`).
-  **Banco de Dados**: Conecte-se ao MySQL em `localhost:3306` com as credenciais do `.env`.

Para parar os containers:

```bash
docker-compose  down
```

## Executando Testes

O backend inclui testes PHPUnit para os controladores (`AuthController`, `RecipeController`) e modelos (`User`, `Recipe`).

1.  **Configurar o Ambiente de Teste**:

- Certifique-se de que o `phpunit.xml` está configurado para usar SQLite em memória:

```xml
<env  name="DB_CONNECTION"  value="sqlite"/>
<env  name="DB_DATABASE"  value=":memory:"/>
```

2.  **Rodar os Testes com Cobertura**:

```bash
docker-compose exec backend ./vendor/bin/phpunit --coverage-text
```

## GitHub Actions

O projeto usa GitHub Actions para CI/CD. O workflow `.github/workflows/ci.yml` executa testes PHPUnit automaticamente em pushes e pull requests para a branch `main`.

## Endpoints da API

-  **POST /api/register**: Cria um novo usuário (nome, email, senha).
-  **POST /api/login**: Autentica um usuário e retorna um token JWT.
-  **GET /api/recipes**: Lista receitas do usuário autenticado (suporta busca com `?search=`).
-  **GET /api/recipes/:id**: Exibe uma receita específica.
-  **POST /api/recipes**: Cria uma nova receita (título, descrição).
-  **PUT /api/recipes/:id**: Atualiza uma receita existente.
-  **DELETE /api/recipes/:id**: Exclui uma receita.
