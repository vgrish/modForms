modforms.window.CreateForm = function (config) {
    config = config || {};
    Ext.applyIf(config, {
        title: _('create'),
        width: 550,
        autoHeight: true,
        url: modforms.config.connector_url,
        action: 'mgr/form/create',
        fields: this.getFields(config),
        keys: [{
            key: Ext.EventObject.ENTER, shift: true, fn: function () {
                this.submit()
            }, scope: this
        }]
    });
    modforms.window.CreateForm.superclass.constructor.call(this, config);
};
Ext.extend(modforms.window.CreateForm, MODx.Window, {

    getFields: function (config) {
        return [{
            xtype: 'hidden',
            name: 'id'
        }, {
            xtype: 'hidden',
            name: 'class'
        }, {
            xtype: 'textfield',
            fieldLabel: _('modforms_name'),
            name: 'name',
            anchor: '99%',
            allowBlank: false
        }, {
            xtype: 'modforms-combo-options',
            custm: true,
            clear: true,
            fieldLabel: _('modforms_selector'),
            hiddenName: 'selector',
            key: 'selector',
            form: config.record.id,
            anchor: '99%',
            allowBlank: false
        }, {
            xtype: 'modforms-combo-options',
            custm: true,
            clear: true,
            fieldLabel: _('modforms_email'),
            hiddenName: 'email',
            key: 'email',
            form: config.record.id,
            anchor: '99%',
            allowBlank: false
        }, {
            xtype: 'modforms-combo-chunk',
            custm: true,
            clear: true,
            fieldLabel: _('modforms_body'),
            name: 'body',
            anchor: '99%',
            allowBlank: false
        }, {
            xtype: 'xcheckbox',
            hideLabel: true,
            boxLabel: _('modforms_subject'),
            checked: false,
            workCount: 1,
            listeners: {
                check: modforms.tools.handleChecked,
                afterrender: modforms.tools.handleChecked
            }
        }, {
            xtype: 'textarea',
            fieldLabel: _('modforms_subject'),
            name: 'subject',
            anchor: '99%',
            allowBlank: true
        }, {
            xtype: 'checkboxgroup',
            hideLabel: true,
            /*fieldLabel: '',*/
            columns: 3,
            items: [{
                xtype: 'xcheckbox',
                boxLabel: _('modforms_active'),
                name: 'active',
                checked: config.record.active
            }]
        }]
    },

    getLeftFields: function (config) {
        return [];
    },

    getRightFields: function (config) {
        return [];
    }

});
Ext.reg('modforms-window-create-form', modforms.window.CreateForm);
