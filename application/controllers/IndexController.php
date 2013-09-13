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

	public function getRunsAction()
	{
		$runs = array();
		$times = array('03', '09', '15', '21');
		$dates = array('20130910', '20130909', '20130908');
		$heights = array('surface', '10 mb', '175 mb');
		$fields = array('TMP', 'UGRD', 'RH');
		foreach ($times as $time)
		{
			foreach ($dates as $date)
			{
				foreach ($heights as $height)
				{
					foreach ($fields as $field)
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

	public function getListSVGFilesAction($run, $field, $height)
	{
		$run = $this->sanitizeFilePath($run);
		$field = $this->sanitizeFilePath($field);
		$height = $this->sanitizeFilePath($height);
		$files = array();
		$svgPath = APPLICATION_PATH . '/../svg/' . $run . '/' . $height . '/' . $field;
		$handle = opendir($svgPath);
		if ($handle)
		{
			while (false !== ($entry = readdir($handle)))
			{
				if ($entry != '.' && $entry != '..')
				{
					$files [] = $entry;
				}
			}
		}
		return $this->_helper->json($files);
	}

	public function getSVGFileAction($id)
	{
		$svgPath = 'C:\\xampp\\htdocs\\WeatherVaneWeb\\public\\svg\\' . $id;
		header('Content-Type: image/svg+xml');
		header('Content-Disposition: attachment; filename="weather-animation.svg"');
		readfile($svgPath);
		$this->view->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
	}

}

