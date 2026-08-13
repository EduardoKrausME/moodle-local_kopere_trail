# local_kopere_trail

Camada de trilhas de aprendizagem acima do Moodle.

O plugin não substitui cursos, atividades, conclusão nativa, notas ou competências. Ele organiza esses elementos em uma jornada maior, permitindo que o estudante entre pela trilha e avance por etapas que podem apontar para cursos Moodle, URLs, HTML, H5P e outros conteúdos adicionados por subplugins.

## Instalação

Copie a pasta `kopere_trail` para `local/kopere_trail` no Moodle e acesse a administração para instalar.

## Conceito

No Moodle tradicional, o estudante entra em um curso e percorre as atividades daquele curso.

No Kopere Trail, o estudante entra primeiro em uma trilha:

`Introdução -> Curso de Comunicação -> Vídeo externo -> Curso de Gestão de Pessoas -> H5P -> Avaliação -> Microcertificação`

Cada etapa pode ter conteúdo, conclusão e pré-requisito próprios. A trilha consolida o progresso em uma visão única, mas o curso continua sendo responsabilidade do Moodle.

## Subplugins incluídos

| Tipo | Subplugin | Uso |
| --- | --- | --- |
| `trailcontent` | `moodlecourse` | Etapa que abre um curso Moodle |
| `trailcontent` | `url` | Etapa que abre uma URL externa |
| `trailcontent` | `html` | Etapa com conteúdo HTML inline |
| `trailcontent` | `h5p` | Etapa que abre uma atividade H5P do Moodle |
| `trailcompletion` | `manual` | O estudante marca a etapa como concluída |
| `trailcompletion` | `coursecompletion` | A etapa conclui quando o curso Moodle estiver concluído |
| `trailcompletion` | `activitycompletion` | A etapa conclui quando uma atividade Moodle estiver concluída |
| `trailprereq` | `previous` | Liberação pela etapa anterior no modo linear |
| `trailprereq` | `step` | Liberação por conclusão de etapa conectada no grafo |
| `trailprereq` | `grade` | Liberação por nota mínima em um item de nota |
| `trailpersonalization` | `rules` | Exibe etapa apenas para coortes configuradas |
| `trailgamification` | `progress` | XP pessoal por etapas concluídas, sem ranking |
| `trailcert` | `microcert` | Certificado interno liberado após a conclusão da trilha |
| `trailcompetency` | `moodlecompetency` | Seleção e exibição de competências Moodle relacionadas à etapa |

## Configuração pela interface

O administrador não precisa escrever JSON para criar trilhas ou etapas. Cada subplugin expõe seus próprios campos no formulário do Moodle. Curso, atividade H5P, URL, HTML, regras de conclusão, coortes, competências e pré-requisitos são escolhidos por seletores e campos apropriados. Os seletores grandes usam busca AJAX e carregam apenas os registros necessários.

Os campos `config`, `contentconfig`, `completionconfig`, `ruleconfig` e similares continuam existindo internamente para persistir a configuração dos subplugins, mas são detalhes de implementação e não são exibidos para o usuário.

## Matrícula e acesso

Atribuições por usuário e por coorte são registradas separadamente da matrícula efetiva. Um mesmo estudante pode receber a mesma trilha por mais de uma origem e só perde o acesso quando nenhuma origem ativa permanecer. Abrir uma URL de trilha não cria matrícula. Trilhas ocultas ou fora do período de início e fim não ficam disponíveis para estudantes.

Quando uma etapa de curso Moodle ou H5P fica disponível, o respectivo subplugin pode preparar o acesso ao curso usando a matrícula manual habilitada naquele curso. Isso evita liberar antecipadamente todos os cursos da jornada.

## Progresso

O progresso consolidado conta as etapas obrigatórias visíveis da trilha:

`7 de 12 etapas concluídas - 63% da trilha`

Etapas opcionais aparecem na jornada, mas não bloqueiam automaticamente a próxima etapa obrigatória no fluxo linear.

## Administração

A visão administrativa mostra trilhas, etapas, atribuições e relatório por estudante. A ordem é controlada por ações de mover para cima ou para baixo, sem exigir números de ordenação. O relatório lê o progresso persistido, enquanto Scheduled Tasks sincronizam atribuições e atualizam o progresso em segundo plano pelo cron do Moodle.
