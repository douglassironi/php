<?php

class GerarQRCode extends TPage
{
	
	public function onInit ($param)
	{
		parent::onInit($param);
		if (!$this->getIsPostBack() && !$this->getIsCallBack())
		{
			null;
		}
	}
	
	
public function btGeraQRcode($sender,$param) {
   $img = "tmp/".date('Ymdhis').".png"; 	
   QRcode::png($this->dado->text, $img,QR_ECLEVEL_L,10);
   $this->img->data = $img;
}
	

}

 ?>

