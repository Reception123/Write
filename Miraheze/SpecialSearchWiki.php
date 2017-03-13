<?php
class SpecialSearchWiki extends SpecialPage {
	function __construct() {
		parent::__construct( 'SearchWiki' );
	}

	function execute( $par ) {
		$request = $this->getRequest();
		$out = $this->getOutput();
		$this->setHeaders();

		if ( is_null( $par ) || $par === '' ) {
			$this->doPagerStuff();
		} else {
			$this->lookupRequest( $par );
		}
	}

	function doPagerStuff() {
		$request = $this->getRequest();
                $out = $this->getOutput();

		$localpage = $this->getPageTitle()->getLocalUrl();
		$searchConds = false;

		$type = $request->getVal( 'wSearchtype' );
		$target = $request->getVal( 'target' );
		$year = $request->getIntOrNull( 'year' );
		$month = $request->getIntOrNull( 'month' );
		$search = $request->getVal( 'wSearch' );

		if ( $type === 'sitename' ) {
			$searchConds = array( 'cw_sitename' => $search );
		} elseif ( $type === 'dbname' ) {
			$searchConds = array( 'cw_dbname' => $search );
		} elseif ( $type === 'status' ) {
			$searchConds = array( 'cw_status' => $search );
		} elseif ( $type === 'language' ) {
      $searchConds = array( 'cw_language' => $search );
    } elseif ( $type === 'closed' ) {
      $searchConds = array( 'cw_closed' => $search );
    }
    

		$selecttypeform = "<select name=\"wSearchtype\"><option value=\"requester\">requester</option><option value=\"sitename\">sitename</option><option value=\"status\">status</option><option value=\"dbname\">dbname</option><option value=\"dbname\">language</option><option value=\"dbname\">closed (1 or 0)</option></select>";

		$form = Xml::openElement( 'form', array( 'action' => $localpage, 'method' => 'get' ) );
                $form .= '<fieldset><legend>' . $this->msg( 'requestwikiqueue-searchrequest' )->escaped() . '</legend>';
                $form .= Xml::openElement( 'table' );
		# TODO: Should be escaped against HTML, but should NOT escape $selecttypeform
		$form .= '<tr><td>Find wiki requests where the ' . $selecttypeform . ' is ';
		$form .= Xml::input( 'wSearch', 40, '' ) . '</td></tr>';
		$form .= '<tr><td>' . Xml::dateMenu( $year, $month ) . '</td>';
		$form .= '<td>' . Xml::submitButton( $this->msg( 'requestwikiqueue-searchbutton' )->escaped() ) . '</td></tr>';
                $form .= Xml::closeElement( 'table' );
                $form .= '</fieldset>';
		$form .= Xml::closeElement( 'form' );

		$out->addHTML( $form );

		$pager = new SearchWikiPager( $this, $searchConds, $year, $month );
		$out->addHTML(
			$pager->getNavigationBar() .
			$pager->getBody() .
			$pager->getNavigationBar()
		);
	}

	function lookupRequest( $par ) {
		$dbr = wfGetDB( DB_SLAVE );

		$res = $dbr->selectRow( 'cw_requests',
			array(
				'cw_user',
				'cw_comment',
				'cw_dbname',
				'cw_language',
				'cw_private',
				'cw_sitename',
				'cw_status',
				'cw_status_comment',
				'cw_status_comment_user',
				'cw_status_comment_timestamp',
				'cw_timestamp',
				'cw_url',
				'cw_user'
			),
			array(
				'cw_id' => $par
			),
			__METHOD__,
			array()
		);

		if ( !$res ) {
			$this->getOutput()->addWikiMsg( 'requestwikiqueue-requestnotfound' );
			return false;
		}
                $form .= Xml::closeElement( 'table' );
                $form .= '</fieldset>';
		$form .= Xml::closeElement( 'form' );

		$this->getOutput()->addHTML( $form );

		if ( $this->getRequest()->wasPosted() ) {
			$this->processRequestStatusChanges( $par );
		}
	}
}
