<?php
  session_start();
  // Display all warnings
  ini_set('display_errors', '1');
  ini_set('display_startup_errors', '1');
  error_reporting(E_ALL);
  ob_start();
?>

	

<!DOCTYPE html>
<html>
<head>
<title>Twitter Data Analysis DM2578</title>
<style>
h1   {color: blue;}
p    {color: gray;}
table {
    background-color: gray;
    width: 100%;
}
table tr{
    background-color: #b1e1ff;
    text-align:center;
}
#container{
    width:90%;
    margin-left:auto;
    margin-right:auto;
    
}
#container >div{
    text-align: center;
    margin: 50px 0;
}
.table1{
    width: 70%;
    margin-left: auto;
    margin-right: auto;
}
#loader{
    position: absolute;
    width: 100%;
    height: 100%;
    background-color: white;
    -webkit-transition: opacity 0.5s; /* Safari */
    transition: opacity 0.5s;
}
#loader img{
    position: absolute;
    top: 50%;
    left: 50%;
    margin-top: -50px;
    margin-left: -50px;
}
</style>
</head>
<body>
    <div id="loader">
        <img src="loader.gif" alt="Loader" height="100" width="100">
    </div>
    
<div id="container">
<h1>Twitter Data Analysis DM2578</h1>

<?php
	require "twitteroauth/autoload.php";

	use Abraham\TwitterOAuth\TwitterOAuth;
	$CONSUMER_KEY = "";
	$CONSUMER_SECRET = "";
	$access_token = "";
	$access_token_secret = "";

	$connection = new TwitterOAuth($CONSUMER_KEY, $CONSUMER_SECRET, $access_token, $access_token_secret);
	//$statuses = $connection->get("search/tweets", ["q" => "#trend", "since" => '2017-03-22']);
	
	$hashtag = "#brexit";
	
	$statuses = $connection->get("search/tweets", ["q" => $hashtag, "count" => 100]);
    $allResults = array();
    array_push($allResults, $statuses->statuses);
    $last_tweet = end($statuses->statuses);
    $max_id = $last_tweet->id;
	$x = 0;
	//date_default_timezone_set("Europe/Stockholm");

	$dt = new DateTime("now", new DateTimeZone('Europe/Stockholm'));
	$currentHour = $dt->format('H');
	$currentHourMin = $dt->format('H:i');


	$x = 0;
	$timer = $currentHour;
	while($x < 3) {
        $statuses_next = $connection->get("search/tweets", ["q" => $hashtag, "count" => 100, "max_id" => $max_id-1]);
	    $results = $statuses_next->statuses;
	    $max_id = end($results)->id;
	    array_push($allResults, $results);
	    //print_r(end($results)->created_at);
	    $last_time = new DateTime(end($results)->created_at);
	    $modified = $last_time->setTimezone(new DateTimezone("Europe/Stockholm")); 
	    $last_hour = $modified->format('H');
	    if($last_hour != $timer){
	        $x++;
	        $timer = $last_hour;
	    }
	//    if(empty($results)){
	//        $x = false;
	 //   }else{
	 //       $max_id = end($results)->id;
	 //       $allResults = array_merge_recursive($allResults,$results);
	//        var_dump($results);
	//    }$hashtag = "#brexit";
    //$fromUser = "from:AngieMeader";
    }
    
    
    $fromUser = "from:AngieMeader";
    
    $statuses_2 = $connection->get("search/tweets", ["q" => $hashtag."+".$fromUser, "count" => 100]);
    $userTweets = $statuses_2->statuses;
	$userData = array_values($userTweets)[0]->user;
?>
<script>
    document.getElementById("loader").style.opacity = 0;
