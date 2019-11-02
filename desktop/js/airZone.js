
/* This file is part of Jeedom.
 *
 * Jeedom is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Jeedom is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Jeedom. If not, see <http://www.gnu.org/licenses/>.
 */


$("#table_cmd").sortable({axis: "y", cursor: "move", items: ".cmd", placeholder: "ui-state-highlight", tolerance: "intersect", forcePlaceholderSize: true});
/*
 * Fonction pour l'ajout de commande, appellé automatiquement par plugin.template
 */
function addCmdToTable(_cmd) {
    if (!isset(_cmd)) {
        var _cmd = {configuration: {}};
    }
    if (!isset(_cmd.configuration)) {
        _cmd.configuration = {};
    }
/*    var tr = '<tr class="cmd" data-cmd_id="' + init(_cmd.id) + '">';
	tr += '<td>';
    tr += '<span class="cmdAttr" data-l1key="id"></span>';
	tr += '</td>';
    tr += '<td>';
    tr += '<input class="cmdAttr form-control input-sm" data-l1key="name" style="width : 140px;" placeholder="{{Nom}}" >';
    tr += '</td>';
    tr += '<td>';
    tr += '<span class="type" type="' + init(_cmd.type) + '">' + jeedom.cmd.availableType() + '</span>';
    tr += '<span class="subType" subType="' + init(_cmd.subType) + '"></span>';
    tr += '</td>';
	tr += '<td>';
    tr += '<span><label class="checkbox-inline"><input type="checkbox" class="cmdAttr checkbox-inline" data-l1key="isHistorized" />{{Historiser}}</label> <br/></span>';
    tr += '<span><label class="checkbox-inline"><input type="checkbox" class="cmdAttr checkbox-inline" data-l1key="isVisible" /> {{Visible}}</label><br/></span>';
    tr += '</td>';
    tr += '<td>';
    if (is_numeric(_cmd.id)) {
        tr += '<a class="btn btn-default btn-xs cmdAction" data-action="configure"><i class="fa fa-cogs"></i></a> ';
        tr += '<a class="btn btn-default btn-xs cmdAction" data-action="test"><i class="fa fa-rss"></i> {{Tester}}</a>';
    }
    tr += '<i class="fa fa-minus-circle pull-right cmdAction cursor" data-action="remove"></i>';
    tr += '</td>';
    tr += '</tr>';
    $('#table_cmd tbody').append(tr);
    $('#table_cmd tbody tr:last').setValues(_cmd, '.cmdAttr');
    if (isset(_cmd.type)) {
        $('#table_cmd tbody tr:last .cmdAttr[data-l1key=type]').value(init(_cmd.type));
    }
    jeedom.cmd.changeType($('#table_cmd tbody tr:last'), init(_cmd.subType));
}*/
	
	//Id
	var tr = '<tr class="cmd" data-cmd_id="' + init(_cmd.id) + '">';
    tr += '<td>';
    tr += '<span class="cmdAttr" data-l1key="id"></span>';
    tr += '</td>';
    if (init(_cmd.type) == 'info') {
        //var disabled = (init(_cmd.configuration.virtualAction) == '1') ? 'disabled' : '';
        var disabled = 'disabled';
        
        //Nom
        tr += '<td>';
        tr += '<input class="cmdAttr form-control input-sm" data-l1key="name" style="width : 140px;" placeholder="{{Nom}}">';
        tr += '</td>';
        
        //Type
        tr += '<td>';
        tr += '<input class="cmdAttr form-control type input-sm" data-l1key="type" value="info" disabled style="margin-bottom : 5px;" />';
        tr += '<span class="subType" subType="' + init(_cmd.subType) + '"></span>';
        tr += '</td>';
        
        //Commande
        tr += '<td>';
        tr += '<div class="col-sm-6">';
        if(typeof _cmd.configuration.type !== "undefined") {
        	tr += _cmd.configuration.type;
    	}
    	tr += '</div>';
    	tr += '<div class="col-sm-6">';
    	if(typeof _cmd.configuration.nparams !== "undefined") {
    		if(_cmd.configuration.nparams > 0) {
    			tr += '<input class="cmdAttr form-control input-sm" data-l1key="configuration" data-l2key="parameters">';
    		}
    	}
    	tr += '</div>';
        tr += '</td>';
        
        //Unité
        tr += '<td>';
        tr += '<input class="cmdAttr form-control input-sm" data-l1key="unite" style="width : 90px;" placeholder="{{Unité}}">';
        tr += '</td>';
        
        //Paramètres
        tr += '<td>';
        tr += '<span><label class="checkbox-inline"><input type="checkbox" class="cmdAttr" data-size="mini" data-l1key="isHistorized" />{{Historiser}}</label></span> ';
        tr += '<span><label class="checkbox-inline"><input type="checkbox" class="cmdAttr" data-size="mini" data-l1key="isVisible" checked/>{{Afficher}}</label></span> ';
        tr += '<span class="expertModeVisible"><label class="checkbox-inline"><input type="checkbox" data-size="mini" class="cmdAttr" data-l1key="display" data-l2key="invertBinary" />{{Inverser}}</label></span> ';
        tr += '<span><label class="checkbox-inline"><input type="checkbox" class="cmdAttr" data-size="mini" data-l1key="eventOnly"' + disabled + ' />{{Evénement}}</label></span> ';
        tr += '<input style="width : 81%;margin-bottom : 2px;" class="tooltips cmdAttr form-control input-sm" data-l1key="cache" data-l2key="lifetime" placeholder="{{Lifetime cache}}" title="{{Lifetime cache}}">';
        tr += '<input class="tooltips cmdAttr form-control input-sm" data-l1key="configuration" data-l2key="minValue" placeholder="{{Min}}" title="{{Min}}" style="width : 40%;display : inline-block;"> ';
        tr += '<input class="tooltips cmdAttr form-control input-sm" data-l1key="configuration" data-l2key="maxValue" placeholder="{{Max}}" title="{{Max}}" style="width : 40%;display : inline-block;">';
        tr += '</td>';
        
    } else if (init(_cmd.type) == 'action') {
        
        //Nom
        tr += '<td>';
        tr += '<div class="row">';
        tr += '<div class="col-sm-6">';
        //Nom - icone
        tr += '<a class="cmdAction btn btn-default btn-sm" data-l1key="chooseIcon"><i class="fa fa-flag"></i> Icône</a>';
        tr += '<span class="cmdAttr" data-l1key="display" data-l2key="icon" style="margin-left : 10px;"></span>';
        tr += '</div>';
        //Nom - nom
        tr += '<div class="col-sm-6">';
        tr += '<input class="cmdAttr form-control input-sm" data-l1key="name">';
        tr += '</div>';
        tr += '</div>';
        tr += '<select class="cmdAttr form-control tooltips input-sm" data-l1key="value" style="display : none;margin-top : 5px;margin-right : 10px;" title="{{La valeur de la commande vaut par défaut la commande}}">';
        tr += '<option value="">Aucune</option>';
        tr += '</select>';
        tr += '</td>';
        
        //Type
        tr += '<td>';
        tr += '<input class="cmdAttr form-control type input-sm" data-l1key="type" value="action" disabled style="margin-bottom : 5px;" />';
        tr += '<span class="subType" subType="' + init(_cmd.subType) + '"></span>';
        tr += '</td>';
        
        //Commande
        tr += '<td>';
        tr += '<div class="col-sm-6">';
        if(typeof _cmd.configuration.commandName !== "undefined") {
        	tr += _cmd.configuration.commandName;
    	}
    	tr += '</div>';
    	tr += '<div class="col-sm-6">';
    	if(typeof _cmd.configuration.nparams !== "undefined") {
    		if(_cmd.configuration.nparams > 0) {
    			tr += '<input class="cmdAttr form-control input-sm" data-l1key="configuration" data-l2key="parameters">';
    		}
    	}
    	tr += '</div>';
        tr += '</td>';
        
        //Unité
        tr += '<td></td>';
        
        //Paramètres
        tr += '<td>';
        tr += '<span><label class="checkbox-inline"><input type="checkbox" class="cmdAttr" data-l1key="isVisible" data-size="mini" checked/>{{Afficher}}</label></span> ';
        tr += '<input class="tooltips cmdAttr form-control input-sm" data-l1key="configuration" data-l2key="minValue" placeholder="{{Min}}" title="{{Min}}" style="width : 40%;display : inline-block;"> ';
        tr += '<input class="tooltips cmdAttr form-control input-sm" data-l1key="configuration" data-l2key="maxValue" placeholder="{{Max}}" title="{{Max}}" style="width : 40%;display : inline-block;">';
        tr += '</td>';
    }
    //Controls
	tr += '<td>';
    if (is_numeric(_cmd.id)) {
        tr += '<a class="btn btn-default btn-xs cmdAction expertModeVisible" data-action="configure"><i class="fa fa-cogs"></i></a> ';
        tr += '<a class="btn btn-default btn-xs cmdAction" data-action="test"><i class="fa fa-rss"></i> {{Tester}}</a>';
    }
    tr += '<i class="fa fa-minus-circle pull-right cmdAction cursor" data-action="remove"></i></td>';
    tr += '</tr>';
    
    $('#table_cmd tbody').append(tr);
    //$('#table_cmd tbody tr:last').setValues(_cmd, '.cmdAttr');
    var tr = $('#table_cmd tbody tr:last');
    jeedom.eqLogic.builSelectCmd({
        id: $(".li_eqLogic.active").attr('data-eqLogic_id'),
        filter: {type: 'info'},
        error: function (error) {
            $('#div_alert').showAlert({message: error.message, level: 'danger'});
        },
        success: function (result) {
            tr.find('.cmdAttr[data-l1key=value]').append(result);
            tr.setValues(_cmd, '.cmdAttr');
            jeedom.cmd.changeType(tr, init(_cmd.subType));
            //initCheckBox();
        }
    });
}

