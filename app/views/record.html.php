<?php
	$results = $results = $_SESSION['results'];
	$queryStringUrl = $results['queryString'];
	$encodedQuery = http_build_query(array('query'=>$_REQUEST['query']));
	$encodedHighLigtTerm = http_build_query(array('highlight'=>$_REQUEST['highlight']));
	if (isset($_REQUEST['recordCount'])) {$totalRecords=$_REQUEST['recordCount']; } else { $totalRecords=$_GET['recordcount']; }
	//error_log($queryStringUrl,0);
	//error_log(print_r($result['Items'],true),0);
	$sws=$_GET['sws']; $prev=intval($sws)-1; $next=intval($sws)+1;
?>
<div>
    <div class ="topbar">
      <div style="float: right;margin: 7px 20px 0 0;color: white">
          <?php if($_REQUEST['resultId']>1){  ?>
           <a href="recordSwich.php?<?php echo $encodedQuery;?>&sws=<?php echo $prev; ?>&fieldcode=<?php echo $_REQUEST['fieldcode'];?>&resultId=<?php echo ($_REQUEST['resultId']-1)?>&<?php echo $queryStringUrl; ?>"><span class="results-paging-previous">&nbsp;&nbsp;&nbsp;&nbsp;</span></a>
            <?php }
            echo $_REQUEST['resultId'].' of '.$totalRecords;
			if($_REQUEST['resultId']<$totalRecords){  ?>
				<a href="recordSwich.php?<?php echo $encodedQuery;?>&sws=<?php echo $next; ?>&fieldcode=<?php echo $_REQUEST['fieldcode'];?>&resultId=<?php echo ($_REQUEST['resultId']+1)?>&<?php echo $queryStringUrl; ?>"><span class="results-paging-next">&nbsp;&nbsp;&nbsp;&nbsp;</span></a>
           <?php } ?>
      </div>
    </div>
</div>
 <?php 
	if($debug=='y'){
		echo '<div style="float:right; padding-bottom: 10px"><a target="_blank" href="debug.php?record=y">Retrieve response XML</a></div>';
	} ?>
	
	
