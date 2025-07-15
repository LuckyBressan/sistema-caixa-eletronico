<?php

include_once(__DIR__ . "/models/caixa_eletronico.php");
include_once(__DIR__ . "/log.php");

$estoque = new ModelCaixaEletronico([]);

//Gera log do ínicio das operações
Log::gravaLog('Ínicio das operações de caixa eletrônico');

echo "Olá, este é o sistema de caixa eletrônico! \n";

/**
 * Realiza o fluxo de operações do caixa eletrônico
 * @param ModelCaixaEletronico $estoque
 */
function caixaEletronico(ModelCaixaEletronico $estoque) {
    echo "Informe a operação que você deseja executar:
        1 - Consultar saldo
        2 - Depositar
        3 - Sacar
        4 - Finalizar
    ";

    $opcao = trim(fgets(STDIN));

    echo "\n";

    switch ($opcao) {
        case '1':
            echo "Você escolheu Consultar saldo.\n";
            echo "O saldo disponível é de: R$" . $estoque->totalCaixa() . "\n\n";
            caixaEletronico($estoque);
            break;
        case '2':
            echo "Você escolheu Depositar.\n";
            $cedulas = depositar();
            if($cedulas) {
                $estoque->deposito($cedulas);
            }
            caixaEletronico($estoque);
            break;
        case '3':
            echo "Você escolheu Sacar.\n";
            echo "Informe a quantidade que você quer sacar. (Deve ser um número inteiro, menor ou igual ao saldo disponível)\n\n";

            $opcao = trim(fgets(STDIN));

            if(is_numeric($opcao)) {
                try {
                    $cedulas = $estoque->saque((int) $opcao);

                    echo "Foi realizado o saque das seguintes cédulas: \n";

                    foreach($cedulas as $valor => $quantidade) {
                        echo $quantidade . 'xR$' . $valor . "\n";
                    }

                    echo "Ao todo, foi sacado R$" . $opcao . "\n";
                    echo "Restando R$" . $estoque->totalCaixa() . " de saldo no caixa eletrônico.\n\n";
                } catch (\Throwable $th) {
                    echo $th->getMessage() . "\n\n";
                }
            }
            caixaEletronico($estoque);
            break;
        case '4':
            echo "Finalizando...\n";
            Log::gravaLog('Operações no caixa finalizadas.');
            break;
        default:
            echo "Opção inválida.\n";
            caixaEletronico($estoque);
            break;
    }
}

/**
 * Realiza o fluxo de depósito no caixa
 * @return int[]|bool
 */
function depositar() {
    $cedulas = [];

    $opcao = '';

    echo "Caso queira finalizar o depósito digite: finalizar";

    $fnQuantidadeCedulas = function() {

        $opcao = 0;

        while($opcao <= 0) {
            echo "Informe a quantidade de cédulas:\n";
            $opcao = trim(fgets(STDIN));

            if($opcao == 'finalizar') break;

            if((int)$opcao <= 0) {
                echo "A quantidade de cédulas não pode ser menor ou igual a zero.\n\n";
            }
        }

        return $opcao == 'finalizar' ? false : (int) $opcao;
    };

    while($opcao != 'finalizar') {
        echo "\n\n";
        echo "Informe a cédula que você deseja depositar:\n";
        echo implode(' - ', ModelCedula::CEDULAS_DISPONIVEIS) . "\n\n";

        $opcao = trim(fgets(STDIN));

        if($opcao == 'finalizar') break;

        try {
            ModelCedula::validaCedula($opcao);

            $cedula = $opcao;

            $cedulas[$cedula] = 0;

            $quantidade = $fnQuantidadeCedulas();

            if(!$quantidade) {
                unset($cedulas[$cedula]);
                break;
            }

            $cedulas[$cedula] = $quantidade;

        } catch (\Throwable $th) {
            echo $th->getMessage();
            continue;
        }
    }

    if(!count($cedulas)) {
        echo "Não foi depositado nenhuma cédula.\n\n";
        return false;
    }

    return $cedulas;
}

caixaEletronico($estoque);