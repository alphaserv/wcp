<?php
class Report
{
	public static function parseReq($reqbody)
	{
		$arr = array();
		
	}
	
	
	public static function defaultType($info)
	{
		header("HTTP/1.1 200 OK");
		header("Content-Type: text/xml; charset=\"utf-8\"");
	
		$buffer = '<?xml version="1.0" encoding="utf-8"?>
					<S:update-report xmlns:S="svn:" xmlns:V="http://subversion.tigris.org/xmlns/dav/" xmlns:D="DAV:" send-all="true">
						<S:target-revision rev="2"/>
							<S:open-directory rev="2">
								<D:checked-in>
									<D:href>/PhPSVN/index.php!svn/ver/2</D:href>
								</D:checked-in>
								<S:set-prop name="svn:entry:committed-rev">2</S:set-prop>
								<S:set-prop name="svn:entry:committed-date">2008-01-11T21:06:39.703125Z</S:set-prop>
								<S:set-prop name="svn:entry:last-author">admin</S:set-prop>
								<S:set-prop name="svn:entry:uuid">5806e170-7a6e-8540-9798-54a2e8462b58</S:set-prop>
								<S:add-file name="qwerty.txt">
									<D:checked-in>
										<D:href>/pHpSVN/index.php!svn/ver/2/qwerty.txt</D:href>
									</D:checked-in>
									<S:set-prop name="svn:entry:committed-rev">2</S:set-prop>
									<S:set-prop name="svn:entry:committed-date">2008-01-11T21:06:39.703125Z</S:set-prop>
									<S:set-prop name="svn:entry:last-author">admin</S:set-prop>
									<S:set-prop name="svn:entry:uuid">5806e170-7a6e-8540-9798-54a2e8462b58</S:set-prop>
									<S:txdelta>U1ZOAQAADQIOAY0Nc2FsdXQgbGEgdG90bw==\n</S:txdelta>
									<S:prop>
										<V:md5-checksum>a7ddc8098c13e0acecfffd26f1842dbd</V:md5-checksum>
									</S:prop>
								</S:add-file>
							<S:prop></S:prop>
						</S:open-directory>
					</S:update-report>';
		return $buffer;
	}
}