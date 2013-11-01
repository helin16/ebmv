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
     * Get the product with isbn and cno
     * 
     * @param string $isbn The ISBN string
     * @param string $cno  The cno
     * 
     * @return Product|null
     */
    public function findProductWithISBNnCno($isbn, $cno)
    {
    	$query = EntityDao::getInstance('Product')->getQuery();
    	$query->eagerLoad('Product.attributes', DaoQuery::DEFAULT_JOIN_TYPE, 'pa')->eagerLoad('ProductAttribute.type', DaoQuery::DEFAULT_JOIN_TYPE, 'pt');
    	$query->eagerLoad('Product.attributes', DaoQuery::DEFAULT_JOIN_TYPE, 'pa1')->eagerLoad('ProductAttribute.type', DaoQuery::DEFAULT_JOIN_TYPE, 'pt1', 'pa1.typeId = pt1.id');
    	$where = array('pt.code = ? and pa.attribute = ? and pt1.code = ? and pa1.attribute = ?');
    	$params = array('isbn', $isbn, 'cno', $cno);
    	$results = $this->findByCriteria(implode(' AND ', $where), $params, true, 1, 1);
    	return count($results) > 0 ? $results[0] : null;
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
    public function findProductsInCategory($searchText = '', $categorIds = array(), $searchOption = '', Language $language = null, ProductType $productType = null, $searchActiveOnly = true, $pageNo = null, $pageSize = DaoQuery::DEFAUTL_PAGE_SIZE, $orderBy = array())
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
        
        if($language instanceof Language)
        {
        	$where[] = 'pro.languageId = ?';
        	$params[] = $language->getId();
        	$searchMode = true;
        }
        if($productType instanceof ProductType)
        {
        	$where[] = 'pro.productTypeId = ?';
        	$params[] = $productType->getId();
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
     * @param string      $title       The title
     * @param string      $author      The author
     * @param string      $isbn        The isbn
     * @param string      $publisher   The publisher
     * @param string      $publishDate The publish date
     * @param int         $words       The words of the book
     * @param array       $categories  The category of the book
     * @param string      $image       The image path of the book
     * @param string      $description The description of the book
     * @param string      $cno         The supplier's identification no
     * @param string      $cip         The supplier's category ip
     * @param Language    $lang        The language of the book
     * @param ProductType $type        The type of the book
     * 
     * @return Product
     */
    public function createProduct($title, $author, $isbn, $publisher, $publishDate, $words, array $categories, $image, $description, $cno = 0, $cip = '', Language $lang = null, ProductType $type = null)
    {
        return $this->_editProduct(new Product(), $title, $author, $isbn, $publisher, $publishDate, $words, $categories, $image, $description, $cno, $cip, $lang, $type);
    }
    /**
     * update a product/book
     * 
     * @param Product     $product     The Product
     * @param string      $title       The title
     * @param string      $author      The author
     * @param string      $isbn        The isbn
     * @param string      $publisher   The publisher
     * @param string      $publishDate The publish date
     * @param int         $words       The words of the book
     * @param array       $categories  The category of the book
     * @param string      $image       The image path of the book
     * @param string      $description The description of the book
     * @param string      $cno         The supplier's identification no
     * @param string      $cip         The supplier's category ip
     * @param Language    $lang        The language of the book
     * @param ProductType $type        The type of the book
     * 
     * @return Product
     */
    public function updateProduct(Product $product, $title, $author, $isbn, $publisher, $publishDate, $words, array $categories, $image, $description, $cno = 0, $cip = '', Language $lang = null, ProductType $type = null)
    {
        return $this->_editProduct($product, $title, $author, $isbn, $publisher, $publishDate, $words, $categories, $image, $description, $cno, $cip, $lang, $type);
    }
    /**
     * editing a product/book
     * 
     * @param Product     $product     The Product
     * @param string      $title       The title
     * @param string      $author      The author
     * @param string      $isbn        The isbn
     * @param string      $publisher   The publisher
     * @param string      $publishDate The publish date
     * @param int         $words       The words of the book
     * @param array       $categories  The category of the book
     * @param string      $image       The image path of the book
     * @param string      $description The description of the book
     * @param string      $cno         The supplier's identification no
     * @param string      $cip         The supplier's category ip
     * @param Language    $lang        The language of the book
     * @param ProductType $type        The type of the book
     * 
     * @return Product
     */
    private function _editProduct(Product &$product, $title, $author, $isbn, $publisher, $publish_date, $no_of_words, array $categories, $image, $description, $cno = 0, $cip = '', Language $lang = null, ProductType $type = null)
    {
        $transStarted = false;
        try { Dao::beginTransaction();} catch (Exception $ex) {$transStarted = true;}
        try
        {
            $product->setTitle($title);
            $product->setLanguage($lang instanceof Language ? $lang : EntityDao::getInstance('Language')->findById(1));
            $product->setProductType($type instanceof ProductType ? $lang : EntityDao::getInstance('ProductType')->findById(1));
            if(trim($product->getId()) === '')
                $this->save($product);
            
            //add the attributes
            //TODO:: need to resize the thumbnail
            $image_thumb = $image;
            $typeCodes = array('author', 'isbn', 'publisher', 'publish_date', 'no_of_words', 'image', 'image_thumb', 'description', 'cno', 'cip');
            $types = BaseServiceAbastract::getInstance('ProductAttributeType')->getTypesByCodes($typeCodes);
            foreach($typeCodes as $typeCode)
            {
            	if(!isset($types[$typeCode]) || !$types[$typeCode] instanceof ProductAttributeType)
            		throw new CoreException('Could find the typecode for: ' . $typeCode);
            	BaseServiceAbastract::getInstance('ProductAttribute')->updateAttributeForProduct($product, $types[$typeCode], trim($$typeCode));
            }
            
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
     * Adding a supplier to a product
     * 
     * @param Product  $product  The product 
     * @param Supplier $supplier The new supplier
     * @param string   $price    The price
     * 
     * @return ProductService
     */
    public function addSupplier(Product $product, Supplier $supplier, $price = '0.00')
    {
    	$orginal = DaoQuery::$selectActiveOnly;
    	DaoQuery::$selectActiveOnly = false;
    	//check whether the product_supplier exsits
    	$results = EntityDao::getInstance('SupplierPrice')->findByCriteria('productId = ? and supplierId = ?', array($product->getId(), $supplier->getId()), 1, 1);
    	$record = (count($results) === 0 ? new SupplierPrice() : $results[0]);
    	$record->setSupplier($supplier);
    	$record->setProduct($product);
    	$record->setPice($price);
    	EntityDao::getInstance('SupplierPrice')->save($record);
    	DaoQuery::$selectActiveOnly = $orginal;
    	return $this;
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
        $query = EntityDao::getInstance('Product')->getQuery();
        $query->eagerLoad('Product.productStatics', 'left join', 'pstats')->eagerLoad('ProductStatics.type', 'left join', 'pstatstype');
        $results = $this->findByCriteria('pstatstype.code = ? or pstatstype.code is null', array('no_of_clicks'), true, 1, $limit, array('pstats.value'=>'desc'));
        return $results;
    }
    public function getNewReleasedProducts($limit = DaoQuery::DEFAUTL_PAGE_SIZE)
    {
        $query = EntityDao::getInstance('Product')->getQuery();
        $query->eagerLoad('Product.productStatics', 'left join', 'pstats')->eagerLoad('ProductStatics.type', 'left join', 'pstatstype');
        $results = $this->findByCriteria('pstats.value is null or (pstatstype.code = ? and pstats.value = ?)', array(0, 'no_of_clicks'), true, 1, $limit, array('pro.id'=>'desc'));
        return $results;
    }
}
?>
