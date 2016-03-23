
modforms.tools.getMenu = function (actions, grid, selected) {
	var menu = [];
	var cls, icon, title, action = '';

	var has_delete = false;
	for (var i in actions) {
		if (!actions.hasOwnProperty(i)) {
			continue;
		}

		var a = actions[i];
		if (!a['menu']) {
			if (a == '-') {
				menu.push('-');
			}
			continue;
		} else if (menu.length > 0 && (/^sep/i.test(a['action']))) {
			menu.push('-');
			continue;
		}

		if (selected.length > 1) {
			if (!a['multiple']) {
				continue;
			} else if (typeof(a['multiple']) == 'string') {
				a['title'] = a['multiple'];
			}
		}

		cls = a['cls'] ? a['cls'] : '';
		icon = a['icon'] ? a['icon'] : '';
		title = a['title'] ? a['title'] : a['title'];
		action = a['action'] ? grid[a['action']] : '';

		menu.push({
			handler: action,
			text: String.format(
				'<span class="{0}"><i class="x-menu-item-icon {1}"></i>{2}</span>',
				cls, icon, title
			)
		});
	}

	return menu;
};

modforms.tools.renderActions = function (value, props, row) {
	var res = [];
	var cls, icon, title, action, item = '';
	for (var i in row.data.actions) {
		if (!row.data.actions.hasOwnProperty(i)) {
			continue;
		}
		var a = row.data.actions[i];
		if (!a['button']) {
			continue;
		}

		cls = a['cls'] ? a['cls'] : '';
		icon = a['icon'] ? a['icon'] : '';
		action = a['action'] ? a['action'] : '';
		title = a['title'] ? a['title'] : '';

		item = String.format(
			'<li class="{0}"><button class="btn btn-default {1}" action="{2}" title="{3}"></button></li>',
			cls, icon, action, title
		);

		res.push(item);
	}

	return String.format(
		'<ul class="modforms-row-actions">{0}</ul>',
		res.join('')
	);
};


modforms.tools.renderReplace = function (value, replace, color) {
	if (!value) {
		replace = MODx.lang.modforms_no_value || MODx.lang.modforms_no;
	}
	if (!replace) {
		replace = value;
	}
	if (!color) {
		color = '777';
	}
	return String.format('<span class="modforms-render-color" style="color: #{1}">{0}</span>', replace, color);
};


modforms.tools.handleChecked = function (checkbox) {
	var workCount = checkbox.workCount;
	if (!!!workCount) {
		workCount = 1;
	}
	var hideLabel = checkbox.hideLabel;
	if (!!!hideLabel) {
		hideLabel = false;
	}

	var checked = checkbox.getValue();
	var nextField = checkbox.nextSibling();

	for (var i = 0; i < workCount; i++) {
		if (checked) {
			nextField.show().enable();
		}
		else {
			nextField.hide().disable();
		}
		nextField.hideLabel = hideLabel;
		nextField = nextField.nextSibling();
	}
	return true;
};

