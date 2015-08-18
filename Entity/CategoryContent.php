<?php
/*
* This file is part of EC-CUBE
*
* Copyright(c) 2000-2015 LOCKON CO.,LTD. All Rights Reserved.
* http://www.lockon.co.jp/
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

namespace Plugin\CategoryContent\Entity;

use Eccube\Entity\Category;

class CategoryContent extends \Eccube\Entity\AbstractEntity
{
    private $category_id;

    private $Category;

    private $content;

    private $create_date;

    private $update_date;

    public function setCategoryId($id)
    {
        $this->category_id = $id;

        return $this;
    }

    public function getCategoryId()
    {
        return $this->category_id;
    }

    public function setCategory(Category $Category)
    {
        $this->Category = $Category;

        return $this;
    }

    public function getCategory()
    {
        return $this->Category;
    }

    public function setContent($content)
    {
        $this->content = $content;

        return $this;
    }

    public function getContent()
    {
        return $this->content;
    }

    public function setCreateDate($date)
    {
        $this->create_date = $date;

        return $this;
    }

    public function getCreateDate()
    {
        return $this->create_date;
    }

    public function setUpdateDate($date)
    {
        $this->update_date = $date;

        return $this;
    }

    public function getUpdateDate()
    {
        return $this->update_date;
    }
}
