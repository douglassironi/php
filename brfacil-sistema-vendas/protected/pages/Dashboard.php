<?php
class Dashboard extends TPage {
	public function onLoad($param) {
		parent::onLoad ( $param );
		if (! $this->IsPostBack) {

			$sql = QuerysBiRecord::Finder ()->FindBy_Codigo (1);
			$this->fnc_bi ( $sql, $this->grafico ,'chart_div');


			//echo "<meta HTTP-EQUIV='refresh' CONTENT='15;URL=".$this->Service->constructUrl("Dashboard")."&qry=".$this->tipoRelatorio->data."&qry2=".$this->tipoRelatorio2->data."'>";
				
		
		}
	}
	public function atualizaGrafico() {
		{
			$this->Response->redirect($this->Service->constructUrl("Dashboard")."&qry=".$this->tipoRelatorio->data."&qry2=".$this->tipoRelatorio2->data);
		}
	}
	public function fnc_bi($sql,$graf,$div) {
		if (isset($sql)){
		
		// $sql = 'select codigo produtos, quantidade_estoque quantidade from produtos';
		$ipd = VendaProdutosAgrupadosRecord::finder ()->findallBySql ( $sql->query );
		$data = "[";
		foreach ($ipd as $reg){
			$data.=$reg->quantidade.",";
			
		}
		$data.="]";
		
		$col = $sql->colunas;
		$rows = null;

		foreach ( $ipd as $reg ) {
			$rows .= '["' . $reg->data . '",' . $reg->quantidade . '],' . chr ( 10 );
		}
		
		$graf->text = '

			<script>	

var ctx = document.getElementById("myChart").getContext("2d");
				var ctx = $("#myChart").get(0).getContext("2d");
				var myNewChart = new Chart(ctx);
				
				new Chart(ctx).PolarArea(data, options);
				
				var data = {
    labels: ["January", "February", "March", "April", "May", "June", "July"],
    datasets: [
        {
            label: "My First dataset",
            fillColor: "rgba(220,220,220,0.2)",
            strokeColor: "rgba(220,220,220,1)",
            pointColor: "rgba(220,220,220,1)",
            pointStrokeColor: "#fff",
            pointHighlightFill: "#fff",
            pointHighlightStroke: "rgba(220,220,220,1)",
            data: [65, 59, 80, 81, 56, 55, 40]
        },
        {
            label: "My Second dataset",
            fillColor: "rgba(151,187,205,0.2)",
            strokeColor: "rgba(151,187,205,1)",
            pointColor: "rgba(151,187,205,1)",
            pointStrokeColor: "#fff",
            pointHighlightFill: "#fff",
            pointHighlightStroke: "rgba(151,187,205,1)",
            data: [28, 48, 40, 19, 86, 27, 90]
        }
    ]
};
		    
			</script>
				

				
				
				';
		}
		
		}
}

?>

