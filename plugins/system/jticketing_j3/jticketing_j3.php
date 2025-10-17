<?php
/**
 * @version    SVN: <svn_id>
 * @package    JTicketing_Activities
 * @author     Techjoomla <extensions@techjoomla.com>
 * @copyright  Copyright (c) 2009-2017 TechJoomla. All rights reserved.
 * @license    GNU General Public License version 2 or later.
 */
defined('_JEXEC') or die('Restricted access');
use Joomla\CMS\Plugin\CMSPlugin;

/**
 * Jticketing Trigger plugin
 *
 * @package     Jticketing_Activities
 * @subpackage  site
 * @since       1.0
 */
class PlgSystemjticketing_J3 extends CMSPlugin
{
	/**
	 * Trigger onAfterJtEventCreate
	 *
	 * @param   Array  $data  Event Data
	 *
	 * @return  null
	 *
	 * @since   1.0
	 */
	public function onAfterJtEventCreate($data)
	{
		JT::utilities()->generateIcs($data);
	}

	/**
	 * Trigger onBeforeJtEventCreate
	 *
	 * @param   Array  $order  Event data
	 *
	 * @return  null
	 *
	 * @since   1.0
	 */
	public function onBeforeJtEventCreate($order)
	{
	}

	/**
	 * Trigger onJtBeforeTicketEmail
	 *
	 * @param   Array    $data     Data
	 * @param   Integer  $eventid  Event Id
	 *
	 * @return  null
	 *
	 * @since   1.0
	 */
	public function onAfterJtDeleteEvent($data,$eventid)
	{
		JT::utilities()->deleteIcs($data);
	}

	/**
	 * Trigger onJtBeforeTicketEmail
	 *
	 * @param   String  $toemail  email id
	 * @param   String  $subject  Email Subject
	 * @param   String  $message  Email content
	 *
	 * @return  null
	 *
	 * @since   1.0
	 */
	public function onJtBeforeTicketEmail($toemail, $subject,$message)
	{
	}

	/**
	 * Trigger onJtBeforeProcessPayment
	 *
	 * @param   Array    $post       post data
	 * @param   Integer  $order_id   Order id
	 * @param   String   $pg_plugin  Plugin Name
	 *
	 * @return  null
	 *
	 * @since   1.0
	 */
	public function onJtBeforeProcessPayment($post,$order_id,$pg_plugin)
	{
	}

	/**
	 * Trigger onJtAfterProcessPayment
	 *
	 * @param   Array    $post       post data
	 * @param   Integer  $order_id   Order id
	 * @param   String   $pg_plugin  Plugin Name
	 *
	 * @return  null
	 *
	 * @since   1.0
	 */
	public function onJtAfterProcessPayment($post,$order_id,$pg_plugin)
	{
	}

	/**
	 * Trigger jt_OnBeforeInvoiceEmail
	 *
	 * @param   String  $billemail    Billing email id
	 * @param   String  $subject      Email Subject
	 * @param   String  $invoicehtml  Invoice Html
	 *
	 * @return  null
	 *
	 * @since   1.0
	 */
	public function jt_OnBeforeInvoiceEmail($billemail,$subject,$invoicehtml)
	{
	}

	/**
	 * Trigger onAfterJtBillingsaveData
	 *
	 * @param   Array    $billingarr  Billing Data
	 * @param   Array    $postdata    EventData
	 * @param   Integer  $order_id    Order Id
	 * @param   Integer  $userid      User Id
	 *
	 * @return  null
	 *
	 * @since   1.0
	 */
	public function onAfterJtBillingsaveData($billingarr,$postdata,$order_id,$userid)
	{
	}

	/**
	 * Trigger jt_OnAfterCSVHeaderAttendee
	 *
	 * @return  null
	 *
	 * @since   1.0
	 */
	public function jt_OnAfterCSVHeaderAttendee()
	{
	}

	/**
	 * Trigger jt_OnAfterCSVBodyAttendee
	 *
	 * @param   Integer  $order_id        Order Id
	 * @param   Integer  $order_items_id  Order Items (EventId)Id
	 *
	 * @return  null
	 *
	 * @since   1.0
	 */
	public function jt_OnAfterCSVBodyAttendee($order_id,$order_items_id)
	{
	}
}
