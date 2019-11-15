<?php
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

require_once dirname(__FILE__) . '/../../../core/php/core.inc.php';
include_file('core', 'authentification', 'php');
if (!isConnect()) {
    include_file('desktop', '404', 'php');
    die();
}
?>
<form class="form-horizontal">
    <fieldset>
        <div class="form-group">
            <label class="col-lg-3 control-label">{{Adresse API de la passerelle}}</label>
            <div class="col-lg-3">
                <input class="configKey form-control" data-l1key="addr" />
            </div>
			<span class="col-lg-6" style="position:relative;top:7px;">http://XXX.XXX.XXX.XXX:3000/api/v1/hvac</span>
        </div>
	<div class="form-group">
            <label class="col-lg-3 control-label">{{Adresse API pour la vérification de l'intégration }}</label>
            <div class="col-lg-3">
                <input class="configKey form-control" data-l1key="integration" />
            </div>
			<span class="col-lg-6" style="position:relative;top:7px;">http://XXX.XXX.XXX.XXX:3000/api/v1/integration</span>
        </div>
        <div class="form-group">
            <label class="col-lg-3 control-label">{{Nombre de Systèmes de climatisation AirZone	}}</label>
            <div class="col-lg-3">
                <input class="configKey form-control" data-l1key="nbSystems" value="1" />
            </div>
			<span class="col-lg-6" style="position:relative;top:7px;">Par defaut : 1</span>
        </div>
	<div class="form-group">
            <label class="col-lg-3 control-label">{{Mode de fonctionnement}}</label>
            <div class="col-lg-3">
                <input class="configKey form-control" data-l1key="mode" value="" />
            </div>
			<span class="col-lg-6" style="position:relative;top:7px;">Ne pas utiliser</span>
        </div>
        <div class="form-group">
            <label class="col-lg-3 control-label">{{Système Airzone VAF}}</label>
            <div class="col-lg-3">
                 <select class="configKey form-control" data-l1key="VAF">
                    <option value="1">Oui</option>
                    <option value="0" selected >Non</option>
                </select>
            </div>
			<div class="col-lg-6">
			Températures de consignes différentes en mode froid et mode chaud
			</br> Aucune Idée ? Faire une synchro et vérifier la présence d'une commande info "coolsetpoint" et "heatsetpoint", 
			</br> si oui, passer VAF à oui, enregistrer puis désactiver et réactiver le plugin.
			</div>
        </div>
  </fieldset>
</form>

