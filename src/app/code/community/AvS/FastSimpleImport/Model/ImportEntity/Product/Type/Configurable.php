<?php

/**
 * Configurable Products Import Model
 *
 * @category   AvS
 * @package    AvS_FastSimpleImport
 * @author     Andreas von Studnitz <avs@avs-webentwicklung.de>
 */
class AvS_FastSimpleImport_Model_ImportEntity_Product_Type_Configurable
    extends Mage_ImportExport_Model_Import_Entity_Product_Type_Configurable
{
    /**
     * Prepare attributes values for save: remove non-existent, remove empty values, remove static.
     *
     * @param array $rowData
     * @return array
     */
    public function prepareAttributesForSave(array $rowData)
    {
        $resultAttrs = array();

        foreach ($this->_getProductAttributes($rowData) as $attrCode => $attrParams) {
            if (!$attrParams['is_static']) {
                if (isset($rowData[$attrCode]) && strlen($rowData[$attrCode])) {
                    $resultAttrs[$attrCode] =
                        ('select' == $attrParams['type'] || 'multiselect' == $attrParams['type'])
                            ? $attrParams['options'][strtolower($rowData[$attrCode])]
                            : $rowData[$attrCode];
                } elseif (null !== $attrParams['default_value']) {
                    if ($this->_isSkuNew($rowData['sku'])) {
                        $resultAttrs[$attrCode] = $attrParams['default_value'];
                    }
                }
            }
        }
        return $resultAttrs;
    }

    /**
     * Check if the given sku belongs to a new product or an existing one
     *
     * @param $sku
     * @return bool
     */
    protected function _isSkuNew($sku)
    {
        return !in_array($sku, $this->_entityModel->getOldSku());
    }
}