<div class="sws-record-info">
<?php
 
	if ($error) { 
		echo '<div class="error">'.$error.'</div>';
	} 
	
	if((!isset($_SESSION['login']))&&$result['AccessLevel']==1){ ?>
         <p>This record from <b>[<?php echo $result['DbLabel']; ?>]</b> cannot be displayed to guests.<br><a href="login.php?path=record&db=<?php echo $_REQUEST['db']?>&an=<?php echo $_REQUEST['an']?>&<?php echo $encodedHighLigtTerm;?>&resultId=<?php echo $_REQUEST['resultId'] ?>&recordCount=<?php echo $totalRecords ?>&<?php echo $encodedQuery;?>&fieldcode=<?php echo $_REQUEST['fieldcode']; ?>">Login</a> for full access.</p>
<?php 
	}
	else
	{ ?>     
          
	<div class="table-cell floatleft"> 
			<img id="bookjacketdetail" src="<?php echo sws_cover($result); ?>"/>
	</div>
	<div class="table-cell floatleft" style='max-width: 40rem; padding: 2rem'>
		<h1 style='line-height: 1.25'>
		<?php 
			if (!empty($result['Items'])) { 
				echo $result['Items'][0]['Data'];
			} 
		 ?>
		</h1> 
		<!-- book jacket -->
		<?php 
			/*if(!empty($result['ImageInfo'])) {
				echo '<div class="table-cell-box" style="padding:0px;">';
				echo '<img id="bookjacketdetail" src="'.str_replace("http:","",$result['ImageInfo']['medium']).'" />';
				echo '</div>';
			} */
		?>
		<?php 
			if(!empty($result['PLink'])){?>
				 <!--<ul class="table-cell-box">
					  <li>-->
						  <a class="sws-button" href="<?php echo $result['PLink'] ?>" target="_blank">Details</a>
					  <!--</li>
				  </ul>-->
		<?php } 
		// if not guest show export link
		if(isset($_SESSION['login'])||isset($login)){
			//echo '<ul class="table-cell-box"><li>';
			echo '<a class="sws-button" href="export.php?format=ris&an='.$result['An'].'&db='.$result['DbId'].'" target="_blank">RIS Export</a>';
			//echo '</li></ul>';
		}
		if(!empty($result['PDF'])||$result['HTML']==1){?>
		 <!--<ul class="table-cell-box">
			<label>Full Text:</label><hr/>-->
		 
		<?php 
		if(!empty($result['PDF'])){?>
		  <!--<li>-->
			  <a target="_blank" class="sws-button" href="PDF.php?an=<?php echo $result['An']?>&term=<?php echo urlencode($encodedHighLigtTerm); ?>&db=<?php echo $result['DbId']?>">
				PDF full text</a>
		  <!--</li>-->
	  <?php 
		} 
			  
		if($result['HTML']==1){ 
			if((!isset($_SESSION['login']))&&$result['AccessLevel']==2){ ?> 
			  <!--<li>-->
				 <a target="_blank" class="sws-button" href="login.php?path=HTML&an=<?php echo $_REQUEST['an']; ?>&db=<?php echo $_REQUEST['db']; ?>&<?php echo $encodedHighLigtTerm ?>&resultId=<?php echo $_REQUEST['resultId'];?>&recordCount=<?php echo $totalRecords?>&<?php echo $encodedQuery;?>&fieldcode=<?php echo $_REQUEST['fieldcode']; ?>">
				HTML full text
				</a>
			  <!--</li>-->
			  <?php 
			} 
			else
			{?>
			  <!--<li>-->
				  <a class="sws-button"  href="#html">HTML Full Text</a>                       
			  <!--</li>-->                      
			 <?php 
			} 
		} ?>
		<!--</ul>-->
		<?php } ?>
		<?php // FIND ONLINE ACCESS LINK
		if (!empty($result['Items'])) { 
			for($i=1;$i<count($result['Items']);$i++) { 
			
				if ($result['Items'][$i]['Label']=="Online Access") { 
					$tmp=$result['Items'][$i]['Data']; 
					$tmp2=explode('"',$tmp);
					//$tmp2=str_replace('<a name="URL" href=""',"",$tmp);
					//$tmp=substr($tmp,0,strpos($tmp,'"'));
					echo "<a href='".$tmp2[3]."' class='sws-button' target='_blank'>Access Online</a>";
					$tmp3=$sws."|".$tmp2[3];	
				?><script>window.parent.postMessage('<?php echo $tmp3; ?>', 'https://ccharacter.com');</script>
		<?php	} 
			}	
		} ?>
		<?php	  if (!empty($result['CustomLinks'])) { ?>                     
				  <ul class="table-cell-box">
					  <label>Custom Links:</label><hr/>
						<?php foreach ($result['CustomLinks'] as $customLink) { ?>
							<li>
								<a  target="_blank" href="<?php echo $customLink['Url']; ?>" title="<?php echo $customLink['MouseOverText']; ?>"><img src="<?php echo $customLink['Icon']?>" class="customlinkimg" /> <?php echo $customLink['Text']; ?></a>
							</li>
						<?php } ?>
				   </ul>
		<?php }
			  
			  if (!empty($result['FullTextCustomLinks'])) { ?>                     
				  <ul class="table-cell-box">
					  <label>Custom Links:</label><hr/>
						<?php foreach ($result['FullTextCustomLinks'] as $customLink) { ?>
							<li>
								<a href="<?php echo $customLink['Url']; ?>" title="<?php echo $customLink['MouseOverText']; ?>"><img src="<?php echo $customLink['Icon']?>" /> <?php echo $customLink['Text']; ?></a>
							</li>
						<?php } ?>
				   </ul>
			  <?php } ?>   		
	</div>
    <div class="table-cell span-16">
        <table>     
			<?php 
			if (!empty($result['Items'])) { 
				for($i=1;$i<count($result['Items']);$i++) { ?>			  
					 <tr>
						<td class='sws-record-col'>
							<strong><?php echo $result['Items'][$i]['Label']; ?>:</strong>
						</td>
						<td>
						<?php 
							if($result['Items'][$i]['Label']=='URL'){ 	
								echo '<a href="'.$result['Items'][$i]['Data'].'" target="_blank">'.$result['Items'][$i]['Data'].'</a>' ;
							}
							else
							{ 
								echo $result['Items'][$i]['Data']; 
							} 
						?>
					   </td>
					</tr> 
				<?php }
			} ?>
            <?php 	if(!empty($result['pubType'])){ ?> 
                   <tr>
                         <td><strong>PubType:</strong></td>
                         <td><?php echo $result['pubType'] ?></td>
                     </tr>
			<?php } 
			if (!empty($result['DbLabel'])) { ?>
						<tr>
							<td><strong>Database:</strong></td>
							<td>
								<?php echo $result['DbLabel']; ?>
							</td>
						</tr>
			<?php } 
			if( !(isset($_SESSION['login']) || (validAuthIP("Config.xml")==true)) && $result['AccessLevel']==2){ ?>
					<tr>
						<td><br></td>
						<td><br></td>
					</tr>
					 <tr>
						 <td colspan="2">This record from <b>[<?php echo $result['DbLabel']; ?>]</b> cannot be displayed to guests.<br><a href="login.php?path=record&db=<?php echo $_REQUEST['db']?>&an=<?php echo $_REQUEST['an']?>&<?php echo $encodedHighLigtTerm?>&resultId=<?php echo $_REQUEST['resultId'] ?>&recordCount=<?php echo $totalRecords ?>&<?php echo $encodedQuery;?>&fieldcode=<?php echo $_REQUEST['fieldcode']; ?>">Login</a> for full access.</td>
					</tr>
			<?php } ?>
		</table> 
        <?php 
			if(!empty($result['htmllink'])){?>
				 <div id="html" style="margin-top:30px">
					 <?php echo $result['htmllink'] ?>
				 </div>
		<?php } ?>
		
    </div>
		 
      <?php } ?>  
</div>


