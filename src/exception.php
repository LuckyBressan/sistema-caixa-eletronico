<?php

/**
 * Classe para exceptions durante o fluxo do caixa eletrônico
 * Gera logs em caso de erro
 */
class ExceptionCaixaEletronico extends Exception {

    /**
     * Constrói a exception gerando log além do throw
     * @param string $mensagem
     * @throws \Exception
     */
    public function __construct(string $mensagem) {
        Log::gravaLog("ERRO: " . $mensagem);
        throw new Exception($mensagem, 503);
    }
}