</script>
<div>
	<h2>People using the hashtag "<?= $hashtag; ?>" during last 3 hours</h2>
	<table class="table1">
		<tr style="background-color: #60c3ff;">
			<th>Last hour</th>
		    <?php
		    	$numberOfPeople = 0;
		    	$numberOfPeopleList=array();
		    	$peopleIDList = array();
		    	$first_iteration = true;
		    	for ($i = 0; $i < sizeof($allResults); $i++):
		    	    for ($j = 0; $j < sizeof($allResults[$i]); $j++):
		    	        $value2 = $allResults[$i][$j];
		    		    $last_time2 = new DateTime($value2->created_at);
	                    $modified2 = $last_time2->setTimezone(new DateTimezone("Europe/Stockholm")); 
	                    $last_hour2 = $modified2->format('H');
		    		    $person = $value2->user;
		    		    if($last_hour2 == $currentHour){
		    		        if(!in_array($person->id, $peopleIDList)){
		    		            $numberOfPeople += 1;
		    			        array_push($peopleIDList, $person->id);
		    		        }
		    		        //if($i == sizeof($allResults)-1 && $j == sizeof($allResults[$i])-1){
		    		            //$start_h = ((int)$currentHour)+1;
		    ?>
		    			        <!--<th><?php //print_r($start_h.":00 - ".$currentHour.":00"); ?></th> -->
		    <?php
		    		            //array_push($numberOfPeopleList, $numberOfPeople);
		    		        //}
		    		    }else{
		    		        if($first_iteration){
		    		            $start_h = $currentHourMin;
		    		        }else{
		    		            $start_h = (((int)$currentHour)+1).":00";
		    		        }
		    		        
		    ?>
		    			    <th><?php print_r($start_h." - ".$currentHour.":00"); ?></th>
		    <?php
		    			    array_push($numberOfPeopleList, $numberOfPeople);
		    			    $currentHour = $last_hour2;
						    $numberOfPeople = 1;
						    $peopleIDList = array();
						    $first_iteration = false;
		    		    }
		    ?>
		            <?php endfor; ?>
		        <?php endfor; ?>
	    </tr>
	    <tr>
	    	<th style="background-color: #60c3ff;">Number of people</th>
	    	<?php
		    	foreach($numberOfPeopleList as $key=>$value):
		    ?>
	    		<td><?php echo $value; ?></td>
	    	<?php endforeach; ?>
	    </tr>
	    
	</table>
</div>
<div>
	<h2>User</h2>
	<img src="<?= $userData->profile_image_url_https; ?>" alt="Smiley face" height="42" width="42">
	<span><?= $userData->name; ?></span>
	<h3>Statistic over usage of hashtag "<?= $hashtag; ?>" by the user previously</h3>
	<table>
		<tr style="background-color: #60c3ff;">
			<th></th>
		    <?php
		    	$date = date("j F Y");
		    	$amount = 0;
		    	$amountList=array();
		    	$otherHashtagsList = array();
		    	$otherHashtags = array();
		    	foreach($userTweets as $key=>$value):
		    		$time = strtotime($value->created_at);
		    		$newDate = date('j F Y', $time);
		    		$text = explode(" ",$value->text);
		    		
		    		if($newDate == $date){
		    			$amount += 1;
		    			
		    		}else{
		    ?>
		    			<th><?php echo $date; ?></th>
		    <?php
		    			array_push($amountList, $amount);
		    			array_push($otherHashtagsList, $otherHashtags);
						$amount = 1;
						$otherHashtags = array();
						$date = $newDate;
		    		}
		    		foreach ($text as $word) {
                        if(substr($word,0,1) == "#" && !preg_match('/#brexit/',strtolower($word)) && !in_array($word, $otherHashtags)){
                            array_push($otherHashtags, $word);
                        }
                    }
		    ?>
		    <?php endforeach; ?>
	    </tr>
	    <tr>
	    	<th style="background-color: #60c3ff;">Usage frequency</th>
	    	<?php
		    	foreach($amountList as $key=>$value):
		    ?>
	    		<td><?php echo $value; ?></td>
	    	<?php endforeach; ?>
	    </tr>
	    <tr>
	    	<th width="150" style="background-color: #60c3ff;">Hashtags used along with the "<?= $hashtag; ?>"</th>
	    	<?php
		    	foreach($otherHashtagsList as $key=>$value):
		    ?>
	    		<td><?php echo implode("<br>",$value); ?></td>
	    	<?php endforeach; ?>
	    </tr>
	    
	</table>
</div>


</div>



<script>
    console.log(<?= json_encode($allResults); ?>);
    console.log(<?= json_encode($userData); ?>);
</script>
</body>
</html>