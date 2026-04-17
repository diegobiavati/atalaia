# Sistema de Gestão Acadêmica Militar (Atalaia & Gavião)

Este sistema é uma plataforma de gestão acadêmica e administrativa desenvolvida para a Escola de Sargentos das Armas (ESA). Ele automatiza processos desde o Período Básico (Atalaia) até o Período de Qualificação (Gavião/SSAA).

## 🚀 Módulos Principais

*   **Módulo Atalaia:** Gestão de alunos no Período Básico, controle de UETEs e lançamentos de TFM.
*   **Módulo Gavião (SSAA):** Supervisão acadêmica, calendário de provas, Relatório de Aplicação de Prova (RAP) e Grade de Brutos e Objetivos (GBO).
*   **Módulo de Qualificação (QMS):** Processo de escolha de especialidade pelos alunos com base em mérito intelectual.

## 🛠 Tecnologias Utilizadas

*   **Framework:** Laravel 5.6
*   **Linguagem:** PHP 7.3
*   **Bancos de Dados:** MySQL 5.7 (Bancos: `atalaia` e `ssaa`)
*   **Ambiente:** Docker (Nginx, PHP-FPM, MySQL)
*   **Integrações:**
    *   `smbclient`: Para acesso a servidores de arquivos externos.
    *   `JRE (Java)`: Para processamento de dados via arquivos `.jar`.
    *   `MQTT`: Notificações em tempo real.
    *   `Telegram API`: Comunicação direta com os discentes.

## 🗄️ Arquitetura de Banco de Dados

O sistema utiliza dois bancos de dados distintos para isolar a gestão administrativa da supervisão acadêmica:

1.  **Banco `atalaia` (Conexão: `mysql`):** Dados de alunos, operadores, unidades (OMCT) e histórico básico.
2.  **Banco `ssaa` (Conexão: `mysql_ssaa`):** Módulo Gavião, incluindo disciplinas do período de qualificação, índices de provas e GBO.

## 🛠 Qualidade de Código e Testes

O projeto utiliza automação para garantir que apenas código validado seja versionado.

### Ferramentas Utilizadas:
*   **PHPUnit**: Testes de feature e unitários.
*   **PHP_CodeSniffer**: Linter seguindo o padrão PSR-12.
*   **PHPStan**: Análise estática de código (Nível 1).

### Como rodar manualmente:
```bash
# Linter
docker exec -it atalaia-app ./vendor/bin/phpcs

# Análise Estática
docker exec -it atalaia-app ./vendor/bin/phpstan analyze

# Testes
docker exec -it atalaia-app ./vendor/bin/phpunit
```

### Git Hooks ###
Um hook de pre-commit está configurado. Se as verificações falharem, o commit será abortado.

## 📦 Dependências e Ativos (Assets)

Para garantir o funcionamento do sistema em redes restritas (Intranet), as dependências de Front-end foram localizadas:

*   **jQuery UI v1.13.2**: Movido de CDN externo para diretórios locais em `public/css/jquery/` e `public/js/jquery/`.
*   **Protocolo Seguro**: O sistema utiliza o helper `asset()` que, em conjunto com o `URL::forceScheme('https')` configurado no `AppServiceProvider`, garante a entrega via HTTPS.

### Logs do Sistema
O sistema utiliza o Facade `Log` do Laravel para registrar inconsistências de permissão e erros críticos, centralizando-os em `storage/logs/`.

## 📊 Regras de Negócio Implementadas
1.  **Cálculo de Notas:** Precisão de 3 casas decimais.
2.  **Bônus Marexaer:** Atletas possuem acréscimo de pontos na média de TFM baseados na performance (Critério: +1.000 ou +2.000).
3.  **Escolha de QMS:** Algoritmo de distribuição automática de vagas baseado na classificação decrescente dos alunos e prioridades escolhidas por eles.
4.  **Disciplina:** Fluxo automatizado de Fato Observado (FO), com conversão para FATD e arquivamento em FRAD/ROD.

