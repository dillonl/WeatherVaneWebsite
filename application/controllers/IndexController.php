<?php

class IndexController extends BaseController
{

    public function init()
    {
		$this->_helper->layout->setLayout('layout');
        /* Initialize action controller here */
    }

    public function indexAction()
    {
        $this->view->headScript()->appendFile(Model_Helpers_URL::AssetURL() .'/scripts/run_helper.js');
    }
	
	public function testAction()
	{
		$runs_path = '/Users/dillonl/Documents/weathervane/svg/';
		$dates = array_diff(scandir($runs_path), array('.', '..', '.DS_Store'));
		$runs = array();
		foreach ($dates as $date)
		{
			$path = $runs_path . '/' . $date;
			$times = array_diff(scandir($path), array('.', '..', '.DS_Store'));
			foreach ($times as $time)
			{
				$path = $runs_path . '/' . $date . '/' . $time;
				$fields = array_diff(scandir($path), array('.', '..', '.DS_Store'));
				foreach ($fields as $field)
				{
					$runs[] = $field;
				}
			}
		}
		return $this->_helper->json($runs);
	}

	public function getRunsAction()
	{
		$runs_path = '/Users/dillonl/Documents/weathervane/svg/';
		$path = $runs_path;
		$runs = array();
		$dates = array_diff(scandir($runs_path), array('.', '..', '.DS_Store'));
		foreach ($dates as $date)
		{
			$path = $runs_path . '/' . $date;
			$times = array_diff(scandir($path), array('.', '..', '.DS_Store'));
			foreach ($times as $time)
			{
				$path = $runs_path . '/' . $date . '/' . $time;
				$fields = array_diff(scandir($path), array('.', '..', '.DS_Store'));
				foreach ($fields as $field)
				{
					$path = $runs_path . '/' . $date . '/' . $time . '/' . $field;
					$heights = array_diff(scandir($path), array('.', '..', '.DS_Store'));
					foreach ($heights as $height)
					{
						$run = array('date' => $date, 'time' => $time, 'height' => $height, 'field' => $field);
						$runs[] = $run;
					}
				}
			}
		}
		$this->view->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
		return $this->_helper->json($runs);
	}

	public function getSvgFileListAction($run, $height, $field)
	{
//		$run = strtolower($this->sanitizeFilePath($run));
//		$height = strtolower($this->sanitizeFilePath($height));
//		$field = strtolower($this->sanitizeFilePath($field));
		
		$svgPath = '/Users/dillonl/Documents/weathervane/svg/' . str_replace('.' , '/', $run) . '/'. $field . '/' . $height . '/';
		
//		$svgPath = APPLICATION_PATH . '/../public/svg/' . $run . '/' . $height . '/' . $field . '/';
		if (!file_exists($svgPath))
		{
			return $this->_helper->json(array('success' => false, 'message' => 'Weather information is unavailable', 'path' =>$svgPath));
		}
		$files = array_diff(scandir($svgPath), array('.','..'));
		$tmp_files = array();
		foreach ($files as $file)
		{
			if (strpos($file, "anim") === false)
			{
				$tmp_files[] = $file;
			}
		}
		return $this->_helper->json(array('success' => true, 'filenames' =>$tmp_files, 'path' => 'svg/' . $run . '/' . $height . '/' . $field . '/'));
	}
	
	public function getSVGFileAction($run, $field, $height, $filename)
	{	
		$svgFilePath = '/Users/dillonl/Documents/weathervane/svg/' . str_replace('.' , '/', $run) . '/'. $field . '/' . $height . '/' . $filename;
		
		header('Content-Type: image/svg+xml');
		header('Content-Disposition: attachment; filename="' . $filename . '"');
		readfile($svgFilePath);

		// disable the view ... and perhaps the layout
		$this->view->layout()->disableLayout();
		$this->_helper->viewRenderer->setNoRender(true);
		//call the action helper to send the file to the browser
//		$this->_helper->SendFile->sendFile($svgFilePath, 'image/svg+xml');
//		$this->_helper->viewRenderer->setNoRender();
	}

}

