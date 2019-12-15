<div class="sws-results primary-content">
	<?php $sws=0; $even="";
	foreach ($results['records'] as $result) { if ($sws%2) { $even=""; } else { $even="sws-shade"; } // FOREACH ?>
	<div id="sws-cell-<?php echo $sws; ?>" class="result sws-cell <?php echo $even; ?>">
		<?php //if ($sws==3) { error_log(print_r($result,true),0); } ?>
		<div class="sws-row sws-cover">  
			<div class="img-overlay-wrap">
				<?php //if (!empty($result['ImageInfo'])) { ?>               
				<?php if(!empty($result['PLink'])){ // HAS PLINK ?>
					<!--<a href="#commentModal_<?php echo $sws; ?>" data-toggle="modal" role="button">
				<?php } else { // NO PLINK ?>
					<a href="record.php?db=<?php echo $result['DbId']; ?>&an=<?php echo $result['An']; ?>&<?php echo $encodedHighLigtTerm ?>&resultId=<?php echo $result['ResultId'];?>&recordCount=<?php echo $results['recordCount']; ?>&<?php echo $encodedSearchTerm;?>&fieldcode=<?php echo $fieldCode; ?>
				<?php } // NO PLINK ?>-->
						<img class="sws-img" src="<?php echo sws_cover($result); ?>"/>
					<!--</a> -->
			<?php if ($result['pubType']=="eBook") { ?><img src='web/graphics/ebook_icon.svg' class='e-icon'/><?php } ?> 
			<?php if (!empty($result['PLink'])) { ?><a class='i-icon' href='<?php echo $result['PLink']; ?>' target="_blank"><i class="fas fa-question-circle"></i></a><?php  } ?>
			<div id="sws-placeholder-<?php echo $sws; ?>"></div>
			</div>
		</div>
		<div class='sws-row'>                     
			<?php echo "<p class='sws-auth'>".sws_pubyear($result).sws_author($result)."</p>"; ?>
			<?php if (!empty($result['RecordInfo']['BibEntity']['Titles'])){ // HAS A TITLE ?>
				<?php foreach($result['RecordInfo']['BibEntity']['Titles'] as $Ti){ //error_log(print_r($Ti,true),0); // LOOP TITLES ?> 
					<a class="sws-title" href="#commentModal_<?php echo $sws; ?>" data-toggle="modal" role="button">
					<!--<a class="sws-title" href="record.php?db=<?php echo $result['DbId']; ?>&an=<?php echo $result['An']; ?>&<?php echo $encodedHighLigtTerm ?>&resultId=<?php echo $result['ResultId'];?>&recordCount=<?php echo $results['recordCount']; ?>&<?php echo $encodedSearchTerm;?>&fieldcode=<?php echo $fieldCode; ?>">-->
					<?php echo  sws_rm_brackets(sws_trim_string($Ti['TitleFull']));  ?>
					</a>
				<?php } // LOOP TITLES ?> 
			<?php	} // HAS A TITLE ?>
			<?php  //} ?>  
		</div>
				<?php
				if(isset($_SESSION['login'])||isset($login)){
					echo '<div class="exportLink">';
					echo '<a href="export.php?format=ris&an='.$result['An'].'&db='.$result['DbId'].'" target="_blank">RIS Export</a>';
					echo '</div>';
				}
				?>
			<!--</div>-->     
			<?php //} ?>       
		<?php echo sws_modal($result, $sws, sws_recordlink($result,$encodedHighLigtTerm,$encodedSearchTerm,$fieldCode),$results['recordCount']); ?>
	</div>
	<?php include("details.php"); ?>
	<?php  $sws++; } // FOREACH
	//error_log(print_r($result,true),0);
	 ?>
	<?php // SPACER DIVS
	for ($k=0; $k<10; $k++) { ?>
		<div class='sws-grid-spacer'></div>
	<? } ?>	
</div>