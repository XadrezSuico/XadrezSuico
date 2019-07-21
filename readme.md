# Sistema XadrezSuiço

Sejam bem-vindos ao repositório do sistema XadrezSuíço! Aqui está o código-fonte e outras informações importantes sobre o projeto.

### Mas o que é o XadrezSuíço?
É uma plataforma de gerenciamento de Circuitos de Xadrez. A ideia da plataforma é algo que possa simplificar o processo pré e pós eventos, ajudando no recebimento das inscrições das etapas dos circuitos e depois podendo efetuar a classificação geral do circuito.

### Eu posso usar o XadrezSuíço?
Claro que sim! O código está neste repositório, porém, o mesmo está ainda em desenvolvimento, com algumas funções ainda não funcionando, mas já é possível começar a trabalhar com o mesmo. 

### Com quais tecnologias o XadrezSuíço trabalha?
O XadrezSuíço é desenvolvido em PHP com o framework Laravel, podendo ser utilizados vários tipos de bancos de dados, mas o que mais foi focado para o sistema foi o MySQL/MariaDB, que inclusive é o que recomendamos para uso.

### A minha competição utiliza-se dos Ratings FIDE e/ou CBX, vou ter algum problema com isso?
Não! E isso é uma das coisas mais bacanas deste projeto: ao receber um cadastro com o ID FIDE ou CBX, passado alguns minutos, o mesmo irá importar os ratings para o sistema!

IMPORTANTE! Considerando os ratings FIDE e CBX, o XadrezSuíço por enquanto apenas está trabalhando com os Ratings Rápido. Já está na lista fazer atualização no sistema que permita o uso dos Ratings de Blitz e Convencional, mas ainda não está implementado.

## A História por trás do XadrezSuíço
Bom, preciso personificar um pouco aqui, para deixar mais claro o que acontece neste caso.
O idealizador do Projeto, João Paulo Polles, é árbitro de Xadrez há mais de 4 anos, e quando o mesmo iniciou a atuar em competições, principalmente como Árbitro Mesa, percebeu que faltava algo para ajudar os árbitros nas competições. Nesta época, o mesmo já fazia o Curso Técnico em Informática e estava o fazendo integrado ao Ensino Médio.

Então surgiu a ideia de tentar modernizar um pouco o uso de softwares pelos árbitros de xadrez, visto que a maioria utilizava-se (e ainda utiliza) do software Swiss Perfect 98, software criado nos anos 90 para gerenciamento de torneios de xadrez. 

O Swiss Perfect 98 até é competente, mas peca em vários quesitos:
- O software por ser muito antigo, não atende diversos tipos de critérios de desempate atuais, como por exemplo o Confronto Direto;
- O software não aceita importação de dados de outras fontes;
- E o principal de todos: até hoje não conheço um árbitro que tenha a licença deste software (SIM! Este software É PAGO!)

Com isso, veio a ideia de desenvolver um software open-source para gerenciamento de torneios de xadrez, porém a tentativa da execução do mesmo se deu já na Faculdade, mais precisamente na Disciplina de "Projeto Experimental II" do curso de Tecnologia em Análise e Desenvolvimento de Sistemas do Centro Universitário UNIVEL, e tive ajuda de alguns colegas que estavam em meu grupo.

Passou-se um tempo, e percebi que não valeria muito a pena o desenvolvimento de um software para isto, e que eu teria outro problema um tanto maior que este: O gerenciamento dos Circuitos de Xadrez. Este problema eu percebi pouco tempo depois do início daquele projeto, e resolvi que era necessário o desenvolver.

Com isso, no início de 2019 dei início ao projeto que viria a se tornar o XadrezSuíço. E agora, meses depois do início do desenvolvimento decidi que era hora de começar a publicação pública do software.

## Mas e a documentação?
Então, o mesmo ainda está em desenvolvimento, e assim que o mesmo estiver finalizado irei trabalhar em uma documentação explicando alguns conceitos importantes sobre o software.

## E como faço a instalação?
O software foi desenvolvido em Laravel, então é o mesmo procedimento de qualquer software desenvolvido com o mesmo, porém, irei deixar uma breve descrição aqui:
- Instalar as dependências do Composer (composer install)
- Criar um arquivo de variáveis de ambiente (.env) a partir do exemplo (.env.example) e configurar pelo menos as variáveis de banco, email e url do sistema
- Efetuar a migração do banco de dados (php artisan migrate)
- Criar a chave do aplicativo (php artisan key:generate)
- Criar o primeiro usuário (recomendamos usar o comando 'php artisan tinker' para criar este usuário, visto que não é possível criar pelo formulário público) e definir a permissão do mesmo com o Perfil de código 1 (Super-administrador) - Em breve devo fazer uma atualização que já cria um usuário padrão no sistema.
- Começar a utilizar o sistema!

## E as novas funcionalidades? Como posso sugerir novas?
Você pode sugerir alterações através das issues! Estaremos "escutando", digamos desta forma.

## Posso ajudar no desenvolvimento do XadrezSuíço?
CLARO! Faça um fork do projeto e nos ajude a deixar esse projeto ainda com ainda mais funcionalidades!