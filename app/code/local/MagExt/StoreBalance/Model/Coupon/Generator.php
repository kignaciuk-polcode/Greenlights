<?php
/**
 * MagExtension
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the MagExtension EULA 
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.magextension.com/LICENSE.txt
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to info@magextension.com so we can send you a copy.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade the extension to newer
 * versions in the future. If you wish to customize the extension for your
 * needs please refer to http://www.magextension.com for more information.
 *
 * @category   MagExt
 * @package    MagExt_StoreBalance
 * @copyright  Copyright (c) 2010 MagExtension (http://www.magextension.com/)
 * @license    http://www.magextension.com/LICENSE.txt End-User License Agreement
 */
 
/**
 * Random codes generator
 *
 * @author  MagExtension Development team
 */
class MagExt_StoreBalance_Model_Coupon_Generator extends Mage_Core_Model_Abstract
{
    static protected $_alphabet = array(
        'alphabet' => 'abcdefghijklmnopqrstuvwxyz' ,
        'numbers' => '1234567890' ,
        'symbols' => '`~!@#$%^&*()_+-=[];\,./{}:|<>?'
    );
    
    protected function _construct()
    {
        $this->setLength(32)->setUseSmall(false)
        ->setUseBig(true)->setBlockSize(0)->setUseSymbols(false)
        ->setUseNumbers(true)->setAlphabet(self::$_alphabet)
        ->setExclude(array())->setBlockSeparator('-');
    }
    
    /**
     * Reset all generated data
     * @return MagExt_StoreBalance_Model_Coupon_Generator
     */
    protected function _reset()
    {
        if ($this->hasCompiledAlphabet())
        {
            $this->unsCompiledAlphabet();
        }
        return $this;
    }
    
    /**
     * Get compiled symbols set
     * @return mixed
     */
    public function getCompiledAlphabet()
    {
        if (!$this->hasCompiledAlphabet())
        {
            $this->_compileAlphabet();
        }
        return $this->getData('compiled_alphabet');
    }
    
    /**
     * Separator setter
     * @param string $separator
     * @return MagExt_StoreBalance_Model_Coupon_Generator
     */
    public function setBlockSeparator( $separator )
    {
        return $this->_reset()->setData('block_separator',$separator);
    }
    
    /**
     * Characters setter
     * @param string $alphabet
     * @return MagExt_StoreBalance_Model_Coupon_Generator
     */
    public function setAlphabet( array $alphabet )
    {
        return $this->_reset()->setData('alphabet',$alphabet);
    }
    
    /**
     * Code length setter
     * @param int $length
     * @return MagExt_StoreBalance_Model_Coupon_Generator
     */
    public function setLength( $length )
    {
        return $this->_reset()->setData('length',(int)$length);
    }
    
    /**
     * Code blocks size setter
     * @param int $size
     * @return MagExt_StoreBalance_Model_Coupon_Generator
     */
    public function setBlockSize( $size )
    {
        return $this->_reset()->setData('block_size',$size);
    }
    
    /**
     * Use lower case letters
     * @param boolean $use
     * @return MagExt_StoreBalance_Model_Coupon_Generator
     */
    public function setUseSmall( $use = true )
    {
        return $this->_reset()->setData('use_small',$use);
    }
    
    /**
     * Use symbols
     * @param boolean $use
     * @return MagExt_StoreBalance_Model_Coupon_Generator
     */
    public function setUseSymbols( $use = true )
    {
        return $this->_reset()->setData('use_symbols',$use);
    }
    
    /**
     * Use upper case letters
     * @param boolean $use
     * @return MagExt_StoreBalance_Model_Coupon_Generator
     */
    public function setUseBig( $use = true )
    {
        return $this->_reset()->setData('use_big',$use);
    }
    
    /**
     * Use numbers
     * @param boolean $use
     * @return MagExt_StoreBalance_Model_Coupon_Generator
     */
    public function setUseNumbers( $use = true )
    {
        return $this->_reset()->setData('use_numbers',$use);
    }
    
    /**
     * Prepare the set of characters for code generation
     */
    protected function _compileAlphabet()
    {
        $symbols = array(); 
        $tmp = array();
        $this->_parseAlphabetLine($this->alphabet['alphabet'],$tmp);
        $symbols = $this->getUseSmall() ? $tmp : array();
        if ($this->getUseBig())
        {
            foreach ($tmp as $value)
            {
                $value = strtoupper($value);
                $symbols[$value] = $value;
            }
        }
        if ($this->getUseNumbers())
        {
            $this->_parseAlphabetLine($this->alphabet['numbers'],$symbols);
        }
        if ($this->getUseSymbols())
        {
            $this->_parseAlphabetLine($this->alphabet['symbols'],$symbols);
        }
        $this->setCompiledAlphabet($symbols);
    }
    
    /**
     * Return the size of prepared character set
     */
    public function getAlphabetSize()
    {
        return sizeof($this->getCompiledAlphabet());
    }
    
    /**
     * Generates random character sequence
     * @return array randomized characters positions
     */
    protected function _getSequence()
    {
        $sequence = array();
        $length = $this->getLength();
        $latest = $this->getAlphabetSize()-1;
        for ($i = 0 ; $i < $length ; ++$i)
        {
            $sequence[] = mt_rand(0,$latest);
        }
        return $sequence;
    }
    
    /**
     * Generates a code
     * @return string generated code
     */
    public function generate()
    {
        $symbols = $this->getCompiledAlphabet();
        $sequence = $this->_getSequence();
        $length = sizeof($sequence);
        $blockSize = $this->getBlockSize();
        if (is_array($blockSize))
        {
            $currBlockSize = $blockSize ? (int)array_shift($blockSize) : 0;
        }
        else
        {
            $currBlockSize = (int)$blockSize;
            $blockSize = $currBlockSize;
        }
        $result = '';
        $separator = $this->getBlockSeparator();
        $keys = array_keys($symbols);
        for ($i = 0 ; $i < $length ; ++$i )
        {
            $result .= $symbols[$keys[array_shift($sequence)]];
            if ($currBlockSize > 0)
            {
                if (--$currBlockSize == 0 && ($i + 1 < $length) && $blockSize)
                {
                    $result .= $separator;
                    $currBlockSize = is_array($blockSize) ? (int)array_shift($blockSize) : $blockSize;
                }
            }
        }
        return $result;
    }
    
    /**
     * Convert the characters string to the array of characters
     * @param string $alphabet
     * @param array $symbols
     */
    protected function _parseAlphabetLine( $alphabet , &$symbols )
    {
        $len = strlen($alphabet);
        for ($i = 0 ; $i< $len ; ++$i)
        {
            $symbol = substr($alphabet,$i,1);
            $symbols[$symbol] = $symbol;
        }
    }
}