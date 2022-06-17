<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class ProductController extends CI_Controller {

	public function index()
	{
		$this->load->view('product');
	}

	public function getProducts(){
		$result = $this->db->query("select id,product_name,product_price from product")->result();
		$data = array();
		foreach ($result as $row){
			array_push($data, $row);
		}
		echo json_encode(array("data"=>$data,"draw"=>1, "recordsTotal"=>count($result), "recordsFiltered"=>0));
	}

	public function productOperation(){
		$action = $this->input->post("action");
		$response = array();
		if ($action == "insert"){
			$data = array(
				'product_name'=>$this->input->post("product_name"),
				'product_price'=>$this->input->post("product_price"),
				'product_descr'=>$this->input->post("product_descr")
			);
			if($this->db->insert("product")){
				$response['status'] = 200;
				$response['body'] = "inserted";
//				$lastId = $this->db->insert_id();
//				$upload_path = 'uploads/';
//				$inputname = $this->input->post("product_img");
//				$fileUpload = $this->uploadMultiFiles($upload_path, $inputname);
//				if ()
			}
		}else{
			$data = array(
				'product_name'=>$this->input->post("product_name"),
				'product_price'=>$this->input->post("product_price"),
				'product_descr'=>$this->input->post("product_descr")
			);
			$this->db->where("id",$this->input->post("product_id"));
			if($this->db->update("product")){
				$response['status'] = 200;
				$response['body'] = "updated";
//				$lastId = $this->db->insert_id();
//				$upload_path = 'uploads/';
//				$inputname = $this->input->post("product_img");
//				$fileUpload = $this->uploadMultiFiles($upload_path, $inputname);
//				if ()
			}
		}
		echo json_encode($response);
	}

	public function uploadMultiFiles($upload_path, $inputname, $combination = ""){

		$combination = (explode(",", $combination));
		$files = $_FILES;
		$config['upload_path'] = $upload_path;
		$config['allowed_types'] = '*';
//            $config['max_size'] = '20000000';    //limit 10000=1 mb
		$config['remove_spaces'] = true;
		$config['overwrite'] = false;

		$this->load->library('upload', $config);

		if (is_array($_FILES[$inputname]['name'])) {
			$count = count($_FILES[$inputname]['name']); // count element
			$files = $_FILES[$inputname];
			$images = array();
			$dataInfo = array();
			if ($count > 0) {
//				if (in_array("1", $combination)) {
//					for ($j = 0; $j < $count; $j++) {
//						$fileName = $files['name'][$j];
////						if (in_array($fileName, $check_file_exist)) {
////							$response['status'] = 201;
////							$response['body'] = $fileName . " Already exist";
////							return $response;
////						}
//					}
//				}
				$inputname = $inputname . "[]";
				for ($i = 0; $i < $count; $i++) {
					$_FILES[$inputname]['name'] = $files['name'][$i];
					$_FILES[$inputname]['type'] = $files['type'][$i];
					$_FILES[$inputname]['tmp_name'] = $files['tmp_name'][$i];
					$_FILES[$inputname]['error'] = $files['error'][$i];
					$_FILES[$inputname]['size'] = $files['size'][$i];
					$fileName = $files['name'][$i];
					//get system generated File name CONCATE datetime string to Filename
					if (in_array("2", $combination)) {
						$date = date('Y-m-d H:i:s');
						$randomdata = strtotime($date);
						$fileName = $randomdata . $fileName;
					}
					$images[] = $fileName;

					$config['file_name'] = $fileName;

					$this->upload->initialize($config);
					$up = $this->upload->do_upload($inputname);
					//var_dump($up);
					$dataInfo[] = $this->upload->data();
				}
				//var_dump($dataInfo);

				$file_with_path = array();
				foreach ($dataInfo as $row) {
					$raw_name = $row['raw_name'];
					$file_ext = $row['file_ext'];
					$file_name = $raw_name . $file_ext;
					if(!empty($file_name)){
						$file_with_path[] = $upload_path . "/" . $file_name;
					}
				}
				if (count($file_with_path) > 0) {
					$response['status'] = 200;
					$response['body'] = $file_with_path;
				} else {
					$response['status'] = 201;
					$response['body'] = $file_with_path;
				}
				return $response;
			} else {
				$response['status'] = 201;
				$response['body'] = array();
				return $response;
			}
		}
	}
}
