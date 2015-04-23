<?php 

/**
* CustomErrorBehavior
*/
class CustomErrorBehavior extends CBehavior
{
	
	private $_errorMessages = [];

    /**
     * Use in method :  return $this->setCustomErrorMessage(message);
     *
     * @param array $errorMessages
     * @return false
     */
    public function setCustomErrorMessage($errorMessages)
    {
        if(!is_array($errorMessages))
            $errorMessages = [$errorMessages];

        $this->_errorMessages = $errorMessages;

        return false;
    }

    /**
     * @param string $errorMessage
     */
    public function addCustomErrorMessage($errorMessage)
    {
        $this->_errorMessages[] = $errorMessage;
    }

    /**
     * @return array
     */
    public function getCustomErrorMessages()
    {
        return $this->_errorMessages;
    }

    /**
     * @return mixed
     */
    public function getCustomErrorMessageFirst()
    {
        return reset($this->_errorMessages);
    }

    /**
     * @return void
     */
    public function clearCustomErrorMessages()
    {
        $this->_errorMessages = [];
        return;
    }
}


