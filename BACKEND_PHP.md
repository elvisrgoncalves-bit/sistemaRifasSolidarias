# Contratos para PHP puro

Todos os endpoints usados pelo front ficam na pasta `api/` e devem responder JSON.

Use este cabecalho nos PHP:

```php
<?php
header('Content-Type: application/json; charset=utf-8');
```

Para ler JSON enviado por `fetch`:

```php
$input = json_decode(file_get_contents('php://input'), true) ?? [];
```

## POST api/login.php

Entrada:

```json
{
  "email": "organizador@email.com",
  "senha": "123456",
  "lembrar": "on"
}
```

Resposta esperada:

```json
{
  "id": 1,
  "nome": "Joao",
  "email": "organizador@email.com",
  "perfil": "organizador"
}
```

## POST api/usuarios.php

Cria uma conta.

Entrada:

```json
{
  "nome": "Joao Silva",
  "email": "joao@email.com",
  "telefone": "(11) 99999-9999",
  "senha": "123456"
}
```

Resposta:

```json
{
  "id": 1,
  "mensagem": "Usuario cadastrado com sucesso"
}
```

## GET api/me.php

Retorna o usuario logado pela sessao PHP.

Resposta:

```json
{
  "id": 1,
  "nome": "Joao Silva",
  "email": "joao@email.com"
}
```

## GET api/rifas.php

Lista as rifas do organizador logado.

Resposta:

```json
[
  {
    "id": 1,
    "titulo": "Moto Honda CG 160",
    "descricao": "Ajude nosso projeto social e concorra a uma moto 0km.",
    "valorNumero": 10,
    "quantidadeNumero": 1000,
    "reservados": 450,
    "dataSorteio": "31/08/2026 as 18:00",
    "linkPublico": "rifasolidaria.com/r/moto-honda-joao",
    "numeros": [
      { "numero": 1, "status": "disponivel" },
      { "numero": 2, "status": "reservado" },
      { "numero": 3, "status": "indisponivel" }
    ]
  }
]
```

Status aceitos em `numeros`: `disponivel`, `reservado`, `indisponivel`.

## POST api/rifas.php

Cria uma nova rifa.

Entrada:

```json
{
  "titulo": "Moto Honda CG 160",
  "descricao": "Premio da campanha solidaria",
  "valorNumero": 10,
  "quantidadeNumero": 1000,
  "dataSorteio": "2026-08-31",
  "horaSorteio": "18:00",
  "imagemUrl": "https://..."
}
```

Resposta:

```json
{
  "id": 1,
  "mensagem": "Rifa criada com sucesso"
}
```

## GET api/rifa.php?id=1

Busca uma rifa especifica.

Resposta: mesmo formato de um item de `GET api/rifas.php`.

## POST api/reservas.php

Reserva numeros para um participante.

Entrada:

```json
{
  "rifaId": 1,
  "numeros": [6, 16, 27],
  "participante": {
    "nome": "Maria Souza",
    "telefone": "(11) 98888-8888",
    "email": "maria@email.com"
  }
}
```

Resposta:

```json
{
  "id": 10,
  "mensagem": "Numeros reservados com sucesso"
}
```

## POST api/recuperar-senha.php

Entrada:

```json
{
  "email": "joao@email.com"
}
```

Resposta:

```json
{
  "mensagem": "Instrucoes enviadas"
}
```

## POST api/compartilhar.php

Opcional, para registrar cliques em compartilhamento.

Entrada:

```json
{
  "tipo": "plataforma"
}
```

Resposta:

```json
{
  "mensagem": "Compartilhamento registrado"
}
```
