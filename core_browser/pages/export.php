<?php

if (isset($_GET['id']) && !empty($_GET['id']))
{
	$serviceorder = new ServiceOrder();
	
	$lookup = $serviceorder->get($_GET['id']);
	
	if ($lookup === false)
	{
		$page->smarty->assign('errorCode', $serviceorder->errorCode);
		$page->smarty->assign('errorMsg', $serviceorder->errorMsg);
	}
	else
	{	
		switch($_GET['type'])
		{
			case 'pdf':
				require_once(WWW_DIR.'libs/wkhtmltopdf/WkHtmlToPdf.php');
	
				$globalOptions = array(
					'binPath' 		=> '/usr/bin/wkhtmltopdf',
					'background',
					'no-outline',
					'print-media-type',
					'viewport-size'	=> '1280x1024',
					'encoding' 		=> 'UTF-8',
					'page-size' 	=> 'A4',
					'orientation'	=> 'Portrait',
					'footer-line',
					'footer-font-size'	=> '5',
					'footer-left'	=> '[title]',
					'footer-right'	=> '[date] [time] - Page [page]/[topage]'
				);
	
				$pdf = new WkHtmlToPdf($globalOptions);
	
				$reportUrl = $page->serverurl.'?page=serviceorder&view='.$page->view.'&id='.$_GET['id'].'&lang='.$page->language;
				$pdf->addPage($reportUrl, array('javascript-delay'=>1000));
	
				if (!$pdf->send()) 
				{
					sleep(1);
					
					//try again if error occurred during pdf creation
					if (!$pdf->send()) 
					{
						$page->smarty->assign('errorCode', 'EXPDF');
						$page->smarty->assign('errorMsg', $pdf->getError());
					}
				}
			break;
			default:
				$page->smarty->assign('errorCode', 'EXTYPE');
				$page->smarty->assign('errorMsg', 'Export type not defined');
			break;
		
		}
		
	}
	
	$page->content = $page->smarty->fetch('serviceorder.tpl');
	$page->render();
}
else
{
	$page->show404();
}

?>