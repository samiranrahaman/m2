<?php
namespace Addsubscriptionplans\Gridpartsubscriptionplans\Helper;

/**
 * Default review helper
 */
class Option extends \Magento\Framework\App\Helper\AbstractHelper
{
    
    /**
     * Filter manager
     *
     * @var \Magento\Framework\Filter\FilterManager
     */
    protected $filter;

    /**
     * Escaper
     *
     * @var \Magento\Framework\Escaper
     */
    protected $_escaper;

    /**
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Framework\Escaper $escaper
     * @param \Magento\Framework\Filter\FilterManager $filter
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Framework\Escaper $escaper,
        \Magento\Framework\Filter\FilterManager $filter
    ) {
        $this->_escaper = $escaper;
        $this->filter = $filter;
        parent::__construct($context);
    }
    
    
    /**
     * Get review statuses with their codes
     *
     * @return array
     */
    public function getStatuses()
    {
        return [
            \Magento\Review\Model\Review::STATUS_APPROVED => __('Active'),
            \Magento\Review\Model\Review::STATUS_PENDING => __('Deactive')
        ];
    }
    
	
	public function getOngoingfee()
    {
        return [
            \Magento\Review\Model\Review::STATUS_APPROVED => __('Fee'),
            \Magento\Review\Model\Review::STATUS_PENDING => __('Free')
        ];
    }
	
	public function getBusinessApp()
	{
		return [
            \Magento\Review\Model\Review::STATUS_APPROVED => __('Yes'),
            \Magento\Review\Model\Review::STATUS_PENDING => __('No')
        ];
	}
	
	public function getONBA()
	{
		return [
            \Magento\Review\Model\Review::STATUS_APPROVED => __('Yes'),
            \Magento\Review\Model\Review::STATUS_PENDING => __('No')
        ];
	}
	
	public function getED()
	{
		return [
            \Magento\Review\Model\Review::STATUS_APPROVED => __('Yes'),
            \Magento\Review\Model\Review::STATUS_PENDING => __('No')
        ];
	}
	

	
	public function getPAA()
	{
		return [
            \Magento\Review\Model\Review::STATUS_APPROVED => __('Yes'),
            \Magento\Review\Model\Review::STATUS_PENDING => __('No')
        ];
	}
	public function getCT()
	{
		return [
            \Magento\Review\Model\Review::STATUS_APPROVED => __('Yes'),
            \Magento\Review\Model\Review::STATUS_PENDING => __('No')
        ];
	}
	
	
	public function getALOGO()
	{
		return [
            \Magento\Review\Model\Review::STATUS_APPROVED => __('Yes'),
            \Magento\Review\Model\Review::STATUS_PENDING => __('No')
        ];
	}
	
	public function getAYPSP()
	{
		return [
            \Magento\Review\Model\Review::STATUS_APPROVED => __('Yes'),
            \Magento\Review\Model\Review::STATUS_PENDING => __('No')
        ];
	}
	
	
	
	
	public function getELC()
	{
	return [
            \Magento\Review\Model\Review::STATUS_APPROVED => __('Yes'),
            \Magento\Review\Model\Review::STATUS_PENDING => __('No')
        ];	
		
	}
	public function getDMT()
	{
	return [
            \Magento\Review\Model\Review::STATUS_APPROVED => __('Yes'),
            \Magento\Review\Model\Review::STATUS_PENDING => __('No')
        ];	
		
	}
	public function getCAM()
	{
	return [
            \Magento\Review\Model\Review::STATUS_APPROVED => __('Yes'),
            \Magento\Review\Model\Review::STATUS_PENDING => __('No')
        ];	
		
	}
	
	
	public function getCVVC()
	{
	return [
            \Magento\Review\Model\Review::STATUS_APPROVED => __('Yes'),
            \Magento\Review\Model\Review::STATUS_PENDING => __('No')
        ];	
		
	}
  public function getCRC()
	{
	return [
            \Magento\Review\Model\Review::STATUS_APPROVED => __('Yes'),
            \Magento\Review\Model\Review::STATUS_PENDING => __('No')
        ];	
		
	}

