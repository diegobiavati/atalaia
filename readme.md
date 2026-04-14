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

## 🛠 Arquitetura de Dados
O sistema utiliza uma arquitetura multitenant baseada em dois bancos de dados principais:
*   **atalaia (Conexão: mysql):** Focado no Período Básico, cadastro mestre de alunos e gestão de UETEs.
*   **ssaa (Conexão: mysql_ssaa):** Focado na Seção de Supervisão Escolar, contendo índices de provas, GBO e calendários.

## 📊 Regras de Negócio Implementadas
1.  **Cálculo de Notas:** Precisão de 3 casas decimais.
2.  **Bônus Marexaer:** Atletas possuem acréscimo de pontos na média de TFM baseados na performance (Critério: +1.000 ou +2.000).
3.  **Escolha de QMS:** Algoritmo de distribuição automática de vagas baseado na classificação decrescente dos alunos e prioridades escolhidas por eles.
4.  **Disciplina:** Fluxo automatizado de Fato Observado (FO), com conversão para FATD e arquivamento em FRAD/ROD.

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
Certifique-se de que os arquivos de estrutura (`.sql`) estão na pasta `docker-entrypoint-initdb.d/` para criação automática, ou execute as migrations:
```bash
docker exec -it atalaia-app php artisan migrate
```

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

## 🧪 Testes
Para rodar os testes unitários e de integração:
```bash
docker exec -it atalaia-app ./vendor/bin/phpunit

---
**Desenvolvido por:** 1º Ten João Victor  
**Organização:** Exército Brasileiro - Escola de Sargentos das Armas (ESA)

---