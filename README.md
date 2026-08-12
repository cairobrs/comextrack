# ComexTrack – Import Management System

<p align="center">
  <img src="https://img.shields.io/badge/Laravel-12.x-red.svg" alt="Laravel Version">
  <img src="https://img.shields.io/badge/PHP-8.2+-blue.svg" alt="PHP Version">
  <img src="https://img.shields.io/badge/License-MIT-green.svg" alt="License">
</p>

## Sobre o Projeto

**ComexTrack** é um sistema completo de gerenciamento de processos de importação desenvolvido em Laravel. O sistema permite controlar todo o ciclo de vida de uma importação, desde a abertura do processo até a conclusão, incluindo gestão de documentos, custos, etapas e automação de status.

## Funcionalidades Principais

### Processos de Importação
- Cadastro completo de processos com informações detalhadas (cliente, modal, portos, valores, etc.)
- Cálculo automático de valores em reais usando taxa de câmbio informada
- Identificação automática de processos de alto valor (acima de R$ 500.000)
- Filtros avançados por número, cliente e status

### Documentos
- Gestão de documentos essenciais (BL, Mercante, Invoice, Packing List)
- Criação automática de documentos padrão ao criar processo
- Controle de status (aguardando recebimento, aguardando correções, recebido OK, não aplicável)
- Observações detalhadas para cada documento

### Custos
- Gestão de custos obrigatórios (frete internacional, marinha mercante, armazenagem)
- Controle de frete rodoviário
- Conversão automática para reais usando taxa de câmbio do processo
- Controle de status de pagamento e datas (vencimento e pagamento)

### Etapas do Processo
- Criação e gestão de etapas personalizadas
- Controle de datas previstas e realizadas
- Identificação automática de etapas atrasadas
- Responsáveis e observações por etapa

### Automação de Status
- **Aberto**: Status inicial ao criar processo
- **Em Trânsito**: Ativado automaticamente quando BL é recebido
- **Em Desembaraço**: Ativado automaticamente quando todos os custos obrigatórios são pagos
- **Concluído**: Ativado automaticamente quando:
  - Processo está em desembaraço
  - Todos os custos obrigatórios estão pagos
  - Frete rodoviário está pago
  - Todos os documentos essenciais estão OK

### Dashboard
- Total de processos com pendências (documentos ou custos)
- Total de processos concluídos

### Histórico de Movimentações
- Log automático de todas as alterações importantes
- Registro de mudanças de status (manual e automática)
- Rastreamento de alterações em documentos e custos
- Identificação de usuário responsável por cada ação

## Tecnologias Utilizadas

- **Backend**: Laravel 12.x
- **Frontend**: Blade Templates + Tailwind CSS
- **Banco de Dados**: SQLite (desenvolvimento) / MySQL/PostgreSQL (produção)
- **Autenticação**: Laravel Breeze
- **PHP**: 8.2+

## Pré-requisitos

- PHP 8.2 ou superior
- Composer
- Node.js e NPM (para assets)
- SQLite (desenvolvimento) ou MySQL/PostgreSQL (produção)

## Instalação

### 1. Clonar o Repositório

```bash
git clone https://github.com/seu-usuario/comextrack.git
cd comextrack
```

### 2. Instalar Dependências

```bash
composer install
npm install
```

### 3. Configurar Ambiente

```bash
cp .env.example .env
php artisan key:generate
```

### 4. Configurar Banco de Dados

Edite o arquivo `.env` e configure o banco de dados:

```env
DB_CONNECTION=sqlite
# ou para MySQL/PostgreSQL:
# DB_CONNECTION=mysql
# DB_HOST=127.0.0.1
# DB_PORT=3306
# DB_DATABASE=comextrack
# DB_USERNAME=root
# DB_PASSWORD=
```

Para SQLite, certifique-se de que o arquivo `database/database.sqlite` existe:

```bash
touch database/database.sqlite
```

### 5. Executar Migrações

```bash
php artisan migrate
```

### 6. Compilar Assets

```bash
npm run build
```

### 7. Iniciar Servidor

```bash
php artisan serve
```

O sistema estará disponível em `http://127.0.0.1:8000`

## Capturas de Tela

> **Nota**: Adicione capturas de tela do sistema aqui mostrando:
> - Dashboard principal
> - Lista de processos
> - Detalhes de um processo
> - Formulário de criação/edição

## Estrutura de Pastas

```
comex-track/
├── app/
│   ├── Http/
│   │   ├── Controllers/  # Controllers da aplicação
│   │   └── Requests/     # Form Requests (validações)
│   ├── Models/           # Modelos Eloquent
│   ├── Observers/        # Observers para eventos
│   └── Services/         # Serviços de lógica de negócio
├── database/
│   ├── migrations/       # Migrações do banco de dados
│   └── seeders/          # Seeders (se houver)
├── docs/                 # Documentação técnica
├── resources/
│   └── views/            # Views Blade
├── routes/               # Rotas da aplicação
└── public/               # Arquivos públicos
```

## Documentação Técnica

Documentação completa disponível em `/docs`:

- **[SYSTEM_OVERVIEW.md](docs/SYSTEM_OVERVIEW.md)** - Visão geral do sistema, entidades e relacionamentos
- **[ARCHITECTURE.md](docs/ARCHITECTURE.md)** - Arquitetura e padrões utilizados
- **[FEATURES.md](docs/FEATURES.md)** - Lista detalhada de funcionalidades
- **[DEV_LOG.md](docs/DEV_LOG.md)** - Diário de desenvolvimento

## Testes

Execute os testes automatizados:

```bash
php artisan test
```

## Contribuindo

Contribuições são bem-vindas! Por favor:

1. Faça um fork do projeto
2. Crie uma branch para sua feature (`git checkout -b feature/AmazingFeature`)
3. Commit suas mudanças (`git commit -m 'Add some AmazingFeature'`)
4. Push para a branch (`git push origin feature/AmazingFeature`)
5. Abra um Pull Request

## Licença

Este projeto está licenciado sob a licença MIT - veja o arquivo [LICENSE](LICENSE) para detalhes.

## Desenvolvedor

Desenvolvido usando Laravel

---

**ComexTrack** - Sistema profissional de gerenciamento de importações
