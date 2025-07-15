<?php

class Log {

    /**
     * Path do arquivo de logs
     * @var string
     */
    private static string $path = __DIR__ . "/log_caixa.txt";

    /**
     * Retorna a data hora atual para acoplar na string de log
     * @return string
     */
    private static function dataHoraAtual(): string {
        $data = new DateTime();
        return '[' . $data->format('d/m/Y H:i:s') . '] ';
    }

    /**
     * Grava um log de mensagem passada por parâmetro no arquivo de logs
     * @param string $message
     * @throws \InvalidArgumentException
     */
    public static function gravaLog(string $message = '') {
        if(!$message) {
            throw new InvalidArgumentException('O log precisa de uma mensagem.');
        }

        file_put_contents(
            self::$path,
            self::dataHoraAtual() . $message . "\n",
            FILE_APPEND
        );
    }

}