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

$string['actions'] = 'Ações';
$string['active'] = 'Ativo';
$string['alltrails'] = 'Trilhas de aprendizagem';
$string['assignmentcohort'] = 'Coorte';
$string['assignmenttarget'] = 'Usuário ou coorte';
$string['assignmenttype'] = 'Tipo de matrícula';
$string['assignmenttype_cohort'] = 'Coorte';
$string['assignmenttype_user'] = 'Usuário';
$string['assignmentuser'] = 'Usuário';
$string['available'] = 'Disponível';
$string['backtomanage'] = 'Voltar ao gerenciamento';
$string['backtotrail'] = 'Voltar para a trilha';
$string['cancel'] = 'Cancelar';
$string['cannotcompletestep'] = 'Esta etapa não pode ser concluída manualmente.';
$string['certificate'] = 'Certificado';
$string['certificate_completed_intro'] = 'concluiu a trilha de aprendizagem';
$string['certificate_completed_on'] = 'Conclusão em';
$string['certificate_student_intro'] = 'Certificamos que';
$string['certificate_title'] = 'Certificado de conclusão';
$string['certtype'] = 'Certificação';
$string['code'] = 'Código';
$string['competencies'] = 'Competências';
$string['competencyids'] = 'Competências relacionadas';
$string['competencytype'] = 'Competências';
$string['completed'] = 'Concluída';
$string['completedsteps'] = '{$a->completed} de {$a->total} etapas concluídas';
$string['completioncmid'] = 'Atividade para conclusão automática';
$string['completioncourseid'] = 'Curso para conclusão automática';
$string['completiontype'] = 'Tipo de conclusão';
$string['contentcourseid'] = 'Curso Moodle';
$string['contenth5pcmid'] = 'Atividade H5P';
$string['contenthtml'] = 'Conteúdo da etapa';
$string['contenttype'] = 'Tipo de conteúdo';
$string['contenturl'] = 'URL externa';
$string['contenturl_help'] = 'Informe o endereço completo que será aberto quando o estudante acessar esta etapa.';
$string['createedge'] = 'Criar conexão';
$string['createstep'] = 'Criar etapa';
$string['createtrail'] = 'Criar trilha';
$string['currentstep'] = 'Etapa atual';
$string['description'] = 'Descrição';
$string['edge_same_step'] = 'A etapa de origem e a etapa de destino precisam ser diferentes.';
$string['edgecreated'] = 'Conexão criada.';
$string['edgeupdated'] = 'Conexão atualizada.';
$string['edit'] = 'Editar';
$string['editedge'] = 'Editar conexão';
$string['editstep'] = 'Editar etapa';
$string['edittrail'] = 'Editar trilha';
$string['endbeforestart'] = 'A data final não pode ser anterior à data inicial.';
$string['enddate'] = 'Fim';
$string['enrolmentsynced'] = 'Matrículas sincronizadas.';
$string['estimatedtime'] = 'Tempo estimado em minutos';
$string['event_step_completed'] = 'Etapa da trilha concluída';
$string['event_trail_viewed'] = 'Trilha visualizada';
$string['fromstep'] = 'Etapa de origem';
$string['gamificationtype'] = 'Cálculo de progresso e XP';
$string['gotomanage'] = 'Gerenciar trilhas';
$string['gradeitemid'] = 'Item de nota';
$string['inprogress'] = 'Em andamento';
$string['invalidactivity'] = 'A atividade selecionada não existe mais ou não é do tipo esperado.';
$string['invalidcohort'] = 'A coorte selecionada não existe.';
$string['invalidcompetency'] = 'Uma das competências selecionadas não existe mais.';
$string['invalidcourse'] = 'O curso selecionado não existe mais.';
$string['invalidedgetrail'] = 'A conexão informada não pertence a esta trilha.';
$string['invalidgradeitem'] = 'O item de nota selecionado não existe mais.';
$string['invalidmove'] = 'Movimentação inválida.';
$string['invalidsteptrail'] = 'A etapa informada não pertence a esta trilha.';
$string['invaliduser'] = 'O usuário selecionado não existe ou foi excluído.';
$string['kopere_trail:enrol'] = 'Matricular usuários em trilhas';
$string['kopere_trail:manage'] = 'Gerenciar trilhas de aprendizagem';
$string['kopere_trail:view'] = 'Ver trilhas de aprendizagem';
$string['kopere_trail:viewreport'] = 'Ver relatórios das trilhas';
$string['lastupdate'] = 'Última atualização';
$string['launchstep'] = 'Abrir etapa';
$string['locked'] = 'Bloqueada';
$string['managetrails'] = 'Gerenciar trilhas';
$string['markcomplete'] = 'Marcar como concluída';
$string['microcertificate'] = 'Microcertificação';
$string['mingrade'] = 'Nota mínima';
$string['mingrade_help'] = 'Informe a nota mínima exigida no item de nota selecionado para liberar a etapa de destino.';
$string['missingplugin'] = 'Subplugin indisponível: {$a}';
$string['movedown'] = 'Mover para baixo';
$string['moveup'] = 'Mover para cima';
$string['myjourneys'] = 'Minha jornada';
$string['name'] = 'Nome';
$string['nextlocked'] = 'Disponível após concluir os pré-requisitos.';
$string['noassignments'] = 'Nenhuma matrícula configurada para esta trilha.';
$string['noedges'] = 'Nenhuma conexão configurada. Sem conexões, a trilha funciona em ordem linear.';
$string['nonnegativevalue'] = 'Informe um valor igual ou maior que zero.';
$string['noreportrows'] = 'Nenhum estudante encontrado nesta trilha.';
$string['nosteps'] = 'Esta trilha ainda não possui etapas.';
$string['notrailenrolments'] = 'Você ainda não está matriculado em nenhuma trilha.';
$string['notrails'] = 'Nenhuma trilha disponível no momento.';
$string['notstarted'] = 'Não iniciada';
$string['optional'] = 'Etapa opcional';
$string['optionalstep'] = 'Opcional';
$string['percent'] = 'Percentual';
$string['personalizationcohortids'] = 'Coortes autorizadas';
$string['personalizationcohortids_help'] = 'Selecione uma ou mais coortes. A etapa será exibida ao estudante quando ele pertencer a pelo menos uma das coortes selecionadas. Sem coortes selecionadas, a etapa fica disponível para todos.';
$string['personalizationtype'] = 'Personalização';
$string['pluginname'] = 'Kopere Trail';
$string['points'] = 'XP da etapa';
$string['prereqtype'] = 'Tipo de pré-requisito';
$string['prerequisites'] = 'Pré-requisitos';
$string['prerequisites_edges_info'] = 'Os pré-requisitos desta etapa são configurados em Conexões da trilha. Assim, a regra fica ligada à relação entre a etapa de origem e a etapa de destino.';
$string['printcertificate'] = 'Imprimir certificado';
$string['privacy:metadata'] = 'O Kopere Trail armazena matrícula, progresso e conclusão do usuário dentro das trilhas de aprendizagem.';
$string['privacy:metadata:assignment'] = 'Armazena atribuições diretas de usuários ou coortes às trilhas.';
$string['privacy:metadata:assignmenttarget'] = 'Identificador do usuário ou da coorte vinculada à atribuição.';
$string['privacy:metadata:assignmenttype'] = 'Indica se a atribuição é destinada a um usuário ou a uma coorte.';
$string['privacy:metadata:enrol'] = 'Matrícula do usuário na trilha.';
$string['privacy:metadata:enrolsource'] = 'Origens de atribuição que mantêm a matrícula do usuário ativa.';
$string['privacy:metadata:event'] = 'Armazena eventos de auditoria gerados pelo progresso na trilha.';
$string['privacy:metadata:eventdetails'] = 'Detalhes técnicos associados ao evento de progresso.';
$string['privacy:metadata:eventname'] = 'Tipo do evento de progresso registrado.';
$string['privacy:metadata:percent'] = 'Percentual consolidado da trilha.';
$string['privacy:metadata:progress'] = 'Progresso consolidado do usuário na trilha.';
$string['privacy:metadata:progresspercent'] = 'Percentual de progresso da etapa.';
$string['privacy:metadata:status'] = 'Status do registro.';
$string['privacy:metadata:stepid'] = 'Identificador da etapa.';
$string['privacy:metadata:stepprogress'] = 'Progresso do usuário em uma etapa da trilha.';
$string['privacy:metadata:trailid'] = 'Identificador da trilha.';
$string['privacy:metadata:userid'] = 'Identificador do usuário.';
$string['privacy:metadata:xp'] = 'XP pessoal acumulado na trilha.';
$string['progressline'] = '{$a->completed} de {$a->total} etapas concluídas | {$a->percent}% da trilha';
$string['removedcohort'] = 'Coorte removida';
$string['removedstep'] = 'Etapa removida';
$string['removeduser'] = 'Usuário removido';
$string['report'] = 'Relatório';
$string['reports'] = 'Relatórios';
$string['requiredstep'] = 'Obrigatória';
$string['savechanges'] = 'Salvar alterações';
$string['selectactivity'] = 'Pesquise pelo nome da atividade ou do curso';
$string['selectcohort'] = 'Selecione uma coorte';
$string['selectcourse'] = 'Pesquise pelo nome do curso';
$string['selectgradeitem'] = 'Pesquise pelo item de nota ou curso';
$string['selecttrail'] = 'Selecione uma trilha';
$string['selectuser'] = 'Pesquise pelo nome ou e-mail do usuário';
$string['startdate'] = 'Início';
$string['status'] = 'Status';
$string['stepcompleted'] = 'Etapa concluída.';
$string['stepcreated'] = 'Etapa criada.';
$string['stepnotfound'] = 'Etapa não encontrada.';
$string['steps'] = 'Etapas';
$string['stepupdated'] = 'Etapa atualizada.';
$string['student'] = 'Estudante';
$string['subplugintype_trailcert'] = 'Certificação da trilha';
$string['subplugintype_trailcert_plural'] = 'Plugins de certificação da trilha';
$string['subplugintype_trailcompetency'] = 'Competência da trilha';
$string['subplugintype_trailcompetency_plural'] = 'Plugins de competência da trilha';
$string['subplugintype_trailcompletion'] = 'Conclusão da trilha';
$string['subplugintype_trailcompletion_plural'] = 'Plugins de conclusão da trilha';
$string['subplugintype_trailcontent'] = 'Conteúdo da trilha';
$string['subplugintype_trailcontent_plural'] = 'Plugins de conteúdo da trilha';
$string['subplugintype_trailgamification'] = 'Gamificação da trilha';
$string['subplugintype_trailgamification_plural'] = 'Plugins de gamificação da trilha';
$string['subplugintype_trailpersonalization'] = 'Personalização da trilha';
$string['subplugintype_trailpersonalization_plural'] = 'Plugins de personalização da trilha';
$string['subplugintype_trailprereq'] = 'Pré-requisito da trilha';
$string['subplugintype_trailprereq_plural'] = 'Plugins de pré-requisito da trilha';
$string['summary'] = 'Resumo';
$string['suspended'] = 'Suspenso';
$string['task_refresh_progress'] = 'Atualizar progresso das trilhas';
$string['task_sync_enrolments'] = 'Sincronizar matrículas das trilhas';
$string['tostep'] = 'Etapa de destino';
$string['trail'] = 'Trilha';
$string['trailcreated'] = 'Trilha criada.';
$string['trailedges'] = 'Conexões da trilha';
$string['trailenrolments'] = 'Matrículas da trilha';
$string['trailnotfound'] = 'Trilha não encontrada.';
$string['trailsteps'] = 'Etapas da trilha';
$string['trailupdated'] = 'Trilha atualizada.';
$string['unlockmode'] = 'Regra de liberação';
$string['unlockmode_all'] = 'Todas as etapas anteriores exigidas';
$string['unlockmode_any'] = 'Qualquer etapa anterior exigida';
$string['unnamedgradeitem'] = 'Item de nota sem nome';
$string['view'] = 'Ver';
$string['viewcertificate'] = 'Ver certificado';
$string['visible'] = 'Visível';
$string['xp'] = 'XP';
