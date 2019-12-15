<div id="sws-info-<?php echo $sws; ?>" class="info table-cell sws-info">
	<div style='margin-left:10px;'>
				<?php if(!empty($result['PLink'])){?>
						 <ul class="table-cell-box">
							  <li>
								  <a href="<?php echo $result['PLink'] ?>" target="_blank">View in EDS</a>
							  </li>
						  </ul>
                 <?php } ?>		
				 <div id="sws-access-<?php echo $sws; ?>"></div>
		<?php 
		if(!(isset($_SESSION['login']) ||(validAuthIP("Config.xml")==true)) &&$result['AccessLevel']==1){ ?>
			<p>This record from <b>[<?php echo $result['DbLabel'] ?>]</b> cannot be displayed to guests. <a href="login.php?path=results&<?php echo $encodedSearchTerm;?>&fieldcode=<?php echo $fieldCode; ?>">Login</a> for full access.</p>
	   <?php }
	   else{  ?>
		<div class='sws-row'>                     
			<?php if (!empty($result['RecordInfo']['BibEntity']['Titles'])){ ?>
			<?php foreach($result['RecordInfo']['BibEntity']['Titles'] as $Ti){ ?> 
			<a class="sws-title" href="record.php?db=<?php echo $result['DbId']; ?>&an=<?php echo $result['An']; ?>&<?php echo $encodedHighLigtTerm ?>&resultId=<?php echo $result['ResultId'];?>&recordCount=<?php echo $results['recordCount']; ?>&<?php echo $encodedSearchTerm;?>&fieldcode=<?php echo $fieldCode; ?>"><?php echo $Ti['TitleFull']; ?></a>
		   <?php } }
			else { ?> 
			<a class="sws-title" href="record.php?db=<?php echo $result['DbId']; ?>&an=<?php echo $result['An']; ?>&<?php echo $encodedHighLigtTerm ?>&resultId=<?php echo $result['ResultId'];?>&recordCount=<?php echo $results['recordCount']; ?>&<?php echo $encodedSearchTerm;?>&fieldcode=<?php echo $fieldCode; ?>"><?php echo "Title is not Aavailable"; ?></a>                   
		  <?php  } ?>                
		</div>
		
		<?php 
		
		if(!empty($result['Items']['TiAtl'])){ 
				echo "<div>";
				foreach($result['Items']['TiAtl'] as $TiAtl){ 
					echo $TiAtl['Data']; 
				} 
				echo "</div>";
		} 
		
		if (!empty($result['Items']['Au'])) { ?>
			<div class="authors">
				<span>
					<span style="font-style: italic;">By: </span>                                            
					<?php 
					$number = 5;
					handleResultListAuthors($result, $number)
					?>
				</span>                        
			</div>                        
		<?php 
		} 
		?>
		
		<div class="source">
		<span style="font-style: italic; ">
		<?php 
			if(isset($result['RecordInfo']['BibRelationships']['IsPartOfRelationships']['Titles'])){
				foreach($result['RecordInfo']['BibRelationships']['IsPartOfRelationships']['Titles'] as $title){ 
					echo $title['TitleFull'];                                  
				}
			}
		?>
		</span>
		<?php 
		
		if(!empty($result['RecordInfo']['BibEntity']['Identifiers'])){
			foreach($result['RecordInfo']['BibEntity']['Identifiers'] as $identifier){
				$pieces = explode('-',$identifier['Type']); 
				if(isset($pieces[1])){                                       
				   echo ", ".strtoupper($pieces[0]).'-'.ucfirst( $pieces[1]);                                       
				}
				else{ 
				   echo ", ".strtoupper($pieces[0]);
				}
				echo ":".$identifier['Value'];                                                                
			}
		} 
		
		if(isset($result['RecordInfo']['BibRelationships']['IsPartOfRelationships']['Identifiers'])){
			foreach($result['RecordInfo']['BibRelationships']['IsPartOfRelationships']['Identifiers'] as $identifier){
				if($identifier['Type'] != 'issn-locals'){
					$pieces = explode('-',$identifier['Type']);
					if(isset($pieces[1])){                                        
						echo ", ".strtoupper( $pieces[0]).'-'.ucfirst( $pieces[1]);                                       
					}
					else{ 
						echo ", ".strtoupper($pieces[0]);
					}
					echo ": ".$identifier['Value'].", "; 
				}
			}  
		}
		
		if(isset($result['RecordInfo']['BibRelationships']['IsPartOfRelationships']['date'])){
			foreach($result['RecordInfo']['BibRelationships']['IsPartOfRelationships']['date'] as $date){ 
				if ($date["Type"]=='published') {
					echo "Published: ".$date['M']."/".$date['D']."/".$date['Y'];
				}
			}
		}
			
		if(isset($result['RecordInfo']['BibRelationships']['IsPartOfRelationships']['numbering'])){ 
			foreach($result['RecordInfo']['BibRelationships']['IsPartOfRelationships']['numbering'] as $number){
				$type = str_replace('volume','Vol',$number['Type']); $type = str_replace('issue','Issue',$type); 
				echo ", ".$type.": ".$number['Value']; 
			} 
		} 
			
		if(!empty($result['RecordInfo']['BibEntity']['PhysicalDescription']['StartPage'])){
			 echo ', Start Page: '.$result['RecordInfo']['BibEntity']['PhysicalDescription']['StartPage']; 
		} 
			
		if(!empty($result['RecordInfo']['BibEntity']['PhysicalDescription']['Pagination'])){ 
			echo ', Page Count: '.$result['RecordInfo']['BibEntity']['PhysicalDescription']['Pagination']; 
		} 
		
		if(!empty($result['RecordInfo']['BibEntity']['Languages'])){ 
			foreach($result['RecordInfo']['BibEntity']['Languages'] as $language){ 
			 if($language['Text'] != 'unknown'){
				echo ", Language: ".$language['Text'];
			 }
			} 
		}
		?>
		</div>
		<?php if (isset($result['Items']['Ab'])) { ?>
		
		
		 

		<div id="abstract<?php echo $result['ResultId'];?>" class="abstract" onclick="swsShow('full-abstract<?php echo $result['ResultId'];?>',this.id)">
			<span style="font-style: italic;">Abstract: </span>                                    
			<?php 
				foreach($result['Items']['Ab'] as $Abstract){ 
					$xml ="Config.xml";
					$dom = new DOMDocument();
					$dom->load($xml);      
					$length = $dom ->getElementsByTagName('AbstractLength')->item(0)->nodeValue;      
					if($length == 'Full'){
						echo $Abstract['Data'];
					}
					else
					{
						$data = str_replace(array('<span class="highlight">','</span>'), array('',''), $Abstract['Data']);
						$data = mb_substr($data, 0, $length).'&hellip;';
						echo $data;
					}
				} 
			?>                                  
			<span id="abstract-plug<?php echo $result['ResultId'];?>">[+]</span>                                
		</div>
		<div id="full-abstract<?php echo $result['ResultId'];?>" class="full-abstract" onclick="swsShow(this.id,'full-abstract<?php echo $result['ResultId'];?>')">
			<span style="font-style: italic;">Abstract: </span>
			<?php 
				foreach($result['Items']['Ab'] as $Abstract){ 
					echo $Abstract['Data']; 
				} 
			?>                                        
			<span id="full-abstract-plug<?php echo $result['ResultId'];?>">[-]</span>
		</div>
	  <?php } 
	  if (!empty($result['Items']['Su'])) { ?>
		<div class="subjects">
			<span style="font-style: italic;">Subjects:</span>
			<?php 
				foreach($result['Items']['Su'] as $Subject){ 
					echo $Subject['Data']; 
				} 
			?> 
		</div>
	  <?php } ?>
	  <?php 
	  // support for Image Quick View
	  // these represent small icons of tables, figures, graphcs used in articles
	  // Note IQV will only show to authorized users with a link, all others only see thumbnail
	  if(isset($result['ImageQuickView'])){
	  ?>
		<div class="imagequickview">
		<?php
			foreach($result['ImageQuickView'] as $iqv){
				if(isset($_SESSION['login'])||isset($login)||validAuthIP('Config.xml')){
					echo '<a href="iqv.php?db='.$iqv['DbId'].'&an='.$iqv['An'].'"><img src="'.$iqv['Url'].'" class="iqvitem" alt="'.$iqv['Type'].'" title="'.$iqv['Type'].'"></a>';
				}
				else{
					echo '<img src="'.$iqv['Url'].'" class="iqvitem" alt="'.$iqv['Type'].'" title="'.$iqv['Type'].' / Please login for full access">';
				}                           
			}
		?>
		</div>
	  <?php
	  }
	  ?>
	  <div class="links">
		<?php 
		if($result['HTML']==1){
			if  ( !(isset($_SESSION['login']) || validAuthIP("Config.xml")==true)  && $result['AccessLevel']==2){ 
				echo '<a target="_blank" class="icon html fulltext" href="login.php?path=HTML&an='.$result['An'].'&db='.$result['DbId'].'&'.$encodedHighLigtTerm.'&resultId='.$result['ResultId'].'&recordCount='.$results['recordCount'].'&'.$encodedSearchTerm.'&fieldcode='.$fieldCode.'">Full Text</a>';
			} 
			else
			{
				echo '<a target="_blank" class="icon html fulltext" href="record.php?an='.$result['An'].'&db='.$result['DbId'].'&'.$encodedHighLigtTerm.'&resultId='.$result['ResultId'].'&recordCount='.$results['recordCount'].'&'.$encodedSearchTerm.'&fieldcode='.$fieldCode.'#html">Full Text</a>';
			} 
		} 
		
		if(!empty($result['PDF'])){
			echo '<a target="_blank" class="icon pdf fulltext" href="PDF.php?an='.$result['An'].'&db='.$result['DbId'].'">Full Text</a>';
		} 

		if (!empty($result['CustomLinks'])){  
			foreach ($result['CustomLinks'] as $customLink) { 
				echo '<a href="'.$customLink['Url'].'" title="'.$customLink['MouseOverText'].'>" target="_blank">';
				if ($customLink['Icon']!="") {
					echo '<img src="'.$customLink['Icon'].'" />';
				}
				echo $customLink['Text'].'</a>';
				
			 //echo '<a href="'.$customLink['Url'].'" title="'.$customLink['MouseOverText'].'" target="_blank"><img src="'.$customLink['Icon'].'" />'.$customLink['Text'].'</a>';
			} 
		}							
		?>                   
	  </div>                      
	  <?php 
	   
		if (!empty($result['FullTextCustomLinks'])){ 
			foreach ($result['FullTextCustomLinks'] as $customLink) { 
				echo '<a href="'.$customLink['Url'].'" title="'.$customLink['MouseOverText'].'>" target="_blank">';
				if ($customLink['Icon']!="") {
					echo '<img src="'.$customLink['Icon'].'" />';
				}
				echo $customLink['Text'].'</a>';
			}
		} 
	} 
	?>
	</div>
</div>
