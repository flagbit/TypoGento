Here some Typoscript Examples:
####################################################################################

Outputs the Magento Header to TYPO3:

	includeLibs.user_tx_fbmagento_pi1 = EXT:fb_magento/pi1/class.tx_fbmagento_pi1.php
	
	page.headerData.10 = USER_INT
	page.headerData.10 {
	  userFunc = tx_fbmagento_pi1->main
	  block=typo3header
	}
	
	
Outputs the right Column of Magento:

	10 = USER_INT
	10 {
	  userFunc = tx_fbmagento_pi1->main
	  block=right
	  nowrap=1
	}

	
Generates an Magento Catgories Menu
	
	lib.submenu = COA
	lib.submenu{
		
		20 = HMENU
		20 {
			special = userfunction
			special{
				userFunc = user_tx_fbmagento_navigation->categories
				pid=15
				startcategory=18
		  	}
	
			stdWrap.preCObject = TEXT
			stdWrap.preCObject {
				data = {page:subtitle//page:title}
				typolink.parameter = 15
				wrap = <div id="sub-nav-title">|</div>
			}
		  
			expAll = 0
		    
			1 = TMENU
			1 {
				itemArrayProcFunc  = user_tx_fbmagento_navigation->clear
				wrap = <ul style="text-align: left">|</ul>
				noBlur = 1
				expAll = 1
				wrap = <ul id="sub-level1">|</ul>
				NO {
					wrapItemAndSub = <li class="first">|</li> |*| <li>|</li> |*| <li class="last">|</li>
				}
			}
		}	
	}	