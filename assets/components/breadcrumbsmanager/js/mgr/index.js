var BreadCrumbsManager = function(config) {
	config = config || {};
	BreadCrumbsManager.superclass.constructor.call(this,config);
};
Ext.extend(BreadCrumbsManager,Ext.Component,{
	page:{},window:{},grid:{},tree:{},panel:{},combo:{},config: {},view: {},utils: {}
});
Ext.reg('breadcrumbsmanager',BreadCrumbsManager);

BreadCrumbsManager = new BreadCrumbsManager();
if (typeof BreadCrumbsManager.modx23 == 'undefined') {
    BreadCrumbsManager.modx23 = typeof MODx.config.connector_url != 'undefined' ? true : false;
}
