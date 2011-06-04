<?php

class core_admin_image_upload extends core_admin_images
{
	private $assignments;
	
	public function __construct($db, $qs) {
		parent::__construct($db, $qs);
		$this->assignments = parent::getAssignments();
		
		$this->template = 'admin/image-upload.tpl.html';
		
		$dir = configuration::APPROOT.'public_html/';
		$image_dir = 'uploads/images';
		$dir .= $image_dir;
		if(sizeof($_FILES) > 0)
		{
			$allowedExtensions = array("jpg","JPG","gif","GIF","png","PNG");
			foreach ($_FILES as $file)
			{
				$filename = '/'.basename($file['name']);
				$target = $dir.$filename;

				if ($file['tmp_name'] > '')	{
					if (!in_array(end(explode(".", strtolower($file['name']))), $allowedExtensions))
					{
						$this->assignments['page']['feedback'] = 'Sorry - you can only upload a jpg, gif or png files';
					}
					elseif(move_uploaded_file($file['tmp_name'], $target))
					{
						$this->assignments['page']['feedback'] = 'Success - Your file was uploaded';
						$sql = sprintf('INSERT INTO ot_images SET url=%s', $db->queryParameter($image_dir.$filename));
						$db->changeQuery($sql);
					}
					else
					{
						$this->assignments['page']['feedback'] = 'Sorry - we were unable to upload your file at this time.';
					}
				}
			}
		}
	}
	
	public function getTemplate() {
		return $this->template;
	}
	
	public function getAssignments() {
		return $this->assignments;
	}
	
}