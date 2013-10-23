<?php
/**
 * Product service
 *
 * @package    Core
 * @subpackage Service
 * @author     lhe<helin16@gmail.com>
 *
 */
class ProductService extends BaseServiceAbastract
{
    /**
     * constructor
     */
    public function __construct()
    {
        parent::__construct("Product");
    }
    /**
     * Searching any product which has that attributetype code and same attribute content
     * 
     * @param string $code             The code of the attribute type
     * @param string $attribute        The content of the attribute
     * @param bool   $searchActiveOnly Whether we return the inactive products
     * @param int    $pageNo           The page number
     * @param int    $pageSize         The page size
     * @param array  $orderBy          The order by clause
     * 
     * @return array
     */
    public function findProductWithAttrCode($code, $attribute, $searchActiveOnly = true, $pageNo = null, $pageSize = DaoQuery::DEFAUTL_PAGE_SIZE, $orderBy = array())
    {
        $query = EntityDao::getInstance('Product')->getQuery();
        $query->eagerLoad('Product.attributes', DaoQuery::DEFAULT_JOIN_TYPE, 'pa')->eagerLoad('ProductAttribute.type', DaoQuery::DEFAULT_JOIN_TYPE, 'pt');
        $where = array('pt.code = ? and pa.attribute = ?');
        $params = array($code, $attribute);
        return $this->findByCriteria(implode(' AND ', $where), $params, $searchActiveOnly, $pageNo, $pageSize, $orderBy);
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
    public function findProductsInCategory($searchText = '', $categorIds = array(), $searchOption = '', $searchActiveOnly = true, $pageNo = null, $pageSize = DaoQuery::DEFAUTL_PAGE_SIZE, $orderBy = array())
    {
        $searchMode = false;
        $where = $params = array();
        $searchOption = trim($searchOption);
        
        $query = EntityDao::getInstance('Product')->getQuery();
        if(($searchText = trim($searchText)) !== '')
        {
        	$query->eagerLoad('Product.attributes', DaoQuery::DEFAULT_JOIN_TYPE, 'pa')->eagerLoad('ProductAttribute.type', DaoQuery::DEFAULT_JOIN_TYPE, 'pt');
            if($searchOption === '')
            {
            	$criteria = '(pt.searchable = ?';
            	$params[] = 1;
            }	
            else
            {
            	$criteria = '(pt.code = ?';
            	$params[] = $searchOption;
            }		
        	$where[] = $criteria.' and pa.attribute like ?) or pro.title like ?';
            $params[] = '%' . $searchText . '%';
            $params[] = '%' . $searchText . '%';
            $searchMode = true;
        }
        /*
        else
        {
        	if($searchOption !== '')
        	{
        		 $query->eagerLoad('Product.attributes', DaoQuery::DEFAULT_JOIN_TYPE, 'pa')->eagerLoad('ProductAttribute.type', DaoQuery::DEFAULT_JOIN_TYPE, 'pt');
        		 $where[] = "(pt.name = ? And pa.attribute != '')";
        		 $params[] = $searchOption;
        		 $searchMode = true;
        	}
        }
		*/
        
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
     * @param string $isbn        The isbn
     * @param string $publisher   The publisher
     * @param string $publishDate The publish date
     * @param int    $words       The words of the book
     * @param array  $categories  The category of the book
     * @param string $image       The image path of the book
     * @param string $description The description of the book
     * 
     * @return Product
     */
    public function createProduct($title, $author, $isbn, $publisher, $publishDate, $words, array $categories, $image, $description)
    {
        return $this->_editProduct(new Product(), $title, $author, $isbn, $publisher, $publishDate, $words, $categories, $image, $description);
    }
    /**
     * update a product/book
     * 
     * @param Product $product     The Product
     * @param string  $title       The title
     * @param string  $author      The author
     * @param string  $isbn        The isbn
     * @param string  $publisher   The publisher
     * @param string  $publishDate The publish date
     * @param int     $words       The words of the book
     * @param array   $categories  The category of the book
     * @param string  $image       The image path of the book
     * @param string  $description The description of the book
     * 
     * @return Product
     */
    public function updateProduct(Product $product, $title, $author, $isbn, $publisher, $publishDate, $words, array $categories, $image, $description)
    {
        return $this->_editProduct($product, $title, $author, $isbn, $publisher, $publishDate, $words, $categories, $image, $description);
    }
    /**
     * editing a product/book
     * 
     * @param Product $product     The Product
     * @param string  $title       The title
     * @param string  $author      The author
     * @param string  $isbn        The isbn
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
        $transStarted = false;
        try { Dao::beginTransaction();} catch (Exception $ex) {$transStarted = true;}
        try
        {
            $product->setTitle($title);
            if(trim($product->getId()) === '')
                $this->save($product);
            
            //add the attributes
            //TODO:: need to resize the thumbnail
            $image_thumb = $image;
            $typeCodes = array('author', 'isbn', 'publisher', 'publish_date', 'no_of_words', 'image', 'image_thumb', 'description');
            $types = BaseServiceAbastract::getInstance('ProductAttributeType')->getTypesByCodes($typeCodes);
            foreach($typeCodes as $typeCode)
                BaseServiceAbastract::getInstance('ProductAttribute')->updateAttributeForProduct($product, (isset($types[$typeCode]) && $types[$typeCode] instanceof ProductAttributeType) ? $types[$typeCode] : null, trim($$typeCode));
            
            //add categories
            foreach($categories as $category)
            {
                if(!$category instanceof Category)
                    continue;
                $this->addCategory($product, $category);
            }
            
            $this->save($product);
            if($transStarted === false)
                Dao::commitTransaction();
            return $product;
        }
        catch(Exception $ex)
        {
            if($transStarted === false)
            Dao::rollbackTransaction();
            throw $ex;
        }
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
        return BaseServiceAbastract::getInstance('ProductAttribute')->updateAttributeForProduct($product, $type, $attribute);
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
    public function getMostPopularProducts($limit = DaoQuery::DEFAUTL_PAGE_SIZE)
    {
        
    }
    public function getNewReleasedProducts($limit = DaoQuery::DEFAUTL_PAGE_SIZE)
    {
        $query = EntityDao::getInstance('Product')->getQuery();productStatics
        $query->eagerLoad('Product.productStatics', 'left join', 'pstats')->eagerLoad('ProductStatics.type', 'left join', 'pstatstype');
        return $this->findByCriteria('pstatstype.code = ? and (pstats.value = ? or pstats.value is null)', array('no_of_clicks', 0), true, 1, $limit, array('Product.id'=>'desc''))
    }
}
?>
