# ArTour — Assistente regional de turismo

![Logo ArTour](assets/img/artour-logo.png)

O **ArTour** é uma aplicação web voltada ao turismo de **Conselheiro Lafaiete, Minas Gerais**. A proposta é facilitar a descoberta de lugares por moradores e visitantes, reunindo informações sobre pontos de interesse e estabelecimentos locais.

## Funcionalidades

- Consulta de locais, com navegação por categorias e filtros.
- Página de detalhes dos locais, com informações e fotos.
- Cadastro e autenticação de usuários.
- Favoritos e avaliações de locais.
- Agenda de eventos com visualização em calendário.
- Cadastro de empresas e painel para gerenciamento dos próprios locais.
- Área administrativa para gerenciar categorias, cidades, locais, fotos, usuários, eventos e empresas.
- Recursos de localização por coordenadas e mapa no gerenciamento de locais.

## Tecnologias

- **PHP** — lógica da aplicação no servidor.
- **MySQL/MariaDB** — persistência dos dados.
- **PDO** — acesso ao banco de dados.
- **HTML5, CSS3 e JavaScript** — interface e interações.
- **Apache/XAMPP** — ambiente sugerido para execução local.

## Organização do projeto

O código é organizado utilizando os padrões **MVC** e **DAO**:

| Diretório | Responsabilidade |
|---|---|
| `controller/` | Controladores que coordenam as operações da aplicação. |
| `model/` | Classes que representam as entidades do sistema. |
| `dao/` | Acesso e persistência dos dados no banco. |
| `view/` | Páginas e interfaces da aplicação. |
| `assets/` | Arquivos CSS, JavaScript e imagens. |
| `database/` | Scripts SQL complementares. |
| `tools/` | Ferramentas auxiliares do projeto. |

## Executar localmente

### 1. Requisitos

Tenha instalado um ambiente com **PHP**, **Apache** e **MySQL ou MariaDB**. O [XAMPP](https://www.apachefriends.org/pt_br/index.html) pode ser utilizado no Windows.

### 2. Baixar o projeto

Clone o repositório para a pasta pública do Apache (por exemplo, `htdocs` no XAMPP):

```bash
git clone https://github.com/ArthurGPereira/turismo_lafaiete.git
```

### 3. Configurar a conexão com o banco

O arquivo `config/Conexao.php` não é disponibilizado no repositório, pois contém configurações locais de conexão. Crie esse arquivo no ambiente de desenvolvimento e disponibilize nele a classe `Conexao`, com o método estático `Conexao::conectar()`, retornando uma conexão PDO.

Use os dados do seu próprio ambiente. **Não publique senhas, tokens ou credenciais no GitHub.**

### 4. Preparar o banco de dados

Prepare o banco de dados base da aplicação e, quando necessário, execute o script complementar:

```
database/02_recursos_novos_artour.sql
```

> **Observação:** o script disponível é complementar; ele não é um dump completo de criação de todas as tabelas do banco utilizado pela aplicação. A estrutura base precisa estar preparada separadamente.

### 5. Abrir a aplicação

Com Apache e banco de dados em execução, acesse no navegador o endereço local correspondente à pasta do projeto. Em uma instalação padrão do XAMPP, por exemplo:

```
http://localhost/turismo_lafaiete/
```

## Segurança e arquivos locais

Arquivos de conexão, credenciais, backups, arquivos temporários e imagens enviadas pelos usuários não fazem parte do conteúdo versionado. As regras de exclusão estão definidas no arquivo `.gitignore`.

---

**ArTour** — tecnologia para aproximar pessoas dos lugares da região.
