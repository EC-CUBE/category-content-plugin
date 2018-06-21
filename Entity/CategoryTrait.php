<?php
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
     * @ORM\Column(name="plg_category_content", type="string", length=1024, nullable=true)
     */
    protected $content;

    /**
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * @param $content
     *
     * @return $this
     */
    public function setContent($content)
    {
        $this->content = $content;
        return $this;
    }
}