$('#bt_syncEqLogic').on('click', function () {
    SyncAirzone();
});

$('#bt_integration').on('click', function () {
    Integration();
});


$("#table_cmd").sortable({axis: "y", cursor: "move", items: ".cmd", placeholder: "ui-state-highlight", tolerance: "intersect", forcePlaceholderSize: true});


function SyncAirzone() {
    $.ajax({// fonction permettant de faire de l'ajax
        type: "POST", // méthode de transmission des données au fichier php
        url: "plugins/airZone/core/ajax/airZone.ajax.php", // url du fichier php
        data: {
            action: "SyncAirzone",
        },
        dataType: 'json',
        error: function (request, status, error) {
            handleAjaxError(request, status, error);
        },
        success: function (data) { // si l'appel a bien fonctionné
        if (data.state != 'ok') {
            $('#div_alert').showAlert({message: data.result, level: 'danger'});
            return;
        }
        window.location.reload();
    }
});
}
function Integration() {
    $.ajax({// fonction permettant de faire de l'ajax
        type: "POST", // méthode de transmission des données au fichier php
        url: "plugins/airZone/core/ajax/airZone.ajax.php", // url du fichier php
        data: {
            action: "Integration",
        },
        dataType: 'json',
        error: function (request, status, error) {
            handleAjaxError(request, status, error);
        },
        success: function (data) { // si l'appel a bien fonctionné
        if (data.state != 'ok') {
            $('#div_alert').showAlert({message: data.result, level: 'danger'});
            return;
        }
        window.location.reload();
    }
});
}
