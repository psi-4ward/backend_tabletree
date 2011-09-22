<?php if (!defined('TL_ROOT')) die('You can not access this file directly!');

/**
 * TYPOlight webCMS
 *
 * The TYPOlight webCMS is an accessible web content management system that 
 * specializes in accessibility and generates W3C-compliant HTML code. It 
 * provides a wide range of functionality to develop professional websites 
 * including a built-in search engine, form generator, file and user manager, 
 * CSS engine, multi-language support and many more. For more information and 
 * additional TYPOlight applications like the TYPOlight MVC Framework please 
 * visit the project website http://www.typolight.org.
 *
 * PHP version 5
 * @copyright  2008 Thyon Design
 * @author     John Brand <john.brand@thyon.com> 
 * @package    Backend
 * @license    LGPL
 * @filesource
 */


/**
 * Class TableTree
 *
 * Provide methods to handle input field "tableTree".
 * @copyright  2008 Thyon Design
 * @author     John Brand <john.brand@thyon.com> 
 * @package    Controller
 */

class TableTree extends Widget
{

	/**
	 * Submit user input
	 * @var boolean
	 */
	protected $blnSubmitInput = true;

	/**
	 * Template
	 * @var string
	 */
	protected $strTemplate = 'be_widget';


	/**
	 * Ajax id
	 * @var string
	 */
	protected $strAjaxId;

	/**
	 * Ajax key
	 * @var string
	 */
	protected $strAjaxKey;

	/**
	 * Ajax name
	 * @var string
	 */
	protected $strAjaxName;


	/**
	 * Data Container
	 * @var object
	 */
	protected $dataContainer;


	/**
	 * Load database object
	 * @param array
	 */
	public function __construct($arrAttributes=false, $dc=null)
	{
		$this->import('Database');
    
		parent::__construct($arrAttributes);
    	$this->dataContainer = $dc;
	}


	/**
	 * Add specific attributes
	 * @param string
	 * @param mixed
	 */
	public function __set($strKey, $varValue)
	{
		switch ($strKey)
		{
			case 'mandatory':
				$this->arrConfiguration['mandatory'] = $varValue ? true : false;
				break;

			default:
				parent::__set($strKey, $varValue);
				break;
		}
	}


	/**
	 * Skip the field if "change selection" is not checked
	 * @param mixed
	 * @return mixed
	 */
	protected function validator($varInput)
	{
		if (!($this->Input->post($this->strName.'_save') || $this->alwaysSave))
		{
//			$this->mandatory = false;
			$this->blnSubmitInput = false;
		}

		return parent::validator($varInput);
	}


	/**
	 * Generate the widget and return it as string
	 * @return string
	 */
	public function generate()
	{
		// include javascript file
		$strJSSuffix = '';
		switch(VERSION)
		{
			case '2.7':
			case '2.8':
			case '2.9':
				$strJSSuffix = '_notoken';
				break;
		}
		
		$GLOBALS['TL_JAVASCRIPT'][] = 'system/modules/backend_tabletree/html/tabletreewizard' . $strJSSuffix . '.js';
 
		// get table, column and setup root id's
		$root = $GLOBALS['TL_DCA'][$this->strTable]['fields'][$this->strField]['eval']['root'];
		$root = is_array($root) ? $root : ((is_numeric($root) && $root > 0) ? $root : 0);

		// Reset radio button selection
		if ($GLOBALS['TL_DCA'][$this->strTable]['fields'][$this->strField]['eval']['fieldType'] == 'radio')
		{
			$strReset = "\n" . '    <li class="tl_folder"><div class="tl_left">&nbsp;</div> <div class="tl_right"><label for="ctrl_'.$this->strId.'_0" class="tl_change_selected">'.$GLOBALS['TL_LANG']['MSC']['resetSelected'].'</label> <input type="radio" name="'.$this->strName.'" id="'.$this->strName.'_0" class="tl_tree_radio" value="" onfocus="Backend.getScrollOffset();" /></div><div style="clear:both;"></div></li>';
		}

		// Select ALL checkbox selection
		if ($GLOBALS['TL_DCA'][$this->strTable]['fields'][$this->strField]['eval']['fieldType'] == 'checkbox')
		{
			$strReset = "\n" . '    <li class="tl_folder"><div class="tl_left">&nbsp;</div> <div class="tl_right"><label for="check_all_'.$this->strId.'_0" class="tl_change_selected">'.$GLOBALS['TL_LANG']['MSC']['selectAll'].'</label> <input type="checkbox" id="check_all_' . $this->strId . '_0" class="tl_checkbox" value="" onclick="Backend.toggleCheckboxGroup(this, \'' . $this->strName . '\')" /></div><div style="clear:both;"></div></li>';
		}

		$this->setTableHeader();


		// Create Tree Render with custom root points
		$strTabletree = '';
		$root = is_array($root) ? $root : array($root);
		foreach($root as $pid)
		{
			// call renderTabletree
			$strTabletree .= $this->renderTabletree($pid, -20);			
		}
	
		return '  <ul class="tl_listing'.(strlen($this->strClass) ? ' ' . $this->strClass : '').'" id="'.$this->strId.'">
    <li class="tl_folder_top"><div class="tl_left">'.$this->generateImage((strlen($GLOBALS['TL_DCA'][$this->strTable]['fields'][$this->strField]['eval']['titleIcon']) ? $GLOBALS['TL_DCA'][$this->strTable]['fields'][$this->strField]['eval']['titleIcon'] : 'tablewizard.gif')).' '.(sprintf(strlen($GLOBALS['TL_DCA'][$this->strTable]['fields'][$this->strField]['eval']['title']) ? $GLOBALS['TL_DCA'][$this->strTable]['fields'][$this->strField]['eval']['title'] : ($GLOBALS['TL_LANG']['MSC']['tableTree']['title'] ? $GLOBALS['TL_LANG']['MSC']['tableTree']['title'] : 'Table: %s') , $sourceTable)) .'</div> <div class="tl_right"><label for="ctrl_'.$this->strId.'" class="tl_change_selected">'.$GLOBALS['TL_LANG']['MSC']['changeSelected'].'</label> <input type="checkbox" name="'.$this->strName.'_save" id="ctrl_'.$this->strId.'" class="tl_tree_checkbox" value="1" onclick="Backend.showTreeBody(this, \''.$this->strId.'_parent\');" /></div><div style="clear:both;"></div></li><li class="parent" id="'.$this->strId.'_parent"><ul>'.$strTabletree.$strReset.'
  </ul></li></ul>';
	}


