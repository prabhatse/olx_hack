<?php
/*
 * Copyright 2014 Empodex PHP Framework.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 *
 * @copyright 2014-2015 Empoddy Labs.
 * @author Prabhat Shankar <prabhat.singh88[at]gmail.com>
 */

class FileUploadController extends EFCController {

    public function __construct($session_started=false) {
        parent::__construct($session_started);
        $this->setViewTemplate('upload/file.tpl');
        $this->setPageTitle('User Files');
    }

    public function control() {          
    	$this->redirectToSternIndiaEndPoint();

    	$config = Config::getInstance();

    	if (isset($_POST['upload']) && $_POST['upload'] == 'Upload') {

    		$target_dir = new FileSystem('upload/');
    		$file = new File('foo',$target_dir);

    		$name = date('D_d_m_Y_H_m_s_');
    		$name = $name.$file->getName();
    		$file->setName($name);

    		$config = Config::getInstance();

    		$file->addValidations(array(
    			new Mimetype($config->getMimeTypes()),
    			//new MimeType('text/csv'),
    			new Size('5M')
    	    ));

    		$data = array(
			    'name'       => $file->getNameWithExtension(),
			    'extension'  => $file->getExtension(),
			    'mime'       => $file->getMimetype(),
			    'size'       => $file->getSize(),
			    'md5'        => $file->getMd5()
			);

    		try {
    			    			// /Profiler::debugPoint(true,__METHOD__, __FILE__, __LINE__,$data);
    			$file->upload();
    			//Profiler::debugPoint(true,__METHOD__, __FILE__, __LINE__,$data);
    			
			} catch (Exception $e) {
    			$errors = $file->getErrors();
			}

			$csvReader =new CSVReader();

            $destinationFile = $target_dir->directory.$file->getNameWithExtension();

			$data = $csvReader->parse_file($destinationFile);

			//$country= DAOFactory::getDAO('LocationDAO');
			
			foreach ($data as $loc_arr) {

                Utils::processLocation($loc_arr);

			}
			//Profiler::debugPoint(true,__METHOD__, __FILE__, __LINE__);

	    	$target_dir = "uploads/";
			$target_file = $target_dir . basename($_FILES["fileToUpload"]["name"]);
			$uploadOk = 1;
			$imageFileType = pathinfo($target_file,PATHINFO_EXTENSION);
			// Check if image file is a actual image or fake image
			if(isset($_POST["submit"])) {
		    	$check = getimagesize($_FILES["fileToUpload"]["tmp_name"]);
		    	if($check !== false) {
		        	echo "File is an image - " . $check["mime"] . ".";
		        	$uploadOk = 1;
		    	} else {
		        	echo "File is not an image.";
		        	$uploadOk = 0;
		    	}		    	
			}
		}
		return $this->generateView();
    }
}
