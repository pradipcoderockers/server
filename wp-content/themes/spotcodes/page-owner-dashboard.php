<?php /* Template Name: Ownerdashboard */?>
<?php get_header();

$current_user = wp_get_current_user();
$userId = isset($current_user->data->ID)?$current_user->data->ID:"";
$roles = isset($current_user->roles[0])?$current_user->roles:"";
global $wpdb;
if($userId==0)
{
	header('Location: '.get_home_url().'/login');exit;
}
get_template_part('partials/title_box'); 
if(!in_array('vendor',$current_user->roles))
{
	if($wp->request==='dashboard')
	{ ?>
		<link href="https://blackrockdigital.github.io/startbootstrap-sb-admin-2/dist/css/sb-admin-2.css" rel="stylesheet" id="bootstrap-css">
		<div class="container">
		   <div class="row">
			<div class="col-lg-4 col-md-6">
				<div class="panel panel-primary">
					<div class="panel-heading">
						<div class="row">
							<div class="col-xs-6">
								<i class="fa upme-icon-user fa-5x"></i>
							</div>
							<div class="col-xs-9 text-right">
								<div class="huge"></div>
							<div>Server IQ Profile</div>
						</div>
					</div>
				</div>
				<a href="<?php echo get_home_url();?>/server-iq-profile">
					<div class="panel-footer">
						<span class="pull-left">View Details</span>
						<span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>
						<div class="clearfix"></div>
					</div>
				</a>
			</div>
		</div>
		<div class="col-lg-4 col-md-6">
			<div class="panel panel-green">
				<div class="panel-heading">
					<div class="row">
						<div class="col-xs-6">
							<i class="fa fa-cogs fa-5x"></i>
						</div>
						<div class="col-xs-9 text-right">
							<div class="huge"></div>
							<div>Account Setting</div>
						</div>
					</div>
				</div>
				<a href="<?php echo get_home_url();?>/profile">
					<div class="panel-footer">
						<span class="pull-left">View Details</span>
						<span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>
						<div class="clearfix"></div>
					</div>
				</a>
			</div>
		</div>
		<div class="col-lg-4 col-md-6">
			<div class="panel panel-yellow">
				<div class="panel-heading">
					<div class="row">
						<div class="col-xs-6">
							<i class="fa fa-graduation-cap fa-5x"></i>
						</div>
						<div class="col-xs-9 text-right">
							<div class="huge"></div>
							<div>Your IQ Result</div>
						</div>
					</div>
				</div>
				<a href="<?php echo get_home_url();?>/server-iq-result">
					<div class="panel-footer">
						<span class="pull-left">View Details</span>
						<span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>
						<div class="clearfix"></div>
					</div>
				</a>
			</div>
		</div>
	</div>
</div>
     <?php
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
<?php
	}
}
else
{

	if($wp->request=='dashboard')
	{ ?>
		<link href="https://blackrockdigital.github.io/startbootstrap-sb-admin-2/dist/css/sb-admin-2.css" rel="stylesheet" id="bootstrap-css">
		<div class="container">
		 <div class="row">
			<div class="col-lg-4 col-md-6">
			<div class="panel panel-primary">
				<div class="panel-heading">
					<div class="row">
						<div class="col-xs-6">
							<i class="fa upme-icon-user fa-5x"></i>
						</div>
						<div class="col-xs-9 text-right">
							<div class="huge"></div>
							<div>Manage Server</div>
						</div>
					</div>
				</div>
				<a href="<?php echo get_home_url();?>/restaurant-user-list">
					<div class="panel-footer">
						<span class="pull-left">View Details</span>
						<span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>
						<div class="clearfix"></div>
					</div>
				</a>
			</div>
		</div>
		<div class="col-lg-4 col-md-6">
			<div class="panel panel-green">
				<div class="panel-heading">
					<div class="row">
						<div class="col-xs-6">
							<i class="fa fa-cogs fa-5x"></i>
						</div>
						<div class="col-xs-9 text-right">
							<div class="huge"></div>
							<div>Account Setting</div>
						</div>
					</div>
				</div>
				<a href="<?php echo get_home_url();?>/profile">
					<div class="panel-footer">
						<span class="pull-left">View Details</span>
						<span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>
						<div class="clearfix"></div>
					</div>
				</a>
			</div>
		</div>
		<div class="col-lg-4 col-md-6">
			<div class="panel panel-yellow">
				<div class="panel-heading">
					<div class="row">
						<div class="col-xs-6">
							<i class="fa fa-user-plus fa-5x"></i>
						</div>
						<div class="col-xs-9 text-right">
							<div class="huge"></div>
							<div>Create Server/Manager</div>
						</div>
					</div>
				</div>
				<a href="<?php echo get_home_url();?>/add-user">
					<div class="panel-footer">
						<span class="pull-left">View Details</span>
						<span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>
						<div class="clearfix"></div>
					</div>
				</a>
			</div>
		</div>
		
		
	</div>
</div>
<div class="container">
	  <h2>Latest Server</h2>
	  <table class="table table-bordered">
		<thead>
	 <div class="container">
	  <table class="table table-bordered">
		<thead>
		  <tr>
			<th>UserName</th>
			<th>Lastname</th>
			<th>Email</th>
			<th>View Exam Result</th>
		  </tr>
		</thead>
		<tbody>
		  <?php	 
			$query = "SELECT * FROM wp_users WHERE parentId ={$userId} ORDER BY ID DESC LIMIT 5";
			$wpdb->query($query);
			$userList = $wpdb->get_results($query);
			if(!empty($userList))
			{
				foreach($userList as $list)
				{
				 ?>
				  <tr>
					<td><?php echo $list->user_login;?></td>
					<td><?php echo $list->user_email;?></td>
					<td><?php echo $list->display_name;?></td>
					<td><a href="<?php echo get_home_url();?>/restaurant-user-exam-detail?userId=<?php echo $list->ID;?>">View Result</td>
				  </tr>
				<?php
			}
		}
		else
		{
			?>
			No server found!
			
		  <?php
		}
		?>
		</tbody>
	  </table>
	</div>
<?php }
else if($wp->request==='delete-restaurant-user')
{
   $userId = isset($_GET['userId'])?$_GET['userId']:"";
   $query = "DELETE FROM wp_users WHERE ID ={$userId}";
   if($wpdb->query($query))
   {
	    header('Location: '.get_home_url().'/restaurant-user-list');exit;
   }
  
}
else if($wp->request==='restaurant-user-list')
{
	 ?>
	 <div class="container">
	  <h2>Manage User</h2>
	  <table class="table table-bordered">
		<thead>
		  <tr>
			<th>UserName</th>
			<th>Lastname</th>
			<th>Email</th>
			<th>View Exam Result</th>
			<th>Action</th>
		  </tr>
		</thead>
		<tbody>
		 <?php	 
			$query = "SELECT * FROM wp_users WHERE parentId ={$userId} ORDER BY ID DESC";
			$wpdb->query($query);
			$userList = $wpdb->get_results($query);
			if(!empty($userList))
			{
				foreach($userList as $list)
				{
				 ?>
				  <tr>
					<td><?php echo $list->user_login;?></td>
					<td><?php echo $list->user_email;?></td>
					<td><?php echo $list->display_name;?></td>
					<td><a href="<?php echo get_home_url();?>/restaurant-owner-dashboard/restaurant-user-exam-detail?userId=<?php echo $list->ID;?>">View Result</td>
					<td><a onclick="return confirm('Are you sure you want to delete this user?')" href="<?php echo get_home_url();?>/delete-restaurant-user?userId=<?php echo $list->ID;?>">Delete</td>
				  </tr>
				<?php
			}
		}
		else
		{
			?>
			No server found!
		  <?php
		}
		?>
		</tbody>
	  </table>
	</div>
	  <?php
}
else if($wp->request=='restaurant-user-exam-detail')
{
	$userId = isset($_GET['userId'])?$_GET['userId']:"";
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
}
?>  
<?php get_footer();?>
