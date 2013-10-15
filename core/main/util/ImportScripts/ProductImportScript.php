<?php
class ProductImportScript
{
    private $_tmpFileFolder;
    private $_tmpFile;
    /**
     * constructor
     * 
     * @param string $tmpFileFolder The tmp folder to hold the downloaded file
     * @param string $tmpFile       The tmp file
     */
    public function __construct($tmpFileFolder, $tmpFile = '')
    {
        $this->_tmpFileFolder = $tmpFileFolder;
        $this->_tmpFile = (trim($tmpFile) === '' ? $this->_tmpFileFolder . DIRECTORY_SEPARATOR . md5(Core::getUser() . new UDate()). ".import" : trim($tmpFile));
    }
    /**
     * Download the file
     * 
     * @param unknown_type $url
     * 
     * @return ProductImportScripts
     */
    public function getDataFromUrl($url, $overWriteExsiting = false)
    {
        if(file_exists($this->_tmpFile))
        {
            if($overWriteExsiting !== true)
                throw new CoreException('file: ' . $this->_tmpFile . ' exsits!');
            unlink($this->_tmpFile);
        }
        $this->downloadFile($url, $this->_tmpFile);
        return $this;
    }
    /**
     * Getting the tmp file's parth
     * 
     * @return string
     */
    public function getTmpFile()
    {
        return $this->_tmpFile;
    }
    /**
     * download the url to a local file
     * 
     * @param string $url       The url
     * @param string $localFile The local file path
     * 
     * @return string The local file path
     */
    public function downloadFile($url, $localFile)
    {
        $fp = fopen($localFile, 'w+');
        $options = array(
            CURLOPT_FILE    => $fp,
            CURLOPT_TIMEOUT =>  20, // set this to 8 hours so we dont timeout on big files
            CURLOPT_URL     => $url
        );
        $ch = curl_init();
        curl_setopt_array($ch, $options);
        curl_exec($ch);
        fclose($fp);
        return $localFile;
    }
    /**
     * removing the tmp file
     * 
     * @param string $tmpFile The path of the tmp file
     * 
     * @return ProductImportScript
     */
    public function removeTmpFile($tmpFile = null)
    {
        $file = (trim($tmpFile) === '' ? $this->_tmpFile : $tmpFile);
        if(file_exists($file))
            unlink($file);
        return $this;
    }
    /**
     * Parsing the downloaded file
     * 
     * @param string $filePath The path of the downloaded file
     * @param int    $index    Which product of the file to import
     * 
     * @throws CoreException
     * @return array
     */
    public function parseXmltoProduct($filePath, $index = null)
    {
        $transStarted = false;
        try { Dao::beginTransaction();} catch (Exception $ex) {$transStarted = true; }
        try
        {
            try{ $xml = simplexml_load_file($filePath); } catch(Exception $ex) {
                throw new CoreException("Error when parsing the downloaded file: " . $filePath);
            }
            
            $products = array();
            $result = $xml->xpath("//Books/Book");
            if(trim($index) === '')
            {
                foreach($result as $node) {
                   $products[] = $this->importProduct($child);
                }
            }
            else
            {
                $products[] = $this->importProduct($result[$index]);
            }
            if($transStarted === false)
                Dao::commitTransaction();
            return $products;
        }
        catch(Exception $ex)
        {
            if($transStarted === false)
                Dao::rollbackTransaction();
            throw $ex;
        }
    }
    /**
     * Importing the product
     * 
     * @param SimpleXMLElement $xml The product xml
     * 
     * @param array $categories
     */
    public function importProduct(SimpleXMLElement $xml, array $categories = array())
    {
        $transStarted = false;
        try { Dao::beginTransaction();} catch (Exception $ex) {$transStarted = true; }
        try
        {
            if(($isbn = $this->_getAttribute($xml, 'Isbn')) === '')
                throw new Exception('No ISBN provided!');
            
            $categories = (count($categories) > 0 ? $categories : $this->importCategories($xml));
            $products = BaseServiceAbastract::getInstance('Product')->findProductWithAttrCode('isbn', $isbn, true, 1, 1);
            //updating the product
            if(count($products) > 0)
            {
                $product = BaseServiceAbastract::getInstance('Product')->updateProduct($products[0],
                    $this->_getAttribute($xml, 'BookName'),
                    $this->_getAttribute($xml, 'Author'),
                    $this->_getAttribute($xml, 'Isbn'),
                    $this->_getAttribute($xml, 'Press'),
                    $this->_getAttribute($xml, 'PublicationDate'),
                    $this->_getAttribute($xml, 'Words'),
                    $categories,
                    $this->importImage($this->_getAttribute($xml, 'FrontCover')),
                    $this->_getAttribute($xml, 'Introduction')
                );
            }
            //creating new product
            else
            {
                $product = BaseServiceAbastract::getInstance('Product')->createProduct(
                    $this->_getAttribute($xml, 'BookName'),
                    $this->_getAttribute($xml, 'Author'),
                    $this->_getAttribute($xml, 'Isbn'),
                    $this->_getAttribute($xml, 'Press'),
                    $this->_getAttribute($xml, 'PublicationDate'),
                    $this->_getAttribute($xml, 'Words'),
                    $categories,
                    $this->importImage($this->_getAttribute($xml, 'FrontCover')),
                    $this->_getAttribute($xml, 'Introduction')
                );
            }
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
     * Importing the categories
     * 
     * @param SimpleXMLElement $xml The category xml
     * 
     * @return array
     */
    public function importCategories(SimpleXMLElement $xml)
    {
        $transStarted = false;
        try { Dao::beginTransaction();} catch (Exception $ex) {$transStarted = true; }
        try
        {
            $categoryNames = explode('/', $this->_getAttribute($xml, 'BookType'));
            $cateogories = array();
            foreach($categoryNames as $index => $name)
            {
                $cateogories[$index] = BaseServiceAbastract::getInstance('Category')->updateCategory($name, (isset($cateogories[$index - 1]) && $cateogories[$index - 1] instanceof Category) ? $cateogories[$index - 1] : null);
            }
            if($transStarted === false)
                Dao::commitTransaction();
            return array_filter($cateogories);
        }
        catch(Exception $ex)
        {
            if($transStarted === false)
            Dao::rollbackTransaction();
            throw $ex;
        }
    }
    /**
     * importing the image file
     * 
     * @param string $imageUrl The url of the image
     * 
     * @return string the asssetid
     */
    public function importImage($imageUrl)
    {
        $transStarted = false;
        try { Dao::beginTransaction();
        } catch (Exception $ex) {
            $transStarted = true;
        }
        try
        {
            if(($imageUrl = trim($imageUrl)) === '')
                return '';
            $paths = parse_url($imageUrl);
            $paths = explode('/', $paths['path']);
            $tmpFile = $this->downloadFile($imageUrl, $this->_tmpFileFolder . DIRECTORY_SEPARATOR . md5($imageUrl));
            $assetId = BaseServiceAbastract::getInstance('Asset')->registerAsset(end($paths), file_get_contents($tmpFile));
            if($transStarted === false)
                Dao::commitTransaction();
            return $assetId;
        }
        catch(Exception $ex)
        {
            if($transStarted === false)
            Dao::rollbackTransaction();
            throw $ex;
        }
    }
    /**
     * Getting the value of the attribute
     * 
     * @param SimpleXMLElement $xml           The xml element
     * @param string           $attributeName The attr name
     * 
     * @return string
     */
    private function _getAttribute(SimpleXMLElement $xml, $attributeName)
    {
        return (isset($xml->$attributeName) && ($attribute = trim($xml->$attributeName)) !== '') ? $attribute : '';
    }
}