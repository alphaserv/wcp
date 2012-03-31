<?php

class Propfind
{
	public static function parseReq($reqbody)
	{
	
	}
	
	public static function emptyType($info)
	{
		header("HTTP/1.1 207 Multi-Status");
		header("Content-Type: text/xml; charset=\"utf-8\"");
		$buffer = '<?xml version="1.0" encoding="utf-8"?>
					<D:multistatus xmlns:D="DAV:" xmlns:ns1="http://subversion.tigris.org/xmlns/dav/" xmlns:ns0="DAV:">
						<D:response xmlns:lp1="DAV:" xmlns:lp3="http://subversion.tigris.org/xmlns/dav/">
							<D:href>' . $_SERVER['SCRIPT_NAME'] . '</D:href>
							<D:propstat>
								<D:prop>
									<lp1:version-controlled-configuration>
										<D:href>' . $_SERVER['SCRIPT_NAME'] . '?args=type=default</D:href>
									</lp1:version-controlled-configuration>
									<lp1:resourcetype>
										<D:collection/>
									</lp1:resourcetype>
									<lp3:baseline-relative-path></lp3:baseline-relative-path>
									<lp3:repository-uuid>5806e170-7a6e-8540-9798-54a2e8462b58</lp3:repository-uuid>
								</D:prop>
								<D:status>HTTP/1.1 200 OK</D:status>
							</D:propstat>
						</D:response>
					</D:multistatus>
		';
		return $buffer;
	}
	
	public static function defaultType($info)
	{
		if ($_SERVER['HTTP_LABEL']) {
			header("Vary: Label");
			header("HTTP/1.1 207 Multi-Status");
			header("Content-Type: text/xml; charset=\"utf-8\"");
			$buffer = '<?xml version="1.0" encoding="utf-8"?>
						<D:multistatus xmlns:D="DAV:" xmlns:ns0="DAV:">
							<D:response xmlns:lp1="DAV:" xmlns:lp3="http://subversion.tigris.org/xmlns/dav/">
								<D:href>' . $_SERVER['SCRIPT_NAME'] . '?args=type=bln!file=fake!rev=0</D:href>
								<D:propstat>
									<D:prop>
										<lp1:baseline-collection>
											<D:href>' . $_SERVER['SCRIPT_NAME'] . '?args=type=bc!file=fake!rev=0</D:href>
										</lp1:baseline-collection>
										<lp1:version-name>2</lp1:version-name>
									</D:prop>
									<D:status>HTTP/1.1 200 OK</D:status>
								</D:propstat>
							</D:response>
						</D:multistatus>';
		}
		if (!$_SERVER['HTTP_LABEL']) {
			header("HTTP/1.1 207 Multi-Status");
			header("Content-Type: text/xml; charset=\"utf-8\"");
			$buffer = '<?xml version="1.0" encoding="utf-8"?>
						<D:multistatus xmlns:D="DAV:" xmlns:ns0="DAV:">
							<D:response xmlns:lp1="DAV:" xmlns:lp3="http://subversion.tigris.org/xmlns/dav/">
								<D:href>' . $_SERVER['SCRIPT_NAME'] . '?args=type=default</D:href>
								<D:propstat>
									<D:prop>
										<lp1:checked-in>
											<D:href>' . $_SERVER['SCRIPT_NAME'] . '?args=type=bln!file=fake!rev=0</D:href>
										</lp1:checked-in>
									</D:prop>
									<D:status>HTTP/1.1 200 OK</D:status>
								</D:propstat>
							</D:response>
						</D:multistatus>
			';
		}
		return $buffer;
	}
	
	public static function blnType($info)
	{
		header("HTTP/1.1 207 Multi-Status");
		header("Content-Type: text/xml; charset=\"utf-8\"");
		$buffer = '<?xml version="1.0" encoding="utf-8"?>
					<D:multistatus xmlns:D="DAV:" xmlns:ns0="DAV:">
						<D:response xmlns:lp1="DAV:" xmlns:lp3="http://subversion.tigris.org/xmlns/dav/">
							<D:href>' . $_SERVER['SCRIPT_NAME'] . '?args=type=bln!file=!rev=</D:href>
							<D:propstat>
								<D:prop>
									<lp1:baseline-collection>
										<D:href>' . $_SERVER['SCRIPT_NAME'] . '?args=type=bc!file=!rev=</D:href>
									</lp1:baseline-collection>
									<lp1:version-name>2</lp1:version-name>
								</D:prop>
								<D:status>HTTP/1.1 200 OK</D:status>
							</D:propstat>
						</D:response>
					</D:multistatus>
				';
		return $buffer;
	}
	
	public static function bcType()
	{
		header("HTTP/1.1 207 Multi-Status");
		header("Content-Type: text/xml; charset=\"utf-8\"");
		$buffer = '<?xml version="1.0" encoding="utf-8"?>
					<D:multistatus xmlns:D="DAV:" xmlns:ns1="http://subversion.tigris.org/xmlns/dav/" xmlns:ns0="DAV:">
						<D:response xmlns:lp1="DAV:" xmlns:lp3="http://subversion.tigris.org/xmlns/dav/">
							<D:href>' . $_SERVER['SCRIPT_NAME'] . '?args=type=bc!file=!rev=</D:href>
							<D:propstat>
								<D:prop>
									<lp1:version-controlled-configuration>
										<D:href>' . $_SERVER['SCRIPT_NAME'] . '?args=type=default</D:href>
									</lp1:version-controlled-configuration>
									<lp1:resourcetype>
										<D:collection/>
									</lp1:resourcetype>
									<lp3:baseline-relative-path></lp3:baseline-relative-path>
									<lp3:repository-uuid>5806e170-7a6e-8540-9798-54a2e8462b58</lp3:repository-uuid>
								</D:prop>
								<D:status>HTTP/1.1 200 OK</D:status>
							</D:propstat>
						</D:response>
					</D:multistatus>
				';
		return $buffer;
	}
}

?>