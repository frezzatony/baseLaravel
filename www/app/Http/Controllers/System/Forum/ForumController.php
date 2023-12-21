<?php

namespace App\Http\Controllers\System\Forum;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Notifications\SystemNotification;
use App\Services\System\WebsocketService;
use Illuminate\Support\Facades\Notification;

class ForumController extends Controller
{
    public function index()
    {

        // $testMailData = [
        //     'to'    =>  'frezzatony@gmail.com',
        //     'title' => 'TITULO',
        //     'body' => 'This is the body of test email.'
        // ];
        // dispatch(new \App\Jobs\TestEmailJob($testMailData));
        // dd('Success! Email has been sent successfully.');

        $users = User::find(2);
        Notification::send($users, new SystemNotification([
            'title'     =>  'Essa é nova ' . time(),
            'text'      =>  'corpo mensagem => ' . time(),
            'author'    =>  'Tony',
        ]));
        WebsocketService::message('notifications.user.' . auth()->user()->api_token, json_encode([
            'action'    =>  'new_message',
        ]));

        $topicos_prefeitura = array(
            array(
                "titulo" => "Tecnologia no Horizonte",
                "categoria" => "Tecnologia e Inovação",
                "descritivo" => "Na prefeitura, buscamos entender como a evolução tecnológica afetará nossos serviços públicos. Quais previsões vocês têm sobre como a IA e a realidade virtual moldarão a interação cidadão-governo? Como podemos melhorar a qualidade de vida usando essas tecnologias?",
                'comentarios'   =>  rand(3, 100),
                'status'        =>  'discussão',
                'cor'           =>  'primary'
            ),
            array(
                "titulo" => "Bem-Estar Mental na Comunidade",
                "categoria" => "Saúde e Bem-Estar",
                "descritivo" => "Nosso compromisso com a comunidade é essencial. Como podemos promover o bem-estar mental entre os residentes? Compartilhem estratégias para fornecer apoio psicológico, criar espaços seguros e incentivar atividades de relaxamento.",
                'comentarios'   =>  rand(3, 100),
                'status'        =>  'aberto',
                'cor'           =>  'warning'
            ),
            array(
                "titulo" => "Iniciativas Culturais Locais",
                "categoria" => "Cultura e Entretenimento",
                "descritivo" => "A diversidade cultural é uma prioridade para nós. Como podemos promover atividades culturais que unam nossa comunidade? Quais eventos, festivais ou exposições vocês recomendam para celebrar nossas tradições locais?",
                'comentarios'   =>  rand(3, 100),
                'status'        =>  'urgente',
                'cor'           =>  'danger'
            ),
            array(
                "titulo" => "Sustentabilidade Urbana",
                "categoria" => "Meio Ambiente e Sustentabilidade",
                "descritivo" => "Estamos comprometidos em tornar nossa cidade mais sustentável. Quais ações sustentáveis vocês sugerem para reduzir o impacto ambiental em nossa região? Como podemos engajar os cidadãos na conservação de recursos e na adoção de práticas eco-friendly?",
                'comentarios'   =>  rand(3, 100),
                'status'        =>  'resolvido',
                'cor'           =>  'success'
            ),
            array(
                "titulo" => "Desenvolvimento Profissional para Jovens",
                "categoria" => "Educação e Desenvolvimento",
                "descritivo" => "A educação é uma prioridade para nossa prefeitura. Como podemos fornecer oportunidades de desenvolvimento profissional para jovens? Compartilhem ideias sobre programas de estágio, workshops e parcerias educacionais que prepararão a próxima geração.",
                'comentarios'   =>  rand(3, 100),
                'status'        =>  'tutorial',
                'cor'           =>  'purple'
            ),
            array(
                "titulo" => "Rotas de Ecoturismo na Região",
                "categoria" => "Turismo e Exploração",
                "descritivo" => "Estamos buscando impulsionar o ecoturismo local. Quais são as rotas naturais e culturais imperdíveis em nossa área? Compartilhem informações sobre trilhas, pontos históricos e atividades que atrairiam visitantes preocupados com a sustentabilidade.",
                'comentarios'   =>  rand(3, 100),
                'status'        =>  'discussão',
                'cor'           =>  'primary'
            )
        );
        return view('system.forum.index', ['topicos_prefeitura'   =>  $topicos_prefeitura]);
    }
}
