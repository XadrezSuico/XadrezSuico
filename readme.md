# Sistema XadrezSuiço

Sejam bem-vindos ao repositório do sistema XadrezSuíço! Aqui está o código-fonte e outras informações importantes sobre o projeto.

### Mas o que é o XadrezSuíço?
É uma plataforma de gerenciamento de Circuitos de Xadrez. A ideia da plataforma é algo que possa simplificar o processo pré e pós eventos, ajudando no recebimento das inscrições das etapas dos circuitos e depois podendo efetuar a classificação geral do circuito.

### Eu posso usar o XadrezSuíço?
Claro que sim! O código está neste repositório, porém, o mesmo está ainda em desenvolvimento, com algumas funções ainda não funcionando, mas já é possível começar a trabalhar com o mesmo. 

### O XadrezSuíço é um software proprietário?
Não, se trata de um software livre licenciado por uma licença reciproca total, ou seja, se trata de uma licença que caso faça um fork (garfo em inglês, que tem o significado de fazer uma cópia do projeto para você poder trabalhar) do projeto e efetue alterações no mesmo, não seja possível alterar a licença a ser disponibilizada, ou seja, a licença da versão modificada deverá ser a mesma que aqui está.
No caso foi escolhida a licença GPL de versão 3, por se tratar da versão mais atualizada da licença e também para garantir que este software continue livre para todo o sempre.

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
- E o principal de todos: até hoje não conheço um árbitro que tenha a licença deste software (SIM! O Swiss Perfect 98 é PAGO!)

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

-----

## O que há de novo?
### Versão 0.1.0.1 Beta
 - Agora o cálculo de rating é efetuado pelo XadrezSuíço. Para tal, o evento deve calcular o rating e estar vinculado com o Tipo de Rating que é gerenciado pelo XadrezSuíço, e além de importar os resultados, deve-se importar os emparceiramentos com os seus devidos resultados. Além disso, deve-se atentar aos W.O.s para que não seja efetuado o cálculo erroneamente - Issue #54.
 - Em toda inscrição há um campo onde o(a) enxadrista cede seus direitos de imagem para a organização - Issue #47.
 - Há forma de criar emparceiramento de chave de semi-final/final sem disputa de 3º e 4º - #42.
 - Nova tela onde engloba tanto a inscrição quanto a confirmação - Issue #35.
 - Criada verificação se no cadastro do enxadrista falta alguma informação obrigatória para aquele evento, caso falte, encaminha o enxadrista para atualização cadastral - #34.
 - Criado cadastro de documento, a fim de que ocorra menor quantidade de duplicidades de cadastros no sistema - #32.
 - Adicionado o país de nascimento do enxadrista, necessário para poder aparecer os documentos disponíveis para aquele país - #33.
 - Adicionado o código IBGE no cadastro de Cidade - #31.
 - Melhoria na validação de documentos - #46.
 - Implementado a confirmação pública - #55.
 - Diversas outras melhorias e correções de bugs.

### Versão 0.1.0.0 Beta
 - As versões anteriores deixam de serem compatíveis com as versões a partir da 0.1.0.0, sendo necessária uma instalação limpa do sistema a fim de ter o seu uso.
 - Categorias: As categorias agora estão vinculadas ao Grupo de Evento ou Evento, e para poder gerenciá-las, é necessário acessar a Dashboard de onde ela foi cadastrada, seja no Grupo de Evento ou então no Evento.
 - Agora é possível exportar os enxadristas para poder fazer uma correção externa ou então para poder importar em outro sistema, caso necessário. Mas lembrando, que é necessário seguir a legislação vigente para isto em seu país.
 - Template de Torneios: Agora estão vinculados ao Grupo de Evento, e para gerenciar é necessário acessar a Dashboard de Grupo de Evento de que ele foi criado.
 - Permissões: agora todas as permissões estão de fato sendo aplicadas a fim de limitar o acesso a funções e páginas de acordo com o perfil do usuário. Finalização da issue #29.


### Versão 0.0.2.1 Beta
 - Correção em bug relativo a forma de armazenamento da imagem e descrição do evento.
 - Agora é possível visualizar a lista de inscrições do evento de forma pública, porém, é necessário que a opção 'Permite a visualização da lista de inscrições de forma pública?' esteja selecionada - Issue #12
 - A lista de Enxadristas agora é carregada de acordo com a demanda, assim deixando a lista mais rápida para ser acessada. - Issue #5
 - Agora é possível definir que um evento só é permitido a realização de inscrições pelo link (Inscrições Privadas). No caso, há uma informação a mais no link que libera ou não a inscrição para este evento. - Issue #22
 - Agora é possível gerar lista de Enxadristas inscritos em determinado evento. - Issue #17
 - Os ratings FIDE, CBX e LBX agora são divididos pelos seus tipos: Relâmpago, Rápido e Convencional. De acordo com a seleção do tipo de modalidade escolhido no cadastro de evento será apresentado o rating de acordo com o tipo de modalidade escolhida. - Issue #8
 - A classificação de evento está corrigida, agora quando é finalizada a mesma retorna ao Dashboard de Evento - Já a classificação geral, a mesma já está com a classificação sendo realizada através de solicitações AJAX - Issue #11
 - Agora é possível criar Tipos de Rating dentro do sistema e suas regras. - Issue #2
 - Agora há a integração do sistema com o LBXRatingServer para importação 'automática' do Rating LBX. - Issue #9


### Versão 0.0.2.0 Beta
 - Agora é possível definir uma imagem e um texto de apresentação do evento. - Issue #3

### Versão 0.0.1.2 Beta
 - Agora o email é validado quando é inserido em um cadastro de enxadrista a fim de garantir que o email é válido. Em breve apresentará uma mensagem de erro. - Issue #21
 - Corrigido bug que permitia que um enxadrista se recadastrasse caso utilizasse mais espaços entre os nomes.

### Versão 0.0.1.1 Beta
 - A página de 'O que há de novo?' agora possui link no menu para usuários logados.
 - Grupo de Evento - Agora é possível definir que a pontuação geral do enxadrista é a sua pontuação adquirida durante as etapas que participou.
   Para isso, é necessário configurar o Grupo de Evento, selecionando a opção _'A pontuação do enxadrista será composta pelos seus resultados?'_ e salvando a edição do Grupo de Evento.
   Após isso, caso já existam eventos classificados, *será necessário reclassificá-los*, antes de classificar o Grupo de Evento.
   
### Versão 0.0.1.0 Beta
 - Versão inicial do sistema em beta.
 