	public function setTableHeader()
	{
			
		// get icons
		list($sourceTable, $sourceColumn) = explode('.', $GLOBALS['TL_DCA'][$this->strTable]['fields'][$this->strField]['eval']['tableColumn']);

		// setup default icons
		$titleIcon = 'tablewizard.gif';
		$icon = 'iconPLAIN.gif';

		// check if this is a catalog table
		$blnCatalog = false;
		if ($this->Database->tableExists('tl_catalog_types'))
		{
			$objCatalog = $this->Database->prepare("SELECT * FROM tl_catalog_types WHERE tableName=?")
					->limit(1)
					->execute($sourceTable);
		
			if ($objCatalog->numRows)
			{
				$blnCatalog = true;
				$this->import('Catalog');
				
				// load langages
				$GLOBALS['TL_LANG'][$objCatalog->tableName] = $GLOBALS['TL_LANG']['tl_catalog_items'];
				// load dca
				$GLOBALS['TL_DCA'][$objCatalog->tableName] = $this->Catalog->getCatalogDca($objCatalog->id);
	
				$titleIcon = 'system/modules/catalog/html/icon.gif';
				$icon = 'iconPLAIN.gif';
				$headerPrefix = $objCatalog->name;
	
			}
		}		
		
		if (!$blnCatalog)
		{
			$this->loadLanguageFile($sourceTable);
			$this->loadDataContainer($sourceTable);			

			$headerPrefix = ucfirst(str_replace('tl ', '', str_replace('_', ' ', $sourceTable)));

			$titleIcon = $GLOBALS['TL_DCA'][$this->strTable]['fields'][$this->strField]['reference']['icon'][$sourceTable][0] ? $GLOBALS['TL_DCA'][$this->strTable]['fields'][$this->strField]['reference']['icon'][$sourceTable][0] : ($GLOBALS['TL_CONFIG']['tableTree']['icon'][$sourceTable][0] ? $GLOBALS['TL_CONFIG']['tableTree']['icon'][$sourceTable][0] : 'tablewizard.gif');

			$icon = $GLOBALS['TL_DCA'][$this->strTable]['fields'][$this->strField]['reference']['icon'][$sourceTable][1] ? $GLOBALS['TL_DCA'][$this->strTable]['fields'][$this->strField]['reference']['icon'][$sourceTable][1] : ($GLOBALS['TL_CONFIG']['tableTree']['icon'][$sourceTable][1] ? $GLOBALS['TL_CONFIG']['tableTree']['icon'][$sourceTable][1] : 'iconPLAIN.gif');
		}

		if ($GLOBALS['TL_DCA'][$this->strTable]['fields'][$this->strField]['eval']['titleValues'])
		{
			// cannot use $this->varValue, as its value is not available at load time
			$objData = $this->Database->prepare("SELECT ".$this->strField." FROM ".$this->strTable." WHERE id=?")
									 ->execute($this->Input->get('id'));

			$varData = $objData->fetchAllAssoc();
			$varData = $varData[0][$this->strField];
			
			$ids = array_flip(explode(',', deserialize($varData)));

			$selection = array_intersect_key($GLOBALS['TL_DCA'][$this->strTable]['fields'][$this->strField]['options']?$GLOBALS['TL_DCA'][$this->strTable]['fields'][$this->strField]['options']:array(), $ids);
			if (count($selection))
			{
				$GLOBALS['TL_DCA'][$this->strTable]['fields'][$this->strField]['eval']['title'] = implode(', ', $selection);
			}
			else
			{
				$GLOBALS['TL_DCA'][$this->strTable]['fields'][$this->strField]['eval']['title'] = sprintf($GLOBALS['TL_LANG']['MSC']['tableTree']['optionsTitle'], $GLOBALS['TL_DCA'][$this->strTable]['fields'][$this->strField]['label'][0]);
			}
		}

		if (!strlen($GLOBALS['TL_DCA'][$this->strTable]['fields'][$this->strField]['eval']['title']))
		{
			$GLOBALS['TL_DCA'][$this->strTable]['fields'][$this->strField]['eval']['title'] = '<strong>'. $headerPrefix . '</strong>: ' .
				($GLOBALS['TL_DCA'][$sourceTable]['fields'][$sourceColumn]['label'][0] ? $GLOBALS['TL_DCA'][$sourceTable]['fields'][$sourceColumn]['label'][0] : '<em>'.$sourceColumn.'</em>') ;
		}
		$GLOBALS['TL_DCA'][$this->strTable]['fields'][$this->strField]['eval']['titleIcon'] =  $titleIcon;
		$GLOBALS['TL_DCA'][$this->strTable]['fields'][$this->strField]['eval']['icon'] = $icon;
	}


