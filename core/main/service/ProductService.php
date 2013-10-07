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
}
?>
