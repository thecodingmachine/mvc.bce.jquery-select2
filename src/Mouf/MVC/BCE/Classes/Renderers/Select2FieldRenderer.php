<?php
namespace Mouf\MVC\BCE\Classes\Renderers;

use Mouf\Html\Widgets\Form\Styles\LayoutStyle;
use Mouf\MVC\BCE\Classes\Descriptors\FieldDescriptorInstance;
use Mouf\Html\Widgets\Form\SelectField;
use Mouf\Html\Tags\Option;
use Mouf\MVC\BCE\Classes\ValidationHandlers\BCEValidationUtils;
use Mouf\Html\Widgets\Form\RadiosField;
use Mouf\Html\Widgets\Form\RadioField;
use Mouf\Utils\Value\ValueUtils;
use Mouf\Html\Widgets\Select2\Select2Field;


/**
 * A renderer class that ouputs a select2 box. Tt doesn't handle multiple selection.
 *
 * @ApplyTo {"type": ["fk"]}
 */
class Select2FieldRenderer extends DefaultViewFieldRenderer implements SingleFieldRendererInterface {
	
	/**
	 * (non-PHPdoc)
	 * @see FieldRendererInterface::render()
	 */
	public function renderEdit($descriptorInstance){
		/* @var $descriptorInstance FieldDescriptorInstance */
		$descriptor = $descriptorInstance->fieldDescriptor;
		$value = $descriptorInstance->getFieldValue();
		
		$selectField = new Select2Field($descriptor->getFieldLabel(), $descriptorInstance->getFieldName(), $value);
		$selectField->getSelect()->setName($descriptorInstance->getFieldName());
		$selectField->getSelect()->setId($descriptorInstance->getFieldName());
		if($descriptorInstance->getValidator()) {
			$selectField->setSelectClasses($descriptorInstance->getValidator());
		}
		if(isset($descriptorInstance->attributes['styles'])) {
			$selectField->getSelect()->setStyles($descriptorInstance->attributes['styles']);
		}
		$selectField->getSelect()->setDisabled((!$descriptor->canEdit()) ? "disabled" : null);
		$selectField->setRequired(BCEValidationUtils::hasRequiredValidator($descriptor->getValidators()));
		
		$options = array();
		if($descriptor->getPlaceHolder()) {
			$option = new Option();
			$option->setValue('');
			$option->addText($descriptor->getPlaceHolder());
			$options[] = $option;
		}
		$data = $descriptor->getData();
		foreach ($data as $linkedBean) {
			$beanId = $descriptor->getRelatedBeanId($linkedBean);
			$beanLabel = $descriptor->getRelatedBeanLabel($linkedBean);
			$option = new Option();
			$option->setValue($beanId);
			$option->addText($beanLabel);
			if ($beanId == $value) {
				$option->setSelected('selected');
			}
			$options[] = $option;
		}
		$selectField->setOptions($options);

		if ($this->getLayout() == null){
			$selectField->setLayout($descriptorInstance->form->getDefaultLayout());
		}
		
		ob_start();
		$selectField->toHtml();
		return ob_get_clean();
	
	}
	
	/**
	 * (non-PHPdoc)
	 * @see FieldRendererInterface::getJS()
	 */
	public function getJSEdit($descriptor, $bean, $id){
		/* @var $descriptorInstance FieldDescriptorInstance */
		return array();
	}
	
}