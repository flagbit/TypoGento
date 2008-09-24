<?php

class user_tx_fbmagento_navigation extends  tx_fbmagento_navigation {
	
}

class tx_fbmagento_navigation {

		
	function test($content,$conf)    {

	    return array(
	        array(
	            'title' => 'Contact',
	            '_OVERRIDE_HREF' => 'index.php?id=10',
	            '_SUB_MENU' => array(
	                array(
	                    'title' => 'Offices',
	                    '_OVERRIDE_HREF' => 'index.php?id=11',
	                    '_OVERRIDE_TARGET' => '_top',
	                    'ITEM_STATE' => 'ACT',
	                    '_SUB_MENU' => array(
	                        array(
	                            'title' => 'Copenhagen Office',
	                            '_OVERRIDE_HREF' => 'index.php?id=11&officeId=cph',
	                        ),
	                        array(
	                            'title' => 'Paris Office',
	                            '_OVERRIDE_HREF' => 'index.php?id=11&officeId=paris',
	                        ),
	                        array(
	                            'title' => 'New York Office',
	                            '_OVERRIDE_HREF' => 'http://www.newyork-office.com',
	                            '_OVERRIDE_TARGET' => '_blank',
	                        )
	                    )
	                ),
	                array(
	                    'title' => 'Form',
	                    '_OVERRIDE_HREF' => 'index.php?id=10&cmd=showform',
	                ),
	                array(
	                    'title' => 'Thank you',
	                    '_OVERRIDE_HREF' => 'index.php?id=10&cmd=thankyou',
	                ),
	            ),
	        ),
	        array(
	            'title' => 'Products',
	            '_OVERRIDE_HREF' => 'index.php?id=14',
	        )
	    );
	}	
	
}



?>