	/**
	 * Generate a particular subpart of the page tree and return it as HTML string
	 * @param integer
	 * @param string
	 * @param integer
	 * @return string
	 */
	public function generateAjax($id, $strField, $level)
	{
		$this->strField = $strField;

		if (!$this->Input->post('isAjax'))
		{
			return '';
		}

		if ($this->Database->fieldExists($this->strField, $this->strTable))
		{
			$objField = $this->Database->prepare("SELECT " . $this->strField . " FROM " . $this->strTable . " WHERE id=?")
										 ->limit(1)
										 ->execute($this->strId);
			
			if ($objField->numRows)
			{
				$this->varValue = deserialize($objField->$strField);

				$this->dataContainer->field = $strField;
				$loadProcs = $GLOBALS['TL_DCA'][$this->strTable]['fields'][$this->strField]['load_callback'];
				if (is_array($loadProcs))
				{
					foreach ($loadProcs as $loadProc)
					{
						list($c, $m) = $loadProc;
						$this->import($c);
						$this->varValue = $this->$c->$m($this->varValue, $this->dataContainer);
					}
				}

			}
		}
       
		return $this->renderTabletree($id, ($level * 20));
	}




	/**
	 * Check the Ajax pre actions
	 * @param string
	 * @param object
	 * @return string
	 */
	public function executePreActions($action)
	{
		switch ($action)
		{
			// Toggle nodes of the file or page tree
			case 'toggleTabletree':
				$this->strAjaxId = preg_replace('/.*_([0-9a-zA-Z]+)$/i', '$1', $this->Input->post('id'));
				$this->strAjaxKey = str_replace('_' . $this->strAjaxId, '', $this->Input->post('id'));

				if ($this->Input->get('act') == 'editAll')
				{
					$this->strAjaxKey = preg_replace('/(.*)_[0-9a-zA-Z]+$/i', '$1', $this->strAjaxKey);
					$this->strAjaxName = preg_replace('/.*_([0-9a-zA-Z]+)$/i', '$1', $this->Input->post('name'));
				}

				$nodes = $this->Session->get($this->strAjaxKey);
				$nodes[$this->strAjaxId] = intval($this->Input->post('state'));

				$this->Session->set($this->strAjaxKey, $nodes);
				exit; break;

			// Load nodes of the file or page tree
			case 'loadTabletree':
				$this->strAjaxId = preg_replace('/.*_([0-9a-zA-Z]+)$/i', '$1', $this->Input->post('id'));
				$this->strAjaxKey = str_replace('_' . $this->strAjaxId, '', $this->Input->post('id'));

				if ($this->Input->get('act') == 'editAll')
				{
					$this->strAjaxKey = preg_replace('/(.*)_[0-9a-zA-Z]+$/i', '$1', $this->strAjaxKey);
					$this->strAjaxName = preg_replace('/.*_([0-9a-zA-Z]+)$/i', '$1', $this->Input->post('name'));
				}

				$nodes = $this->Session->get($this->strAjaxKey);
				$nodes[$this->strAjaxId] = intval($this->Input->post('state'));

				$this->Session->set($this->strAjaxKey, $nodes);
				break;
		}
	}


