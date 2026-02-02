<div align="center">

<img src="public/assets/imagens/exemplo-logo-readme.png" width="100px">


<h1>Loja Ponto Com</h1>

</div>


<div align="center">

![Status](https://img.shields.io/badge/Status-Finalizado-green)
![PHP](https://img.shields.io/badge/PHP-777BB4?logo=php&logoColor=white)
![MySQL](https://img.shields.io/badge/MySQL-4479A1?logo=mysql&logoColor=white)
![JavaScript](https://img.shields.io/badge/JavaScript-F7DF1E?logo=javascript&logoColor=black)
![Redis](https://img.shields.io/badge/Redis-DC382D?logo=redis&logoColor=white)
![E2E](https://img.shields.io/badge/E2E%20Tests-Cypress-brightgreen?logo=cypress)
![Docker](https://img.shields.io/badge/Docker-2496ED?logo=docker&logoColor=white)
</div>

---

## Índice

- [Sobre o Projeto](#sobre-o-projeto)
- [Funcionalidades](#funcionalidades)
- [Tecnologias Utilizadas](#tecnologias-utilizadas)
- [Como Instalar e Rodar o Projeto](#como-instalar-e-rodar-o-projeto)
- [Como Rodar os Testes](#como-rodar-os-testes)
- [Estrutura de CI/CD](#estrutura-de-cicd)
- [Como Contribuir](#como-contribuir)
- [Capturas de Tela](#capturas-de-tela)
- [Autores](#autores)
- [Suporte](#suporte)

---


## Sobre o Projeto

**Loja Ponto Com** é uma plataforma de e-commerce desenvolvida como projeto prático da disciplina de Desenvolvimento Web 1 do IFSP. O sistema permite que usuários comprem produtos de diversos fornecedores, além de possibilitar que os próprios usuários se cadastrem como vendedores para oferecer seus produtos na plataforma.

### Objetivo

Criar uma solução de marketplace robusta e escalável que:
- Facilite a compra e venda de produtos online com alta performance.
- Ofereça uma experiência de uso fluida e responsiva.
- Implemente práticas modernas de desenvolvimento web (Cache, CDN, Automação de Testes).

---

## Funcionalidades

### Para Clientes

-  **Cadastro e Login**: Sistema completo de autenticação.
-  **Catálogo de Produtos**: Navegação por categorias e visualização detalhada.
-  **Busca e Filtros**: Busca inteligente por nome de produtos.
-  **Comparação de Produtos**: Ferramenta para comparar até 3 produtos lado a lado.
-  **Sistema de Cupons**: Aplicação de códigos promocionais no carrinho.
-  **Notificações em Tempo Real**: Alertas sobre status de pedidos, novidades e mensagens.
-  **Carrinho de Compras**: Gestão completa de itens e quantidades.
-  **Checkout Seguro**: Processo de múltiplas etapas (Endereço, Pagamento, Confirmação).
-  **Múltiplas Formas de Pagamento**: Simulação de Cartão de Crédito, PIX e Boleto.
-  **Histórico e Avaliações**: Acompanhamento de pedidos e review de produtos com upload de fotos.

### Para Fornecedores

-  **Dashboard Aprimorado**: Visão geral de vendas e produtos.
-  **Gestão de Produtos**: CRUD completo de produtos.
-  **Upload Otimizado**: Integração com Cloudinary para armazenamento de imagens.
-  **Modo Editor**: Interface *drag-and-drop* para organizar vitrines na Home.
-  **Sistema de Rascunhos**: Salvar produtos para edição posterior.
-  **Controle de Estoque**: Gerenciamento em tempo real.

### Funcionalidades Técnicas

-  **Performance com Redis**: Cache de produtos e sessões para carregamento instantâneo.
-  **CDN de Imagens**: Entrega otimizada de assets via Cloudinary.
-  **Segurança**: Prepared Statements, CSRF protection e tratamento de XSS.
-  **CI/CD**: Pipeline automatizado no GitHub Actions rodando testes E2E.
-  **Responsividade**: Layout adaptável para todos os dispositivos.

---

## Tecnologias Utilizadas

### Frontend
- **HTML5**: Estruturação semântica.
- **CSS3**: Estilização vanilla moderna.
- **JavaScript (ES6+)**: Interatividade e chamadas assíncronas.
- **Ajax/Fetch API**: Comunicação dinâmica com o backend.

### Backend
- **PHP 8.2+**: Linguagem core do sistema.
- **PDO**: Abstração segura de banco de dados.
- **Redis**: Sistema de cache (via `predis/predis`).
- **Cloudinary SDK**: Gerenciamento de mídia na nuvem.

### Banco de Dados
- **MySQL 8.0**: Armazenamento relacional principal.
- **Redis**: Armazenamento em memória (Key-Value) para cache.

### DevOps & QA
- **GitHub Actions**: Automação de CI/CD.
- **Cypress**: Testes End-to-End (E2E).
- **PHPUnit**: Testes unitários de backend.
- **Composer**: Gerenciador de dependências PHP.
- **NPM**: Gerenciador de dependências Frontend/Testes.

---

## Como Instalar e Rodar o Projeto

### Docker (Recomendado)

A maneira mais fácil de rodar o projeto é utilizando o Docker, pois ele sobe todos os serviços (PHP, MySQL, Redis) automaticamente.

**Pré-requisitos:**
- [Docker](https://www.docker.com/products/docker-desktop/) instalado e rodando.

**Passo a Passo:**

1.  **Clone o repositório:**
    ```bash
    git clone https://github.com/mar-moraes/Loja-Ponto-Com.git
    cd Loja-Ponto-Com
    ```

2.  **Configure o ambiente:**
    - Crie um arquivo `.env` na raiz (copie do `.env.example`).
    - **Importante:** Se for rodar com Docker, ajuste o host do banco de dados e do Redis no `.env` para apontar para os containers ou use os valores padrões do `docker-compose.yml`.

3.  **Suba os containers:**
    ```bash
    docker-compose up -d --build
    ```

O sistema estará acessível em: `http://localhost:80` (ou apenas `http://localhost`).

---

### Instalação Manual (Sem Docker)

#### Pré-requisitos

- **PHP 8.2+**
- **MySQL 8.0+**
- **Redis Server** (Obrigatório para funcionalidades de cache)
- **Composer**
- **Node.js 18+** (Para rodar os testes Cypress)

### Passo a Passo

#### 1. Clone o Repositório

```bash
git clone https://github.com/mar-moraes/Loja-Ponto-Com.git
cd Loja-Ponto-Com
```

#### 2. Instale as Dependências e Ferramentas

Para facilitar, execute o arquivo `requirements.bat` (no Windows), ele irá:
- Verificar e instalar o **Redis** automaticamente (caso não esteja instalado).
- Instalar dependências do **PHP** (Backend).
- Instalar dependências do **Node.js** (Testes).

Basta dar dois cliques no arquivo ou rodar no terminal:
```bash
.\requirements.bat
```

 **Nota:** Caso prefira instalar manualmente:
 *   [Redis](https://github.com/tporadowski/redis/releases/download/v5.0.14.1/Redis-x64-5.0.14.1.msi): Baixe e instale via MSI
 *   PHP: `composer install`
 *   Node: `cd tests/E2E && npm install`

#### 3. Configuração de Variáveis de Ambiente

Crie um arquivo `.env` na raiz do projeto (use `.env.example` como base):

```ini
# Cloudinary (Imagens)
# Pegue sua URL no Dashboard do Cloudinary: https://console.cloudinary.com/
CLOUDINARY_URL=cloudinary://API_KEY:API_SECRET@CLOUD_NAME

# Redis (Cache)
REDIS_HOST=127.0.0.1
REDIS_PORT=6379
# REDIS_PASSWORD=null 
```

#### 4. Configure o Banco de Dados

1.  Crie um banco de dados chamado `bancodadosteste`.
2.  Importe o arquivo `Banco de dados/bancodadosteste.sql`.
3.  Configure as credenciais em `Banco de dados/conexao.php`:

```php
$host = 'localhost';
$dbname = 'bancodadosteste';
$username = 'seu_usuario';
$password = 'sua_senha';
```

#### 5. Inicie os Serviços

Certifique-se de que o **MySQL** e o **Redis** estejam rodando.

Inicie o servidor PHP embutido:
```bash
php -S localhost:3000
```

Acesse o sistema em: `http://localhost:3000/src/index.php`

---

## Como Rodar os Testes

O projeto possui testes automatizados (E2E com Cypress) para garantir a qualidade.

### Via Script Automático (Windows)
Execute o script `run_tests.ps1` com PowerShell. Ele irá:
1.  Iniciar o servidor PHP embutido.
2.  Rodar os testes do Cypress em modo *headless* (terminal).
3.  Encerrar o servidor ao final.

```powershell
.\run_tests.ps1
```

### Manualmente
1.  Inicie o servidor PHP: `php -S localhost:8000 -t .`
2.  Em outro terminal, entre na pasta de testes: `cd tests/E2E`
3.  Execute o Cypress: `npx cypress run` (headless) ou `npx cypress open` (interface visual).

---

## Estrutura de CI/CD

O projeto utiliza **GitHub Actions** para Integração Contínua. A cada push na branch `main`:
1.  Sobe containers MySQL e Redis.
2.  Instala dependências PHP e Node.js.
3.  Roda testes unitários (PHPUnit).
4.  Roda testes E2E (Cypress) para validar fluxos críticos (Login, Compra, Cadastro de Produto).

Arquivo de configuração: `.github/workflows/ci.yml`

---

## Como Contribuir

Para detalhes sobre como colaborar com este projeto, leia o **[Guia de Contribuição](CONTRIBUTING.md)**.

---

## Capturas de Tela

### Página Inicial (Catálogo)
![Página Inicial](Artefatos/Readme/Fotos%20do%20readme/homepage.jpg)

### Detalhes do Produto
![Produto](Artefatos/Readme/Fotos%20do%20readme/produto_1.jpg)

### Carrinho e Cupons
![Carrinho](Artefatos/Readme/Fotos%20do%20readme/carrinho.jpg)

### Checkout
![Pagamento](Artefatos/Readme/Fotos%20do%20readme/pagamento.jpg)

---

### Minha Conta

![Minha Conta](Artefatos/Readme/Fotos%20do%20readme/minha_conta_endereço.jpg)

---

### Painel do Fornecedor

![Painel Fornecedor](Artefatos/Readme/Fotos%20do%20readme/fornecedor.jpg)

---

## Autores

<table width="100%">
  <tr>
    <td align="left" width="33%">
      <a href="https://github.com/mar-moraes">
        <img src="https://github.com/mar-moraes.png" width="80" />
      </a>
    </td>
    <td align="center" width="33%">
      <a href="https://github.com/Vanamaral">
        <img src="https://github.com/Vanamaral.png" width="80" />
      </a>
    </td>
    <td align="right" width="33%">
      <a href="https://github.com/Igaust-5767">
        <img src="https://github.com/Igaust-5767.png" width="80" />
      </a>
    </td>
  </tr>
</table>

---

## Suporte

Encontrou algum problema ou tem alguma sugestão? Abra uma [issue](https://github.com/mar-moraes/Loja-Ponto-Com/issues) no GitHub.

---

