<?php
class ProductImportScripts
{
    private $_tmpFileFolder;
    private $_tmpFile;
    
    public function __constructor($tmpFileFolder)
    {
        $this->_tmpFileFolder = $tmpFileFolder;
        $this->_tmpFile = $this->_tmpFileFolder . "importFile.import";
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
        return this;
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
        $options = array(
            CURLOPT_FILE    => $localFile,
            //            CURLOPT_TIMEOUT =>  ini_get($varname), // set this to 8 hours so we dont timeout on big files
            CURLOPT_URL     => $url
        );
        $ch = curl_init();
        curl_setopt_array($ch, $options);
        curl_exec($ch);
        return $localFile;
    }
    /**
     * Parsing the downloaded file
     * 
     * @param string $filePath The path of the downloaded file
     * 
     * @throws CoreException
     * @return ProductImportScripts
     */
    public function parseXmltoProduct($filePath)
    {
        //l
        try 
        {
            $xml = new simplexml_load_file($filePath);
        } 
        catch(Exception $ex)
        {
            throw new CoreException("Error when parsing the downloaded file: " . $filePath);
        }
        
        $result = $xml->xpath('Book');
        while(list( , $node) = each($result)) {
            $this->importProduct($node);
        }
        return $this;
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
        $categories = (count($categories) > 0 ? $categories : $this->importCategories($xml));
        return BaseServiceAbastract::getInstance('Product')->createProduct(
            $this->_getAttribute($xml, 'BookName'),
            $this->_getAttribute($xml, 'Author'),
            $this->_getAttribute($xml, 'Press'),
            $this->_getAttribute($xml, 'PublicationDate'),
            $this->_getAttribute($xml, 'Words'),
            $categories,
            $this->importImage($this->_getAttribute($xml, 'FrontCover')),
            $this->_getAttribute($xml, 'Introduction')
        );
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
        $categoryNames = explode('/', $this->_getAttribute($xml, 'BookType'));
        $cateogories = array();
        foreach($categoryNames as $index => $name)
        {
            $cateogories[$index] = BaseServiceAbastract::getInstance('Category')->updateCategory($name, (isset($cateogories[$index]) && $cateogories[$index] instanceof Category) ? $cateogories[$index] : null);
        }
        return array_filter($cateogories);
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
        if(($imageUrl = trim($imageUrl)) === '')
            return '';
        $paths = parse_url($imageUrl);
        $paths = explode('/', $paths['path']);
        $tmpFile = $this->downloadFile($imageUrl, $this->_tmpFileFolder . DIRECTORY_SEPARATOR . md5($imageUrl));
        return BaseServiceAbastract::getInstance('Asset')->registerAsset(end($paths), file_get_contents($tmpFile));
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