<?php
/**
 * This file is part of PHPPowerPoint - A pure PHP library for reading and writing
 * presentations documents.
 *
 * PHPPowerPoint is free software distributed under the terms of the GNU Lesser
 * General Public License version 3 as published by the Free Software Foundation.
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code. For the full list of
 * contributors, visit https://github.com/PHPOffice/PHPWord/contributors.
 *
 * @link        https://github.com/PHPOffice/PHPPowerPoint
 * @copyright   2009-2014 PHPPowerPoint contributors
 * @license     http://www.gnu.org/licenses/lgpl.txt LGPL version 3
 */

namespace PhpOffice\PhpPowerpoint;

use PhpOffice\PhpPowerpoint\Slide;
use PhpOffice\PhpPowerpoint\Slide\Iterator;

/**
 * PHPPowerPoint
 *
 * @category   PHPPowerPoint
 * @package    PHPPowerPoint
 * @copyright  Copyright (c) 2009 - 2010 PHPPowerPoint (http://www.codeplex.com/PHPPowerPoint)
 */
class PhpPowerpoint
{
    /**
     * Document properties
     *
     * @var PHPPowerPoint_DocumentProperties
     */
    private $properties;

    /**
     * Document layout
     *
     * @var PHPPowerPoint_DocumentLayout
     */
    private $layout;

    /**
     * Collection of Slide objects
     *
     * @var PHPPowerPoint_Slide[]
     */
    private $slideCollection = array();

    /**
     * Active slide index
     *
     * @var int
     */
    private $activeSlideIndex = 0;

    /**
     * Create a new PHPPowerPoint with one Slide
     */
    public function __construct()
    {
        // Initialise slide collection and add one slide
        $this->createSlide();
        $this->setActiveSlideIndex();

        // Set initial document properties & layout
        $this->setProperties(new DocumentProperties());
        $this->setLayout(new DocumentLayout());
    }

    /**
     * Get properties
     *
     * @return PHPPowerPoint_DocumentProperties
     */
    public function getProperties()
    {
        return $this->properties;
    }

    /**
     * Set properties
     *
     * @param  PHPPowerPoint_DocumentProperties $value
     * @return PHPPowerPoint
     */
    public function setProperties(DocumentProperties $value)
    {
        $this->properties = $value;

        return $this;
    }

    /**
     * Get layout
     *
     * @return PHPPowerPoint_DocumentLayout
     */
    public function getLayout()
    {
        return $this->layout;
    }

    /**
     * Set layout
     *
     * @param  PHPPowerPoint_DocumentLayout $value
     * @return PHPPowerPoint
     */
    public function setLayout(DocumentLayout $value)
    {
        $this->layout = $value;

        return $this;
    }

    /**
     * Get active slide
     *
     * @return PHPPowerPoint_Slide
     */
    public function getActiveSlide()
    {
        return $this->slideCollection[$this->activeSlideIndex];
    }

    /**
     * Create slide and add it to this presentation
     *
     * @return PhpOffice\PhpPowerpoint\Slide
     */
    public function createSlide()
    {
        $newSlide = new Slide($this);
        $this->addSlide($newSlide);
        return $newSlide;
    }

    /**
     * Add slide
     *
     * @param  PHPPowerPoint_Slide $slide
     * @throws \Exception
     * @return PHPPowerPoint_Slide
     */
    public function addSlide(Slide $slide = null)
    {
        $this->slideCollection[] = $slide;

        return $slide;
    }

    /**
     * Remove slide by index
     *
     * @param  int           $index Slide index
     * @throws \Exception
     * @return PHPPowerPoint
     */
    public function removeSlideByIndex($index = 0)
    {
        if ($index > count($this->slideCollection) - 1) {
            throw new \Exception("Slide index is out of bounds.");
        } else {
            array_splice($this->slideCollection, $index, 1);
        }

        return $this;
    }

    /**
     * Get slide by index
     *
     * @param  int                 $index Slide index
     * @return PHPPowerPoint_Slide
     * @throws \Exception
     */
    public function getSlide($index = 0)
    {
        if ($index > count($this->slideCollection) - 1) {
            throw new \Exception("Slide index is out of bounds.");
        } else {
            return $this->slideCollection[$index];
        }
    }

    /**
     * Get all slides
     *
     * @return PHPPowerPoint_Slide[]
     */
    public function getAllSlides()
    {
        return $this->slideCollection;
    }

    /**
     * Get index for slide
     *
     * @param  PHPPowerPoint_Slide $slide
     * @return Slide               index
     * @throws \Exception
     */
    public function getIndex(Slide $slide)
    {
        $index = null;
        foreach ($this->slideCollection as $key => $value) {
            if ($value->getHashCode() == $slide->getHashCode()) {
                $index = $key;
                break;
            }
        }
        return $index;
    }

    /**
     * Get slide count
     *
     * @return int
     */
    public function getSlideCount()
    {
        return count($this->slideCollection);
    }

    /**
     * Get active slide index
     *
     * @return int Active slide index
     */
    public function getActiveSlideIndex()
    {
        return $this->activeSlideIndex;
    }

    /**
     * Set active slide index
     *
     * @param  int                 $index Active slide index
     * @throws \Exception
     * @return PHPPowerPoint_Slide
     */
    public function setActiveSlideIndex($index = 0)
    {
        if ($index > count($this->slideCollection) - 1) {
            throw new \Exception("Active slide index is out of bounds.");
        } else {
            $this->activeSlideIndex = $index;
        }

        return $this->getActiveSlide();
    }

    /**
     * Add external slide
     *
     * @param  PHPPowerPoint_Slide $slide External slide to add
     * @throws \Exception
     * @return PHPPowerPoint_Slide
     */
    public function addExternalSlide(Slide $slide)
    {
        $slide->rebindParent($this);

        return $this->addSlide($slide);
    }

    /**
     * Get slide iterator
     *
     * @return PHPPowerPoint_Slide_Iterator
     */
    public function getSlideIterator()
    {
        return new Iterator($this);
    }

    /**
     * Copy presentation (!= clone!)
     *
     * @return PHPPowerPoint
     */
    public function copy()
    {
        $copied = clone $this;

        $slideCount = count($this->slideCollection);
        for ($i = 0; $i < $slideCount; ++$i) {
            $this->slideCollection[$i] = $this->slideCollection[$i]->copy();
            $this->slideCollection[$i]->rebindParent($this);
        }

        return $copied;
    }
}
