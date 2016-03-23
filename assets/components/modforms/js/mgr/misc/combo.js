Ext.namespace('modforms.combo');



modforms.combo.Search = function(config) {
	config = config || {};
	Ext.applyIf(config, {
		xtype: 'twintrigger',
		ctCls: 'x-field-search',
		allowBlank: true,
		msgTarget: 'under',
		emptyText: _('search'),
		name: 'query',
		triggerAction: 'all',
		clearBtnCls: 'x-field-search-clear',
		searchBtnCls: 'x-field-search-go',
		onTrigger1Click: this._triggerSearch,
		onTrigger2Click: this._triggerClear
	});
	modforms.combo.Search.superclass.constructor.call(this, config);
	this.on('render', function() {
		this.getEl().addKeyListener(Ext.EventObject.ENTER, function() {
			this._triggerSearch();
		}, this);
	});
	this.addEvents('clear', 'search');
};
Ext.extend(modforms.combo.Search, Ext.form.TwinTriggerField, {

	initComponent: function() {
		Ext.form.TwinTriggerField.superclass.initComponent.call(this);
		this.triggerConfig = {
			tag: 'span',
			cls: 'x-field-search-btns',
			cn: [{
				tag: 'div',
				cls: 'x-form-trigger ' + this.searchBtnCls
			}, {
				tag: 'div',
				cls: 'x-form-trigger ' + this.clearBtnCls
			}]
		};
	},

	_triggerSearch: function() {
		this.fireEvent('search', this);
	},

	_triggerClear: function() {
		this.fireEvent('clear', this);
	}

});
Ext.reg('modforms-field-search', modforms.combo.Search);


modforms.combo.Active = function(config) {
	config = config || {};

	if (config.custm) {
		config.triggerConfig = [{
			tag: 'div',
			cls: 'x-field-search-btns',
			style: String.format('width: {0}px;', config.clear ? 62 : 31),
			cn: [{
				tag: 'div',
				cls: 'x-form-trigger x-field-modforms-active-go'
			}]
		}];
		if (config.clear) {
			config.triggerConfig[0].cn.push({
				tag: 'div',
				cls: 'x-form-trigger x-field-modforms-active-clear'
			});
		}

		config.initTrigger = function() {
			var ts = this.trigger.select('.x-form-trigger', true);
			this.wrap.setStyle('overflow', 'hidden');
			var triggerField = this;
			ts.each(function(t, all, index) {
				t.hide = function() {
					var w = triggerField.wrap.getWidth();
					this.dom.style.display = 'none';
					triggerField.el.setWidth(w - triggerField.trigger.getWidth());
				};
				t.show = function() {
					var w = triggerField.wrap.getWidth();
					this.dom.style.display = '';
					triggerField.el.setWidth(w - triggerField.trigger.getWidth());
				};
				var triggerIndex = 'Trigger' + (index + 1);

				if (this['hide' + triggerIndex]) {
					t.dom.style.display = 'none';
				}
				t.on('click', this['on' + triggerIndex + 'Click'], this, {
					preventDefault: true
				});
				t.addClassOnOver('x-form-trigger-over');
				t.addClassOnClick('x-form-trigger-click');
			}, this);
			this.triggers = ts.elements;
		};
	}
	Ext.applyIf(config, {
		name: config.name || 'active',
		hiddenName: config.name || 'active',
		displayField: 'name',
		valueField: 'value',
		editable: true,
		fields: ['name', 'value'],
		pageSize: 10,
		emptyText: _('modforms_combo_select'),
		hideMode: 'offsets',
		url: modforms.config.connector_url,
		baseParams: {
			action: 'mgr/misc/active/getlist',
			combo: true,
			addall: config.addall || 0
		},
		tpl: new Ext.XTemplate(
			'<tpl for="."><div class="x-combo-list-item">',
			'<small>({value})</small> <b>{name}</b></span>',
			'</div></tpl>', {
				compiled: true
			}),
		cls: 'input-combo-modforms-active',
		clearValue: function() {
			if (this.hiddenField) {
				this.hiddenField.value = '';
			}
			this.setRawValue('');
			this.lastSelectionText = '';
			this.applyEmptyText();
			this.value = '';
			this.fireEvent('select', this, null, 0);
		},

		getTrigger: function(index) {
			return this.triggers[index];
		},

		onTrigger1Click: function() {
			this.onTriggerClick();
		},

		onTrigger2Click: function() {
			this.clearValue();
		}
	});
	modforms.combo.Active.superclass.constructor.call(this, config);

};
Ext.extend(modforms.combo.Active, MODx.combo.ComboBox);
Ext.reg('modforms-combo-active', modforms.combo.Active);


