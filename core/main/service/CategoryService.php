<?php
/**
 * Category service
 *
 * @package    Core
 * @subpackage Service
 * @author     lhe<helin16@gmail.com>
 *
 */
class CategoryService extends BaseServiceAbastract
{
    /**
     * constructor
     */
    public function __construct()
    {
        parent::__construct("Category");
    }
    /**
     * Find or create a category with the same name
     * 
     * @param string   $categoryName The name of the category
     * @param Category $parent       The parent category
     * 
     * @return Category
     */
    public function updateCategory($categoryName, Category $parent = null)
    {
        $category = $this->findByCriteria('name = ?', trim($categoryName), true, 1, 1);
        if(count($category) > 0)
            return $category;
        
        $category = new Category();
        $category->setName($categoryName);
        $category->setParent($parent);
        return $this->save($category);
    }
}
?>