	/**
	 * Check the Ajax post actions
	 * @param string
	 * @param object
	 * @return string
	 */
	public function executePostActions($action, $dc)
	{
		if ($action == 'loadTabletree')
		{

			$arrData['strTable'] = $dc->table;
			$arrData['id'] = strlen($this->strAjaxName) ? $this->strAjaxName : $dc->id;
			$arrData['name'] = $this->Input->post('name');
		
			$objWidget = new $GLOBALS['BE_FFL']['tableTree']($arrData, $dc);
	
			echo $objWidget->generateAjax($this->strAjaxId, $this->Input->post('field'), intval($this->Input->post('level')));
			exit; break;

		}
	}



	/**
	 * Recursively render the tabletree
	 * @param int
	 * @param integer
	 * @return string
	 */
	private function renderTabletree($pid, $intMargin)
	{
		static $session;
		$session = $this->Session->getData();

		list($sourceTable, $sourceColumn) = explode('.', $GLOBALS['TL_DCA'][$this->strTable]['fields'][$this->strField]['eval']['tableColumn']);

		$flag = substr($this->strField, 0, 2);
		$node = 'tree_' . $this->strTable . '_' . $this->strField;
		$xtnode = 'tree_' . $this->strTable . '_' . $this->strName;

		// Get session data and toggle nodes
		if ($this->Input->get($flag.'tg'))
		{
			$session[$node][$this->Input->get($flag.'tg')] = (isset($session[$node][$this->Input->get($flag.'tg')]) && $session[$node][$this->Input->get($flag.'tg')] == 1) ? 0 : 1;
			$this->Session->setData($session);

			$this->redirect(preg_replace('/(&(amp;)?|\?)'.$flag.'tg=[^& ]*/i', '', $this->Environment->request));
		}

		$return = '';
		$intSpacing = 20;
		$level = ($intMargin / $intSpacing + 1);

		// check if this tree has a pid, catalog or a flat table
		try
		{
			if($this->Database->tableExists('tl_catalog_types'))
			{
				$objCatalog = $this->Database->prepare("SELECT tableName FROM tl_catalog_types WHERE tableName=?")
						->limit(1)
						->execute($sourceTable);
				$blnCatalog = ($objCatalog->numRows == 1);
			}
			else
			{
				$blnCatalog = false;
			}

			$treeView = $this->Database->fieldExists('pid', $sourceTable) && !$blnCatalog;

			$sort = $GLOBALS['TL_DCA'][$this->strTable]['fields'][$this->strField]['eval']['sortColumn'];
			if(!($sort && $this->Database->fieldExists($sort, $sourceTable)))
				$sort = $this->Database->fieldExists('sorting', $sourceTable) ? 'sorting' : $sourceColumn;
			if ($treeView)
			{

				$children = $GLOBALS['TL_DCA'][$this->strTable]['fields'][$this->strField]['eval']['children'] 
					? '' : " AND (SELECT COUNT(*) FROM ". $sourceTable ." q WHERE q.pid=o.id)>0";
				$grandChildren = "(SELECT COUNT(*) FROM ". $sourceTable ."
										INNER JOIN ". $sourceTable ." AS Child ON ". $sourceTable .".id=Child.pid
										INNER JOIN ". $sourceTable ." GrandChild ON Child.id=GrandChild.pid
										WHERE ". $sourceTable .".pid=?) AS grandchildCount";

				$objNodes = $this->Database->prepare("SELECT id, (SELECT COUNT(*) FROM ". $sourceTable ." i WHERE i.pid=o.id) AS childCount, ".$grandChildren .", ". $sourceColumn . " AS name FROM ". $sourceTable. " o WHERE pid=?".$children." ORDER BY ". $sort)
										 ->execute($pid, $pid);                           
			}
			
			if (!$treeView || ($treeView && $objNodes->numRows == 0 && $level == 0))  
			{
				if (!$treeView && $pid>0)
				{
					$strWhere = " WHERE id=".$pid;
				}
				$objNodes = $this->Database->execute("SELECT id, 0 AS childCount, 0 AS grandchildCount, ". $sourceColumn ." AS name FROM ". $sourceTable .$strWhere." ORDER BY ".$sort);
			}

		}
		catch (Exception $ee)
		{
			return '';
		}


