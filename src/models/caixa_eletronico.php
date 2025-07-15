<?php

include_once(__DIR__ . "/cedula.php");
include_once(__DIR__ . "/../exception.php");

/**
 * Classe de controle do caixa eletrônico
 */
class ModelCaixaEletronico {

    /**
     * Valor total disponível no caixa eletrônico
     * @var int
     */
    private int $total;

    /**
     * Cédulas disponíveis no caixa
     * @var ModelCedula[]
     */
    private array $cedulas = [];

    public function __construct(array $cedulas = []) {
        $this->cedulas = $cedulas;
    }

    /**
     * Calcula, armazena e retorna o total que está em caixa
     * @return int
     */
    public function totalCaixa(): int {
        if(!isset($this->total)) {
            $iTotal = 0;
            foreach ($this->cedulas as $cedula) {
                $iTotal += $cedula->calculaValor();
            }
            $this->total = $iTotal;
        }
        return $this->total;
    }

    /**
     * Deposita cédulas no caixa eletrônico
     * @param int[] $cedulas
     * @return bool
     */
    public function deposito(array $cedulas): bool {

        Log::gravaLog('Ínicio da operação de depósito.');

        if(!count($cedulas)) {
            throw new ExceptionCaixaEletronico('Não foi repassado nenhuma cédula para depósito');
        }

        $log = [];

        foreach($cedulas as $valor => $quantidade) {

            if(!$valor || (!$quantidade && $quantidade <= 0)) {
                throw new ExceptionCaixaEletronico('Valor ou quantidade de cédulas repassadas é inválido.');
            }

            ModelCedula::validaCedula($valor);

            if(isset($this->cedulas[$valor])) {
                $this->cedulas[$valor]->aumentaQuantidade($quantidade );
            }
            else {
                $this->cedulas[$valor] = new ModelCedula($valor, $quantidade);
            }

            $log[] = $quantidade . 'xR$' . $valor;

            //reseta o valor, para que quando for consultado seja recalculado o total disponível
            unset($this->total);
        }

        Log::gravaLog('Depósito realizado de: ' . implode(', ', $log));

        return true;
    }

    /**
     * Saca as cédulas do caixa eletrônico, baseado no valor passado por parâmetro
     * @param int $valor
     * @throws \ExceptionCaixaEletronico
     * @return int[]
     */
    public function saque(int $valor) {

        Log::gravaLog('Ínicio da operação de saque.');

        if($valor <= 0) {
            throw new ExceptionCaixaEletronico(
                "Valor de saque é inválido, necessário informar um valor maior que zero.\n" .
                "Você pode sacar R$" . $this->totalCaixa()
            );
        }

        if($valor > $this->totalCaixa()) {
            throw new ExceptionCaixaEletronico(
                "Não é possível sacar, pois o valor do saque é maior do que está em saldo.\n" .
                "Você pode sacar R$" . $this->totalCaixa()
            );
        }

        // Ordena as cédulas disponíveis do maior para o menor valor
        $cedulasOrdenadas = $this->cedulas;
        usort($cedulasOrdenadas, function($a, $b) {
            return $b->valor <=> $a->valor;
        });

        $valorRestante = $valor;
        $cedulasParaSaque = [];

        foreach ($cedulasOrdenadas as $cedula) {
            if ($cedula->valor > $valorRestante || $cedula->quantidade == 0) {
                continue;
            }

            $maxCedulas = intdiv($valorRestante, $cedula->valor);
            $cedulasUsadas = min($maxCedulas, $cedula->quantidade);

            if ($cedulasUsadas > 0) {
                $cedulasParaSaque[$cedula->valor] = $cedulasUsadas;
                $valorRestante -= $cedula->valor * $cedulasUsadas;
            }
        }

        if ($valorRestante > 0) {
            throw new ExceptionCaixaEletronico(
                "Não foi possível realizar o saque de R$" . $valor .
                ". Não há cédulas suficientes ou não é possível compor o valor exato com as cédulas disponíveis."
            );
        }

        // Atualiza o estoque de cédulas após o saque
        foreach ($cedulasParaSaque as $valorCedula => $quantidadeSacada) {
            $this->cedulas[$valorCedula]->diminuiQuantidade($quantidadeSacada);
        }

        //reseta o valor, para que quando for consultado seja recalculado o total disponível
        unset($this->total);

        Log::gravaLog('Saque realizado de: R$' . $valor);

        return $cedulasParaSaque;
    }
}