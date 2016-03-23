modforms.panel.All = function(config) {

    config = config || {};
    Ext.apply(config, {
        baseCls: 'modx-formpanel',
        cls: 'modforms-formpanel',
        layout: 'anchor',
        hideMode: 'offsets',
        items: [{
            xtype: 'modx-tabs',
            defaults: {
                border: false,
                autoHeight: true
            },
            border: true,
            hideMode: 'offsets',
            items: [{
                title: _('modforms_forms'),
                layout: 'anchor',
                items: [{
                    html: _('modforms_forms_intro'),
                    cls: 'panel-desc'
                }, {
                    xtype: 'modforms-grid-forms',
                    cls: 'main-wrapper'
                }]
            }]
        }]
    });
    modforms.panel.All.superclass.constructor.call(this, config);
};
Ext.extend(modforms.panel.All, MODx.Panel);
Ext.reg('modforms-panel-all', modforms.panel.All);
