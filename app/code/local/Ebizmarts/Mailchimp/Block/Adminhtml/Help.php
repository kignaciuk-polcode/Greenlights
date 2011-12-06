<?php

class Ebizmarts_Mailchimp_Block_Adminhtml_Help extends Mage_Adminhtml_Block_Widget_View_Container{

	public function __construct(){

    	$this->_controller = 'adminhtml_help';
        $this->_blockGroup = 'mailchimp';
        $this->_headerText = Mage::helper('mailchimp')->__('MailChimp - HELP');
		parent::__construct();
		$this->_removeButton('edit');
		$this->_removeButton('back');
    }

	protected function _prepareLayout(){

      	if(!$this->getRequest()->isXmlHttpRequest()){
      		$this->getLayout()->getBlock('head')->addItem('skin_js', 'mailchimp/MailChimp.js');
      		$this->getLayout()->getBlock('head')->addItem('skin_css', 'mailchimp/MailChimp.css');
      	}
    }

	public function getViewHtml(){

		$menu = '<div class="left-menu">
					<h3>HELP CONTENTS</h3>
					<ul>
						<li onclick="showMe(\'install\',this);">Module Installation</li>
						<li onclick="showMe(\'setup\',this);">Module Setup</li>
						<li onclick="showMe(\'webhooks\',this);">Webhooks</li>
						<li onclick="showMe(\'bulk\',this);">Bulk Synchronization</li>
						<li onclick="showMe(\'ecomm360\',this);">Ecommerce 360</li>
						<li onclick="showMe(\'sts\',this);">STS</li>
						<li onclick="showMe(\'ctemplates\',this);">Campaign Templates</li>
						<li onclick="showMe(\'uninstall\',this);">How to uninstall</li>
						<li onclick="showMe(\'tips\',this);">General Tips and FAQs</li>
					</ul>
				</div>';
		$landing = '<div class="section" id="landing">
						<p><div class="preebizlogo">Welcome to the</div><div class="ebizlogo">Ebizmarts MailChimp</div><div class="postebizlogo">HELP guide.</div>
						<div class="info"><a href="http://twitter.com/#!/ebizmarts" target="_blank">Follow us</a> to be aware of all the updates and new modules that <a href="http://ebizmarts.com" target="_blank">Ebizmarts</a> does for you.</div><div class="version">version 0.2</div></p>
					</div>';
		$install = '<div class="section" id="install" style="display:none;">
						<h1>Module Installation</h1>
						<h2>Compatibility:</h2>
						<p>All the module basics and the most of its features are compatible with Magento CE 1.3 and EE 1.8, and is full compatible with Magento EE (1.9 and 1.10), PRO (1.9 and 1.10) and CE (1.4, 1.5 and 1.6).</p>
						<h2>Beware:</h2>
						<p>If you have Ebizmarts MailChimp v1.4.3 or below, or Ebizmarts MailChimp Sync Pro v2.0.0 or higher you must do the following:<br/>
							<ul>
								<li>AFTER install the module, you must remove the app/etc/modules/Ebizmarts_MailchimpPro.xml file in order to avoid possible errors.</li>
								<li>Once the module is updated you must re save the module settings.</li>
								<li>The Ebizmarts MailChimp Synchronized subscribers list will be blanked, so you should run the Ebizmarts MailChimp Bulk action in order to increase the module performance.</li>
							</ul>
						</p>
						<h2>Manual Installation:</h2>
						<h3>Backend files:</h3>
						<p>If you desire to install the Ebizmarts MailChimp module manually, first you must download it, <a target="_blank" href=\'http://connect.magentocommerce.com/community/get/Ebizmarts_MailChimp-3.1.2.tar\'>here</a> you will find the latest 3.1.2 version.<br/>
						Once uncompressed the package you must copy its content to your Magento installation as follows:<br/>
						folder /Ebizmarts_MailChimp-3.1.2/adminhtml/default/default/layout/ <b>to</b> /app/design/adminhtml/default/default/<br/>
						folder /Ebizmarts_MailChimp-3.1.2/adminhtml/default/default/template/ <b>to</b> /app/design/adminhtml/default/default/<br/>
						folder /Ebizmarts_MailChimp-3.1.2/adminhtml/default/default/mailchimp/ <b>to</b> /skin/adminhtml/default/default/<br/>
						folder /Ebizmarts_MailChimp-3.1.2/Ebizmarts/ <b>to</b> /app/code/local/<br/>
						folder /Ebizmarts_MailChimp-3.1.2/frontend/base/default/layout/ <b>to</b> /app/design/frontend/base/default/<br/>
						folder /Ebizmarts_MailChimp-3.1.2/frontend/base/default/template/ <b>to</b> /app/design/frontend/base/default/<br/>
						folder /Ebizmarts_MailChimp-3.1.2/frontend/base/default/mailchimp/ <b>to</b> /skin/frontend/base/default/<br/>
						file   /Ebizmarts_MailChimp-3.1.2/modules/Ebizmarts_Mailchimp.xml <b>to</b> /app/etc/modules/</p>
						<h3>Frontend files:</h3>
						<p>Once you have the module files installed on your store, you must edit some of the frontend templates:<br/>
							<ul>
								<li>To show the additionals subscribed lists on Customer Dashboard <b>[optional]</b>:<br/>
								/app/design/frontend/your/theme/template/customer/account/dashboard/info.phtml</li>
								<li>To keep synchronized the customer subscriptions on the newsletters page <b>[mandatory]</b>:<br/>
								/app/design/frontend/your/theme/template/customer/form/newsletter.phtml</li>
								<li>To keep synchronized the customer subscriptions on the register page <b>[mandatory]</b>:<br/>
								/app/design/frontend/your/theme/template/customer/form/register.phtml</li>
								<li>To add a subscription box on the cart page <b>[optional]</b>:<br/>
								/app/design/frontend/your/theme/template/checkout/cart.phtml</li>
								<li>To add a subscription box on the chekcout billing page (this only applies to default Magento Checkout) <b>[optional]</b>:<br/>
								/app/design/frontend/your/theme/template/checkout/onepage/billing.phtml</li>
							</ul>
							<br/>Basically you must add on those a Magento code line as seen on this <a target="_blank" href=\'http://www.magentocommerce.com/boards/viewthread/226883/\'>post</a>.
						</p>
					</div>';
		$setup = '<div class="section" id="setup" name="setup" style="display:none;">
					<h1>Module Setup</h1>
					<h2>Settings location:</h2>
					<p>The Ebizmarts MailChimp module has been developed to be easily seteable, all its settings can be found on the Magento Admin Panel --> System --> Configuration --> Customers --> Ebizmarts MailChimp.</p>
					<h2>Settings options:</h2>
					<h3>General tab:</h3>
					<h4>Enabled:</h4>
					<p>This option will enable/disable <b>all</b> the module actions.</p>
					<h4>API Key:</h4>
					<p>Here you must set your MailChimp API key, this is provided by logging in at your MailChimp account (you must save the settings with this key firstly in order to get the module working, this field is <u>mandatory</u>).<p>
					<h4>General Subscription:</h4>
					<p>You must select your main MailChimp list, this will be the list where all the subscribers will go when they subscribe on any subscription box, the Magento general newsletter will be synchronized with this.</p>
					<h4>Additional Lists:</h4>
					<p>All the selected lists on this multi-select input will be available to customers as additionals lists on the Customer Newsletter page.</p>
					<h4>Enable Interest Groups:</h4>
					<p>Allow customers to select over the existing <a target="_blank" href=\'http://kb.mailchimp.com/article/what-is-a-group-and-why-would-i-want-to-set-one-up/\'>groups</a> per each enabled list.</p>
					<h4>Enable Ecommerce360 feature:</h4>
					<p>If you enable this option then the module will send sales info to MailChimp on last checkout step.</p>
					<h4>Enable STS feature:</h4>
					<p>This will send all the store emails using the Amazon Simple Email Service through the MailChimp STS API.</p>
					<h3>MailChimp Subscribe Options tab:</h3>
					<h4>Force Checkout:</h4>
					<p>This will do the subscription mandatory and not visible for guests and new customers at checkout page.</p>
					<h4>Force Register:</h4>
					<p>This will do the subscription mandatory and not visible for guests and new customers at register page.</p>
					<h4>Email Type:</h4>
					<p>Email type preference for the email.</p>
					<h4>Double Optin:</h4>
					<p>This controls whether a double opt-in confirmation message is sent. <b>ABUSING THIS MAY CAUSE YOUR ACCOUNT TO BE SUSPENDED</b>. Magento note: If user subscribes own login email then the confirmation is not needed. </p>
					<h4>Update Existing:</h4>
					<p>This controls whether an existing subscribers should be updated instead of throwing and error.</p>
					<h4>Replace Interests:</h4>
					<p>This determines whether we replace the interest groups with the groups provided, or we add the provided groups to the member\'s interest groups.</p>
					<h4>Send Welcome:</h4>
					<p>The Ebizmarts MailChimp module allows you to change the Magento Success email by the MailChimp Welcome email, in order to do this you must set this to \'Yes\', but if the \'Double opt-in\' option is also set to \'Yes\' then this Welcome email won\'t be sent. </p>
					<h4>Disable Success/Welcome emails:</h4>
					<p>If you enable this setting then no success or welcome emails will be sent to the subscriber.</p>
					<h4>General Customer Mapping:</h4>
					<p>You can map the customer fields that you desire to send to MailChimp with the following format: &lt customer=\'attribute_code\' mailchimp=\'merge_tag\' &gt<br/>
						Where customer means the Magento customer attribute code, and mailchimp the created merge field.<br/>
						The email is not necessary to map due to this is mandatory so it is already included on the code.<br/>
						Below you will find some examples:<br/>
						<p>
							&lt;customer=\'firstname\' mailchimp=\'FNAME\'&gt;<br/>
							&lt;customer=\'lastname\' mailchimp=\'LNAME\'&gt;<br/>
							&lt;customer=\'dob\' mailchimp=\'DOB\'&gt;<br/>
							&lt;customer=\'prefix\' mailchimp=\'PRENAME\'&gt;<br/>
							&lt;customer=\'address\' mailchimp=\'ADDRESS\'&gt;<br/>
							&lt;customer=\'gender\' mailchimp=\'GENDER\'&gt;<br/>
							&lt;customer=\'store_id\' mailchimp=\'STOREID\'&gt;<br/>
							&lt;customer=\'website_id\' mailchimp=\'WEBSITE\'&gt;<br/>
							&lt;customer=\'date_of_purchase\' mailchimp=\'DOP\'&gt;<br/>
						</p>
						<b>BEWARE</b>:
						<ul>
							<li>the MERGE fields are NOT equal to GROUPS. For this reason you can not use this mapping to force the subscriber to register into any interest group.</li>
							<li>If you want to send the whole customer address you must create the MailChimp merge field as \'ADDRESS\' type and set the customer attribute code as \'address\' too (the current module version only allows you to send the customer country with the address details and not as a single field, otherwise you should have the customer country as a customer attribute and map it alone).</li>
							<li>At MailChimp, do not use the \'BIRTHDAY\' type as the customer date of birth, instead of that the MailChimp merge field should be \'DATE\' type.</li>
						</ul>
					</p>
					<h4>Default name for guests subscribers:</h4>
					<p>When you create a new list, you have a default merge field \'name\', this will set the default data to be sent when the subscriber doesn\' have a name.</p>
					<h4>Default lastname for guests subscribers:</h4>
					<p>When you create a new list, you have a default merge field \'lastname\', this will set the default data to be sent when the subscriber doesn\' have a name.</p>
					<h3>MailChimp Unsubscribe Options tab:</h3>
					<h4>Delete Member:</h4>
					<p>If you set this to \'Yes\' then MailChimp will completely delete the member from your list instead of just unsubscribing.</p>
					<h4>Send Goodbye:</h4>
					<p>If you want to send the MailChimp goodbye email to the email address, set this to \'Yes\'.</p>
					<h4>Send Notify:</h4>
					<p>This will send the MailChimp unsubscribe notification email to the address defined in the MailChimp list email notification settings.</p>
				</div>';
		$webhooks = '<div class="section" id="webhooks" name="webhooks" style="display:none;">
						<h1>Webhooks:</h1>
						<h2>Webhooks location:</h2>
						<p>The Ebizmarts MailChimp module has incorporated the <a target="_blank" href=\'http://blog.mailchimp.com/webhooks-and-easier-syncing-with-mailchimp/\'>MailChimp WebHoooks</a>. To setup those WebHooks you must go to Magento Admin Panel --> Newsletter --> MailChimp --> WebHoooks.</p>
						<h2>Setting up the Webhooks:</h2>
						<p>Basically you only must check all the boxes that you are interested in and finally just click the \'WebHooks Synchronization\' button.<br/>
							The ones which starts with <b>"Actions:"</b> means on which events the WebHooks should run.<br/>
							The ones which starts with <b>"Source:"</b> switchs the event sources.<br/>
						</p>
						<h3>An easy example:</h3>
						<p>Let\'s suppose that you are only interested in receive information related to unsubscriptions from MailChimp, then you should check the following boxes:<br/>
							<ul>
								<li>Actions: Unsubscribe</li>
								<li>Source: User</li>
								<li>Source: Admin</li>
							</ul>
						</p>
						<h2>Last tips:</h2>
						<p>
							<ul>
								<li>We recommend to check all the boxes but the ones which reads "Source:API" to avoid possible conflicts with other modules.</li>
								<li>Remember that if your site is pass protected the MailChimp WebHooks won’t work.</li>
							</ul>
						</p>
						<h2>Beware:</h2>
						<p>
							<ul>
								<li>If you are working with a Magento multi-store I\'m afraid the MailChimp WebHooks can\'t be set to work per website, since MailChimp doesn\'t send info related to the subscriber website, for this reason all info processed by the Ebizmarts MailChimp WebHooks will be registered to the same Website, generally this will be the main one.</li>
							</ul>
						</p>
					</div>';
		$bulk = '<div class="section" id="bulk" name="bulk" style="display:none;">
					<h1>Bulk Synchronization:</h1>
					<h2>Bulk Synchronization location:</h2>
					<p>The Ebizmarts MailChimp module allows you to import and export several subscribers at once using a feed file. This feature can be found on the Magento Admin Panel --> Newsletter --> MailChimp --> Bulk Synchronization.</p>
					<h2>Why I should use this feature?:</h2>
					<p>We recommend you to run the Bulk Synchronization on both import and export ways per store as soon as the module is installed, this will improve the module speed.<br/>Those actions should be run only once because as soon as the module is correctly installed and set there is no more need to worry about the new subscribers and its updates.</p>
					<h2>How this works?:</h2>
					<p>
						<ul>
							<li>The Ebizmarts MailChimp Bulk Synchronization will handle two feeds (an import and an export one) per list per store, whose you are be able to run all the times you need to. </li>
							<li>When you run an export action, the module will pick all the subscribers from the Magento general list from the store selected and will send those to the preselected MailChimp list.</li>
							<li>On the import action, the module will generate a file with the subscribers from the preselected list and will register those locally, if the selected list matches with the one set to be the main list on the module settings at the pre-selected store then all the new subscribers will be registered on the Magento newsletter (on both cases all new subscribers will be added to the MailChimp Synchronized Subscribers list).</li>
						</ul>
					</p>
					<h2>Last tips:</h2>
					<p>
						<ul>
							<liYou can edit the generated feed file before run it (only if you have access to the /var/mailchimp folder).</li>
							<li>The text delimiter character is the single quote (\') and the text separator is the tilde (~).</li>
						</ul>
					</p>
					<h2>Beware:</h2>
					<p>
						<ul>
							<li>If you had not selected the MailChimp list to be synchronized with your Magento newsletter list, then the IMPORT action won\'t work</li>
							<li>the current module version doesn\'t handle the cron to regenerate or run those feeds automatically</li>
						</ul>
					</p>
				</div>';
		$ecomm360 = '<div class="section" id="ecomm360" name="ecomm360" style="display:none;">
						<h1>Ecommerce 360:</h1>
						<h2>Ecommerce 360 location:</h2>
						<p>The Ebizmarts MailChimp module has added the MailChimp Ecommerce 360 feature, and you are able to check all the orders sent to MailChimp on the Magento Admin Panel by going to Newsletter --> MailChimp --> Ecommerce360.</p>
						<h2>How this works?:</h2>
						<p>
							<ul>
								<li>Firstly you must know that this feature will only work if you have enabled the <a target="_blank" href=\'http://kb.mailchimp.com/article/how-do-i-turn-on-ecommerce360-tracking-on-my-campaigns/\'>Ecommerce360</a> on your launched campaign.</li>
								<li>When a customer is redirected to your store by a link on the email sent by your campaign, the Ebizmarts MailChimp module will generate two different cookies with the MailChimp and Campaign IDs.</li>
								<li>Now, each time the customer place an order successfully the Ebizmarts MailChimp will send the order info to your MailChimp statics.</li>
								<li>Please consider that the current Ecommerce 360 feature will only use the <a target="_blank" href=\'http://apidocs.mailchimp.com/api/1.3/campaignecommorderadd.func.php\'>campaignEcommOrderAdd</a> API function which doesn’t allow to send customer data to MailChimp.</li>
							</ul>
						</p>
					</div>';
		$ctemplates = '<div class="section" id="ctemplates" name="ctemplates" style="display:none;">
							<h1>Campaign Templates:</h1>
							<h2>Campaign Templates location:</h2>
							<p>The Ebizmarts MailChimp module has added the possibility to edit and add new Campaign Templates. This feature can be found at Magento Admin Panel --> Newsletter --> MailChimp --> Campaign Templates.</p>
							<h2>How this works?:</h2>
							<p>
								<ul>
									<li>This feature has been thought to allow you to create campaign templates with your store contents, ie: to automatically get your "new" products or your store shipping conditions.</li>
									<li>Due to the MailChimp templates works with <a href="http://kb.mailchimp.com/article/getting-started-with-mailchimps-template-language" target="_blank">sections</a>, the Ebizmarts MailChimp module will allow you to edit those sections independently, keeping on this way your current template styles.</li>
								</ul>
							</p>
							<h2>Beware:</h2>
							<p>
								<ul>
									<li>This feature is really new, so it is still on beta phase.</li>
									<li>You only can deactivate and edit "user" type templates.</li>
									<li>The emails cannot use stylesheets files (.css), so all the html styles must be inlined or defined on the &lt;style&gt; tag.</li>
									<li>Due to the large amount of defined styles on the Magento stylesheet, we recommend to <b>not</b> work the styles on this page.</li>
									<li>In order to use the "Get content from CMS Page" feature, the selected CMS page must be enabled.</li>
								</ul>
							</p>
						</div>';
		$sts = '<div class="section" id="sts" name="sts" style="display:none;">
					<h1>Simple Transactional Service (STS):</h1>
					<h2>STS location:</h2>
					<p>The Ebizmarts MailChimp module has added the MailChimp Simple Transactional Service (STS) feature which allows you to send all your store emails from MailChimp instead of use your server. To handle the MailChimp registered emails you must go to Magento Admin Panel --> Newsletter --> MailChimp --> STS.</p>
					<h2>How this works?:</h2>
					<p>
						<ul>
							<li>In order to get the Ebizmarts MailChimp STS working, firstly you must have an <a target="_blank" href=\'http://aws.amazon.com/ses/\'>Amazon account</a>, then you must <a target="_blank" href=\'http://kb.mailchimp.com/article/how-does-mailchimp-integrate-with-amazon-ses\'>link</a> it to your MailChimp account.</li>
							<li>Each time you add a new email, you\'ll receive a confirmation email to the added account.</li>
							<li>To easily test a confirmed email, just click the \'Send Test Email\' link.</li>
						</ul>
					</p>
					<h2>Beware:</h2>
					<p>
						<ul>
							<li>In order to add this feature, the Ebizmarts MailChimp module had to rewrite the Mage_Core_Model_Email_Template class so if you have other module that also rewrites this same file it is very probably that you have a module conflict, if you won\'t use the STS feature you can fix this issue by editing the /app/code/local/Ebizmarts/Mailchimp/etc/config.xml file and comment the following section:<br/>
						&lt;core&gt;<br/>&lt;rewrite&gt;<br/>&lt;email_template&gt;Ebizmarts_Mailchimp_Model_Email_Template&lt;/email_template&gt;<br/>&lt;/rewrite&gt;<br/>&lt;/core&gt;</li>
						</ul>
					</p>
				</div>';
		$uninstall = '<div class="section" id="uninstall" name="uninstall" style="display:none;">
						<h1>Module Uninstall:</h1>
						<h2>Module files:</h2>
						<p>
							<ul>
								<li>If you installed the module via Magento Connect, then you\'ll be able to uninstall it using this same tool.</li>
								<li>If you installed the module manually, then you must delete all module files including frontend and backend templates, css, js files <b>and</b> the etc module file located at: /app/etc/modules/Ebizmarts_Mailchimp.xml.</li>
							</ul>
						</p>
						<h2>Module info located on the Magento database:</h2>
						<p>
							Despite you use the Magento Connet to uninstall the module, all the info located on the DataBase won\'t be removed, so in order to full uninstall the Ebizmarts MailChimp you should execute the following SQL queries:<br/>
							<ul>
								<li>DELETE FROM \'core_resource\' WHERE \'core_resource\'.\'code\' = \'Mailchimp_setup\';</li>
								<li>DELETE FROM \'core_config_data\' WHERE \'path\' LIKE \'%mailchimp%\';</li>
								<li>DROP TABLE \'ebizmarts_ecomm360\';</li>
								<li>DROP TABLE \'ebizmarts_mailchimppro\';</li>
							</ul>
						</p>
						<h2>Beware:</h2>
						<p>
							<ul>
								<li>Once removed the module you <b>must</b> refresh all your store caches and recompile your Magento (only if you have this enabled).</li>
							</ul>
						</p>
					</div>';
		$tips = '<div class="section" id="tips" name="tips" style="display:none;">
					<h1>General Tips and FAQs:</h1>
					<h2>Since I installed the Ebizmarts MailChimp module, some of my templates styles have been broken up, what can I do?:</h2>
					<p>The Ebizmarts MailChimp module has its own stylesheet, so you can easily redefine all those styles that are breaking your templates by editing this css file.<br/><b>BEWARE</b>: before edit any Ebizmarts MailChimp file, first create a backup of it (just in case).</p>
					<h2>Can I add new newsletter boxes on my store and keep the module working with those new boxes?:</h2>
					<p>Yes you can. If you want to keep the module working with a new newsletter page/box, you only need to use the same input’s ids, names and action used on the original Magento newsletter box form.</p>
					<h2>The guests subscribers are not being registered, why?:</h2>
					<p>This could be related to the double opt-in, this setting enabled means that the subscriber must confirm the subscription on its email account before being subscribed.</p>
					<h2>I\'m getting a 404 error when I try to access to the module settings, why?:</h2>
					<p>This means that you are getting an user role issue and you must do the following:
						<ul>
							<li>Refresh all your Magento caches.</li>
							<li>Recompile Magento (only if you have it enabled).</li>
							<li>Logout and login again on the Magento Admin Panel.</li>
						</ul>
					</p>
					<h2>I have several websites on my Magento installation, can I setup different lists as the Magento general newsletter for each one of those websites?:</h2>
					<p>The Ebizmarts MailChimp module will only allows you to setup one MailChimp account per installation, despite of this you can setup different MailChimp lists as Magento General newsletter per website.</p>
					<h2>Why the customers that doesn\'t complete the default Magento checkout are subscribed anyway to my MailChimp list?:</h2>
					<p>When a customer subscribes to the newsletter on the checkout billing page (optionally or forced), the subscription takes place on this step submit, for this same reason the customer receives the subscription email before complete the checkout process.</p>
					<h2>I have installed the module but I cannot see it anywhere:</h2>
					<p>Please check the following:
						<ul>
							<li>The module files are enabled (you can check this by going to system --> configuration --> advanced --> advanced, there you should see \'Ebizmarts_Mailchimp\' as enabled on the modules list).</li>
							<li>Refresh all your Magento caches and re-login on the Admin Panel.</li>
							<li>The Magento compiler is enabled (System --> Tools --> Compilation)?, if so then just recompile it.</li>
							<li>Files and folders permissions.</li>
							<li>Check that all the file locations are OK (only if you had installed the module manually).</li>
						</ul>
					</p>
					<h2>I have several lists on my MailChimp account, to which one of those will the subscribers be synchronized?:</h2>
					<p>The Ebizmarts MailChimp has been developed to support multi-lists and interest groups for each one of those, despite of this on the current module version you can only setup one those as the Magento general newsletter, for this reason all guests and new subscribers who came from the register page will be subscribed only to the pre-selected list set as general newsletter. </p>
					<h2>The Ebizmarts MailChimp module is conflicting my Admin Panel and I cannot access to it -or- I suspect that the Ebizmarts MailChimp module is conflicting other module on the store:</h2>
					<p>If you are sure that the you can\'t access to the Admin Panel for this module, just do the following:
						<ul>
							<li>Manually disable the module, to do this you must edit the /app/etc/modules/Ebizmarts_Mailchimp.xml file and<br/>replace: &lt;active&gt;true&lt;/active&gt;<br/>with: &lt;active&gt;false&lt;/active&gt;</li>
							<li>Refresh your Magento cache. If you have not access to the Magento Admin Panel, you\'ll need to "refresh" the Magento cache manually, to do this just rename the following folders:<br/>/var/cache <b>to</b> var/cache_bak<br/>/var/session <b>to</b> var/session_bak</li>
						</ul>
					Now  the module is disabled and you should be able to log in at the Admin panel. </p>
					<h2>At the Ebizmarts MailChimp WebHooks page, nothing happens when I click on the \'WebHooks Synchronization\' button</h2>
					<p>This issue could be related to a missing js file, please check if the /skin/adminhtml/default/default/mailchimp/MailChimp.js file is being loaded correctly on this page.</p>
					<h2>I have the double opt-in enabled but I\'m not getting the email confirmation neither the subscriber is not on my MailChimp list, why?</h2>
					<p>Firstly you must know that if the subscriber is an already registered customer on your store, then he won\'t receive the confirmation email (this is a Magento native behavior and have been extended to this feature too).<br/>
					Despite of this, if you are still not being subscribed on your MailChimp list, you can check the module logs: if you have your store logs enabled then the module will track any issue on its own exception log file located at <b>/var/log/mailChimp_Exceptions.log</b>.<br/>
					If this file hadn\'t been created or this file doesn\'t include info related to this issue, then I\'d like to suggest you to subscribe the customer via Magento Admin Panel, if this last works then you have edited incorrectly your frontend templates.</p>
					<h2>I realized the ones that signed up from the register page does not get synced. I tried replacing my template\'s register.html with the one from the guide but the style gets messed up, however it DID solved the syncing problem, what can I do?:</h2>
					<p>If you have edited correctly your register.phtml as required by the module but your issue only is resolved when you replace this file with the provided by the frontend installation post, then t is most probably that your register.phtml could have different ids on the form elements.</p>
				</div>';

        return $menu.'<div class="main">'.$landing.$install.$setup.$webhooks.$bulk.$ecomm360.$ctemplates.$sts.$uninstall.$tips.'</div>';
    }
}