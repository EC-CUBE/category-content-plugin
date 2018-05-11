<?php
/*
  * This file is part of the CategoryContent plugin
  *
  * Copyright (C) 2016 LOCKON CO.,LTD. All Rights Reserved.
  *
  * For the full copyright and license information, please view the LICENSE
  * file that was distributed with this source code.
  */

namespace Plugin\CategoryContent\Repository;

use Eccube\Repository\AbstractRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Plugin\CategoryContent\Entity\CategoryContent;

/**
 * Class CategoryContentRepository.
 */
class CategoryContentRepository extends AbstractRepository
{
    /**
     * CategoryContentRepository constructor.
     *
     * @param ManagerRegistry $registry
     * @param string $entityClass
     */
    public function __construct(ManagerRegistry $registry, string $entityClass = CategoryContent::class)
    {
        parent::__construct($registry, $entityClass);
    }
}