## ⚙️ Automações de Banco de Dados

Para otimizar a performance e garantir a integridade dos dados, o sistema utiliza recursos nativos do MySQL:

### 1. Views
*   **`vw_alunos_esa`**: Consolida dados de alunos que já possuem QMS definida (Período de Qualificação), unindo informações da tabela `alunos` e `ano_formacao`.

### 2. Triggers
*   **`esa_avaliacoes_indice_after_update`**: Disparado sempre que um item de prova (índice) tem seu valor alterado no banco `ssaa`. Ele soma todos os scores dos itens e atualiza automaticamente o campo `gbm` na tabela de avaliações.

## 📦 Como Rodar o Projeto (Docker)

### 1. Pré-requisitos
*   Docker instalado.
*   Docker Compose instalado.

### 2. Configuração Inicial
Clone o repositório e entre na pasta:
```bash
git clone <url-do-repositorio>
cd <pasta-do-projeto>
```

Crie o arquivo de ambiente e configure as senhas e IPs:
```bash
cp .env.example .env
```

### 3. Subindo os Containers
O comando abaixo criará as imagens e subirá os serviços do servidor web, aplicação e banco de dados:
```bash
docker-compose up -d --build
```

### 4. Instalação de Dependências
Entre no container da aplicação e instale as bibliotecas via Composer:
```bash
docker exec -it atalaia-app composer install
docker exec -it atalaia-app php artisan key:generate
```

### 5. Banco de Dados
Após subir os containers, você deve preparar o banco de dados e as permissões fundamentais:

1. **Migrate e Seed:**
   ```bash
   docker exec -it atalaia-app php artisan migrate
   docker exec -it atalaia-app php artisan db:seed
   ```

2. **Credenciais de Administrador (Desenvolvimento):**
   - **Login:** `admin@admin.com`
   - **Senha:** `admin123`
   - **Acesso:** Total (Atalaia e Gavião)

> **Nota:** O `db:seed` limpa as tabelas de operadores e permissões para garantir que o acesso mestre seja restaurado. Use com cautela em produção.

## 📂 Estrutura de Pastas Importantes

*   `app/Http/Controllers/Ajax`: Lógica principal das chamadas assíncronas do sistema.
*   `app/Http/Controllers/Relatorios`: Geração de demonstrativos, fichas disciplinares e mapas de efetivo.
*   `app/Http/OwnClasses`: Classes customizadas como `ClassLog` e `EscolhaQMSLoader`.
*   `app/Models`: Modelos de dados e relacionamentos Eloquent.

## ⚙️ Comandos Úteis

*   **Limpar Logs de Sistema:**
    O sistema gera logs customizados em `public/logs/logsistema/`. Verifique periodicamente o tamanho desta pasta.
*   **Backup do Banco:**
    Existe um comando Artisan para backup manual:
    ```bash
    docker exec -it atalaia-app php artisan backup:multi-sql
    ```

## 🔒 Segurança e Logs
*   Todas as ações críticas (login, alterações de notas, exclusões) são registradas pela classe `ClassLog`.
*   O middleware `CheckAuth` garante a proteção das rotas e redirecionamento correto entre os módulos Atalaia e Gavião.

## 🔒 Segurança (HTTPS)

O sistema está configurado para operar exclusivamente via HTTPS, inclusive em ambiente de desenvolvimento.

### Gerar Certificados Locais
Para rodar localmente, é necessário gerar um certificado autoassinado:
```bash
openssl req -x509 -nodes -days 365 -newkey rsa:2048 -keyout .docker/nginx/ssl/server.key -out .docker/nginx/ssl/server.crt``
O acesso local deve ser feito via: https://localhost:8443

## 🧪 Testes
Para rodar os testes unitários e de integração:
```bash
docker exec -it atalaia-app ./vendor/bin/phpunit

---
**Desenvolvido por:** 1º Ten João Victor  
**Organização:** Exército Brasileiro - Escola de Sargentos das Armas (ESA)

---