<?php
/**
 * Product service
 *
 * @package    Core
 * @subpackage Service
 * @author     lhe<helin16@gmail.com>
 *
 */
class ProductService extends BaseService
{
    /**
     * constructor
     */
    public function __construct()
    {
        parent::__construct("Product");
    }
    /**
     * Searching the products in category
     * 
     * @param string $searchText       The searching text
     * @param array  $categorIds       the ids of the category
     * @param bool   $searchActiveOnly Whether we return the inactive products
     * @param int    $pageNo           The page number
     * @param int    $pageSize         The page size
     * @param array  $orderBy          The order by clause
     * 
     * @return array
     */
    public function findProductsInCategory($searchText = '', $categorIds = array(), $searchActiveOnly = true, $pageNo = null, $pageSize = DaoQuery::DEFAUTL_PAGE_SIZE, $orderBy = array())
    {
        $searchMode = false;
        $where = array();
        $params = array();
        $query = EntityDao::getInstance('Product')->getQuery();
        if(($searchText = trim($searchText)) !== '')
        {
            $query->eagerLoad('Product.attributes', DaoQuery::DEFAULT_JOIN_TYPE, 'pa')->eagerLoad('ProductAttribute.type', DaoQuery::DEFAULT_JOIN_TYPE, 'pt');
            $where[] = '(pt.searchable = 1 and pa.attribute like ?) or pro.title like ?';
            $params[] = '%' . $searchText . '%';
            $params[] = '%' . $searchText . '%';
            $searchMode = true;
        } 
        
        if(count($categorIds = array_filter($categorIds)) > 0)
        {
            $query->eagerLoad('Product.categorys');
            $where[] = '(pcat.id in (' . implode(', ', array_fill(0, count($categorIds), '?')) . '))';
            $params = array_merge($params, $categorIds);
            $searchMode = true;
        }
        
        if($searchMode === false)
            return $this->findAll($searchActiveOnly, $pageNo, $pageSize, $orderBy);
        return $this->findByCriteria(implode(' AND ', $where), $params, $searchActiveOnly, $pageNo, $pageSize, $orderBy);
    }
    /**
     * Create a product/book
     * 
     * @param string $title       The title
     * @param string $author      The author
     * @param string $publisher   The publisher
     * @param string $publishDate The publish date
     * @param int    $words       The words of the book
     * @param array  $categories  The category of the book
     * @param string $image       The image path of the book
     * @param string $description The description of the book
     * 
     * @return Product
     */
    public function createProduct($title, $author, $publisher, $publishDate, $words, array $categories, $image, $description)
    {
        return $this->_editProduct(new Product(), $title, $author, $isbn, $publisher, $publish_date, $no_of_words, $categories, $image, $description);
    }
    /**
     * update a product/book
     * 
     * @param Product $product     The Product
     * @param string  $title       The title
     * @param string  $author      The author
     * @param string  $publisher   The publisher
     * @param string  $publishDate The publish date
     * @param int     $words       The words of the book
     * @param array   $categories  The category of the book
     * @param string  $image       The image path of the book
     * @param string  $description The description of the book
     * 
     * @return Product
     */
    public function updateProduct(Product $product, $title, $author, $publisher, $publishDate, $words, array $categories, $image, $description)
    {
        return $this->_editProduct($product, $title, $author, $isbn, $publisher, $publish_date, $no_of_words, $categories, $image, $description);
    }
    /**
     * editing a product/book
     * 
     * @param Product $product     The Product
     * @param string  $title       The title
     * @param string  $author      The author
     * @param string  $publisher   The publisher
     * @param string  $publishDate The publish date
     * @param int     $words       The words of the book
     * @param array   $categories  The category of the book
     * @param string  $image       The image path of the book
     * @param string  $description The description of the book
     * 
     * @return Product
     */
    private function _editProduct(Product &$product, $title, $author, $isbn, $publisher, $publish_date, $no_of_words, array $categories, $image, $description)
    {
        $product->setTitle($title);
        if(trim($product->getId()) === '')
            $this->save($product);
        
        //add the attributes
        $typeCodes = array('author', 'isbn', 'publisher', 'publish_date', 'no_of_words', 'image', 'desciption');
        $types = BaseService::getInstance('ProductAttributeType')->getTypesByCodes($typeCodes);
        foreach($typeCodes as $typeCode)
            BaseService::getInstance('ProductAttribute')->updateAttributeForProduct($product, (isset($types[$typeCode]) && $types[$typeCode] instanceof ProductAttributeType) ? $types[$typeCode] : null, trim($$typeCode));
        
        //add categories
        foreach($categories as $category)
        {
            if(!$category instanceof Category)
                continue;
            $this->addCategory($product, $category);
        }
        return $product;
    }
    /**
     * Update the product attributes from _editProduct() function
     * 
     * @param Product              $product   The product
     * @param ProductAttributeType $type      The product type
     * @param string               $attribute The attribute content
     * 
     * @return Product
     */
    private function _updateAttribute(Product &$product, ProductAttributeType $type = null, $attribute = "")
    {
        if($type instanceof ProductAttributeType || ($attribute = trim($attribute)) === "")
            return $product;
        return BaseService::getInstance('ProductAttribute')->updateAttributeForProduct($product, $type, $attribute);
    }
    /**
     * Adding a product to a category
     * 
     * @param Product  $product  The product
     * @param Category $category The category
     * 
     * @return ProductService
     */
    public function addCategory(Product $product, Category $category)
    {
        EntityDao::getInstance('Product')->saveManyToManyJoin($category, $product);
        return $this;
    } 
    /**
     * Removing a product from a category
     * 
     * @param Product  $product  The product
     * @param Category $category The category
     * 
     * @return ProductService
     */
    public function removeCategory(Product $product, Category $category)
    {
        EntityDao::getInstance('Product')->deleteManyToManyJoin($category, $product);
        return $this;
    } 
}
?>
