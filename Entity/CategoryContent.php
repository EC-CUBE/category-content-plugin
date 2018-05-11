<?php
/*
  * This file is part of the CategoryContent plugin
  *
  * Copyright (C) 2016 LOCKON CO.,LTD. All Rights Reserved.
  *
  * For the full copyright and license information, please view the LICENSE
  * file that was distributed with this source code.
  */

namespace Plugin\CategoryContent\Entity;

use Eccube\Entity\AbstractEntity;
use Doctrine\ORM\Mapping as ORM;

/**
 * Class CategoryContent.
 *
 * @ORM\Table(name="plg_category_content")
 * @ORM\Entity(repositoryClass="Plugin\CategoryContent\Repository\CategoryContentRepository")
 */
class CategoryContent extends AbstractEntity
{
    /**
     * @var integer
     *
     * @ORM\Column(name="category_id", type="integer", options={"unsigned":false})
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="content", type="string", nullable=true)
     */
    private $content;

    /**
     * getId.
     *
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * setId.
     *
     * @param int $id
     *
     * @return $this
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * getContent.
     *
     * @return mixed
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * setContent.
     *
     * @param string $content
     *
     * @return $this
     */
    public function setContent($content)
    {
        $this->content = $content;

        return $this;
    }
}
