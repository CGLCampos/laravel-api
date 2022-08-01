<?php

namespace App\Exceptions;


use Exception;

class ResponseException extends Exception {

    private string $campo;
    private string $mensagem;

    function __construct($campo, $mensagem) {
        parent::__construct($mensagem);
        $this->campo = $campo;
        $this->mensagem = $mensagem;
    }

    function getCampo(): string {
        return $this->campo;
    }

    function getMensagem(): string {
        return $this->mensagem;
    }

    function getErro(): array {
        return [$this->campo => $this->mensagem];
    }
}