		// Return if there are no items
		if ($objNodes->numRows < 1)
		{
			return '';
		}

		$minLevel=$GLOBALS['TL_DCA'][$this->strTable]['fields'][$this->strField]['eval']['minLevel']<=$level;
		$maxLevel=(!$GLOBALS['TL_DCA'][$this->strTable]['fields'][$this->strField]['eval']['maxLevel'] || ($GLOBALS['TL_DCA'][$this->strTable]['fields'][$this->strField]['eval']['maxLevel']>$level));

		// Add table item nodes
		while ($objNodes->next())
		{

			$return .= "\n    " . '<li class="'.(($objNodes->childCount && $maxLevel) ? 'tl_folder' : 'tl_file').'" onmouseover="Theme.hoverDiv(this, 1);" onmouseout="Theme.hoverDiv(this, 0);"><div class="tl_left" style="padding-left:'.($intMargin + $intSpacing).'px;">';

			$folderAttribute = 'style="margin-left:20px;"';
			$session[$node][$objNodes->id] = is_numeric($session[$node][$objNodes->id]) ? $session[$node][$objNodes->id] : 0;
			
			//	toggleStructure: function (el, id, level, mode)
			if ((($GLOBALS['TL_DCA'][$this->strTable]['fields'][$this->strField]['eval']['children'] && $objNodes->childCount) || (!$GLOBALS['TL_DCA'][$this->strTable]['fields'][$this->strField]['eval']['children'] && $objNodes->grandchildCount)) && $maxLevel)
			{
				$folderAttribute = '';
				$img = ($session[$node][$objNodes->id] == 1) ? 'folMinus.gif' : 'folPlus.gif';
				$return .= '<a href="'.$this->addToUrl($flag.'tg='.$objNodes->id).'" onclick="Backend.getScrollOffset(); return AjaxRequestTabletree.toggleTabletree(this, \''.$xtnode.'_'.$objNodes->id.'\', \''.$this->strField.'\', \''.$this->strName.'\', '.$level.');">'.$this->generateImage($img, '', 'style="margin-right:2px;"').'</a>';
			}

			$sub = 0;
			$image = $GLOBALS['TL_DCA'][$this->strTable]['fields'][$this->strField]['eval']['icon'] ? $GLOBALS['TL_DCA'][$this->strTable]['fields'][$this->strField]['eval']['icon'] : 'iconPLAIN.gif';


			// Add table item name
			$return .= $this->generateImage($image, '', $folderAttribute).' <label for="'.$this->strName.'_'.$objNodes->id.'">'.(($objNodes->childCount && $maxLevel) ? '<strong>' : '').$objNodes->name.(($objNodes->childCount) ? '</strong>' : '').'</label></div> <div class="tl_right">';

			// Prevent parent selection
			if ($GLOBALS['TL_DCA'][$this->strTable]['fields'][$this->strField]['eval']['childrenOnly'] && $objNodes->childCount)
			{
				$return .= '&nbsp;';
			}

			// Add checkbox or radio button
			else
			{
				// only add input when the minumum level has been reached
				if($minLevel)
				{
					switch ($GLOBALS['TL_DCA'][$this->strTable]['fields'][$this->strField]['eval']['fieldType'])
					{
						case 'checkbox':
							$return .= '<input type="checkbox" name="'.$this->strName.'[]" id="'.$this->strName.'_'.$objNodes->id.'" class="tl_checkbox" value="'.specialchars($objNodes->id).'" onfocus="Backend.getScrollOffset();"'.$this->optionChecked($objNodes->id, $this->varValue).' />';
							break;
		
						case 'radio':
							$return .= '<input type="radio" name="'.$this->strName.'" id="'.$this->strName.'_'.$objNodes->id.'" class="tl_tree_radio" value="'.specialchars($objNodes->id).'" onfocus="Backend.getScrollOffset();"'.$this->optionChecked($objNodes->id, $this->varValue).' />';
							break;
					}
				}
			}

			$return .= '</div><div style="clear:both;"></div></li>';
			// Call next node
			if ($objNodes->childCount && $session[$node][$objNodes->id] == 1 && $maxLevel)
			{
				$return .= '<li class="parent" id="'.$xtnode.'_'.$objNodes->id.'"><ul class="level_'.$level.'">';
				$return .= $this->renderTabletree($objNodes->id, ($intMargin + $intSpacing));
				$return .= '</ul></li>';
			}
		}

		return $return;
	}
}

?>