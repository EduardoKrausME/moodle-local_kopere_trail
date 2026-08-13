# Arquitetura do Kopere Trail

## Responsabilidade do núcleo

O núcleo `local_kopere_trail` cuida de:

- definição da trilha
- etapas
- conexões entre etapas
- matrícula de usuários e coortes
- progresso consolidado
- disponibilidade das etapas
- telas de estudante, gestão e relatório

Ele não implementa cada tipo pedagógico diretamente. Tipos de conteúdo, conclusão, pré-requisito, personalização, certificação, gamificação e competência entram como subplugins.

## Fluxo do estudante

1. O estudante acessa `local/kopere_trail/index.php`.
2. O plugin lista as trilhas nas quais o estudante está matriculado.
3. Ao abrir uma trilha, o serviço recalcula conclusão nativa e disponibilidade.
4. O núcleo delega ao `trailcontent` da etapa a preparação de acesso necessária, como matrícula em curso quando aplicável.
5. O progresso da trilha é atualizado em `local_kopere_trail_prog`.
6. Abrir uma trilha nunca cria matrícula; o acesso depende de uma matrícula efetiva ativa, visibilidade e período da trilha.

## Tabelas principais

| Tabela | Função |
| --- | --- |
| `local_kopere_trail` | Cadastro da trilha |
| `local_kopere_trail_step` | Etapas da trilha |
| `local_kopere_trail_edge` | Conexões não lineares entre etapas |
| `local_kopere_trail_assign` | Matrículas por usuário ou coorte |
| `local_kopere_trail_enrol` | Matrícula efetiva do usuário na trilha |
| `local_kopere_trail_enrolsrc` | Origens de atribuição que mantêm a matrícula efetiva ativa |
| `local_kopere_trail_prog` | Progresso consolidado por usuário |
| `local_kopere_trail_progstep` | Progresso por etapa |
| `local_kopere_trail_event` | Log simples de eventos da trilha |

## Subplugin types

| Tipo | Contrato |
| --- | --- |
| `trailcontent` | `local_kopere_trail\contract\content_provider` |
| `trailcompletion` | `local_kopere_trail\contract\completion_provider` |
| `trailprereq` | `local_kopere_trail\contract\prereq_provider` |
| `trailpersonalization` | `local_kopere_trail\contract\personalization_provider` |
| `trailcert` | `local_kopere_trail\contract\cert_provider` |
| `trailgamification` | `local_kopere_trail\contract\gamification_provider` |
| `trailcompetency` | `local_kopere_trail\contract\competency_provider` |

## Extensão

Para criar um novo conteúdo, crie por exemplo:

`local/kopere_trail/trailcontent/video/classes/handler.php`

Com componente:

`trailcontent_video`

E implemente:

```php
class handler implements \local_kopere_trail\contract\content_provider {
    // ...
}
```

O núcleo descobre automaticamente o subplugin com `core_component::get_plugin_list()`.


## Sincronização

`sync_enrolments` sincroniza atribuições de usuários e coortes sem confundir origens múltiplas. `refresh_progress` recalcula o progresso persistido de matrículas ativas em trilhas atualmente abertas. O relatório não recalcula todos os estudantes durante a renderização.

## Configuração de subplugins

Os dados complexos continuam serializados internamente, mas os subplugins que precisam de configuração implementam `configurable_provider` e expõem campos normais de formulário. O administrador não edita JSON, IDs de usuário/coorte nem números de ordenação.
