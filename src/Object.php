<?php
namespace Concrete\Package\Ht7Default\Src;

use Exception,
    JsonSerializable,
    Serializable;

class Object implements Serializable, JsonSerializable
{
    /**
     * If not empty these variable names are used by export methods.<br />
     * Use the construct
     * 
     * @var     array    Indexed array    
     */
    protected $exportVars;
    /**
     * Whetever the export vars is set or not.
     * 
     * @var     boolean
     */
    protected $hasExportVars;
    /**
     * Set this variable true to restrict the import/export to variables with an associated method.
     * 
     * @var     boolean
     */
    protected $hasMethodRestriction;
    /**
     * Set this variable true to restrict the import/export to variables with an explicit class variable declaration.
     * 
     * @var     boolean
     */
    protected $hasVarRestriction;
    /**
     * The default value which will be set to the object variables on reset().
     * 
     * @var     mixed
     */
    protected $resetValue;
    
    /**
     * Creates an object which has extended export/import functionallity.<br />
     * The options can have following keys:
     * - <i>exportVariables</i>: the corresponding method after initialisation is setExportVariables().
     * 
     * @param   mixed       $data           A serialized object of this class or an extender as string,<br />
     *                                      or an assoc array with variable name as key and the variable value as value.
     * @param   array       $options        Assoc array with several options.
     */
    public function __construct($data = false, $options = [])
    {
        if (isset($options['exportVariables'])) {
            $this->setExportVariables($options['exportVariables']);
        } else {
            $this->hasExportVars = false;
        }
        if (isset($options['resetValue'])) {
            $this->setResetValue($options['resetValue']);
        } else {
            $this->setResetValue();
        }
        if (isset($options['hasMethodRestriction'])) {
            $this->hasMethodRestriction = $options['hasMethodRestriction'];
        } else {
            $this->hasMethodRestriction = false;
        }
        if (isset($options['hasVarRestriction'])) {
            $this->hasVarRestriction = $options['hasVarRestriction'];
        } else {
            $this->hasVarRestriction = false;
        }
        $data ? $this->load($data) : '';
    }
    /**
     * Returns an array with predefined object properties. Called by json_encode() which transforms the value into an json string.<br />
     * The set method has to have the pattern as following:<br />
     * setVariableName() - with an uppercase first letter of the variable.
     * 
     * @return  array
     */
    public function jsonSerialize()
    {
        $vars = get_object_vars($this);
        $vars2Export = [];
        foreach ($vars as $key => $value) {
            if ($this->hasExportVars) {
                if (in_array($key, $this->exportVars)) {
                    if ($this->hasMethodRestriction) {
                        if (is_callable([$this, 'get' . ucfirst($key)])) {
                            $vars2Export[$key] = call_user_method('get' . ucfirst($key), $this);
                        }
                    } else {
                        if (is_callable([$this, 'get' . ucfirst($key)])) {
                            $vars2Export[$key] = call_user_method('get' . ucfirst($key), $this);
                        } else {
                            $vars2Export[$key] = $this->$key;
                        }
                    }
                }
            } else {
                if ($this->hasMethodRestriction) {
                    if (is_callable([$this, 'get' . ucfirst($key)])) {
                        $vars2Export[$key] = call_user_method('get' . ucfirst($key), $this);
                    }
                } else {
                    if (is_callable([$this, 'get' . ucfirst($key)])) {
                        $vars2Export[$key] = call_user_method('get' . ucfirst($key), $this);
                    } else {
                        $vars2Export[$key] = $this->$key;
                    }
                }
            }
        }
        return $vars2Export;
    }
    /**
     * Loads the commited data into this object.
     * 
     * @param   mixed       $data       The data to import. It can be an assoc array or a string of an serialized object.
     * @throws Exception
     */
    public function load($data)
    {
        if (is_string($data)) {
            $this->unserialize($data);
        } elseif (!is_array($data) && !is_object($data)) {
            throw new Exception('Wrong data type: ' . gettype($data) . ' found - array or string needed', E_USER_ERROR);
        } else {
            $this->loadObject($data);
        }
    }
    /**
     * Loads the data of an array or object into this object.
     * 
     * @param   mixed           $data       Assoc array or object with the variables to import.
     */
    public function loadObject($data)
    {
        $vars = get_object_vars($this);
        foreach ($data as $key => $value) {
            if ($this->hasExportVars && in_array($key, $this->exportVars)) {
                if ($this->hasMethodRestriction) {
                    call_user_func_array([$this, 'set' . ucfirst($key)], [$value]);
                } else {
                    if (is_callable([$this, 'set' . ucfirst($key)])) {
                        call_user_func_array([$this, 'set' . ucfirst($key)], [$value]);
                    } else {
                        if ($this->hasVarRestriction) {
                            if (key_exists($key, $vars)) {
                                $this->$key = $value;
                            } else {
                                // in debug mode throw exception??
                            }
                        } else {
                            $this->$key = $value;
                        }
                    }
                }
            } elseif (!$this->hasExportVars) {
                if ($this->hasMethodRestriction) {
                    if (is_callable([$this, 'set' . ucfirst($key)])) {
                        call_user_func_array([$this, 'set' . ucfirst($key)], [$value]);
                    }
                } elseif ($this->hasVarRestriction) {
                    if (is_callable([$this, 'set' . ucfirst($key)])) {
                        call_user_func_array([$this, 'set' . ucfirst($key)], [$value]);
                    } elseif (key_exists($key, $vars)) {
                        $this->$key = $value;
                    }
                } else {
                    if (is_callable([$this, 'set' . ucfirst($key)])) {
                        call_user_func_array([$this, 'set' . ucfirst($key)], [$value]);
                    } else {
                        $this->$key = $value;
                    }
                }
            } else {
                // in debug mode throw exception??
            }
        }
    }
    /**
     * Resets the object variables to the default value.
     */
    public function reset()
    {
        $vars = get_object_vars($this);
        foreach ($vars as $key => $value) {
            $this->$key = $this->resetValue;
        }
    }
    /**
     * Serializes this object by checking if there is a set method of the variable.<br />
     * The set method has to have the pattern as following:<br />
     * setVariableName() - with an uppercase first letter of the variable.
     * 
     * @return  string          The serialized object.
     */
    public function serialize()
    {
        return serialize($this->jsonSerialize());
    }
    
