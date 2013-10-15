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
     * @param bool     $isNew        Whether we create a new category for this
     * 
     * @return Category
     */
    public function updateCategory($categoryName, Category $parent = null, &$isNew = false)
    {
        $category = $this->findByCriteria('name = ?', trim($categoryName), true, 1, 1);
        if(count($category) > 0)
        {
            $isNew = false;
            return $category;
        }
        
        $isNew = true;
        $category = new Category();
        $category->setName($categoryName);
        return $this->moveCategory($category, $parent);
    }
    /**
     * move the category to another
     * 
     * @param Category $category The moving category
     * @param Caregory $parent   The target category
     * 
     * @return CategoryService
     */
    public function moveCategory(Category &$category, Category $parent = null)
    {
        $transStarted = false;
        try { Dao::beginTransaction();} catch (Exception $ex) {$transStarted = true; }
        try
        {
            
            if(($pos = trim($category->getPosition())) === '' || $pos === '1')
                $category = $this->save($category);
            
            if($parent instanceof Category)
            {
                $newPos = $parent->getNextPosition();
                $this->updateByCriteria('set position = CONCAT(:newPos, substring(position, :posLen)), rootId = :newRootId', 'rootId = :rootId and position like :oldPos', 
                    array(
                		'newPos' => $newPos, 
                		'oldPos' => $pos, 
                		'posLen' => strlen($pos) + 1,
                		'newRootId' => $parent->getRoot()->getId(),
                		'rootId' => $category->getRoot()->getId()
                	)
                );
            }
            else 
            {
                $this->updateByCriteria('set position = CONCAT(:newPos, substring(position, :posLen)), rootId = :newRootId', 'rootId = :rootId and position like :oldPos',
                    array(
                		'newPos' => '1', 
                		'oldPos' => $pos, 
                		'posLen' => strlen($pos) + 1,
                		'newRootId' => $category->getId(),
                		'rootId' => $category->getRoot()->getId()
                    )
                );
            }
            
            $category = $this->get($category->getId());
            $category->setPosition($parent->getNextPosition());
            $category->setParent($parent);
            $category->setRoot($parent->getRoot());
            $category = $this->save($category);
            return $category;
        }
        catch(Exception $ex)
        {
            if($transStarted === false)
                Dao::rollbackTransaction();
            throw $ex;
        }
        
    }
}
?>
