# Funcionalidades do Sistema - ComexTrack

## Índice

1. [Gestão de Clientes](#gestão-de-clientes)
2. [Gestão de Processos de Importação](#gestão-de-processos-de-importação)
3. [Gestão de Documentos](#gestão-de-documentos)
4. [Gestão de Custos](#gestão-de-custos)
5. [Gestão de Etapas](#gestão-de-etapas)
6. [Automação de Status](#automação-de-status)
7. [Dashboard](#dashboard)
8. [Histórico de Movimentações](#histórico-de-movimentações)
9. [Autenticação e Segurança](#autenticação-e-segurança)

---

## Gestão de Clientes

### Funcionalidades

**CRUD Completo:**
- Criar novo cliente
- Listar clientes (paginado)
- Visualizar detalhes do cliente
- Editar cliente
- Excluir cliente

**Campos do Cliente:**
- Nome fantasia (obrigatório)
- Razão social (opcional)
- CNPJ (opcional)
- Email (opcional)
- Nome do responsável (opcional)
- Telefone do responsável (opcional)
- Observações (opcional)

**Relacionamentos:**
- Visualizar todos os processos de importação do cliente
- Contador de processos por cliente

### Validações

- Nome fantasia obrigatório
- Campos de texto com limite de 255 caracteres
- Email com validação de formato

---

## Gestão de Processos de Importação

### Funcionalidades

**CRUD Completo:**
- Criar novo processo
- Listar processos (paginado, 10 por página)
- Visualizar detalhes completos do processo
- Editar processo
- Excluir processo

**Campos do Processo:**
  - Número do processo (único, obrigatório)
  - Cliente (obrigatório)
  - Responsável interno (opcional)
  - Modal (marítimo, aéreo, rodoviário)
  - NCM principal
  - Descrição da mercadoria (obrigatório)
  - País de origem
  - Porto de origem
  - Porto de destino
  - Valor da fatura
  - Moeda
  - Taxa de câmbio (obrigatória se moeda ≠ BRL)
  - Data de abertura (obrigatório)
  - Data prevista de chegada
  - Status atual (aberto, em_transito, em_desembaraco, concluido, cancelado)
  - Observações

**Filtros Avançados:**
- Busca por número do processo (extrai apenas números do input)
- Filtro por cliente
- Filtro por status atual

**Criação Automática:**
Ao criar processo, são automaticamente criados 4 documentos essenciais (BL, Mercante, Invoice, Packing List) e 4 custos padrão (frete internacional, marinha mercante, armazenagem, frete rodoviário).

**Cálculos Automáticos:**
- Valor em reais calculado usando taxa de câmbio informada
- Identificação automática de processos de alto valor (acima de R$ 500.000)
- Alerta visual para processos de alto valor

### Validações

- Número do processo único e obrigatório
- Cliente obrigatório
- Modal obrigatório (valores: maritimo, aereo, rodoviario)
- Descrição obrigatória
- Taxa de câmbio obrigatória quando moeda ≠ BRL
- Taxa de câmbio numérica e maior que zero
- Data de abertura obrigatória

---

## Gestão de Documentos

### Funcionalidades

**Documentos Essenciais:**
- BL (Bill of Lading)
- Mercante (Declaração de Importação)
- Invoice (Fatura Comercial)
- Packing List (Lista de Embalagem)

**Status de Documentos:**
- Aguardando recebimento
- Aguardando correções
- Recebido OK
- Não aplicável

**Gestão:**
- Editar status do documento
- Adicionar observações
- Observação automática quando status é "recebido_ok"

**Automação:**
Quando BL é marcado como "recebido_ok", o processo muda automaticamente para "em_transito".

### Validações

- Status deve ser um dos valores permitidos
- Observações opcionais

---

## Gestão de Custos

### Funcionalidades

**Tipos de Custo:**
- Frete Internacional (obrigatório)
- Marinha Mercante (obrigatório)
- Armazenagem do Porto (obrigatório)
- Frete Rodoviário (opcional)

**Campos do Custo:**
- Tipo de custo
- Valor
- Moeda (padrão: USD)
- Status de pagamento (pendente, pago)
- Data de vencimento
- Data de pagamento
- Observações

**Cálculos:**
- Valor em reais calculado usando taxa de câmbio do processo
- Se moeda for BRL, retorna valor sem conversão

**Automação:**
Quando todos os custos obrigatórios são pagos, o processo muda automaticamente para "em_desembaraco".

### Validações

- Status de pagamento obrigatório
- Status deve ser um dos valores permitidos (pendente, pago)

---

## Gestão de Etapas

### Funcionalidades

**CRUD Completo:**
- Criar nova etapa
- Listar etapas do processo
- Editar etapa
- Excluir etapa

**Campos da Etapa:**
- Nome da etapa (obrigatório)
- Data prevista
- Data realizada
- Responsável
- Observações

**Status Automático:**
- **Concluída**: quando data_realizada está preenchida
- **Atrasada**: quando data_prevista passou e data_realizada está vazia
- **Pendente**: caso contrário

**Visualização:**
- Etapas ordenadas por data prevista
- Indicadores visuais de status

### Validações

- Nome da etapa obrigatório
- Datas no formato válido

---

## Automação de Status

### Fluxo Automático

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
   - Trigger: Documento BL marcado como "recebido_ok"
   - Ação: Status do processo muda automaticamente

2. **Custos Obrigatórios Pagos → EM_DESEMBARAÇO**
   - Trigger: Todos os 3 custos obrigatórios com status "pago"
   - Ação: Status do processo muda automaticamente

3. **Processo Concluído**
   - Requisitos:
     - Processo em "em_desembaraco"
     - Todos os custos obrigatórios pagos
     - Frete rodoviário pago
     - Todos os documentos essenciais com status "recebido_ok"
   - Ação: Status do processo muda automaticamente

### Proteções

- Processos concluídos ou cancelados não são alterados automaticamente
- Alterações automáticas são registradas no histórico

---

## Dashboard

### Métricas Exibidas

**Total de Processos de Alto Valor:**
- Processos com valor acima de R$ 500.000

**Total de Processos com Pendências:**
- Processos com documentos essenciais pendentes OU
- Processos com custos obrigatórios pendentes

**Total de Processos Concluídos:**
- Processos com status "concluido"

### Visualização

- Cards com números destacados
- Links para listagens filtradas (futuro)

---

## Histórico de Movimentações

### Funcionalidades

**Registro Automático:**
- Todas as alterações importantes são registradas automaticamente
- Não requer ação manual do usuário

**Tipos de Eventos:**
- Alteração de status do processo (manual ou automática)
- Alteração de status de documento
- Alteração de status de pagamento de custo

**Informações Registradas:**
- Data e hora
- Tipo de evento
- Usuário responsável (se autenticado)
- Descrição detalhada
- Dados anteriores (JSON)
- Dados novos (JSON)
- Flag indicando se foi automático

**Visualização:**
- Listagem dos últimos logs (ex: 20 mais recentes)
- Ordenação por data/hora (mais recente primeiro)
- Identificação de alterações automáticas

---

## Autenticação e Segurança

### Funcionalidades

**Autenticação:**
- Login com email e senha
- Registro de novos usuários
- Recuperação de senha
- Verificação de email (opcional)

**Autorização:**
- Todas as rotas protegidas por middleware `auth`
- Policies para controle de acesso (ClientPolicy, ImportPolicy, etc.)

**Segurança:**
- Proteção CSRF automática
- Validação de entrada em todos os formulários
- Proteção contra SQL Injection (Eloquent ORM)
- Senhas hasheadas (bcrypt)

---

## Funcionalidades Futuras (Sugeridas)

- [ ] Upload de arquivos para documentos
- [ ] Notificações por email
- [ ] Relatórios avançados
- [ ] API REST para integração
- [ ] Dashboard com gráficos
- [ ] Busca global
- [ ] Exportação em PDF
- [ ] Filtros salvos
- [ ] Etiquetas/tags para processos
- [ ] Anexos múltiplos por documento

