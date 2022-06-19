<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class ProductController extends CI_Controller {

	public function index()
	{
		$this->load->view('product');
	}

	public function getProducts(){
		$result = $this->db->query("select id,product_name,product_price,product_descr from product where status = 1")->result();
		$dataArray = array();
		foreach ($result as $row){
			$imgcount = $this->imgCount($row->id);
			$viewImagesBtn = '';
			if ($imgcount > 0) {
				$viewImagesBtn = '<button class="btn btn-xs btn-success" onclick="viewImages(\''.$row->id.'\')">View Images</button>';
			}
			$actionBtn=$viewImagesBtn.'<button class="btn btn-xs btn-info" onclick="productAction(\''.$row->id.'\',\'edit\')">Edit</button><button class="btn btn-xs btn-danger" onclick="productAction(\''.$row->id.'\',\'delete\')">Delete</button>';
			// $actionBtn = '<button class="btn btn-xs btn-info" onclick="productAction("'.$row->id.'","edit")">Edit</button><button class="btn btn-xs btn-danger" onclick="productAction("'.$row->id.'","delete")">Delete</button>';
			$tableRow = array(
				"id"=>$row->id,
				"product_name"=>$row->product_name,
				"product_price"=>$row->product_price,
				"product_descr"=>$row->product_descr,
				"action"=>$actionBtn
			);
			array_push($dataArray, $tableRow);
		}
		echo json_encode(array("data"=>$dataArray,"draw"=>1, "recordsTotal"=>count($result), "recordsFiltered"=>0));
	}
	public function imgCount($product_id=''){
		return $imgs = $this->db->where("product_id",$product_id)->get("product_img")->num_rows();
	}


	public function productOperation(){
		$action = $this->input->post("action");
		// echo json_encode($this->input->post());
		$response = array();
		if ($action == "insert"){
			$data = array(
				'product_name'=>$this->input->post("product_name"),
				'product_price'=>$this->input->post("product_price"),
				'product_descr'=>$this->input->post("product_descr"),
				'status'=>1
			);
			if($this->db->insert("product",$data)){
				$response['status'] = 200;
				$response['body'] = "inserted";
				$lastId = $this->db->insert_id();
				$response['lastProductId'] = $lastId;
		
			}
		}else if($action == 'edit'){
			$product_id = $this->input->post("product_id");
			$data = array(
				'product_name'=>$this->input->post("product_name"),
				'product_price'=>$this->input->post("product_price"),
				'product_descr'=>$this->input->post("product_descr")
			);
			$this->db->where("id",$product_id);
			if($this->db->update("product",$data)){
				$response['status'] = 200;
				$response['body'] = "updated";
				$response['lastProductId'] = $product_id;
//				if ()
			}
		}else{
			$data = array(
				"status"=>0
			);
			$delete = $this->db->where("id",$this->input->post("product_id"))->update("product",$data);
			if ($delete) {
				$response["status"] = 200;
				$response["body"] = "deleted Successfully";

			}
		}
				$response["lastQuery"] = $this->db->last_query();
		echo json_encode($response);
	}

	public function uploadProductImg(){
		$imageFile = $this->input->post('product_img');
		$lastProductId = $this->input->post('lastProductId');
		$count = $this->input->post('filesCount');
		$target_dir = "uploads/";
		$uploadedFiles = 0;

		$count = count($_FILES);
	    for ($i = 0; $i < $count; $i++) {
		        $filename = $_FILES['product_img_'.$i];
		        /* Location */
		        echo $location = "uploads/".$filename['name'];
		        /* Upload file */
		        if(move_uploaded_file($filename['tmp_name'],$location)){
		            $imgData = array(
		            	"product_id"=>$lastProductId,
						"file_path"=>$location
		            );
		            $this->db->insert("product_img",$imgData);
	            // echo $location;
		            $uploadedFiles++;
		        }
		}

		// for($i = 0;$i<$count;$i++){
		// 	$target_file = $target_dir . basename($_FILES["product_img"]["name"][$i]);
		// 	move_uploaded_file($_FILES["product_img"]["tmp_name"][$i], $target_file);	
		// 	// if(move_uploaded_file($_FILES["product_img"]["tmp_name"][$i], $target_file)){
				// $data = array(
	   //          	"product_id"=>$lastProductId,
				// 	"file_path"=>$target_file
	   //          );
	   //          $this->db->insert("product_img",$data);
	 //            $uploadedFiles++;
		// 	// }
		// }

		
		
		// if(move_uploaded_file($_FILES["product_img"]["tmp_name"], $target_file)){
			$response['status'] = 200;
			$response['data'] = $uploadedFiles .' Image uploaded';
		// }else{
			// $response['status'] = 201;
			// $response['data'] = 'Image not uploaded';
		// }
		echo json_encode($response);
	}
	public function getProductToEdit(){
		$product_id = $this->input->post("product_id");
		$result = $this->db->query("select * from product where id = ".$product_id)->row();
		if (count($result) > 0) {
			$response['status'] = 200;
			$response['data'] = $result;
		}else{
			$response['status'] = 201;
			$response['data'] = "No data found";
		}
		echo json_encode($response);
	}

	public function uploadMultiFiles($upload_path, $inputname, $combination = "",$productId){
		$error=array();
		$extension=array("jpeg","jpg","png","gif");
		foreach($_FILES["product_img"]["tmp_name"] as $key=>$tmp_name) {
		    $file_name=$_FILES["product_img"]["name"][$key];
		    $file_tmp=$_FILES["product_img"]["tmp_name"][$key];
		    $ext=pathinfo($file_name,PATHINFO_EXTENSION);

		    if(in_array($ext,$extension)) {
		        if(!file_exists($upload_path.$file_name)) {
		            move_uploaded_file($file_tmp=$_FILES["product_img"]["tmp_name"][$key],$upload_path.$file_name);
		            $data = array(
		            	"product_id"=>$productId,
						"file_path"=>'uploads/'.$newFileName
		            );
		            $this->db->insert("product_img",$data);
		        }
		        else {
		            $filename=basename($file_name,$ext);
		            $newFileName=$filename.time().".".$ext;
		            move_uploaded_file($file_tmp=$_FILES["product_img"]["tmp_name"][$key],$upload_path.$newFileName);
		            $data = array(
		            	"product_id"=>$productId,
						"file_path"=>'uploads/'.$newFileName
		            );
		            $this->db->insert("product_img",$data);
		        }
		    }
		    else {
		        array_push($error,"$file_name, ");
		    }
		}

	}

	public function getProuctImages(){
	$response = array();
		$count = 0;
		$count1 = 0;
		$imgs = $this->db->where("product_id",$this->input->post("product_id"))->get("product_img")->result();
		$imgSlider = '<div id="myCarousel" class="carousel slide" data-ride="carousel">
			  <ol class="carousel-indicators">';
			  	 foreach ($imgs as $value) {

			  	 	$active='';
			  	 	if ($count == 0) {
			  	 		$active ='active';
			  	 	}
			    	$imgSlider .= '<li data-target="#myCarousel" data-slide-to="'.$count.'" class="'.$active.'">1</li>';
			    	$count++;
			    }
			    
			  $imgSlider .= '</ol>';

			 $imgSlider .= '<div class="carousel-inner">';

			 foreach ($imgs as $value) {
			 	$active1='';
					if ($count1==0) {
						$active1 = 'active';
					}
				    $imgSlider .= '<div class="item '.$active1.'">
				      <img src="'.base_url($value->file_path).'">
				    </div>';
				   $count1++;
			}

			   
			  $imgSlider .='<a class="left carousel-control" href="#myCarousel" data-slide="prev">
			    <span class="glyphicon glyphicon-chevron-left"></span>
			    <span class="sr-only">Previous</span>
			  </a>
			  <a class="right carousel-control" href="#myCarousel" data-slide="next">
			    <span class="glyphicon glyphicon-chevron-right"></span>
			    <span class="sr-only">Next</span>
			  </a>
			</div>';
		$response['images'] = $imgSlider;
		$response['imagesArr'] = $imgs;
		echo json_encode($response);
	}
}
