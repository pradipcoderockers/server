<?php /* Template Name: CustomPage1 */?>
<?php get_header();
$current_user = wp_get_current_user();
$userId = isset($current_user->data->ID)?$current_user->data->ID:"";
if($userId==0)
{
	header('Location: '.get_home_url().'/login');exit;
}
global $wpdb;
if($wp->request==='server-iq-profile')
{
	?>
	 <div class="container">
	    <?php if ( have_posts() ) :
	        while ( have_posts() ) : the_post();
				the_content();
	        endwhile;
	    endif; ?>
	</div> 
<?php }
else if($wp->request==='process-result')
{
	$query = "SELECT * FROM wp_quiz WHERE 1";
	$wpdb->query($query);
	$questionList = $wpdb->get_results($query);
	if(!empty($questionList))
	{
		foreach($questionList as $list)
		{
			 $queId = 'ques_'.$list->id; 
		     $vendorId = 1;
			 $val  = isset($_COOKIE[$queId])?$_COOKIE[$queId]:0;
			$val = $val;
			$sql = "INSERT INTO wp_quiz_result (quiz_id, user_id, vendor_id,point_score,added_on) VALUES (".$list->id.", ".$userId.", ".$vendorId.",".$val.",".time().")"; 
		  	$wpdb->query($sql);
		}
		header('Location: '.get_home_url().'/server-iq-result');exit;
	}
}
else if($wp->request==='server-iq-result')
{
	$query = "SELECT * FROM wp_quiz WHERE 1";
	$wpdb->query($query);
	$arr = array();
	$arr1 = array();
	$pointScorePer = 0;
	$questionList = $wpdb->get_results($query);
	if(!empty($questionList))
	{
	   foreach($questionList as $result)
	   {
		   $query = "SELECT point_score FROM wp_quiz_result WHERE quiz_id={$result->id} AND user_id={$userId} ORDER BY id DESC";
		   $questionListRow = $wpdb->get_row($query);
		   if(!empty($questionListRow))
		   {
				$pointScore = $questionListRow->point_score;   
		   }
		   $arr[$result->quiz_id][] = array('question_name'=>$result->question_name,'point_score'=>$pointScore);
		   $arr1[$result->quiz_id][] = $pointScorePer+(int)$pointScore;
	   }
		$sum1 = array_sum($arr1[1]);
		$sum2 = array_sum($arr1[2]);
		$sum3 = array_sum($arr1[3]);
		$sum4 = array_sum($arr1[4]);
		$sum5 = array_sum($arr1[5]);
		$per1  = $sum1*100/20;
		$per2 = $sum2*100/20;
		$per3 = $sum3*100/20;
		$per4 = $sum4*100/20;
		$per5 = $sum5*100/20;
		
		$totalGraphPer = ($per1+$per2+$per3+$per4+$per5)/5;
	}
	//echo '<pre>';print_r($arr);exit;
	?>
	  	<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
	  	<script>
				google.charts.load("current", {packages:["corechart"]});
				google.charts.setOnLoadCallback(drawChart);
				function drawChart() {
				  var data = google.visualization.arrayToDataTable([
							 ['Element', '', { role: 'style' }],
							 ['R', <?php echo $per1;?>, '#3366CC'], 
							 ['P', <?php echo $per2;?>, '#109618'],            // RGB value
							 ['A', <?php echo $per3;?>, '#FF9900'],            // English color name
							 ['T', <?php echo $per4;?>, '#DC3912'],
							 ['H', <?php echo $per5;?>, '#990099'], // CSS-style declaration
						  ]);

				  var view = new google.visualization.DataView(data);
				  view.setColumns([0, 1,
								   { calc: "stringify",
									 sourceColumn: 1,
									 type: "string",
									 role: "annotation" },
								   2]);

				  var options = {
					title: "",
					hAxis: {
							  title: 'Grand Total <?php echo $totalGraphPer;?>/100',
							  minValue: 0
							},
					legend: { position: "none" },
				  };
				  var chart = new google.visualization.BarChart(document.getElementById("barchart_values"));
				  chart.draw(view, options);
			  }
			</script>
          <div class="container">
               <div class="row">
				 <div class="col-md-12" id="barchart_values" style="width: 100%; height: 400px;"></div>
			 </div>
		 </div>
	     <div class="container">
               <div class="row">
					<div class="col-md-6" style="border-right:1px solid">
						<h3>Section 1 Results <u><?php echo $per1;?></u> %</h3>
						<h4><strong>Section 1: Responsibility</strong></h4>
						<ol>
							<?php foreach($arr[1]  as $first){?>
								<li><?php echo $first['question_name'];?></li>
							<?php }?>
						</ol>
					</div>
					<div class="col-md-6">
						<h3>Section 2 Results <u><?php echo $per2;?></u> %</h3>
						<h4><strong>Section 2: Positivity</strong></h4>
						<ol>
							<?php foreach($arr[2]  as $first){?>
								<li><?php echo $first['question_name'];?></li>
							<?php }?>
						</ol><br/><br/>
					</div>
					<div class="col-md-6" style="border-right:1px solid;margin-top: 17px;">
						<h3>Section 3 Results <u><?php echo $per3;?></u> %</h3>
						<h4><strong>Section 3: Awareness</strong></h4>
						<ol>
						<?php foreach($arr[3]  as $first){?>
								<li><?php echo $first['question_name'];?></li>
							<?php }?>
							
						</ol>
					</div>
					<div class="col-md-6">
						<h3>Section 4 Results <u><?php echo $per4;?></u> %</h3>
						<h4><strong>Section 4: Timeliness</strong></h4>
						<ol>
							<?php foreach($arr[4]  as $first){?>
								<li><?php echo $first['question_name'];?></li>
							<?php }?>
							
						</ol>
					</div>
					<div class="col-md-6" style="border-right:1px solid">
						<h3>Section 5 Results <u><?php echo $per5;?></u> %</h3>
						<h4><strong>Section 5: Hospitality</strong></h4>
						<ol>
							<?php foreach($arr[5]  as $first){?>
								<li><?php echo $first['question_name'];?></li>
							<?php }?>
						</ol>
					</div>
					<div class="col-md-6" ></div>
			   </div>
			   
               <div class="clearfix">
               </div>
            </div>
            
<?php 
}
else
{
    $headingArr = array('1'=>'RESPONSIBILITY','2'=>'POSITIVITY','3'=>'AWARENESS','4'=>'TIMELINESSS','5'=>'HOSPITALITY');	
	$arrayId = explode('-',$wp->request);
	$Id = $arrayId[count($arrayId)-1];
	$query = "SELECT * FROM wp_quiz WHERE quiz_id={$Id}";
	$wpdb->query($query);
	$questionList = $wpdb->get_results($query);
?>
  <div class="container">
   <div class="row">
		<div class="col-md-12">
			<h3>SIQ Profile</h3>
			<h4 class="offsetbottom15"><strong>Section <?php echo $Id;?> : <?php echo $headingArr[$Id]?></strong></h4>
		</div>
		<div class="col-md-8">
			<ul style="list-style:none;">
				<?php if(!empty($questionList)){
					foreach($questionList as $key=>$data)
					{
					?>
						<li class="offsetbottom25 offset<?php echo $data->id;?>"> <strong>(<?php echo $data->id;?>)</strong>  <span><?php echo $data->question_name;?></span>
							<input type="hidden" id="questionId_<?php echo $key+1;?>" value="<?php echo $data->id;?>">
						</li>
				<?php 
					}
					} ?>
			</ul>
		</div>
		<div class="col-md-4">
			<ul style="list-style:none;">
				<?php if(!empty($questionList)){
					foreach($questionList as $key=>$data)
					{
					?>
						<li class="offsetbottom25 offset<?php echo $data->id;?>">
							<div class="slider-container">
							<input type="text" id="slider<?php echo $key+1;?>" class="slider" />
						</div>
					<?php 
					}
				} ?>
			</ul>
		</div>
   </div>
   <div class="row">
		<div class="col-md-12 text-right">
			<?php if($Id==5)
			{?>
				<a href="<?php echo get_home_url(); ?>/process-result" style="background:#00B3BC;border-radius:20px;color:#FFF" class="btn btn-primary">SEE RESULT</a>	
			<?php }
			else
			{
			?>
				<a href="<?php echo get_home_url(); ?>/server-iq-exam-<?php echo $Id+1;?>" style="background:#CCD53C;border-radius:20px;color:#FFF" class="btn btn-primary">CONTINUE TO SECTION <?php echo $Id+1;?> ></a>
			<?php } ?>
		</div>
		</div>
   <div class="clearfix">
   </div>
</div>
<style>
	.offsetbottom15{margin-bottom:60px!important;}
	.offsetbottom25{height:75px;}
	.offset18{margin-bottom:40px !important;}
</style>
</div>
<link rel="stylesheet" href="<?php echo get_stylesheet_directory_uri(); ?>/assets/css/rSlider.min.css">
<script src="<?php echo get_stylesheet_directory_uri(); ?>/assets/js/rSlider.min.js"></script>
    <script>
        (function () {
            'use strict';

            var init = function () {                
                var slider1 = new rSlider({
                    target: '#slider1',
                    values: [0,1, 2, 3, 4],
                    range: false,
                    set: [0],
                    tooltip: false,
                    onChange: function (vals) {
                        var question1 = document.getElementById("questionId_1").value;
                        document.cookie = "ques_"+question1+"="+vals+";path=/";

                    }
                });
                
                var slider1 = new rSlider({
                    target: '#slider2',
                    values: [0,1, 2, 3, 4],
                    range: false,
                    set: [0],
                    tooltip: false,
                    onChange: function (vals) {
                        var question2 = document.getElementById("questionId_2").value;
                        document.cookie = "ques_"+question2+"="+vals+";path=/";
                    }
                });
                
                var slider1 = new rSlider({
                    target: '#slider3',
                    values: [0,1, 2, 3, 4],
                    range: false,
                    set: [0],
                    tooltip: false,
                    onChange: function (vals) {
                        var question3 = document.getElementById("questionId_3").value;
                        document.cookie = "ques_"+question3+"="+vals+";path=/";
                    }
                });
                
                var slider1 = new rSlider({
                    target: '#slider4',
                    values: [0,1, 2, 3, 4],
                    range: false,
                    set: [0],
                    tooltip: false,
                    onChange: function (vals) {
                        var question4 = document.getElementById("questionId_4").value;
                        document.cookie = "ques_"+question4+"="+vals+";path=/";
                    }
                });
                
                var slider1 = new rSlider({
                    target: '#slider5',
                    values: [0,1, 2, 3, 4],
                    range: false,
                    set: [0],
                    tooltip: false,
                    onChange: function (vals) {
                        var question5 = document.getElementById("questionId_5").value;
                        document.cookie = "ques_"+question5+"="+vals+";path=/";
                    }
                });

               
            };
            window.onload = init;
        })();
    </script>
<?php }?>    
<?php get_footer();?>
