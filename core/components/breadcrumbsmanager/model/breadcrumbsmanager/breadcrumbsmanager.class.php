<?php

class BreadCrumbsManager{
	
	public $modx;
	public $config = array();
	public $showInContextMenu = true;
	
	public function __construct(modX &$modx,array $config = array()) {
		$this->modx =& $modx;
		
		$corePath = $this->modx->getOption('breadcrumbsmanager.core_path', $config, $this->modx->getOption('core_path').'components/breadcrumbsmanager/');
		$assetsPath = $this->modx->getOption('breadcrumbsmanager.assets_path', $config, $this->modx->getOption('assets_path').'components/breadcrumbsmanager/');
		$assetsUrl = $this->modx->getOption('breadcrumbsmanager.assets_url', $config, $this->modx->getOption('assets_url').'components/breadcrumbsmanager/');
		$actionUrl = $this->modx->getOption('breadcrumbsmanager.action_url', $config, $assetsUrl.'action.php');
		$connectorUrl = $assetsUrl.'connector.php';

		$this->config = array_merge(array(
			'assetsUrl' => $assetsUrl,
			'cssUrl' => $assetsUrl.'css/',
			'jsUrl' => $assetsUrl.'js/',
			'jsPath' => $assetsPath.'js/',

			'connectorUrl' => $connectorUrl,
			'actionUrl' => $actionUrl,

			'corePath' => $corePath,
			'modelPath' => $corePath.'model/',
		), $config);

		$this->modx->addPackage('breadcrumbsmanager', $this->config['modelPath']);
		
	}
	
	public function get($resource, $mode) {
        if (($mode === modSystemEvent::MODE_NEW) || !$resource) {
            if (!isset($_GET['parent'])) {return;}
            $resource = $this->modx->getObject('modResource', $_GET['parent']);
            if (!$resource) {return;}
        }
        $context = $resource->get('context_key');
        if ($context != 'web') {
            $this->modx->reloadContext($context);
        }

        // /** @TODO вынести в настройки, когда они будут */
        $limit = 3;
        $resources = $this->modx->getParentIds($resource->get('id'), $limit, array( 'context' => $context ));


        if ($mode === modSystemEvent::MODE_NEW) {
            $resources[] = $_GET['parent'];
        }

        $crumbs = array();
        $root = $this->modx->toJSON(array(
            'text' => $context,
            'className' => 'first',
            'root' => true,
            'url' => '?'
        ));
		
		$setting = $this->modx->getObject('modSystemSetting', 'settings_version');
		$version = explode('.',$setting->get('value'));
		$action = $version[1]==3 ? 'resource/update' : '30';
		
		for ($i = count($resources)-1; $i >= 0; $i--) {
            $resId = $resources[$i];
            if ($resId == 0) {
                continue;
            }
            $parent = $this->modx->getObject('modResource', $resId);
            if (!$parent) {break;}			
			$url = '?a=' . $action . '&id=' . $parent->get('id');
			
            $crumbs[] = array(
                'text' => $parent->get('pagetitle'),
			    'url' => $url,
				'id' => $parent->get('id')
            );
        }

        if (count($resources) == $limit) {
            array_unshift($crumbs, array(
                'text' => '...',
            ));
        }
		
		$resourcesRules = array();
        
        $eventParams = array(
            'params' => array(
                'resourcesRules' => & $resourcesRules
            ),
        );
        
        $this->modx->invokeEvent('OnShopModxSetResourcesCreateRules', $eventParams);
         
        $resourcesRulesJSON = $this->modx->toJSON($resourcesRules);
		

        $crumbs = $this->modx->toJSON($crumbs);

        $this->modx->controller->addJavascript($this->config['jsUrl'] . 'mgr/index.js');
        $this->modx->controller->addJavascript($this->config['jsUrl'] . 'mgr/widgets/breadcrumbsmanager.panel.js');
        $this->modx->controller->addHtml("<script>
            Ext.onReady(function() {			
                var header = Ext.getCmp('modx-panel-resource');
                header.insert(1, {
                    xtype: 'breadcrumbsmanager-panel'
                    ,id: 'resource-breadcrumbs'
                    ,desc: ''
                    ,root : {$root}
                });				
                header.doLayout();				

                var crumbCmp = Ext.getCmp('resource-breadcrumbs');
				
                var bd = { trail : {$crumbs}};
                bd.trail.push({text: crumbCmp.getPagetitle()})

		        crumbCmp.updateDetail(bd);
		        //Ext.getCmp('modx-resource-header').hide();

		        Ext.getCmp('modx-resource-pagetitle').on('keyup', function(){
                    bd.trail[bd.trail.length-1] = {text: crumbCmp.getPagetitle()};
                    crumbCmp._updatePanel(bd);
                });				
            });
            </script>"
        );
    }
	
	
}

?>