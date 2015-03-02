BreadCrumbsManager.BreadcrumbsPanel = function(config) {
    config = config || {};
    Ext.applyIf(config,{
        bdMarkup: '<tpl if="typeof(trail) != &quot;undefined&quot;">'
            +'<div class="crumb_wrapper">'			
			+'<ul class="crumbs">'
            +'<tpl for="trail">'
            +'<li{[values.className != undefined ? \' class="\'+values.className+\'"\' : \'\' ]}>'
            +'<tpl if="typeof url != \'undefined\'">'
            +'<button type="button" data-url="{url}" data-id="web_{id}" class="controlBtn {[values.root ? \' root\' : \'\' ]}" ext:tree-node-id="web_{id}" >{text}</button>'
            +'</tpl>'
            +'<tpl if="typeof url == \'undefined\'"><span data-id="web_{id}" class="text{[values.root ? \' root\' : \'\' ]}" ext:tree-node-id="web_{id}">{text}</span></tpl>'
            +'</li></tpl></ul>'			
			+'</div></tpl>',
        bodyStyle: {background: 'transparent'}
    });
    BreadCrumbsManager.BreadcrumbsPanel.superclass.constructor.call(this,config);
}

Ext.extend(BreadCrumbsManager.BreadcrumbsPanel,MODx.BreadcrumbsPanel,{

	init: function(){
		var tree = Ext.getCmp('modx-resource-tree');

        this.tpl = new Ext.XTemplate(this.bdMarkup, { compiled: true });
        this.reset(this.desc);
        this.body.on('click', this.onClick, this);
		this.body.on('contextmenu', this.onContextmenu, this);
		this.cm = new Ext.menu.Menu(MODx.menuConfig);
	},

    onClick: function(e) {
		console.log(e);
        var target = e.getTarget();
        if (typeof target != "undefined") {
            var url = target.getAttribute('data-url');
            if (url) {
                MODx.loadPage(url);
            }
        }		
    }
	,onContextmenu: function(e) {
		var target = e.getTarget();
		var tree = Ext.getCmp('modx-resource-tree');	
		var model = tree.getSelectionModel();		
		var id = target.getAttribute('data-id');	
		
		var m = [];
		m.push({
			text: 'Редактировать'
			,handler: function(itm,e) {
				tree.addAttribute(itm,e,tree.cm.activeNode);
			}
			,scope: tree
		});
		console.log(tree.body);
		if(typeof tree !== 'undefined'){
			var node = tree.nodeHash[id];
			//tree.addContextMenuItem(m);
			
			tree._showContextMenu(node,e);
		}
    }
    ,_updateBreadcrumbsPanel: function(data){
        this.tpl.overwrite(this.body, data);
        var $this = this;
        setTimeout(function(){
            $this.ownerCt.doLayout();
        }, 200);
    }
    ,getPagetitle: function(){
        var pagetitleCmp = Ext.getCmp('modx-resource-pagetitle');
        var pagetitle;
        if (typeof pagetitleCmp != "undefined") {
            pagetitle = pagetitleCmp.getValue();
            if (pagetitle.length == 0) {
                pagetitle = _('new_document');
            }
        } else {
            pagetitle = '';
        }
        return pagetitle;
    }
});
Ext.reg('breadcrumbsmanager-panel',BreadCrumbsManager.BreadcrumbsPanel);