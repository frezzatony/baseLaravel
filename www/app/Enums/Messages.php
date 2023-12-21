<?php

declare(strict_types=1);

namespace App\Enums;

use BenSampo\Enum\Enum;

/**
 * @method static static OptionOne()
 * @method static static OptionTwo()
 * @method static static OptionThree()
 */
final class Messages extends Enum
{
    const FORM_ERROR = [
        'Ops, temos um probleminha :( Parece que algo não está certo com o seu formulário... <br><b>O formulário possui erros.</b>',
        'Oh não, o que será que houve? :( Vejo que o seu formulário está com erros... <br><b>O formulário possui erros.</b>',
        'Opa! :( Alguma coisa não está certa com o seu formulário. Dá uma olhadinha! <br><b>O formulário possui erros.</b>',
        'Eita, parece que temos um erro no formulário. :( Pode dar uma conferida? <br><b>O formulário possui erros.</b>',
        'Não foi dessa vez, :( Algo está errado com o seu formulário... <br><b>O formulário possui erros.</b>',
        'Que pena, :( Mas não desista! Temos erros no formulário para serem corrigidos... <br><b>O formulário possui erros.</b>',
        'Ah, :( Algo deu errado com o seu formulário. Vamos dar uma olhadinha? <br><b>O formulário possui erros.</b>',
        'Poxa vida, :( Seu formulário parece estar com alguns erros... Vamos consertar? <br><b>O formulário possui erros.</b>',
        'Ué, :( Tem algo errado com o seu formulário... O que será? <br><b>O formulário possui erros.</b>',
        'Ai ai, :( O formulário está com erros... Vamos dar um jeito nisso? <br><b>O formulário possui erros.</b>',
        'Que triste, :( Seu formulário está com erros... Mas podemos consertar juntos! <br><b>O formulário possui erros.</b>',
        'Nooooossa, :( Temos problemas no formulário... Mas não se preocupe, vamos resolver! <br><b>O formulário possui erros.</b>',
        'Meu coração ficou triste, :( O formulário está com erros... Vamos tentar corrigir? <br><b>O formulário possui erros.</b>',
        'Ops, :( Temos uma situação delicada... Seu formulário possui erros! <br><b>O formulário possui erros.</b>',
        'Oh não, :( O seu formulário não passou na validação... Vamos consertar? <br><b>O formulário possui erros.</b>',
        'Eita, :( Temos alguns erros no seu formulário... Mas vamos dar um jeito nisso juntos! <br><b>O formulário possui erros.</b>',
        'Puxa vida, :( O formulário está com erros... Mas tenho certeza que podemos corrigir! <br><b>O formulário possui erros.</b>',
        'Que coisa, :( Algo está errado com o seu formulário... Mas vamos tentar resolver, ok? <br><b>O formulário possui erros.</b>',
        'Ai ai ai, :( Seu formulário parece estar com alguns erros... Mas nada que não possamos corrigir! <br><b>O formulário possui erros.</b>',
        'Que tristeza, :( O formulário não passou na validação... Vamos dar uma olhada? <br><b>O formulário possui erros.</b>',
        'Oh não, você pode dar uma olhadinha nos campos preenchidos? <br><b>O formulário possui erros.</b>',
        'Ops, alguns campos foram preenchidos incorretamente. :( <br><b>O formulário possui erros.</b>',
        'Oh não, temos um probleminha aqui. <br><b>O formulário possui erros.</b>'
    ];
}
