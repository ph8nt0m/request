<?php

// ������ ���� �����ص帮�� ������������ ���� ���� �� �������� ������ �ֽñ� �ٶ��ϴ�. 
	header("Content-type: text/html; charset=euc-kr");

	/********************************************************************************************************************************************
		NICE������ Copyright(c) KOREA INFOMATION SERVICE INC. ALL RIGHTS RESERVED
		
		���񽺸� : ����Ȯ�� ����
		�������� : ����Ȯ�� ������û ������

		��ȭ�� �̿� �� �Ʒ� IP�� Port�� �������ּž� �մϴ�
		IP : 121.162.155.160 / Port : 80, 443
	*********************************************************************************************************************************************/

	$niceUid = "NID204469";				// NICE���� �߱޹��� ����Ʈ�ڵ�
	$svcPwd  = "81212319";				// NICE���� �߱޹��� ����Ʈ �н�����
	$strCharset	= "EUC-KR";		// ���������� �ѱ� ���ڵ� (EUC-KR, UTF-8)

	// �Է� ���������� ���޵� �Է°� ���
	$service		= $_POST["service"];		// ���񽺱���
	$svcGbn			= $_POST["svcGbn"];			// ��������
	$strGbn			= $_POST["strGbn"];			// ���±��� (1:���ΰ���, 2:���ΰ���)
	$strBankCode	= $_POST["strBankCode"];	// �����ڵ�
	$strAccountNo	= $_POST["strAccountNo"];	// ���¹�ȣ

	// �����ָ� �ʱ�ȭ �� ���
	$strNm	= "";
	if(isset($_POST["name"])){
		$strNm = $_POST["name"];	

		// URL���ڵ� ó��
		$strNm = urlencode($strNm);
	}

	// ������� �ʱ�ȭ �� ��� (����-������� 6�ڸ�, ����-����ڹ�ȣ 10�ڸ�)
	$strResId = "";
	if(isset($_POST["birth"])){
		$strResId = $_POST["birth"];
	}
	
	// �ֹ���ȣ ���� (�ߺ��� ���� �Ұ�, �ҽ� ���� ���ʿ�)	
	$strOrderNo		= date("Ymd") . rand(1000000000,9999999999);	

	// ��ȸ���� ���� (10:ȸ������ 20:����ȸ������ 30:�������� 40:��ȸ��Ȯ�� 90:��Ÿ)	
	$inq_rsn		= "10";			

	// �������� ó��
	$bcResult = bankCheck($strCharset, $niceUid, $svcPwd, $service, $svcGbn, $strGbn, $strBankCode, $strAccountNo, $strNm, $strResId, $inq_rsn, $strOrderNo); 

	if( $bcResult == "") {
		echo "�ֹ���ȣ   : " . $strOrderNo . "<br>";
		echo "�����ڵ�   : E999<br>";
		echo "����޽��� : ���Ͽ��ῡ �����Ͽ����ϴ�.<br>";
	}
	else
	{
		// ����� ����
		$bcResults = explode("|", $bcResult);		

		// ����� ����
		$resultOrderNo	= $bcResults[0];	// �ֹ���ȣ
		$resultCode		= $bcResults[1];	// ����ڵ�
		$resultMsg		= $bcResults[2];	// ����޼���
		
		// echo "�ֹ���ȣ   : " . $resultOrderNo . "<br>";
		// echo "����ڵ�   : " . $resultCode . "<br>";
		// echo "����޽��� : " . $resultMsg . "<br>";

		echo json_encode(array('result_code' => '200', 'result'=>$resultMsg));
	}

	// �������� ����-POST �Լ�
	function bankCheck($strCharset, $niceUid, $svcPwd, $service, $svcGbn, $strGbn, $strBankCode, $strAccountNo, $strNm, $strResId, $inq_rsn, $strOrderNo){
		
		// ����� �ʱ�ȭ
		$result = "";

		// NICE �������� ȣ��Ʈ
		$host = "secure.nuguya.com";

		// NICE �������� URL(EUC-KR)																													
		$target = "https://secure.nuguya.com/nuguya/service/realname/sprealnameactconfirm.do";
		if (strtoupper($strCharset) == "UTF-8"){
			// NICE �������� URL(UTF-8)
			$target = "https://secure.nuguya.com/nuguya2/service/realname/sprealnameactconfirm.do";
		}
		
		// �������� �Ķ���� ����
		$postValues = "niceUid"			. "=" . $niceUid	
				. "&" . "svcPwd"		. "=" . $svcPwd	
				. "&" . "service"		. "=" . $service	
				. "&" . "svcGbn"		. "=" . $svcGbn	
				. "&" . "strGbn"		. "=" . $strGbn	
				. "&" . "strBankCode"	. "=" . $strBankCode	
				. "&" . "strAccountNo"	. "=" . $strAccountNo	
				. "&" . "strNm"			. "=" . $strNm	
				. "&" . "strResId"		. "=" . $strResId	
				. "&" . "inq_rsn"		. "=" . $inq_rsn	
				. "&" . "strOrderNo"	. "=" . $strOrderNo;

		// URL �� �Ķ���� Ȯ��
		// echo "URL:<br>" . $target. "<br><br>";
		// echo "POST:<br>" . $postValues. "<br><br>";

		// ���� ��Ʈ ���� (HTTP:80, HTTPS:443)
		$port = 80;

		// ���� Ÿ�Ӿƿ� (10��)
		$timeout = 10;

		// ������� ����
		$socket  = fsockopen($host, $port, $errno, $errstr, $timeout); 

		// ������� Ȯ��
		if(!$socket){
			// ������� ���� ���
			echo "ERR: $errstr ($errno)<br />\n";	
		}
		else
		{
			// ��û ��� ����
			$request  = "POST $target HTTP/1.1\r\n"
						. "Host: $host\r\n"
						. "Content-Type: application/x-www-form-urlencoded\r\n"
						. "Content-Length: " . strlen($postValues) . "\r\n"
						. "Connection: close\r\n"
						. "\r\n"
						. $postValues; 

			// ��û ��� ����		
			fputs($socket, $request); 

			// ���� ��� �ʱ�ȭ
			$response = "";

			// ������� ó��
			while(!feof($socket)){
				$response .= fgets($socket, 1024);
			}

			//������� ����
			fclose($socket); 

			// ������� ����
			$rspInfo = explode("\r\n", $response);		

			// 9��° ���� ����
			$result = $rspInfo[8];

		}

		return $result;
	}

?>