	public function getCd()
	{
	return [
            \Magento\Review\Model\Review::STATUS_APPROVED => __('Yes'),
            \Magento\Review\Model\Review::STATUS_PENDING => __('No')
        ];	
		
	}
	
	
	
	
	
	
	
    /**
     * Get review statuses as option array
     *
     * @return array
     */
    public function getStatusesOptionArray()
    {
        $result = [];
        foreach ($this->getStatuses() as $value => $label) {
            $result[] = ['value' => $value, 'label' => $label];
        }

        return $result;
    }
	
	
	public function getOngoingfeeOptionArray()
    {
        $result = [];
        foreach ($this->getOngoingfee() as $value => $label) {
            $result[] = ['value' => $value, 'label' => $label];
        }

        return $result;
    }
	
	
	public function getBusinessAppOptionArray()
    {
        $result = [];
        foreach ($this->getBusinessApp() as $value => $label) {
            $result[] = ['value' => $value, 'label' => $label];
        }

        return $result;
    }
	public function getOwnNameBrandedAppArray()
    {
        $result = [];
        foreach ($this->getONBA() as $value => $label) {
            $result[] = ['value' => $value, 'label' => $label];
        }

        return $result;
    }
	
	public function getExtensionDomainAppArray()
    {
        $result = [];
        foreach ($this->getED() as $value => $label) {
            $result[] = ['value' => $value, 'label' => $label];
        }

        return $result;
    }

	public function getPublishedToAppleAndAndroidArray()
    {
        $result = [];
        foreach ($this->getPAA() as $value => $label) {
            $result[] = ['value' => $value, 'label' => $label];
        }

        return $result;
    }
	
	public function getCustomiseYourThemesAndColoursArray()
    {
        $result = [];
        foreach ($this->getCT() as $value => $label) {
            $result[] = ['value' => $value, 'label' => $label];
        }

        return $result;
    }
	public function getAddYourLogoArray()
    {
        $result = [];
        foreach ($this->getALOGO() as $value => $label) {
            $result[] = ['value' => $value, 'label' => $label];
        }

        return $result;
    }
	
	public function getAddyourproductsservicesArray()
    {
        $result = [];
        foreach ($this->getAYPSP() as $value => $label) {
            $result[] = ['value' => $value, 'label' => $label];
        }

        return $result;
    }
	
	public function geteCommerceandlogisticscapabilityArray()
    {
        $result = [];
        foreach ($this->getELC() as $value => $label) {
            $result[] = ['value' => $value, 'label' => $label];
        }
        return $result;
    }
	
	public function getDashboardToMonitorTransactionArray()
    {
        $result = [];
        foreach ($this->getDMT() as $value => $label) {
            $result[] = ['value' => $value, 'label' => $label];
        }
        return $result;
    }
	
	public function getCustomerAppointmentManagementArray()
    {
        $result = [];
        foreach ($this->getCAM() as $value => $label) {
            $result[] = ['value' => $value, 'label' => $label];
        }
        return $result;
    }
	
	
	
	public function getCustomertextvoiceorvideocommunicationArray()
    {
        $result = [];
        foreach ($this->getCVVC() as $value => $label) {
            $result[] = ['value' => $value, 'label' => $label];
        }
        return $result;
    }
	
	
	
	public function getCustomerArray()
    {
        $result = [];
        foreach ($this->getCRC() as $value => $label) {
            $result[] = ['value' => $value, 'label' => $label];
        }
        return $result;
    }
	
	public function getCustomerdatabaseArray()
    {
        $result = [];
        foreach ($this->getCd() as $value => $label) {
            $result[] = ['value' => $value, 'label' => $label];
        }
        return $result;
    }
	
	
	
	
	
	
	
	

}
