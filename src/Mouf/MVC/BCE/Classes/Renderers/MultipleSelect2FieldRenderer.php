<?php
namespace Mouf\MVC\BCE\Classes\Renderers;
use Mouf\Html\Utils\WebLibraryManager\WebLibraryManager;
use Mouf\MVC\BCE\Classes\Descriptors\BCEFieldDescriptorInterface;
use Mouf\MVC\BCE\Classes\Descriptors\FieldDescriptorInstance;
use Mouf\MVC\BCE\Classes\Descriptors\Many2ManyFieldDescriptor;
use Mouf\Html\Widgets\Form\CheckboxesField;
use Mouf\Html\Widgets\Form\CheckboxField;
use Mouf\Html\Widgets\Form\SelectMultipleField;
use Mouf\Html\Tags\Option;
use Mouf\MVC\BCE\Classes\ValidationHandlers\BCEValidationUtils;
use Mouf\Html\Widgets\Form\RadiosField;
use Mouf\Html\Widgets\Form\RadioField;
use Mouf\Html\Widgets\Select2\Select2MultipleField;
/**
 * A renderer class that ouputs multiple values field like checkboxes , multiselect list, ... fits for many to many relations
 */
class MultipleSelect2FieldRenderer extends BaseFieldRenderer implements MultiFieldRendererInterface, ViewFieldRendererInterface {
	
	/**
	 * 
	 * @var bool
	 */
	private $defaultTradMode = false;
	
	/**
	 * (non-PHPdoc)
	 * @see \Mouf\MVC\BCE\Classes\Renderers\EditFieldRendererInterface::renderEdit()
	 */
	public function renderEdit($descriptorInstance){
		/* @var $descriptorInstance FieldDescriptorInstance */
		$descriptor = $descriptorInstance->fieldDescriptor;
		/* @var $descriptor Many2ManyFieldDescriptor */
		$fieldName = $descriptorInstance->getFieldName();
		$values = $descriptorInstance->getFieldValue();
		$html = "";
		$data = $descriptor->getData();
		$selectIds = array();
		if ($values){
			foreach ($values as $bean) {
				$id = $descriptor->getMappingRightKey($bean);
				$selectIds[] = $id;
			}
		}
		
		$selectMultipleField = new Select2MultipleField($descriptor->getFieldLabel(), $fieldName);
		if($descriptorInstance->getValidator()) {
			$selectMultipleField->setSelectClasses($descriptorInstance->getValidator());
		}
		if(isset($descriptorInstance->attributes['styles'])) {
			$selectMultipleField->getSelect()->setStyles($descriptorInstance->attributes['styles']);
		}
		$options = array();
		foreach ($data as $bean) {
			$beanId = $descriptor->getRelatedBeanId($bean);
			$beanLabel = $descriptor->getRelatedBeanLabel($bean);
			
			$option = new Option();
			$option->setValue($beanId);
			$option->addText($beanLabel);
			if (array_search($beanId, $selectIds) !== false) {
				$option->setSelected('selected');
			}
			$options[] = $option;
		}
		$selectMultipleField->setOptions($options);

		ob_start();
		$selectMultipleField->toHtml();
		return ob_get_clean();
	}
	
	
	/**
	 * (non-PHPdoc)
	 * @see \Mouf\MVC\BCE\Classes\Renderers\EditFieldRendererInterface::getJSEdit()
	 */
	public function getJSEdit(BCEFieldDescriptorInterface $descriptor, $bean, $id, WebLibraryManager $webLibraryManager){
		return array();
	}
	
	/**
	 * (non-PHPdoc)
	 * @see \Mouf\MVC\BCE\Classes\Renderers\DefaultViewFieldRenderer::renderView()
	 */
	public function renderView($descriptor){
		/* @var $descriptor Many2ManyFieldDescriptor */
		$values = $descriptor->getBeanValues();
		foreach ($values as $bean){
			$label = $descriptor->getRelatedBeanLabel($bean);
			$labels[] = $label;
		}
		return count($labels) ? "<ul id='".$descriptor->getFieldName()."-view-field'><li>" . implode("</li><li>", $labels) . "</li><ul>" : "";
	}
	
	/**
	 * (non-PHPdoc)
	 * @see \Mouf\MVC\BCE\Classes\Renderers\DefaultViewFieldRenderer::getJSView()
	 */
	public function getJSView(BCEFieldDescriptorInterface $descriptor, $bean, $id, WebLibraryManager $webLibraryManager){
		return array();
	}
	
	/**
	 *
	 *
	 */
	public function seti18nUtilisation($tradMode){
		$this->defaultTradMode = $tradMode;
	}
	
}