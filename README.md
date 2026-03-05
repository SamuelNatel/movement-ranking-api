# Movement Ranking API

API RESTful desenvolvida em PHP puro (sem frameworks) para fornecer ranking de movimentos com base nos recordes pessoais cadastrados no banco de dados MySQL 8.

O projeto foi estruturado com foco em organização, separação de responsabilidades e boas práticas de arquitetura backend.

---

## 📚 Sumário

- [📌 Arquitetura](#-arquitetura)
- [🛠️ Decisões Técnicas](#-decisões-técnicas)
- [🚀 Como rodar o projeto localmente](#-como-rodar-o-projeto-localmente)
- [🔐 Autenticação](#-autenticacao)
- [📡 Documentação API](#-documentacao-api)
    - [🏋️ Movimentos](#-movimentos)
- [🧪 Testes realizados](#-testes-realizados)
- [🔮 Possíveis implementações futuras](#-possíveis-implementações-futuras)
- [📚 Considerações finais](#-considerações-finais)

---

## 📌 Arquitetura

A aplicação segue uma arquitetura em camadas:

- **Controller** → Camada de entrada HTTP
- **Service** → Regra de negócio
- **Repository** → Acesso a dados (PDO)
- **Middleware** → Autenticação
- **Core (Router / Response)** → Infraestrutura HTTP

Padrões aplicados:

- PSR-4 (autoload via Composer)
- Strict Types
- PDO com Prepared Statements
- Variáveis de ambiente via Dotenv
- Tratamento global de exceções
- Middleware para autenticação

---

## 🛠️ Decisões Técnicas

### 🌎 Padrão de idioma

- **Código 100% em inglês**
- **Respostas da API em português**

O código foi escrito em inglês por ser o padrão da indústria, enquanto as respostas são em português por decisão de usabilidade, considerando o público consumidor da API.

---

### 📦 Uso mínimo de bibliotecas externas

Foi utilizado o menor número possível de bibliotecas externas para demonstrar domínio técnico em PHP puro.

A única dependência adicionada foi:

- `vlucas/phpdotenv`

Toda a estrutura de Router, Middleware, Response handler e tratamento de exceções foi implementada manualmente.

---

## 🚀 Como rodar o projeto localmente

### 1️⃣ Pré-requisitos

- PHP 8.1+
- Composer
- MySQL 8

### 2️⃣ Instalação

```bash
git clone https://github.com/SamuelNatel/movement-ranking-api
cd movement-ranking-api
composer install
```

### 3️⃣ Configuração do ambiente

Crie o arquivo `.env` na raiz do projeto:

```
DB_HOST=localhost
DB_NAME=movement
DB_USER=root
DB_PASS=

API_AUTH_USER=usuario
API_AUTH_PASSWORD=senha
```

### 4️⃣ Servidor HTTP

```
php -S localhost:8080 -t public
```

---

## 🔐 Autenticação

A API utiliza autenticação via Bearer Token.

Formato do header:

```
Authorization: Bearer base64(API_AUTH_USER:API_AUTH_PASSWORD)
```

Gerando o token:

Para gerar o token de autenticação, siga os passos abaixo:

1. Acesse o site: https://www.base64encode.org/
2. No campo de texto, digite no seguinte formato:

```
usuario:senha
```

Exemplo:

```
admin:123456
```

3. Clique em **Encode**.
4. Copie o valor gerado — esse será seu token Base64.

---

### 📌 Utilizando o token

Após gerar o Base64, envie no header da requisição:

```
Authorization: Basic SEU_TOKEN_AQUI
```

Substitua `SEU_TOKEN_AQUI` pelo valor retornado no site.

---
## 📡 Documentação API

### 🏋️ Movimentos

#### 📊 GET /api/v1/movements/ranking

Retorna o ranking de um movimento específico com base no recorde pessoal (maior peso) de cada usuário.

A busca deve ser realizada informando **apenas um dos parâmetros abaixo**:

- `id` → Identificador do movimento
- `name` → Nome exato do movimento

> ⚠️ Regra de negócio:
> - É obrigatório informar **id OU name**
> - Caso ambos sejam enviados, a API considera o id
> - Caso nenhum seja enviado, a API retorna erro

---

### 🔎 Exemplos de requisição

Buscar por ID:

```
http://localhost:8080/api/v1/movements/ranking?id=3
```

Buscar por nome (movimento com ou sem espaço):

```
http://localhost:8080/api/v1/movements/ranking?name=Deadlift
```

```
http://localhost:8080/api/v1/movements/ranking?name=Back Squat
```

---

```json
RESPONSE 200 (OK)
{
  "movement": "Back Squat",
  "ranking": [
    {
      "position": 1,
      "user": "Joao",
      "personalRecord": 130,
      "date": "2021-01-03 00:00:00"
    },
    {
      "position": 1,
      "user": "Jose",
      "personalRecord": 130,
      "date": "2021-01-03 00:00:00"
    },
    {
      "position": 3,
      "user": "Paulo",
      "personalRecord": 125,
      "date": "2021-01-03 00:00:00"
    }
  ]
}
```

---

```json
RESPONSE 400 (Bad Request)
{
  "error": "Parâmetro do movimento inválido!"
}
```

```json
RESPONSE 401 (Unauthorized)
{
  "error": "Não autorizado!"
}
```

```json
RESPONSE 404 (Not Found)
{
  "error": "Movimento não encontrado!"
}
```

---

## 🧪 Testes realizados

Testes manuais realizados utilizando:

- cURL
- Insomnia
- Servidor embutido do PHP

Cenários testados:

- ✔ Token válido
- ✔ Token inválido
- ✔ Token ausente
- ✔ Movimento inexistente
- ✔ Parâmetros inválidos
- ✔ Rota não encontrada
- ✔ Falha de conexão com banco
- ✔ Tratamento global de exceções

---

## 🔮 Possíveis implementações futuras

- Implementação de JWT
- Endpoint de login (/auth/login)
- Testes automatizados com PHPUnit
- Rate limiting
- Cache de ranking
- Logs estruturados
- Middleware por rota
- Paginação de ranking
- Documentação via OpenAPI

---

## 📚 Considerações finais

O projeto foi desenvolvido com foco em clareza arquitetural, separação de responsabilidades e boas práticas de backend.

A estrutura permite fácil evolução para cenários mais complexos e escaláveis, mantendo organização e previsibilidade.