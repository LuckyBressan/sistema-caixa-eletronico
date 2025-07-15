<?php

/**
 * Classe de construção da cédula
 * @author Lucas Adriano dos Santos Bressan
 */
class ModelCedula {

    const CEDULA_2 = 2,
        CEDULA_5 = 5,
        CEDULA_10 = 10,
        CEDULA_20 = 20,
        CEDULA_50 = 50,
        CEDULA_100 = 100,
        CEDULA_200 = 200;

    const CEDULAS_DISPONIVEIS = [
        self::CEDULA_2,
        self::CEDULA_5,
        self::CEDULA_10,
        self::CEDULA_20,
        self::CEDULA_50,
        self::CEDULA_100,
        self::CEDULA_200
    ];

    /**
     * Valor da cédula
     * O valor deve seguir as constantes da classe
     */
    public int $valor {
        set(int $valor) {
            self::validaCedula($valor);
            $this->valor = $valor;
        }
    }

    /**
     * Quantidade de cédulas
     * A quantidade não deve ser inferior a 0
     */
    public int $quantidade {
        set(int $quantidade) {
            if($quantidade < 0) {
                throw new InvalidArgumentException('Cédula não pode conter quantidade menor que zero');
            }
            $this->quantidade = $quantidade;
        }
    }

    /**
     * Construtor da classe de cédula
     * @param int $valor valor da cédula (UTILIZAR CONSTANTES DISPONÍVEIS NA CLASSE)
     * @param int $quantidade quantidade de cédulas do tipo/valor (NÃO PODE SER INFERIOR A ZERO)
     */
    public function __construct(int $valor, int $quantidade = 1) {
        $this->valor      = $valor;
        $this->quantidade = $quantidade;
    }

    /**
     * Calcula o valor total das cédulas, multiplicando o valor pela quantidade de cédulas
     */
    public function calculaValor(): int {
        return $this->valor * $this->quantidade;
    }

    /**
     * Aumenta a quantidade de cédulas
     * @param int $quantidade
     */
    public function aumentaQuantidade(int $quantidade): static {
        $this->quantidade += $quantidade;
        return $this;
    }

    /**
     * Diminui a quantidade de cédulas
     * @param int $quantidade
     */
    public function diminuiQuantidade(int $quantidade): static {
        $this->quantidade -= $quantidade;
        return $this;
    }

    /**
     * Valida se a cédula informada por parâmetro corresponde com as disponibilizadas nacionalmente
     * @param int $valor
     * @throws \InvalidArgumentException
     * @return bool
     */
    public static function validaCedula(int $valor) {
        if(!in_array($valor, self::CEDULAS_DISPONIVEIS)) {
            throw new InvalidArgumentException('Cédula informada não corresponde às existentes no território nacional.');
        }
        return true;
    }

}