modforms.combo.Chunk = function(config) {
	config = config || {};

	if (config.custm) {
		config.triggerConfig = [{
			tag: 'div',
			cls: 'x-field-search-btns',
			style: String.format('width: {0}px;', config.clear ? 62 : 31),
			cn: [{
				tag: 'div',
				cls: 'x-form-trigger x-field-modforms-chunk-go'
			}]
		}];
		if (config.clear) {
			config.triggerConfig[0].cn.push({
				tag: 'div',
				cls: 'x-form-trigger x-field-modforms-chunk-clear'
			});
		}

		config.initTrigger = function() {
			var ts = this.trigger.select('.x-form-trigger', true);
			this.wrap.setStyle('overflow', 'hidden');
			var triggerField = this;
			ts.each(function(t, all, index) {
				t.hide = function() {
					var w = triggerField.wrap.getWidth();
					this.dom.style.display = 'none';
					triggerField.el.setWidth(w - triggerField.trigger.getWidth());
				};
				t.show = function() {
					var w = triggerField.wrap.getWidth();
					this.dom.style.display = '';
					triggerField.el.setWidth(w - triggerField.trigger.getWidth());
				};
				var triggerIndex = 'Trigger' + (index + 1);

				if (this['hide' + triggerIndex]) {
					t.dom.style.display = 'none';
				}
				t.on('click', this['on' + triggerIndex + 'Click'], this, {
					preventDefault: true
				});
				t.addClassOnOver('x-form-trigger-over');
				t.addClassOnClick('x-form-trigger-click');
			}, this);
			this.triggers = ts.elements;
		};
	}
	Ext.applyIf(config, {
		name: config.name || 'chunk',
		hiddenName: config.name || 'chunk',
		displayField: 'name',
		valueField: 'id',
		editable: true,
		fields: ['id', 'name', 'description'],
		pageSize: 10,
		emptyText: _('modforms_combo_select'),
		hideMode: 'offsets',
		url: modforms.config.connector_url,
		baseParams: {
			action: 'mgr/misc/chunk/getlist',
			mode: 'chunks',
			combo: config.combo || true
		},
		tpl: new Ext.XTemplate(
			'<tpl for="."><div class="x-combo-list-item">',
			'<small>({id})</small> <b>{name}</b></span>',
			'<tpl if="description"><br><small>{description}</small></tpl>',
			'</div></tpl>', {
				compiled: true
			}),
		cls: 'input-combo-chunk',
		clearValue: function() {
			if (this.hiddenField) {
				this.hiddenField.value = '';
			}
			this.setRawValue('');
			this.lastSelectionText = '';
			this.applyEmptyText();
			this.value = '';
			this.fireEvent('select', this, null, 0);
		},

		getTrigger: function(index) {
			return this.triggers[index];
		},

		onTrigger1Click: function() {
			this.onTriggerClick();
		},

		onTrigger2Click: function() {
			this.clearValue();
		}
	});
	modforms.combo.Chunk.superclass.constructor.call(this, config);

};
Ext.extend(modforms.combo.Chunk, MODx.combo.ComboBox);
Ext.reg('modforms-combo-chunk', modforms.combo.Chunk);



/*------------------------------------------------------*/

modforms.combo.Options = function(config) {
	config = config || {};
	Ext.applyIf(config, {
		xtype: 'superboxselect',
		name: config.name || 'option',
		originalName: config.name || 'option',
		displayField: 'value',
		valueField: 'value',
		fields: ['value'],
		mode: 'remote',

		allowBlank: true,
		allowAddNewData: true,
		addNewDataOnBlur: true,

		forceSameValueQuery: true,
		forceFormValue: false,

		editable: true,
		resizable: true,
		msgTarget: 'under',
		anchor: '100%',
		minChars: 2,
		pageSize: 10,
		store: new Ext.data.JsonStore({
			root: 'results',
			autoLoad: true,
			autoSave: false,
			totalProperty: 'total',
			fields: ['value'],
			url: modforms.config.connector_url,
			baseParams: {
				action: 'mgr/option/getlist',
				form: config.form,
				key: config.key,
				all: config.all || false
			}
		}),
		triggerAction: 'all',
		extraItemCls: 'x-tag',
		expandBtnCls: 'x-form-trigger',
		clearBtnCls: 'x-form-trigger',

		queryValuesDelimiter: '|',
		listeners: {
			additem: function(bs, v) {
				if (this._addBlur) {
					return;
				}
				MODx.Ajax.request({
					url: modforms.config.connector_url,
					params: {
						action: 'mgr/option/update',
						value: v,
						mode: 'add',
						form: config.store.baseParams.form || config.form,
						key: config.key,
						all: config.all || false
					},
					listeners: {
						success: {
							fn: function() {},
							scope: this
						}
					}
				});
			},
			removeitem: function(bs, v) {
				if (this._addBlur) {
					return;
				}
				MODx.Ajax.request({
					url: modforms.config.connector_url,
					params: {
						action: 'mgr/option/update',
						value: v,
						mode: 'remove',
						form: config.store.baseParams.form || config.form,
						key: config.key,
						all: config.all || false,
					},
					listeners: {
						success: {
							fn: function() {},
							scope: this
						}
					}
				});
			},
			clear: function(bs, v) {
				if (this._addBlur) {
					return;
				}
				MODx.Ajax.request({
					url: modforms.config.connector_url,
					params: {
						action: 'mgr/option/remove',
						form: config.store.baseParams.form || config.form,
						key: config.key
					},
					listeners: {
						success: {
							fn: function() {},
							scope: this
						}
					}
				});
			}
		}
	});
	modforms.combo.Options.superclass.constructor.call(this, config);
};
Ext.extend(modforms.combo.Options, Ext.ux.form.SuperBoxSelect);
Ext.reg('modforms-combo-options', modforms.combo.Options);