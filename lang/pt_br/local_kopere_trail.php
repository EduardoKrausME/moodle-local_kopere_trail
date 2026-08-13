<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * local_kopere_trail.php
 *
 * @package   local_kopere_trail
 * @copyright 2026 Eduardo Kraus {@link https://eduardokraus.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['pluginname'] = 'Kopere Trail';
$string['privacy:metadata'] = 'O Kopere Trail armazena matrícula, progresso e conclusão do usuário '
    . 'dentro das trilhas de aprendizagem.';
$string['privacy:metadata:trailid'] = 'Identificador da trilha.';
$string['privacy:metadata:userid'] = 'Identificador do usuário.';
$string['privacy:metadata:status'] = 'Status do registro.';
$string['privacy:metadata:enrol'] = 'Matrícula do usuário na trilha.';
$string['privacy:metadata:enrolsource'] = 'Origens de atribuição que mantêm a matrícula do usuário ativa.';
$string['privacy:metadata:progress'] = 'Progresso consolidado do usuário na trilha.';
$string['privacy:metadata:stepprogress'] = 'Progresso do usuário em uma etapa da trilha.';
$string['privacy:metadata:stepid'] = 'Identificador da etapa.';
$string['privacy:metadata:percent'] = 'Percentual consolidado da trilha.';
$string['privacy:metadata:xp'] = 'XP pessoal acumulado na trilha.';
$string['privacy:metadata:progresspercent'] = 'Percentual de progresso da etapa.';
$string['kopere_trail:view'] = 'Ver trilhas de aprendizagem';
$string['kopere_trail:manage'] = 'Gerenciar trilhas de aprendizagem';
$string['kopere_trail:enrol'] = 'Matricular usuários em trilhas';
$string['kopere_trail:viewreport'] = 'Ver relatórios das trilhas';
$string['myjourneys'] = 'Minha jornada';
$string['alltrails'] = 'Trilhas de aprendizagem';
$string['managetrails'] = 'Gerenciar trilhas';
$string['createtrail'] = 'Criar trilha';
$string['edittrail'] = 'Editar trilha';
$string['trailsteps'] = 'Etapas da trilha';
$string['trailedges'] = 'Conexões da trilha';
$string['createstep'] = 'Criar etapa';
$string['editstep'] = 'Editar etapa';
$string['createedge'] = 'Criar conexão';
$string['editedge'] = 'Editar conexão';
$string['report'] = 'Relatório';
$string['trailenrolments'] = 'Matrículas da trilha';
$string['name'] = 'Nome';
$string['code'] = 'Código';
$string['summary'] = 'Resumo';
$string['visible'] = 'Visível';
$string['startdate'] = 'Início';
$string['enddate'] = 'Fim';
$string['certtype'] = 'Certificação';
$string['description'] = 'Descrição';
$string['contenttype'] = 'Tipo de conteúdo';
$string['contenthtml'] = 'Conteúdo da etapa';
$string['contenturl'] = 'URL externa';
$string['contenturl_help'] = 'Informe o endereço completo que será aberto quando o estudante acessar esta etapa.';
$string['contentcourseid'] = 'Curso Moodle';
$string['contenth5pcmid'] = 'Atividade H5P';
$string['completiontype'] = 'Tipo de conclusão';
$string['completioncourseid'] = 'Curso para conclusão automática';
$string['completioncmid'] = 'Atividade para conclusão automática';
$string['prereqtype'] = 'Tipo de pré-requisito';
$string['personalizationtype'] = 'Personalização';
$string['personalizationcohortids'] = 'Coortes autorizadas';
$string['personalizationcohortids_help'] = 'Selecione uma ou mais coortes. A etapa será exibida ao estudante '
    . 'quando ele pertencer a pelo menos uma das coortes selecionadas. Sem '
    . 'coortes selecionadas, a etapa fica disponível para todos.';
$string['prerequisites'] = 'Pré-requisitos';
$string['prerequisites_edges_info'] = 'Os pré-requisitos desta etapa são configurados em Conexões da trilha. '
    . 'Assim, a regra fica ligada à relação entre a etapa de origem e a etapa '
    . 'de destino.';
$string['gradeitemid'] = 'Item de nota';
$string['mingrade'] = 'Nota mínima';
$string['mingrade_help'] = 'Informe a nota mínima exigida no item de nota selecionado para liberar a etapa de destino.';
$string['optional'] = 'Etapa opcional';
$string['unlockmode'] = 'Regra de liberação';
$string['unlockmode_all'] = 'Todas as etapas anteriores exigidas';
$string['unlockmode_any'] = 'Qualquer etapa anterior exigida';
$string['points'] = 'XP da etapa';
$string['estimatedtime'] = 'Tempo estimado em minutos';
$string['savechanges'] = 'Salvar alterações';
$string['cancel'] = 'Cancelar';
$string['actions'] = 'Ações';
$string['view'] = 'Ver';
$string['edit'] = 'Editar';
$string['steps'] = 'Etapas';
$string['reports'] = 'Relatórios';
$string['completed'] = 'Concluída';
$string['inprogress'] = 'Em andamento';
$string['notstarted'] = 'Não iniciada';
$string['locked'] = 'Bloqueada';
$string['available'] = 'Disponível';
$string['optionalstep'] = 'Opcional';
$string['requiredstep'] = 'Obrigatória';
$string['launchstep'] = 'Abrir etapa';
$string['markcomplete'] = 'Marcar como concluída';
$string['completedsteps'] = '{$a->completed} de {$a->total} etapas concluídas';
$string['progressline'] = '{$a->completed} de {$a->total} etapas concluídas | {$a->percent}% da trilha';
$string['nosteps'] = 'Esta trilha ainda não possui etapas.';
$string['notrails'] = 'Nenhuma trilha disponível no momento.';
$string['notrailenrolments'] = 'Você ainda não está matriculado em nenhuma trilha.';
$string['nextlocked'] = 'Disponível após concluir os pré-requisitos.';
$string['trailnotfound'] = 'Trilha não encontrada.';
$string['stepnotfound'] = 'Etapa não encontrada.';
$string['trailcreated'] = 'Trilha criada.';
$string['trailupdated'] = 'Trilha atualizada.';
$string['stepcreated'] = 'Etapa criada.';
$string['stepupdated'] = 'Etapa atualizada.';
$string['edgecreated'] = 'Conexão criada.';
$string['edgeupdated'] = 'Conexão atualizada.';
$string['edge_same_step'] = 'A etapa de origem e a etapa de destino precisam ser diferentes.';
$string['stepcompleted'] = 'Etapa concluída.';
$string['cannotcompletestep'] = 'Esta etapa não pode ser concluída manualmente.';
$string['enrolmentsynced'] = 'Matrículas sincronizadas.';
$string['status'] = 'Status';
$string['student'] = 'Estudante';
$string['lastupdate'] = 'Última atualização';
$string['currentstep'] = 'Etapa atual';
$string['percent'] = 'Percentual';
$string['xp'] = 'XP';
$string['assignmenttype'] = 'Tipo de matrícula';
$string['assignmenttype_user'] = 'Usuário';
$string['assignmenttype_cohort'] = 'Coorte';
$string['assignmentuser'] = 'Usuário';
$string['assignmentcohort'] = 'Coorte';
$string['assignmenttarget'] = 'Usuário ou coorte';
$string['selectuser'] = 'Pesquise pelo nome ou e-mail do usuário';
$string['selectcohort'] = 'Selecione uma coorte';
$string['invaliduser'] = 'O usuário selecionado não existe ou foi excluído.';
$string['invalidcohort'] = 'A coorte selecionada não existe.';
$string['active'] = 'Ativo';
$string['suspended'] = 'Suspenso';
$string['noassignments'] = 'Nenhuma matrícula configurada para esta trilha.';
$string['noedges'] = 'Nenhuma conexão configurada. Sem conexões, a trilha funciona em ordem linear.';
$string['noreportrows'] = 'Nenhum estudante encontrado nesta trilha.';
$string['task_sync_enrolments'] = 'Sincronizar matrículas das trilhas';
$string['event_trail_viewed'] = 'Trilha visualizada';
$string['event_step_completed'] = 'Etapa da trilha concluída';
$string['trail'] = 'Trilha';
$string['selecttrail'] = 'Selecione uma trilha';
$string['gotomanage'] = 'Gerenciar trilhas';
$string['backtotrail'] = 'Voltar para a trilha';
$string['backtomanage'] = 'Voltar ao gerenciamento';
$string['fromstep'] = 'Etapa de origem';
$string['tostep'] = 'Etapa de destino';

$string['subplugintype_trailcontent'] = 'Conteúdo da trilha';
$string['subplugintype_trailcontent_plural'] = 'Plugins de conteúdo da trilha';
$string['subplugintype_trailcompletion'] = 'Conclusão da trilha';
$string['subplugintype_trailcompletion_plural'] = 'Plugins de conclusão da trilha';
$string['subplugintype_trailprereq'] = 'Pré-requisito da trilha';
$string['subplugintype_trailprereq_plural'] = 'Plugins de pré-requisito da trilha';
$string['subplugintype_trailpersonalization'] = 'Personalização da trilha';
$string['subplugintype_trailpersonalization_plural'] = 'Plugins de personalização da trilha';
$string['subplugintype_trailcert'] = 'Certificação da trilha';
$string['subplugintype_trailcert_plural'] = 'Plugins de certificação da trilha';
$string['subplugintype_trailgamification'] = 'Gamificação da trilha';
$string['subplugintype_trailgamification_plural'] = 'Plugins de gamificação da trilha';
$string['subplugintype_trailcompetency'] = 'Competência da trilha';
$string['subplugintype_trailcompetency_plural'] = 'Plugins de competência da trilha';
$string['gamificationtype'] = 'Cálculo de progresso e XP';
$string['competencytype'] = 'Competências';
$string['competencyids'] = 'Competências relacionadas';
$string['competencies'] = 'Competências';
$string['selectcourse'] = 'Pesquise pelo nome do curso';
$string['selectactivity'] = 'Pesquise pelo nome da atividade ou do curso';
$string['selectgradeitem'] = 'Pesquise pelo item de nota ou curso';
$string['endbeforestart'] = 'A data final não pode ser anterior à data inicial.';
$string['nonnegativevalue'] = 'Informe um valor igual ou maior que zero.';
$string['removeduser'] = 'Usuário removido';
$string['removedcohort'] = 'Coorte removida';
$string['removedstep'] = 'Etapa removida';
$string['unnamedgradeitem'] = 'Item de nota sem nome';
$string['moveup'] = 'Mover para cima';
$string['movedown'] = 'Mover para baixo';
$string['invalidmove'] = 'Movimentação inválida.';
$string['invalidsteptrail'] = 'A etapa informada não pertence a esta trilha.';
$string['invalidedgetrail'] = 'A conexão informada não pertence a esta trilha.';
$string['task_refresh_progress'] = 'Atualizar progresso das trilhas';
$string['certificate'] = 'Certificado';
$string['microcertificate'] = 'Microcertificação';
$string['certificate_title'] = 'Certificado de conclusão';
$string['certificate_student_intro'] = 'Certificamos que';
$string['certificate_completed_intro'] = 'concluiu a trilha de aprendizagem';
$string['certificate_completed_on'] = 'Conclusão em';
$string['printcertificate'] = 'Imprimir certificado';
$string['viewcertificate'] = 'Ver certificado';

$string['missingplugin'] = 'Subplugin indisponível: {$a}';
$string['invalidcourse'] = 'O curso selecionado não existe mais.';
$string['invalidactivity'] = 'A atividade selecionada não existe mais ou não é do tipo esperado.';
$string['invalidgradeitem'] = 'O item de nota selecionado não existe mais.';
$string['invalidcompetency'] = 'Uma das competências selecionadas não existe mais.';
$string['privacy:metadata:assignment'] = 'Armazena atribuições diretas de usuários ou coortes às trilhas.';
$string['privacy:metadata:assignmenttype'] = 'Indica se a atribuição é destinada a um usuário ou a uma coorte.';
$string['privacy:metadata:assignmenttarget'] = 'Identificador do usuário ou da coorte vinculada à atribuição.';
$string['privacy:metadata:event'] = 'Armazena eventos de auditoria gerados pelo progresso na trilha.';
$string['privacy:metadata:eventname'] = 'Tipo do evento de progresso registrado.';
$string['privacy:metadata:eventdetails'] = 'Detalhes técnicos associados ao evento de progresso.';
