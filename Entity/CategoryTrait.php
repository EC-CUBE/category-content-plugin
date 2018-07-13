<?php

/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Plugin\CategoryContent\Entity;

use Eccube\Annotation as Eccube;

/**
 * @Eccube\EntityExtension("Eccube\Entity\Category")
 */
trait CategoryTrait
{
    /**
     * @var string
     *
     * @ORM\Column(name="plg_category_content_content", type="string", length=1024, nullable=true)
     */
    protected $category_content_content;

    /**
     * @return string
     */
    public function getCategoryContentContent()
    {
        return $this->category_content_content;
    }

    /**
     * @param $categoryContentContent
     *
     * @return $this
     */
    public function setCategoryContentContent($categoryContentContent)
    {
        $this->category_content_content = $categoryContentContent;

        return $this;
    }
}