    public function setExportVariables($exportVars)
    {
        if (is_array($exportVars)) {
            if (!empty($exportVars)) {
                $exportVarsSanitized = [];
                $definedVars = get_object_vars($this);
                foreach ($exportVars as $variableName) {
                    if (key_exists($variableName, $definedVars)) {
                        $exportVarsSanitized[] = $variableName;
                    }
                }
                if (!empty($exportVarsSanitized)) {
                    $this->exportVars = $exportVarsSanitized;
                    $this->hasExportVars = true;
                } else {
                    throw new Exception(t('The commited array had not valid variable names!'));
                }
            } else {
                throw new Exception(t('Empty array commited!'));
            }
        } else {
            throw new Exception(t('Wrong datatype commited. %s found, %s needed.', [gettype($exportVars), 'array']));
        }
    }
    
    public function setHasMethodRestriction($hasMethodRestriction)
    {
        $this->hasMethodRestriction = $hasMethodRestriction ? true : false;
    }
    public function setHasVarRestriction($hasVarRestriction)
    {
        $this->hasVarRestriction = $hasVarRestriction ? true : false;
    }
    /**
     * Sets the value which will be set to the object valriables if reset() is called.
     * 
     * @param   mixed       $resetValue     The reset value.
     */
    public function setResetValue($resetValue = null)
    {
        $this->resetValue = $resetValue;
    }
    /**
     * Unserializes the commited data and fills this class or an extender of this class with the data.
     * 
     * @param   string      $data           A serialized string of an extender of this class.
     * @throws  Exception
     */
    public function unserialize($data)
    {
        if (is_string($data)) {
            $unserializedData = unserialize($data);
            $this->__construct($unserializedData);
        } else {
            throw new Exception('Wrong data type');
        }
    }
}
