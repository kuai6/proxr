<?php

namespace Application\Activity\Activity\Condition;

use Application\Activity\AbstractActivity;
use Application\Activity\Activity;
use Application\Activity\Context;
use Application\Activity\Exception\RuntimeException;


/**
 * Проверка значения переменной из контекста или условия
 *
 * Example
 *  <if variable="form" >
 *      <sequence />
 *  </if>
 *
 * <if condition="someCondition" >
 *      <sequence />
 * </if>
 *
 * Class IfCondition
 * @package Application\Activity\Activity\Condition
 */
class IfCondition extends Activity\Condition
{
    /**
     * значение (слева в условии)
     * @var mixed
     */
    protected $rawValue;

    /**
     * тип значения (слева в условии)
     * @var string
     */
    protected $rawType;

    /**
     * имя переменной со значением (слева в условии)
     * @var  string
     */
    protected $variable;

    /**
     * значение(справа в условии), с которым сравнивают через операнд
     * @var  string
     */
    protected $value = null;

    /**
     * имя переменной, из которой читают значение, с которым сравнивают через операнд
     * @var string
     */
    protected $inValue = null;

    /**
     * проверять на существование
     * @var  bool|null
     */
    protected $isExists = null;

    /**
     * проверять на пустоту
     * @var bool|null
     */
    protected $isNull = null;

    /**
     * значение операнда
     * @var string|null
     */
    protected $operand = null;

    /**
     * @var string
     */
    protected $type = 'string';

    /**
     * проверять на bool
     * @var bool|null
     */
    protected $isTrue = null;

    /**
     * активити, которое нужно выполнить в случае успеха
     * @var Activity\Condition
     */
    protected $conditionActivity;

    /**
     * @param Context $context
     * @return mixed
     */
    public function execute(Context $context)
    {
        $result = $this->resolveCondition($context);
        // выполняем действия только есть это "простой" if, условия,
        // которые вызывались через type сами запускают выполнение
        if ($result && $this->getActivity() != null && $this->getConditionActivity() === null) {
            $this->getActivity()->execute($context);
        }
        return $result;
    }

    /**
     * @param Context $context
     * @return bool
     * @throws RuntimeException
     */
    protected function resolveCondition(Context $context)
    {
        $contextArray = $context->getContext();
        if ($this->getConditionActivity()) {
            return $this->getConditionActivity()->execute($context);
        } elseif ($this->getVariable() || $this->getRawType()) {
            if ($this->getIsExists() !== null) {
                $answer = null !== $this->getRawType()
                    ? ('null' !== $this->getRawType())
                    : array_key_exists($this->getVariable(), $contextArray);

                return ($this->getIsExists() && $answer)
                || (!$this->getIsExists() && !$answer);
            }

            if ($this->getIsNull() !== null) {
                $answer = null !== $this->getRawType()
                    ? (null === $this->getRawValue())
                    : (!array_key_exists($this->getVariable(), $contextArray) || (null === $contextArray[$this->getVariable()]));

                return ($this->getIsNull() && $answer)
                || (!$this->getIsNull() && !$answer);
            }

            if ($this->getIsTrue() !== null) {
                $this->setType('bool');
                $this->setOperand('eq');
                $this->setValue($this->getIsTrue());
            }

            if ($this->getInValue()) {
                $value = $context->get($this->getInValue());
            } else {
                $value = $this->getValue();
            }
            // работаем с операндом
            if ($value !== null && $this->getOperand() !== null) {
                $value = $this->filterByType($value, $this->getType());
                $variableValue = $this->getRawType() !== null
                    ? $this->filterByType($this->getRawValue(), $this->getRawType())
                    : $contextArray[$this->getVariable()];
                $answer = $this->compareViaOperand($variableValue, $value, $this->getOperand());
                return $answer;
            } // operand
        }
        throw new RuntimeException('не удалось проверить условие');
    }

    /**
     * @param $value
     * @param $type
     * @return bool|float|int|string
     */
    protected function filterByType($value, $type)
    {
        switch ($type) {
            case 'bool':
            case 'boolean':
                $t = $value;
                $value = true;
                if ($t === 'false') {
                    $value = false;
                }
                break;
            case 'string':
                $value = (string)$value;
                break;
            case 'int':
            case 'integer':
                $value = (int)$value;
                break;
            case 'float':
            case 'double':
                $value = (float)$value;
                break;
        }
        return $value;
    }

