modforms.grid.Forms = function(config) {
    config = config || {};

    this.exp = new Ext.grid.RowExpander({
        expandOnDblClick: false,
        tpl: new Ext.Template('<p class="desc">{subject}</p>'),
        renderer: function(v, p, record) {
            return record.data.subject != '' && record.data.subject != null ? '<div class="x-grid3-row-expander">&#160;</div>' : '&#160;';
        }
    });

    this.dd = function(grid) {
        this.dropTarget = new Ext.dd.DropTarget(grid.container, {
            ddGroup: 'dd',
            copy: false,
            notifyDrop: function(dd, e, data) {
                var store = grid.store.data.items;
                var target = store[dd.getDragData(e).rowIndex].id;
                var source = store[data.rowIndex].id;

                if (target != source) {
                    dd.el.mask(_('loading'), 'x-mask-loading');
                    MODx.Ajax.request({
                        url: modforms.config.connector_url,
                        params: {
                            action: config.action || 'mgr/form/sort',
                            source: source,
                            target: target
                        },
                        listeners: {
                            success: {
                                fn: function(r) {
                                    dd.el.unmask();
                                    grid.refresh();
                                },
                                scope: grid
                            },
                            failure: {
                                fn: function(r) {
                                    dd.el.unmask();
                                },
                                scope: grid
                            }
                        }
                    });
                }
            }
        });
    };

    this.sm = new Ext.grid.CheckboxSelectionModel();

    Ext.applyIf(config, {
        url: modforms.config.connector_url,
        baseParams: {
            action: 'mgr/form/getlist',
            class: config.class || '',
            options: Ext.util.JSON.encode(["selector","email"])
        },
        save_action: 'mgr/form/updatefromgrid',
        autosave: true,
       /* save_callback: this.updateRow,*/

        fields: this.getFields(config),
        columns: this.getColumns(config),
        tbar: this.getTopBar(config),
        listeners: this.getListeners(config),

        sm: this.sm,
        plugins: this.exp,
        ddGroup: 'dd',
        enableDragDrop: true,

        autoHeight: true,
        paging: true,
        pageSize: 10,
        remoteSort: true,
        viewConfig: {
            forceFit: true,
            enableRowBody: true,
            autoFill: true,
            showPreview: true,
            scrollOffset: 0
        },
        cls: 'modforms-grid',
        bodyCssClass: 'grid-with-buttons',
        stateful: true,
        stateId: 'modforms-grid-forms-state'

    });

    modforms.grid.Forms.superclass.constructor.call(this, config);

};
Ext.extend(modforms.grid.Forms, MODx.grid.Grid, {
    windows: {},

    getFields: function(config) {
        var fields = modforms.config.fields_grid_forms;

        return fields;
    },

    getTopBarComponent: function(config) {
        var component = ['menu', 'create', 'left', 'active', 'search'];
        if (!!config.compact) {
            component = ['menu', 'create', 'left', 'spacer'];
        }

        return component;
    },

    getTopBar: function(config) {
        var tbar = [];
        var add = {
            menu: {
                text: '<i class="icon icon-cogs"></i> ',
                menu: [{
                    text: '<i class="icon icon-toggle-on green"></i> ' + _('modforms_action_active'),
                    cls: 'modforms-cogs',
                    handler: this.active,
                    scope: this
                }, {
                    text: '<i class="icon icon-toggle-off red"></i> ' + _('modforms_action_inactive'),
                    cls: 'modforms-cogs',
                    handler: this.inactive,
                    scope: this
                }, '-', {
                    text: '<i class="icon icon-plus"></i> ' + _('modforms_action_create'),
                    cls: 'modforms-cogs',
                    handler: this.create,
                    scope: this
                }, {
                    text: '<i class="icon icon-trash-o red"></i> ' + _('modforms_action_remove'),
                    cls: 'modforms-cogs',
                    handler: this.remove,
                    scope: this
                }]
            },
            create: {
                text: '<i class="icon icon-plus"></i>',
                handler: this.create,
                scope: this
            },
            left: '->',
            active: {
                xtype: 'modforms-combo-active',
                width: 210,
                custm: true,
                clear: true,
                addall: true,
                value: '',
                listeners: {
                    select: {
                        fn: this._filterByCombo,
                        scope: this
                    },
                    afterrender: {
                        fn: this._filterByCombo,
                        scope: this
                    }
                }
            },
            search: {
                xtype: 'modforms-field-search',
                width: 210,
                listeners: {
                    search: {
                        fn: function (field) {
                            this._doSearch(field);
                        },
                        scope: this
                    },
                    clear: {
                        fn: function (field) {
                            field.setValue('');
                            this._clearSearch();
                        },
                        scope: this
                    }
                }
            },
            spacer: {
                xtype: 'spacer',
                style: 'width:1px;'
            }
        };

        var cmp = this.getTopBarComponent(config);
        for (var i = 0; i < cmp.length; i++) {
            var item = cmp[i];
            if (add[item]) {
                tbar.push(add[item]);
            }
        }

        return tbar;
    },


    getColumns: function(config) {
        var columns = [this.exp, this.sm];
        var add = {
            id: {
                width: 10,
                sortable: false
            },
            name: {
                width: 20,
                sortable: true,
                editor: {
                    xtype: 'textfield',
                    allowBlank: false,
                }
            },
            selector: {
                width: 20,
                sortable: false,
                autoHeight: true,
               /* editor: {
                    xtype: 'modforms-combo-options',
                    custm: true,
                    clear: true,
                    width: 210,
                    key: 'selector',
                    hiddenName: 'selector',
                    form: 0,
                    allowBlank: false,
                    ctCls:'modforms-combo-options-in-grid',
                    resizable:true,
                    onFocus:function(){
                        this._addBlur = false;
                        this.outerWrapEl.addClass(this.focusClass);
                        Ext.ux.form.SuperBoxSelect.superclass.onFocus.call(this);
                    },
                    beforeBlur: function(){
                        this._addBlur = true;
                    }
                }*/
            },
            email: {
                width: 20,
                sortable: false,
                autoHeight: true,
                /*editor: {
                    xtype: 'modforms-combo-options',
                    custm: true,
                    clear: true,
                    width: 210,
                    key: 'email',
                    hiddenName: 'email',
                    form: 0,
                    allowBlank: false,
                    ctCls:'modforms-combo-options-in-grid',
                    resizable:true,
                    onFocus:function(){
                        this._addBlur = false;
                        this.outerWrapEl.addClass(this.focusClass);
                        Ext.ux.form.SuperBoxSelect.superclass.onFocus.call(this);
                    },
                    beforeBlur: function(){
                        this._addBlur = true;
                    }
                }*/
            },

            actions: {
                width: 20,
                sortable: false,
                renderer: modforms.tools.renderActions,
                id: 'actions'
            }
        };

        if (!!config.compact) {

        }

        var fields = this.getFields();
        for (var i = 0; i < fields.length; i++) {
            var field = fields[i];
            if (add[field]) {
                Ext.applyIf(add[field], {
                    header: _('modforms_header_' + field),
                    tooltip: _('modforms_tooltip_' + field),
                    dataIndex: field
                });
                columns.push(add[field]);
            }
        }

        return columns;
    },

    getListeners: function(config) {
        return {
            render: {
                fn: this.dd,
                scope: this
            },
            beforeedit: function(e) {
                if (e.record.id) {
                    var combo = e.grid.colModel.config[e.column].getEditor(e.record);
                    if (combo.initialConfig && combo.initialConfig.store) {
                        combo.initialConfig.store.baseParams.form = e.record.id;
                    }

                }
            }
        };
    },

    getMenu: function(grid, rowIndex) {
        var ids = this._getSelectedIds();
        var row = grid.getStore().getAt(rowIndex);
        var menu = modforms.tools.getMenu(row.data['actions'], this, ids);
        this.addContextMenuItem(menu);
    },

    onClick: function(e) {
        var elem = e.getTarget();
        if (elem.nodeName == 'BUTTON') {
            var row = this.getSelectionModel().getSelected();
            if (typeof(row) != 'undefined') {
                var action = elem.getAttribute('action');
                if (action == 'showMenu') {
                    var ri = this.getStore().find('id', row.id);
                    return this._showMenu(this, ri, e);
                } else if (typeof this[action] === 'function') {
                    this.menu.record = row.data;
                    return this[action](this, e);
                }
            }
        }
        return this.processEvent('click', e);
    },

    setAction: function(method, field, value) {
        var ids = this._getSelectedIds();
        if (!ids.length && (field !== 'false')) {
            return false;
        }
        MODx.Ajax.request({
            url: modforms.config.connector_url,
            params: {
                action: 'mgr/form/multiple',
                method: method,
                field_name: field,
                field_value: value,
                ids: Ext.util.JSON.encode(ids)
            },
            listeners: {
                success: {
                    fn: function() {
                        this.refresh();
                    },
                    scope: this
                },
                failure: {
                    fn: function(response) {
                        MODx.msg.alert(_('error'), response.message);
                    },
                    scope: this
                }
            }
        })
    },

    active: function(btn, e) {
        this.setAction('setproperty', 'active', 1);
    },

    inactive: function(btn, e) {
        this.setAction('setproperty', 'active', 0);
    },

    remove: function() {
        Ext.MessageBox.confirm(
            _('modforms_action_remove'),
            _('modforms_confirm_remove'),
            function(val) {
                if (val == 'yes') {
                    this.setAction('remove');
                }
            },
            this
        );
    },

    update: function(btn, e, row) {
        var record = typeof(row) != 'undefined' ? row.data : this.menu.record;
        MODx.Ajax.request({
            url: modforms.config.connector_url,
            params: {
                action: 'mgr/form/get',
                id: record.id,
                options: Ext.util.JSON.encode(["selector","email"])
            },
            listeners: {
                success: {
                    fn: function(r) {
                        var record = r.object;
                        var w = MODx.load({
                            xtype: 'modforms-window-create-form',
                            title: _('modforms_action_update'),
                            action: 'mgr/form/update',
                            record: record,
                            update: true,
                            listeners: {
                                success: {
                                    fn: this.refresh,
                                    scope: this
                                }
                            }
                        });
                        w.reset();
                        w.setValues(record);
                        w.show(e.target);
                    },
                    scope: this
                }
            }
        });
    },

    create: function(btn, e) {
        var record = {
            active: 1
        };

        w = MODx.load({
            xtype: 'modforms-window-create-form',
            record: record,
			fileUpload: true,
            listeners: {
                success: {
                    fn: this.refresh,
                    scope: this
                }
            }
        });
        w.reset();
        w.setValues(record);
        w.show(e.target);
    },

    updateRow: function(response) {
        this.refresh();
    },

    _filterByCombo: function (cb) {
        this.getStore().baseParams[cb.name] = cb.value;
        this.getBottomToolbar().changePage(1);
    },

    _doSearch: function(tf) {
        this.getStore().baseParams.query = tf.getValue();
        this.getBottomToolbar().changePage(1);
    },

    _clearSearch: function() {
        this.getStore().baseParams.query = '';
        this.getBottomToolbar().changePage(1);
    },

    _getSelectedIds: function() {
        var ids = [];
        var selected = this.getSelectionModel().getSelections();

        for (var i in selected) {
            if (!selected.hasOwnProperty(i)) {
                continue;
            }
            ids.push(selected[i]['id']);
        }

        return ids;
    }

});
Ext.reg('modforms-grid-forms', modforms.grid.Forms);
