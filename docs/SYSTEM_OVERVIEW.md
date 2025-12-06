# Visão Geral do Sistema - ComexTrack

## Introdução

O **ComexTrack** é um sistema completo de gerenciamento de processos de importação que automatiza e organiza todo o ciclo de vida de uma importação, desde a abertura do processo até sua conclusão.

## Entidades Principais

### 1. Client (Cliente)
Representa as empresas clientes que realizam importações.

**Campos principais:**
- Nome fantasia
- Razão social
- CNPJ
- Email
- Nome do responsável
- Telefone do responsável
- Observações

**Relacionamentos:**
- `hasMany` Import (um cliente pode ter muitos processos de importação)

### 2. Import (Processo de Importação)
Entidade central do sistema. Representa um processo completo de importação.

**Campos principais:**
- Número do processo (único)
- Cliente (relacionamento)
- Responsável interno (relacionamento com User)
- Modal (marítimo, aéreo, rodoviário)
- NCM principal
- Descrição da mercadoria
- País de origem
- Porto de origem
- Porto de destino
- Valor da fatura
- Moeda
- Taxa de câmbio (informada pelo usuário)
- Data de abertura
- Data prevista de chegada
- Status atual (aberto, em_transito, em_desembaraco, concluido, cancelado)
- Observações

**Relacionamentos:**
- `belongsTo` Client
- `belongsTo` User (responsável interno)
- `hasMany` ImportStep (etapas do processo)
- `hasMany` ImportDocument (documentos)
- `hasMany` ImportCost (custos)
- `hasMany` ImportLog (histórico de movimentações)

**Regras de Negócio:**
- Ao criar um processo, são automaticamente criados 4 documentos padrão (BL, Mercante, Invoice, Packing List)
- Ao criar um processo, são automaticamente criados 4 custos padrão (frete internacional, marinha mercante, armazenagem, frete rodoviário)
- O valor em reais é calculado usando a taxa de câmbio informada manualmente
- Processos com valor acima de R$ 500.000 são considerados de alto valor

### 3. ImportDocument (Documento)
Representa um documento relacionado ao processo de importação.

**Campos principais:**
- Tipo de documento (BL, Mercante, Invoice, Packing List)
- Status (aguardando_recebimento, aguardando_correcoes, recebido_ok, nao_aplicavel)
- Arquivo (opcional)
- Observações

**Documentos Essenciais:**
- **BL (Bill of Lading)**: Documento de transporte marítimo
- **Mercante**: Declaração de Importação
- **Invoice**: Fatura comercial
- **Packing List**: Lista de embalagem

**Regras de Negócio:**
- Quando o BL é marcado como "recebido_ok", o processo automaticamente muda para "em_transito"
- Quando todos os documentos essenciais estão OK + custos pagos, o processo pode ser concluído

### 4. ImportCost (Custo)
Representa um custo relacionado ao processo de importação.

**Campos principais:**
- Tipo de custo (frete_internacional, marinha_mercante, armazenagem_porto, frete_rodoviario)
- Valor
- Moeda
- Status de pagamento (pendente, pago)
- Data de vencimento
- Data de pagamento
- Observações

**Custos Obrigatórios:**
- Frete Internacional
- Marinha Mercante
- Armazenagem do Porto

**Custo Opcional:**
- Frete Rodoviário

**Regras de Negócio:**
- Quando todos os custos obrigatórios são pagos, o processo muda para "em_desembaraco"
- O valor em reais é calculado usando a taxa de câmbio do processo relacionado

### 5. ImportStep (Etapa)
Representa uma etapa personalizada do processo de importação.

**Campos principais:**
- Nome da etapa
- Data prevista
- Data realizada
- Responsável
- Observações

**Status Automático:**
- **Concluída**: quando data_realizada está preenchida
- **Atrasada**: quando data_prevista passou e data_realizada está vazia
- **Pendente**: caso contrário

### 6. ImportLog (Log de Movimentação)
Registra todas as alterações importantes no processo.

**Campos principais:**
- Tipo de evento (status_processo_alterado, status_documento_alterado, status_custo_alterado)
- Descrição
- Usuário (relacionamento com User)
- Entidade tipo (Import, ImportDocument, ImportCost)
- Entidade ID
- Dados anteriores (JSON)
- Dados novos (JSON)
- Automático (boolean)

## Fluxos Automáticos

### Fluxo de Status do Processo

```
ABERTO
  ↓
  [BL recebido] → EM_TRÂNSITO
  ↓
  [Todos os custos obrigatórios pagos] → EM_DESEMBARAÇO
  ↓
  [Frete rodoviário pago + Documentos OK + Custos OK] → CONCLUÍDO
```

### Regras de Automação

1. **BL Recebido → EM_TRÂNSITO**
   - Quando o documento BL é marcado como "recebido_ok"
   - Ação automática via `ImportDocumentObserver`

2. **Custos Obrigatórios Pagos → EM_DESEMBARAÇO**
   - Quando todos os 3 custos obrigatórios (frete internacional, marinha mercante, armazenagem) estão com status "pago"
   - Ação automática via `ImportCostObserver` → `ImportStatusManager`

3. **Processo Concluído**
   - Requisitos:
     - Processo em "em_desembaraco"
     - Todos os custos obrigatórios pagos
     - Frete rodoviário pago
     - Todos os documentos essenciais com status "recebido_ok"
   - Ação automática via `ImportStatusManager`

## Fases do Processo

### Fase 1: Antes do Navio Atracar
**Foco**: Documentos essenciais
- Invoice
- Packing List
- BL (Bill of Lading)
- Mercante

### Fase 2: Após o Navio Atracar
**Foco**: Custos obrigatórios
- Frete Internacional
- Marinha Mercante
- Armazenagem do Porto

### Fase 3: Transferência / EADI / Frete Rodoviário
**Foco**: Etapas finais e frete rodoviário
- Etapas personalizadas (agendamento de coleta, transferência para EADI)
- Frete Rodoviário

## Relacionamentos entre Entidades

```
Client
  └── hasMany → Import
        ├── belongsTo → User (responsável interno)
        ├── hasMany → ImportStep
        ├── hasMany → ImportDocument
        ├── hasMany → ImportCost
        └── hasMany → ImportLog
              └── belongsTo → User
```

## Cálculos Automáticos

### Valor em Reais
```
valor_fatura_em_reais = valor_fatura * taxa_cambio
```

Se a moeda for BRL, retorna o valor sem conversão.

### Alto Valor
```
is_high_value = valor_fatura_em_reais > 500000
```

### Status de Etapa
- Concluída: `data_realizada !== null`
- Atrasada: `data_prevista < hoje && data_realizada === null`
- Pendente: caso contrário

## Histórico de Movimentações

Todas as alterações importantes são registradas automaticamente:

1. **Alteração de Status do Processo**
   - Manual ou automática
   - Registra status anterior e novo
   - Identifica se foi automático

2. **Alteração de Status de Documento**
   - Registra qual documento foi alterado
   - Status anterior e novo

3. **Alteração de Status de Custo**
   - Registra qual custo foi alterado
   - Status de pagamento anterior e novo

Todos os logs incluem:
- Data e hora
- Usuário responsável (se autenticado)
- Descrição detalhada
- Dados anteriores e novos (JSON)

