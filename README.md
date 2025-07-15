# Sistema de Caixa Eletrônico
Projeto em PHP de um sistema de controle de cédulas e movimentação de um caixa eletrônico, seguindo os principíos da programação orientada a objetos e boas práticas.

## Funcionalidades
1. **Consultar total disponível:**
    - Consulta o saldo total disponível no caixa eletrônico durante a execução do fluxo de operações;
2. **Depositar:**
    - Deposita cédulas no caixa eletrônico durante a execução do fluxo de operações;
    - Ação que gera log;
3. **Sacar:**
    - Realiza o saque de cédulas no caixa eletrônico durante a execução do fluxo de operações;
    - Ação que gera log;

## Logs
Ações específicas geram logs, estes logs ficam salvos no arquivo log_caixa.txt na pasta `src`

## Como executar o sistema

1. Clone este repositório ou baixe os arquivos manualmente;
2. Navegue até o diretório `src` no `CMD`;
3. Execute o comando `php` em cima do `src/index.php`, da seguinte forma:
    ```sh
    php src/index.php
    ```
4. O fluxo do sistema começará a rodar no terminal, permitindo que as operações sejam feitas a vontade;
5. Consulte os logs disponíveis em `src/log_caixa.txt`.