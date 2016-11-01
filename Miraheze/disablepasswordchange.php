$wgHooks['PrefsPasswordAudit'][] = 'onPrefsPasswordAuditTestWiki';
function onPrefsPasswordAuditTestWiki( $user, $newPass, $error ) {
	if ( $user->getName() == 'Example' ) {
		return "User not allowed to change password, Example account";
	}

		return true;
}
