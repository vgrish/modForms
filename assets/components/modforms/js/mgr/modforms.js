var modforms = function(config) {
	config = config || {};
	modforms.superclass.constructor.call(this, config);
};
Ext.extend(modforms, Ext.Component, {
	page: {},
	window: {},
	grid: {},
	tree: {},
	panel: {},
	combo: {},
	config: {},
	view: {},
	tools: {}
});
Ext.reg('modforms', modforms);

modforms = new modforms();