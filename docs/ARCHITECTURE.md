# Arquitetura do Sistema - ComexTrack

## Visão Geral

O ComexTrack segue os princípios de **Clean Architecture** e **SOLID**, utilizando o framework Laravel como base. A arquitetura é organizada em camadas bem definidas, facilitando manutenção, testes e evolução do sistema.

## Estrutura de Camadas

```
┌─────────────────────────────────────┐
│         Presentation Layer           │
│  (Controllers, Views, Routes)       │
└──────────────┬───────────────────────┘
               │
┌──────────────▼───────────────────────┐
│      Application Layer              │
│  (Form Requests, Services)         │
└──────────────┬───────────────────────┘
               │
┌──────────────▼───────────────────────┐
│        Domain Layer                 │
│  (Models, Observers, Business Logic)│
└──────────────┬───────────────────────┘
               │
┌──────────────▼───────────────────────┐
│      Infrastructure Layer           │
│  (Database, File System, External) │
└─────────────────────────────────────┘
```

## Componentes Principais

### 1. Controllers (Camada de Apresentação)

**Responsabilidade**: Orquestração de requisições HTTP, validação básica e retorno de respostas.

**Localização**: `app/Http/Controllers/`

**Principais Controllers:**
- `ImportController`: Gerencia processos de importação (CRUD + exportação)
- `ClientController`: Gerencia clientes (CRUD)
- `ImportStepController`: Gerencia etapas do processo
- `ImportDocumentController`: Gerencia documentos
- `ImportCostController`: Gerencia custos
- `DashboardController`: Exibe métricas do dashboard

**Padrão**: Controllers focados apenas em orquestração, delegando lógica de negócio para Services.

### 2. Form Requests (Validação)

**Responsabilidade**: Centralizar validações de entrada e preparação de dados.

**Localização**: `app/Http/Requests/`

**Form Requests:**
- `StoreImportRequest`: Validação para criar processo
- `UpdateImportRequest`: Validação para atualizar processo
- `StoreClientRequest`: Validação para criar cliente
- `UpdateClientRequest`: Validação para atualizar cliente

**Benefícios:**
- Validações reutilizáveis
- Controllers mais limpos
- Melhor testabilidade

### 3. Models (Camada de Domínio)

**Responsabilidade**: Representar entidades de negócio, relacionamentos e lógica de domínio básica.

**Localização**: `app/Models/`

**Models Principais:**
- `Import`: Processo de importação (entidade central)
- `Client`: Cliente
- `ImportDocument`: Documento do processo
- `ImportCost`: Custo do processo
- `ImportStep`: Etapa do processo
- `ImportLog`: Log de movimentação
- `User`: Usuário do sistema

**Características:**
- Relacionamentos Eloquent bem definidos
- Accessors para cálculos (valor em reais, alto valor, status)
- Scopes para consultas comuns
- Métodos de domínio (temDocumentosEssenciaisPendentes, etc.)

### 4. Services (Lógica de Negócio)

**Responsabilidade**: Implementar regras de negócio complexas e orquestrar operações entre diferentes camadas.

**Localização**: `app/Services/`

**Services:**
- `ImportStatusManager`: Gerencia automação de status do processo
  - Avalia condições para mudança de status
  - Implementa regras de transição (aberto → em_transito → em_desembaraco → concluido)
  
- `ImportLogService`: Centraliza criação de logs
  - Registra alterações de status
  - Registra alterações em documentos e custos
  - Identifica se alteração foi automática ou manual

### 5. Observers (Eventos de Domínio)

**Responsabilidade**: Reagir a eventos de modelos e executar ações automáticas.

**Localização**: `app/Observers/`

**Observers:**
- `ImportObserver`: Reage a alterações no modelo Import
  - Registra logs quando status é alterado
  
- `ImportDocumentObserver`: Reage a alterações em documentos
  - Quando BL é recebido, muda processo para "em_transito"
  - Registra logs de alteração de status
  - Avalia se processo pode ser concluído
  
- `ImportCostObserver`: Reage a alterações em custos
  - Registra logs de alteração de pagamento
  - Avalia se processo pode mudar de status

**Padrão**: Observers registrados no `AppServiceProvider`.