    /**
     * @param mixed $var1
     * @param mixed $var2
     * @param string $operand
     * @return bool
     * @throws RuntimeException
     */
    protected function compareViaOperand($var1, $var2, $operand)
    {
        switch ($operand) {
            case 'gt':
                $answer = ($var1 > $var2);
                break;
            case 'gte':
                $answer = ($var1 >= $var2);
                break;
            case 'lt':
                $answer = ($var1 < $var2);
                break;
            case 'lte':
                $answer = ($var1 <= $var2);
                break;
            case 'eq':
                $answer = ($var1 == $var2);
                break;
            case 'neq':
                $answer = ($var1 != $var2);
                break;
            default:
                throw new RuntimeException('not allowed operand: ' . $this->getOperand());
        }
        return $answer;
    }

    /**
     * @param \SimpleXMLElement $metadata
     * @return mixed
     */
    public function fromMetadata($metadata)
    {
        $attributes = $metadata->attributes();
        $this->setVariable((string)$attributes['variable']);

        if (isset($attributes['condition'])) {
            /** @var Activity\Condition $condition */
            $condition = $this->getServiceLocator()->get((string)$attributes['condition']);
            $condition->fromMetadata($metadata);
            $this->setConditionActivity($condition);
        }

        if (isset($attributes['isNull'])) {
            $this->setIsNull('true' === (string)$attributes['isNull']);
        }
        if (isset($attributes['isExists'])) {
            $this->setIsExists('true' === (string)$attributes['isExists']);
        }
        if (isset($attributes['isTrue'])) {
            $this->setIsTrue((string)$attributes['isTrue']);
        }
        if (isset($attributes['source'])) {
        }
        if (isset($attributes['operand']) && (isset($attributes['value']) || isset($attributes['inValue']))) {
            $this->setOperand((string)$attributes['operand']);
            if (isset($attributes['value'])) {
                $this->setValue((string)$attributes['value']);
            }
            if (isset($attributes['inValue'])) {
                $this->setInValue((string)$attributes['inValue']);
            }
            if (isset($attributes['type'])) {
                $this->setType((string)$attributes['type']);
            }
        }

        if ($metadata->children()->count()) {
            /** @var \SimpleXMLElement $children */
            $children = $metadata->children()[0];
            /** @var AbstractActivity $activity */
            $activity = $this->getServiceLocator()->get($children->getName());

            $activity->fromMetadata($children);
            $this->setActivity($activity);
        }
    }


    /**
     * @param Activity\Condition $conditionActivity
     * @return $this
     */
    public function setConditionActivity($conditionActivity)
    {
        $this->conditionActivity = $conditionActivity;
        return $this;
    }

    /**
     * @return Activity\Condition
     */
    public function getConditionActivity()
    {
        return $this->conditionActivity;
    }

    /**
     * @param string $variable
     * @return $this
     */
    public function setVariable($variable)
    {
        $this->variable = $variable;
        return $this;
    }

    /**
     * @return string
     */
    public function getVariable()
    {
        return $this->variable;
    }

    /**
     * @param string $value
     * @return $this
     */
    public function setValue($value)
    {
        $this->value = $value;
        return $this;
    }

    /**
     * @return string
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @param bool|null $isExists
     * @return $this
     */
    public function setIsExists($isExists)
    {
        $this->isExists = $isExists;
        return $this;
    }

    /**
     * @return bool|null
     */
    public function getIsExists()
    {
        return $this->isExists;
    }

    /**
     * @param bool|null $isNull
     * @return $this
     */
    public function setIsNull($isNull)
    {
        $this->isNull = $isNull;
        return $this;
    }

    /**
     * @return bool|null
     */
    public function getIsNull()
    {
        return $this->isNull;
    }

    /**
     * @param null|string $operand
     * @return $this
     */
    public function setOperand($operand)
    {
        $this->operand = $operand;
        return $this;
    }

    /**
     * @return null|string
     */
    public function getOperand()
    {
        return $this->operand;
    }

    /**
     * @param string $type
     *
     * @return $this
     */
    public function setType($type)
    {
        $this->type = $type;
        return $this;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param bool|null $isTrue
     *
     * @return $this
     */
    public function setIsTrue($isTrue)
    {
        $this->isTrue = $isTrue;
        return $this;
    }

    /**
     * @return bool|null
     */
    public function getIsTrue()
    {
        return $this->isTrue;
    }

    /**
     * @return string
     */
    public function getInValue()
    {
        return $this->inValue;
    }

    /**
     * @param string $inValue
     */
    public function setInValue($inValue)
    {
        $this->inValue = $inValue;
    }

    /**
     * @return string
     */
    public function getRawType()
    {
        return $this->rawType;
    }

    /**
     * @param string $rawType
     * @return $this
     */
    public function setRawType($rawType)
    {
        $this->rawType = $rawType;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getRawValue()
    {
        return $this->rawValue;
    }

    /**
     * @param mixed $rawValue
     * @return $this
     */
    public function setRawValue($rawValue)
    {
        $this->rawValue = $rawValue;
        return $this;
    }
}
