<?php
/**
 * Description of LinkFormField
 *
 * @author Simon
 */
class LinkFormField extends FormField {
	
	static $module_dir = 'link-field';
	
	/**
	 * @var FormField
	 */
	protected $fieldPageID = null;
	
	/**
	 * @var FormField
	 */
	protected $fieldCustomURL = null;
	
	function __construct($name, $title = null, $value = null, $form = null) {
		// naming with underscores to prevent values from actually being saved somewhere
		$this->fieldCustomURL = new TextField("{$name}[CustomURL]", ' External URL: ');
		$this->fieldPageID = new SimpleTreeDropdownField("{$name}[PageID]", "Title", "SiteTree", '', "Title", null, "(External/Custom URL)");
		
		parent::__construct($name, $title, $value, $form);
	}
	
	/**
	 * @return string
	 */
	function Field() {
		Requirements::javascript(self::$module_dir . '/js/admin.js');
		return "<div class=\"fieldgroup\">" .
			"<div id=\"fieldgroupFieldPage\" class=\"fieldgroupField\">" . $this->fieldPageID->SmallFieldHolder() . "</div>" . 
			"<div id=\"fieldgroupFieldCustomURL\" class=\"fieldgroupField\">" . $this->fieldCustomURL->SmallFieldHolder() . "</div>" . 
		"</div>";
	}
	
	function setValue($val) {
//		Debug::dump($eh);
//		Debug::backtrace();
		$this->value = $val;
//		Debug::dump($val);
		if(is_array($val)) {
			$this->fieldPageID->setValue($val['PageID']);
			$this->fieldCustomURL->setValue($val['CustomURL']);
		} elseif($val instanceof LinkField) {
			$this->fieldPageID->setValue($val->getPageID());
			$this->fieldCustomURL->setValue($val->getCustomURL());
		}
	}
	
	/**
	 * 30/06/2009 - Enhancement: 
	 * SaveInto checks if set-methods are available and use them 
	 * instead of setting the values in the money class directly. saveInto
	 * initiates a new Money class object to pass through the values to the setter
	 * method.
	 *
	 * (see @link MoneyFieldTest_CustomSetter_Object for more information)
	 */
	function saveInto($dataObject) {
		
		$fieldName = $this->name;
		if($dataObject->hasMethod("set$fieldName")) {
			$dataObject->$fieldName = DBField::create('LinkField', array(
				"PageID" => $this->fieldPageID->Value(),
				"CustomURL" => $this->fieldCustomURL->Value()
			));
		} else {
			$dataObject->$fieldName->setPageID($this->fieldPageID->Value()); 
			$dataObject->$fieldName->setCustomURL($this->fieldCustomURL->Value());
		}
		
//		Debug::dump($dataObject->$fieldName->getPageID());
//		Debug::dump($dataObject->$fieldName->getCustomURL());
//		Debug::dump($dataObject);
//		die;
	}

	/**
	 * Returns a readonly version of this field.
	 */
	function performReadonlyTransformation() {
		$clone = clone $this;
		$clone->setReadonly(true);
		return $clone;
	}
	
	/**
	 * @todo Implement removal of readonly state with $bool=false
	 * @todo Set readonly state whenever field is recreated, e.g. in setAllowedCurrencies()
	 */
	function setReadonly($bool) {
		parent::setReadonly($bool);
		
		if($bool) {
			$this->fieldPageID = $this->fieldPageID->performReadonlyTransformation();
			$this->fieldCustomURL = $this->fieldCustomURL->performReadonlyTransformation();
		}
	}
}

?>
