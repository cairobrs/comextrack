# Estrutura do Projeto ComexTrack

## Visão Geral

Este documento descreve a organização e estrutura do projeto **ComexTrack - Import Management System**, um sistema de gerenciamento de importações construído com Laravel.

## Organização de Diretórios

### `/app/Domain`
Esta pasta contém os **Models** e a **lógica de domínio** do sistema. Aqui ficam as entidades de negócio e suas regras de domínio.

**Estrutura proposta:**
```
app/Domain/
├── Models/          # Modelos de domínio
├── Repositories/    # Repositórios para acesso a dados
└── ValueObjects/    # Objetos de valor
```

### `/app/Http/Controllers/Admin`
Esta pasta contém os **controllers administrativos** do sistema. Todos os controllers relacionados à área administrativa devem ser organizados aqui.

**Estrutura proposta:**
```
app/Http/Controllers/Admin/
├── DashboardController.php
├── ImportController.php
├── UserController.php
└── ...
```

### `/app/Services`
Esta pasta contém os **serviços** da aplicação. Aqui ficam as classes que implementam a lógica de negócio e orquestram as operações entre diferentes camadas.

**Estrutura proposta:**
```
app/Services/
├── ImportService.php
├── DocumentService.php
└── ...
```

### `/docs/architecture`
Esta pasta contém a **documentação de arquitetura** do projeto, incluindo decisões de design, padrões utilizados e diagramas.

## Padrões e Convenções

### Nomenclatura
- **Controllers**: PascalCase com sufixo `Controller` (ex: `ImportController`)
- **Models**: PascalCase singular (ex: `Import`, `Document`)
- **Services**: PascalCase com sufixo `Service` (ex: `ImportService`)
- **Repositories**: PascalCase com sufixo `Repository` (ex: `ImportRepository`)

### Separação de Responsabilidades
- **Controllers**: Apenas recebem requisições, validam e delegam para Services
- **Services**: Contêm a lógica de negócio e orquestram operações
- **Repositories**: Gerenciam acesso e persistência de dados
- **Models**: Representam entidades de domínio com suas regras básicas

## Fluxo de Requisição

```
Request → Controller → Service → Repository → Model → Database
                ↓
            Response
```

## Notas sobre Estrutura

A estrutura proposta inclui:
- Models no `/app/Domain/Models` (atualmente em `/app/Models`)
- Services em `/app/Services` (já implementado)
- Controllers administrativos em `/app/Http/Controllers/Admin` (estrutura criada)
- Repositories para abstração de dados (a ser implementado conforme necessidade)

