<?php 

	if ($error) { 
		echo '<div class="error">'.$error.'</div>';
	} 

	 if (!empty($results)) { ?>
		 <div class="top-menu-row">
			<div class="statistics top-items">
				Showing <strong><?php if($results['recordCount']>0){ echo ($start - 1) * $limit + 1;} else { echo 0; } ?>  - <?php if((($start - 1) * $limit + $limit)>=$results['recordCount']){ echo $results['recordCount']; } else { echo ($start - 1) * $limit + $limit;} ?></strong>  
					of <strong><?php echo $results['recordCount']; ?></strong>
					for "<strong><?php echo $searchTerm; ?></strong>" <?php echo $insidejournal;?>
			</div>            
			<div class ="topbar-resultList top-items">
				<div class="optionsControls">
					<ul style="margin:0; padding:0;">              
						<li class="options-controls-li">                   
							<form action="pageOptions.php">
								<label><b>Sort</b></label>
								<select onchange="this.form.submit()" name="sort" > 
									<?php foreach($Info['sort'] as $s){ 
										  if($sortBy==$s['Id']){ ?>
										<option selected="selected" value="<?php echo $s['Action']; ?>"><?php echo $s['Label'] ?></option>
									<?php }else{ ?>
										<option value="<?php echo $s['Action']; ?>"><?php echo $s['Label'] ?></option>
									<?php }}?>
								</select>
								<input type="hidden" value="<?php echo $searchTerm;?>" name="query" />
								<input type="hidden" value="<?php echo $fieldCode;?>"  name="fieldcode" />      
							</form>
						</li>
						<!--<li class="options-controls-li">
							  <?php 
								$option = array(
								  'Detailed' => '',
								  'Brief' => '',
								  'Title' => '',                      
								);
								if($amount== 'detailed'){
									$option['Detailed']= '  selected="selected"';
								}
								if($amount== 'brief'){
									$option['Brief']= '  selected="selected"';
								}
								if($amount== 'title'){
									$option['Title']= '  selected="selected"';
								}                              
							?>    
							<form action="pageOptions.php">
								<label><b>Page options</b></label>
								<select onchange="this.form.submit()" name="view">
									<option  <?php echo $option['Detailed']?> value="detailed">Detailed</option>
									<option  <?php echo $option['Brief']?> value="brief">Brief</option>
									<option  <?php echo $option['Title']?> value="title">Title Only</option>
								</select>
								<input type="hidden" value="<?php echo $searchTerm;?>" name="query" />
								<input type="hidden" value="<?php echo $fieldCode;?>"  name="fieldcode" />  
							</form>
						 </li>-->
							<li class="options-controls-li">
							
							<?php 
								$select = array(
									'5' => '',
									'10' => '',
									'20' => '',
									'24' => '',
									'30' => '',
									'40' => '',
									'50' => ''
								);
								if($limit== 5){
								  $select['5']= '  selected="selected"';
								}
								if($limit== 10){
								  $select['10']= '  selected="selected"';
								}
								if($limit== 24){
								  $select['24']= '  selected="selected"';
								}
								if($limit== 20){
								  $select['20']= '  selected="selected"';
								}
								if($limit== 30){
								  $select['30']= '  selected="selected"';
								}
								if($limit== 40){
								  $select['40']= '  selected="selected"';
								}
								if($limit== 50){
								  $select['50']= '  selected="selected"';
								}                          
							?>                          
							 <form action="pageOptions.php">
								<label><b>Results per page</b></label>
								<select onchange="this.form.submit()" name="resultsperpage">
									<option <?php echo $select['5']?> value="setResultsperpage(5)">5</option>
									<option <?php echo $select['10']?> value="setResultsperpage(10)">10</option>
									<option <?php echo $select['20']?> value="setResultsperpage(20)">20</option>
									<option <?php echo $select['24']?> value="setResultsperpage(24)">24</option>
									<option <?php echo $select['30']?> value="setResultsperpage(30)">30</option>
									<option <?php echo $select['40']?> value="setResultsperpage(40)">40</option>
									<option <?php echo $select['50']?> value="setResultsperpage(50)">50</option>
								</select>
								<input type="hidden" value="<?php echo $searchTerm;?>" name="query" />
								<input type="hidden" value="<?php echo $fieldCode;?>"  name="fieldcode" />  
							</form>
							</li>
						</ul>
				</div>
			</div>
			<div class="pagination top-items">
			<?php echo paginate($results['recordCount'], $limit, $start, $encodedSearchTerm, $fieldCode); ?>
		</div>
		</div>
	</div>
	<!-- begin research starters & Exact Match Placard -->


		<?php
            // check if the research starters are returned

            if(isset($results['relatedRecords'])){
                $rsCount = 0;
                $empCount = 0;
                $rsData = '';
                $empData = '';
                foreach($results['relatedRecords'] as $relRec){
                    if($relRec['Type'] == 'rs' && $rsCount == 0){
                        $rsData = $relRec;
                        $rsCount++;
                    }
                    elseif($relRec['Type'] == 'emp' && $empCount == 0){
                        $empData = $relRec;
                        $empCount++;
                    }
                }
                if(!empty($rsData)){
                    buildResearchStarterPlacard($rsData, $empCount, $fieldCode, $encodedHighLigtTerm, $encodedSearchTerm);
                }
                if(!empty($empData)){
                    buildExactMatchPlacard($empData, $rsCount);
                }
                

            }	
		?>

	<!-- end research starters -->

<?php } ?>