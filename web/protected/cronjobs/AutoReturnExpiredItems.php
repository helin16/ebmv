<?php
require_once dirname(__FILE__) . '/../../bootstrap.php';

class AutoReturnExpiredShelfItems
{
	/**
	 * Getting the logs
	 *
	 * @param string $logKey
	 * @param string $lineBreaker
	 */
	public static function showLogs($logKey = '', $lineBreaker = "\r\n")
	{
		$logKey = (($logKey = trim($logKey)) === '' ? self::getLogTransId() : $logKey);
		$where = 'transId = ?';
		$logs = Log::getAllByCriteria($where, array($logKey));
		foreach($logs as $log)
		{
			echo $log . $lineBreaker;
		}
	}
	/**
	 * Run this script
	 * 
	 * @throws Exception
	 */
	public static function run()
	{
		foreach(ProductShelfItem::getAllByCriteria('expiryTime < ?', array(trim(new UDate()))) as $shelfItem)
		{
			try
			{
				Dao::beginTransaction();
				
				$user = $shelfItem->getOwner();
				$lib = $user->getLibrary();
				ProductShelfItem::cleanUpShelfItems($user);
				ProductShelfItem::returnItem($user, $shelfItem->getProduct(), $lib);
				SupplierConnectorAbstract::getInstance($shelfItem->getProduct()->getSupplier(), $lib)->returnProduct($shelfItem->getProduct(), $user)
					->removeBookShelfList($user, $shelfItem->getProduct());
				Log::LogEntity($lib, $shelfItemId, 'ProductShelfItem', 'Auto Returned ShelfItem(ID' . $shelfItemId . ', ProductID=' . $shelfItem->getProduct()->getId(), ', OwnerID=' . $user->getId() . ')' , Log::TYPE_AUTO_EXPIRY);
				Dao::commitTransaction();
			}
			catch (Exception $ex)
			{
				Dao::rollbackTransaction();
				echo 'ERROR: ' . $ex->getMessage() . "\n";
				echo 'Trace:' . $ex->getTraceAsString() . "\n";
				continue;
			}
		}
		
		self::showLogs(Log::getTransKey());
	}
}

AutoReturnExpiredShelfItems::run();