### 6. Exports (Exportação de Dados)

**Responsabilidade**: Gerar arquivos Excel com dados do processo.

**Localização**: `app/Exports/`

**Classes:**
- `ImportProcessExport`: Gera arquivo `.xlsx` com 5 abas
  - Resumo do Processo
  - Documentos
  - Custos
  - Etapas
  - Histórico

**Tecnologia**: PhpOffice/PhpSpreadsheet

## Padrões de Design Utilizados

### 1. Repository Pattern (Implícito)
Os Models Eloquent atuam como repositórios, abstraindo acesso a dados.

### 2. Observer Pattern
Observers reagem a eventos de modelos, implementando automações.

### 3. Service Layer Pattern
Services centralizam lógica de negócio complexa.

### 4. Form Request Pattern
Form Requests centralizam validações e preparação de dados.

## Fluxo de Requisição Típico

```
1. Request HTTP
   ↓
2. Route → Controller
   ↓
3. Form Request (validação)
   ↓
4. Controller → Service (se necessário)
   ↓
5. Service → Model
   ↓
6. Model → Database
   ↓
7. Observer (eventos automáticos)
   ↓
8. Response (View/JSON/Redirect)
```

## Exemplo: Atualizar Status de Documento

```
1. Usuário atualiza documento via formulário
   ↓
2. ImportDocumentController@update
   ↓
3. Validação inline (status, observações)
   ↓
4. $document->update($validated)
   ↓
5. ImportDocumentObserver@updated
   ├── Registra log de alteração
   ├── Se BL recebido → muda processo para "em_transito"
   └── ImportStatusManager avalia conclusão
   ↓
6. Redirect com mensagem de sucesso
```

## Automação de Status

A automação de status é implementada através de:

1. **Observers**: Detectam mudanças em documentos e custos
2. **ImportStatusManager**: Avalia condições e atualiza status
3. **ImportLogService**: Registra todas as alterações

**Fluxo:**
```
Documento/Custo Alterado
  ↓
Observer detecta mudança
  ↓
ImportStatusManager avalia condições
  ↓
Se condições atendidas → atualiza status
  ↓
ImportLogService registra log
```

## Segurança

- **Autenticação**: Laravel Breeze (middleware `auth`)
- **Autorização**: Policies (ClientPolicy, ImportPolicy, etc.)
- **Validação**: Form Requests + validação inline
- **Proteção CSRF**: Middleware automático do Laravel
- **SQL Injection**: Protegido pelo Eloquent ORM

## Performance

- **Eager Loading**: Uso de `with()` para evitar N+1 queries
- **Paginação**: Listagens paginadas (10 itens por página)
- **Cache**: Configurado para sessões e cache
- **Indexes**: Chaves estrangeiras e campos únicos indexados

## Testabilidade

- **Separação de responsabilidades**: Facilita testes unitários
- **Dependency Injection**: Services injetáveis
- **Form Requests**: Validações testáveis isoladamente
- **Factories**: Factories para modelos (testes)

## Extensibilidade

A arquitetura permite fácil extensão:

1. **Novos Services**: Adicionar em `app/Services/`
2. **Novos Observers**: Registrar no `AppServiceProvider`
3. **Novos Exports**: Criar em `app/Exports/`
4. **Novos Form Requests**: Criar em `app/Http/Requests/`

## Convenções de Nomenclatura

- **Controllers**: PascalCase + `Controller` (ex: `ImportController`)
- **Models**: PascalCase singular (ex: `Import`)
- **Services**: PascalCase + `Service` (ex: `ImportStatusManager`)
- **Observers**: PascalCase + `Observer` (ex: `ImportObserver`)
- **Form Requests**: PascalCase + `Request` (ex: `StoreImportRequest`)
- **Métodos**: camelCase (ex: `evaluateAndUpdateStatus`)
- **Variáveis**: camelCase (ex: `$importStatus`)

## Melhorias Futuras

Possíveis evoluções arquiteturais:
- Value Objects para valores monetários e datas
- DTOs para transferência de dados complexos
- Repositories para abstração explícita de acesso a dados
- Event System com eventos customizados para melhor desacoplamento
- Queue Jobs para operações pesadas (exportação, notificações)

